<?php
/**
 * Tests inc/i18n-content-language.php with Polylang NOT active (no
 * pll_* functions defined, as on a single-language install) -- it
 * must stay a complete no-op, never forcing a 'lang' filter when
 * there's no Polylang language to filter by.
 *
 * Run: php tests/test-content-language-filtering-no-polylang.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

function is_admin() {
    return false;
}

require __DIR__ . '/../inc/i18n-content-language.php';

assert_equal( null, globalfxhub_current_content_lang(), 'returns null when Polylang is not active' );

$q = new Fake_WP_Query( array( 'post_type' => 'post' ) );
globalfxhub_enforce_content_language( $q );
assert_equal( '', $q->get( 'lang' ), 'safety net stays a no-op with Polylang inactive' );

assert_test_summary_and_exit();
