#!/usr/bin/env python3
"""
Read-only census of the duplicate "Blog" pages created by
globalfxhub_ensure_blog_page() in functions.php (hooked on
after_setup_theme, running on every request): it looks up a page at
path "blog", and if none exists yet in the current Polylang language
context, creates a new one -- but never calls pll_set_post_language(),
and the slug-uniqueness race is the same kind already fixed once this
session for the container pages, so WordPress auto-suffixes every
collision (blog-2, blog-3, ...) instead of reusing the first page.

Prints every page whose slug matches ^blog(-\\d+)?$, plus whatever the
live site's page_for_posts setting currently points at (the one page
actually serving /blog/), so the real page can be told apart from the
accumulated junk before anything gets trashed.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/diagnose_blog_duplicate_pages.py
"""
import os
import re
import sys
import time

import requests

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"
BLOG_RE = re.compile(r"^blog(-\d+)?$")

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
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    resp = request_with_retry("GET", f"{WP_BASE.rsplit('/wp/v2', 1)[0]}/wp/v2/settings", auth=auth, timeout=30)
    if resp.status_code == 200:
        settings = resp.json()
        page_for_posts = settings.get("page_for_posts")
        print(f"Live site setting page_for_posts = {page_for_posts}\n")
    else:
        page_for_posts = None
        print(f"Could not read /settings (HTTP {resp.status_code}) -- falling back to lowest-ID 'blog' as the presumed canonical page.\n")

    pages = []
    page_num = 1
    while True:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/pages",
            params={
                "per_page": 100, "page": page_num, "orderby": "id", "order": "asc",
                "_fields": "id,slug,title,status,date,content,gfh_lang",
            },
            auth=auth, timeout=30,
        )
        if resp.status_code == 400:
            break
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        pages.extend(batch)
        if len(batch) < 100:
            break
        page_num += 1

    print(f"Fetched {len(pages)} total pages from the live site.\n")

    blog_pages = [p for p in pages if BLOG_RE.match(p["slug"])]
    print(f"Found {len(blog_pages)} pages matching ^blog(-\\d+)?$:\n")
    for p in sorted(blog_pages, key=lambda x: x["id"]):
        title = (p.get("title") or {}).get("rendered", "")
        content = (p.get("content") or {}).get("rendered", "")
        marker = " <-- page_for_posts" if page_for_posts and p["id"] == page_for_posts else ""
        print(f"  id={p['id']:<5} slug={p['slug']:<10} lang={p.get('gfh_lang') or '-':<4} status={p['status']:<8} date={p['date']} title={title!r} content_len={len(content)}{marker}")


if __name__ == "__main__":
    main()
