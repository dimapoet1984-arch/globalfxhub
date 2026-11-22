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
        "intro": "FCA and CySEC are the two Tier-1 regulators that show up most often across the brokers we've researched -- 23 of the 139 brokers in our dataset hold both licences at once, usually through separate UK and EU entities under the same brand. They're often treated as interchangeable \"proper regulation,\" but they differ in ways that matter if you're actually choosing between two entities of the same broker, or deciding whether a broker's EU-only or UK-only credential is enough for you.",
        "section_1_heading": "£85,000 vs €20,000: investor compensation",
        "section_1_body": "An FCA-authorised firm's eligible retail clients are covered by the Financial Services Compensation Scheme (FSCS) up to £85,000 per person if the firm fails. A CySEC-licensed firm's eligible retail clients are covered by the Investor Compensation Fund (ICF) up to €20,000 per person instead. Both are real, statutory schemes with a hard cap -- neither is marketing language -- but they're denominated in different currencies, set at very different levels, and each applies only to the specific entity licensed by that regulator, never to an unrelated sister brand that merely shares a brand name.",
        "section_2_heading": "Market access: UK-only vs EU-wide passporting",
        "section_2_body": "Since Brexit, an FCA licence covers UK clients only -- it no longer carries automatic EU passporting. A CySEC licence, by contrast, lets a Cyprus Investment Firm passport its services across the entire EU/EEA under MiFID II with that one licence, without needing a separate authorisation in every member state. That's why a broker serving both UK and EU clients typically runs two separately-licensed, separately-capitalised entities rather than one licence covering both regions.",
        "section_3_heading": "Leverage and conduct rules",
        "section_3_body": "Both regulators apply similar ESMA-style retail leverage caps -- typically 30:1 on major currency pairs, down to 2:1 on crypto CFDs -- since the UK retained these rules after Brexit rather than diverging from them. Where they differ more is conduct: the FCA's Consumer Duty rules require firms to actively deliver good outcomes for retail customers, not merely avoid harming them, while CySEC-licensed firms follow MiFID II's conduct-of-business rules instead. In practice, this doesn't translate into a large difference in how much leverage a retail client is offered, but it is a distinct additional obligation on the FCA side.",
        "section_4_heading": "Which one should you actually care about?",
        "section_4_body": "Whichever entity your own account is actually held with -- not the brand's overall reputation. A UK resident's compensation protection comes from the FCA entity and the FSCS; an EU resident's comes from the CySEC entity and the ICF. If a broker holds both licences, confirm in writing exactly which legal entity your account sits under, then check that entity's specific licence number against the corresponding regulator's own register -- not the brand's general claim to be \"FCA and CySEC regulated.\"",
        "section_5_heading": "How common is dual FCA/CySEC regulation?",
        "section_5_body": "Of the 139 brokers in our research set, 93 hold a CySEC licence, 43 hold an FCA licence, and 23 hold both at once -- typically via separate UK and EU entities rather than one licence covering both regions. 26 of the 139 hold neither as a primary credential, though many of those still hold another Tier-1 or offshore licence elsewhere in our dataset. Holding both is common among larger, more established brokers, but it never means a single compensation scheme covers every client -- each entity's scheme only ever covers its own entity's clients.",
        "faq_1_q": "Is CySEC regulation as good as FCA regulation?",
        "faq_1_a": "Different, not simply better or worse: CySEC covers eligible clients up to €20,000 under the ICF and carries EU-wide MiFID II passporting, while FCA covers up to £85,000 under the FSCS but applies only to UK clients post-Brexit. Which one offers more relevant protection for you depends on where you live and which entity you're actually signed up with.",
        "faq_2_q": "If a broker has both FCA and CySEC entities, which one protects me?",
        "faq_2_a": "Whichever entity you actually opened your account with -- not the brand as a whole. Confirm in writing from the broker exactly which legal entity holds your account, then check that entity's specific licence number against the corresponding regulator's own register.",
        "faq_3_q": "How many brokers hold both an FCA and a CySEC licence?",
        "faq_3_a": "23 of the 139 brokers in our research set hold both at once, typically through separate UK and EU entities under one brand -- see the full breakdown above.",
    },
    "asic-vs-fca": {
        "title": "ASIC vs FCA: What's the Difference for Forex Traders?",
        "meta_title": "ASIC vs FCA (2026): Which Forex Regulator Protects Traders Better?",
        "meta_description": "ASIC and FCA compared on investor compensation, leverage rules, and complaint handling -- using our regulation knowledge base, not marketing claims. See which actually matters more for your situation.",
        "intro": "ASIC and FCA are two of the Tier-1 regulators that show up most often across the brokers we've researched -- 14 of the 139 brokers in our dataset hold both at once, typically through separate UK and Australian entities under the same brand. Both carry real regulatory weight, but they diverge sharply on one point that matters a lot if something actually goes wrong: what happens if the broker itself can't pay you back.",
        "section_1_heading": "£85,000 vs an unclear backstop: investor compensation",
        "section_1_body": "An FCA-authorised firm's eligible retail clients are covered by the Financial Services Compensation Scheme (FSCS) up to £85,000 per person if the firm fails -- a real, statutory, clearly-capped scheme. ASIC's equivalent is murkier: Australia's Compensation Scheme of Last Resort (CSLR) can pay unpaid determinations from the Australian Financial Complaints Authority (AFCA) up to AU$150,000, but its confirmed scope is personal financial advice, securities dealing for retail clients, and credit -- whether a forex/CFD broker dispute actually counts as \"securities dealing\" under the scheme isn't clearly confirmed from public guidance. This is the single biggest practical difference between the two: FCA gives you a clear number and clear scope, ASIC doesn't (yet) give you the same certainty for this specific industry.",
        "section_2_heading": "Making a complaint: a clearer parallel",
        "section_2_body": "Both regulators give you a genuine, independent complaints route, and here they're more alike than compensation makes them look. An FCA-authorised firm's unresolved complaint goes to the Financial Ombudsman Service after 8 weeks, with binding decisions. An ASIC-licensed firm's complaint goes to AFCA directly, whose determinations are also binding on the firm -- AFCA itself does handle forex/CFD complaints; it's specifically the CSLR backstop fund (what pays out if the firm can't pay an AFCA determination) whose scope for this industry is the uncertain part, not AFCA's willingness to hear the complaint in the first place.",
        "section_3_heading": "Leverage and conduct rules",
        "section_3_body": "Both regulators cap retail leverage in the same broad range, but ASIC's rule is more granular. ASIC's Product Intervention Order (in force since 29 March 2021, extended to 23 May 2027) sets 30:1 on major FX pairs, 20:1 on minor FX pairs/gold/major indices, 10:1 on other commodities/minor indices, 5:1 on shares, and 2:1 on crypto-assets, plus mandatory negative balance protection. FCA retained the same ESMA-style caps post-Brexit -- 30:1 on major FX pairs down to 2:1 on crypto CFDs -- a simpler set of tiers covering similar ground. Where FCA adds something ASIC doesn't explicitly match: its Consumer Duty rules require firms to actively deliver good outcomes for retail customers, not merely avoid harming them.",
        "section_4_heading": "Market access: neither one passports elsewhere",
        "section_4_body": "Unlike a CySEC licence, which lets a firm passport across the entire EU/EEA with one authorisation, neither an FCA nor an ASIC licence works that way. An FCA licence covers UK clients only since Brexit. An AFS Licence authorises a firm to provide financial services in Australia; whether it extends to clients elsewhere depends on the specific authorisations that licence covers, not a blanket right the way CySEC's MiFID passport works. That's why a broker serving both markets typically runs two separately-licensed, separately-capitalised entities.",
        "section_5_heading": "How common is dual FCA/ASIC regulation?",
        "section_5_body": "Of the 139 brokers in our research set, 43 hold an FCA licence, 29 hold an ASIC licence, and 14 hold both at once -- usually via separate UK and Australian entities rather than one licence covering both regions. 81 of the 139 hold neither as a primary Tier-1 credential, though many of those still hold another Tier-1 or offshore licence elsewhere in our dataset.",
        "faq_1_q": "Does ASIC regulation include a compensation scheme like the UK's FSCS?",
        "faq_1_a": "Not a clearly-confirmed equivalent. Australia's CSLR can pay unpaid AFCA determinations up to AU$150,000, but whether that covers a forex/CFD broker dispute specifically isn't confirmed from public guidance -- unlike FCA's clearly-capped £85,000 FSCS.",
        "faq_2_q": "Can I complain about an ASIC-regulated broker the way I would with an FCA one?",
        "faq_2_a": "Yes -- AFCA hears the complaint and its determinations are binding, broadly parallel to FCA's Financial Ombudsman Service. The uncertainty is specifically about the CSLR backstop fund if the firm itself can't pay, not about whether AFCA will hear a forex/CFD complaint.",
        "faq_3_q": "How many brokers hold both FCA and ASIC licences?",
        "faq_3_a": "14 of the 139 brokers in our research set hold both at once, typically through separate UK and Australian entities under one brand.",
    },
    "fsca-vs-cysec": {
        "title": "FSCA vs CySEC: Comparing Two Very Different Regulatory Regimes",
        "meta_title": "FSCA vs CySEC (2026): Comparing Two Very Different Regulatory Regimes",
        "meta_description": "CySEC caps retail leverage and runs a statutory €20,000 compensation fund; South Africa's FSCA does neither, but has its own real (if different) complaint mechanism -- what the difference actually means for a retail trader.",
        "intro": "CySEC and FSCA both show up constantly in broker regulatory footprints, but they're not two flavors of the same thing. One caps retail leverage under EU rules and backs client losses with a statutory compensation fund; the other doesn't cap leverage at all and has no equivalent fund, but does run a real, independent complaint process with meaningful awards. This breaks down what each actually covers, using the same researched data we score brokers against across our full 139-broker dataset.",
        "section_1_heading": "The core difference: CySEC caps leverage and runs a statutory compensation fund; FSCA does neither",
        "section_1_body": "CySEC is a full EU/MiFID II-passported regulator: retail leverage is capped under ESMA-aligned rules (up to 30:1 on major FX pairs, down to 2:1 on crypto CFDs), and if a CySEC-licensed firm fails, the Investor Compensation Fund covers eligible retail clients up to €20,000 per person -- a real, statutory, guaranteed-cap scheme. FSCA, South Africa's regulator, has no confirmed leverage cap in its public guidance (its Joint Standard 2 of 2020 covers margin for non-centrally-cleared OTC derivatives, a different mechanism from a retail leverage ceiling) -- FSCA-regulated brokers commonly advertise leverage of 1:500 or higher. FSCA also runs no statutory investor-compensation scheme at all.",
        "section_2_heading": "FSCA does have a real complaint mechanism -- just not a guaranteed fund",
        "section_2_body": "\"No compensation scheme\" doesn't mean \"no recourse.\" A client disputes an FSCA-licensed firm directly first; if unresolved, the FAIS Ombud -- an independent, free dispute-resolution body -- can investigate and issue a binding determination, escalatable to the Financial Services Tribunal. For complaints received on or after 1 July 2024, the Ombud can award up to R3.5 million (roughly $190,000), up from an R800,000 cap that applied to older complaints. That's a real, case-by-case remedy -- but it depends on the Ombud ruling in your favor and the firm actually being able to pay, unlike CySEC's ICF, which is a guaranteed fund that pays out automatically up to its cap once a firm fails.",
        "section_3_heading": "In practice, FSCA is almost always a second licence, not a substitute for CySEC",
        "section_3_body": "Of the 33 brokers in our 139-broker dataset that hold an FSCA licence, 27 (82%) also hold CySEC. Only 6 -- TioMarkets, FXTM, PU Prime, ICM Capital, Eurotrade, and Vantage Markets -- hold FSCA without also holding CySEC. For the large majority of FSCA-regulated brokers we've researched, FSCA functions as an additional, South-Africa-facing licence sitting alongside a primary EU/CySEC relationship, not as an alternative regulatory home.",
        "section_4_heading": "Our dataset: 93 hold CySEC, 33 hold FSCA, 40 hold neither",
        "section_4_body": "Across the full 139-broker set: 93 brokers (67%) hold a CySEC licence, 33 (24%) hold an FSCA licence, 27 hold both, and 40 (29%) hold neither. CySEC is by far the more common licence in our research set -- unsurprising given how many brokers serving EU retail clients need MiFID passporting, while FSCA is more often picked up by brokers specifically building out a South African or broader-Africa client base.",
        "section_5_heading": "What this means if you're choosing between a broker's CySEC entity and its FSCA entity",
        "section_5_body": "Some broker groups operate both a CySEC-licensed EU entity and a separate FSCA-licensed entity under the same brand. Which one you're actually onboarded to can depend on your country of residence, not your own choice -- and the protections genuinely differ: EU/CySEC leverage caps and a guaranteed €20,000 fund on one side, uncapped leverage and a case-by-case ombud process with no guaranteed payout on the other. Always confirm, in writing, which specific legal entity your account sits under and what that entity's actual licence covers, rather than assuming your broker's regulatory reputation transfers evenly across every entity it operates.",
        "faq_1_q": "Does FSCA have an investor compensation scheme like CySEC's ICF?",
        "faq_1_a": "No. CySEC's Investor Compensation Fund guarantees eligible retail clients up to €20,000 if a licensed firm fails. FSCA has no equivalent statutory scheme -- its FAIS Ombud can award compensation case by case (up to R3.5 million since 1 July 2024), but that depends on a ruling in the client's favor and the firm's ability to pay, not a guaranteed fund.",
        "faq_2_q": "Is FSCA a weaker regulator than CySEC?",
        "faq_2_a": "Not simply \"weaker\" -- it's a different model. FSCA has no confirmed retail leverage cap and no compensation fund, both real gaps compared to CySEC's ESMA-aligned caps and ICF. But it does run a genuine, independent, free complaint mechanism (the FAIS Ombud) with binding, sometimes substantial awards -- something not every offshore-only regulator offers.",
        "faq_3_q": "Do most FSCA-regulated brokers also hold a CySEC licence?",
        "faq_3_a": "Yes -- 27 of the 33 FSCA-regulated brokers in our 139-broker dataset (82%) also hold CySEC. Only 6 hold FSCA without CySEC: TioMarkets, FXTM, PU Prime, ICM Capital, Eurotrade, and Vantage Markets.",
    },
    "dfsa-vs-fca": {
        "title": "DFSA vs FCA: Dubai's Regulator Compared to the UK's",
        "meta_title": "DFSA vs FCA (2026): Dubai's Regulator Compared to the UK's",
        "meta_description": "FCA backs client losses with a statutory £85,000 fund and a general-purpose ombudsman; DFSA has neither in the same form -- what the difference actually means for a retail trader choosing between them.",
        "intro": "DFSA and FCA both show up in broker regulatory footprints serving Middle East and UK clients, but they're built on different foundations. FCA runs a general-purpose dispute-resolution body and a statutory compensation fund; DFSA's complaint process is real but narrower, and it has no confirmed compensation scheme at all. This breaks down what each actually covers, using the same researched data we score brokers against across our full 139-broker dataset.",
        "section_1_heading": "The core gap: FCA runs a general-purpose ombudsman and a compensation fund; DFSA does neither in the same form",
        "section_1_body": "An FCA-authorised firm's eligible retail clients are covered by the Financial Services Compensation Scheme (FSCS) up to £85,000 per person if the firm fails, and an unresolved complaint goes to the Financial Ombudsman Service after 8 weeks, with binding decisions on virtually any dispute. DFSA has no confirmed investor-compensation scheme for retail CFD/forex client losses -- its framework centres on licensing, capital, and client-money segregation rather than a standing fund.",
        "section_2_heading": "DFSA's complaint process is real, but much narrower than it might sound",
        "section_2_body": "DFSA-authorised firms must have their own adequate complaint-handling mechanism, and a client disputes the firm directly first -- so far, similar to FCA. The difference is what happens next: DFSA itself will consider a written complaint only about specific matters (integrity and fair dealing, corporate governance failures, or misuse of the DIFC/DFSA name), not as a general-purpose ombudsman for any type of client dispute the way FOS is for FCA-authorised firms.",
        "section_3_heading": "Leverage rules differ in structure -- and the data itself has a real disagreement worth disclosing",
        "section_3_body": "DFSA raised its minimum margin requirements for retail clients effective 6 December 2021: 3.33% margin (30:1 leverage) on major FX pairs, 5% (20:1) on gold, and 10% (10:1) on oil. FCA instead retained simpler ESMA-style tiered caps post-Brexit: 30:1 on major FX pairs down to 2:1 on crypto CFDs. Worth flagging plainly: a 2025 industry report instead cited a 50:1 DFSA retail cap on majors, which conflicts with the 2021 margin rule. We're treating the margin rule as the better-sourced figure since it's confirmed against this site's own UAE country-page research, but we're not papering over the disagreement.",
        "section_4_heading": "Market access: a DFSA licence covers the DIFC free zone, not the wider UAE",
        "section_4_body": "The Dubai Financial Services Authority only licenses firms operating within the Dubai International Financial Centre (DIFC) -- a defined financial free zone, distinct from the UAE's wider federal regulators. An FCA licence covers UK clients only since Brexit, with no automatic passporting elsewhere. Neither licence extends as broadly as a CySEC/MiFID passport does across the EU/EEA.",
        "section_5_heading": "How common is dual DFSA/FCA regulation?",
        "section_5_body": "Of the 139 brokers in our research set, 11 hold a DFSA licence, 53 hold an FCA licence, and 9 hold both at once -- usually through separate entities under one brand. 84 of the 139 hold neither as a primary credential, though many of those still hold another Tier-1 or offshore licence elsewhere in our dataset.",
        "faq_1_q": "Does DFSA have an investor compensation scheme like FCA's FSCS?",
        "faq_1_a": "No confirmed scheme. FCA's FSCS guarantees eligible retail clients up to £85,000 if an authorised firm fails; DFSA's framework has no equivalent standing fund for retail CFD/forex losses.",
        "faq_2_q": "Can I complain about a DFSA-regulated broker the way I would with an FCA one?",
        "faq_2_a": "Only partly. DFSA-authorised firms must handle complaints directly, and DFSA itself will consider specific matters (integrity, governance failures, misuse of the DIFC/DFSA name) -- but it isn't a general-purpose ombudsman for any dispute the way FCA's Financial Ombudsman Service is.",
        "faq_3_q": "What leverage does DFSA allow?",
        "faq_3_a": "Minimum margin requirements effective December 2021 work out to 30:1 on major FX pairs, 20:1 on gold, and 10:1 on oil -- though one 2025 industry report cites a conflicting 50:1 major-pairs figure, which we flag as an unresolved discrepancy rather than picking a side.",
        "faq_4_q": "How many brokers hold both DFSA and FCA licences?",
        "faq_4_a": "9 of the 139 brokers in our research set hold both at once, typically through separate entities under one brand.",
    },
    "seychelles-fsa-vs-cysec": {
        "title": "Seychelles FSA vs CySEC: Is an Offshore Licence Safe Enough?",
        "meta_title": "Seychelles FSA vs CySEC (2026): Is an Offshore Licence Safe Enough?",
        "meta_description": "CySEC caps retail leverage and runs a statutory €20,000 compensation fund with an independent ombudsman; Seychelles FSA has none of the three -- what that gap actually means for a retail trader.",
        "intro": "A large share of the brokers we've researched hold a Seychelles FSA licence -- usually alongside a stronger one, sometimes not. Seychelles FSA is a real regulator, not a rubber stamp, but it's built on a meaningfully thinner foundation than CySEC on every dimension that matters if something goes wrong: leverage limits, compensation, and who you can actually complain to. This breaks down the real gap, using the same researched data we score brokers against across our full 139-broker dataset.",
        "section_1_heading": "The core gap: CySEC caps leverage and runs a statutory fund with an independent ombudsman; Seychelles FSA has none of the three",
        "section_1_body": "CySEC is a full EU/MiFID II-passported regulator: retail leverage is capped under ESMA-aligned rules (up to 30:1 on major FX pairs, down to 2:1 on crypto CFDs), the Investor Compensation Fund covers eligible retail clients up to €20,000 per person if a licensed firm fails, and the Financial Ombudsman of Cyprus can independently hear and resolve client complaints. Seychelles FSA's rules are materially less prescriptive than MiFID or FCA rules -- part of why offshore entities routinely advertise much higher leverage than an EU/UK sister brand under the same name -- and it runs no statutory compensation scheme at all.",
        "section_2_heading": "What \"the FSA can take regulatory action\" actually means for a client dispute",
        "section_2_body": "A client disputes a Seychelles-licensed firm directly with the firm first -- so far similar to any other regulator. But there's no equivalent next step: the Seychelles FSA itself can take regulatory action against a licensed Securities Dealer (sanctions, licence conditions, and so on), but there's no independent ombudsman scheme that hears an individual client's complaint and can award compensation, the way CySEC's Cyprus Ombudsman or even FSCA's FAIS Ombud (in South Africa) can. That's a thinner recourse path than either of those two offshore-adjacent comparisons we've already covered.",
        "section_3_heading": "In practice, Seychelles FSA is usually a second licence, not a substitute for CySEC",
        "section_3_body": "Of the 57 brokers in our 139-broker dataset that hold a Seychelles FSA licence, 44 (77%) also hold CySEC. Only 13 hold Seychelles without CySEC: TioMarkets, BDSwiss, Hantec Markets, Titan FX, PU Prime, VT Markets, TenTrade, GO4REX, ICM Capital, Baxia Markets, CXM Direct, ForexVox, and Ventezo. For the large majority of Seychelles-regulated brokers we've researched, it functions as an additional, offshore-client-facing licence sitting alongside a primary EU/CySEC relationship, not as an alternative regulatory home.",
        "section_4_heading": "Our dataset: 93 hold CySEC, 57 hold Seychelles FSA, 33 hold neither",
        "section_4_body": "Across the full 139-broker set: 93 brokers (67%) hold a CySEC licence, 57 (41%) hold a Seychelles FSA licence, 44 hold both, and 33 (24%) hold neither. Seychelles FSA is the single most common offshore licence in our research set -- more brokers hold it than any other regulator except CySEC itself.",
        "section_5_heading": "What this means if you're choosing between a broker's CySEC entity and its Seychelles entity",
        "section_5_body": "Many broker groups operate both a CySEC-licensed EU entity and a separate Seychelles-licensed offshore entity under the same brand, often assigning clients to one or the other based on country of residence rather than client choice. The protections genuinely differ: EU/CySEC leverage caps, a guaranteed €20,000 fund, and an independent ombudsman on one side; materially higher advertised leverage, no compensation fund, and no independent dispute body on the other. Always confirm, in writing, which specific legal entity your account sits under before assuming your broker's overall reputation applies evenly to every entity it operates.",
        "faq_1_q": "Does Seychelles FSA have an investor compensation scheme like CySEC's ICF?",
        "faq_1_a": "No. CySEC's Investor Compensation Fund guarantees eligible retail clients up to €20,000 if a licensed firm fails. Seychelles FSA has no statutory compensation scheme at all -- not even the case-by-case ombud mechanism that South Africa's FSCA has.",
        "faq_2_q": "Is there an ombudsman for Seychelles FSA-regulated brokers?",
        "faq_2_a": "No independent one. The FSA itself can take regulatory action against a licensed firm, but there's no equivalent to CySEC's Cyprus Ombudsman that hears an individual client's complaint and can award compensation.",
        "faq_3_q": "Do most Seychelles FSA-regulated brokers also hold a CySEC licence?",
        "faq_3_a": "Yes -- 44 of the 57 Seychelles-regulated brokers in our 139-broker dataset (77%) also hold CySEC. Only 13 hold Seychelles without CySEC.",
        "faq_4_q": "Which brokers hold a Seychelles FSA licence without also holding CySEC?",
        "faq_4_a": "13 in our dataset: TioMarkets, BDSwiss, Hantec Markets, Titan FX, PU Prime, VT Markets, TenTrade, GO4REX, ICM Capital, Baxia Markets, CXM Direct, ForexVox, and Ventezo.",
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
site, comparing two specific, named financial regulators. Every number,
percentage, currency figure, and regulator/scheme name appearing in the
text below is a sourced fact -- preserve every one of them EXACTLY, never
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
