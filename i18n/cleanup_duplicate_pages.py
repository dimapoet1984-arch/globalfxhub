#!/usr/bin/env python3
"""
Emergency cleanup for the duplicate pages created by
globalfxhub_sync_templated_pages_across_languages()'s race condition
on 2026-10-29 (see functions.php's "EMERGENCY DISABLE" comment and
docs/TRANSLATION_PLAYBOOK.md section 3.4). That function has since
been disabled; this cleans up what it already created.

Identifies every page whose slug is one of the 11 managed base slugs
followed by -N (e.g. "reviews-7") -- confirmed via the live REST API
to all be empty-content, status=publish duplicates created in the
same few seconds today. Only ever acts on pages matching exactly that
pattern AND with empty content: a duplicate-looking slug that somehow
already has real content is left alone and flagged for a human to
look at, never auto-trashed.

Usage:
    python3 cleanup_duplicate_pages.py --mode dry-run
    python3 cleanup_duplicate_pages.py --mode execute

Trashes (never permanently deletes) via the REST API's default
DELETE behavior for a trash-supporting post type -- recoverable from
wp-admin's Trash for 30 days if anything here turns out wrong.
"""
import argparse
import os
import re
import sys

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"
MANAGED_SLUGS = [
    "reviews", "learn", "compare", "news", "countries", "best",
    "regulation", "broker-finder", "cost-calculator",
    "regulation-checker", "broker-changelog",
]
DUPLICATE_RE = re.compile(r"^(" + "|".join(MANAGED_SLUGS) + r")-\d+$")
HOME_STUB_RE = re.compile(r"^home-.")


def fetch_all_pages(auth):
    pages = []
    page_num = 1
    while True:
        resp = requests.get(
            f"{WP_BASE}/pages",
            params={
                "per_page": 100,
                "page": page_num,
                "_fields": "id,slug,status,date,content",
            },
            auth=auth,
            timeout=30,
        )
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        pages.extend(batch)
        if len(batch) < 100:
            break
        page_num += 1
    return pages


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

    pages = fetch_all_pages(auth)
    print(f"Fetched {len(pages)} total pages from the live site.\n")

    candidates = []
    skipped_has_content = []
    for p in pages:
        slug = p["slug"]
        if not DUPLICATE_RE.match(slug):
            continue
        content = (p.get("content") or {}).get("rendered", "")
        if content.strip() != "":
            skipped_has_content.append(p)
            continue
        if p["status"] != "publish":
            continue
        candidates.append(p)

    if skipped_has_content:
        print(f"SKIPPED (slug matches the duplicate pattern but has real content -- needs a human look, never auto-trashed):")
        for p in skipped_has_content:
            print(f"    id={p['id']:<5} slug={p['slug']:<30} status={p['status']}")
        print()

    by_base = {}
    for p in candidates:
        base = DUPLICATE_RE.match(p["slug"]).group(1)
        by_base.setdefault(base, []).append(p)

    print(f"Found {len(candidates)} empty duplicate pages matching <managed-slug>-N:\n")
    for base, items in sorted(by_base.items()):
        print(f"  {base}: {len(items)} duplicates")
        for p in sorted(items, key=lambda x: x["id"]):
            print(f"    id={p['id']:<5} slug={p['slug']:<30} date={p['date']}")

    home_stubs = [p for p in pages if HOME_STUB_RE.match(p["slug"])]
    if home_stubs:
        print(f"\n{len(home_stubs)} separate 'home-<language>' pages found -- NOT included in this cleanup (different naming pattern, likely hand-created in September, needs its own explicit decision):")
        for p in home_stubs:
            print(f"    id={p['id']:<5} slug={p['slug']:<45} status={p['status']}")

    print(f"\nTOTAL: {len(candidates)} pages {'would be' if args.mode == 'dry-run' else 'will be'} trashed.")

    if args.mode == "dry-run":
        print("\nDry run only -- nothing was changed. Re-run with mode=execute to actually trash these.")
        return

    print("\nExecuting...")
    trashed = 0
    failures = []
    for p in candidates:
        resp = requests.delete(
            f"{WP_BASE}/pages/{p['id']}",
            params={"force": "false"},
            auth=auth,
            timeout=30,
        )
        if resp.status_code not in (200, 410):
            failures.append((p["id"], p["slug"], resp.status_code, resp.text[:200]))
            print(f"  FAILED id={p['id']} slug={p['slug']}: HTTP {resp.status_code}")
        else:
            trashed += 1
            print(f"  trashed id={p['id']} slug={p['slug']}")

    print(f"\nDone. {trashed} trashed, {len(failures)} failed.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
