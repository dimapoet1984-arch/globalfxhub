#!/usr/bin/env python3
"""
Track A translation pipeline -- translates real WP posts/pages (the
11 container pages, 7 service pages, Learn/News/candlestick-patterns
posts) into every target language, per
docs/TRANSLATION_PIPELINE_ARCHITECTURE.md sections 1-2 and
docs/TRANSLATION_PLAYBOOK.md section 3.

One structured request per item per language (title, slug, excerpt,
SEO title/description, and every content piece together -- playbook
3.2), using the splitter (i18n/splitter.py) to isolate translatable
HTML from structure, the shared Claude layer (i18n/translate_common.py)
for the actual API calls, and this theme's new REST fields
(inc/i18n-rest-connector.php) to link the result into Polylang's
translation group under the existing process-wide lock.

Usage:
    python3 translate.py --mode dry-run  --languages es,de
    python3 translate.py --mode translate --languages es --items 281,282
    python3 translate.py --mode translate --languages all --reviewer sonnet

--mode dry-run makes ZERO Claude API calls (playbook's "translate-dry":
counts only) -- it still hits the WP REST API (read-only) to report
exactly how many items in each language actually need work.
"""
import argparse
import json
import os
import sys
import time

import requests

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt
from markup_check import check_strict, check_order_relaxed
from slugify import transliterate_slug
from splitter import split_html, pieces_for_translation, rebuild, verify_round_trip

WP_BASE = "https://globalfxhub.net/wp-json/wp/v2"

POST_TYPES = ["page", "post"]

RETRY_ATTEMPTS = 4
RETRY_BACKOFF_SECONDS = 3

TRACK_A_SCHEMA = {
    "type": "object",
    "properties": {
        "title": {"type": "string"},
        "slug_source": {
            "type": "string",
            "description": "A short, natural translated phrase to derive the URL slug from -- not the slug itself.",
        },
        "excerpt": {"type": "string"},
        "seo_title": {"type": "string"},
        "seo_description": {"type": "string"},
        "pieces": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "id": {"type": "integer"},
                    "html": {"type": "string"},
                },
                "required": ["id", "html"],
                "additionalProperties": False,
            },
        },
    },
    "required": ["title", "slug_source", "excerpt", "seo_title", "seo_description", "pieces"],
    "additionalProperties": False,
}

# Used for the retry-with-a-note and review passes, which only ever
# need to re-translate/correct specific CONTENT PIECES -- never the
# full TRACK_A_SCHEMA, which would force the model to also re-emit
# title/slug/excerpt/SEO fields it was given no context for on those
# calls (a real bug caught in testing: reusing TRACK_A_SCHEMA for a
# pieces-only review call has nothing for the model to base those
# other required fields on).
PIECES_ONLY_SCHEMA = {
    "type": "object",
    "properties": {"pieces": TRACK_A_SCHEMA["properties"]["pieces"]},
    "required": ["pieces"],
    "additionalProperties": False,
}


def request_with_retry(method, url, **kwargs):
    """Same retry shape as i18n/cleanup_duplicate_pages.py and
    i18n/fix_home_stub_slugs.py (4 attempts, linear backoff, 5xx/network
    errors only -- a live host under shared hosting has shown a
    transient 500 before, this session, on an otherwise-fine request)."""
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
            print(f"  (transient failure on attempt {attempt}/{RETRY_ATTEMPTS}, retrying in {wait}s: {last_exc})")
            time.sleep(wait)
    raise last_exc


def fetch_all_items(auth, post_type):
    """id-ordered pagination -- playbook 3.5: date order with equal
    dates can skip/duplicate items across pages; id order can't."""
    items = []
    page = 1
    while True:
        resp = request_with_retry(
            "GET",
            f"{WP_BASE}/{post_type}s",
            params={
                "per_page": 100,
                "page": page,
                "orderby": "id",
                "order": "asc",
                "_fields": "id,slug,status,parent,template,title,excerpt,content,gfh_lang,gfh_translations",
            },
            auth=auth,
            timeout=30,
        )
        resp.raise_for_status()
        batch = resp.json()
        if not batch:
            break
        for item in batch:
            item["_post_type"] = post_type
        items.extend(batch)
        if len(batch) < 100:
            break
        page += 1
    return items


