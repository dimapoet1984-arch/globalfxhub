#!/usr/bin/env python3
"""
Read-only diagnostic: every post (any status, any language) currently
in WordPress's default "Uncategorized" category (id 1), grouped by
language. Direct REST query -- not limited to Track A's own notion of
"needs fixing" (i18n/backfill_post_categories.py only looks at posts
with a known English source and a resolvable category translation;
this script just asks "what's in category 1 right now," full stop,
so it also catches anything outside that script's scope).

Makes no changes. Read-only GETs only.

Usage:
    python3 diagnose_uncategorized_posts.py
"""
import os
import sys

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    posts = []
    page = 1
    while True:
        resp = requests.get(
            f"{WP_BASE}/posts",
            params={
                "categories": 1,
                "status": "publish,draft,pending,private,future,trash",
                "per_page": 100,
                "page": page,
                "_fields": "id,slug,status,gfh_lang,title",
                "orderby": "id",
                "order": "asc",
            },
            auth=auth, timeout=30,
        )
        if resp.status_code == 400:
            # Past the last page -- WP returns 400 "invalid page number".
            break
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        posts.extend(batch)
        total_pages = int(resp.headers.get("X-WP-TotalPages", "1"))
        if page >= total_pages:
            break
        page += 1

    print(f"Total posts currently in category 1 (Uncategorized): {len(posts)}\n")

    by_lang = {}
    for post in posts:
        lang = post.get("gfh_lang") or "(no gfh_lang set)"
        by_lang.setdefault(lang, []).append(post)

    for lang in sorted(by_lang, key=lambda l: -len(by_lang[l])):
        items = by_lang[lang]
        print(f"=== {lang}: {len(items)} post(s) ===")
        for post in items[:15]:
            title = (post.get("title") or {}).get("rendered", "")
            print(f"  id={post['id']} status={post['status']} slug={post['slug']} title={title!r}")
        if len(items) > 15:
            print(f"  ... and {len(items) - 15} more")
        print()


if __name__ == "__main__":
    main()
