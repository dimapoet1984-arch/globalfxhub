#!/usr/bin/env python3
"""
Read-only audit: every published post/page on the live site whose
gfh_lang (Polylang's canonical per-post language field, exposed by
inc/i18n-rest-connector.php) is missing, empty, or not one of the
site's registered/live Polylang languages.

A post with no language assignment at all is exactly the kind of
content that the pre-fix implicit-filtering bug (fixed in
inc/i18n-content-language.php) could have let leak into any locale's
listing -- Polylang's own language filter typically excludes
language-less content from every language-filtered query, but under
the old unfiltered behavior nothing stopped it from showing up
anywhere.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/audit_missing_post_language.py
"""
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# inc/i18n-live-languages.php's GLOBALFXHUB_LIVE_LANGUAGES as of this
# audit. Any gfh_lang value outside this set (and non-empty) is also
# flagged as "unexpected" even though it's not technically "missing".
LIVE_LANGUAGES = {"en", "es", "de", "ar", "it", "pt", "fr", "pl"}


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    for post_type in ("posts", "pages"):
        missing = []
        unexpected = []
        total = 0
        page_num = 1
        while True:
            resp = request_with_retry(
                "GET", f"{WP_BASE}/{post_type}",
                params={
                    "per_page": 100, "page": page_num, "orderby": "id", "order": "asc",
                    "status": "publish",
                    "_fields": "id,slug,link,title,gfh_lang",
                },
                auth=auth, timeout=30,
            )
            if resp.status_code == 400:
                break
            resp.raise_for_status()
            batch = resp.json()
            if not batch:
                break
            total += len(batch)
            for item in batch:
                lang = item.get("gfh_lang")
                if not lang:
                    missing.append(item)
                elif lang not in LIVE_LANGUAGES:
                    unexpected.append(item)
            page_num += 1

        print(f"=== {post_type}: {total} published, {len(missing)} missing gfh_lang, {len(unexpected)} with an unrecognized gfh_lang ===\n")
        for item in missing:
            title = (item.get("title") or {}).get("rendered", "")
            print(f"  [missing lang] id={item['id']} slug={item['slug']} title={title!r} link={item.get('link')}")
        for item in unexpected:
            title = (item.get("title") or {}).get("rendered", "")
            print(f"  [unexpected lang={item.get('gfh_lang')!r}] id={item['id']} slug={item['slug']} title={title!r} link={item.get('link')}")
        print()


if __name__ == "__main__":
    main()
