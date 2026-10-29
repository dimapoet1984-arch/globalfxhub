<?php
/**
 * Language-aware URL routing for the Track B virtual routes (broker
 * reviews, best-lists, regulation pages, countries, compare pairs,
 * broker-news items) -- see docs/TRANSLATION_PIPELINE_ARCHITECTURE.md
 * section 1. None of these had a language-prefixed URL variant before
 * this file: they're custom rewrite-rule routes with no WP post of
 * their own, so Polylang's usual per-post URL handling never applied
 * to them, and the English-only rewrite rules (functions.php,
 * inc/countries.php, inc/best.php is data not rules, inc/regulation.php
 * is data not rules, inc/broker-vs.php, inc/broker-news.php,
 * inc/broker-alternatives.php) only ever matched a bare, unprefixed path.
 *
 * Each route below gets a SECOND rewrite rule matching the same path
 * with a language-code prefix ("/es/reviews/ig/"), capturing the
 * language into the 'lang' query var (Polylang's own 'request' filter
 * hook already consumes that to set the current language for the
 * rest of the request -- no extra code needed for that part) and
 * into a new gfh_track_b_container var naming the CONTAINER page's
 * own stable English slug/pagename.
 *
 * Deliberately NOT done by hardcoding each language's translated
 * pagename (e.g. "reviews-es") directly into the rewrite target: that
 * works for the 11 pages globalfxhub_do_sync_templated_pages_across_languages()
 * manages (which do now get a deterministic $slug-$lang slug -- see
 * that function's fix, functions.php), but "news/brokers" and
 * "news/markets" are regular Track A pages translated by
 * i18n/translate.py, whose slugs are organically generated from the
 * translated title (confirmed live: "noticias-de-brokers", not
 * anything predictable from a pattern). Resolving through Polylang's
 * own pll_get_post() instead works correctly for EITHER kind of page,
 * present or future, without caring what slug it actually ended up
 * with.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Regex alternation of every Polylang language except the default
 * one ("es|de|ar|fr|pl|it|pt" today), built from Polylang's own
 * registered languages rather than hardcoded, so adding or removing
 * a language doesn't leave this file silently out of sync.
 */
function globalfxhub_track_b_lang_pattern() {
    if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_default_language' ) ) {
        return '';
    }
    $default  = pll_default_language();
    $non_default = array_filter( pll_languages_list(), function ( $lang ) use ( $default ) {
        return $lang !== $default;
    } );
    if ( empty( $non_default ) ) {
        return '';
    }
    return implode( '|', array_map( 'preg_quote', $non_default ) );
}

/**
 * One rewrite rule per Track B route, each a language-prefixed
 * variant of an EXISTING English-only rule elsewhere in the theme --
 * see this file's header for exactly which file owns each English
 * original. $container_slug is that route's container page's own
 * stable English pagename (never translated) -- globalfxhub_resolve_track_b_container_language()
 * below resolves it to the correct language's page ID at request
 * time, however that page's own slug turned out.
 */
