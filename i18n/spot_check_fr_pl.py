#!/usr/bin/env python3
"""
Read-only spot-check before adding fr/pl to GLOBALFXHUB_LIVE_LANGUAGES
(inc/i18n-live-languages.php): pulls a handful of real fr and pl posts
and prints title + a content excerpt so the content can be eyeballed
for sanity (not literally still English, no obviously broken HTML
entities, no leftover glossary placeholders) before flipping the
SEO-indexing gate live. This is the human-judgment check
docs/TRANSLATION_PLAYBOOK.md section 5 calls for before launch.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/spot_check_fr_pl.py
"""
import os
import re
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"


def strip_html(html):
    text = re.sub(r"<[^>]+>", " ", html or "")
    return re.sub(r"\s+", " ", text).strip()


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    # This theme's custom REST fields (inc/i18n-rest-connector.php) only
    # expose gfh_lang per-item on GET -- they don't wire up `?lang=` query
    # filtering. Fetch recent batches with no language filter and filter
    # by gfh_lang client-side, paginating back until both languages have
    # samples (fr/pl each have hundreds of posts, so this usually resolves
    # within the first page, but paginate defensively anyway).
    for post_type in ("posts",):
        # fr/pl content predates the it/pt Track A run by a wide margin --
        # their post IDs sit much further back than the newest few hundred.
        # Paginate deep (up to 6000 items) until both languages have
        # samples, rather than assuming a shallow recent window covers them.
        batch = []
        found_counts = {}
        for page in range(1, 61):
            resp = request_with_retry(
                "GET", f"{WP_BASE}/{post_type}",
                params={
                    "per_page": 100, "page": page, "orderby": "id", "order": "desc",
                    "_fields": "id,slug,title,content,gfh_lang",
                },
                auth=auth, timeout=30,
            )
            if resp.status_code == 400:
                # Past the last valid page (WP REST errors on an out-of-range page).
                break
            resp.raise_for_status()
            page_items = resp.json()
            if not page_items:
                break
            batch.extend(page_items)
            found_counts = {lang: len([p for p in batch if p.get("gfh_lang") == lang]) for lang in ("fr", "pl")}
            if all(found_counts.get(lang, 0) >= 5 for lang in ("fr", "pl")):
                break
        print(f"--- scanned {len(batch)} {post_type}, found fr={found_counts.get('fr', 0)} pl={found_counts.get('pl', 0)} ---\n")
        for lang in ("fr", "pl"):
            print(f"=== {lang} ({post_type}) ===\n")
            items = [p for p in batch if p.get("gfh_lang") == lang][:5]
            for item in items:
                title = strip_html((item.get("title") or {}).get("rendered", ""))
                content = strip_html((item.get("content") or {}).get("rendered", ""))
                print(f"  [{post_type}] id={item['id']} slug={item['slug']}")
                print(f"    title: {title}")
                print(f"    content (first 300 chars): {content[:300]}")
                print()

    audit_pages(auth)


def audit_pages(auth):
    """
    Full census of fr/pl pages (not just a sample): how many have empty
    content and/or a title that looks untranslated, since the first
    spot-check run turned up auto-duplicated container-page shells
    (data-fr, regulators-fr, research-fr, ...) with English titles and
    no body -- the exact Sept 21 Polylang-rollout problem
    inc/i18n-live-languages.php's own docblock describes.
    """
    for lang in ("fr", "pl"):
        all_pages = []
        for page in range(1, 61):
            resp = request_with_retry(
                "GET", f"{WP_BASE}/pages",
                params={
                    "per_page": 100, "page": page, "orderby": "id", "order": "desc",
                    "_fields": "id,slug,title,content,gfh_lang",
                },
                auth=auth, timeout=30,
            )
            if resp.status_code == 400:
                break
            resp.raise_for_status()
            page_items = resp.json()
            if not page_items:
                break
            all_pages.extend(page_items)
        lang_pages = [p for p in all_pages if p.get("gfh_lang") == lang]
        empty = [p for p in lang_pages if not strip_html((p.get("content") or {}).get("rendered", ""))]
        print(f"=== {lang} pages census: {len(lang_pages)} total, {len(empty)} with empty content ===\n")
        for item in empty[:30]:
            title = strip_html((item.get("title") or {}).get("rendered", ""))
            print(f"  [empty] id={item['id']} slug={item['slug']} title={title}")
        print()


if __name__ == "__main__":
    main()
