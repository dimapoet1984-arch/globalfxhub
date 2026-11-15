#!/usr/bin/env python3
"""
Read-only: compares the number of English (source) posts to the number
of posts in every other language, and explains the gap rather than
just reporting it.

A raw per-language post count alone conflates two very different
things:
  1. Translation backlog -- an English source post that simply hasn't
     been translated into a given language yet (gfh_translations has
     no entry for that language).
  2. Orphan/duplicate surplus -- a post that exists in a target
     language but ISN'T the one actually linked from any English
     source's gfh_translations (the same "no English source found"
     bucket i18n/list_orphaned_uncategorized_posts.py dumps, plus any
     post whose gfh_lang doesn't match the slot it occupies).

This script fetches every post (any status) and, for each non-English
post, checks whether it is the specific post ID some English source's
gfh_translations names for its language -- not just whether its
gfh_lang matches. That separates "still needs to be translated" from
"something unlinked exists in this language that isn't the real
translation."

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/compare_lang_post_counts.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

ALL_STATUSES = "publish,draft,pending,private,future,trash"


def fetch_all_posts(auth):
    posts = []
    page = 1
    while True:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts",
            params={
                "per_page": 100, "page": page, "orderby": "id", "order": "asc",
                "status": ALL_STATUSES,
                "_fields": "id,slug,status,categories,gfh_lang,gfh_translations",
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


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    all_posts = fetch_all_posts(auth)
    print(f"Fetched {len(all_posts)} posts total (all statuses, including trash)\n")

    by_id = {p["id"]: p for p in all_posts}

    # --- Raw counts per gfh_lang, split live (not trash) vs trash ---
    raw_counts = {}
    trash_counts = {}
    for p in all_posts:
        lang = p.get("gfh_lang") or "(none)"
        if p["status"] == "trash":
            trash_counts[lang] = trash_counts.get(lang, 0) + 1
        else:
            raw_counts[lang] = raw_counts.get(lang, 0) + 1

    print("=== Raw post counts per gfh_lang (live, i.e. status != trash) ===")
    for lang in sorted(raw_counts, key=lambda l: -raw_counts[l]):
        trashed = trash_counts.get(lang, 0)
        suffix = f"  (+{trashed} in trash)" if trashed else ""
        print(f"  {lang}: {raw_counts[lang]}{suffix}")
    print()

    # --- English sources: what each one's gfh_translations actually links ---
    english_sources = [p for p in all_posts if (p.get("gfh_lang") or "en") == "en" and p["status"] != "trash"]
    total_en = len(english_sources)
    print(f"English (source) posts, live: {total_en}\n")

    target_langs = set()
    for p in english_sources:
        target_langs.update((p.get("gfh_translations") or {}).keys())
    target_langs.discard("en")
    target_langs = sorted(target_langs)

    print("=== Per-language breakdown vs the English baseline ===")
    print(f"{'lang':6} {'actual':>8} {'linked':>8} {'backlog':>9} {'orphan':>8}  explanation")
    for lang in target_langs:
        actual = raw_counts.get(lang, 0)

        linked_ids = set()
        for p in english_sources:
            tid = (p.get("gfh_translations") or {}).get(lang)
            if tid is not None:
                linked_ids.add(tid)

        # linked: the target id exists, is live, and genuinely has this gfh_lang
        linked_valid = 0
        for tid in linked_ids:
            target = by_id.get(tid)
            if target and target["status"] != "trash" and target.get("gfh_lang") == lang:
                linked_valid += 1

        backlog = total_en - len(linked_ids)  # EN sources with no translation link at all for this lang
        orphan = actual - linked_valid  # posts tagged this lang that aren't a valid linked translation

        print(f"{lang:6} {actual:>8} {linked_valid:>8} {backlog:>9} {orphan:>8}")

    print(
        "\nColumns: actual = live posts with gfh_lang==<lang>; linked = of those, how many are the "
        "exact post ID an English source's gfh_translations names for <lang> (a genuine, intact "
        "translation); backlog = English posts with NO translation link at all for <lang> yet "
        "(still to be translated); orphan = posts in <lang> that are NOT a valid linked translation "
        "(broken links, duplicates, or content with no English source -- the same bucket "
        "i18n/list_orphaned_uncategorized_posts.py reports on)."
    )


if __name__ == "__main__":
    main()
