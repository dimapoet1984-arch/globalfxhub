#!/usr/bin/env python3
"""
One-off cleanup for the French duplicate posts confirmed live
2026-11-15, same bug and same discipline as
i18n/trash_pl_duplicate_posts.py / i18n/trash_ar_duplicate_posts.py:
of the 210 French orphan posts (not the genuine, linked fr translation
of any English source), 154 are standalone unique-title content (left
alone), 5 are recurring editorial templates spread across days (also
left alone -- not duplicates), and 51 sit in 22 same-title clusters
confirmed, via i18n/inspect_fr_duplicate_clusters.py, to be the
duplicate-post bug fixed in i18n/translate.py (link_back_to_source()).

For each cluster, a post is KEPT if it is the one its OWN
gfh_translations group currently names for "fr" (self-consistent --
the live, currently-linked member; two clusters have TWO
self-consistent members each because their English source article
was itself republished a second time, each run spawning its own
complete group -- both are genuinely live and kept) or, when no
member in a cluster is self-consistent, the earliest-published one.
Every other member is trashed. Hardcoded, discovered list -- not
re-derived here -- same discipline as the pl/ar scripts and
i18n/trash_duplicate_mistagged_posts.py.

Sanity-checks each post's CURRENT date and categories against what
was found at classification time before trashing. Moves each to
trash via DELETE force=false (reversible), then verifies the final
status with a fresh GET.

Usage:
    python3 i18n/trash_fr_duplicate_posts.py --mode dry-run
    python3 i18n/trash_fr_duplicate_posts.py --mode execute
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (id, expected date, expected categories) at classification time
# 2026-11-15, from i18n/inspect_fr_duplicate_clusters.py's output.
TRASH_RECORDS = [
    (3960, "2026-11-05T08:28:29", [1]),
    (3962, "2026-11-05T08:28:55", [1]),
    (4108, "2026-11-05T09:37:06", [1360]),
    (4010, "2026-11-05T08:50:11", [23]),
    (4038, "2026-11-05T09:05:41", [23]),
    (4164, "2026-11-05T10:03:54", [23]),
    (4046, "2026-11-05T09:09:26", [23]),
    (4172, "2026-11-05T10:07:00", [23]),
    (4050, "2026-11-05T09:11:33", [23]),
    (4176, "2026-11-05T10:09:06", [23]),
    (4180, "2026-11-05T10:10:40", [23]),
    (3968, "2026-11-05T08:31:40", [23]),
    (4096, "2026-11-05T09:32:32", [1333]),
    (3996, "2026-11-05T08:43:51", [23]),
    (4138, "2026-11-05T09:51:52", [1333]),
    (4140, "2026-11-05T09:52:44", [1333]),
    (4016, "2026-11-05T08:54:33", [23]),
    (4032, "2026-11-05T09:02:38", [23]),
    (4062, "2026-11-05T09:17:18", [23]),
    (4106, "2026-11-05T09:36:21", [1360]),
    (4128, "2026-11-05T09:46:33", [1360]),
    (4162, "2026-11-05T10:03:06", [23]),
    (4174, "2026-11-05T10:08:23", [23]),
    (4178, "2026-11-05T10:09:55", [23]),
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
            and post.get("gfh_lang") == "fr"
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
            print(f"  expected status=publish gfh_lang=fr date={expected_date} categories={expected_categories}")

    print(f"\n{len(ok_to_trash)} of {len(TRASH_RECORDS)} post(s) match and are OK to trash.")

    if args.mode == "dry-run":
        print("Dry run only -- nothing changed.")
        return

    print(f"\nTrashing {len(ok_to_trash)} post(s)...")
    for post_id in ok_to_trash:
        resp = request_with_retry(
            "DELETE", f"{WP_BASE}/posts/{post_id}",
            params={"force": "false"},
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        print(f"  trashed post {post_id}")

    print("\nVerifying final state:")
    failures = []
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