function globalfxhub_track_b_routing_rewrite_rules() {
    $lang_pattern = globalfxhub_track_b_lang_pattern();
    if ( ! $lang_pattern ) {
        return; // Polylang inactive or only the default language exists -- nothing to prefix.
    }

    $routes = array(
        // [ 'path regex (lang already captured as group 1 by the caller)', 'container_slug', 'extra query vars, $matches offset by +1' ]
        array( 'reviews/([^/]+)/alternatives/?$', 'reviews', array( 'broker_alts' => 2 ) ),
        array( 'reviews/([^/]+)/?$', 'reviews', array( 'broker' => 2 ) ),
        array( 'best/([^/]+)/?$', 'best', array( 'bestlist' => 2 ) ),
        array( 'regulation/([^/]+)/?$', 'regulation', array( 'regpage' => 2 ) ),
        array( 'countries/([^/]+)/?$', 'countries', array( 'country' => 2 ) ),
        array( 'compare/([^/]+)-vs-([^/]+)/?$', 'compare', array( 'vs_a' => 2, 'vs_b' => 3 ) ),
        array( 'news/brokers/([^/]+)/?$', 'news/brokers', array( 'broker_news_item' => 2 ) ),
    );

    foreach ( $routes as $route ) {
        list( $path, $container_slug, $extra_vars ) = $route;

        $query = 'index.php?gfh_track_b_container=' . rawurlencode( $container_slug ) . '&lang=$matches[1]';
        foreach ( $extra_vars as $var_name => $match_index ) {
            $query .= '&' . $var_name . '=$matches[' . $match_index . ']';
        }

        add_rewrite_rule( '^(' . $lang_pattern . ')/' . $path, $query, 'top' );
    }
}
add_action( 'init', 'globalfxhub_track_b_routing_rewrite_rules', 15 ); // after Polylang's own init, before the sync function (priority 20) that flushes rules when it changes anything.

