<?php
/**
 * Language-aware URL builder for the 14 "templated" hub pages
 * globalfxhub_do_sync_templated_pages_across_languages() (functions.php)
 * keeps a real, distinct WP Page for in every language: reviews,
 * learn, compare, news, countries, best, regulation, research,
 * regulators, data, broker-finder, cost-calculator,
 * regulation-checker, broker-changelog.
 *
 * Confirmed live: every breadcrumb/cross-reference link to one of
 * these pages' own bare URL (e.g. the "Reviews" breadcrumb segment
 * shown on /es/reviews-es/ itself, or on an individual broker review
 * page) was built with a plain home_url( '/reviews/' ), which always
 * produces the bare English URL regardless of the current page's own
 * language -- the correctly-localized URL (/es/reviews-es/) already
 * existed and worked fine if visited directly, exactly the same root
 * cause already fixed for the Track B dynamic routes
 * (inc/i18n-track-b-routing.php) and the <title>-tag
 * is_page()/is_page_template() bug earlier.
 *
 * Deliberately NOT used by inc/sitemap.php's own URL listing (lists
 * one canonical English entry per item for crawling, a different
 * mechanism with no single "current visitor language" to resolve --
 * same reasoning as inc/i18n-track-b-routing.php's own docblock), nor
 * by inc/changelog-subscribers.php's email digest (sent outside any
 * web request, to a subscriber whose preferred language isn't
 * currently tracked at all -- a separate, not-yet-tackled gap, not a
 * regression from leaving it as-is here), nor by the News hub's
 * "markets"/"brokers" CHILD pages (globalfxhub_ensure_child_page(), a
 * different, English-only mechanism with no per-language slug variant
 * -- same exception already established for is_page('markets')/
 * is_page('brokers') elsewhere).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Builds a language-correct URL to one of the 14 templated hub pages
 * listed above.
 *
 * $slug is the page's stable English slug (e.g. 'reviews'), with no
 * leading or trailing slash.
 *
 * On the default language, or when Polylang isn't active, returns the
 * same bare home_url( '/' . $slug . '/' ) every call site already
 * builds today. Otherwise returns the "$slug-$lang"-suffixed URL
 * ("/es/reviews-es/") that globalfxhub_do_sync_templated_pages_across_languages()
 * guarantees exists for every language -- see that function's own
 * comment for exactly why that deterministic suffix, not whatever
 * slug Polylang might otherwise have generated, is what every
 * language's copy of these pages actually has.
 *
 * Reuses globalfxhub_track_b_current_lang() (inc/i18n-track-b-routing.php)
 * for the current language, for the same reason a Track B link does:
 * a breadcrumb link to e.g. Reviews can be rendered FROM a Track B
 * page itself (an individual broker review's own breadcrumb trail),
 * where plain pll_current_language() is confirmed to return the
 * wrong language.
 */
function globalfxhub_templated_page_url( $slug ) {
    $lang = function_exists( 'globalfxhub_track_b_current_lang' ) ? globalfxhub_track_b_current_lang() : null;
    if ( ! $lang || ( function_exists( 'pll_default_language' ) && $lang === pll_default_language() ) ) {
        return home_url( '/' . $slug . '/' );
    }
    return home_url( '/' . $lang . '/' . $slug . '-' . $lang . '/' );
}
