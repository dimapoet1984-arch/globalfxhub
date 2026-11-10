<?php
/**
 * Head-to-head regulator comparisons at /regulation/{slug}/ -- same
 * URL space and query var ('regpage') as inc/regulation.php's single-
 * regulator knowledge-base pages, not a new rewrite rule. Distinct
 * from those pages: each single-regulator page (e.g. /regulation/fca/)
 * explains ONE regulator on its own; this compares TWO head-to-head
 * (compensation caps, market access, leverage/conduct rules), the
 * genuinely new angle neither a single-regulator page nor either
 * regulator's Learn-cluster explainer (fca-forex-regulation-explained,
 * cysec-forex-regulation-explained) already covers -- same reasoning
 * that kept the Pepperstone vs IC Markets editorial comparison from
 * duplicating either broker's own review.
 *
 * Every fact restated here (compensation caps, passporting, register
 * URLs) already exists in inc/regulation.php's single-regulator pages
 * -- nothing new is sourced for this comparison, only synthesized.
 *
 * Structured and flattened for translation exactly like
 * inc/broker-comparison-editorial.php and
 * inc/broker-research-articles.php (section_N_heading/body,
 * faq_N_q/faq_N_a) -- unlike inc/regulation.php's existing 5 pages,
 * which store 'content' as one opaque English-only HTML blob with no
 * translation layer at all. Deliberately not retrofitting those 5
 * existing pages to this structure right now -- that's a bigger,
 * separate optimization-queue change, not part of adding this one new
 * comparison.
 *
 * Added one regulator pair at a time, same precedent as the broker
 * comparisons/research articles -- never a template mechanically
 * applied to every regulator pairing.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_get_regulation_comparisons() {
    return array(
        'fca-vs-cysec' => array(
            'title'            => "FCA vs CySEC: What's the Difference for Forex Traders?",
            'meta_title'       => 'FCA vs CySEC (2026): Which Forex Regulator Protects Traders Better?',
            'meta_description' => 'FCA and CySEC compared on investor compensation, market access, and leverage rules -- using our regulation knowledge base, not marketing claims. See which actually matters more for your situation.',
            'intro'            => "FCA and CySEC are the two Tier-1 regulators that show up most often across the brokers we've researched -- 33 of the 139 brokers in our dataset hold both licences at once, usually through separate UK and EU entities under the same brand. They're often treated as interchangeable \"proper regulation,\" but they differ in ways that matter if you're actually choosing between two entities of the same broker, or deciding whether a broker's EU-only or UK-only credential is enough for you.",
            'sections' => array(
                array(
                    'heading' => '£85,000 vs €20,000: investor compensation',
                    'body'    => "An FCA-authorised firm's eligible retail clients are covered by the Financial Services Compensation Scheme (FSCS) up to £85,000 per person if the firm fails. A CySEC-licensed firm's eligible retail clients are covered by the Investor Compensation Fund (ICF) up to €20,000 per person instead. Both are real, statutory schemes with a hard cap -- neither is marketing language -- but they're denominated in different currencies, set at very different levels, and each applies only to the specific entity licensed by that regulator, never to an unrelated sister brand that merely shares a brand name.",
                ),
                array(
                    'heading' => 'Market access: UK-only vs EU-wide passporting',
                    'body'    => "Since Brexit, an FCA licence covers UK clients only -- it no longer carries automatic EU passporting. A CySEC licence, by contrast, lets a Cyprus Investment Firm passport its services across the entire EU/EEA under MiFID II with that one licence, without needing a separate authorisation in every member state. That's why a broker serving both UK and EU clients typically runs two separately-licensed, separately-capitalised entities rather than one licence covering both regions.",
                ),
                array(
                    'heading' => 'Leverage and conduct rules',
                    'body'    => "Both regulators apply similar ESMA-style retail leverage caps -- typically 30:1 on major currency pairs, down to 2:1 on crypto CFDs -- since the UK retained these rules after Brexit rather than diverging from them. Where they differ more is conduct: the FCA's Consumer Duty rules require firms to actively deliver good outcomes for retail customers, not merely avoid harming them, while CySEC-licensed firms follow MiFID II's conduct-of-business rules instead. In practice, this doesn't translate into a large difference in how much leverage a retail client is offered, but it is a distinct additional obligation on the FCA side.",
                ),
                array(
                    'heading' => 'Which one should you actually care about?',
                    'body'    => "Whichever entity your own account is actually held with -- not the brand's overall reputation. A UK resident's compensation protection comes from the FCA entity and the FSCS; an EU resident's comes from the CySEC entity and the ICF. If a broker holds both licences, confirm in writing exactly which legal entity your account sits under, then check that entity's specific licence number against the corresponding regulator's own register -- not the brand's general claim to be \"FCA and CySEC regulated.\"",
                ),
                array(
                    'heading' => 'How common is dual FCA/CySEC regulation?',
                    'body'    => "Of the 139 brokers in our research set, 95 hold a CySEC licence, 53 hold an FCA licence, and 33 hold both at once -- typically via separate UK and EU entities rather than one licence covering both regions. 24 of the 139 hold neither as a primary credential, though many of those still hold another Tier-1 or offshore licence elsewhere in our dataset. Holding both is common among larger, more established brokers, but it never means a single compensation scheme covers every client -- each entity's scheme only ever covers its own entity's clients.",
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Is CySEC regulation as good as FCA regulation?',
                    'a' => "Different, not simply better or worse: CySEC covers eligible clients up to €20,000 under the ICF and carries EU-wide MiFID II passporting, while FCA covers up to £85,000 under the FSCS but applies only to UK clients post-Brexit. Which one offers more relevant protection for you depends on where you live and which entity you're actually signed up with.",
                ),
                array(
                    'q' => 'If a broker has both FCA and CySEC entities, which one protects me?',
                    'a' => "Whichever entity you actually opened your account with -- not the brand as a whole. Confirm in writing from the broker exactly which legal entity holds your account, then check that entity's specific licence number against the corresponding regulator's own register.",
                ),
                array(
                    'q' => 'How many brokers hold both an FCA and a CySEC licence?',
                    'a' => '33 of the 139 brokers in our research set hold both at once, typically through separate UK and EU entities under one brand -- see the full breakdown above.',
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, same shape/purpose as
 * globalfxhub_editorial_comparison_translations() and
 * globalfxhub_broker_research_translations() -- filled in once
 * i18n/translate_regulation_comparisons.py's real translate+review
 * run completes for a given key.
 */
