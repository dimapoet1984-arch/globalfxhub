<?php
/**
 * Localization QA for globalfxhub_broker_regulation_label()
 * (functions.php) -- the regulator-summary string ("CySEC No. 309/16
 * + FCA No. 195355") shown on the review page's own eyebrow/tag, in
 * every FAQ's "Is X regulated?" answer, and reused by
 * inc/broker-vs.php, inc/broker-finder.php, and the review index.
 *
 * Its three "unconfirmed"/"EU-regulated" branches were already
 * translated as cmp_reg_eu_mifid / cmp_reg_fca_unconfirmed /
 * cmp_reg_seychelles_unconfirmed / cmp_reg_not_confirmed (built
 * earlier for page-compare.php's JS mirror of this same logic) -- this
 * only had to add the three number-interpolated branches
 * (rev_reglabel_cysec_no / _fca_no / _seychelles_no) that function
 * needed but the compare tool's JS never did on its own (it builds
 * "CySEC No. ${x.cysec}" with a raw JS template literal, also fixed
 * alongside this to use the same three new keys via
 * T.key.replace('%s', ...), matching page-broker-finder.php's existing
 * pattern for reusing a sprintf-style key in JS).
 *
 * Run: php tests/test-broker-regulation-label-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$NEW_KEYS = array( 'rev_reglabel_cysec_no', 'rev_reglabel_fca_no', 'rev_reglabel_seychelles_no' );
$REUSED_KEYS = array( 'cmp_reg_eu_mifid', 'cmp_reg_fca_unconfirmed', 'cmp_reg_seychelles_unconfirmed', 'cmp_reg_not_confirmed' );

$LIVE_LANGS = array( 'es', 'de', 'ar', 'fr', 'pl', 'it', 'pt' );

$all_keys = globalfxhub_translatable_strings();
$live     = globalfxhub_live_string_translations();

foreach ( array_merge( $NEW_KEYS, $REUSED_KEYS ) as $key ) {
    assert_equal( true, in_array( $key, $all_keys, true ), "'$key' is registered in globalfxhub_translatable_strings()" );

    $en = globalfxhub_string_text( $key );
    assert_equal( true, $en !== $key && '' !== $en, "'$key' has a real English source" );

    foreach ( $LIVE_LANGS as $lang ) {
        $value = $live[ $lang ][ $key ] ?? null;
        assert_equal( true, null !== $value && '' !== $value, "'$key' has a non-empty '$lang' translation" );
    }
}

echo "\n--- sprintf render check (the 3 new number-interpolated keys, every language) ---\n";
foreach ( array_merge( array( 'en' ), $LIVE_LANGS ) as $lang ) {
    foreach ( $NEW_KEYS as $key ) {
        $template = ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
        $rendered = sprintf( $template, '309/16' );
        assert_equal( true, false !== strpos( $rendered, '309/16' ), "'$key' in '$lang' keeps the licence number intact after sprintf" );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
}

assert_test_summary_and_exit();
