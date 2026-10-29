#!/usr/bin/env python3
"""
Track B translation pipeline -- translates the ~900 virtual,
PHP-array-driven items (broker reviews, countries, best-lists,
regulation pages, broker-news) that have no WP post behind them, per
docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 3. Reads English
source values from inc/i18n-track-b-translations.php's read-only REST
endpoint (there's no WP post to read title/content from the way
Track A does), and writes results to the wp_globalfxhub_translations
table via its write endpoint.

Two translation paths per item, per the field inventory in
i18n/track_b_fields.py:

  - Plain fields (the overwhelming majority -- every broker field, all
    of country/best_list/regulation's structural fields): batched into
    ONE structured request per item per language covering every
    missing plain field at once. No markup check, no retry/review
    escalation -- there's no HTML to corrupt, and this first build
    deliberately skips an unconditional review pass here (the Track A
    sample run this session showed strong first-pass Haiku quality
    with zero retries needed; reconsider if real Track B output shows
    otherwise).
  - HTML fields (only best_list.intro and regulation.content today --
    confirmed against the real data in i18n/track_b_fields.py's own
    comments): routed through the exact same splitter/markup-check/
    retry/review pipeline Track A uses (i18n/splitter.py,
    i18n/markup_check.py), since a broken <a href> here is exactly as
    real a markup bug as one in a blog post.

Usage:
    python3 translate_track_b.py --mode dry-run --content-types broker
    python3 translate_track_b.py --mode translate --content-types broker --languages es --items ig,xtb
    python3 translate_track_b.py --mode translate --content-types all --languages all
"""
import argparse
import json
import os
import sys
import time

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt
from markup_check import check_strict, check_order_relaxed
from splitter import split_html, pieces_for_translation, rebuild, verify_round_trip
from track_b_fields import FIELDS, iter_field_paths, resolve_field_values
from translate import request_with_retry  # same retry shape (5xx/network/409), reused rather than duplicated

API_BASE = "https://globalfxhub.net/wp-json/globalfxhub/v1"

PLAIN_FIELDS_SCHEMA = {
    "type": "object",
    "properties": {
        "fields": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {"id": {"type": "string"}, "text": {"type": "string"}},
                "required": ["id", "text"],
                "additionalProperties": False,
            },
        },
    },
    "required": ["fields"],
    "additionalProperties": False,
}

PIECES_ONLY_SCHEMA = {
    "type": "object",
    "properties": {
        "pieces": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {"id": {"type": "integer"}, "html": {"type": "string"}},
                "required": ["id", "html"],
                "additionalProperties": False,
            },
        },
    },
    "required": ["pieces"],
    "additionalProperties": False,
}


def fetch_source_items(auth, content_type):
    resp = request_with_retry("GET", f"{API_BASE}/track-b-source/{content_type}", auth=auth, timeout=60)
    resp.raise_for_status()
    return resp.json()["items"]


def fetch_existing_translations(auth, content_type, lang):
    resp = request_with_retry("GET", f"{API_BASE}/track-b-translations/{content_type}/{lang}", auth=auth, timeout=30)
    resp.raise_for_status()
    return resp.json()


def write_translation(auth, content_type, content_id, field, lang, value):
    resp = request_with_retry(
        "POST", f"{API_BASE}/track-b-translation",
        json={"content_type": content_type, "content_id": content_id, "field": field, "lang": lang, "value": value},
        auth=auth, timeout=30,
    )
    resp.raise_for_status()


def resolve_item_fields(content_type, item_fields):
    """Every (concrete_field_name, english_value, is_html) triple for
    one item, flattening array-expanded fields (paragraphs[], faqs[].q)
    via resolve_field_values()."""
    resolved = []
    for path, is_html in iter_field_paths(content_type):
        for name, value in resolve_field_values(item_fields, path):
            resolved.append((name, value, is_html))
    return resolved


def translate_plain_fields(client, system, item_description, fields, lang):
    """fields: [(name, english_value), ...]. One structured request,
    returns {name: translated_text}."""
    user_content = (
        f"Target language: {lang}\n\nItem: {item_description}\n\n"
        f"Fields to translate (JSON array, translate each by id, plain text -- no HTML):\n"
        + json.dumps([{"id": name, "text": value} for name, value in fields], ensure_ascii=False)
    )
    parsed, _ = tc.structured_request(client, tc.HAIKU_MODEL, system, user_content, PLAIN_FIELDS_SCHEMA, tc.TRANSLATE_EFFORT)
    return {f["id"]: f["text"] for f in parsed["fields"]}


