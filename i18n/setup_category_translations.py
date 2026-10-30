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
        resp = requests.request("GET", f"{WP_BASE}/categories/{en_id}", auth=auth, timeout=30)
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

            if args.dry_run:
                print(f"  {lang}: WOULD create term {translated_name!r} (slug {slug}) and link it")
                continue

            create_resp = requests.request(
                "POST", f"{WP_BASE}/categories",
                json={"name": translated_name, "slug": slug, "parent": 0},
                auth=auth, timeout=30,
            )
            create_resp.raise_for_status()
            new_term = create_resp.json()
            new_id = new_term["id"]

            requests.request(
                "POST", f"{WP_BASE}/categories/{new_id}",
                json={"gfh_term_lang": lang},
                auth=auth, timeout=30,
            ).raise_for_status()

            requests.request(
                "POST", f"{WP_BASE}/categories/{en_id}",
                json={"gfh_term_translations": {lang: new_id}},
                auth=auth, timeout=30,
            ).raise_for_status()

            print(f"  {lang}: created {translated_name!r} (slug {slug}, id {new_id}) and linked")


if __name__ == "__main__":
    main()
