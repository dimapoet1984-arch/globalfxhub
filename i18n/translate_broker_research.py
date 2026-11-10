#!/usr/bin/env python3
"""
Translates broker-specific "deep dive" research articles (fees,
and later regulation, breakdowns for one named broker --
inc/broker-research-articles.php) into every language this pipeline
supports, via the same real translate -> review pipeline as
i18n/translate_comparison_editorial.py -- this is substantial,
nuanced prose (worked explanations, a cross-broker cost comparison,
FAQs), not a handful of short UI strings.

Flattens each article's structured content (sections and FAQs) into a
flat {id: text} map, translates+reviews each item, then reassembles
the same structure per language. Preserves every broker name,
regulator code, and number exactly (enforced via the glossary's
DO_NOT_TRANSLATE list plus explicit instructions in the prompt below)
-- these are sourced facts, not prose to adapt.

Runs for all 7 LANGUAGES even though only es/de/ar are live today
(GLOBALFXHUB_LIVE_LANGUAGES) -- same precedent as the rest of this
theme's hardcoded translation tables.

Reusable across articles: add a new key to ARTICLES below as each new
broker-research piece is drafted, rather than writing a new script per
article. Prints {article_key: {lang: {id: translated_text}}} as JSON
to stdout for manual insertion into
inc/broker-research-articles.php's globalfxhub_broker_research_translations()
-- there's no WordPress REST write to make here, same as the other
hand-curated-content translation scripts in this directory.

Usage: python3 i18n/translate_broker_research.py [--keys pepperstone-fees,...]
"""
import argparse
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt

