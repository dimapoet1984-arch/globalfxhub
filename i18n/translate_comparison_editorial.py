#!/usr/bin/env python3
"""
Translates hand-curated broker-vs-broker editorial comparisons
(inc/broker-comparison-editorial.php) into every language this
pipeline supports, via the same real translate -> review pipeline as
i18n/translate_footer_disclosure.py -- this is substantial, nuanced
prose (a verdict, pros/cons framing, FAQs), not a handful of short UI
strings, so it gets the same quality bar as other real page content
rather than a quick hand translation.

Flattens each comparison's structured content (the two point-list
arrays and the FAQ list) into a flat {id: text} map, translates+
reviews each item, then reassembles the same structure per language.
Preserves every broker name, regulator code, and number exactly
(enforced via the glossary's DO_NOT_TRANSLATE list plus explicit
instructions in the prompt below) -- these are factual, sourced
figures, not prose to adapt.

Runs for all 7 LANGUAGES even though only es/de/ar are live today
(GLOBALFXHUB_LIVE_LANGUAGES) -- same precedent as the rest of this
theme's hardcoded translation tables, so fr/pl/it/pt are ready the
moment those languages launch rather than needing a second pass.

Reusable across comparisons: add a new key to COMPARISONS below as
each new broker-vs-broker pairing is drafted, rather than writing a
new script per pairing (this script originally only handled
Pepperstone vs IC Markets as a single hardcoded SOURCE dict -- same
one-off shape i18n/translate_broker_research.py,
i18n/translate_regulation_comparisons.py, and
i18n/translate_research_reports.py all started as before being made
reusable the same way). Prints {comparison_key: {lang: {id: translated_text}}}
as JSON to stdout for manual insertion into
inc/broker-comparison-editorial.php's globalfxhub_editorial_comparison_translations()
-- there's no WordPress REST write to make here, same as the other
hand-curated-content translation scripts in this directory.

Usage: python3 i18n/translate_comparison_editorial.py [--keys pepperstone-ic-markets,...]
"""
import argparse
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt

