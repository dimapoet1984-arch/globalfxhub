<?php
/**
 * "Alternatives to [Broker]" pages at /reviews/{slug}/alternatives/.
 * Reuses the same instrument/platform tag derivation the Broker Finder
 * already computes (globalfxhub_broker_instrument_tags() /
 * globalfxhub_broker_platform_tags() in inc/broker-finder.php) --
 * never a new broker fact invented for this page specifically.
 *
 * Bounded to the top 40 brokers by rank as the reference broker a page
 * exists for (globalfxhub_top_ranked_broker_slugs()) -- a lower-ranked
 * broker is far less likely to be searched "alternatives to" in the
 * first place, and this keeps the page count disciplined rather than
 * generating one for all 139. The candidate pool shown as alternatives
 * is not similarly restricted -- any of the 139 can appear as a result
 * if it genuinely shares enough tags with the reference broker.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_alternatives_rewrite_rules() {
    add_rewrite_rule( '^reviews/([^/]+)/alternatives/?$', 'index.php?pagename=reviews&broker_alts=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_alternatives_rewrite_rules' );

function globalfxhub_alternatives_query_vars( $vars ) {
    $vars[] = 'broker_alts';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_alternatives_query_vars' );

/**
 * Returns the reference broker plus its top 10 alternatives (sharing
 * at least 2 of its instrument/platform tags, sorted by overall
 * score), or null if the slug doesn't exist or isn't in the top-40
 * bound.
 */
function globalfxhub_broker_alternatives( $slug ) {
    if ( ! in_array( $slug, globalfxhub_top_ranked_broker_slugs( 40 ), true ) ) {
        return null;
    }
    $broker = globalfxhub_get_broker_by_slug( $slug );
    if ( ! $broker ) {
        return null;
    }
    $ref_instruments = globalfxhub_broker_instrument_tags( $broker );
    $ref_platforms = globalfxhub_broker_platform_tags( $broker );

    $candidates = array();
    foreach ( globalfxhub_get_brokers() as $candidate ) {
        if ( $candidate['slug'] === $slug ) {
            continue;
        }
        $shared = count( array_intersect( $ref_instruments, globalfxhub_broker_instrument_tags( $candidate ) ) )
                + count( array_intersect( $ref_platforms, globalfxhub_broker_platform_tags( $candidate ) ) );
        if ( $shared >= 2 ) {
            $candidates[] = $candidate;
        }
    }
    usort( $candidates, function( $a, $b ) { return ( $b['scores']['overall'] ?? 0 ) <=> ( $a['scores']['overall'] ?? 0 ); } );

    return array(
        'broker'       => $broker,
        'alternatives' => array_slice( $candidates, 0, 10 ),
    );
}

function globalfxhub_alternatives_seo_title( $title_parts ) {
    $slug = get_query_var( 'broker_alts' );
    if ( $slug ) {
        $data = globalfxhub_broker_alternatives( $slug );
        if ( $data ) {
            $title_parts['title'] = 'Best ' . $data['broker']['name'] . ' Alternatives ' . date( 'Y' ) . ': Similar Brokers Ranked';
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_alternatives_seo_title' );

function globalfxhub_alternatives_canonical( $canonical_url ) {
    $slug = get_query_var( 'broker_alts' );
    if ( $slug && globalfxhub_broker_alternatives( $slug ) ) {
        return home_url( '/reviews/' . $slug . '/alternatives/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_alternatives_canonical' );

function globalfxhub_alternatives_seo_head() {
    $slug = get_query_var( 'broker_alts' );
    if ( ! $slug ) {
        return;
    }
    $data = globalfxhub_broker_alternatives( $slug );
    if ( ! $data ) {
        return;
    }
    $description = sprintf(
        'Researched alternatives to %s: brokers offering similar instruments and platforms, ranked by our disclosed nine-category score -- not a popularity guess.',
        $data['broker']['name']
    );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'globalfxhub_alternatives_seo_head', 5 );
