#!/usr/bin/env python3
"""
Translates head-to-head regulator comparisons (inc/regulation-comparisons.php)
into every language this pipeline supports, via the same real
translate -> review pipeline as i18n/translate_broker_research.py and
i18n/translate_comparison_editorial.py.

Flattens each comparison's structured content (sections and FAQs) into
a flat {id: text} map, translates+reviews each item, then reassembles
the same structure per language. Preserves every regulator name,
currency figure, and number exactly (enforced via the glossary's
DO_NOT_TRANSLATE list plus explicit instructions in the prompt below)
-- these are sourced facts (compensation caps, counts from the live
broker dataset), not prose to adapt.

Runs for all 7 LANGUAGES even though only es/de/ar are live today
(GLOBALFXHUB_LIVE_LANGUAGES) -- same precedent as the rest of this
theme's hardcoded translation tables.

Reusable across comparisons: add a new key to COMPARISONS below as
each new regulator pairing is drafted, rather than writing a new
script per pairing. Prints {comparison_key: {lang: {id: translated_text}}}
as JSON to stdout for manual insertion into
inc/regulation-comparisons.php's globalfxhub_regulation_comparison_translations()
-- there's no WordPress REST write to make here, same as the other
hand-curated-content translation scripts in this directory.

Usage: python3 i18n/translate_regulation_comparisons.py [--keys fca-vs-cysec,...]
"""
import argparse
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt

# Mirrors inc/regulation-comparisons.php's globalfxhub_get_regulation_comparisons()
# entries exactly -- keep these two in sync if the English content ever
# changes. Flattened the same way as that function's own translation
# overlay expects: title/meta_title/meta_description/intro, then
# section_N_heading/section_N_body per section, then faq_N_q/faq_N_a
# per FAQ.
COMPARISONS = {
    "fca-vs-cysec": {
        "title": "FCA vs CySEC: What's the Difference for Forex Traders?",
        "meta_title": "FCA vs CySEC (2026): Which Forex Regulator Protects Traders Better?",
        "meta_description": "FCA and CySEC compared on investor compensation, market access, and leverage rules -- using our regulation knowledge base, not marketing claims. See which actually matters more for your situation.",
        "intro": "FCA and CySEC are the two Tier-1 regulators that show up most often across the brokers we've researched -- 33 of the 139 brokers in our dataset hold both licences at once, usually through separate UK and EU entities under the same brand. They're often treated as interchangeable \"proper regulation,\" but they differ in ways that matter if you're actually choosing between two entities of the same broker, or deciding whether a broker's EU-only or UK-only credential is enough for you.",
        "section_1_heading": "£85,000 vs €20,000: investor compensation",
        "section_1_body": "An FCA-authorised firm's eligible retail clients are covered by the Financial Services Compensation Scheme (FSCS) up to £85,000 per person if the firm fails. A CySEC-licensed firm's eligible retail clients are covered by the Investor Compensation Fund (ICF) up to €20,000 per person instead. Both are real, statutory schemes with a hard cap -- neither is marketing language -- but they're denominated in different currencies, set at very different levels, and each applies only to the specific entity licensed by that regulator, never to an unrelated sister brand that merely shares a brand name.",
        "section_2_heading": "Market access: UK-only vs EU-wide passporting",
        "section_2_body": "Since Brexit, an FCA licence covers UK clients only -- it no longer carries automatic EU passporting. A CySEC licence, by contrast, lets a Cyprus Investment Firm passport its services across the entire EU/EEA under MiFID II with that one licence, without needing a separate authorisation in every member state. That's why a broker serving both UK and EU clients typically runs two separately-licensed, separately-capitalised entities rather than one licence covering both regions.",
        "section_3_heading": "Leverage and conduct rules",
        "section_3_body": "Both regulators apply similar ESMA-style retail leverage caps -- typically 30:1 on major currency pairs, down to 2:1 on crypto CFDs -- since the UK retained these rules after Brexit rather than diverging from them. Where they differ more is conduct: the FCA's Consumer Duty rules require firms to actively deliver good outcomes for retail customers, not merely avoid harming them, while CySEC-licensed firms follow MiFID II's conduct-of-business rules instead. In practice, this doesn't translate into a large difference in how much leverage a retail client is offered, but it is a distinct additional obligation on the FCA side.",
        "section_4_heading": "Which one should you actually care about?",
        "section_4_body": "Whichever entity your own account is actually held with -- not the brand's overall reputation. A UK resident's compensation protection comes from the FCA entity and the FSCS; an EU resident's comes from the CySEC entity and the ICF. If a broker holds both licences, confirm in writing exactly which legal entity your account sits under, then check that entity's specific licence number against the corresponding regulator's own register -- not the brand's general claim to be \"FCA and CySEC regulated.\"",
        "section_5_heading": "How common is dual FCA/CySEC regulation?",
        "section_5_body": "Of the 139 brokers in our research set, 95 hold a CySEC licence, 53 hold an FCA licence, and 33 hold both at once -- typically via separate UK and EU entities rather than one licence covering both regions. 24 of the 139 hold neither as a primary credential, though many of those still hold another Tier-1 or offshore licence elsewhere in our dataset. Holding both is common among larger, more established brokers, but it never means a single compensation scheme covers every client -- each entity's scheme only ever covers its own entity's clients.",
        "faq_1_q": "Is CySEC regulation as good as FCA regulation?",
        "faq_1_a": "Different, not simply better or worse: CySEC covers eligible clients up to €20,000 under the ICF and carries EU-wide MiFID II passporting, while FCA covers up to £85,000 under the FSCS but applies only to UK clients post-Brexit. Which one offers more relevant protection for you depends on where you live and which entity you're actually signed up with.",
        "faq_2_q": "If a broker has both FCA and CySEC entities, which one protects me?",
        "faq_2_a": "Whichever entity you actually opened your account with -- not the brand as a whole. Confirm in writing from the broker exactly which legal entity holds your account, then check that entity's specific licence number against the corresponding regulator's own register.",
        "faq_3_q": "How many brokers hold both an FCA and a CySEC licence?",
        "faq_3_a": "33 of the 139 brokers in our research set hold both at once, typically through separate UK and EU entities under one brand -- see the full breakdown above.",
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

This is a head-to-head regulator comparison for a forex/CFD broker research
site, comparing two specific, named financial regulators (the FCA and
CySEC). Every number, regulator/scheme name, and currency figure (£85,000,
€20,000, 30:1, 2:1, 95, 53, 33, 139, 24, FCA, CySEC, FSCS, ICF, MiFID II,
ESMA, etc.) is a sourced fact -- preserve every one of them EXACTLY, never
translating, rounding, or reordering them. Translate the surrounding prose
naturally.

Translate each item below by id, preserving the JSON structure:
{items}"""

REVIEW_USER_TEMPLATE = """\
Target language: {lang}

Review this machine translation of a regulator-comparison research article
against the English original. Fix grammar/register, but do not change,
soften, or strengthen any claim, and do not alter any number, regulator/
scheme name, or currency figure.

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