def order_parents_first(items):
    """Pages only have a meaningful parent chain (posts' `parent` is
    always 0 here) -- sort by depth so a child's translated parent
    already exists when the child is processed (playbook 3.5:
    "Parents before children")."""
    by_id = {item["id"]: item for item in items}

    def depth(item, seen=None):
        seen = seen or set()
        parent_id = item.get("parent") or 0
        if not parent_id or parent_id in seen or parent_id not in by_id:
            return 0
        return 1 + depth(by_id[parent_id], seen | {parent_id})

    return sorted(items, key=depth)


def needs_translation(item, lang):
    translations = item.get("gfh_translations") or {}
    return lang not in translations


def item_description(item):
    title = (item.get("title") or {}).get("rendered", "") or item["slug"]
    return f"{item['_post_type']} \"{title}\" (slug: {item['slug']})"


def build_full_user_content(item, pieces, lang):
    """First-pass translation only -- title/excerpt/SEO fields and
    every content piece together in one request, matching
    TRACK_A_SCHEMA's required fields."""
    title = (item.get("title") or {}).get("rendered", "")
    excerpt = (item.get("excerpt") or {}).get("rendered", "")
    return "\n".join([
        f"Target language: {lang}", "", f"Item: {item_description(item)}", "",
        f"Title to translate: {title!r}",
        f"Excerpt to translate: {excerpt!r}", "",
        "Content pieces to translate (JSON array, translate each by id, preserve all HTML exactly):",
        json.dumps(pieces, ensure_ascii=False),
    ])


def build_pieces_user_content(item_desc, pieces, lang, note=None, reviewing=False):
    """Retry and review calls -- PIECES_ONLY_SCHEMA, no title/excerpt/SEO
    fields, since those calls have no original title/excerpt context
    to re-translate or review in the first place."""
    verb = "review and correct" if reviewing else "re-translate"
    lines = [f"Target language: {lang}", "", f"Item: {item_desc}", ""]
    if note:
        lines.append(f"NOTE from a previous attempt: {note}")
        lines.append("")
    lines.append(f"Pieces to {verb} (JSON array, by id, preserve all HTML exactly):")
    lines.append(json.dumps(pieces, ensure_ascii=False))
    return "\n".join(lines)


