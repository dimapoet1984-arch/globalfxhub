<?php
/**
 * Localization QA for the broker review page's pros/cons and investor-
 * protection sentences (globalfxhub_broker_pros_cons() /
 * globalfxhub_broker_investor_protection() in functions.php). These are
 * computed per-broker from already-confirmed data fields (regulator
 * counts, deposit/spread, platform list, founding year) rather than
 * free text from the broker dataset, so they're translated as finite
 * sprintf-template UI strings in inc/translatable-strings.php -- same
 * mechanism as every other rev_* key, not the Track B per-broker
 * content pipeline (globalfxhub_tf()) the dataset's free-text fields
 * use.
 *
 * Checks every key is registered in the keylist, has a real English
 * source (not silently echoing the raw key), has a non-empty
 * translation in all 7 live languages, and that every templated key's
 * placeholders survive a real sprintf() call in every language without
 * leaving an unresolved %s/%d or throwing.
 *
 * Run: php tests/test-broker-pros-cons-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'rev_pc_base_cysec', 'rev_pc_base_cysec_fca', 'rev_pc_base_eu_mifid',
    'rev_pc_base_eu_mifid_fca', 'rev_pc_base_fca', 'rev_pc_base_seychelles',
    'rev_pc_reg_wide', 'rev_pc_reg_thin', 'rev_pc_no_tier1',
    'rev_pc_cost_unconfirmed', 'rev_pc_cost_low', 'rev_pc_cost_high',
    'rev_pc_platforms_broad', 'rev_pc_no_metatrader', 'rev_pc_track_unconfirmed',
    'rev_pc_track_long', 'rev_pc_track_short', 'rev_pc_no_weaknesses',
    'rev_ip_fca', 'rev_ip_eu_mifid', 'rev_ip_cysec', 'rev_ip_seychelles',
    'rev_ip_none_found',
);

// Placeholder args for the keys that are sprintf templates, used to
// prove they actually render -- missing from this list means the key
// is a plain, no-placeholder sentence.
$RENDER_ARGS = array(
    'rev_pc_reg_wide'        => array( 'CySEC', 3, 'ASIC, FSCA' ),
    'rev_pc_cost_low'        => array( '$10', 0.9 ),
    'rev_pc_platforms_broad' => array( 'MT4, MT5' ),
    'rev_pc_track_long'      => array( 2001 ),
    'rev_pc_track_short'     => array( 2020 ),
);

$LIVE_LANGS = array( 'es', 'de', 'ar', 'fr', 'pl', 'it', 'pt' );

$all_keys = globalfxhub_translatable_strings();
$live     = globalfxhub_live_string_translations();

foreach ( $KEYS as $key ) {
    assert_equal( true, in_array( $key, $all_keys, true ), "'$key' is registered in globalfxhub_translatable_strings()" );

    $en = globalfxhub_string_text( $key );
    assert_equal( true, $en !== $key && '' !== $en, "'$key' has a real English source" );

    foreach ( $LIVE_LANGS as $lang ) {
        $value = $live[ $lang ][ $key ] ?? null;
        assert_equal( true, null !== $value && '' !== $value, "'$key' has a non-empty '$lang' translation" );
    }
}

echo "\n--- sprintf render check (every templated key, every language) ---\n";
foreach ( $RENDER_ARGS as $key => $args ) {
    $tables = array_merge( array( 'en' => null ), array_fill_keys( $LIVE_LANGS, null ) );
    foreach ( array_keys( $tables ) as $lang ) {
        $template = ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
        $rendered = vsprintf( $template, $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
}

assert_test_summary_and_exit();
