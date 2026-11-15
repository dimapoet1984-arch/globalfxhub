#!/usr/bin/env python3
"""
Read-only companion to i18n/backfill_post_categories.py: dumps the FULL
list of posts that script's "no English source found" bucket skips --
i.e. a non-English post still stuck on categories == [1] where no
English post's gfh_translations points back at it.

backfill_post_categories.py's own dry-run output only logs these as it
goes (useful for a quick read, not for a focused follow-up investigation).
This script reuses its exact classification logic
(build_translation_sources / categories == [1] check) but prints every
match with title, slug, status and date so the full set can be pulled
out for a duplicate/orphaned-content review -- these are suspected to be
duplicate translations from a past slug-collision issue, not a simple
categorization gap, based on sampling during the original dry-run.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/list_orphaned_uncategorized_posts.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"


def fetch_all_posts(auth):
    posts = []
    page = 1
    while True:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts",
            params={
                "per_page": 100, "page": page, "orderby": "id", "order": "asc",
                "status": "publish,draft,pending,private,future,trash",
                "_fields": "id,slug,status,date,categories,gfh_lang,gfh_translations,title",
            },
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        posts.extend(batch)
        if len(batch) < 100:
            break
        page += 1
    return posts


def build_translation_sources(all_posts):
    sources = {}
    for post in all_posts:
        if post.get("gfh_lang") not in (None, "", "en"):
            continue
        source_categories = post.get("categories") or []
        if not source_categories:
            continue
        for lang, translated_id in (post.get("gfh_translations") or {}).items():
            sources[translated_id] = (source_categories, post["id"], post["slug"])
    return sources


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    all_posts = fetch_all_posts(auth)
    print(f"Fetched {len(all_posts)} posts total\n")

    sources = build_translation_sources(all_posts)

    orphans = []
    for post in all_posts:
        lang = post.get("gfh_lang")
        if lang in (None, "", "en"):
            continue
        current_categories = post.get("categories") or []
        if current_categories != [1]:
            continue
        if sources.get(post["id"]):
            continue
        orphans.append(post)

    by_lang = {}
    for post in orphans:
        by_lang.setdefault(post.get("gfh_lang") or "(none)", []).append(post)

    print(f"=== {len(orphans)} orphaned Uncategorized post(s): no English source found ===\n")
    for lang in sorted(by_lang, key=lambda l: -len(by_lang[l])):
        items = by_lang[lang]
        print(f"--- {lang}: {len(items)} ---")
        for post in items:
            title = (post.get("title") or {}).get("rendered", "")
            print(
                f"id={post['id']} status={post['status']} date={post['date']} "
                f"slug={post['slug']} title={title!r}"
            )
        print()


if __name__ == "__main__":
    main()
