#!/usr/bin/env python3
"""
Read-only spot-check before adding it/pt to GLOBALFXHUB_LIVE_LANGUAGES
(inc/i18n-live-languages.php): pulls a handful of real it and pt posts
and prints title + a content excerpt so the translation can be
eyeballed for sanity (not literally still English, no obviously broken
HTML entities, no leftover glossary placeholders) before flipping the
SEO-indexing gate live. Doesn't replace per-piece markup verification
(i18n/translate.py's splitter/round-trip/retry/review already
guarantees structural HTML safety per item) -- this is specifically
the human-judgment check docs/TRANSLATION_PLAYBOOK.md section 5 calls
for before launch.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/spot_check_it_pt.py
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

    for lang in ("it", "pt"):
        print(f"=== {lang} ===\n")
        for post_type in ("posts", "pages"):
            resp = request_with_retry(
                "GET", f"{WP_BASE}/{post_type}",
                params={
                    "per_page": 3, "orderby": "id", "order": "desc",
                    "_fields": "id,slug,title,content,gfh_lang",
                    "lang": lang,
                },
                auth=auth, timeout=30,
            )
            resp.raise_for_status()
            items = resp.json()
            for item in items:
                title = strip_html((item.get("title") or {}).get("rendered", ""))
                content = strip_html((item.get("content") or {}).get("rendered", ""))
                print(f"  [{post_type}] id={item['id']} slug={item['slug']}")
                print(f"    title: {title}")
                print(f"    content (first 300 chars): {content[:300]}")
                print()


if __name__ == "__main__":
    main()
