#!/usr/bin/env python3
"""
Trash the 25 duplicate "Blog" pages created by globalfxhub_ensure_blog_page()
in functions.php -- it looks up a page at path "blog" without accounting for
Polylang's current-language query filtering, and (unlike the already-fixed
container-page sync) never calls pll_set_post_language(), so a request under
a new language context that doesn't find an existing "blog" page creates
another one; WordPress auto-suffixes the slug collision (blog-2, blog-3, ...)
instead of reusing the real page.

Confirmed via i18n/diagnose_blog_duplicate_pages.py against the live site:
the real page is id=10 (slug=blog, lang=en, page_for_posts setting points
at it, created 2026-09-21 -- the original rollout). The 25 below are all
empty-content, status=publish duplicates dated 2026-10-30 through
2026-11-15 (the most recent one created today, confirming the bug is still
live). Hardcoding the exact (id, slug, date) triples below rather than
re-deriving the list live, so this only ever acts on the pages actually
reviewed -- if the live site no longer matches, it stops rather than
guessing.

Usage:
    python3 i18n/trash_duplicate_blog_pages.py --mode dry-run
    python3 i18n/trash_duplicate_blog_pages.py --mode execute

Trashes (never permanently deletes) via the REST API's default DELETE
behavior for a trash-supporting post type -- recoverable from wp-admin's
Trash for 30 days if anything here turns out wrong.
"""
import argparse
import os
import sys
import time

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (id, slug, date) exactly as confirmed live -- id=10 (the real page) is
# deliberately NOT in this list.
DUPLICATES = [
    (761, "blog-2", "2026-10-30T07:41:55"),
    (1304, "blog-3", "2026-10-30T11:13:13"),
    (1322, "blog-4", "2026-10-30T11:21:23"),
    (3108, "blog-5", "2026-11-01T23:05:12"),
    (3126, "blog-6", "2026-11-01T23:07:45"),
    (3140, "blog-7", "2026-11-01T23:09:43"),
    (3158, "blog-8", "2026-11-01T23:12:30"),
    (4382, "blog-9", "2026-11-05T11:48:30"),
    (4400, "blog-10", "2026-11-05T11:58:22"),
    (4414, "blog-11", "2026-11-05T12:04:21"),
    (4432, "blog-12", "2026-11-05T12:13:03"),
    (4434, "blog-13", "2026-11-05T12:13:13"),
    (4450, "blog-14", "2026-11-05T12:21:03"),
    (4462, "blog-15", "2026-11-05T12:26:22"),
    (4480, "blog-16", "2026-11-05T12:35:26"),
    (4482, "blog-17", "2026-11-05T12:35:36"),
    (4500, "blog-18", "2026-11-05T12:44:57"),
    (4514, "blog-19", "2026-11-05T12:52:41"),
    (4532, "blog-20", "2026-11-05T13:01:48"),
    (4670, "blog-21", "2026-11-07T10:35:04"),
    (4686, "blog-22", "2026-11-07T10:45:10"),
    (4702, "blog-23", "2026-11-07T10:55:43"),
    (4712, "blog-24", "2026-11-07T11:03:39"),
    (4717, "blog-25", "2026-11-07T11:05:09"),
    (6302, "blog-26", "2026-11-15T19:29:36"),
]

RETRY_ATTEMPTS = 4
RETRY_BACKOFF_SECONDS = 3


def request_with_retry(method, url, **kwargs):
    last_exc = None
    for attempt in range(1, RETRY_ATTEMPTS + 1):
        try:
            resp = requests.request(method, url, **kwargs)
        except requests.exceptions.RequestException as exc:
            last_exc = exc
            resp = None
        if resp is not None:
            if resp.status_code < 500:
                return resp
            last_exc = requests.exceptions.HTTPError(
                f"{resp.status_code} Server Error for url: {url}", response=resp
            )
        if attempt < RETRY_ATTEMPTS:
            wait = RETRY_BACKOFF_SECONDS * attempt
            print(f"  (transient failure on attempt {attempt}/{RETRY_ATTEMPTS}, retrying in {wait}s: {last_exc})")
            time.sleep(wait)
    raise last_exc


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

    print(f"Verifying {len(DUPLICATES)} pages still match expected (slug, date, empty content, publish) before touching anything...\n")
    verified = []
    mismatches = []
    for post_id, expected_slug, expected_date in DUPLICATES:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/pages/{post_id}",
            params={"_fields": "id,slug,status,date,content"},
            auth=auth, timeout=30,
        )
        if resp.status_code == 404:
            mismatches.append((post_id, "404 not found (already trashed/deleted?)"))
            continue
        resp.raise_for_status()
        page = resp.json()
        content = (page.get("content") or {}).get("rendered", "")
        problems = []
        if page["slug"] != expected_slug:
            problems.append(f"slug={page['slug']!r} != expected {expected_slug!r}")
        if page["date"] != expected_date:
            problems.append(f"date={page['date']!r} != expected {expected_date!r}")
        if content.strip() != "":
            problems.append(f"content is not empty (len={len(content)})")
        if page["status"] != "publish":
            problems.append(f"status={page['status']!r} != 'publish'")
        if problems:
            mismatches.append((post_id, "; ".join(problems)))
        else:
            verified.append((post_id, expected_slug))

    if mismatches:
        print("MISMATCHES -- live state no longer matches what was reviewed, stopping without touching anything:")
        for post_id, reason in mismatches:
            print(f"    id={post_id}: {reason}")
        sys.exit(1)

    print(f"All {len(verified)} verified. {'Would trash' if args.mode == 'dry-run' else 'Trashing'}:")
    for post_id, slug in verified:
        print(f"    id={post_id:<5} slug={slug}")

    if args.mode == "dry-run":
        print("\nDry run only -- nothing was changed. Re-run with mode=execute to actually trash these.")
        return

    print("\nExecuting...")
    trashed = 0
    failures = []
    for post_id, slug in verified:
        try:
            resp = request_with_retry(
                "DELETE", f"{WP_BASE}/pages/{post_id}",
                params={"force": "false"}, auth=auth, timeout=30,
            )
        except requests.exceptions.RequestException as exc:
            failures.append((post_id, slug, None, str(exc)))
            print(f"  FAILED id={post_id} slug={slug}: {exc}")
            continue
        if resp.status_code not in (200, 410):
            failures.append((post_id, slug, resp.status_code, resp.text[:200]))
            print(f"  FAILED id={post_id} slug={slug}: HTTP {resp.status_code}")
        else:
            trashed += 1
            print(f"  trashed id={post_id} slug={slug}")

    print(f"\nDone. {trashed} trashed, {len(failures)} failed.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
