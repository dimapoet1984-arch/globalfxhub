#!/usr/bin/env python3
"""
Creates/updates 'gfh_comparison' WP posts (inc/broker-comparison-cpt.php)
via the REST API -- the only way to get real content into this post
type from outside wp-admin, since there's no WP-CLI or direct database
access available to this pipeline (same constraint as every other
Track A/B script in this directory).

Input is a single JSON file: {slug: {title, status, date?, data}}.
  - slug: the post's post_name (and the key
    globalfxhub_editorial_comparison_translations() uses for
    translations -- must match exactly).
  - title: the post_title shown in wp-admin (e.g. "AvaTrade vs
    Pepperstone").
  - status: "publish" or "draft" (future/scheduled posts aren't used
    here deliberately -- "schedule" for this content means a planned
    TARGET date while a human still reviews and publishes manually,
    not WordPress auto-publishing it unattended).
  - date (optional): ISO 8601 (e.g. "2026-12-08T09:00:00"), the
    post's target/intended date -- stored as post_date even for a
    draft, purely so wp-admin's post list sorts/filters by the
    calendar week this was drafted for.
  - data: the full structured comparison content (a_slug, b_slug,
    meta_title, meta_description, intro, verdict, choose_a_heading,
    choose_a_points[], choose_b_heading, choose_b_points[],
    worth_knowing_heading, worth_knowing, faqs[{q,a}]) -- JSON-encoded
    verbatim into the gfh_cmp_data post meta field.

Idempotent: looks up each slug first (GET .../gfh-comparisons?slug=X);
updates the existing post if found (preserving its ID), creates a new
one otherwise. Safe to re-run.

Usage:
    python3 i18n/create_comparison_posts.py tests/fixtures/gfh-comparison-posts-import.json
"""
import json
import os
import sys
import time

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"
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
            last_exc = requests.exceptions.HTTPError(f"{resp.status_code} Server Error for url: {url}", response=resp)
        if attempt < RETRY_ATTEMPTS:
            wait = RETRY_BACKOFF_SECONDS * attempt
            print(f"  (transient failure on attempt {attempt}/{RETRY_ATTEMPTS}, retrying in {wait}s: {last_exc})", file=sys.stderr)
            time.sleep(wait)
    raise last_exc


def find_existing_post_id(auth, slug):
    resp = request_with_retry(
        "GET", f"{WP_BASE}/gfh-comparisons",
        params={"slug": slug, "status": "publish,draft,future,pending", "_fields": "id,slug"},
        auth=auth, timeout=30,
    )
    resp.raise_for_status()
    matches = resp.json()
    return matches[0]["id"] if matches else None


def upsert_comparison(auth, slug, entry):
    payload = {
        "title": entry["title"],
        "slug": slug,
        "status": entry["status"],
        "meta": {"gfh_cmp_data": json.dumps(entry["data"], ensure_ascii=False)},
    }
    if entry.get("date"):
        payload["date"] = entry["date"]

    existing_id = find_existing_post_id(auth, slug)
    if existing_id:
        resp = request_with_retry("POST", f"{WP_BASE}/gfh-comparisons/{existing_id}", auth=auth, json=payload, timeout=30)
        action = "updated"
    else:
        resp = request_with_retry("POST", f"{WP_BASE}/gfh-comparisons", auth=auth, json=payload, timeout=30)
        action = "created"

    if resp.status_code >= 300:
        print(f"  FAILED ({resp.status_code}): {resp.text[:500]}", file=sys.stderr)
        resp.raise_for_status()

    result = resp.json()
    print(f"  {action} '{slug}' -> id={result['id']} status={result['status']} date={result.get('date')}")
    return result


def main():
    if len(sys.argv) != 2:
        print("Usage: python3 create_comparison_posts.py <input.json>", file=sys.stderr)
        sys.exit(1)

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    with open(sys.argv[1], encoding="utf-8") as f:
        entries = json.load(f)

    results = {}
    for slug, entry in entries.items():
        print(f"=== {slug} ===")
        results[slug] = upsert_comparison(auth, slug, entry)

    print(json.dumps(results, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
