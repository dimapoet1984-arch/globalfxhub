<?php
/**
 * Original data reports at /research/{slug}/ -- the "original
 * research/data (unique, citable reports built from this site's own
 * broker dataset)" 15% content bucket from CLAUDE.md's 2026-11-09
 * content strategy, explicitly called out there as worth
 * disproportionate effort since these are the assets other sites
 * cite.
 *
 * New top-level section (like /learn/, /best/, /regulation/), not
 * nested under an existing one, because unlike this week's two prior
 * additions (/reviews/{slug}/fees/ reusing the review's own URL
 * space, /regulation/fca-vs-cysec/ reusing the regulator knowledge
 * base's URL space) a cross-broker statistical report isn't about any
 * one broker or regulator -- there's no existing page whose URL space
 * it belongs under. The primary nav's "Research" dropdown
 * (inc/i18n-nav-menu.php's globalfxhub_primary_nav_label_map(),
 * already translated into all 7 languages as 'nav_research') already
 * anticipates exactly this section; it had no link target until now.
 *
 * Every figure in this report is computed directly from the live
 * globalfxhub_get_brokers() dataset (see the verification harness run
 * before this content was written) and frozen into hand-written prose
 * at authoring time -- same precedent as every other hand-curated
 * editorial piece on this site. Re-verify and refresh the numbers
 * whenever broker platform data changes materially.
 *
 * Added one report at a time, same precedent as the broker/regulation
 * comparisons this week -- never a template mechanically generated
 * for every possible cross-section of the dataset.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_research_reports_rewrite_rules() {
    add_rewrite_rule( '^research/([^/]+)/?$', 'index.php?pagename=research&report=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_research_reports_rewrite_rules' );

function globalfxhub_research_reports_query_vars( $vars ) {
    $vars[] = 'report';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_research_reports_query_vars' );

function globalfxhub_get_research_reports() {
    return array(
        'forex-trading-platform-adoption-report' => array(
            'title'            => 'Forex Trading Platform Adoption Report',
            'meta_title'       => 'Forex Trading Platform Adoption Report (2026): MT4, MT5, cTrader and TradingView Across 139 Brokers',
            'meta_description' => 'How many forex brokers still support MT4, how many have moved to MT5, and how rare is cTrader or TradingView support -- an original data study across our full 139-broker research set.',
            'intro'            => "Which trading platforms a broker actually supports rarely gets the same scrutiny as its spread or its regulator -- so we pulled it directly from our own 139-broker research set and counted it. This report covers every confirmed platform across that full set: how common MT4 and MT5 still are, how many brokers run them side by side versus having moved on, and how rare cTrader and TradingView support actually is. Every figure below comes from the same platforms field used throughout this site's reviews, best-broker lists, and comparisons -- nothing here is a separate, newly-sourced claim.",
            'sections' => array(
                array(
                    'heading' => 'MT4 is still the most common platform, but MT5 has nearly caught up',
                    'body'    => "103 of the 139 brokers in our research set (74%) are confirmed to support MT4, and 97 (70%) are confirmed to support MT5. Despite MetaTrader 4 being the older of the two platforms, and despite MetaQuotes having stopped actively developing it in favor of MT5, it remains the more widely supported platform by a modest margin -- brokers have not abandoned MT4 at anywhere near the rate its \"legacy platform\" reputation might suggest.",
                ),
                array(
                    'heading' => 'Most brokers run MT4 and MT5 side by side -- few have actually dropped MT4',
                    'body'    => "84 of the 139 brokers support both MT4 and MT5 at once -- by far the most common setup. Only 13 of the 97 brokers that support MT5 (13%) have dropped MT4 entirely; the other 87% keep both running in parallel. 19 brokers still offer MT4 without having added MT5 at all, and 23 brokers (17% of the full set) offer neither platform, relying instead on cTrader, TradingView, or a proprietary platform of their own.",
                ),
                array(
                    'heading' => 'cTrader and TradingView remain minority platforms',
                    'body'    => "18 of the 139 brokers (13%) are confirmed to support cTrader, and 16 (12%) are confirmed to support TradingView -- both still a clear minority next to MT4/MT5, though far from negligible. The two overlap more than they compete: 7 brokers support both cTrader and TradingView, and only 1 of the 16 TradingView-supporting brokers does so without also offering MT4, meaning TradingView support is almost always additive to a broker's existing MetaTrader offering rather than a replacement for it.",
                ),
                array(
                    'heading' => 'TradingView support correlates strongly with our top-ranked brokers',
                    'body'    => "Splitting the dataset at our top 40 by overall rank shows a real gap: 30% of our top-40 brokers (12 of 40) support TradingView, against just 4% of the remaining 99 (4 brokers). MT4 shows a smaller version of the same pattern -- 83% of the top 40 support it, against 71% of the rest. This is a correlation, not a causal claim: supporting TradingView doesn't itself make a broker better-regulated or cheaper, and our ranking formula doesn't reward platform count directly (platforms is a flat function of how many platforms are confirmed, weighted at 10% of the overall score) -- but the two clearly tend to travel together in this dataset.",
                ),
                array(
                    'heading' => 'The five brokers confirmed to support all four major platforms',
                    'body'    => "Only 5 of the 139 brokers in our research set -- Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital -- are confirmed to support all four of MT4, MT5, cTrader, and TradingView at once. That's a genuinely rare combination: offering every major third-party platform means supporting four separate pieces of trading infrastructure rather than concentrating on one or two, and all five of these brokers happen to also run an ECN/no-dealing-desk-style execution model, though our dataset doesn't let us say whether that's a cause, a correlation, or a coincidence at this sample size.",
                ),
                array(
                    'heading' => 'Proprietary platforms are common individually, but fragmented as a category',
                    'body'    => "Beyond the big four, 22 of the 139 brokers confirm exactly one platform, and 5 of those are MT4-only. The rest of the dataset is a long tail of broker-specific proprietary platforms -- each one typically confirmed for exactly one broker, with no single proprietary platform used by more than a handful of brokers -- so \"proprietary\" is common as a category but fragmented in practice, unlike the real cross-broker standardisation MT4, MT5, cTrader, and TradingView each represent. 12 of the 139 brokers have no platform confirmed in our research at all; we're treating that as an honest data gap rather than guessing, consistent with how every other unconfirmed field on this site is handled.",
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Is MT4 being phased out by forex brokers?',
                    'a' => 'Not yet, at least not among the brokers we\'ve researched: 74% still support it, and 87% of the brokers that have added MT5 keep MT4 running alongside it rather than dropping it. Only 13% of MT5-supporting brokers in our dataset have moved on from MT4 entirely.',
                ),
                array(
                    'q' => 'Which forex brokers support MT4, MT5, cTrader, and TradingView?',
                    'a' => 'Five brokers in our 139-broker research set are confirmed to support all four: Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital.',
                ),
                array(
                    'q' => 'Does TradingView support mean a broker is better?',
                    'a' => "Not directly -- our scoring formula doesn't reward platform count on its own. But there's a real correlation in our data: 30% of our top-40-ranked brokers support TradingView, against just 4% of the remaining 99, so platform breadth and overall ranking clearly tend to move together even though one doesn't mechanically cause the other.",
                ),
                array(
                    'q' => 'How was this data collected?',
                    'a' => "From the platforms field already used throughout this site's broker reviews, best-broker lists, and comparisons -- the same 139-broker research set, not a separate survey. Where we couldn't independently confirm a broker's platform support, we counted it as unconfirmed rather than assuming either way; 12 of the 139 brokers fall into that category.",
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, same shape/purpose as the other two
 * hand-curated-content translation overlays added this week --
 * filled in once i18n/translate_research_reports.py's real
 * translate+review run completes for a given key.
 */
