<?php
/**
 * Broker-specific "deep dive" research articles -- fees and (later)
 * regulation breakdowns for ONE named broker, grounded entirely in
 * that broker's own confirmed fields in globalfxhub_get_brokers() plus
 * cross-broker stats computed from the same dataset. Distinct from:
 *  - the broker's own /reviews/{slug}/ page, which already surfaces
 *    the raw fee fields (account_types, overnight_financing, etc.) as
 *    a plain fact table -- this synthesizes them into worked
 *    explanations, a cross-broker cost comparison, and FAQs, the way
 *    CLAUDE.md's "broker-specific research (fees/regulation
 *    deep-dives)" content bucket calls for, rather than repeating the
 *    same table under a new URL.
 *  - inc/broker-comparison-editorial.php's two-broker comparisons,
 *    which overlay onto the existing /compare/{a}-vs-{b}/ page; a
 *    single-broker deep-dive has no existing page to overlay onto, so
 *    it gets its own URL at /reviews/{slug}/fees/ (and, once drafted,
 *    /reviews/{slug}/regulation/) -- nested under the review's own URL
 *    space rather than a new top-level section, same convention as
 *    the existing /reviews/{slug}/alternatives/ pages
 *    (inc/broker-alternatives.php).
 *
 * Cross-broker stats quoted in the prose below (cost-score rank,
 * minimum-deposit percentile, platform-breadth count) are frozen,
 * hand-verified numbers computed once against the live dataset at
 * authoring time -- same precedent as every other hand-curated
 * editorial piece on this site (e.g. broker-comparison-editorial.php),
 * not a live recompute on every page load. Re-verify them whenever
 * this article is revisited for a data refresh.
 *
 * Added one broker/kind at a time as the editorial calendar reaches
 * it, same precedent as broker-comparison-editorial.php -- never a
 * template mechanically applied to all 139 brokers.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_research_rewrite_rules() {
    add_rewrite_rule( '^reviews/([^/]+)/fees/?$', 'index.php?pagename=reviews&broker_research=$matches[1]&broker_research_kind=fees', 'top' );
    add_rewrite_rule( '^reviews/([^/]+)/regulation/?$', 'index.php?pagename=reviews&broker_research=$matches[1]&broker_research_kind=regulation', 'top' );
}
add_action( 'init', 'globalfxhub_research_rewrite_rules' );

function globalfxhub_research_query_vars( $vars ) {
    $vars[] = 'broker_research';
    $vars[] = 'broker_research_kind';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_research_query_vars' );

/**
 * English content, keyed "{broker_slug}-{kind}". Flattened the same
 * way inc/broker-comparison-editorial.php flattens its content for
 * the real translate+review pipeline (i18n/translate_broker_research.py):
 * section_N_heading/section_N_body, faq_N_q/faq_N_a.
 */
