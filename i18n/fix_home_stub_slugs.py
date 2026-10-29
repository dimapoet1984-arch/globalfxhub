#!/usr/bin/env python3
"""
Renames the 7 per-language front-page objects from their current
odd, inconsistent slugs ("home-portugues", "home-italiano", etc. --
apparently typed by hand as a title like "Home - Portugues" and
auto-slugified, rather than created through Polylang's own "+ add
translation" flow) to the plain "home", matching how every other
auto-synced page (reviews, compare, best, ...) already behaves: same
slug in every language, differentiated only by the language URL
prefix.

These are NOT the duplicate-page bug's output (different naming
pattern, confirmed via the live site that each one correctly renders
the real homepage through front-page.php, meaning WordPress's
is_front_page() already recognizes it -- so it's genuinely each
language's front-page translation, just with a messy slug). Never
trashes anything; only ever PATCHes a page's slug, and only for a
slug matching ^home-. (excluding the literal "home" page itself).

Usage:
    python3 fix_home_stub_slugs.py --mode dry-run
    python3 fix_home_stub_slugs.py --mode execute
"""
import argparse
import os
import re
import sys
import time

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"
HOME_STUB_RE = re.compile(r"^home-.")

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


def fetch_all_pages(auth):
    pages = []
    page_num = 1
    while True:
        resp = request_with_retry(
            "GET",
            f"{WP_BASE}/pages",
            params={"per_page": 100, "page": page_num, "_fields": "id,slug,link"},
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

    candidates = [p for p in pages if HOME_STUB_RE.match(p["slug"])]
    print(f"Found {len(candidates)} home-<language> stub pages to rename to 'home':\n")
    for p in candidates:
        print(f"    id={p['id']:<5} slug={p['slug']:<45} link={p['link']}")

    if args.mode == "dry-run":
        print("\nDry run only -- nothing was changed. Re-run with mode=execute to actually rename these.")
        return

    print("\nExecuting...")
    renamed = 0
    failures = []
    for p in candidates:
        try:
            resp = request_with_retry(
                "POST",
                f"{WP_BASE}/pages/{p['id']}",
                json={"slug": "home"},
                auth=auth,
                timeout=30,
            )
        except requests.exceptions.RequestException as exc:
            failures.append((p["id"], p["slug"], str(exc)))
            print(f"  FAILED id={p['id']} slug={p['slug']}: {exc}")
            continue
        if resp.status_code != 200:
            failures.append((p["id"], p["slug"], f"HTTP {resp.status_code}: {resp.text[:200]}"))
            print(f"  FAILED id={p['id']} slug={p['slug']}: HTTP {resp.status_code}")
            continue
        new_slug = resp.json().get("slug")
        if new_slug != "home":
            # WordPress/Polylang silently resolved a slug collision by
            # appending a suffix -- report the ACTUAL result rather
            # than assuming success.
            print(f"  id={p['id']}: requested slug 'home', WordPress returned '{new_slug}' instead (likely a collision Polylang didn't allow) -- left as-is, needs a look")
            failures.append((p["id"], p["slug"], f"WP returned slug '{new_slug}' instead of 'home'"))
        else:
            renamed += 1
            print(f"  renamed id={p['id']}: {p['slug']} -> home")

    print(f"\nDone. {renamed} renamed, {len(failures)} failed/unexpected.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
