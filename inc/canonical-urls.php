<?php
/**
 * Several tool pages accept a query-string alternate route to content
 * that also has (or, for /compare/, renders identically to) a clean
 * canonical URL:
 *
 * - /reviews/?broker={slug}  -> same content as /reviews/{slug}/
 * - /reviews/?search={q}     -> a filtered view of /reviews/, not a
 *   distinct page in its own right
 * - /countries/?country={slug} -> same content as /countries/{slug}/
 * - /compare/?a={slug}&b={slug} -> server-rendered HTML identical to
 *   /compare/ (the two params only pre-fill the client-side JS picker,
 *   see page-compare.php's PRESET_A/PRESET_B) -- or, when both slugs
 *   resolve to an eligible top-40 pairing, the same content as the
 *   static /compare/{a}-vs-{b}/ page
 *
 * With no canonical tag pointing these back to the clean URL, Yoast's
 * default behavior is to self-canonicalize every query-string
 * variation, making each one an independently indexable near-duplicate
 * of a page that already exists at a clean URL -- exactly the kind of
 * parameter-URL bloat a sitemap/canonical audit flags.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_canonical_for_query_param_routes( $canonical ) {
    if ( is_page( 'reviews' ) ) {
        if ( ! empty( $_GET['broker'] ) ) {
            $broker = globalfxhub_get_broker_by_slug( sanitize_title( wp_unslash( $_GET['broker'] ) ) );
            if ( $broker ) {
                return home_url( '/reviews/' . $broker['slug'] . '/' );
            }
        }
        if ( isset( $_GET['search'] ) && '' !== $_GET['search'] ) {
            return home_url( '/reviews/' );
        }
    }

    if ( is_page( 'countries' ) && ! empty( $_GET['country'] ) ) {
        $country = globalfxhub_get_country_by_slug( sanitize_title( wp_unslash( $_GET['country'] ) ) );
        if ( $country ) {
            return home_url( '/countries/' . $country['slug'] . '/' );
        }
    }

    if ( is_page( 'compare' ) && ! empty( $_GET['a'] ) && ! empty( $_GET['b'] ) ) {
        $a = sanitize_title( wp_unslash( $_GET['a'] ) );
        $b = sanitize_title( wp_unslash( $_GET['b'] ) );
        if ( function_exists( 'globalfxhub_vs_pair' ) && globalfxhub_vs_pair( $a, $b ) ) {
            return home_url( '/compare/' . $a . '-vs-' . $b . '/' );
        }
        return home_url( '/compare/' );
    }

    return $canonical;
}
add_filter( 'wpseo_canonical', 'globalfxhub_canonical_for_query_param_routes' );
