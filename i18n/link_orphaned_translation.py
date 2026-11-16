#!/usr/bin/env python3
"""
One-off repair for a translation that was created correctly but never
got linked into Polylang's translation group, because the gfh_translations
REST write hit the lock-timeout bug fixed in inc/i18n-rest-connector.php
(see that commit) before this script existed. Confirmed live 2026-10-29:
page 66 (en, "about") -> page 746 (es, "acerca-de-globalfxhub") -- the
translation itself is correct and already published; only the link
never completed.

Deliberately NOT a general-purpose tool: takes explicit
(post_type, original_id, lang, translation_id) rows, so it can only
ever link exactly what's named on the command line, never discover
and "fix" something unrelated.

Usage:
    python3 link_orphaned_translation.py --mode dry-run
    python3 link_orphaned_translation.py --mode execute
"""
import argparse
import os
import sys
import time

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# (post_type, original_id, lang, translation_id)
ORPHANS = [
    ("page", 66, "es", 746),
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
            if resp.status_code < 500 and resp.status_code != 409:
                return resp
            last_exc = requests.exceptions.HTTPError(f"{resp.status_code} Server Error for url: {url}", response=resp)
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

    print(f"Repairing {len(ORPHANS)} orphaned translation link(s):\n")
    for post_type, original_id, lang, translation_id in ORPHANS:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/{post_type}s/{original_id}",
            params={"_fields": "id,slug,gfh_translations"}, auth=auth, timeout=30,
        )
        resp.raise_for_status()
        current = resp.json()
        already_linked = current.get("gfh_translations", {}).get(lang) == translation_id
        print(
            f"    {post_type} {original_id} ({current.get('slug')}) -> "
            f"{lang}:{translation_id} -- "
            f"{'already linked' if already_linked else 'NOT yet linked, would link' if args.mode == 'dry-run' else 'linking now'}"
        )

    if args.mode == "dry-run":
        print("\nDry run only -- nothing was changed. Re-run with mode=execute to actually link these.")
        return

    print("\nExecuting...")
    failures = []
    for post_type, original_id, lang, translation_id in ORPHANS:
        try:
            resp = request_with_retry(
                "POST", f"{WP_BASE}/{post_type}s/{original_id}",
                json={"gfh_translations": {lang: translation_id}}, auth=auth, timeout=90,
            )
            resp.raise_for_status()
        except requests.exceptions.RequestException as exc:
            failures.append((original_id, lang, str(exc)))
            print(f"  FAILED {post_type} {original_id} -> {lang}: {exc}")
            continue

        verify = request_with_retry(
            "GET", f"{WP_BASE}/{post_type}s/{original_id}",
            params={"_fields": "gfh_translations"}, auth=auth, timeout=30,
        )
        verify.raise_for_status()
        linked = verify.json().get("gfh_translations", {}).get(lang) == translation_id
        if linked:
            print(f"  linked {post_type} {original_id} -> {lang}:{translation_id}")
        else:
            failures.append((original_id, lang, "verification read-back did not show the link"))
            print(f"  FAILED {post_type} {original_id} -> {lang}: verification read-back did not show the link")

    print(f"\nDone. {len(ORPHANS) - len(failures)} linked, {len(failures)} failed.")
    if failures:
        sys.exit(1)


if __name__ == "__main__":
    main()
