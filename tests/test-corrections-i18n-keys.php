<?php
/**
 * Localization QA for the /corrections/ page (page-corrections.php):
 * the eyebrow, intro paragraph, empty state, table header, and
 * per-row "Previously:"/"Corrected to:"/"Why:"/"Source" labels.
 *
 * The log itself is currently empty (globalfxhub_get_corrections()
 * returns no entries yet -- see inc/corrections-log.php), so there is
 * no per-entry free text to wire through Track B yet; that's flagged
 * in a code comment there for whenever the first real entry is added,
 * not covered by this test.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, and that every
 * templated key renders cleanly via sprintf() in every language with
 * no leftover placeholder and no stray '%' character.
 *
 * Run: php tests/test-corrections-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'corrections_eyebrow', 'corrections_intro', 'corrections_link_text_changelog',
    'corrections_empty', 'corrections_link_text_let_us_know',
    'corrections_th_date', 'corrections_th_what',
    'corrections_label_previously', 'corrections_label_corrected_to',
    'corrections_label_why', 'corrections_source_link_text',
);

$POSITIONAL = array(
    'corrections_intro' => array( '<a>policy</a>', '<a>changelog</a>' ),
    'corrections_empty' => array( '<a>changelog</a>', '<a>contact</a>' ),
);
$PLAIN = array_values( array_diff( $KEYS, array_keys( $POSITIONAL ) ) );

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

assert_equal( 11, count( $KEYS ), 'exactly 11 new corrections-page keys are checked' );
assert_equal( 11, count( $PLAIN ) + count( $POSITIONAL ), 'every key is classified exactly once' );

echo "\n--- sprintf render check (templated keys, every language) ---\n";
foreach ( $ALL_LANGS as $lang ) {
    $get = function( $key ) use ( $lang, $live ) {
        return ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
    };

    foreach ( $POSITIONAL as $key => $args ) {
        $rendered = vsprintf( $get( $key ), $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
        assert_equal( 0, substr_count( $rendered, '%' ), "'$key' in '$lang' leaves no stray '%' character" );
    }
}

assert_test_summary_and_exit();
