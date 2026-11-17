<?php
/**
 * Tests globalfxhub_news_child_page_url() (inc/i18n-templated-page-links.php).
 *
 * Unlike globalfxhub_templated_page_url() (flat top-level pages,
 * "$slug-$lang" string-built), the News hub's "markets"/"brokers"
 * children are nested under that language's own "news-$lang" page, so
 * this helper resolves the real translated post via pll_get_post() +
 * get_permalink() instead of hand-building the URL -- this suite
 * stubs those two WordPress primitives directly rather than
 * reimplementing WordPress's own hierarchical permalink logic.
 *
 * Run: php tests/test-news-child-page-url.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

function add_filter( ...$args ) {}
function home_url( $path = '' ) {
    return 'https://globalfxhub.net' . $path;
}

$GLOBALS['__test_pll_current_language'] = '';
function pll_current_language() {
    return $GLOBALS['__test_pll_current_language'];
}
function pll_default_language() {
    return 'en';
}
function pll_languages_list( $args = array() ) {
    return array( 'es', 'de', 'ar', 'fr', 'pl', 'it', 'pt' );
}

// Minimal page-object + lookup stand-ins. English "news/markets" is
// post ID 501; its Spanish translation ("markets-es", a child of
// "news-es") is post ID 601. "news/brokers" has no Spanish
// translation yet, to exercise the fallback path.
function get_page_by_path( $path ) {
    if ( 'news/markets' === $path ) {
        return (object) array( 'ID' => 501 );
    }
    if ( 'news/brokers' === $path ) {
        return (object) array( 'ID' => 502 );
    }
    return null;
}
function pll_get_post( $post_id, $lang ) {
    if ( 501 === $post_id && 'es' === $lang ) {
        return 601;
    }
    return 0; // no translation for anything else in this test
}
function get_permalink( $post_id ) {
    $permalinks = array(
        501 => 'https://globalfxhub.net/news/markets/',
        502 => 'https://globalfxhub.net/news/brokers/',
        601 => 'https://globalfxhub.net/es/news-es/markets-es/',
    );
    return $permalinks[ $post_id ] ?? false;
}

require __DIR__ . '/../inc/i18n-content-language.php';
require __DIR__ . '/../inc/i18n-track-b-routing.php';
require __DIR__ . '/../inc/i18n-templated-page-links.php';

// --- Case 1: a translation exists -- resolves to its real permalink. ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'https://globalfxhub.net/es/news-es/markets-es/',
    globalfxhub_news_child_page_url( 'markets' ),
    'markets in es resolves to the real translated permalink'
);

// --- Case 2: the default language -- the English page's own permalink. ---
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/news/markets/',
    globalfxhub_news_child_page_url( 'markets' ),
    'default language resolves to the English permalink'
);

// --- Case 3: no translation exists yet for this language (brokers has
// none in this test) -- falls back to the English permalink rather
// than a 404 or an empty string. ---
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'https://globalfxhub.net/news/brokers/',
    globalfxhub_news_child_page_url( 'brokers' ),
    'no translation yet falls back to the English permalink'
);

// --- Case 4: linking to a child page FROM a Track B route itself.
// The recorded request language must win over pll_current_language(). ---
$GLOBALS['globalfxhub_track_b_request_lang'] = 'es';
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/es/news-es/markets-es/',
    globalfxhub_news_child_page_url( 'markets' ),
    'linking from a Track B route uses the recorded request language'
);

assert_test_summary_and_exit();
