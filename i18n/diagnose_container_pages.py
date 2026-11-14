#!/usr/bin/env python3
"""
Read-only diagnostic for the 11 container pages' per-language
translations (reviews, learn, compare, news, countries, best,
regulation, broker-finder, cost-calculator, regulation-checker,
broker-changelog). Confirmed live 2026-10-29: a direct lookup on one
translation ID (433, the Spanish "reviews" page) returns 401
rest_forbidden -- WordPress's standard response for content in Trash,
not published content -- and the ID is absent from the normal public
pages listing. This script checks ALL 77 (11 x 7) translation IDs the
same way, with authentication (which CAN see trashed/private content),
to confirm the actual scope before deciding on a fix.

Makes no changes. Read-only GETs only.

Usage:
    python3 diagnose_container_pages.py
"""
import os
import sys

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

CONTAINER_SLUGS = [
    "reviews", "learn", "compare", "news", "countries", "best",
    "regulation", "broker-finder", "cost-calculator",
    "regulation-checker", "broker-changelog",
]


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    by_status = {}

    for slug in CONTAINER_SLUGS:
        resp = requests.get(
            f"{WP_BASE}/pages",
            params={"slug": slug, "_fields": "id,slug,gfh_translations", "status": "publish,draft,pending,private,trash"},
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        matches = resp.json()
        if not matches:
            print(f"{slug}: NO ENGLISH PAGE FOUND AT ALL (unexpected)")
            continue
        page = matches[0]
        translations = page.get("gfh_translations") or {}
        print(f"\n=== {slug} (en id={page['id']}) ===")
        for lang, translated_id in translations.items():
            if lang == "en":
                continue
            r = requests.get(
                f"{WP_BASE}/pages/{translated_id}",
                params={"_fields": "id,slug,status,content,template", "context": "edit"},
                auth=auth, timeout=30,
            )
            if r.status_code == 404:
                status = "DOES NOT EXIST (fully deleted, not even in trash)"
            elif r.status_code != 200:
                status = f"HTTP {r.status_code}: {r.text[:150]}"
            else:
                data = r.json()
                content_len = len((data.get("content") or {}).get("raw") or (data.get("content") or {}).get("rendered") or "")
                status = f"status={data.get('status')} slug={data.get('slug')} template={data.get('template')!r} content_len={content_len}"
            by_status[status.split(":")[0].split()[0]] = by_status.get(status.split(":")[0].split()[0], 0) + 1
            print(f"  {lang}: id={translated_id} -> {status}")

    print("\n=== Summary (by first word of status) ===")
    for k, v in sorted(by_status.items()):
        print(f"  {k}: {v}")


if __name__ == "__main__":
    main()
