#!/usr/bin/env python3
"""
One-off, targeted repair for the exact live incident caused by
setup_category_translations.py's slug-collision bug (see that file's
own comment, and commit a16a63d): "Noticias" (es) and "Notícias" (pt)
both transliterate to the same ASCII slug "noticias", so the
orphan-reuse logic (before it was fixed) found the already-linked
Spanish term (id 1348) and relabeled it to Portuguese, dropping the
"es" link from category 5 ("News") entirely.

Deliberately NOT reusable for anything else: the exact term id to fix
(1348) is hardcoded, not discovered, so this can only ever act on
exactly this one known incident -- same pattern as
i18n/link_orphaned_translation.py earlier this session.

What this does:
1. Sets term 1348's gfh_term_lang back to "es" (it currently reads
   "pt" live) -- it already has the clean, un-suffixed "noticias"
   slug, so Spanish keeping it is the right outcome: that's the slug
   the original, correct creation produced.
2. Creates a NEW, separate term "Notícias" with the disambiguated
   slug "noticias-pt" for Portuguese -- exactly what the now-fixed
   setup_category_translations.py would have created had the
   collision bug not existed.
3. Links both into category 5's gfh_term_translations.

Confirms the final state afterward via a fresh GET, rather than
assuming the writes succeeded.

Usage: python3 i18n/repair_news_pt_es_collision.py [--dry-run]
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

NEWS_CATEGORY_ID = 5
MISLABELED_TERM_ID = 1348  # "Noticias", currently gfh_term_lang=pt, should be es


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--dry-run", action="store_true")
    args = parser.parse_args()

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    # --- Sanity check the incident is still in the exact state expected ---
    term_resp = request_with_retry("GET", f"{WP_BASE}/categories/{MISLABELED_TERM_ID}", auth=auth, timeout=30)
    term_resp.raise_for_status()
    term = term_resp.json()
    print(f"Term {MISLABELED_TERM_ID}: slug={term['slug']!r} name={term['name']!r} gfh_term_lang={term.get('gfh_term_lang')!r}")

    if term["slug"] != "noticias" or term.get("gfh_term_lang") != "pt":
        print(
            "This term is NOT in the expected mislabeled state (slug=noticias, gfh_term_lang=pt) -- "
            "someone may have already fixed this, or the state has changed. Stopping rather than guessing.",
            file=sys.stderr,
        )
        sys.exit(1)

    news_resp = request_with_retry("GET", f"{WP_BASE}/categories/{NEWS_CATEGORY_ID}", auth=auth, timeout=30)
    news_resp.raise_for_status()
    news_term = news_resp.json()
    existing = news_term.get("gfh_term_translations") or {}
    print(f"Category {NEWS_CATEGORY_ID} (News) current translations: {existing}")

    if "es" in existing:
        print("Category 5 already has an 'es' link -- someone may have already fixed this. Stopping rather than guessing.", file=sys.stderr)
        sys.exit(1)

    if args.dry_run:
        print(f"\nWOULD set term {MISLABELED_TERM_ID}'s gfh_term_lang back to 'es'")
        print("WOULD create a new term 'Notícias' (slug noticias-pt) for Portuguese")
        print("WOULD link both into category 5's gfh_term_translations")
        return

    # --- Step 1: fix the mislabeled term back to Spanish ---
    request_with_retry(
        "POST", f"{WP_BASE}/categories/{MISLABELED_TERM_ID}",
        json={"gfh_term_lang": "es"},
        auth=auth, timeout=30,
    ).raise_for_status()
    print(f"Set term {MISLABELED_TERM_ID}'s gfh_term_lang back to 'es'")

    # --- Step 2: create the genuinely separate Portuguese term ---
    create_resp = request_with_retry(
        "POST", f"{WP_BASE}/categories",
        json={"name": "Notícias", "slug": "noticias-pt", "parent": 0},
        auth=auth, timeout=30,
    )
    create_resp.raise_for_status()
    pt_term = create_resp.json()
    pt_id = pt_term["id"]
    print(f"Created new Portuguese term 'Notícias' (slug noticias-pt, id {pt_id})")

    request_with_retry(
        "POST", f"{WP_BASE}/categories/{pt_id}",
        json={"gfh_term_lang": "pt"},
        auth=auth, timeout=30,
    ).raise_for_status()
    print(f"Set term {pt_id}'s gfh_term_lang to 'pt'")

    # --- Step 3: link both into category 5 ---
    request_with_retry(
        "POST", f"{WP_BASE}/categories/{NEWS_CATEGORY_ID}",
        json={"gfh_term_translations": {"es": MISLABELED_TERM_ID, "pt": pt_id}},
        auth=auth, timeout=90,
    ).raise_for_status()
    print(f"Linked es={MISLABELED_TERM_ID} and pt={pt_id} into category {NEWS_CATEGORY_ID}'s gfh_term_translations")

    # --- Verify the final state ---
    final_resp = request_with_retry("GET", f"{WP_BASE}/categories/{NEWS_CATEGORY_ID}", auth=auth, timeout=30)
    final_resp.raise_for_status()
    final = final_resp.json()
    print(f"\nFinal category {NEWS_CATEGORY_ID} (News) gfh_term_translations: {final.get('gfh_term_translations')}")

    final_term_resp = request_with_retry("GET", f"{WP_BASE}/categories/{MISLABELED_TERM_ID}", auth=auth, timeout=30)
    final_term_resp.raise_for_status()
    final_term = final_term_resp.json()
    print(f"Final term {MISLABELED_TERM_ID}: slug={final_term['slug']!r} gfh_term_lang={final_term.get('gfh_term_lang')!r}")


if __name__ == "__main__":
    main()
