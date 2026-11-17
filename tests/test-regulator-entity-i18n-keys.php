<?php
/**
 * Localization QA for the individual regulator-entity detail page
 * (page-regulators.php's $regulator_slug branch): the "not available"
 * state, and the real entity page's eyebrow, H1, stats paragraph,
 * facts table labels, related-content links, licensed-broker roster
 * heading/disclosure, and roster table -- fixed alongside the
 * /regulators/ index page (test-regulators-index-i18n-keys.php).
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, every templated
 * key renders cleanly via sprintf() in every language, and that
 * regulator_entity_stats_para's %% escaping (the one key on this page
 * with a literal "%" in its own text) survives as exactly one literal
 * "%" character with no doubling, in every language.
 *
 * Run: php tests/test-regulator-entity-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'regulator_not_available_h1', 'regulator_not_available_body', 'regulator_see_all_link',
    'regulator_entity_eyebrow', 'regulator_entity_h1', 'regulator_entity_stats_para',
    'regulator_th_jurisdiction', 'regulator_th_leverage', 'regulator_th_compensation',
    'regulator_no_compensation_scheme', 'regulator_th_complaint_process', 'regulator_th_official_register',
    'regulator_search_register_link', 'regulator_see_explainer', 'regulator_link_text_explainer',
    'regulator_link_text_guide', 'regulator_see_best_list', 'regulator_link_text_best',
    'regulator_roster_h2', 'regulator_entity_disclosure_tracked', 'regulator_entity_disclosure_untracked',
    'regulator_no_brokers_hold_licence', 'regulator_th_licence_number', 'regulator_th_status',
    'regulator_licence_held_untracked',
);

// Single-%s templates, rendered with one placeholder arg.
$SINGLE_S_KEYS = array(
    'regulator_not_available_body', 'regulator_entity_h1', 'regulator_search_register_link',
    'regulator_see_explainer', 'regulator_link_text_explainer', 'regulator_link_text_guide',
    'regulator_see_best_list', 'regulator_link_text_best', 'regulator_roster_h2',
    'regulator_entity_disclosure_untracked',
);
// Positional (%1$s..%4$s) template, with its own arg set.
$POSITIONAL = array(
    'regulator_entity_stats_para' => array( 'CySEC', 95, 139, '68' ),
);

$LIVE_LANGS = array( 'es', 'de', 'ar', 'fr', 'pl', 'it', 'pt' );
$ALL_LANGS  = array_merge( array( 'en' ), $LIVE_LANGS );

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
foreach ( $ALL_LANGS as $lang ) {
    $table = ( 'en' === $lang ) ? null : $live[ $lang ];
    $get = function( $key ) use ( $lang, $table ) {
        return ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $table[ $key ];
    };

    foreach ( $SINGLE_S_KEYS as $key ) {
        $rendered = sprintf( $get( $key ), 'X' );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
    foreach ( $POSITIONAL as $key => $args ) {
        $rendered = vsprintf( $get( $key ), $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
        assert_equal( 1, substr_count( $rendered, '%' ), "'$key' in '$lang' renders exactly 1 literal '%' character" );
    }
}

assert_test_summary_and_exit();
