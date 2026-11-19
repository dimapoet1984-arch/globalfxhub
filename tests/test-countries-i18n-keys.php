<?php
/**
 * Localization QA for the /countries/{slug}/ and /countries/ index page
 * (page-countries.php) and the three EU-wide generator functions in
 * inc/countries.php (globalfxhub_country_eu_framework_html(),
 * globalfxhub_country_investor_compensation_html(),
 * globalfxhub_country_leverage_rules_html()): eyebrow/H1/intro for
 * both EU and non-EU country pages, section H2 headings, the
 * local-entity and ranking-methodology paragraphs, the regulatory-
 * sources block, the index page's eyebrow/intro/card title, and the
 * meta description globalfxhub_country_seo_head() builds for the hub
 * page and each per-country page.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, and that every
 * templated key renders cleanly via sprintf()/vsprintf() in every
 * language with no leftover placeholder and no stray '%' character.
 *
 * Run: php tests/test-countries-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'country_eyebrow', 'country_h1', 'country_has_local_suffix',
    'country_intro_eu', 'country_intro_noneu', 'country_link_text_full_reviews',
    'country_label_currency', 'country_eurozone_suffix', 'country_label_regulator',
    'country_label_eu_since', 'country_not_researched', 'country_link_text_coverage',
    'country_h2_eu_framework', 'country_h2_framework_other', 'country_h2_investor_compensation',
    'country_h2_leverage_rules', 'country_h2_taxation', 'country_h2_deposit_methods',
    'country_h2_restrictions_eu', 'country_h2_restrictions_other', 'country_h2_local_entity',
    'country_h2_faqs', 'country_h2_regulatory_sources', 'country_tax_disclaimer',
    'country_local_entity_has_local', 'country_local_entity_eu_licence_clause',
    'country_local_entity_other_licence_clause', 'country_local_entity_none_eu',
    'country_local_entity_none_other', 'country_methodology_eu', 'country_methodology_other',
    'country_methodology_shortlist_note', 'country_link_text_score',
    'country_link_text_comparison_tool', 'country_link_text_leverage_guide',
    'country_regsrc_official_site_suffix', 'country_regsrc_eu_wide_label',
    'country_regsrc_broker_licensing_label', 'country_link_text_cysec_page',
    'country_index_eyebrow', 'country_index_intro', 'country_index_card_title',
    'country_badge_eu_since', 'country_badge_regulated', 'country_badge_non_eu',
    'country_eu_framework_p1', 'country_leverage_rules_p1', 'country_investor_compensation_p1',
    'country_investor_compensation_no_local', 'country_link_text_investor_compensation',
    'country_meta_desc_hub', 'country_meta_desc_eu', 'country_meta_desc_other',
);

// Keys taking a single %s/%d arg (vsprintf with one positional element).
$SINGLE_ARG = array(
    'country_eyebrow' => 'Germany', 'country_h1' => 'Germany', 'country_h2_faqs' => 'Germany',
    'country_local_entity_other_licence_clause' => 'ASIC', 'country_local_entity_none_eu' => 'Germany',
    'country_regsrc_official_site_suffix' => 'BaFin', 'country_index_card_title' => 'Germany',
    'country_badge_eu_since' => 1995, 'country_badge_regulated' => 'ASIC',
    'country_eu_framework_p1' => 'Germany', 'country_leverage_rules_p1' => 'Germany',
    'country_not_researched' => '<a href="x">y</a>',
    'country_meta_desc_eu' => 'Germany',
);
// Keys taking a positional (%1$s..) arg list.
$POSITIONAL = array(
    'country_intro_eu' => array( ' SFX', 'Germany', '<a>y</a>' ),
    'country_intro_noneu' => array( 'ASIC', ' SFX', 'ASIC', 'Australia', '<a>y</a>' ),
    'country_local_entity_has_local' => array( 'Germany', 'EU licence' ),
    'country_local_entity_none_other' => array( 'Germany', 'ASIC' ),
    'country_methodology_eu' => array( '<a>score</a>', 'Germany', '<a>cmp</a>', '<a>lev</a>' ),
    'country_methodology_other' => array( '<a>score</a>', 'ASIC', '', '<a>cmp</a>' ),
    'country_methodology_shortlist_note' => array( 3, 'ASIC' ),
    'country_investor_compensation_p1' => array( 'Germany' ),
    'country_investor_compensation_no_local' => array( 'Germany', '<a>x</a>' ),
    'country_meta_desc_other' => array( 'ASIC', 'Australia' ),
);
// Non-templated (plain) keys -- just need a non-empty translation.
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

assert_equal( 53, count( $KEYS ), 'exactly 53 new countries-page keys are checked' );
assert_equal( 53, count( $PLAIN ) + count( $SINGLE_ARG ) + count( $POSITIONAL ), 'every key is classified exactly once' );

echo "\n--- sprintf render check (every templated key, every language) ---\n";
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
    // No key in this batch has a literal '%' in its own text -- confirm
    // none of them leave a stray '%' behind after substitution either.
    foreach ( array_merge( $SINGLE_ARG, $POSITIONAL ) as $key => $args ) {
        $rendered = is_array( $args ) ? vsprintf( $get( $key ), $args ) : sprintf( $get( $key ), $args );
        assert_equal( 0, substr_count( $rendered, '%' ), "'$key' in '$lang' leaves no stray '%' character" );
    }
}

assert_test_summary_and_exit();