# Mirrors inc/broker-comparison-editorial.php's globalfxhub_get_editorial_comparisons()
# entries exactly -- keep these two in sync if the English content ever
# changes. Flattened the same way as that function's own translation
# overlay expects: meta_title/meta_description/intro/verdict, then
# choose_a_heading/choose_a_point_N, choose_b_heading/choose_b_point_N,
# worth_knowing_heading/worth_knowing, then faq_N_q/faq_N_a.
COMPARISONS = {
    "pepperstone-ic-markets": {
        "meta_title": "Pepperstone vs IC Markets (2026): Which Broker Should You Choose?",
        "meta_description": "Pepperstone vs IC Markets compared on regulation, cost, platforms, and track record -- using our disclosed methodology, not marketing claims. See which fits your trading style.",
        "intro": "Pepperstone and IC Markets are both built around the same pitch: tight ECN-style pricing aimed at active and algorithmic traders, not a mass-market retail product. That similarity is exactly why they get compared so often -- but the two brokers actually differ in ways that matter more than their shared \"built for serious traders\" positioning suggests.",
        "verdict": "Pepperstone holds the wider Tier-1 regulatory footprint (CySEC, FCA, ASIC, and DFSA, against IC Markets' CySEC and ASIC), a far lower minimum deposit ($10 vs $200), and one more supported platform (TradingView, alongside MT4/MT5/cTrader). IC Markets edges ahead on our on-file headline EUR/USD spread (0.8 pips against Pepperstone's 1.1), and has a longer operating history (founded 2007 vs 2010) -- though both brokers also offer a separate raw-spread-plus-commission tier not captured by that single headline figure, so check each broker's current account terms for your specific setup. Under our disclosed methodology, Pepperstone scores 3.75/5 overall against IC Markets' 3.46/5, driven mainly by the regulation category (30% of the total score, and Pepperstone's biggest advantage).",
        "choose_a_heading": "Choose Pepperstone if...",
        "choose_a_point_1": "You want the broader regulatory footprint -- Pepperstone holds CySEC, FCA, ASIC, and DFSA authorisation, against IC Markets' CySEC and ASIC.",
        "choose_a_point_2": "You're starting with a small account -- Pepperstone's $10 minimum deposit is a fraction of IC Markets' $200.",
        "choose_a_point_3": "You trade on TradingView -- Pepperstone supports it directly; IC Markets does not.",
        "choose_a_point_4": "An explicit no-inactivity-fee policy matters to you -- multiple independent sources confirm Pepperstone charges none, with no unresolved conflict in our research.",
        "choose_b_heading": "Choose IC Markets if...",
        "choose_b_point_1": "Raw headline spread is your priority and you can meet the $200 minimum -- IC Markets' 0.8-pip EUR/USD spread is tighter than Pepperstone's 1.1 pips on the comparable tier.",
        "choose_b_point_2": "A longer operating history matters to you -- IC Markets has operated since 2007, three years longer than Pepperstone.",
        "choose_b_point_3": "You don't need DFSA coverage or TradingView, and ASIC plus CySEC is enough regulatory footprint for your needs.",
        "worth_knowing_heading": "Worth knowing before you decide",
        "worth_knowing": "IC Markets' EU entity publishes a formal Inactivity Fees Policy (an account is defined as inactive after 6 continuous months with no executed trade), but multiple independent comparison sites report that no fee is actually charged in practice -- a genuine, unresolved conflict between the broker's own published policy and third-party reporting that we could not resolve in our research. If dormancy risk matters to you, confirm the current position directly with IC Markets before relying on either source. Pepperstone's no-inactivity-fee claim, by contrast, is consistently corroborated across the sources we checked.",
        "faq_1_q": "Is Pepperstone or IC Markets better for beginners?",
        "faq_1_a": "Pepperstone's $10 minimum deposit makes it far more accessible to start with than IC Markets' $200, and its wider regulatory footprint (adding FCA and DFSA alongside CySEC and ASIC) gives a beginner more regulatory coverage to fall back on. Neither broker's account structure is explicitly designed for beginners over active traders, though.",
        "faq_2_q": "Which has the lower trading cost, Pepperstone or IC Markets?",
        "faq_2_a": "On our on-file headline EUR/USD spread, IC Markets is tighter at 0.8 pips against Pepperstone's 1.1 pips. Both also offer a separate raw-spread-plus-commission tier (Pepperstone's Razor, IC Markets' Raw Spread) with broadly similar commission levels -- check each broker's own current fee schedule for your specific account type before relying on either headline figure.",
    },
    "exness-xm": {
        "meta_title": "Exness vs XM (2026): Which Broker Should You Choose?",
        "meta_description": "Exness vs XM compared on spreads, fees, platforms, and regulation -- using our disclosed methodology, not marketing claims. See which fits your trading style.",
        "intro": "Exness and XM are both Cyprus-headquartered, CySEC-licensed brokers with a large international retail client base and a long operating history -- which is exactly why they get compared so often. But the two differ sharply on cost and platform breadth, in ways a shared \"established, well-known broker\" reputation doesn't capture.",
        "verdict": "Exness holds a clear overall edge under our disclosed methodology, scoring 3.97/5 against XM's 3.42/5 -- driven mainly by cost (Exness's 0.2-pip on-file EUR/USD spread against XM's 1.1 pips) and platform breadth (Exness supports MT4, MT5, and its own proprietary Exness Terminal; XM supports MT4 and MT5 only). Both hold the same regulation score (4.2/5) despite different specific footprints: XM adds ASIC and DFSA alongside CySEC, while Exness adds South Africa's FSCA and Kenya's CMA -- neither broker's FCA status is independently confirmed as serving retail clients, and Exness's own disclosures state its FCA entity specifically does not. XM's $5 minimum deposit is the lower, single clearly-confirmed figure; Exness's typical $10 minimum is reported to go as low as $1 via some payment methods, though that specific claim wasn't independently confirmed.",
        "choose_a_heading": "Choose Exness if...",
        "choose_a_point_1": "Trading cost is your priority -- Exness's on-file EUR/USD spread of 0.2 pips is dramatically tighter than XM's 1.1 pips, reflected in a much higher cost score (4.74/5 against 3.57/5).",
        "choose_a_point_2": "You want an explicit no-inactivity-fee policy -- multiple independent sources confirm Exness charges none, while XM charges a $15 one-off fee after 90 days of inactivity plus an ongoing $5/month afterward.",
        "choose_a_point_3": "Platform choice matters to you -- Exness supports MT4, MT5, and its own proprietary Exness Terminal; XM supports MT4 and MT5 only.",
        "choose_b_heading": "Choose XM if...",
        "choose_b_point_1": "You're starting with a very small, clearly-confirmed minimum -- XM's $5 minimum deposit is a single reliable figure, where Exness's reported minimums conflict across sources (commonly $10, but cited as low as $1 on some payment methods and as high as $200 for its Pro account elsewhere).",
        "choose_b_point_2": "You want a wider menu of account types for different trading styles -- XM offers five (Micro, Standard, Ultra Low, Zero, and a direct Shares account), against Exness's four (Standard, Pro, Raw Spread, and Zero).",
        "choose_b_point_3": "You want ASIC and DFSA alongside CySEC specifically -- XM's additional regulators are both established Tier-1-style jurisdictions, where Exness's additional coverage (FSCA, CMA) sits outside that set.",
        "worth_knowing_heading": "Worth knowing before you decide",
        "worth_knowing": "Both brokers' execution models carry some ambiguity in independent sourcing. More recent coverage of XM describes it as operating a market-maker/dealing-desk model on most accounts, despite older material positioning it as no-dealing-desk -- the two accounts appear to reflect a shift over time rather than a live disagreement. Exness is different: current independent sources actively disagree, with some describing a market-maker/dealing-desk default and Exness's own marketing describing STP/no-dealing-desk execution -- a genuine, unresolved conflict we could not settle in our research. If execution model matters to your strategy, checking each broker's own order-execution policy directly is the only way to get a current, authoritative answer.",
        "faq_1_q": "Which is cheaper, Exness or XM?",
        "faq_1_a": "Exness, by a wide margin on our on-file figures: its EUR/USD spread of 0.2 pips is far tighter than XM's 1.1 pips, and its cost score under our methodology (4.74/5) is correspondingly far ahead of XM's (3.57/5).",
        "faq_2_q": "Does Exness or XM charge an inactivity fee?",
        "faq_2_a": "XM does: a $15 one-off fee after 90 days of inactivity, followed by an ongoing $5/month charge if the account stays dormant. Multiple independent sources confirm Exness charges no inactivity fee at all.",
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

This is editorial comparison content for a forex/CFD broker research site,
comparing two specific, named brokers. Every number, broker name,
regulator code, and currency figure is a sourced fact -- preserve every
one of them EXACTLY, never translating, rounding, or reordering them.
Translate the surrounding prose naturally.

Translate each item below by id, preserving the JSON structure:
{items}"""

REVIEW_USER_TEMPLATE = """\
Target language: {lang}

Review this machine translation of editorial broker-comparison content
against the English original. Fix grammar/register, but do not change,
soften, or strengthen any claim, and do not alter any number, broker name,
or regulator code.

English original:
{source}

Machine translation to review:
{translation}"""


def translate_and_review(client, lang, items):
    system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.TRANSLATE_ROLE_INSTRUCTIONS)
    parsed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, system,
        TRANSLATE_USER_TEMPLATE.format(lang=lang, items=json.dumps(items, ensure_ascii=False)),
        BATCH_SCHEMA, tc.TRANSLATE_EFFORT, max_tokens=12000,
    )
    draft = {item["id"]: item["text"] for item in parsed["translations"]}

    review_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.REVIEW_ROLE_INSTRUCTIONS)
    draft_items = [{"id": k, "text": v} for k, v in draft.items()]
    reviewed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, review_system,
        REVIEW_USER_TEMPLATE.format(lang=lang, source=json.dumps(items, ensure_ascii=False), translation=json.dumps(draft_items, ensure_ascii=False)),
        BATCH_SCHEMA, tc.REVIEW_EFFORT, max_tokens=12000,
    )
    return {item["id"]: item["text"] for item in reviewed["translations"]}


def main():
    parser = argparse.ArgumentParser()
    parser.add_argument("--keys", default="", help="Comma-separated comparison keys to translate (default: all)")
    args = parser.parse_args()
    keys = [k.strip() for k in args.keys.split(",") if k.strip()] or list(COMPARISONS.keys())

    client = tc.get_client()
    results = {}
    for key in keys:
        source = COMPARISONS[key]
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
