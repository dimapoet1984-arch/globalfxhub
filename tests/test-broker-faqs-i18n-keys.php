<?php
/**
 * Localization QA for the broker review page's FAQ section
 * (globalfxhub_broker_faqs() in functions.php), which feeds both the
 * on-page FAQ display (page-reviews.php) and the FAQPage JSON-LD
 * schema (globalfxhub_review_seo_head()) from the same source array --
 * same finite sprintf-template pattern as the pros/cons,
 * investor-protection, and who-for keys fixed just before this.
 *
 * Checks every key is registered, has a real English source, has a
 * non-empty translation in all 7 live languages, every templated key
 * renders cleanly via sprintf() in every language, and -- specific to
 * this file, since rev_faq_a_score_calc is the one key on this whole
 * page with literal "%" characters in its own text (the methodology
 * percentages) -- that every language's %% escaping survives a real
 * sprintf() call as exactly 9 literal "%" characters, never doubled
 * and never swallowed as a stray format specifier.
 *
 * Run: php tests/test-broker-faqs-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'rev_faq_q_regulated', 'rev_faq_a_regulated', 'rev_faq_q_money_protected',
    'rev_faq_q_min_deposit', 'rev_faq_a_min_deposit', 'rev_faq_not_confirmed',
    'rev_faq_q_platforms', 'rev_faq_a_platforms', 'rev_faq_q_spread',
    'rev_faq_a_spread', 'rev_faq_q_founded', 'rev_faq_a_founded',
    'rev_faq_q_score_calc', 'rev_faq_a_score_calc',
);

// Single-%s templates, rendered with one placeholder arg.
$SINGLE_S_KEYS = array(
    'rev_faq_q_regulated', 'rev_faq_a_regulated', 'rev_faq_q_money_protected',
    'rev_faq_q_min_deposit', 'rev_faq_a_min_deposit', 'rev_faq_q_platforms',
    'rev_faq_q_spread', 'rev_faq_a_spread', 'rev_faq_q_founded', 'rev_faq_q_score_calc',
);
// Positional (%1$s/%2$s/%2$d) templates, with their own arg sets.
$POSITIONAL = array(
    'rev_faq_a_platforms'  => array( 'AvaTrade', 'MT4, MT5' ),
    'rev_faq_a_founded'    => array( 'AvaTrade', 2006 ),
    'rev_faq_a_score_calc' => array( 'AvaTrade', '4.12' ),
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
        $rendered = sprintf( $get( $key ), 'AvaTrade' );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
    foreach ( $POSITIONAL as $key => $args ) {
        $rendered = vsprintf( $get( $key ), $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
}

echo "\n--- rev_faq_a_score_calc: literal \"%\" survives %% escaping in every language ---\n";
foreach ( $ALL_LANGS as $lang ) {
    $template = ( 'en' === $lang ) ? globalfxhub_string_text( 'rev_faq_a_score_calc' ) : $live[ $lang ]['rev_faq_a_score_calc'];
    $rendered = vsprintf( $template, array( 'AvaTrade', '4.12' ) );
    assert_equal( 9, substr_count( $rendered, '%' ), "'rev_faq_a_score_calc' in '$lang' renders exactly 9 literal '%' characters" );
    assert_equal( false, strpos( $rendered, '%%' ) !== false, "'rev_faq_a_score_calc' in '$lang' leaves no doubled '%%'" );
}

assert_test_summary_and_exit();
