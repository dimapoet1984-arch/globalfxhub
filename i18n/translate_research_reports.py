#!/usr/bin/env python3
"""
Translates original data reports (inc/research-reports.php) into
every language this pipeline supports, via the same real translate ->
review pipeline as i18n/translate_broker_research.py and
i18n/translate_regulation_comparisons.py.

Flattens each report's structured content (sections and FAQs) into a
flat {id: text} map, translates+reviews each item, then reassembles
the same structure per language. Preserves every platform name,
broker name, and number exactly (enforced via the glossary's
DO_NOT_TRANSLATE list plus explicit instructions in the prompt below)
-- these are sourced statistics computed from the live 139-broker
dataset, not prose to adapt.

Runs for all 7 LANGUAGES even though only es/de/ar are live today
(GLOBALFXHUB_LIVE_LANGUAGES) -- same precedent as the rest of this
theme's hardcoded translation tables.

Reusable across reports: add a new key to REPORTS below as each new
original-research piece is drafted, rather than writing a new script
per report. Prints {report_key: {lang: {id: translated_text}}} as
JSON to stdout for manual insertion into
inc/research-reports.php's globalfxhub_research_report_translations()
-- there's no WordPress REST write to make here, same as the other
hand-curated-content translation scripts in this directory.

Usage: python3 i18n/translate_research_reports.py [--keys forex-trading-platform-adoption-report,...]
"""
import argparse
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt

# Mirrors inc/research-reports.php's globalfxhub_get_research_reports()
# entries exactly -- keep these two in sync if the English content ever
# changes. Flattened the same way as that function's own translation
# overlay expects: title/meta_title/meta_description/intro, then
# section_N_heading/section_N_body per section, then faq_N_q/faq_N_a
# per FAQ.
REPORTS = {
    "forex-trading-platform-adoption-report": {
        "title": "Forex Trading Platform Adoption Report",
        "meta_title": "Forex Trading Platform Adoption Report (2026): MT4, MT5, cTrader and TradingView Across 139 Brokers",
        "meta_description": "How many forex brokers still support MT4, how many have moved to MT5, and how rare is cTrader or TradingView support -- an original data study across our full 139-broker research set.",
        "intro": "Which trading platforms a broker actually supports rarely gets the same scrutiny as its spread or its regulator -- so we pulled it directly from our own 139-broker research set and counted it. This report covers every confirmed platform across that full set: how common MT4 and MT5 still are, how many brokers run them side by side versus having moved on, and how rare cTrader and TradingView support actually is. Every figure below comes from the same platforms field used throughout this site's reviews, best-broker lists, and comparisons -- nothing here is a separate, newly-sourced claim.",
        "section_1_heading": "MT4 is still the most common platform, but MT5 has nearly caught up",
        "section_1_body": "103 of the 139 brokers in our research set (74%) are confirmed to support MT4, and 97 (70%) are confirmed to support MT5. Despite MetaTrader 4 being the older of the two platforms, and despite MetaQuotes having stopped actively developing it in favor of MT5, it remains the more widely supported platform by a modest margin -- brokers have not abandoned MT4 at anywhere near the rate its \"legacy platform\" reputation might suggest.",
        "section_2_heading": "Most brokers run MT4 and MT5 side by side -- few have actually dropped MT4",
        "section_2_body": "84 of the 139 brokers support both MT4 and MT5 at once -- by far the most common setup. Only 13 of the 97 brokers that support MT5 (13%) have dropped MT4 entirely; the other 87% keep both running in parallel. 19 brokers still offer MT4 without having added MT5 at all, and 23 brokers (17% of the full set) offer neither platform, relying instead on cTrader, TradingView, or a proprietary platform of their own.",
        "section_3_heading": "cTrader and TradingView remain minority platforms",
        "section_3_body": "18 of the 139 brokers (13%) are confirmed to support cTrader, and 16 (12%) are confirmed to support TradingView -- both still a clear minority next to MT4/MT5, though far from negligible. The two overlap more than they compete: 7 brokers support both cTrader and TradingView, and only 1 of the 16 TradingView-supporting brokers does so without also offering MT4, meaning TradingView support is almost always additive to a broker's existing MetaTrader offering rather than a replacement for it.",
        "section_4_heading": "TradingView support correlates strongly with our top-ranked brokers",
        "section_4_body": "Splitting the dataset at our top 40 by overall rank shows a real gap: 30% of our top-40 brokers (12 of 40) support TradingView, against just 4% of the remaining 99 (4 brokers). MT4 shows a smaller version of the same pattern -- 83% of the top 40 support it, against 71% of the rest. This is a correlation, not a causal claim: supporting TradingView doesn't itself make a broker better-regulated or cheaper, and our ranking formula doesn't reward platform count directly (platforms is a flat function of how many platforms are confirmed, weighted at 10% of the overall score) -- but the two clearly tend to travel together in this dataset.",
        "section_5_heading": "The five brokers confirmed to support all four major platforms",
        "section_5_body": "Only 5 of the 139 brokers in our research set -- Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital -- are confirmed to support all four of MT4, MT5, cTrader, and TradingView at once. That's a genuinely rare combination: offering every major third-party platform means supporting four separate pieces of trading infrastructure rather than concentrating on one or two, and all five of these brokers happen to also run an ECN/no-dealing-desk-style execution model, though our dataset doesn't let us say whether that's a cause, a correlation, or a coincidence at this sample size.",
        "section_6_heading": "Proprietary platforms are common individually, but fragmented as a category",
        "section_6_body": "Beyond the big four, 22 of the 139 brokers confirm exactly one platform, and 5 of those are MT4-only. The rest of the dataset is a long tail of broker-specific proprietary platforms -- each one typically confirmed for exactly one broker, with no single proprietary platform used by more than a handful of brokers -- so \"proprietary\" is common as a category but fragmented in practice, unlike the real cross-broker standardisation MT4, MT5, cTrader, and TradingView each represent. 12 of the 139 brokers have no platform confirmed in our research at all; we're treating that as an honest data gap rather than guessing, consistent with how every other unconfirmed field on this site is handled.",
        "faq_1_q": "Is MT4 being phased out by forex brokers?",
        "faq_1_a": "Not yet, at least not among the brokers we've researched: 74% still support it, and 87% of the brokers that have added MT5 keep MT4 running alongside it rather than dropping it. Only 13% of MT5-supporting brokers in our dataset have moved on from MT4 entirely.",
        "faq_2_q": "Which forex brokers support MT4, MT5, cTrader, and TradingView?",
        "faq_2_a": "Five brokers in our 139-broker research set are confirmed to support all four: Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital.",
        "faq_3_q": "Does TradingView support mean a broker is better?",
        "faq_3_a": "Not directly -- our scoring formula doesn't reward platform count on its own. But there's a real correlation in our data: 30% of our top-40-ranked brokers support TradingView, against just 4% of the remaining 99, so platform breadth and overall ranking clearly tend to move together even though one doesn't mechanically cause the other.",
        "faq_4_q": "How was this data collected?",
        "faq_4_a": "From the platforms field already used throughout this site's broker reviews, best-broker lists, and comparisons -- the same 139-broker research set, not a separate survey. Where we couldn't independently confirm a broker's platform support, we counted it as unconfirmed rather than assuming either way; 12 of the 139 brokers fall into that category.",
    },
}

