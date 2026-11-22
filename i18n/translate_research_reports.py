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
    "minimum-deposit-report": {
        "title": "Minimum Deposit Report",
        "meta_title": "Minimum Deposit Requirements Across 139 Forex Brokers (2026): How Low Can You Actually Start?",
        "meta_description": "What forex brokers actually require to open an account, from $0 to $10,800 -- an original data study across our full 139-broker research set, including which minimums are confirmed and which brokers don't publish one.",
        "intro": "How much money it actually takes to open an account rarely gets the same side-by-side treatment as spreads or regulation -- so we pulled the min_deposit_usd field already used throughout this site's reviews and best-broker lists and counted it across the full research set. This report covers every confirmed minimum deposit figure: where the typical broker lands, which brokers require nothing at all, which require four figures, and how much of the dataset doesn't have one single confirmed number at all. Nothing here is newly sourced -- it's the same field already disclosed on each broker's own review page.",
        "section_1_heading": "A $100 median among confirmed brokers, but a quarter require $10 or less",
        "section_1_body": "79 of the 139 brokers in our research set (57%) have a confirmed minimum-deposit figure; the median among them is $100. Underneath that median, though, 26 of those 79 (33%) require under $50, and 20 (25%) require $10 or less. The mean is $415 -- far higher than the median -- because a handful of four-figure outliers pull it up; the median is the more representative number for what a typical account actually costs to open.",
        "section_2_heading": "Ten brokers confirmed at a flat $0 minimum deposit",
        "section_2_body": "ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX, and FXCC all confirm no minimum deposit at all. They span our rank list from 3rd to 100th, so a $0 minimum tracks with neither being a top performer nor a weak one on its own.",
        "section_3_heading": "A small number of brokers require four-figure minimums -- and they're a different kind of broker",
        "section_3_body": "EXANTE ($10,800) and LMAX ($10,000) sit well above the rest of the dataset, followed by GBE Brokers and EXCA Prime (both $1,080) and Swissquote ($1,000). These aren't retail-first operations competing on accessibility -- EXANTE and LMAX are positioned toward professional/institutional clients, and Swissquote is a Swiss private bank -- so their minimums reflect a different target client, not a worse deal for retail traders.",
        "section_4_heading": "Minimum deposit carries no weight in our ranking formula, but our better-ranked brokers skew lower anyway",
        "section_4_body": "min_deposit_usd isn't one of the nine weighted components in our scoring formula (regulation 30%, cost 20%, non-trading fees 10%, platforms 10%, execution 10%, product range 5%, deposits/withdrawals 5%, transparency 5%, track record 5%) -- a broker's minimum deposit has zero direct effect on its score. Even so, our top 40 ranked brokers (32 with a confirmed figure) show a $50 median, against a $100 median for the remaining 99 (47 confirmed) -- and 59% of the top 40's confirmed brokers sit under $100, against 36% of the rest. That's a correlation, not a causal claim: the same brokers competing hardest on regulation and spread also tend to compete on accessibility, but a low minimum doesn't buy rank.",
        "section_5_heading": "60 of 139 brokers have no single confirmed minimum -- usually because there isn't one flat number to confirm",
        "section_5_body": "Brokers like Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo, and HYCM don't have a confirmed figure in our research, most often because their real minimum varies by legal entity, account currency, or account type rather than being one number the broker publishes once. We count that honestly as unconfirmed rather than guessing at a figure or defaulting it to $0.",
        "faq_1_q": "What's the average minimum deposit required by forex brokers?",
        "faq_1_a": "Across the 79 of 139 brokers in our research set with a confirmed figure, the median is $100 and 33% require under $50. The mean ($415) is pulled much higher by a handful of four-figure outliers aimed at professional or private-banking clients, so the median is the more representative number.",
        "faq_2_q": "Which forex brokers have no minimum deposit?",
        "faq_2_a": "Ten confirmed: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX, and FXCC.",
        "faq_3_q": "Does a low minimum deposit mean a broker is better?",
        "faq_3_a": "Not directly -- minimum deposit isn't one of the nine weighted components in our ranking formula. But our top-40 confirmed brokers do skew lower (a $50 median) than the rest of the set ($100 median), likely because brokers competing hardest on regulation and spread also tend to compete on accessibility.",
        "faq_4_q": "Why don't all 139 brokers have a listed minimum deposit?",
        "faq_4_a": "60 don't have one confirmed figure, usually because the actual minimum varies by legal entity, account currency, or account type -- common at multi-jurisdiction brokers -- rather than there being one flat number the broker publishes.",
    },
    "eurusd-spread-report": {
        "title": "EUR/USD Spread Report",
        "meta_title": "EUR/USD Spread Report (2026): What Trading Costs Actually Look Like Across 139 Forex Brokers",
        "meta_description": "The real spread distribution across our full broker dataset -- median, tightest, widest, and which well-known brokers don't publish a confirmed figure at all -- computed from the same field behind every cost score on this site.",
        "intro": "A broker's EUR/USD spread is the single number most cost comparisons reduce everything to, but side-by-side spread data across a real, broad set of brokers is rarer than it should be. We pulled the spread_eurusd field already used throughout this site's reviews, cost calculator, and scoring formula and counted it across the full 139-broker set. This report covers the real distribution: where the typical confirmed broker lands, how tight the tightest spreads actually get, and how much of the dataset has no confirmed figure at all.",
        "section_1_heading": "A 1.1-pip median among confirmed brokers, with a third under 1.0 pip",
        "section_1_body": "67 of the 139 brokers in our research set (48%) have a confirmed EUR/USD spread; the median among them is 1.1 pips, and the mean is higher still (1.25 pips), pulled up by a handful of wide outliers. Underneath that median, 23 of the 67 (34%) confirm a spread under 1.0 pip, and 6 (9%) confirm under 0.5 pips.",
        "section_2_heading": "One broker confirmed at a flat 0.0-pip EUR/USD spread -- with a caveat worth stating plainly",
        "section_2_body": "FXView is the only broker in our dataset confirmed at a 0.0-pip headline EUR/USD spread. A 0.0-pip headline is common on ECN/raw-style accounts that charge a separate per-lot commission instead of building the cost into the spread -- it doesn't necessarily mean trading is free, just that the cost is structured differently. Confirm the full cost (spread plus any commission) directly before assuming the lowest headline number is automatically the cheapest account.",
        "section_3_heading": "A handful of brokers confirm spreads of 2 pips or more",
        "section_3_body": "LiteForex (2.0), TrioMarkets (2.4), Fortrade (2.5), Trading 212 (2.7), InstaForex (3.0), Trade360 (4.0), and OEXN (5.0) sit well above the rest of the confirmed set. A wider headline spread isn't automatically a red flag -- it can reflect a different account model or client base -- but it's a real, confirmed cost difference worth knowing before comparing these brokers to a tighter-spread one on anything else.",
        "section_4_heading": "Our better-ranked brokers confirm tighter spreads -- and here the correlation is partly by design",
        "section_4_body": "Brokers ranked in our top 40 (31 with a confirmed spread) show a 1.0-pip median, with 48% confirming under 1.0 pip; the remaining 99 (36 confirmed) show a 1.5-pip median, with only 22% under 1.0 pip. Unlike minimum deposit, which carries no weight in our formula, EUR/USD spread feeds directly into our cost score at a 20% weighting -- the second-heaviest single factor after regulation. So this pattern isn't just a correlation we're noting after the fact: a broker with a tighter confirmed spread mechanically scores better on this factor by construction, not merely tends to.",
        "section_5_heading": "72 of 139 brokers have no confirmed EUR/USD spread -- including some well-known, well-regulated names",
        "section_5_body": "Interactive Brokers (rank 2), OANDA (rank 6), Vantage Markets, Ultima Markets, CMC Markets, and several others don't have a confirmed figure in our research, most often because of variable or market-maker-style pricing without one published headline number, rather than any gap in how well-regulated or well-known the broker is. Our cost score for these brokers is excluded from their overall average rather than guessed at or defaulted to the dataset's median.",
        "faq_1_q": "What's the average EUR/USD spread among forex brokers?",
        "faq_1_a": "Across the 67 of 139 brokers in our research set with a confirmed figure, the median is 1.1 pips. The mean (1.25 pips) is pulled higher by a handful of wide outliers, so the median is the more representative number.",
        "faq_2_q": "Which broker has the tightest confirmed EUR/USD spread?",
        "faq_2_a": "FXView, at a flat 0.0 pips -- though a 0.0-pip headline spread often means the cost moved to a separate commission instead, so it isn't automatically the cheapest account overall.",
        "faq_3_q": "Does a lower EUR/USD spread mean a broker scores better?",
        "faq_3_a": "Yes, directly -- unlike minimum deposit, EUR/USD spread is weighted 20% in our cost score, the second-heaviest single factor in our overall formula after regulation.",
        "faq_4_q": "Why don't all 139 brokers have a confirmed EUR/USD spread?",
        "faq_4_a": "72 don't, usually because of variable or market-maker-style pricing without one published headline figure -- including some well-known names like Interactive Brokers and OANDA.",
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
research dataset. Every number, percentage, currency figure, and
platform/broker/regulator name appearing in the text below is a sourced
statistic -- preserve every one of them EXACTLY, never translating,
rounding, or reordering them. Translate the surrounding prose naturally.

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
