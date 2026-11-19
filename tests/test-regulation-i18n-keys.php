<?php
/**
 * Localization QA for the /regulation/ index and /regulation/{slug}/
 * pages (page-regulation.php): the shared eyebrow, the index page's
 * intro/meta description, and the two "see all regulation pages
 * [, or every broker ... under Best Brokers]" sentences. The
 * individual regulation pages' title/summary/content (now read via
 * globalfxhub_tf()) and the regulation-comparison page (already
 * localized via globalfxhub_get_localized_regulation_comparison())
 * are Track B/pre-existing content, not new UI-string keys, so
 * they're out of scope for this key-completeness test.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, and that every
 * templated key renders cleanly via sprintf() in every language with
 * no leftover placeholder and no stray '%' character.
 *
 * Run: php tests/test-regulation-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'regulation_eyebrow', 'regulation_index_intro', 'regulation_meta_desc_hub',
    'regulation_link_text_all_pages', 'regulation_see_all_pages_simple',
    'regulation_see_all_pages_and_best',
);

$SINGLE_ARG = array(
    'regulation_see_all_pages_simple' => '<a>x</a>',
);
$POSITIONAL = array(
    'regulation_see_all_pages_and_best' => array( '<a>x</a>', '<a>y</a>' ),
);
$PLAIN = array_values( array_diff( $KEYS, array_keys( $SINGLE_ARG ), array_keys( $POSITIONAL ) ) );

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

assert_equal( 6, count( $KEYS ), 'exactly 6 new regulation-page keys are checked' );
assert_equal( 6, count( $PLAIN ) + count( $SINGLE_ARG ) + count( $POSITIONAL ), 'every key is classified exactly once' );

echo "\n--- sprintf render check (templated keys, every language) ---\n";
foreach ( $ALL_LANGS as $lang ) {
    $get = function( $key ) use ( $lang, $live ) {
        return ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
    };

    foreach ( $SINGLE_ARG as $key => $arg ) {
        $rendered = sprintf( $get( $key ), $arg );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
    foreach ( $POSITIONAL as $key => $args ) {
        $rendered = vsprintf( $get( $key ), $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
    foreach ( array_merge( $SINGLE_ARG, $POSITIONAL ) as $key => $args ) {
        $rendered = is_array( $args ) ? vsprintf( $get( $key ), $args ) : sprintf( $get( $key ), $args );
        assert_equal( 0, substr_count( $rendered, '%' ), "'$key' in '$lang' leaves no stray '%' character" );
    }
}

assert_test_summary_and_exit();
