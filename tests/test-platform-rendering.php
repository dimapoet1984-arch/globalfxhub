<?php
/**
 * Tests globalfxhub_render_platform_name()/globalfxhub_render_platforms_list()
 * in inc/translatable-strings.php -- the one broker-data field that
 * safely converts to a semantic value + i18n label without changing
 * the underlying data: $broker['platforms'] mixes protected brand
 * names (MT4, MT5, cTrader, TradingView, ...) with the literal
 * English word "Proprietary" as a category descriptor, sometimes with
 * a brand name in parentheses (e.g. "Proprietary (XTrend)").
 *
 * Run: php tests/test-platform-rendering.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

$GLOBALS['__test_current_lang'] = 'en';

function pll_current_language() {
    return $GLOBALS['__test_current_lang'];
}

require __DIR__ . '/../inc/translatable-strings.php';

// Protected brand names must never be touched, in any language.
foreach ( array( 'en', 'es' ) as $lang ) {
    $GLOBALS['__test_current_lang'] = $lang;
    foreach ( array( 'MT4', 'MT5', 'cTrader', 'TradingView', 'ProRealTime' ) as $brand ) {
        assert_equal( $brand, globalfxhub_render_platform_name( $brand ), "'$brand' is never translated (lang=$lang)" );
    }
}

// Bare "Proprietary" (no brand name in parentheses).
$GLOBALS['__test_current_lang'] = 'en';
assert_equal( 'Proprietary', globalfxhub_render_platform_name( 'Proprietary' ), 'bare "Proprietary" stays English in en' );
$GLOBALS['__test_current_lang'] = 'es';
assert_equal( 'Propia', globalfxhub_render_platform_name( 'Proprietary' ), 'bare "Proprietary" translates to "Propia" in es' );

// "Proprietary (BrandName)" -- the descriptor translates, the brand
// name inside the parentheses is preserved exactly, in every real
// shape found in the live dataset.
$cases = array( 'XTrend', 'CAPEX WebTrader', 'Admirals Terminal', 'FXC', "iFOREX/Forex.com" );
foreach ( $cases as $brand ) {
    $GLOBALS['__test_current_lang'] = 'en';
    assert_equal( "Proprietary ($brand)", globalfxhub_render_platform_name( "Proprietary ($brand)" ), "en preserves brand name '$brand'" );
    $GLOBALS['__test_current_lang'] = 'es';
    assert_equal( "Propia ($brand)", globalfxhub_render_platform_name( "Proprietary ($brand)" ), "es preserves brand name '$brand', translates descriptor" );
}

// A whole platforms list -- the drop-in implode() replacement.
$GLOBALS['__test_current_lang'] = 'es';
assert_equal(
    'MT4, Propia (XTrend), cTrader',
    globalfxhub_render_platforms_list( array( 'MT4', 'Proprietary (XTrend)', 'cTrader' ) ),
    'renders a whole mixed platforms list in es'
);
assert_equal(
    'Propia',
    globalfxhub_render_platforms_list( array( 'Proprietary' ) ),
    'renders a single-element platforms list'
);
assert_equal( '', globalfxhub_render_platforms_list( array() ), 'empty platforms list renders empty string' );

assert_test_summary_and_exit();