def translate_html_field(client, translate_system, review_system, item_description, field_name, html, lang, reviewer_model):
    """Same translate -> markup-check -> retry-with-note -> review ->
    order-relaxed-last-resort -> stays-English-and-logged escalation
    as i18n/translate.py's translate_item_pieces(), for one single
    HTML field. Returns (translated_html, warnings)."""
    try:
        ok, _ = verify_round_trip(html)
    except ValueError as exc:
        return html, [f"{field_name}: splitter refused this content ({exc}) -- kept in English."]
    if not ok:
        return html, [f"{field_name}: content failed byte-exact round-trip check -- kept in English."]

    segments = split_html(html)
    pieces = pieces_for_translation(segments)
    pieces_by_id = {p["id"]: p["html"] for p in pieces}
    warnings = []

    user_content = (
        f"Target language: {lang}\n\nItem: {item_description} (field: {field_name})\n\n"
        f"Pieces to translate (JSON array, by id, preserve all HTML exactly):\n"
        + json.dumps(pieces, ensure_ascii=False)
    )
    parsed, _ = tc.structured_request(client, tc.HAIKU_MODEL, translate_system, user_content, PIECES_ONLY_SCHEMA, tc.TRANSLATE_EFFORT)

    failing_ids = [p["id"] for p in parsed["pieces"] if p["id"] in pieces_by_id and not check_strict(pieces_by_id[p["id"]], p["html"])]

    if failing_ids:
        retry_pieces = [p for p in pieces if p["id"] in failing_ids]
        note = f"Pieces {failing_ids} had a markup mismatch last attempt -- preserve every HTML tag and attribute exactly."
        retry_content = (
            f"Target language: {lang}\n\nItem: {item_description} (field: {field_name})\n\n"
            f"NOTE from a previous attempt: {note}\n\n"
            f"Pieces to re-translate (JSON array, by id):\n" + json.dumps(retry_pieces, ensure_ascii=False)
        )
        retry_parsed, _ = tc.structured_request(client, tc.HAIKU_MODEL, translate_system, retry_content, PIECES_ONLY_SCHEMA, tc.TRANSLATE_EFFORT)
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
        review_content = (
            f"Target language: {lang}\n\nItem: {item_description} (field: {field_name})\n\n"
            f"Pieces to review and correct (JSON array, by id):\n" + json.dumps(review_pieces, ensure_ascii=False)
        )
        review_parsed, _ = tc.structured_request(client, reviewer_model, review_system, review_content, PIECES_ONLY_SCHEMA, tc.REVIEW_EFFORT)
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
        warnings.append(f"{field_name} piece {piece_id}: failed markup check after translate+retry+review -- kept in English.")

    translated_by_id = {p["id"]: p["html"] for p in parsed["pieces"] if p["id"] not in failing_ids}
    return rebuild(segments, translated_by_id), warnings


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--mode", choices=["dry-run", "translate"], required=True)
    parser.add_argument("--content-types", default="all", help="Comma-separated (broker,country,best_list,regulation,broker_news), or 'all'.")
    parser.add_argument("--languages", default="all")
    parser.add_argument("--items", default="all", help="Comma-separated content_ids (within each selected content_type), or 'all'.")
    parser.add_argument("--reviewer", choices=["haiku", "sonnet"], default="haiku")
    args = parser.parse_args()

    content_types = list(FIELDS.keys()) if args.content_types == "all" else args.content_types.split(",")
    languages = LANGUAGES if args.languages == "all" else args.languages.split(",")
    item_filter = None if args.items == "all" else set(args.items.split(","))
    reviewer_model = tc.SONNET_MODEL if args.reviewer == "sonnet" else tc.HAIKU_MODEL

    username = os.environ.get("WP_USERNAME")
    app_password = os.environ.get("WP_APP_PASSWORD")
    if not username or not app_password:
        print("WP_USERNAME and WP_APP_PASSWORD must both be set", file=sys.stderr)
        sys.exit(1)
    auth = (username, app_password)

    client = None if args.mode == "dry-run" else tc.get_client()
    counts = {}

    for content_type in content_types:
        print(f"\n=== {content_type} ===")
        source_items = fetch_source_items(auth, content_type)
        if item_filter is not None:
            source_items = [item for item in source_items if item["id"] in item_filter]

        for lang in languages:
            translate_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.TRANSLATE_ROLE_INSTRUCTIONS)
            review_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.REVIEW_ROLE_INSTRUCTIONS)
            existing = fetch_existing_translations(auth, content_type, lang)
            key = f"{content_type}/{lang}"
            lang_counts = {"would_translate": 0, "translated": 0, "already_done": 0}
            counts[key] = lang_counts

            for item in source_items:
                resolved = resolve_item_fields(content_type, item["fields"])
                existing_for_item = existing.get(item["id"], {})
                missing_plain = [(name, value) for name, value, is_html in resolved if not is_html and name not in existing_for_item]
                missing_html = [(name, value) for name, value, is_html in resolved if is_html and name not in existing_for_item]

                if not missing_plain and not missing_html:
                    lang_counts["already_done"] += 1
                    continue

                if args.mode == "dry-run":
                    lang_counts["would_translate"] += 1
                    continue

                item_description = f"{content_type} \"{item['id']}\""
                warnings = []

                if missing_plain:
                    translated = translate_plain_fields(client, translate_system, item_description, missing_plain, lang)
                    for name, text in translated.items():
                        write_translation(auth, content_type, item["id"], name, lang, text)

                for name, html in missing_html:
                    translated_html, field_warnings = translate_html_field(
                        client, translate_system, review_system, item_description, name, html, lang, reviewer_model,
                    )
                    write_translation(auth, content_type, item["id"], name, lang, translated_html)
                    warnings.extend(field_warnings)

                for w in warnings:
                    print(f"  WARNING [{item_description} / {lang}]: {w}")

                lang_counts["translated"] += 1
                print(f"  OK {item_description} -> {lang} ({len(missing_plain)} plain field(s), {len(missing_html)} HTML field(s), {len(warnings)} warning(s))")

    print("\n=== Summary ===")
    for key, lang_counts in counts.items():
        print(f"  {key}: {lang_counts}")


if __name__ == "__main__":
    main()