# Mirrors inc/broker-research-articles.php's globalfxhub_get_broker_research_articles()
# entries exactly -- keep these two in sync if the English content ever
# changes. Flattened the same way as that function's own translation
# overlay expects: title/meta_title/meta_description/intro, then
# section_N_heading/section_N_body per section, then faq_N_q/faq_N_a
# per FAQ.
ARTICLES = {
    "pepperstone-fees": {
        "title": "Pepperstone Fees Explained",
        "meta_title": "Pepperstone Fees Explained (2026): Spreads, Commissions, Swap and Hidden Costs",
        "meta_description": "A full breakdown of every fee Pepperstone can charge -- spreads, Razor commission, swap, inactivity and currency conversion -- grounded in our own researched broker dataset, not marketing claims.",
        "intro": "Pepperstone markets \"raw spreads from 0.0 pips,\" but that's only one line item in what actually determines your cost to trade with it. This breaks down every fee Pepperstone can charge -- spread, commission, overnight financing, inactivity, currency conversion, and deposits/withdrawals -- using the same researched data we score it against the other 138 brokers in our dataset, not Pepperstone's own promotional framing. Every figure below is for Pepperstone EU Limited, the CySEC- and FCA-regulated entity reviewed on this site; a different Pepperstone entity elsewhere in the world may set different terms.",
        "section_1_heading": "Spreads and commissions: Standard vs Razor",
        "section_1_body": "Pepperstone's EU entity offers three account types -- Standard, Razor, and Professional (eligibility-gated, with higher leverage) -- plus an Islamic/swap-free option on both Standard and Razor. Standard charges no separate commission: spreads start from around 1.0 pip, and our own on-file headline EUR/USD spread for the account is 1.1 pips. Razor instead charges a raw spread from 0.0 pips plus a round-turn commission of roughly $6-7 per lot, which varies by the platform you trade on. Execution is no-dealing-desk on both tiers -- Standard runs STP-style, Razor runs ECN-style -- and direct market access (DMA) CFDs are available on either tier via cTrader and MT4/MT5. Which account is actually cheaper depends on your own trade size and frequency: a trader placing a few, larger trades often pays less in pure spread on Standard than in spread-plus-commission on Razor, while an active or algorithmic trader running higher volume at the raw Razor spread typically comes out ahead once the commission is netted against the tighter spread. There's no substitute for running your own numbers against your actual account size, trade size, and trading frequency.",
        "section_2_heading": "Overnight financing (swap) costs",
        "section_2_body": "Hold a position open overnight and Pepperstone charges a swap fee based on the tom-next rate from its liquidity providers, plus its own admin/funding fee on top of that. The Standard account doesn't break this out as a separate line: instead, Pepperstone bakes roughly a 1-pip markup into the spread rather than charging a distinct overnight fee. Switch to the Islamic/swap-free variant of either account and the daily swap itself disappears, but a reported $100-per-lot administration fee applies for every five-day period a position stays open -- worth modelling carefully if you intend to hold positions for weeks rather than days, since that flat fee doesn't scale down the way a per-night swap would for a smaller position.",
        "section_3_heading": "Non-trading fees: inactivity and currency conversion",
        "section_3_body": "Multiple independent sources we checked report that Pepperstone charges no inactivity fee at all, though we weren't able to independently verify that directly against the EU entity's own client agreement -- treat it as well-corroborated rather than contractually confirmed. On currency conversion, Pepperstone itself doesn't appear to charge a deposit fee when your funding currency differs from your account's base currency, but the sending bank or card network may apply its own conversion markup on that transfer, and we couldn't independently confirm a specific percentage for that pass-through cost.",
        "section_4_heading": "Deposit and withdrawal fees",
        "section_4_body": "Pepperstone charges no fee on deposits or withdrawals except for international bank-transfer withdrawals, which carry a fee of roughly $20 (or £15). There's no minimum via bank transfer; card, Apple Pay, and Google Pay deposits carry a minimum of around £10. Processing time matters as much as the fee itself here: card deposits land instantly and bank-transfer deposits take up to two business days, while withdrawals range from under one business day (card, Apple Pay, Google Pay) to up to two business days (domestic bank transfer) to as long as seven business days (international bank transfer).",
        "section_5_heading": "How Pepperstone's costs actually rank against the brokers we've researched",
        "section_5_body": "Running Pepperstone's 1.1-pip EUR/USD spread through our disclosed scoring formula gives it a trading-costs score of 3.57/5 -- 31st of the 65 brokers in our researched set with a confirmed EUR/USD spread, almost exactly the dataset's median rather than meaningfully cheap or expensive on that one measure. That's worth knowing because Pepperstone's $10 minimum deposit undercuts 59 of the 78 brokers (76%) we have a confirmed minimum deposit for, and its overall score of 3.75/5 sits well above the dataset's midpoint -- \"accessible and well-regulated\" and \"cheapest spread\" are two different claims, and only the first one holds up for Pepperstone on the numbers. One place it is genuinely rare: Pepperstone is one of just 5 of the 139 brokers we've researched confirmed to support all four of MT4, MT5, cTrader, and TradingView -- platform breadth that's a real differentiator even where the headline spread itself is unremarkable.",
        "faq_1_q": "Does Pepperstone charge an inactivity fee?",
        "faq_1_a": "Multiple independent sources we checked report that Pepperstone charges no inactivity fee, though we weren't able to verify that directly against the EU entity's own client agreement.",
        "faq_2_q": "What's the difference between Pepperstone's Standard and Razor accounts?",
        "faq_2_a": "Standard charges no separate commission, with spreads from around 1.0 pip (our on-file EUR/USD figure is 1.1 pips); Razor charges a raw spread from 0.0 pips plus a round-turn commission of roughly $6-7 per lot that varies by platform. Both have an Islamic/swap-free variant, and execution is no-dealing-desk on each -- Standard runs STP-style, Razor runs ECN-style.",
        "faq_3_q": "Does Pepperstone charge a fee to convert currency on deposit?",
        "faq_3_a": "Not from Pepperstone itself, as far as we could confirm -- but the bank or card network handling the transfer may apply its own conversion markup if your funding currency differs from your account's base currency, and we couldn't independently confirm a specific percentage for that pass-through cost.",
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

This is a broker-specific "fees explained" research article for a forex/CFD
broker research site, about one specific, named broker (Pepperstone). Every
number, broker/account-type name, platform name, regulator reference, and
currency figure ($10, $200, $6-7, $20, £15, £10, $100, 1.0 pip, 1.1 pips,
0.0 pips, 3.57/5, 3.75/5, 31, 65, 59, 78, 76%, 5, 139, 138, MT4, MT5,
cTrader, TradingView, Standard, Razor, Professional, etc.) is a sourced
fact -- preserve every one of them EXACTLY, never translating, rounding, or
reordering them. Translate the surrounding prose naturally.

Translate each item below by id, preserving the JSON structure:
{items}"""

REVIEW_USER_TEMPLATE = """\
Target language: {lang}

Review this machine translation of a broker-specific fees research article
against the English original. Fix grammar/register, but do not change,
soften, or strengthen any claim, and do not alter any number, broker/account
name, platform name, or currency figure.

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
    parser.add_argument("--keys", default="", help="Comma-separated article keys to translate (default: all)")
    args = parser.parse_args()
    keys = [k.strip() for k in args.keys.split(",") if k.strip()] or list(ARTICLES.keys())

    client = tc.get_client()
    results = {}
    for key in keys:
        source = ARTICLES[key]
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
