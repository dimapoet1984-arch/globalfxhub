#!/usr/bin/env python3
"""
One-off cleanup for the Arabic duplicate posts confirmed live
2026-11-15, same bug and same discipline as
i18n/trash_pl_duplicate_posts.py: of the 393 Arabic orphan posts (not
the genuine, linked ar translation of any English source), 329 are
standalone unique-title content (left alone) and 64 sit in 29
same-title clusters confirmed, via i18n/inspect_ar_duplicate_clusters.py,
to be the duplicate-post bug fixed in i18n/translate.py
(link_back_to_source()).

For each cluster, a post is KEPT if it is the one its OWN
gfh_translations group currently names for "ar" (self-consistent --
the live, currently-linked member; one cluster, "شرح تنظيم الفوركس من
هيئة FSCA", has TWO self-consistent members because the English
source article was itself republished a second time, each run
spawning its own complete group -- both are genuinely live and kept)
or, when no member in a cluster is self-consistent, the
earliest-published one. Every other member is trashed. Hardcoded,
discovered list -- not re-derived here -- same discipline as
i18n/trash_pl_duplicate_posts.py / i18n/trash_duplicate_mistagged_posts.py.

Sanity-checks each post's CURRENT date and categories against what
was found at classification time before trashing. Moves each to
trash via DELETE force=false (reversible), then verifies the final
status with a fresh GET.

Usage:
    python3 i18n/trash_ar_duplicate_posts.py --mode dry-run
    python3 i18n/trash_ar_duplicate_posts.py --mode execute
"""
import argparse
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (id, expected date, expected categories) at classification time
# 2026-11-15, from i18n/inspect_ar_duplicate_clusters.py's output.
TRASH_RECORDS = [
    (2009, "2026-11-01T19:12:37", [5]),
    (2029, "2026-11-01T19:15:31", [5]),
    (2073, "2026-11-01T19:24:22", [5]),
    (2151, "2026-11-01T19:37:17", [5]),
    (2169, "2026-11-01T19:39:49", [5]),
    (2433, "2026-11-01T20:36:45", [1]),
    (2585, "2026-11-01T21:02:29", [1]),
    (2331, "2026-11-01T20:19:59", [1]),
    (2337, "2026-11-01T20:20:46", [1]),
    (1905, "2026-11-01T18:57:45", [3]),
    (1915, "2026-11-01T18:59:12", [3]),
    (2353, "2026-11-01T20:23:12", [1]),
    (2373, "2026-11-01T20:26:19", [1]),
    (2381, "2026-11-01T20:27:24", [1]),
    (2383, "2026-11-01T20:27:40", [1]),
    (2385, "2026-11-01T20:27:59", [1]),
    (1975, "2026-11-01T19:08:22", [3]),
    (2415, "2026-11-01T20:33:21", [1]),
    (2461, "2026-11-01T20:40:07", [1]),
    (2479, "2026-11-01T20:42:29", [1]),
    (2533, "2026-11-01T20:53:22", [1]),
    (2609, "2026-11-01T21:05:52", [1]),
    (2623, "2026-11-01T21:09:59", [1]),
    (2627, "2026-11-01T21:10:51", [1]),
    (2633, "2026-11-01T21:12:04", [1]),
    (2639, "2026-11-01T21:13:34", [1]),
    (2655, "2026-11-01T21:17:39", [1]),
    (2659, "2026-11-01T21:19:06", [1]),
    (2663, "2026-11-01T21:21:01", [1]),
    (2665, "2026-11-01T21:21:59", [1]),
    (2673, "2026-11-01T21:23:38", [1]),
    (2675, "2026-11-01T21:23:58", [1]),
    (2603, "2026-11-01T21:04:39", [1]),
    (2507, "2026-11-01T20:47:51", [1]),
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
            and post.get("gfh_lang") == "ar"
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
            print(f"  expected status=publish gfh_lang=ar date={expected_date} categories={expected_categories}")

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
