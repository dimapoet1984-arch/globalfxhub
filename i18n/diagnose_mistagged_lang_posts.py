#!/usr/bin/env python3
"""
Read-only diagnostic for 7 specific posts flagged during the orphaned-
Uncategorized-posts review (2026-11-15) as having a gfh_lang value that
doesn't match the actual language of their title/content -- found by
scanning i18n/list_orphaned_uncategorized_posts.py's output for Arabic
script under gfh_lang=pl, and non-Arabic script under gfh_lang=ar.

Dumps full title + plain-text content (HTML stripped) for each, plus
its current gfh_lang/gfh_translations/categories, so the correct
language can be confirmed by actually reading the content rather than
guessing from the slug or title alone.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/diagnose_mistagged_lang_posts.py
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

POST_IDS = [5480, 5494, 5540, 5842, 5844, 2621, 2643]


def strip_html(html):
    text = re.sub(r"<[^>]+>", " ", html or "")
    text = re.sub(r"\s+", " ", text).strip()
    return text


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    for post_id in POST_IDS:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts/{post_id}",
            params={
                "_fields": "id,slug,status,date,categories,gfh_lang,gfh_translations,title,content,excerpt",
            },
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        post = resp.json()
        title = strip_html((post.get("title") or {}).get("rendered", ""))
        content = strip_html((post.get("content") or {}).get("rendered", ""))
        print(f"=== post {post['id']} ===")
        print(f"slug: {post['slug']}")
        print(f"status: {post['status']}  date: {post['date']}")
        print(f"gfh_lang: {post.get('gfh_lang')!r}")
        print(f"gfh_translations: {post.get('gfh_translations')}")
        print(f"categories: {post.get('categories')}")
        print(f"title: {title}")
        print(f"content (first 600 chars): {content[:600]}")
        print()


if __name__ == "__main__":
    main()
