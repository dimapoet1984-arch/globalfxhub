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
    "avatrade-fees": {
        "title": "AvaTrade Fees Explained",
        "meta_title": "AvaTrade Fees Explained (2026): Spreads, Swap, and Hidden Costs",
        "meta_description": "A full breakdown of every fee AvaTrade can charge -- spread, overnight financing, inactivity, and currency conversion -- grounded in our own researched broker dataset, not marketing claims.",
        "intro": "AvaTrade doesn't market itself on raw pricing the way a commission-based ECN broker does -- its cost structure is simpler but less transparent on paper, since everything is built into one dealing-desk spread rather than split into spread-plus-commission line items. This breaks down every fee AvaTrade can charge using the same researched data we score it against the other 138 brokers in our dataset. Every figure below is for Ava Trade EU Ltd, the entity reviewed on this site; a different AvaTrade entity elsewhere in the world may set different terms.",
        "section_1_heading": "Spreads and the dealing-desk model",
        "section_1_body": "AvaTrade runs a single pricing model across its account types rather than a Standard/Razor-style split: it operates as a dealing desk / market maker, meaning AvaTrade itself is the counterparty to your trade and sets its own spread, rather than passing through a raw interbank price plus a separate commission the way an ECN broker does. Our on-file headline EUR/USD spread is 0.93 pips. Because the spread is the whole cost -- there's no separate commission line to add or subtract -- what you see quoted is close to the full trading cost, unlike a Razor- or Raw-style account where the headline spread understates the true cost until commission is added back in.",
        "section_2_heading": "Overnight financing (swap) costs",
        "section_2_body": "Standard accounts are charged normal overnight swap rates, calculated the way most brokers do it -- a benchmark rate differential plus AvaTrade's own markup, though the exact markup percentage wasn't confirmed in our research. The Islamic/swap-free account has an unusual structure worth flagging prominently: it's genuinely swap-free only for the first 5 days a position is held, after which an administration fee applies instead -- not the indefinite swap-free status some other brokers' Islamic accounts offer. It also excludes certain currencies (RUB, MXN, TRY, ZAR) and crypto from swap-free treatment entirely, and AvaTrade reserves the right to revoke swap-free status altogether if it suspects abuse of the feature.",
        "section_3_heading": "Non-trading fees: inactivity and currency conversion",
        "section_3_body": "Both of these are genuinely unconfirmed in our research, and we're not going to guess at numbers. We found no inactivity-fee figure for AvaTrade at all in this research pass -- if one exists, confirm it directly with AvaTrade before assuming your account is exempt. Currency conversion is the same: no specific percentage or fee schedule was found, so if your funding currency differs from your account's base currency, check AvaTrade's current terms directly rather than assuming either a specific cost or that it's free.",
        "section_4_heading": "Deposit and withdrawal fees",
        "section_4_body": "No AvaTrade-side fee was confirmed on deposits or withdrawals, though third-party processor or bank fees may still apply depending on your method. The $100 minimum deposit applies across Retail and Islamic accounts alike. Processing time varies by method: deposits are near-instant by card, within 24 hours by e-wallet, and 7-10 business days by wire transfer; withdrawals process in 24-48 hours by card or e-wallet once verified, but can take up to 10 business days by wire.",
        "section_5_heading": "How AvaTrade's costs actually rank against the brokers we've researched",
        "section_5_body": "Running AvaTrade's 0.93-pip EUR/USD spread through our disclosed scoring formula gives it a trading-costs score of 3.79/5 -- 23rd of the 67 brokers in our researched set with a confirmed EUR/USD spread, meaning it's cheaper than roughly two-thirds of the dataset on this one measure. Minimum deposit tells a different story: AvaTrade's $100 only undercuts 24 of the 78 brokers (31%) we have a confirmed minimum deposit for -- on this measure it's pricier to get started than most of the dataset, not cheaper. Where AvaTrade is genuinely rare: it supports 5 trading platforms (its own AvaTradeGO, MT4, MT5, DupliTrade, and ZuluTrade) -- more than any other single broker in our full 139-broker dataset. No other broker we've researched matches or exceeds that platform count.",
        "faq_1_q": "Does AvaTrade charge an inactivity fee?",
        "faq_1_a": "We found no confirmed inactivity-fee figure for AvaTrade in this research pass -- confirm directly with AvaTrade before assuming either way.",
        "faq_2_q": "What execution model does AvaTrade use?",
        "faq_2_a": "Dealing desk / market maker -- AvaTrade is the counterparty to your trade and sets its own spread, rather than operating as a true STP/ECN broker.",
        "faq_3_q": "Does AvaTrade charge a fee to convert currency on deposit?",
        "faq_3_a": "Not independently confirmed -- no specific percentage or fee schedule was found in our research, so check AvaTrade's current terms directly if your funding currency differs from your account's base currency.",
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
