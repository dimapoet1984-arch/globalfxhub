#!/usr/bin/env python3
"""
Restores the 7 per-language front-page objects to clear, self-
documenting slugs. A first version of this script tried renaming all
7 to the plain "home", on the assumption (wrong, confirmed live) that
Polylang would allow the same slug across languages the way it does
for every other auto-synced page (reviews, compare, ...). It doesn't,
here: this site's Polylang "front page URL contains the language
code" setting is off (see docs/TRANSLATION_PLAYBOOK.md section 1),
so these front-page objects fall back to plain WordPress's GLOBAL
slug-uniqueness rule instead of Polylang's per-language one -- the
rename attempt actually went through, but WordPress silently
auto-suffixed every one of them to home-2 through home-8 instead of
rejecting the request, which is worse (less self-documenting) than
where they started.

This restores each page's explicit, known original slug -- strictly
better than "home-2"..."home-8" for anyone (human or script) trying
to tell them apart -- while the real fix (checking the Polylang
setting, or revisiting this once the real translation pipeline
exists) stays a separate, owner-driven decision.

Usage:
    python3 fix_home_stub_slugs.py --mode dry-run
    python3 fix_home_stub_slugs.py --mode execute
"""
import argparse
import os
import sys
import time

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# Known from the live site before the failed rename attempt.
RESTORE_SLUGS = {
    583: "home-portugues",
    582: "home-italiano",
    581: "home-polski",
    580: "home-francais",
    579: "home-العربية",  # Arabic for "Arabic"
    578: "home-deutsch",
    577: "home-espanol",
}

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

    print(f"Restoring {len(RESTORE_SLUGS)} pages to their original slugs:\n")
    for page_id, slug in RESTORE_SLUGS.items():
        resp = request_with_retry("GET", f"{WP_BASE}/pages/{page_id}", params={"_fields": "id,slug"}, auth=auth, timeout=30)
        resp.raise_for_status()
        current = resp.json()["slug"]
        print(f"    id={page_id:<5} current slug={current:<15} -> target={slug}")

    if args.mode == "dry-run":
        print("\nDry run only -- nothing was changed. Re-run with mode=execute to actually restore these.")
        return

    print("\nExecuting...")
    restored = 0
    failures = []
    for page_id, slug in RESTORE_SLUGS.items():
        try:
            resp = request_with_retry(
                "POST",
                f"{WP_BASE}/pages/{page_id}",
                json={"slug": slug},
                auth=auth,
                timeout=30,
            )
        except requests.exceptions.RequestException as exc:
            failures.append((page_id, slug, str(exc)))
            print(f"  FAILED id={page_id}: {exc}")
            continue
        if resp.status_code != 200:
            failures.append((page_id, slug, f"HTTP {resp.status_code}: {resp.text[:200]}"))
            print(f"  FAILED id={page_id}: HTTP {resp.status_code}")
            continue
        new_slug = resp.json().get("slug")
        if new_slug != slug:
            print(f"  id={page_id}: requested '{slug}', WordPress returned '{new_slug}' instead -- unexpected, needs a look")
            failures.append((page_id, slug, f"WP returned '{new_slug}'"))
        else:
            restored += 1
            print(f"  restored id={page_id}: -> {slug}")

    print(f"\nDone. {restored} restored, {len(failures)} failed/unexpected.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
