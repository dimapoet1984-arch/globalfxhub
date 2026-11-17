<?php
/**
 * Localization QA for the /regulators/ index page (page-regulators.php's
 * no-slug branch): the eyebrow, breadcrumb/H1 (both now reuse
 * nav_regulators rather than the_title()/a hardcoded literal), the
 * intro paragraph (templated with a live broker count and two
 * pre-built link strings), and each regulator teaser card's stat
 * sentence.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, and that the two
 * templated keys render cleanly via sprintf() in every language with
 * no leftover placeholder.
 *
 * Run: php tests/test-regulators-index-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'regulators_eyebrow',
    'regulators_index_intro',
    'regulators_index_link_regulation',
    'regulators_card_stat',
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

echo "\n--- sprintf render check (templated keys, every language) ---\n";
$render_args = array(
    'regulators_index_intro' => array( 139, '<a href="x">link1</a>', '<a href="y">link2</a>' ),
    'regulators_card_stat'   => array( 112, 139 ),
);
foreach ( $ALL_LANGS as $lang ) {
    foreach ( $render_args as $key => $args ) {
        $template = ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
        $rendered = vsprintf( $template, $args );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
    }
}

assert_test_summary_and_exit();