function globalfxhub_research_report_translations() {
    return array(
        'forex-trading-platform-adoption-report' => array(
            // Filled in once i18n/translate_research_reports.py's real
            // translate+review run completes.
        ),
    );
}

function globalfxhub_get_research_report( $slug ) {
    $reports = globalfxhub_get_research_reports();
    return $reports[ $slug ] ?? null;
}

function globalfxhub_get_localized_research_report( $slug ) {
    $entry = globalfxhub_get_research_report( $slug );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_research_report_translations();
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

function globalfxhub_research_reports_seo_title( $title_parts ) {
    $slug = get_query_var( 'report' );
    if ( $slug ) {
        $entry = globalfxhub_get_localized_research_report( $slug );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    } elseif ( is_page( 'research' ) ) {
        $title_parts['title'] = 'Original Forex Broker Research ' . date( 'Y' ) . ': Data Studies and Reports';
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_research_reports_seo_title' );

function globalfxhub_research_reports_canonical( $canonical_url ) {
    $slug = get_query_var( 'report' );
    if ( $slug && globalfxhub_get_research_report( $slug ) ) {
        return home_url( '/research/' . $slug . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_research_reports_canonical' );

function globalfxhub_research_reports_seo_head() {
    $slug = get_query_var( 'report' );
    if ( ! $slug ) {
        if ( is_page( 'research' ) ) {
            echo '<meta name="description" content="' . esc_attr( 'Original data studies and reports built directly from our own 139-broker research set -- platform adoption, regulation, fees, and more, computed from the same dataset behind every review and ranking on this site.' ) . '">' . "\n";
        }
        return;
    }
    $entry = globalfxhub_get_localized_research_report( $slug );
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
add_action( 'wp_head', 'globalfxhub_research_reports_seo_head', 5 );
