<?php
/**
 * Tests globalfxhub_learn_article_url() (inc/i18n-templated-page-links.php).
 *
 * Confirmed live: page-learn.php's own article grid, inc/learn-articles.php's
 * cluster-sibling and review/tool cross-links, and page-regulators.php's
 * "full regulation guide" link all built a Learn article's URL as
 * home_url( '/learn/' . $slug . '/' ), which always produces the bare
 * English URL regardless of the current page's own language -- same
 * bug class as globalfxhub_news_child_page_url(), just for a real post
 * with its own organically-translated slug once translated, rather
 * than a flat "$slug-$lang" pattern.
 *
 * Run: php tests/test-learn-article-url.php
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

// Post 701: "how-to-choose-a-forex-broker", translated into es as
// post 801 with its own organically-translated slug. Post 702:
// "forex-broker-fees-explained", no translation yet.
function get_page_by_path( $path, $output = OBJECT, $post_type = 'page' ) {
    if ( 'how-to-choose-a-forex-broker' === $path ) {
        return (object) array( 'ID' => 701 );
    }
    if ( 'forex-broker-fees-explained' === $path ) {
        return (object) array( 'ID' => 702 );
    }
    return null;
}
if ( ! defined( 'OBJECT' ) ) {
    define( 'OBJECT', 'OBJECT' );
}
function pll_get_post( $post_id, $lang ) {
    if ( 701 === $post_id && 'es' === $lang ) {
        return 801;
    }
    return 0;
}
function get_permalink( $post ) {
    $post_id = is_object( $post ) ? $post->ID : $post;
    $permalinks = array(
        701 => 'https://globalfxhub.net/learn/how-to-choose-a-forex-broker/',
        702 => 'https://globalfxhub.net/learn/forex-broker-fees-explained/',
        801 => 'https://globalfxhub.net/es/como-elegir-un-broker-de-forex/',
    );
    return $permalinks[ $post_id ] ?? false;
}
function get_the_title( $post_id ) {
    $titles = array(
        701 => 'How to Choose a Forex Broker',
        702 => 'Forex Broker Fees Explained',
        801 => 'Cómo elegir un bróker de forex',
    );
    return $titles[ $post_id ] ?? '';
}

require __DIR__ . '/../inc/i18n-content-language.php';
require __DIR__ . '/../inc/i18n-track-b-routing.php';
require __DIR__ . '/../inc/i18n-templated-page-links.php';

// --- Case 1: a translation exists -- resolves to its real,
// organically-translated permalink (not a predictable slug pattern). ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'https://globalfxhub.net/es/como-elegir-un-broker-de-forex/',
    globalfxhub_learn_article_url( 'how-to-choose-a-forex-broker' ),
    'a translated article resolves to its real translated permalink'
);

// --- Case 2: the default language -- the English permalink. ---
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/learn/how-to-choose-a-forex-broker/',
    globalfxhub_learn_article_url( 'how-to-choose-a-forex-broker' ),
    'default language resolves to the English permalink'
);

// --- Case 3: no translation exists yet -- falls back to the English
// permalink (the currently-live case for every Learn article). ---
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'https://globalfxhub.net/learn/forex-broker-fees-explained/',
    globalfxhub_learn_article_url( 'forex-broker-fees-explained' ),
    'no translation yet falls back to the English permalink'
);

// --- Case 4: passing an already-fetched post object skips the lookup
// (same post 701) and still resolves correctly. ---
$already_fetched = (object) array( 'ID' => 701 );
assert_equal(
    'https://globalfxhub.net/es/como-elegir-un-broker-de-forex/',
    globalfxhub_learn_article_url( 'how-to-choose-a-forex-broker', $already_fetched ),
    'an already-fetched post object is used directly'
);

// --- Case 5: linking FROM a Track B route itself. The recorded
// request language must win over pll_current_language(). ---
$GLOBALS['globalfxhub_track_b_request_lang'] = 'es';
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'https://globalfxhub.net/es/como-elegir-un-broker-de-forex/',
    globalfxhub_learn_article_url( 'how-to-choose-a-forex-broker' ),
    'linking from a Track B route uses the recorded request language'
);

// --- globalfxhub_learn_article_title(): the real, translated
// post_title for a Learn article -- not a static lookup-table label
// (the bug fixed in inc/learn-articles.php's cluster-sibling footer
// and globalfxhub_resolve_learn_link(), both of which previously used
// their own English 'title'/'label' text regardless of the viewer's
// language, even though the link's HREF was already correctly
// translated). ---
unset( $GLOBALS['globalfxhub_track_b_request_lang'] );

// --- Case 6: a translation exists -- the TRANSLATED post's own title,
// not the English fallback label passed in. ---
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'Cómo elegir un bróker de forex',
    globalfxhub_learn_article_title( 'how-to-choose-a-forex-broker', null, 'How to choose a forex broker (fallback)' ),
    'a translated article resolves to its own translated title, not the fallback label'
);

// --- Case 7: default language -- the English post's own title. ---
$GLOBALS['__test_pll_current_language'] = 'en';
assert_equal(
    'How to Choose a Forex Broker',
    globalfxhub_learn_article_title( 'how-to-choose-a-forex-broker', null, 'fallback' ),
    'default language resolves to the English post\'s own title'
);

// --- Case 8: no translation yet -- falls back to the English post's
// own real title (NOT the $fallback_label param, which is reserved for
// when the post can't be resolved at all). ---
$GLOBALS['__test_pll_current_language'] = 'es';
assert_equal(
    'Forex Broker Fees Explained',
    globalfxhub_learn_article_title( 'forex-broker-fees-explained', null, 'fallback should not be used' ),
    'no translation yet falls back to the English post\'s own real title, not the static fallback label'
);

// --- Case 9: the slug doesn't resolve to any real post at all -- only
// now does $fallback_label get used. ---
assert_equal(
    'fallback label',
    globalfxhub_learn_article_title( 'this-slug-does-not-exist', null, 'fallback label' ),
    'an unresolvable slug uses the fallback label'
);

// --- Case 10: an already-fetched post object is used directly, same
// as globalfxhub_learn_article_url(). ---
assert_equal(
    'Cómo elegir un bróker de forex',
    globalfxhub_learn_article_title( 'how-to-choose-a-forex-broker', (object) array( 'ID' => 701 ), 'fallback' ),
    'an already-fetched post object is used directly'
);

assert_test_summary_and_exit();