function globalfxhub_get_broker_research_articles() {
    return array(
        'pepperstone-fees' => array(
            'broker_slug'      => 'pepperstone',
            'kind'             => 'fees',
            'title'            => 'Pepperstone Fees Explained',
            'meta_title'       => 'Pepperstone Fees Explained (2026): Spreads, Commissions, Swap and Hidden Costs',
            'meta_description' => 'A full breakdown of every fee Pepperstone can charge -- spreads, Razor commission, swap, inactivity and currency conversion -- grounded in our own researched broker dataset, not marketing claims.',
            'intro'            => 'Pepperstone markets "raw spreads from 0.0 pips," but that\'s only one line item in what actually determines your cost to trade with it. This breaks down every fee Pepperstone can charge -- spread, commission, overnight financing, inactivity, currency conversion, and deposits/withdrawals -- using the same researched data we score it against the other 138 brokers in our dataset, not Pepperstone\'s own promotional framing. Every figure below is for Pepperstone EU Limited, the CySEC- and FCA-regulated entity reviewed on this site; a different Pepperstone entity elsewhere in the world may set different terms.',
            'sections' => array(
                array(
                    'heading' => 'Spreads and commissions: Standard vs Razor',
                    'body'    => 'Pepperstone\'s EU entity offers three account types -- Standard, Razor, and Professional (eligibility-gated, with higher leverage) -- plus an Islamic/swap-free option on both Standard and Razor. Standard charges no separate commission: spreads start from around 1.0 pip, and our own on-file headline EUR/USD spread for the account is 1.1 pips. Razor instead charges a raw spread from 0.0 pips plus a round-turn commission of roughly $6-7 per lot, which varies by the platform you trade on. Execution is no-dealing-desk on both tiers -- Standard runs STP-style, Razor runs ECN-style -- and direct market access (DMA) CFDs are available on either tier via cTrader and MT4/MT5. Which account is actually cheaper depends on your own trade size and frequency: a trader placing a few, larger trades often pays less in pure spread on Standard than in spread-plus-commission on Razor, while an active or algorithmic trader running higher volume at the raw Razor spread typically comes out ahead once the commission is netted against the tighter spread. There\'s no substitute for running your own numbers against your actual account size, trade size, and trading frequency.',
                ),
                array(
                    'heading' => 'Overnight financing (swap) costs',
                    'body'    => 'Hold a position open overnight and Pepperstone charges a swap fee based on the tom-next rate from its liquidity providers, plus its own admin/funding fee on top of that. The Standard account doesn\'t break this out as a separate line: instead, Pepperstone bakes roughly a 1-pip markup into the spread rather than charging a distinct overnight fee. Switch to the Islamic/swap-free variant of either account and the daily swap itself disappears, but a reported $100-per-lot administration fee applies for every five-day period a position stays open -- worth modelling carefully if you intend to hold positions for weeks rather than days, since that flat fee doesn\'t scale down the way a per-night swap would for a smaller position.',
                ),
                array(
                    'heading' => 'Non-trading fees: inactivity and currency conversion',
                    'body'    => 'Multiple independent sources we checked report that Pepperstone charges no inactivity fee at all, though we weren\'t able to independently verify that directly against the EU entity\'s own client agreement -- treat it as well-corroborated rather than contractually confirmed. On currency conversion, Pepperstone itself doesn\'t appear to charge a deposit fee when your funding currency differs from your account\'s base currency, but the sending bank or card network may apply its own conversion markup on that transfer, and we couldn\'t independently confirm a specific percentage for that pass-through cost.',
                ),
                array(
                    'heading' => 'Deposit and withdrawal fees',
                    'body'    => 'Pepperstone charges no fee on deposits or withdrawals except for international bank-transfer withdrawals, which carry a fee of roughly $20 (or £15). There\'s no minimum via bank transfer; card, Apple Pay, and Google Pay deposits carry a minimum of around £10. Processing time matters as much as the fee itself here: card deposits land instantly and bank-transfer deposits take up to two business days, while withdrawals range from under one business day (card, Apple Pay, Google Pay) to up to two business days (domestic bank transfer) to as long as seven business days (international bank transfer).',
                ),
                array(
                    'heading' => 'How Pepperstone\'s costs actually rank against the brokers we\'ve researched',
                    'body'    => 'Running Pepperstone\'s 1.1-pip EUR/USD spread through our disclosed scoring formula gives it a trading-costs score of 3.57/5 -- 31st of the 65 brokers in our researched set with a confirmed EUR/USD spread, almost exactly the dataset\'s median rather than meaningfully cheap or expensive on that one measure. That\'s worth knowing because Pepperstone\'s $10 minimum deposit undercuts 59 of the 78 brokers (76%) we have a confirmed minimum deposit for, and its overall score of 3.75/5 sits well above the dataset\'s midpoint -- "accessible and well-regulated" and "cheapest spread" are two different claims, and only the first one holds up for Pepperstone on the numbers. One place it is genuinely rare: Pepperstone is one of just 5 of the 139 brokers we\'ve researched confirmed to support all four of MT4, MT5, cTrader, and TradingView -- platform breadth that\'s a real differentiator even where the headline spread itself is unremarkable.',
                ),
            ),
            'faqs' => array(
                array(
                    'q' => 'Does Pepperstone charge an inactivity fee?',
                    'a' => 'Multiple independent sources we checked report that Pepperstone charges no inactivity fee, though we weren\'t able to verify that directly against the EU entity\'s own client agreement.',
                ),
                array(
                    'q' => 'What\'s the difference between Pepperstone\'s Standard and Razor accounts?',
                    'a' => 'Standard charges no separate commission, with spreads from around 1.0 pip (our on-file EUR/USD figure is 1.1 pips); Razor charges a raw spread from 0.0 pips plus a round-turn commission of roughly $6-7 per lot that varies by platform. Both have an Islamic/swap-free variant, and execution is no-dealing-desk on each -- Standard runs STP-style, Razor runs ECN-style.',
                ),
                array(
                    'q' => 'Does Pepperstone charge a fee to convert currency on deposit?',
                    'a' => 'Not from Pepperstone itself, as far as we could confirm -- but the bank or card network handling the transfer may apply its own conversion markup if your funding currency differs from your account\'s base currency, and we couldn\'t independently confirm a specific percentage for that pass-through cost.',
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, mirrors
 * globalfxhub_editorial_comparison_translations()'s shape and purpose
 * exactly -- filled in once i18n/translate_broker_research.py's real
 * translate+review run completes for a given key.
 */
function globalfxhub_broker_research_translations() {
    return array(
        'pepperstone-fees' => array(
            // Filled in once i18n/translate_broker_research.py's real
            // translate+review run completes.
        ),
    );
}

/**
 * Resolves "{broker_slug}-{kind}" to its article plus the live broker
 * record, or null if either the article isn't drafted yet or the
 * broker slug it names doesn't resolve -- so a bad/future URL 404s
 * rather than rendering with missing data.
 */
function globalfxhub_get_broker_research_article( $broker_slug, $kind ) {
    $key = $broker_slug . '-' . $kind;
    $articles = globalfxhub_get_broker_research_articles();
    if ( ! isset( $articles[ $key ] ) || $articles[ $key ]['broker_slug'] !== $broker_slug || $articles[ $key ]['kind'] !== $kind ) {
        return null;
    }
    $broker = globalfxhub_get_broker_by_slug( $broker_slug );
    if ( ! $broker ) {
        return null;
    }
    $entry = $articles[ $key ];
    $entry['key'] = $key;
    $entry['broker'] = $broker;
    return $entry;
}

/**
 * Same overlay pattern as
 * globalfxhub_get_localized_editorial_comparison(): flattens this
 * language's translation map onto the English entry field by field,
 * falling back to English for any key the translation doesn't cover.
 */
function globalfxhub_get_localized_broker_research_article( $broker_slug, $kind ) {
    $entry = globalfxhub_get_broker_research_article( $broker_slug, $kind );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_broker_research_translations();
    $flat = $translations[ $entry['key'] ][ $lang ] ?? array();
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

/**
 * All drafted research articles for a given broker (either kind) --
 * used for the review page's own contextual links, same idea as
 * globalfxhub_editorial_comparisons_for_broker().
 */
function globalfxhub_broker_research_articles_for_broker( $slug ) {
    $matches = array();
    foreach ( globalfxhub_get_broker_research_articles() as $key => $entry ) {
        if ( $entry['broker_slug'] === $slug ) {
            $matches[] = array(
                'kind'  => $entry['kind'],
                'title' => $entry['title'],
                'url'   => home_url( '/reviews/' . $slug . '/' . $entry['kind'] . '/' ),
            );
        }
    }
    return $matches;
}

function globalfxhub_research_seo_title( $title_parts ) {
    $slug = get_query_var( 'broker_research' );
    $kind = get_query_var( 'broker_research_kind' );
    if ( $slug && $kind ) {
        $entry = globalfxhub_get_localized_broker_research_article( $slug, $kind );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_research_seo_title' );

function globalfxhub_research_canonical( $canonical_url ) {
    $slug = get_query_var( 'broker_research' );
    $kind = get_query_var( 'broker_research_kind' );
    if ( $slug && $kind && globalfxhub_get_broker_research_article( $slug, $kind ) ) {
        return home_url( '/reviews/' . $slug . '/' . $kind . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_research_canonical' );

function globalfxhub_research_seo_head() {
    $slug = get_query_var( 'broker_research' );
    $kind = get_query_var( 'broker_research_kind' );
    if ( ! $slug || ! $kind ) {
        return;
    }
    $entry = globalfxhub_get_localized_broker_research_article( $slug, $kind );
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
add_action( 'wp_head', 'globalfxhub_research_seo_head', 5 );
