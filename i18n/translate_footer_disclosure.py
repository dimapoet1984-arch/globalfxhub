#!/usr/bin/env python3
"""
One-off: translates footer_disclosure_full (the advertiser-disclosure
and risk-warning paragraph in inc/translatable-strings.php) through
the real Claude translate+review pipeline, not a quick hand
translation -- deliberately excluded from the hand-translated table
this session added for every other nav/footer/hero/methodology
string, since this is financial risk-warning/compliance text where
getting the phrasing wrong in a target language carries real weight.

Plain text, not HTML (confirmed: the English source has no markup,
just an en-dash and quotes), so this skips Track A's
splitter/markup-check machinery entirely and just runs the same
translate -> review escalation translate_common.py's role prompts
already describe, via a minimal {"text": string} schema.

Prints each language's translated text to stdout -- this storage
(inc/translatable-strings.php's hardcoded globalfxhub_live_string_translations()
table) isn't a WordPress REST write Track A/B's scripts make, so
there's nothing to POST here. The reviewer inserts the printed text by
hand into that table, same as every other hardcoded translation this
session added.

Usage: python3 i18n/translate_footer_disclosure.py
"""
import json
import os
import sys

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
import translate_common as tc
from glossary import LANGUAGES, glossary_block_for_prompt

SOURCE_TEXT = (
    "Advertiser disclosure & risk warning: Broker names, regulatory licence numbers "
    "(CySEC, the UK's FCA, Seychelles FSA, and others), and trading conditions referenced "
    "on this site describe real, independently operating companies. Entity and pricing "
    "figures were originally compiled March 2026; regulatory status and broker-specific "
    "disclosures were most recently reviewed October 2026, with another full review "
    "scheduled January 2027 -- see each broker's own review page for its specific dates -- "
    "and figures change, so verify directly with the broker and on the relevant regulator's "
    "register before you rely on them. Scores are calculated using a disclosed methodology, "
    "not hands-on account testing. GlobalFXHub does not currently have referral or "
    "advertising relationships with any broker listed; if that changes, this section will "
    "disclose it. CFDs are complex instruments carrying a high risk of losing money rapidly "
    "due to leverage -- most retail investor accounts lose money trading CFDs. This is not "
    "financial advice."
)

TEXT_SCHEMA = {
    "type": "object",
    "properties": {"text": {"type": "string"}},
    "required": ["text"],
    "additionalProperties": False,
}

TRANSLATE_USER_TEMPLATE = """\
Target language: {lang}

This is a financial advertiser-disclosure and risk-warning paragraph for a forex/CFD
broker comparison site's footer. Accuracy of meaning matters more than usual here --
do not soften, strengthen, or drop any specific claim (dates, the fact that scores are
disclosed-methodology not hands-on testing, the no-referral-relationship statement, the
CFD risk warning). Translate it faithfully into {lang}:

{text}"""

REVIEW_USER_TEMPLATE = """\
Target language: {lang}

Review this machine translation of a financial advertiser-disclosure and risk-warning
paragraph against the English original. Fix grammar/register, but do not change,
soften, or strengthen any specific claim (dates, the disclosed-methodology-not-testing
statement, the no-referral-relationship statement, the CFD risk warning).

English original:
{source}

Machine translation to review:
{translation}"""


def translate_and_review(client, lang):
    system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.TRANSLATE_ROLE_INSTRUCTIONS)
    parsed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, system,
        TRANSLATE_USER_TEMPLATE.format(lang=lang, text=SOURCE_TEXT),
        TEXT_SCHEMA, tc.TRANSLATE_EFFORT,
    )
    draft = parsed["text"]

    review_system = tc.build_system_prompt(glossary_block_for_prompt(lang), tc.REVIEW_ROLE_INSTRUCTIONS)
    reviewed, _ = tc.structured_request(
        client, tc.HAIKU_MODEL, review_system,
        REVIEW_USER_TEMPLATE.format(lang=lang, source=SOURCE_TEXT, translation=draft),
        TEXT_SCHEMA, tc.REVIEW_EFFORT,
    )
    return reviewed["text"]


def main():
    client = tc.get_client()
    results = {}
    for lang in LANGUAGES:
        print(f"=== {lang} ===", file=sys.stderr)
        results[lang] = translate_and_review(client, lang)
        print(results[lang], file=sys.stderr)
        print(file=sys.stderr)

    print(json.dumps(results, ensure_ascii=False, indent=2))


if __name__ == "__main__":
    main()
