#!/usr/bin/env python3
"""
Read-only diagnostic: given a comma-separated list of English Learn
(post-type) slugs, looks up each one's post ID and which languages
already have a translation linked via gfh_translations -- the exact
fact translate.py's --items flag needs (it only accepts numeric post
IDs, not slugs) before dispatching a real translation run for a
specific article rather than the whole site.

Makes no changes. Read-only GETs only.

Usage:
    python3 diagnose_learn_article_translations.py --slugs asic-forex-regulation-explained,fsca-forex-regulation-explained
"""
import argparse
import os
import sys

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--slugs", required=True, help="Comma-separated English post slugs to look up.")
    args = parser.parse_args()

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    for slug in args.slugs.split(","):
        slug = slug.strip()
        resp = requests.get(
            f"{WP_BASE}/posts",
            params={"slug": slug, "_fields": "id,slug,status,gfh_translations,modified_gmt", "status": "publish,draft,pending,private,trash"},
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        matches = resp.json()
        if not matches:
            print(f"{slug}: NO ENGLISH POST FOUND AT ALL (unexpected)")
            continue
        post = matches[0]
        translations = post.get("gfh_translations") or {}
        print(f"\n=== {slug} (en id={post['id']}, status={post['status']}, modified_gmt={post.get('modified_gmt')}) ===")
        if not translations or (len(translations) == 1 and "en" in translations):
            print("  No non-English translations exist yet.")
            continue
        for lang, translated_id in translations.items():
            if lang == "en":
                continue
            r = requests.get(
                f"{WP_BASE}/posts/{translated_id}",
                params={"_fields": "id,slug,status,modified_gmt", "context": "edit"},
                auth=auth, timeout=30,
            )
            if r.status_code == 200:
                t = r.json()
                print(f"  {lang}: id={translated_id} status={t.get('status')} slug={t.get('slug')} modified_gmt={t.get('modified_gmt')}")
            else:
                print(f"  {lang}: id={translated_id} -- lookup failed ({r.status_code})")


if __name__ == "__main__":
    main()