BATCH_SCHEMA = {
    "type": "object",
    "properties": {
        "translations": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "id": {"type": "string"},
                    "text": {"type": "string"},
                },
                "required": ["id", "text"],
                "additionalProperties": False,
            },
        },
    },
    "required": ["translations"],
    "additionalProperties": False,
}

TRANSLATE_USER_TEMPLATE = """\
Target language: {lang}

This is an original data report for a forex/CFD broker research site,
presenting statistics computed directly from the site's own 139-broker
research dataset (platform adoption: MT4, MT5, cTrader, TradingView).
Every number and platform/broker name (103, 97, 84, 13, 19, 23, 18, 16, 7,
1, 30, 12, 40, 4, 99, 83, 71, 5, 139, 22, 74%, 70%, 13%, 87%, 17%, 12%, 30%,
4%, MT4, MT5, cTrader, TradingView, Pepperstone, FP Markets, GO Markets,
Fusion Markets, ICM Capital, etc.) is a sourced statistic -- preserve every
one of them EXACTLY, never translating, rounding, or reordering them.
Translate the surrounding prose naturally.

Translate each item below by id, preserving the JSON structure:
{items}"""

REVIEW_USER_TEMPLATE = """\
Target language: {lang}

Review this machine translation of an original data-report article against
the English original. Fix grammar/register, but do not change, soften, or
strengthen any claim, and do not alter any number, platform name, or
broker name.

English original:
{source}

Machine translation to review:
{translation}"""


def translate_and_review(client, lang, items):
    system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.TRANSLATE_ROLE_INSTRUCTIONS)
    parsed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, system,
        TRANSLATE_USER_TEMPLATE.format(lang=lang, items=json.dumps(items, ensure_ascii=False)),
        BATCH_SCHEMA, tc.TRANSLATE_EFFORT, max_tokens=16000,
    )
    draft = {item["id"]: item["text"] for item in parsed["translations"]}

    review_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.REVIEW_ROLE_INSTRUCTIONS)
    draft_items = [{"id": k, "text": v} for k, v in draft.items()]
    reviewed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, review_system,
        REVIEW_USER_TEMPLATE.format(lang=lang, source=json.dumps(items, ensure_ascii=False), translation=json.dumps(draft_items, ensure_ascii=False)),
        BATCH_SCHEMA, tc.REVIEW_EFFORT, max_tokens=16000,
    )
    return {item["id"]: item["text"] for item in reviewed["translations"]}


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--keys", default="", help="Comma-separated report keys to translate (default: all)")
    args = parser.parse_args()
    keys = [k.strip() for k in args.keys.split(",") if k.strip()] or list(REPORTS.keys())

    client = tc.get_client()
    results = {}
    for key in keys:
        source = REPORTS[key]
        items = [{"id": k, "text": v} for k, v in source.items()]
        results[key] = {}
        for lang in LANGUAGES:
            print(f"=== {key} / {lang} ===", file=sys.stderr)
            results[key][lang] = translate_and_review(client, lang, items)
            print(json.dumps(results[key][lang], ensure_ascii=False, indent=2), file=sys.stderr)
            print(file=sys.stderr)

    print(json.dumps(results, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
