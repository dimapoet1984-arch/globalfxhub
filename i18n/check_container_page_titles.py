#!/usr/bin/env python3
"""
Read-only check: do the auto-duplicated container pages (reviews-<lang>,
learn-<lang>, compare-<lang>, ...) created by
globalfxhub_do_sync_templated_pages_across_languages() in functions.php
have a translated post_title, or the literal English title copied at
creation time? the_title() is called directly in page-reviews.php,
page-learn.php, page-regulation.php (confirmed via grep) so whatever
post_title holds renders as the visible <h1>/breadcrumb for every
language.

Checks es/de (already live) alongside it/pt (just flipped live) and
fr/pl (candidate) to find out whether this is a known-fixed, per-
language task that simply hasn't run for the newer languages yet, or a
site-wide defect that also affects the already-live languages.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/check_container_page_titles.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

SLUGS = [
    "reviews", "learn", "compare", "news", "countries", "best",
    "regulation", "research", "regulators", "data", "broker-finder",
    "cost-calculator", "regulation-checker", "broker-changelog",
]
ENGLISH_TITLES = {
    "reviews": "Reviews", "learn": "Learn", "compare": "Compare Brokers",
    "news": "News", "countries": "Countries", "best": "Best Brokers",
    "regulation": "Regulation", "research": "Research",
    "regulators": "Regulators", "data": "GlobalFXHub Data",
    "broker-finder": "Broker Finder",
    "cost-calculator": "Forex Spread Cost Calculator",
    "regulation-checker": "Broker Regulation Checker",
    "broker-changelog": "Broker Change Log",
}
LANGS = ["es", "de", "it", "pt", "fr", "pl"]


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    for lang in LANGS:
        print(f"=== {lang} ===")
        for slug in SLUGS:
            canonical_slug = f"{slug}-{lang}"
            resp = request_with_retry(
                "GET", f"{WP_BASE}/pages",
                params={"slug": canonical_slug, "_fields": "id,slug,title,status"},
                auth=auth, timeout=30,
            )
            resp.raise_for_status()
            results = resp.json()
            if not results:
                print(f"  {canonical_slug}: NOT FOUND")
                continue
            page = results[0]
            title = (page.get("title") or {}).get("rendered", "")
            english = ENGLISH_TITLES[slug]
            flag = "UNTRANSLATED" if title.strip() == english else "translated"
            print(f"  {canonical_slug}: id={page['id']} status={page.get('status')} title={title!r} [{flag}]")
        print()


if __name__ == "__main__":
    main()
