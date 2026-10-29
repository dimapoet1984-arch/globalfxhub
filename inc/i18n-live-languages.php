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
 *
 * Yoast SEO (added 2026-10-29) prints its own <meta name="robots">
 * tag on every page regardless of language, which would otherwise
 * land ALONGSIDE this one -- two separate, contradictory robots meta
 * tags on the same non-live-language page ("index, follow, ..." from
 * Yoast, then "noindex,follow" from here). Skip this echo whenever
 * Yoast is active and let globalfxhub_yoast_robots_unlaunched_languages()
 * below control the single tag Yoast prints instead.
 */
function globalfxhub_noindex_unlaunched_languages() {
    if ( defined( 'WPSEO_VERSION' ) ) {
        return;
    }
    if ( ! globalfxhub_is_live_language() ) {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_noindex_unlaunched_languages', 1 );

/**
 * Same noindex decision, routed through Yoast's own robots filter so
 * there's exactly one <meta name="robots"> tag once Yoast is active --
 * per docs/TRANSLATION_PLAYBOOK.md section 2.2's own guidance
 * ("wpseo_robots -> noindex, nofollow"), adapted to keep this site's
 * existing "follow" choice rather than switching to "nofollow".
 * Modern Yoast (the version actually installed here, confirmed live
 * via its rendered output) passes $robots as an associative array
 * ('index' => 'index', 'follow' => 'follow', ...); older Yoast
 * releases passed a plain string -- both shapes are handled so this
 * doesn't silently no-op if the installed version differs from what
 * was checked live.
 */
function globalfxhub_yoast_robots_unlaunched_languages( $robots ) {
    if ( globalfxhub_is_live_language() ) {
        return $robots;
    }
    if ( is_array( $robots ) ) {
        $robots['index'] = 'noindex';
    } elseif ( is_string( $robots ) ) {
        $robots = preg_replace( '/\bindex\b/', 'noindex', $robots, 1 );
    }
    return $robots;
}
add_filter( 'wpseo_robots', 'globalfxhub_yoast_robots_unlaunched_languages' );

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
 * Every published post ID (of the given post type, or every
 * post/page if none given) whose Polylang language isn't live yet --
 * shared by both sitemap-exclusion filters below so the two stay in
 * sync rather than drifting into separately-maintained copies of the
 * same query.
 */
function globalfxhub_unlaunched_language_post_ids( $post_type = array( 'post', 'page' ) ) {
    if ( ! function_exists( 'pll_get_post_language' ) ) {
        return array();
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
    return $exclude;
}

/**
 * Excludes every post/page in a non-live language from WordPress
 * core's own /wp-sitemap.xml (separate from, and in addition to, the
 * custom /sitemap.xml in inc/sitemap.php, which never listed
 * non-English URLs in the first place since it's built from
 * home_url() calls with no language prefix -- this filter protects
 * the one sitemap that actually would have exposed them: core's,
 * which walks all published posts/pages of the relevant post types
 * regardless of language).
 *
 * Dormant while Yoast SEO is active: Yoast disables and 301s core's
 * /wp-sitemap.xml in favor of its own (confirmed live), so this
 * filter's hook never fires in that state. Left in place rather than
 * removed, since it's the correct protection again the moment Yoast
 * is ever deactivated -- see globalfxhub_exclude_unlaunched_languages_from_yoast_sitemap()
 * below for the filter that actually matters while Yoast is active.
 */
function globalfxhub_exclude_unlaunched_languages_from_core_sitemap( $args, $post_type ) {
    if ( ! function_exists( 'pll_languages_list' ) ) {
        return $args;
    }
    $exclude = globalfxhub_unlaunched_language_post_ids( $post_type );
    if ( $exclude ) {
        $args['post__not_in'] = array_merge( $args['post__not_in'] ?? array(), $exclude );
    }
    return $args;
}
add_filter( 'wp_sitemaps_posts_query_args', 'globalfxhub_exclude_unlaunched_languages_from_core_sitemap', 10, 2 );

/**
 * Same protection for Yoast SEO's own sitemap (added 2026-10-29),
 * which has nothing to do with core's and isn't touched by the
 * filter above -- confirmed live that Yoast's /page-sitemap.xml was
 * listing every home-<language> stub page (the 7 front-page objects
 * for unlaunched languages) before this filter existed. Per
 * docs/TRANSLATION_PLAYBOOK.md section 2.2's own guidance
 * ("wpseo_exclude_from_sitemap_by_post_ids -> their posts").
 */
function globalfxhub_exclude_unlaunched_languages_from_yoast_sitemap( $excluded_ids ) {
    return array_merge( (array) $excluded_ids, globalfxhub_unlaunched_language_post_ids() );
}
add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'globalfxhub_exclude_unlaunched_languages_from_yoast_sitemap' );
