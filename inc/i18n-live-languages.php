<?php
/**
 * Keeps every Polylang language except English out of search results
 * until its translated content is actually reviewed and ready -- the
 * protection the September 21 Polylang rollout never had.
 *
 * That rollout (up to 15 languages; this theme's container pages --
 * reviews, compare, best, countries, regulation, the four tools, news
 * and its children, learn -- auto-duplicated per language on every
 * request via globalfxhub_sync_templated_pages_across_languages() in
 * functions.php) broke the live site and was paused by hiding the
 * front-end language switcher (GLOBALFXHUB_LANG_SWITCHER_ENABLED),
 * but a hidden switcher never stopped those duplicated pages from
 * being directly reachable, and -- since they're real wp_insert_post()
 * page objects -- from being picked up by WordPress core's own
 * /wp-sitemap.xml and crawled. Nothing before this added noindex,
 * dropped them from hreflang, or excluded them from that sitemap.
 *
 * GLOBALFXHUB_LIVE_LANGUAGES lists the Polylang language codes
 * allowed to be indexed. Add a language only once it's launched (see
 * docs/TRANSLATION_PLAYBOOK.md section 2.2 and step 9 of the order of
 * work) -- never earlier, and never by editing this file under time
 * pressure without going through that checklist.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBALFXHUB_LIVE_LANGUAGES', array( 'en' ) );

function globalfxhub_is_live_language( $lang = null ) {
    if ( ! function_exists( 'pll_current_language' ) ) {
        // Polylang inactive -- the whole site is English, nothing to hide.
        return true;
    }
    $lang = $lang ? $lang : pll_current_language();
    return in_array( $lang, GLOBALFXHUB_LIVE_LANGUAGES, true );
}

/**
 * noindex every page in a language not yet launched. Matches the
 * existing noindex pattern in inc/news-feed.php (a direct wp_head
 * echo at priority 1, "noindex,follow" since the point is to keep
 * these pages out of search results, not to stop crawling/link
 * equity flowing through them) rather than the wp_robots filter, for
 * consistency with how this theme already does it.
 */
function globalfxhub_noindex_unlaunched_languages() {
    if ( ! globalfxhub_is_live_language() ) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_noindex_unlaunched_languages', 1 );

/**
 * Drops hreflang alternates for any language not yet launched, and
 * drops the whole hreflang block once only one (or zero) remains -- a
 * lone self-referencing hreflang entry is meaningless and can confuse
 * crawlers.
 */
function globalfxhub_hreflang_live_languages_only( $hreflangs ) {
    if ( ! is_array( $hreflangs ) ) {
        return $hreflangs;
    }
    $filtered = array_intersect_key( $hreflangs, array_flip( GLOBALFXHUB_LIVE_LANGUAGES ) );
    return count( $filtered ) > 1 ? $filtered : array();
}
add_filter( 'pll_rel_hreflang_attributes', 'globalfxhub_hreflang_live_languages_only' );

/**
 * Excludes every post/page in a non-live language from WordPress
 * core's own /wp-sitemap.xml (separate from, and in addition to, the
 * custom /sitemap.xml in inc/sitemap.php, which never listed
 * non-English URLs in the first place since it's built from
 * home_url() calls with no language prefix -- this filter protects
 * the one sitemap that actually would have exposed them: core's,
 * which walks all published posts/pages of the relevant post types
 * regardless of language).
 */
function globalfxhub_exclude_unlaunched_languages_from_core_sitemap( $args, $post_type ) {
    if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_get_post_language' ) ) {
        return $args;
    }
    $exclude = array();
    foreach ( get_posts( array(
        'post_type'      => $post_type,
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ) ) as $post_id ) {
        $lang = pll_get_post_language( $post_id );
        if ( $lang && ! in_array( $lang, GLOBALFXHUB_LIVE_LANGUAGES, true ) ) {
            $exclude[] = $post_id;
        }
    }
    if ( $exclude ) {
        $args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), $exclude );
    }
    return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'globalfxhub_exclude_unlaunched_languages_from_core_sitemap', 10, 2 );
