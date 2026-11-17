<?php
/**
 * Tests globalfxhub_track_b_url() (inc/i18n-track-b-routing.php).
 *
 * Confirmed live before this fix: every internal link to a Track B
 * route (reviews/{slug}, best/{slug}, regulation/{slug},
 * regulators/{slug}, countries/{slug}, compare/{a}-vs-{b},
 * news/brokers/{item}, research/{slug}) was built with a plain
 * home_url( '/reviews/' . $slug . '/' ) (etc.), which always produces
 * the bare English URL regardless of the current page's own language
 * -- 139/139 broker-review links on both /es/reviews-es/ and
 * /de/reviews-de/ pointed at the English review.
 *
 * The critical case this suite exists to guard is Case 3 below:
 * globalfxhub_resolve_track_b_container_language() in the same file
 * documents, and confirms live, that pll_current_language() itself
 * returns the WRONG language when the CURRENT request is already a
 * Track B route (Polylang's own curlang detection never recognizes
 * these custom rewrite-rule URLs as language-bearing at all) -- so
 * globalfxhub_track_b_url() must prefer
 * $GLOBALS['globalfxhub_track_b_request_lang'] over
 * pll_current_language() whenever that global is set, or a link built
 * FROM a Track B page (e.g. one broker review linking to another)
 * would silently revert to English again.
 *
 * Run: php tests/test-track-b-url.php
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

// --- Case 1: a normal page (not a Track B route) -- Polylang resolves
// the current language normally via pll_current_language(). ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'https://globalfxhub.net/es/reviews/avatrade/',
    globalfxhub_track_b_url( 'reviews/avatrade/' ),
    'normal page in es builds an /es/ prefixed link'
);

// --- Case 2: a normal page in the default language (en) -- no prefix. ---
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/reviews/avatrade/',
    globalfxhub_track_b_url( 'reviews/avatrade/' ),
    'normal page in en builds a bare link'
);

// --- Case 3: ON a Track B route itself (e.g. rendering
// /es/reviews/avatrade/ and linking to a different review, like
// "similar brokers"). The recorded request language must win over
// pll_current_language(), which per the documented bug would (wrongly)
// say 'en' here. ---
$GLOBALS['globalfxhub_track_b_request_lang'] = 'es';
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/es/reviews/exness/',
    globalfxhub_track_b_url( 'reviews/exness/' ),
    'on a Track B route itself, the recorded request language wins over pll_current_language()'
);

// --- Case 4: ON a Track B route whose URL requested the default
// language (en) -- bare link, no prefix. ---
$GLOBALS['globalfxhub_track_b_request_lang'] = 'en';
assert_equal(
    'https://globalfxhub.net/reviews/exness/',
    globalfxhub_track_b_url( 'reviews/exness/' ),
    'on a Track B route requesting en, builds a bare link'
);

// --- Case 5: compare pair route, 2 variables. ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );
$GLOBALS['__test_pll_current_language'] = 'fr';
assert_equal(
    'https://globalfxhub.net/fr/compare/ig-vs-cmc-markets/',
    globalfxhub_track_b_url( 'compare/ig-vs-cmc-markets/' ),
    'compare pair route in fr'
);

// The Polylang-inactive case (globalfxhub_current_content_lang()
// returning null) is already covered by
// test-content-language-filtering-no-polylang.php -- this helper
// falls straight through to that same function for a non-Track-B
// request, so it's not re-tested here.
assert_test_summary_and_exit();
