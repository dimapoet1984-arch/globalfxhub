<?php
/**
 * Tests globalfxhub_templated_page_url() (inc/i18n-templated-page-links.php).
 *
 * Confirmed live before this fix: every breadcrumb/cross-reference
 * link to one of the 14 templated hub pages' own bare URL (e.g. the
 * "Reviews" breadcrumb segment shown on /es/reviews-es/ itself, or on
 * an individual broker review page) was built with a plain
 * home_url( '/reviews/' ), which always produces the bare English
 * URL regardless of the current page's own language.
 *
 * Mirrors tests/test-track-b-url.php's Case 3: a link to a hub page
 * can be rendered FROM a Track B route (a broker review's own
 * breadcrumb trail), where plain pll_current_language() is confirmed
 * to return the wrong language -- globalfxhub_templated_page_url()
 * must still get it right there too, via
 * globalfxhub_track_b_current_lang().
 *
 * Run: php tests/test-templated-page-url.php
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

require __DIR__ . '/../inc/i18n-content-language.php';
require __DIR__ . '/../inc/i18n-track-b-routing.php';
require __DIR__ . '/../inc/i18n-templated-page-links.php';

// --- Case 1: a normal page, Polylang resolves the current language
// normally via pll_current_language(). ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'https://globalfxhub.net/es/reviews-es/',
    globalfxhub_templated_page_url( 'reviews' ),
    'normal page in es builds the "$slug-$lang"-suffixed link'
);

// --- Case 2: the default language -- bare, unsuffixed link. ---
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/reviews/',
    globalfxhub_templated_page_url( 'reviews' ),
    'default language builds the bare link'
);

// --- Case 3: linking to a hub page FROM a Track B route itself (e.g.
// a broker review's own breadcrumb trail). The recorded Track B
// request language must win over pll_current_language(), which per
// the documented bug would (wrongly) say 'en' here. ---
$GLOBALS['globalfxhub_track_b_request_lang'] = 'de';
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/de/best-de/',
    globalfxhub_templated_page_url( 'best' ),
    'linking to a hub page from a Track B route uses the recorded request language'
);

// --- Case 4: a multi-word slug (broker-finder, cost-calculator, ...)
// is suffixed the same way, not split on its own hyphens. ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );
$GLOBALS['__test_pll_current_language'] = 'pt';
assert_equal(
    'https://globalfxhub.net/pt/broker-finder-pt/',
    globalfxhub_templated_page_url( 'broker-finder' ),
    'a hyphenated slug is suffixed correctly'
);

assert_test_summary_and_exit();
