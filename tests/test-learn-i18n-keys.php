<?php
/**
 * Localization QA for the /learn/ index page (page-learn.php) and the
 * learn-cluster system shared with inc/learn-articles.php's related-
 * articles footer: eyebrow, intro, empty/general-guides states, the
 * "X MIN READ" label (also reused on front-page.php and
 * template-parts/blog-loop.php), and each of the 10 clusters'
 * title/description -- globalfxhub_learn_clusters() now returns these
 * translated via globalfxhub_t() instead of hardcoded English.
 *
 * Individual learn articles themselves (post_title/post_content/etc.)
 * are real WP posts translated via Polylang (Track A) -- not covered
 * here, and not a gap: page-learn.php's WP_Query already filters by
 * globalfxhub_current_content_lang(). Also not covered here (no WP
 * runtime available in this standalone script): the
 * globalfxhub_learn_article_resolved_id()/globalfxhub_learn_article_title()
 * helpers in inc/i18n-templated-page-links.php, which fixed a real bug
 * where a related-article link's visible text came from a static
 * English lookup-table entry instead of that article's own translated
 * post_title.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, that every
 * templated key renders cleanly via sprintf() in every language with
 * no leftover placeholder and no stray '%' character, and that
 * globalfxhub_learn_clusters() returns exactly the 10 expected
 * cluster slugs each with a non-empty title/description.
 *
 * Run: php tests/test-learn-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

function get_template_directory() {
    return dirname( __DIR__ );
}
function add_filter( ...$args ) {}

require __DIR__ . '/../inc/translatable-strings.php';
require __DIR__ . '/../inc/learn-articles.php';

$KEYS = array(
    'learn_eyebrow', 'learn_index_intro', 'learn_no_guides_yet', 'learn_general_guides_h2',
    'guide_min_read', 'learn_more_in_cluster',
    'learn_cluster_forex_fundamentals_title', 'learn_cluster_forex_fundamentals_desc',
    'learn_cluster_leverage_margin_risk_title', 'learn_cluster_leverage_margin_risk_desc',
    'learn_cluster_choosing_a_broker_title', 'learn_cluster_choosing_a_broker_desc',
    'learn_cluster_broker_regulation_title', 'learn_cluster_broker_regulation_desc',
    'learn_cluster_fees_and_costs_title', 'learn_cluster_fees_and_costs_desc',
    'learn_cluster_trading_platforms_title', 'learn_cluster_trading_platforms_desc',
    'learn_cluster_orders_and_execution_title', 'learn_cluster_orders_and_execution_desc',
    'learn_cluster_analysis_and_economics_title', 'learn_cluster_analysis_and_economics_desc',
    'learn_cluster_trading_concepts_title', 'learn_cluster_trading_concepts_desc',
    'learn_cluster_safety_and_due_diligence_title', 'learn_cluster_safety_and_due_diligence_desc',
);

$SINGLE_ARG = array(
    'guide_min_read' => 5,
    'learn_more_in_cluster' => 'Forex Fundamentals',
);
$PLAIN = array_values( array_diff( $KEYS, array_keys( $SINGLE_ARG ) ) );

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

assert_equal( 26, count( $KEYS ), 'exactly 26 new learn-page keys are checked' );
assert_equal( 26, count( $PLAIN ) + count( $SINGLE_ARG ), 'every key is classified exactly once' );

echo "\n--- sprintf render check (templated keys, every language) ---\n";
foreach ( $ALL_LANGS as $lang ) {
    $get = function( $key ) use ( $lang, $live ) {
        return ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
    };

    foreach ( $SINGLE_ARG as $key => $arg ) {
        $rendered = sprintf( $get( $key ), $arg );
        assert_equal( false, (bool) preg_match( '/%[0-9]*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
        assert_equal( 0, substr_count( $rendered, '%' ), "'$key' in '$lang' leaves no stray '%' character" );
    }
}

echo "\n--- globalfxhub_learn_clusters() structure (EN, no WP runtime needed) ---\n";
$expected_slugs = array(
    'forex-fundamentals', 'leverage-margin-risk', 'choosing-a-broker', 'broker-regulation',
    'fees-and-costs', 'trading-platforms', 'orders-and-execution', 'analysis-and-economics',
    'trading-concepts', 'safety-and-due-diligence',
);
$clusters = globalfxhub_learn_clusters();
assert_equal( $expected_slugs, array_keys( $clusters ), 'globalfxhub_learn_clusters() returns exactly the 10 expected slugs, in order' );
foreach ( $clusters as $slug => $cluster ) {
    assert_equal( true, isset( $cluster['title'] ) && '' !== $cluster['title'], "cluster '$slug' has a non-empty title" );
    assert_equal( true, isset( $cluster['description'] ) && '' !== $cluster['description'], "cluster '$slug' has a non-empty description" );
}

assert_test_summary_and_exit();
