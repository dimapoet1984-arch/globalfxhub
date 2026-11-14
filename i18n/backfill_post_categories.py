#!/usr/bin/env python3
"""
One-time backfill: fixes every already-created Track A translated
post that landed in WordPress's default "Uncategorized" category
(id 1) because translate.py never read or set a category on a
translated post before this session's fix (commit
"Assign translated categories when creating Track A translations") --
confirmed live 2026-10-30: 392 posts, all non-English, under
GET /wp-json/wp/v2/posts?categories=1.

For each English source post, resolves its own real category
(the "categories" field) through the Polylang term-translation links
i18n/setup_category_translations.py created
(inc/i18n-term-rest-connector.php's gfh_term_translations), via the
exact same translate.py helpers the live pipeline now uses
(fetch_category_term_map / resolve_translated_categories), then
updates the matching translated post -- found by walking the
source's own gfh_translations, not by guessing from the slug.

Only ever touches a post whose current categories are EXACTLY [1]
(the default, unmodified since creation) -- anything else is left
alone rather than guessed at, same "stop rather than guess" discipline
as i18n/repair_news_pt_es_collision.py. A post with no known English
source, or whose source category has no translation on file for this
post's language, is reported and skipped, never force-categorized.

Idempotent: safe to re-run -- a post already fixed no longer has
categories == [1], so a second run counts it as "already had a
non-default category" and leaves it untouched.

Usage:
    python3 i18n/backfill_post_categories.py --dry-run
    python3 i18n/backfill_post_categories.py
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import fetch_category_term_map, request_with_retry, resolve_translated_categories

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"


def fetch_all_posts(auth):
    """Same id-ordered pagination as translate.py's fetch_all_items,
    but for every post regardless of language -- this backfill needs
    to see both the English sources (for their real category) and
    every translated post (to find the ones still stuck on [1])."""
    posts = []
    page = 1
    while True:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts",
            params={
                "per_page": 100, "page": page, "orderby": "id", "order": "asc",
                "_fields": "id,slug,categories,gfh_lang,gfh_translations",
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
    """translated post id -> (source lang-agnostic category ids, source id, source slug),
    read off every ENGLISH post's own gfh_translations."""
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
    parser = argparse.ArgumentParser()
    parser.add_argument("--dry-run", action="store_true", help="Report what would change, without writing anything.")
    args = parser.parse_args()

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    category_term_map = fetch_category_term_map(auth)
    print(f"Category term map (English category id -> {{lang: translated id}}): {category_term_map}")

    all_posts = fetch_all_posts(auth)
    print(f"Fetched {len(all_posts)} posts total")

    sources = build_translation_sources(all_posts)

    fixed = 0
    skipped_no_source = 0
    skipped_no_resolution = 0
    already_fine = 0

    for post in all_posts:
        lang = post.get("gfh_lang")
        if lang in (None, "", "en"):
            continue

        current_categories = post.get("categories") or []
        if current_categories != [1]:
            already_fine += 1
            continue

        source = sources.get(post["id"])
        if not source:
            skipped_no_source += 1
            print(f"  post {post['id']} (slug {post['slug']}, lang {lang}): no English source found pointing at it -- skipping")
            continue

        source_categories, source_id, source_slug = source
        resolved = resolve_translated_categories(source_categories, lang, category_term_map)
        if not resolved:
            skipped_no_resolution += 1
            print(
                f"  post {post['id']} (slug {post['slug']}, lang {lang}): source post {source_id} "
                f"({source_slug})'s categories {source_categories} have no {lang} translation on file -- skipping"
            )
            continue

        if args.dry_run:
            print(
                f"  post {post['id']} (slug {post['slug']}, lang {lang}): WOULD set categories to {resolved} "
                f"(from source {source_id}/{source_slug}'s {source_categories})"
            )
            fixed += 1
            continue

        request_with_retry(
            "POST", f"{WP_BASE}/posts/{post['id']}",
            json={"categories": resolved}, auth=auth, timeout=30,
        ).raise_for_status()
        print(f"  post {post['id']} (slug {post['slug']}, lang {lang}): set categories to {resolved}")
        fixed += 1

    verb = "would be fixed" if args.dry_run else "fixed"
    print(
        f"\n{fixed} post(s) {verb}, {skipped_no_source} skipped (no source found), "
        f"{skipped_no_resolution} skipped (no translation on file), "
        f"{already_fine} already had a non-default category"
    )


if __name__ == "__main__":
    main()
