#!/usr/bin/env python3
"""
Read-only: of the 393 Arabic "orphan" posts found by
i18n/compare_lang_post_counts.py (live posts tagged gfh_lang=ar that
aren't the genuine, linked translation of any English source),
classifies each as a likely DUPLICATE (the same content published
more than once -- the i18n/translate.py bug fixed 2026-11-15, same
pattern confirmed for Polish via i18n/classify_pl_orphans.py) vs
likely STANDALONE NEWS (unique content that simply never had an
English source, e.g. direct-language market bulletins).

Heuristic, since there's no single authoritative signal:
  - Groups orphans by their EXACT title text (duplicates from the
    failed-link-back bug keep the same title and get a disambiguated
    slug suffix, e.g. "-5"/"-6", from slugify() colliding on an
    already-used title).
  - Within each same-title cluster (size > 1), looks at how far apart
    the posts were published. A tight cluster (same day or two) is a
    likely genuine duplicate. A cluster spread across many days/weeks
    is a recurring EDITORIAL TEMPLATE (e.g. a daily "today's key
    events" headline reused on purpose for different days' content),
    not a duplicate -- each one is standalone, dated news with the
    same generic headline.
  - A title that appears only once among the orphans is standalone by
    definition.

Reports the breakdown and lists the tight-cluster (likely duplicate)
groups by name so they can be spot-checked before any cleanup action.

Makes no changes. Read-only GETs only.

Usage:
    python3 i18n/classify_ar_orphans.py
"""
import os
import re
import sys
from datetime import datetime

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from translate import request_with_retry

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

# A same-title cluster published within this many hours of each other
# is treated as a likely genuine duplicate rather than a recurring
# editorial template.
TIGHT_CLUSTER_HOURS = 48


def strip_html(html):
    text = re.sub(r"<[^>]+>", " ", html or "")
    return re.sub(r"\s+", " ", text).strip()


def fetch_all_posts(auth):
    posts = []
    page = 1
    while True:
        resp = request_with_retry(
            "GET", f"{WP_BASE}/posts",
            params={
                "per_page": 100, "page": page, "orderby": "id", "order": "asc",
                "status": "publish,draft,pending,private,future",
                "_fields": "id,slug,status,date,categories,gfh_lang,gfh_translations,title",
            },
            auth=auth, timeout=30,
        )
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        posts.extend(batch)
        if len(batch) < 100:
            break
        page += 1
    return posts


def main():
    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    all_posts = fetch_all_posts(auth)
    print(f"Fetched {len(all_posts)} live posts total\n")

    english_sources = [p for p in all_posts if (p.get("gfh_lang") or "en") == "en"]
    linked_ar_ids = set()
    for p in english_sources:
        tid = (p.get("gfh_translations") or {}).get("ar")
        if tid is not None:
            linked_ar_ids.add(tid)

    ar_posts = [p for p in all_posts if p.get("gfh_lang") == "ar"]
    orphans = [p for p in ar_posts if p["id"] not in linked_ar_ids]
    print(f"Arabic orphans (not a genuine linked translation): {len(orphans)}\n")

    clusters = {}
    for p in orphans:
        title = strip_html((p.get("title") or {}).get("rendered", ""))
        clusters.setdefault(title, []).append(p)

    singles = [members[0] for members in clusters.values() if len(members) == 1]
    multi = {title: members for title, members in clusters.items() if len(members) > 1}

    tight_dupe_count = 0
    spread_template_count = 0
    tight_groups = []
    spread_groups = []

    for title, members in multi.items():
        dates = sorted(datetime.fromisoformat(m["date"]) for m in members)
        spread_hours = (dates[-1] - dates[0]).total_seconds() / 3600
        if spread_hours <= TIGHT_CLUSTER_HOURS:
            tight_dupe_count += len(members)
            tight_groups.append((title, members, spread_hours))
        else:
            spread_template_count += len(members)
            spread_groups.append((title, members, spread_hours))

    print("=== Classification of the Arabic orphans ===")
    print(f"Standalone (unique title, appears once): {len(singles)}")
    print(f"Likely genuine duplicates (same title, published within {TIGHT_CLUSTER_HOURS}h of each other): {tight_dupe_count}")
    print(f"Recurring editorial template (same title, spread across many days -- standalone dated news, not duplicates): {spread_template_count}")
    print(f"  (sanity check: {len(singles)} + {tight_dupe_count} + {spread_template_count} = {len(singles) + tight_dupe_count + spread_template_count}, orphans = {len(orphans)})\n")

    print(f"=== Likely genuine duplicate groups ({len(tight_groups)} group(s)) ===")
    for title, members, spread_hours in sorted(tight_groups, key=lambda g: -len(g[1])):
        ids = [m["id"] for m in members]
        print(f"  {title!r} -- {len(members)} posts, spread {spread_hours:.1f}h, ids={ids}")

    print(f"\n=== Recurring editorial templates ({len(spread_groups)} group(s), NOT duplicates) ===")
    for title, members, spread_hours in sorted(spread_groups, key=lambda g: -len(g[1]))[:20]:
        print(f"  {title!r} -- {len(members)} posts, spread {spread_hours/24:.0f} days")
    if len(spread_groups) > 20:
        print(f"  ... and {len(spread_groups) - 20} more template group(s)")


if __name__ == "__main__":
    main()
