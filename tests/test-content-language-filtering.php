<?php
/**
 * Tests inc/i18n-content-language.php's current-language resolution
 * and pre_get_posts safety net with Polylang active -- the fix for
 * Portuguese content appearing on the English homepage, because every
 * content-listing query in the theme relied entirely on Polylang's own
 * implicit language filtering, with no query asserting its language
 * explicitly and no fallback if that implicit filtering didn't apply.
 *
 * No WordPress/Polylang install needed: stubs the handful of Polylang
 * functions globalfxhub_current_content_lang() actually calls, and the
 * minimal WP_Query stand-in from test-helpers.php.
 *
 * Run: php tests/test-content-language-filtering.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

$GLOBALS['__test_current_lang'] = 'en';
$GLOBALS['__test_default_lang'] = 'en';

function pll_current_language() {
    return $GLOBALS['__test_current_lang'];
}
function pll_default_language() {
    return $GLOBALS['__test_default_lang'];
}
function is_admin() {
    return false;
}

require __DIR__ . '/../inc/i18n-content-language.php';

// Every live locale (inc/i18n-live-languages.php's
// GLOBALFXHUB_LIVE_LANGUAGES) must resolve to itself -- the canonical
// field (pll_current_language(), backed by Polylang's term_language
// taxonomy assignment) is the only thing this depends on, never the
// URL.
$live_languages = array( 'en', 'es', 'de', 'ar', 'it', 'pt', 'fr', 'pl' );
foreach ( $live_languages as $lang ) {
    $GLOBALS['__test_current_lang'] = $lang;
    assert_equal( $lang, globalfxhub_current_content_lang(), "current_content_lang resolves '$lang'" );
}

// Polylang active but current language unresolvable -- must fail safe
// to the default language, never to "no filter" (the exact gap that
// let Portuguese content reach the English homepage).
$GLOBALS['__test_current_lang'] = false;
$GLOBALS['__test_default_lang'] = 'en';
assert_equal( 'en', globalfxhub_current_content_lang(), 'falls back to default language when pll_current_language() is empty' );

$GLOBALS['__test_current_lang'] = '';
$GLOBALS['__test_default_lang'] = 'de';
assert_equal( 'de', globalfxhub_current_content_lang(), 'falls back to default language (de) when pll_current_language() is empty string' );

// The safety net: a query with no 'lang' of its own gets the current
// language filled in, for every live locale.
foreach ( $live_languages as $lang ) {
    $GLOBALS['__test_current_lang'] = $lang;
    $q = new Fake_WP_Query( array( 'post_type' => 'post' ) );
    globalfxhub_enforce_content_language( $q );
    assert_equal( $lang, $q->get( 'lang' ), "safety net fills in lang='$lang' for an unset query" );
}

// A query that already set its own 'lang' -- including an intentional
// '' bypass (inc/sitemap.php's native-post listing,
// globalfxhub_ensure_blog_page() in functions.php) -- must be left
// alone, never overridden.
$GLOBALS['__test_current_lang'] = 'pt';
$q = new Fake_WP_Query( array( 'post_type' => 'post', 'lang' => '' ) );
globalfxhub_enforce_content_language( $q );
assert_equal( '', $q->get( 'lang' ), 'safety net never overrides an explicit lang bypass' );

$q = new Fake_WP_Query( array( 'post_type' => 'post', 'lang' => 'es' ) );
globalfxhub_enforce_content_language( $q );
assert_equal( 'es', $q->get( 'lang' ), 'safety net never overrides an explicitly-set lang' );

// A query with no post_type set at all defaults to 'post' (matching
// WP_Query's own default), so the main blog-archive query (home.php /
// template-parts/blog-loop.php) is covered too, not just the
// custom WP_Query() calls in front-page.php/single.php/page-learn.php/
// page-news.php/page-news-markets.php.
$GLOBALS['__test_current_lang'] = 'fr';
$q = new Fake_WP_Query( array() );
globalfxhub_enforce_content_language( $q );
assert_equal( 'fr', $q->get( 'lang' ), 'safety net covers a query with no post_type set (main query default)' );

// 'page' post_type is covered too (container/template pages).
$GLOBALS['__test_current_lang'] = 'ar';
$q = new Fake_WP_Query( array( 'post_type' => 'page' ) );
globalfxhub_enforce_content_language( $q );
assert_equal( 'ar', $q->get( 'lang' ), 'safety net covers post_type=page' );

// A query for an unrelated post type is left alone.
$GLOBALS['__test_current_lang'] = 'ar';
$q = new Fake_WP_Query( array( 'post_type' => 'attachment' ) );
globalfxhub_enforce_content_language( $q );
assert_equal( '', $q->get( 'lang' ), 'safety net does not touch unrelated post types' );

assert_test_summary_and_exit();
