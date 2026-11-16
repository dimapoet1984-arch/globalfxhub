#!/usr/bin/env python3
"""
Read-only spot-check before adding fr/pl to GLOBALFXHUB_LIVE_LANGUAGES
(inc/i18n-live-languages.php): pulls a handful of real fr and pl posts
and prints title + a content excerpt so the content can be eyeballed
for sanity (not literally still English, no obviously broken HTML
entities, no leftover glossary placeholders) before flipping the
SEO-indexing gate live. This is the human-judgment check
docs/TRANSLATION_PLAYBOOK.md section 5 calls for before launch.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/spot_check_fr_pl.py
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"


def strip_html(html):
    text = re.sub(r"<[^>]+>", " ", html or "")
    return re.sub(r"\s+", " ", text).strip()


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    # This theme's custom REST fields (inc/i18n-rest-connector.php) only
    # expose gfh_lang per-item on GET -- they don't wire up `?lang=` query
    # filtering. Fetch recent batches with no language filter and filter
    # by gfh_lang client-side, paginating back until both languages have
    # samples (fr/pl each have hundreds of posts, so this usually resolves
    # within the first page, but paginate defensively anyway).
    for post_type in ("posts", "pages"):
        batch = []
        for page in (1, 2, 3, 4, 5):
            resp = request_with_retry(
                "GET", f"{WP_BASE}/{post_type}",
                params={
                    "per_page": 100, "page": page, "orderby": "id", "order": "desc",
                    "_fields": "id,slug,title,content,gfh_lang",
                },
                auth=auth, timeout=30,
            )
            resp.raise_for_status()
            page_items = resp.json()
            if not page_items:
                break
            batch.extend(page_items)
            if all(len([p for p in batch if p.get("gfh_lang") == lang]) >= 3 for lang in ("fr", "pl")):
                break
        for lang in ("fr", "pl"):
            print(f"=== {lang} ({post_type}) ===\n")
            items = [p for p in batch if p.get("gfh_lang") == lang][:3]
            for item in items:
                title = strip_html((item.get("title") or {}).get("rendered", ""))
                content = strip_html((item.get("content") or {}).get("rendered", ""))
                print(f"  [{post_type}] id={item['id']} slug={item['slug']}")
                print(f"    title: {title}")
                print(f"    content (first 300 chars): {content[:300]}")
                print()


if __name__ == "__main__":
    main()
