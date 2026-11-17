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
 * Regulator count for a broker, matching exactly what
 * globalfxhub_broker_scores() counts for its regulation score --
 * so a "wider regulatory footprint" reason below cites the same
 * number the score itself was built from, not a separate guess.
 */
function globalfxhub_broker_regulator_count( $broker ) {
    $cysec_held = ( null !== $broker['cysec'] ) ? 1 : 0;
    $fca_held   = ( ! empty( $broker['fca'] ) || ! empty( $broker['fca_note'] ) ) ? 1 : 0;
    return $cysec_held + $fca_held + (int) $broker['other_reg_count'];
}

/**
 * A short, parenthetical-free deposit figure for inline citation
 * inside a sentence that already wraps the comparison in its own
 * parentheses -- many brokers' min_deposit_display carries a real,
 * valuable caveat (e.g. "$10 (varies by payment method; some
 * methods allow as low as $1)"), which reads fine on its own but
 * produces an ugly, confusing nested-parens mess once embedded in
 * "($10 (varies by payment method...) vs $50)". This strips that
 * trailing qualifier for THIS inline use only; the full string with
 * its real caveat is still what every other page on this site shows.
 */
function globalfxhub_short_deposit_display( $display ) {
    if ( null === $display ) {
        return $display;
    }
    $head = trim( preg_split( '/\s*[(;]/', $display, 2 )[0] );
    return '' !== $head ? $head : $display;
}

/**
 * Up to 2 concrete, data-grounded reasons a reader might consider
 * switching from $ref to $candidate -- deliberately never an
 * abstract "scores higher" claim on its own. Each reason cites an
 * actual fact already in the dataset (a real deposit figure, a real
 * platform name, a real founding year, a real regulator count),
 * matching the same "ground every claim in a real field" standard
 * globalfxhub_broker_pros_cons() already applies to review pages --
 * this is the same technique aimed at a different question (why
 * switch, not just how this one broker scores). Returns an empty
 * array when nothing clears a meaningful threshold on either side;
 * the template shows a neutral, honest line rather than inventing a
 * reason that isn't really there.
 */
function globalfxhub_alternative_reasons( $ref, $candidate ) {
    $reasons = array();

    $extra_platforms = array_diff( $candidate['platforms'] ?? array(), $ref['platforms'] ?? array() );
    if ( ! empty( $extra_platforms ) ) {
        $reasons[] = array(
            'weight' => 0.6,
            'text'   => 'Offers ' . implode( ' and ', $extra_platforms ) . ', not confirmed on ' . $ref['name'] . '.',
        );
    }

    $cost_bits = array();
    if ( null !== $candidate['min_deposit_usd'] && null !== $ref['min_deposit_usd'] && $candidate['min_deposit_usd'] < $ref['min_deposit_usd'] ) {
        $cost_bits[] = 'a lower minimum deposit (' . globalfxhub_short_deposit_display( $candidate['min_deposit_display'] ) . ' vs ' . globalfxhub_short_deposit_display( $ref['min_deposit_display'] ) . ')';
    }
    if ( null !== $candidate['spread_eurusd'] && null !== $ref['spread_eurusd'] && ( $ref['spread_eurusd'] - $candidate['spread_eurusd'] ) >= 0.3 ) {
        $cost_bits[] = 'a tighter average EUR/USD spread (' . $candidate['spread_eurusd'] . ' vs ' . $ref['spread_eurusd'] . ' pips)';
    }
    if ( ! empty( $cost_bits ) ) {
        $reasons[] = array(
            'weight' => ( $candidate['scores']['cost'] ?? 0 ) - ( $ref['scores']['cost'] ?? 0 ),
            'text'   => 'Lower cost to start and trade: ' . implode( ' and ', $cost_bits ) . '.',
        );
    }

    $cand_reg_count = globalfxhub_broker_regulator_count( $candidate );
    $ref_reg_count  = globalfxhub_broker_regulator_count( $ref );
    if ( $cand_reg_count - $ref_reg_count >= 1 ) {
        $reasons[] = array(
            'weight' => ( $candidate['scores']['regulation'] ?? 0 ) - ( $ref['scores']['regulation'] ?? 0 ),
            'text'   => 'Wider regulatory footprint: ' . $cand_reg_count . ' confirmed regulator(s) vs ' . $ref_reg_count . ' for ' . $ref['name'] . '.',
        );
    }

    if ( null !== $candidate['founded'] && null !== $ref['founded'] && ( $ref['founded'] - $candidate['founded'] ) >= 10 ) {
        $reasons[] = array(
            'weight' => ( $candidate['scores']['track_record'] ?? 0 ) - ( $ref['scores']['track_record'] ?? 0 ),
            'text'   => 'Longer operating history: trading since ' . $candidate['founded'] . ', vs ' . $ref['founded'] . ' for ' . $ref['name'] . '.',
        );
    }

    usort( $reasons, function( $a, $b ) { return $b['weight'] <=> $a['weight']; } );
    return array_slice( array_column( $reasons, 'text' ), 0, 2 );
}

/**
 * Returns the reference broker plus its top 10 alternatives (sharing
 * at least 2 of its instrument/platform tags, sorted by overall
 * score, each with a 'reasons' key from globalfxhub_alternative_reasons()
 * above), or null if the slug doesn't exist or isn't in the top-40
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

    $top = array_slice( $candidates, 0, 10 );
    foreach ( $top as &$candidate ) {
        $candidate['reasons'] = globalfxhub_alternative_reasons( $broker, $candidate );
    }
    unset( $candidate );

    return array(
        'broker'       => $broker,
        'alternatives' => $top,
    );
}

function globalfxhub_alternatives_seo_title( $title_parts ) {
    $slug = get_query_var( 'broker_alts' );
    if ( $slug ) {
        $data = globalfxhub_broker_alternatives( $slug );
        if ( $data ) {
            $title_parts['title'] = sprintf( globalfxhub_t( 'seo_broker_alternatives_title' ), $data['broker']['name'], date( 'Y' ) );
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_alternatives_seo_title' );

function globalfxhub_alternatives_canonical( $canonical_url ) {
    $slug = get_query_var( 'broker_alts' );
    if ( $slug && globalfxhub_broker_alternatives( $slug ) ) {
        return globalfxhub_track_b_url( 'reviews/' . $slug . '/alternatives/' );
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
        'Researched alternatives to %s, each with the specific confirmed reasons -- a real platform, deposit, spread, regulator count, or founding year -- that might make it worth switching to, not just a list of similarly-tagged brokers.',
        $data['broker']['name']
    );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'globalfxhub_alternatives_seo_head', 5 );