function globalfxhub_track_b_routing_query_vars( $vars ) {
    $vars[] = 'gfh_track_b_container';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_track_b_routing_query_vars' );

/**
 * Resolves gfh_track_b_container (the container's stable English
 * pagename) + lang into the ACTUAL page ID for that language via
 * Polylang's own pll_get_post(), replacing 'pagename' with a direct
 * 'page_id' so WordPress's main query loads the right page
 * regardless of what slug it actually has. Runs on the 'request'
 * filter -- the standard WordPress hook for resolving custom query
 * vars into the vars core actually understands, before the main
 * query executes.
 */
function globalfxhub_resolve_track_b_container_language( $query_vars ) {
    if ( empty( $query_vars['gfh_track_b_container'] ) || empty( $query_vars['lang'] ) ) {
        return $query_vars;
    }
    if ( ! function_exists( 'pll_get_post' ) ) {
        return $query_vars;
    }

    $english_page = get_page_by_path( $query_vars['gfh_track_b_container'] );
    if ( ! $english_page ) {
        return $query_vars; // container page genuinely doesn't exist -- let WP 404 normally, nothing to resolve.
    }

    $translated_id = pll_get_post( $english_page->ID, $query_vars['lang'] );
    if ( ! $translated_id ) {
        // No translation for this language yet -- fall back to the
        // English page rather than 404ing on a route that works fine
        // in English, since the page content itself (the template)
        // is identical either way; only globalfxhub_tf() calls inside
        // it fall back to English text, same as everywhere else.
        $translated_id = $english_page->ID;
    }

    // Confirmed live on EVERY Track B route, not just the no-translation
    // fallback above: Polylang's own curlang detection never recognizes
    // these custom rewrite-rule URLs ("/es/best/", "/ar/reviews/ig/", ...)
    // as language-bearing at all -- <html> came back lang="en-GB" even
    // on /es/best/ where a real Spanish translation exists and loads
    // correctly. Record the language the URL actually asked for so
    // globalfxhub_fix_track_b_language_attributes() below can put the
    // correct <html> attributes back, independently of whatever
    // Polylang's own filter on the same hook decided.
    $GLOBALS['globalfxhub_track_b_request_lang'] = $query_vars['lang'];

    $query_vars['page_id'] = $translated_id;
    unset( $query_vars['pagename'] );
    return $query_vars;
}
add_filter( 'request', 'globalfxhub_resolve_track_b_container_language' );

/**
 * Overrides the <html> lang/dir attributes back to whatever language
 * the URL actually requested, for every Track B route resolved by
 * globalfxhub_resolve_track_b_container_language() above (translated or
 * English-fallback alike -- see that function for why Polylang's own
 * detection can't be trusted here). Runs on WordPress core's own
 * 'language_attributes' filter (the one get_language_attributes()
 * already passes its output through), at a late priority so it wins
 * over Polylang's own filtering on the same hook. Reads the target
 * language's locale/RTL-ness straight from Polylang's own language
 * list rather than hardcoding either, so a newly-added language is
 * handled correctly with no change here.
 */
function globalfxhub_fix_track_b_language_attributes( $output, $doctype = 'html' ) {
    if ( empty( $GLOBALS['globalfxhub_track_b_request_lang'] ) ) {
        return $output;
    }
    if ( ! function_exists( 'pll_languages_list' ) ) {
        return $output;
    }

    $requested_lang = $GLOBALS['globalfxhub_track_b_request_lang'];
    $languages       = pll_languages_list( array( 'fields' => '' ) );
    $lang_obj        = null;
    foreach ( (array) $languages as $language ) {
        if ( isset( $language->slug ) && $language->slug === $requested_lang ) {
            $lang_obj = $language;
            break;
        }
    }
    if ( ! $lang_obj || empty( $lang_obj->locale ) ) {
        return $output; // Unknown language code -- leave Polylang's own output alone.
    }

    $attributes = array();
    if ( ! empty( $lang_obj->is_rtl ) ) {
        $attributes[] = 'dir="rtl"';
    }

    $lang_attr = str_replace( '_', '-', $lang_obj->locale );
    if ( 'text/html' === get_option( 'html_type' ) || 'html' === $doctype ) {
        $attributes[] = 'lang="' . esc_attr( $lang_attr ) . '"';
    }
    if ( 'text/html' !== get_option( 'html_type' ) || 'xhtml' === $doctype ) {
        $attributes[] = 'xml:lang="' . esc_attr( $lang_attr ) . '"';
    }

    return implode( ' ', $attributes );
}
add_filter( 'language_attributes', 'globalfxhub_fix_track_b_language_attributes', 20, 2 );

/**
 * Suppresses the 301 redirect Polylang's own PLL_Canonical::check_canonical_url()
 * would otherwise issue on a Track B request that fell back to the
 * English page -- confirmed live on /ar/news/brokers/<item>/ and
 * /de/news/brokers/<item>/ (no Arabic/German translation of the
 * "news/brokers" container exists yet, unlike the 11 pages
 * globalfxhub_do_sync_templated_pages_across_languages() keeps a stub
 * in every language for): Polylang notices the resolved page_id's
 * actual language ("en") doesn't match the requested URL prefix and
 * redirects the WHOLE request back to the bare English URL (response
 * header x-redirect-by: Polylang) *before* the page, or
 * globalfxhub_fix_track_b_language_attributes() above, ever renders --
 * defeating the fallback-instead-of-404 design entirely for any
 * Track B container that isn't one of those 11 self-healed pages.
 *
 * Hooks Polylang's own 'pll_check_canonical_url' filter -- NOT
 * WordPress core's 'redirect_canonical' -- since that's the actual
 * decision point: check_canonical_url() computes its redirect target
 * via remove_language_from_link()/switch_language_in_link() *after*
 * calling core's redirect_canonical() internally, so filtering core's
 * hook alone gets overwritten by Polylang's own link-rewriting before
 * wp_safe_redirect() ever fires (confirmed live -- an earlier version
 * of this fix targeting 'redirect_canonical' directly had no effect).
 *
 * $GLOBALS['globalfxhub_track_b_request_lang'] is only set once
 * globalfxhub_resolve_track_b_container_language() has actually
 * resolved a Track B route for this request, so this never touches an
 * ordinary page's own (legitimate) canonical redirects.
 */
function globalfxhub_suppress_track_b_canonical_redirect( $redirect_url, $language = null ) {
    if ( ! empty( $GLOBALS['globalfxhub_track_b_request_lang'] ) ) {
        return false;
    }
    return $redirect_url;
}
add_filter( 'pll_check_canonical_url', 'globalfxhub_suppress_track_b_canonical_redirect', 20, 2 );
