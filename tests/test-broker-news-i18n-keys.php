<?php
/**
 * Localization QA for the /news/brokers/ index and
 * /news/brokers/{slug}/ pages (page-news-brokers.php): the shared
 * eyebrow, the not-found state, the 9 category labels plus their
 * "Update" fallback, the "about this broker"/"read full review"
 * sentence, the Sources heading, the back-to-feed link, the index
 * intro, and the empty state. The per-item headline/body/
 * article.paragraphs[] fields (now read via globalfxhub_tf()) are
 * Track B content, not new UI-string keys, so they're out of scope
 * for this key-completeness test.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, and that every
 * templated key renders cleanly via sprintf() in every language with
 * no leftover placeholder and no stray '%' character.
 *
 * Run: php tests/test-broker-news-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'broker_news_eyebrow', 'broker_news_not_found_h1', 'broker_news_not_found_body',
    'broker_news_link_text_see_feed',
    'bn_cat_acquisition', 'bn_cat_license', 'bn_cat_license_withdrawal', 'bn_cat_fine',
    'bn_cat_enforcement', 'bn_cat_platform', 'bn_cat_executive', 'bn_cat_product',
    'bn_cat_outage', 'bn_cat_fallback',
    'broker_news_about_broker', 'broker_news_link_text_read_review',
    'broker_news_sources_h2', 'broker_news_back_to_feed',
    'broker_news_index_intro', 'broker_news_empty',
);

$SINGLE_ARG = array(
    'broker_news_not_found_body' => '<a>x</a>',
    'broker_news_link_text_read_review' => 'XTB',
    'broker_news_index_intro' => '<a>x</a>',
);
$POSITIONAL = array(
    'broker_news_about_broker' => array( 'XTB', '1', '4.5', '<a>x</a>' ),
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

assert_equal( 20, count( $KEYS ), 'exactly 20 new broker-news keys are checked' );
assert_equal( 20, count( $PLAIN ) + count( $SINGLE_ARG ) + count( $POSITIONAL ), 'every key is classified exactly once' );

// broker_news_link_text_read_review and broker_news_back_to_feed carry
// a literal HTML entity (&rarr;/&larr;) in their own text -- confirm it
// survives untouched (no stray '&amp;' double-escaping marker) and that
// sprintf leaves no unresolved placeholder.
echo "\n--- sprintf render + entity-safety check (every templated key, every language) ---\n";
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

    assert_equal( true, false !== strpos( $get( 'broker_news_link_text_read_review' ), '&rarr;' ), "'broker_news_link_text_read_review' in '$lang' keeps the literal &rarr; entity" );
    assert_equal( false, false !== strpos( $get( 'broker_news_link_text_read_review' ), '&amp;rarr;' ), "'broker_news_link_text_read_review' in '$lang' does not double-escape &rarr;" );
    assert_equal( true, false !== strpos( $get( 'broker_news_back_to_feed' ), '&larr;' ), "'broker_news_back_to_feed' in '$lang' keeps the literal &larr; entity" );
    assert_equal( false, false !== strpos( $get( 'broker_news_back_to_feed' ), '&amp;larr;' ), "'broker_news_back_to_feed' in '$lang' does not double-escape &larr;" );
}

assert_test_summary_and_exit();
