#!/usr/bin/env python3
"""
One-off cleanup for the Polish duplicate posts confirmed live
2026-11-15 while investigating why pl (806 live posts) so badly
outnumbers en (254). Of the 563 Polish "orphan" posts (not the
genuine, linked pl translation of any English source --
i18n/compare_lang_post_counts.py / i18n/list_orphaned_uncategorized_posts.py),
384 are standalone, unique-title content (left alone -- direct-language
news with no English source by design) and 179 sit in 56 same-title
clusters confirmed, via i18n/inspect_pl_duplicate_clusters.py, to be
the duplicate-post bug just fixed in i18n/translate.py
(link_back_to_source()): a translation gets created and published,
the write linking it back to its English source fails, and the next
run -- unable to see the unlinked post -- creates another one from
scratch.

For each cluster, a post is KEPT if it is the one its OWN
gfh_translations group currently names for "pl" (self-consistent --
the live, currently-linked member; a cluster can have more than one
of these if the English source article was itself republished more
than once, each run spawning its own complete group) or, when NO
member in a cluster is self-consistent, the earliest-published one.
Every other member -- a stale leftover already superseded by a later
write to the same group, or clearly outranked within its cluster -- is
trashed. This is a hardcoded, discovered list (not re-derived here)
so this script can only ever act on exactly the 104 posts already
confirmed to match that classification, same discipline as
i18n/trash_duplicate_mistagged_posts.py.

Sanity-checks each post's CURRENT date and categories against what
was found at classification time before trashing -- stops rather than
guessing if a post has since changed. Moves each to trash via DELETE
force=false (WordPress's own reversible soft-delete), then verifies
the final status with a fresh GET.

Usage:
    python3 i18n/trash_pl_duplicate_posts.py --mode dry-run
    python3 i18n/trash_pl_duplicate_posts.py --mode execute
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (id, expected date, expected categories) at classification time
# 2026-11-15, from i18n/inspect_pl_duplicate_clusters.py's output.
TRASH_RECORDS = [
    (5552, "2026-11-08T11:32:48", [1]),
    (5882, "2026-11-08T13:56:47", [1]),
    (5952, "2026-11-08T14:27:35", [1]),
    (4969, "2026-11-07T12:54:24", [2]),
    (5516, "2026-11-08T11:16:52", [1]),
    (5760, "2026-11-08T13:02:05", [1]),
    (5916, "2026-11-08T14:11:26", [1]),
    (5008, "2026-11-07T13:12:12", [2]),
    (5474, "2026-11-08T10:58:49", [1]),
    (5848, "2026-11-08T13:39:39", [1]),
    (5518, "2026-11-08T11:17:46", [1]),
    (5762, "2026-11-08T13:02:56", [1]),
    (6045, "2026-11-08T15:09:47", [2]),
    (5524, "2026-11-08T11:20:41", [1]),
    (5650, "2026-11-08T12:13:14", [1]),
    (5544, "2026-11-08T11:29:06", [1]),
    (5670, "2026-11-08T12:22:35", [1]),
    (5788, "2026-11-08T13:14:38", [1]),
    (5944, "2026-11-08T14:24:25", [1]),
    (6071, "2026-11-08T15:23:58", [1]),
    (5554, "2026-11-08T11:33:37", [1]),
    (5680, "2026-11-08T12:26:57", [1]),
    (5798, "2026-11-08T13:18:25", [1]),
    (5954, "2026-11-08T14:29:03", [1]),
    (5556, "2026-11-08T11:34:21", [1]),
    (5682, "2026-11-08T12:27:41", [1]),
    (5800, "2026-11-08T13:19:09", [1]),
    (6085, "2026-11-08T15:29:08", [1]),
    (5684, "2026-11-08T12:28:26", [1]),
    (5802, "2026-11-08T13:19:54", [1]),
    (5958, "2026-11-08T14:30:09", [1]),
    (6087, "2026-11-08T15:29:52", [1]),
    (5508, "2026-11-08T11:12:52", [1]),
    (5634, "2026-11-08T12:06:39", [1]),
    (5752, "2026-11-08T12:56:50", [1]),
    (5908, "2026-11-08T14:06:43", [1]),
    (5672, "2026-11-08T12:23:20", [1]),
    (5790, "2026-11-08T13:15:23", [1]),
    (5946, "2026-11-08T14:25:18", [1]),
    (6073, "2026-11-08T15:24:43", [1]),
    (5560, "2026-11-08T11:35:51", [1]),
    (5804, "2026-11-08T13:20:38", [1]),
    (6089, "2026-11-08T15:30:37", [1]),
    (4745, "2026-11-07T11:18:44", [5]),
    (4766, "2026-11-07T11:31:40", [5]),
    (5614, "2026-11-08T11:57:56", [1]),
    (5020, "2026-11-07T13:17:36", [2]),
    (5528, "2026-11-08T11:22:24", [1]),
    (5538, "2026-11-08T11:26:46", [1]),
    (5664, "2026-11-08T12:19:44", [1]),
    (5606, "2026-11-08T11:54:54", [1]),
    (5880, "2026-11-08T13:55:26", [1]),
    (6006, "2026-11-08T14:51:42", [3]),
    (5780, "2026-11-08T13:11:37", [1]),
    (5936, "2026-11-08T14:21:25", [1]),
    (6063, "2026-11-08T15:19:37", [1]),
    (4810, "2026-11-07T11:50:15", [5]),
    (4888, "2026-11-07T12:21:20", [5]),
    (5640, "2026-11-08T12:08:59", [1]),
    (6041, "2026-11-08T15:08:01", [2]),
    (5688, "2026-11-08T12:30:06", [1]),
    (6091, "2026-11-08T15:31:27", [1]),
    (5564, "2026-11-08T11:37:21", [1]),
    (5964, "2026-11-08T14:33:03", [1]),
    (5976, "2026-11-08T14:38:20", [1]),
    (6105, "2026-11-08T15:39:07", [1]),
    (5878, "2026-11-08T13:54:41", [1]),
    (6004, "2026-11-08T14:50:56", [3]),
    (5612, "2026-11-08T11:57:12", [1]),
    (6012, "2026-11-08T14:54:04", [5]),
    (5648, "2026-11-08T12:12:24", [1]),
    (5922, "2026-11-08T14:14:49", [1]),
    (5668, "2026-11-08T12:21:51", [1]),
    (6069, "2026-11-08T15:23:11", [1]),
    (5698, "2026-11-08T12:33:48", [1]),
    (6014, "2026-11-08T14:54:47", [5]),
    (5124, "2026-11-07T14:07:22", [1]),
    (4764, "2026-11-07T11:29:13", [5]),
    (5138, "2026-11-07T14:13:09", [1]),
    (5470, "2026-11-08T10:56:53", [1]),
    (5876, "2026-11-08T13:53:55", [1]),
    (6024, "2026-11-08T14:59:39", [5]),
    (5920, "2026-11-08T14:13:53", [1]),
    (5932, "2026-11-08T14:19:53", [1]),
    (5942, "2026-11-08T14:23:40", [1]),
    (5948, "2026-11-08T14:26:04", [1]),
    (5570, "2026-11-08T11:39:38", [1]),
    (5578, "2026-11-08T11:42:36", [1]),
    (5584, "2026-11-08T11:44:54", [1]),
    (5898, "2026-11-08T14:02:54", [1]),
    (6039, "2026-11-08T15:06:34", [5]),
    (5652, "2026-11-08T12:14:45", [1]),
    (6059, "2026-11-08T15:18:06", [2]),
    (6067, "2026-11-08T15:21:48", [1]),
    (5722, "2026-11-08T12:43:16", [1]),
    (5828, "2026-11-08T13:30:16", [1]),
    (5770, "2026-11-08T13:06:37", [1]),
    (5774, "2026-11-08T13:09:05", [1]),
    (5776, "2026-11-08T13:10:06", [1]),
    (5938, "2026-11-08T14:22:11", [1]),
    (5814, "2026-11-08T13:25:06", [1]),
    (5950, "2026-11-08T14:26:50", [1]),
    (6010, "2026-11-08T14:53:19", [5]),
    (6035, "2026-11-08T15:04:32", [5]),
]


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
    for post_id, expected_date, expected_categories in TRASH_RECORDS:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts/{post_id}",
            params={"_fields": "id,slug,status,date,categories,gfh_lang"},
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        post = resp.json()
        matches = (
            post["status"] == "publish"
            and post.get("gfh_lang") == "pl"
            and post.get("date") == expected_date
            and post.get("categories") == expected_categories
        )
        print(
            f"post {post_id} (slug {post['slug']}): status={post['status']} date={post.get('date')} "
            f"categories={post.get('categories')} gfh_lang={post.get('gfh_lang')!r} -- "
            f"{'matches expected state, OK to trash' if matches else 'DOES NOT MATCH expected state -- skipping'}"
        )
        if matches:
            ok_to_trash.append(post_id)
        else:
            print(f"  expected status=publish gfh_lang=pl date={expected_date} categories={expected_categories}")

    print(f"\n{len(ok_to_trash)} of {len(TRASH_RECORDS)} post(s) match and are OK to trash.")

    if args.mode == "dry-run":
        print("Dry run only -- nothing changed.")
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
        print(f"  trashed post {post_id}")

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

    print(f"\nDone. {len(ok_to_trash) - len(failures)} trashed, {len(failures)} failed.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