def translate_item_pieces(client, translate_system, review_system, item, segments, lang, reviewer_model):
    """Runs the full retry/review escalation from
    docs/TRANSLATION_PLAYBOOK.md section 3.3 (points 1, 3, 4) for one
    item: translate -> markup-check -> retry with a note -> review
    pass -> accept order-relaxed as a last resort -> any piece still
    failing keeps its English original, logged for a human.

    Returns (parsed, translated_by_id, warnings)."""
    pieces = pieces_for_translation(segments)
    pieces_by_id = {p["id"]: p["html"] for p in pieces}
    warnings = []

    parsed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, translate_system, build_full_user_content(item, pieces, lang),
        TRACK_A_SCHEMA, tc.TRANSLATE_EFFORT,
    )

    failing_ids = [
        p["id"] for p in parsed["pieces"]
        if p["id"] in pieces_by_id and not check_strict(pieces_by_id[p["id"]], p["html"])
    ]

    if failing_ids:
        retry_pieces = [p for p in pieces if p["id"] in failing_ids]
        note = f"Pieces {failing_ids} had a markup mismatch last attempt -- preserve every HTML tag and attribute exactly, only translate visible text."
        retry_parsed, _ = tc.structured_request(
            client, tc.HAIKU_MODEL, translate_system,
            build_pieces_user_content(item_description(item), retry_pieces, lang, note=note),
            PIECES_ONLY_SCHEMA, tc.TRANSLATE_EFFORT,
        )
        retry_by_id = {p["id"]: p["html"] for p in retry_parsed["pieces"]}
        still_failing = []
        for piece_id in failing_ids:
            if piece_id in retry_by_id and check_strict(pieces_by_id[piece_id], retry_by_id[piece_id]):
                for p in parsed["pieces"]:
                    if p["id"] == piece_id:
                        p["html"] = retry_by_id[piece_id]
            else:
                still_failing.append(piece_id)
        failing_ids = still_failing

    if failing_ids:
        review_pieces = [{"id": p["id"], "html": next(pp["html"] for pp in parsed["pieces"] if pp["id"] == p["id"])} for p in pieces if p["id"] in failing_ids]
        review_parsed, _ = tc.structured_request(
            client, reviewer_model, review_system,
            build_pieces_user_content(item_description(item), review_pieces, lang, reviewing=True),
            PIECES_ONLY_SCHEMA, tc.REVIEW_EFFORT,
        )
        review_by_id = {p["id"]: p["html"] for p in review_parsed.get("pieces", [])}
        still_failing = []
        for piece_id in failing_ids:
            candidate = review_by_id.get(piece_id)
            original_html = pieces_by_id[piece_id]
            if candidate and (check_strict(original_html, candidate) or check_order_relaxed(original_html, candidate)):
                for p in parsed["pieces"]:
                    if p["id"] == piece_id:
                        p["html"] = candidate
            else:
                still_failing.append(piece_id)
        failing_ids = still_failing

    for piece_id in failing_ids:
        warnings.append(
            f"piece {piece_id} failed markup check after translate+retry+review -- "
            f"kept in English. Last model attempt: {next((p['html'] for p in parsed['pieces'] if p['id'] == piece_id), '')!r}"
        )

    translated_by_id = {p["id"]: p["html"] for p in parsed["pieces"] if p["id"] not in failing_ids}
    return parsed, translated_by_id, warnings


def create_or_update_translation(auth, item, lang, parsed, translated_content, parent_translation_id):
    """Only ever called in --mode translate -- dry-run never reaches
    this far (process_item returns before any Claude call, let alone
    a write, when args.mode == "dry-run")."""
    slug = transliterate_slug(parsed["slug_source"] or parsed["title"], fallback_prefix=f"{item['slug']}-{lang}")
    payload = {
        "title": parsed["title"],
        "slug": slug,
        "content": translated_content,
        "excerpt": parsed["excerpt"],
        "status": item["status"],
        "meta": {
            "gfh_seo_title": parsed["seo_title"],
            "gfh_seo_desc": parsed["seo_description"],
        },
    }
    if item.get("template"):
        payload["template"] = item["template"]
    if item["_post_type"] == "page" and parent_translation_id:
        payload["parent"] = parent_translation_id

    resp = request_with_retry("POST", f"{WP_BASE}/{item['_post_type']}s", json=payload, auth=auth, timeout=60)
    resp.raise_for_status()
    new_item = resp.json()

    request_with_retry(
        "POST", f"{WP_BASE}/{item['_post_type']}s/{new_item['id']}",
        json={"gfh_lang": lang}, auth=auth, timeout=30,
    ).raise_for_status()

    request_with_retry(
        "POST", f"{WP_BASE}/{item['_post_type']}s/{item['id']}",
        json={"gfh_translations": {lang: new_item["id"]}}, auth=auth, timeout=30,
    ).raise_for_status()

    return new_item["id"]


