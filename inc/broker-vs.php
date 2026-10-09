<?php
/**
 * Static, indexable "Broker A vs Broker B" pages at
 * /compare/{a}-vs-{b}/ -- a crawlable URL per pairing, rather than
 * relying solely on the client-side /compare/?a=&b= tool in
 * page-compare.php, which has no unique URL, title, or meta
 * description per pair for a search engine to index.
 *
 * Bounded deliberately: only pairs where BOTH brokers sit in the top
 * 40 by rank (globalfxhub_top_ranked_broker_slugs()) get a real page --
 * at most 780 possible pairs, not all 9,591 (139 choose 2). Every page
 * is computed fresh from the same broker/scores data every other
 * ranking on this site uses, server-rendered on each request from
 * page-compare.php -- never pre-generated or exported as a static
 * file, so there's nothing to regenerate when a broker's data changes.
 * An out-of-bound or nonexistent pair 404s rather than silently
 * rendering.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_vs_rewrite_rules() {
    add_rewrite_rule( '^compare/([^/]+)-vs-([^/]+)/?$', 'index.php?pagename=compare&vs_a=$matches[1]&vs_b=$matches[2]', 'top' );
}
add_action( 'init', 'globalfxhub_vs_rewrite_rules' );

function globalfxhub_vs_query_vars( $vars ) {
    $vars[] = 'vs_a';
    $vars[] = 'vs_b';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_vs_query_vars' );

/**
 * Validates and returns the pair, or null if either slug doesn't
 * exist, both slugs are the same broker, or either broker sits outside
 * the top-40 bound.
 */
function globalfxhub_vs_pair( $slug_a, $slug_b ) {
    if ( ! $slug_a || ! $slug_b || $slug_a === $slug_b ) {
        return null;
    }
    $eligible = globalfxhub_top_ranked_broker_slugs( 40 );
    if ( ! in_array( $slug_a, $eligible, true ) || ! in_array( $slug_b, $eligible, true ) ) {
        return null;
    }
    $a = globalfxhub_get_broker_by_slug( $slug_a );
    $b = globalfxhub_get_broker_by_slug( $slug_b );
    if ( ! $a || ! $b ) {
        return null;
    }
    return array( 'a' => $a, 'b' => $b );
}

/**
 * Server-rendered comparison rows -- the exact same field list
 * page-compare.php's own client-side tool already displays for a pair
 * (same source data, same globalfxhub_broker_regulation_label() this
 * site's reviews/best-lists/finder already use for regulation, so this
 * can never show a different regulation summary than any of them), so
 * a crawler or a no-JS visitor sees the same facts the interactive
 * tool would render for the same pair -- not a second, divergent
 * description of the same two brokers.
 */
function globalfxhub_vs_comparison_rows( $a, $b ) {
    $na = 'Not independently confirmed';
    $or_na = function( $v, $suffix = '' ) use ( $na ) {
        return ( null === $v || '' === $v ) ? $na : ( $v . $suffix );
    };
    $reg_note = function( $x ) {
        return implode( ' ', array_filter( array( $x['cysec_note'] ?? null, $x['fca_note'] ?? null, $x['seychelles_note'] ?? null ) ) );
    };

    return array(
        array( 'label' => 'Overall score', 'a' => $a['scores']['overall'] . ' / 5', 'b' => $b['scores']['overall'] . ' / 5' ),
        array( 'label' => 'Regulation', 'a' => globalfxhub_broker_regulation_label( $a ), 'note_a' => $reg_note( $a ), 'b' => globalfxhub_broker_regulation_label( $b ), 'note_b' => $reg_note( $b ) ),
        array( 'label' => 'Entity', 'a' => $a['entity'] ?? $na, 'b' => $b['entity'] ?? $na ),
        array( 'label' => 'Founded', 'a' => $or_na( $a['founded'] ?? null ), 'b' => $or_na( $b['founded'] ?? null ) ),
        array( 'label' => 'Headquarters', 'a' => $or_na( $a['hq'] ?? null ), 'b' => $or_na( $b['hq'] ?? null ) ),
        array( 'label' => 'Minimum deposit', 'a' => $or_na( $a['min_deposit_display'] ?? null ), 'b' => $or_na( $b['min_deposit_display'] ?? null ) ),
        array( 'label' => 'Avg. spread (EUR/USD)', 'a' => $or_na( $a['spread_eurusd'] ?? null, ' pips' ), 'b' => $or_na( $b['spread_eurusd'] ?? null, ' pips' ) ),
        array( 'label' => 'Platforms', 'a' => $a['platforms'] ? implode( ', ', $a['platforms'] ) : $na, 'b' => $b['platforms'] ? implode( ', ', $b['platforms'] ) : $na ),
        array( 'label' => 'Other Tier-1 regulators', 'a' => $a['other_reg'] ? implode( ', ', $a['other_reg'] ) : 'None confirmed', 'b' => $b['other_reg'] ? implode( ', ', $b['other_reg'] ) : 'None confirmed' ),
        array( 'label' => 'Instruments', 'a' => $or_na( $a['instruments'] ?? null ), 'b' => $or_na( $b['instruments'] ?? null ) ),
    );
}

function globalfxhub_vs_seo_title( $title_parts ) {
    $pair = globalfxhub_vs_pair( get_query_var( 'vs_a' ), get_query_var( 'vs_b' ) );
    if ( $pair ) {
        $title_parts['title'] = $pair['a']['name'] . ' vs ' . $pair['b']['name'] . ' ' . date( 'Y' ) . ': Which Is Better?';
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_vs_seo_title' );

function globalfxhub_vs_canonical( $canonical_url ) {
    $a_slug = get_query_var( 'vs_a' );
    $b_slug = get_query_var( 'vs_b' );
    if ( globalfxhub_vs_pair( $a_slug, $b_slug ) ) {
        return home_url( '/compare/' . $a_slug . '-vs-' . $b_slug . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_vs_canonical' );

function globalfxhub_vs_seo_head() {
    $pair = globalfxhub_vs_pair( get_query_var( 'vs_a' ), get_query_var( 'vs_b' ) );
    if ( ! $pair ) {
        return;
    }
    $a = $pair['a'];
    $b = $pair['b'];
    $description = sprintf(
        '%s vs %s: compare regulation, cost, platforms, and score side by side -- %s scored %s/5, %s scored %s/5 under our disclosed nine-category methodology.',
        $a['name'], $b['name'], $a['name'], $a['scores']['overall'], $b['name'], $b['scores']['overall']
    );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'globalfxhub_vs_seo_head', 5 );
