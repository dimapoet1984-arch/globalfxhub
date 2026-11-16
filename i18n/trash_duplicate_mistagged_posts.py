#!/usr/bin/env python3
"""
One-off repair for 7 live duplicate posts found while investigating the
491 orphaned Uncategorized posts (2026-11-15). Each has a title in one
language and a body in a different language, is tagged with a gfh_lang
that matches neither, and -- critically -- its OWN gfh_translations
group already links a DIFFERENT, correctly-tagged post for every
language slot (including the one this post itself claims). That means
each of these 7 is a stray extra copy left over from a past botched
translation run, not a simple mistagging: the real, correctly-linked
translation for every language already exists under a different post
ID, confirmed live via i18n/diagnose_mistagged_lang_posts.py:

  id    claimed gfh_lang   title lang   body lang   real pl/ar/es/en slots already linked
  5480  pl                 ar           pl          pl=6127 ar=1959
  5494  pl                 ar           ar          pl=6141 ar=2087
  5540  pl                 ar           pl          pl=6189 ar=2347
  5842  pl                 ar           ar          pl=5864 ar=3467
  5844  pl                 ar           pl          pl=6245 ar=3473
  2621  ar                 es           ar          ar=3044 es=1174 en=218
  2643  ar                 en           ar          ar=3066 es=1198 en=229

Re-tagging gfh_lang on any of these would be wrong: it would mislabel a
broken duplicate as a second "real" translation in a slot a correct
post already legitimately occupies. The actual fix is to retire the
duplicate. Moves each to trash (WordPress's own soft-delete -- fully
reversible from wp-admin, not a permanent delete) rather than editing
it in place.

Sanity-checks each post's CURRENT state (status=publish, categories
exactly [1], same gfh_lang on file) against what diagnose_mistagged_lang_posts.py
found before trashing -- if any post has since changed, stops rather
than guessing.

Usage:
    python3 i18n/trash_duplicate_mistagged_posts.py --mode dry-run
    python3 i18n/trash_duplicate_mistagged_posts.py --mode execute
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# id -> expected (gfh_lang, categories) at diagnosis time 2026-11-15.
DUPLICATES = {
    5480: ("pl", [1]),
    5494: ("pl", [1]),
    5540: ("pl", [1]),
    5842: ("pl", [1]),
    5844: ("pl", [1]),
    2621: ("ar", [1]),
    2643: ("ar", [1]),
}


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--mode", choices=["dry-run", "execute"], required=True)
    args = parser.parse_args()

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    ok_to_trash = []
    for post_id, (expected_lang, expected_categories) in DUPLICATES.items():
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts/{post_id}",
            params={"_fields": "id,slug,status,categories,gfh_lang"},
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        post = resp.json()
        matches = (
            post["status"] == "publish"
            and post.get("categories") == expected_categories
            and post.get("gfh_lang") == expected_lang
        )
        print(
            f"post {post_id} (slug {post['slug']}): status={post['status']} "
            f"categories={post.get('categories')} gfh_lang={post.get('gfh_lang')!r} -- "
            f"{'matches expected state, OK to trash' if matches else 'DOES NOT MATCH expected state -- skipping'}"
        )
        if matches:
            ok_to_trash.append(post_id)
        else:
            print(f"  expected status=publish categories={expected_categories} gfh_lang={expected_lang!r}")

    if args.mode == "dry-run":
        print(f"\nDry run only -- would trash {len(ok_to_trash)} of {len(DUPLICATES)} post(s). Nothing changed.")
        return

    print(f"\nTrashing {len(ok_to_trash)} post(s)...")
    failures = []
    for post_id in ok_to_trash:
        resp = request_with_retry(
            "DELETE", f"{WP_BASE}/posts/{post_id}",
            params={"force": "false"},
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        result = resp.json()
        new_status = (result.get("previous") or {}).get("status") or result.get("status")
        if result.get("trashed") or new_status == "trash" or result.get("status") == "trash":
            print(f"  trashed post {post_id}")
        else:
            failures.append(post_id)
            print(f"  post {post_id}: unexpected response, verifying with a fresh GET")

    # --- Verify final state ---
    print("\nVerifying final state:")
    for post_id in ok_to_trash:
        verify = request_with_retry(
            "GET", f"{WP_BASE}/posts/{post_id}",
            params={"_fields": "id,status", "context": "edit"},
            auth=auth, timeout=30,
        )
        verify.raise_for_status()
        status = verify.json().get("status")
        print(f"  post {post_id}: status={status}")
        if status != "trash":
            failures.append(post_id)

    print(f"\nDone. {len(ok_to_trash) - len(set(failures))} trashed, {len(set(failures))} failed.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