def process_item(client, auth, translate_system, review_system, item, lang, reviewer_model, translated_parent_ids, mode):
    content_html = (item.get("content") or {}).get("rendered", "")
    try:
        ok, _ = verify_round_trip(content_html)
    except ValueError as exc:
        print(f"  SKIP {item_description(item)}: splitter refused this content ({exc})")
        return {"status": "skipped_splitter_error"}
    if not ok:
        print(f"  SKIP {item_description(item)}: content failed byte-exact round-trip check -- needs a human look before this can be translated.")
        return {"status": "skipped_round_trip"}

    segments = split_html(content_html)  # already proven safe by verify_round_trip() above

    if mode == "dry-run":
        return {"status": "would_translate", "piece_count": len(pieces_for_translation(segments))}

    parsed, translated_by_id, warnings = translate_item_pieces(client, translate_system, review_system, item, segments, lang, reviewer_model)

    for w in warnings:
        print(f"  WARNING [{item_description(item)} / {lang}]: {w}")

    translated_content = rebuild(segments, translated_by_id)
    parent_translation_id = translated_parent_ids.get(item.get("parent") or 0)

    new_id = create_or_update_translation(auth, item, lang, parsed, translated_content, parent_translation_id)
    print(f"  OK {item_description(item)} -> {lang} (new id {new_id}, {len(warnings)} piece(s) left in English)")
    return {"status": "translated", "new_id": new_id, "warnings": warnings}


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--mode", choices=["dry-run", "translate"], required=True)
    parser.add_argument("--languages", default="all", help="Comma-separated language codes, or 'all' for every language in i18n/glossary.py.")
    parser.add_argument("--items", default="all", help="Comma-separated post/page IDs, or 'all'.")
    parser.add_argument("--reviewer", choices=["haiku", "sonnet"], default="haiku")
    parser.add_argument("--max-minutes", type=float, default=None)
    args = parser.parse_args()

    languages = LANGUAGES if args.languages == "all" else args.languages.split(",")
    item_filter = None if args.items == "all" else {int(x) for x in args.items.split(",")}
    reviewer_model = tc.SONNET_MODEL if args.reviewer == "sonnet" else tc.HAIKU_MODEL

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    all_items = []
    for post_type in POST_TYPES:
        all_items.extend(fetch_all_items(auth, post_type))
    all_items = order_parents_first(all_items)
    # Deliberately NOT filtering all_items down to item_filter here --
    # a selected child page's real parent (e.g. "news" when --items
    # only names "news/markets") must still be visited so its EXISTING
    # translation gets backfilled into translated_parent_ids below.
    # Filtering here would silently drop that parent from the loop
    # entirely, leaving the child's translation created with no
    # parent even though the parent's translation already exists.
    # The filter is applied per-item instead, just before deciding
    # whether to actually process it.

    client = None if args.mode == "dry-run" else tc.get_client()
    start_time = time.monotonic()
    counts = {}

    for lang in languages:
        translate_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.TRANSLATE_ROLE_INSTRUCTIONS)
        review_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.REVIEW_ROLE_INSTRUCTIONS)
        translated_parent_ids = {}
        lang_counts = {"would_translate": 0, "translated": 0, "skipped_round_trip": 0, "skipped_splitter_error": 0, "already_done": 0}
        counts[lang] = lang_counts

        print(f"\n=== {lang} ===")
        for item in all_items:
            if args.max_minutes is not None and (time.monotonic() - start_time) / 60 > args.max_minutes:
                print(f"\n--max-minutes {args.max_minutes} reached -- stopping early. Re-run to pick up where this left off (already-translated items are skipped automatically).")
                break

            if not needs_translation(item, lang):
                if item["_post_type"] == "page":
                    translated_parent_ids[item["id"]] = (item.get("gfh_translations") or {})[lang]
                if item_filter is None or item["id"] in item_filter:
                    lang_counts["already_done"] += 1
                continue

            if item_filter is not None and item["id"] not in item_filter:
                # Not selected for this run, and not already translated
                # either -- nothing to backfill, nothing to count, just
                # move on (its own translation, if ever created, will
                # supply the parent link for its children some other run).
                continue

            result = process_item(client, auth, translate_system, review_system, item, lang, reviewer_model, translated_parent_ids, args.mode)
            lang_counts[result["status"]] = lang_counts.get(result["status"], 0) + 1
            if result.get("new_id") and item["_post_type"] == "page":
                translated_parent_ids[item["id"]] = result["new_id"]

    print("\n=== Summary ===")
    for lang, lang_counts in counts.items():
        print(f"  {lang}: {lang_counts}")


if __name__ == "__main__":
    main()
