#!/usr/bin/env python3
"""
Translates the hand-curated editorial content for the "Pepperstone vs
IC Markets" comparison (inc/broker-comparison-editorial.php) into
every language this pipeline supports, via the same real translate ->
review pipeline as i18n/translate_footer_disclosure.py -- this is
substantial, nuanced prose (a verdict, pros/cons framing, FAQs), not a
handful of short UI strings, so it gets the same quality bar as other
real page content rather than a quick hand translation.

Flattens the structured content (including the two point-list arrays
and the FAQ list) into a flat {id: text} map, translates+reviews each
item, then reassembles the same structure per language. Preserves
every broker name, regulator code, and number exactly (enforced via
the glossary's DO_NOT_TRANSLATE list plus explicit instructions in the
prompt below) -- these are factual, sourced figures, not prose to
adapt.

Runs for all 7 LANGUAGES even though only es/de/ar are live today
(GLOBALFXHUB_LIVE_LANGUAGES) -- same precedent as the rest of this
theme's hardcoded translation tables, so fr/pl/it/pt are ready the
moment those languages launch rather than needing a second pass.

Prints the final {lang: {id: translated_text}} mapping as JSON to
stdout for manual insertion into inc/broker-comparison-editorial.php --
there's no WordPress REST write to make here, same as the footer
disclosure script.

Usage: python3 i18n/translate_comparison_editorial.py
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt

# Mirrors inc/broker-comparison-editorial.php's 'pepperstone-ic-markets'
# entry exactly -- keep these two in sync if the English content ever
# changes.
SOURCE = {
    "meta_title": "Pepperstone vs IC Markets (2026): Which Broker Should You Choose?",
    "meta_description": "Pepperstone vs IC Markets compared on regulation, cost, platforms, and track record -- using our disclosed methodology, not marketing claims. See which fits your trading style.",
    "intro": "Pepperstone and IC Markets are both built around the same pitch: tight ECN-style pricing aimed at active and algorithmic traders, not a mass-market retail product. That similarity is exactly why they get compared so often -- but the two brokers actually differ in ways that matter more than their shared \"built for serious traders\" positioning suggests.",
    "verdict": "Pepperstone holds the wider Tier-1 regulatory footprint (CySEC, FCA, ASIC, and DFSA, against IC Markets' CySEC and ASIC), a far lower minimum deposit ($10 vs $200), and one more supported platform (TradingView, alongside MT4/MT5/cTrader). IC Markets edges ahead on our on-file headline EUR/USD spread (0.8 pips against Pepperstone's 1.1), and has a longer operating history (founded 2007 vs 2010) -- though both brokers also offer a separate raw-spread-plus-commission tier not captured by that single headline figure, so check each broker's current account terms for your specific setup. Under our disclosed methodology, Pepperstone scores 3.64/5 overall against IC Markets' 3.07/5, driven mainly by the regulation category (30% of the total score, and Pepperstone's biggest advantage).",
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
comparing two specific, named brokers (Pepperstone and IC Markets) on
regulation, cost, platforms, and track record. Every number, broker name,
regulator code (CySEC, FCA, ASIC, DFSA), and currency figure ($10, $200,
0.8 pips, 1.1 pips, 3.64/5, 3.07/5, 2007, 2010, etc.) is a sourced fact --
preserve every one of them EXACTLY, never translating, rounding, or
reordering them. Translate the surrounding prose naturally.

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
        BATCH_SCHEMA, tc.TRANSLATE_EFFORT, max_tokens=4000,
    )
    draft = {item["id"]: item["text"] for item in parsed["translations"]}

    review_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.REVIEW_ROLE_INSTRUCTIONS)
    draft_items = [{"id": k, "text": v} for k, v in draft.items()]
    reviewed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, review_system,
        REVIEW_USER_TEMPLATE.format(lang=lang, source=json.dumps(items, ensure_ascii=False), translation=json.dumps(draft_items, ensure_ascii=False)),
        BATCH_SCHEMA, tc.REVIEW_EFFORT, max_tokens=4000,
    )
    return {item["id"]: item["text"] for item in reviewed["translations"]}


def main():
    client = tc.get_client()
    items = [{"id": k, "text": v} for k, v in SOURCE.items()]
    results = {}
    for lang in LANGUAGES:
        print(f"=== {lang} ===", file=sys.stderr)
        results[lang] = translate_and_review(client, lang, items)
        print(json.dumps(results[lang], ensure_ascii=False, indent=2), file=sys.stderr)
        print(file=sys.stderr)

    print(json.dumps(results, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
