<?php
/**
 * Localization QA for the broker review page's "who this suits" / "who
 * should look elsewhere" sentences (globalfxhub_broker_who_for() in
 * functions.php) -- same finite sprintf-template pattern as the
 * pros/cons and investor-protection keys (see
 * tests/test-broker-pros-cons-i18n-keys.php), computed per-broker from
 * already-confirmed data fields rather than free text from the broker
 * dataset.
 *
 * Checks every key is registered in the keylist, has a real English
 * source, has a non-empty translation in all 7 live languages, and
 * that the one templated key's placeholders survive a real sprintf()
 * call in every language without leaving an unresolved %d/%s.
 *
 * Run: php tests/test-broker-who-for-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'rev_wf_beginners_low_deposit', 'rev_wf_well_capitalized', 'rev_wf_not_beginners_high_deposit',
    'rev_wf_platform_choice', 'rev_wf_wants_metatrader', 'rev_wf_needs_eu_uk_protection',
    'rev_wf_offshore_comfortable', 'rev_wf_eu_uk_protection_wanted', 'rev_wf_reg_breadth',
    'rev_wf_cost_sensitive_good', 'rev_wf_cost_sensitive_bad', 'rev_wf_not_enough_data',
    'rev_wf_no_against_signal',
);

$RENDER_ARGS = array(
    'rev_wf_platform_choice' => array( 3, 'MT4, MT5' ),
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
    foreach ( array_merge( array( 'en' ), $LIVE_LANGS ) as $lang ) {
        $template = ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
        $rendered = vsprintf( $template, $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
}

assert_test_summary_and_exit();
