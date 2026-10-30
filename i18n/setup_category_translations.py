#!/usr/bin/env python3
"""
One-time setup: creates a translated WordPress category term for each
of the 3 real Track A categories (Learn, News, Candlestick Patterns)
in every target language, and links each new term into the same
Polylang term-translation group as its English original, via the
gfh_term_lang / gfh_term_translations REST fields
(inc/i18n-term-rest-connector.php).

Why this exists: confirmed live 2026-10-30 that translate.py never
read or set a category on any translated post, and no translated
category term existed to assign one to even if it had -- so every
post translate.py has created so far landed in WordPress's default
"Uncategorized" term instead of the correctly-translated category.
This script creates the missing terms; translate.py's own fix (reading
the source post's category and resolving it to the translated term)
and the backfill for already-created posts are separate scripts.

Idempotent: skips any (category, language) pair that's already
linked, reading each category's CURRENT gfh_term_translations fresh
on every run -- safe to re-run after a partial failure, or to pick up
a newly-added language later without re-creating existing terms.

Usage:
    python3 i18n/setup_category_translations.py --dry-run
    python3 i18n/setup_category_translations.py
"""
import argparse
import os
import sys

import requests

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from glossary import LANGUAGES
from slugify import transliterate_slug
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# English category id -> {lang: translated name}. Only 3 real
# categories exist on this site (confirmed live via
# GET /wp-json/wp/v2/categories, 2026-10-30); WordPress's own default
# "Uncategorized" term is deliberately not translated or assigned to
# anything here -- it's the fallback this whole fix exists to stop
# translated posts from landing in.
CATEGORY_TRANSLATIONS = {
    2: {  # "Learn"
        "es": "Aprende", "de": "Lernen", "ar": "تعلم", "fr": "Apprendre",
        "pl": "Nauka", "it": "Impara", "pt": "Aprenda",
    },
    5: {  # "News"
        "es": "Noticias", "de": "Nachrichten", "ar": "أخبار", "fr": "Actualités",
        "pl": "Wiadomości", "it": "Notizie", "pt": "Notícias",
    },
    3: {  # "Candlestick Patterns"
        "es": "Patrones de velas japonesas", "de": "Kerzenmuster", "ar": "أنماط الشموع اليابانية",
        "fr": "Figures en chandeliers japonais", "pl": "Formacje świecowe",
        "it": "Pattern delle candele giapponesi", "pt": "Padrões de candlestick",
    },
}


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--dry-run", action="store_true", help="Report what would be created/linked, without writing anything.")
    args = parser.parse_args()

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    for en_id, translations in CATEGORY_TRANSLATIONS.items():
        resp = request_with_retry("GET", f"{WP_BASE}/categories/{en_id}", auth=auth, timeout=30)
        resp.raise_for_status()
        en_term = resp.json()
        en_name = en_term["name"]
        existing = en_term.get("gfh_term_translations") or {}
        existing = existing if isinstance(existing, dict) else {}

        print(f"\n=== {en_name} (id {en_id}) ===")
        print(f"  current translations: {existing}")

        for lang in LANGUAGES:
            if lang in existing:
                print(f"  {lang}: already linked (id {existing[lang]}) -- skipping")
                continue
            if lang not in translations:
                print(f"  {lang}: WARNING -- no translated name on file, skipping")
                continue

            translated_name = translations[lang]
            slug = transliterate_slug(translated_name, fallback_prefix=f"{en_term['slug']}-{lang}")

            # Check for an existing term with this exact slug before
            # creating one -- confirmed live 2026-10-30 that a prior
            # partial run (a timeout on the final link-back step,
            # after the term was already created and tagged) can leave
            # exactly this kind of orphan: a real term that exists and
            # has the right gfh_term_lang, but was never linked into
            # the English term's gfh_term_translations, so the
            # "already linked" check above never saw it. Re-creating
            # blindly would produce a duplicate category instead of
            # finishing the one link step that actually failed.
            lookup_resp = request_with_retry("GET", f"{WP_BASE}/categories", params={"slug": slug}, auth=auth, timeout=30)
            lookup_resp.raise_for_status()
            matches = lookup_resp.json()

            # A slug match is only a reusable orphan if it's NOT already
            # claimed by some OTHER language. Confirmed live 2026-10-30:
            # "Noticias" (es) and "Notícias" (pt) both transliterate to
            # the same ASCII slug "noticias" once accents are stripped
            # -- a real collision between two different target
            # languages, not a partial-failure orphan. Treating it as
            # one and reusing the term silently relabeled the live
            # Spanish "News" term to Portuguese and dropped the "es"
            # link entirely (Polylang derives a group's membership from
            # each member term's OWN current language tag, so changing
            # that tag out from under an already-linked term removes it
            # from its old slot). Disambiguate with a language suffix
            # instead of reusing in this case, same as slugify's own
            # non-Latin-script fallback already does for a different
            # reason.
            collision = bool( matches ) and matches[0].get("gfh_term_lang") and matches[0]["gfh_term_lang"] != lang
            if collision:
                slug = f"{slug}-{lang}"
                print(f"  {lang}: slug {translated_name!r} collides with the existing {matches[0]['gfh_term_lang']} term (id {matches[0]['id']}) -- using {slug!r} instead")
                matches = []

            if args.dry_run:
                if matches:
                    print(f"  {lang}: term {translated_name!r} (slug {slug}) already exists as id {matches[0]['id']} -- WOULD link it (not create a duplicate)")
                else:
                    print(f"  {lang}: WOULD create term {translated_name!r} (slug {slug}) and link it")
                continue

            if matches:
                new_id = matches[0]["id"]
                print(f"  {lang}: found existing orphaned term {translated_name!r} (slug {slug}, id {new_id}) -- reusing it instead of creating a duplicate")
            else:
                create_resp = request_with_retry(
                    "POST", f"{WP_BASE}/categories",
                    json={"name": translated_name, "slug": slug, "parent": 0},
                    auth=auth, timeout=30,
                )
                create_resp.raise_for_status()
                new_term = create_resp.json()
                new_id = new_term["id"]

            request_with_retry(
                "POST", f"{WP_BASE}/categories/{new_id}",
                json={"gfh_term_lang": lang},
                auth=auth, timeout=30,
            ).raise_for_status()

            # This specific call is the one that needs real resilience:
            # inc/i18n-term-rest-connector.php's gfh_term_translations
            # setter waits on a process-wide lock for up to 70s before
            # giving up (the SAME lock + the SAME 60s stale-recovery
            # threshold as the post-level gfh_translations setter) --
            # confirmed live this exact call timing out at this
            # script's original timeout=30 is what created the orphan
            # this function now has to detect and heal above.
            # request_with_retry()'s own timeout kwarg governs each
            # individual attempt; a longer per-attempt timeout here
            # means a single attempt can actually outlast the lock
            # wait instead of giving up long before the server was
            # ever going to answer.
            request_with_retry(
                "POST", f"{WP_BASE}/categories/{en_id}",
                json={"gfh_term_translations": {lang: new_id}},
                auth=auth, timeout=90,
            ).raise_for_status()

            print(f"  {lang}: created {translated_name!r} (slug {slug}, id {new_id}) and linked")


if __name__ == "__main__":
    main()