function globalfxhub_regulation_comparison_translations() {
    return array(
        'fca-vs-cysec' => array(
            // Filled in once i18n/translate_regulation_comparisons.py's
            // real translate+review run completes.
        ),
    );
}

/**
 * Null if the slug isn't a drafted comparison -- callers fall through
 * to inc/regulation.php's single-regulator lookup, then to the plain
 * /regulation/ index, exactly like globalfxhub_vs_pair()'s null-then-404
 * convention elsewhere in this theme.
 */
function globalfxhub_get_regulation_comparison( $slug ) {
    $comparisons = globalfxhub_get_regulation_comparisons();
    return $comparisons[ $slug ] ?? null;
}

function globalfxhub_get_localized_regulation_comparison( $slug ) {
    $entry = globalfxhub_get_regulation_comparison( $slug );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_regulation_comparison_translations();
    $flat = $translations[ $slug ][ $lang ] ?? array();
    if ( empty( $flat ) ) {
        return $entry;
    }
    $get = function( $id, $fallback ) use ( $flat ) {
        return $flat[ $id ] ?? $fallback;
    };

    $entry['title']            = $get( 'title', $entry['title'] );
    $entry['meta_title']       = $get( 'meta_title', $entry['meta_title'] );
    $entry['meta_description'] = $get( 'meta_description', $entry['meta_description'] );
    $entry['intro']            = $get( 'intro', $entry['intro'] );

    $entry['sections'] = array_values( array_map(
        function( $i, $section ) use ( $get ) {
            $n = $i + 1;
            return array(
                'heading' => $get( "section_{$n}_heading", $section['heading'] ),
                'body'    => $get( "section_{$n}_body", $section['body'] ),
            );
        },
        array_keys( $entry['sections'] ), $entry['sections']
    ) );

    $entry['faqs'] = array_values( array_map(
        function( $i, $faq ) use ( $get ) {
            $n = $i + 1;
            return array(
                'q' => $get( "faq_{$n}_q", $faq['q'] ),
                'a' => $get( "faq_{$n}_a", $faq['a'] ),
            );
        },
        array_keys( $entry['faqs'] ), $entry['faqs']
    ) );

    return $entry;
}

function globalfxhub_regulation_comparison_seo_title( $title_parts ) {
    $slug = get_query_var( 'regpage' );
    if ( $slug ) {
        $entry = globalfxhub_get_localized_regulation_comparison( $slug );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_regulation_comparison_seo_title' );

function globalfxhub_regulation_comparison_seo_head() {
    $slug = get_query_var( 'regpage' );
    if ( ! $slug ) {
        return;
    }
    $entry = globalfxhub_get_localized_regulation_comparison( $slug );
    if ( ! $entry ) {
        return;
    }
    echo '<meta name="description" content="' . esc_attr( $entry['meta_description'] ) . '">' . "\n";

    if ( ! empty( $entry['faqs'] ) ) {
        $faq_schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map( function( $faq ) {
                return array(
                    '@type'          => 'Question',
                    'name'           => $faq['q'],
                    'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $faq['a'] ),
                );
            }, $entry['faqs'] ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_regulation_comparison_seo_head', 5 );
