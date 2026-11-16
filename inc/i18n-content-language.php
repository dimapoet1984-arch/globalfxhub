<?php
/**
 * Canonical current-language resolution for content-retrieval queries,
 * plus a site-wide pre_get_posts safety net that enforces it.
 *
 * Confirmed live: Portuguese posts appeared on the English homepage.
 * Every WP_Query/get_posts call across the theme that lists posts for
 * display (homepage sections, the blog archive, related articles,
 * Learn/News indexes) relied entirely on Polylang's own implicit
 * pre_get_posts language filtering, with no query asserting its
 * language explicitly and no fallback if that implicit filtering
 * didn't apply for some reason. Rather than depend on fully
 * reconstructing why the implicit filtering missed in that case, this
 * file makes the correct behavior structural: every front-end content
 * query either states its language explicitly (see front-page.php,
 * single.php, page-learn.php, page-news.php, page-news-markets.php),
 * or gets it filled in here.
 *
 * The language itself always comes from Polylang's own per-request
 * language resolution (pll_current_language()), which is backed by
 * the term_language taxonomy -- the actual canonical language field
 * each post/page is assigned in the content model (see
 * pll_get_post_language()) -- never from the URL text.
 *
 * Deliberately scoped to front-end template rendering only: wp-admin
 * and REST API requests are left untouched. The REST collection
 * endpoints (inc/i18n-rest-connector.php) have their own, separate,
 * already-known gap (?lang= isn't wired up there) which this file
 * does not touch, since several things deliberately read across all
 * languages over REST (the Track A translation pipeline's own
 * dry-run/translate scans).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * The canonical language to filter content by for the CURRENT
 * front-end request.
 *
 * Returns:
 * - the resolved language code, normally.
 * - the site's default language, if Polylang is active but the
 *   current language can't be resolved for some reason -- fails safe
 *   to one real language rather than silently falling through to "no
 *   filter", which is exactly how Portuguese content reached the
 *   English homepage.
 * - null if Polylang isn't active at all (single-language install),
 *   so callers know to skip the 'lang' query arg entirely rather than
 *   pass an empty string (which Polylang reads as "all languages").
 */
function globalfxhub_current_content_lang() {
    if ( ! function_exists( 'pll_current_language' ) ) {
        return null;
    }
    $lang = pll_current_language();
    if ( $lang ) {
        return $lang;
    }
    return function_exists( 'pll_default_language' ) ? pll_default_language() : null;
}

/**
 * For any front-end query fetching posts/pages that didn't already
 * set its own 'lang', force it to globalfxhub_current_content_lang().
 *
 * Deliberately never overrides a query that already set 'lang' --
 * isset() on the raw query_vars, not $query->get(), so an explicit
 * 'lang' => '' bypass (the few queries that are legitimately
 * cross-language: inc/sitemap.php's native-post listing,
 * globalfxhub_ensure_blog_page()'s page_for_posts lookup in
 * functions.php) is left alone. This only fills in a gap, it never
 * overrides stated intent -- including Polylang's own pre_get_posts
 * filter, which normally runs first (default priority 10) and already
 * sets 'lang' correctly; this hook at priority 20 only ever has to act
 * when that didn't happen.
 */
function globalfxhub_enforce_content_language( $query ) {
    if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || isset( $query->query_vars['lang'] ) ) {
        return;
    }

    $post_type = $query->get( 'post_type' );
    if ( empty( $post_type ) ) {
        $post_type = 'post'; // WP_Query's own default when unset.
    }
    if ( ! array_intersect( (array) $post_type, array( 'post', 'page' ) ) ) {
        return;
    }

    $lang = globalfxhub_current_content_lang();
    if ( $lang ) {
        $query->set( 'lang', $lang );
    }
}
add_action( 'pre_get_posts', 'globalfxhub_enforce_content_language', 20 );
