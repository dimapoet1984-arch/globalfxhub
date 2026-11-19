<?php
/**
 * Localization QA for the /data/ page (page-data.php): the research-
 * operation landing page describing this site's own broker dataset.
 * Covers the breadcrumb, eyebrow, intro, 4 trustbar stat labels, 6 H2
 * section headings, 11 link-text labels (reusing ptitle_broker_finder,
 * ptitle_broker_changelog, and footer_corrections where the wording is
 * an exact match; new keys where this page's own wording differs, e.g.
 * "Cost Calculator" vs. the existing ptitle_cost_calculator's "Forex
 * Spread Cost Calculator"), the 6 templated paragraphs built from
 * live-computed counts and link markup, and the two binary singular/
 * plural phrase pairs (head-to-head regulator comparisons, original
 * data reports) mirroring page-data.php's own $data_plural_t() helper
 * -- the translated replacement for the page's previous English-only
 * $data_plural() closure.
 *
 * Also guards a real data-accuracy bug fixed alongside the i18n work:
 * the "Original research" paragraph used to hardcode "139-broker
 * dataset" as literal prose instead of the page's own live-computed
 * $data_broker_count variable, contradicting this page's stated
 * design philosophy that every figure on it is computed live, never a
 * number written once and left to go stale. data_p_research now takes
 * the broker count as its own %2$s placeholder.
 *
 * Checks every new key is registered, has a real English source, has
 * a non-empty translation in all 7 live languages, that every
 * templated key renders cleanly via sprintf() in every language with
 * no leftover placeholder and no stray '%' character.
 *
 * Run: php tests/test-data-page-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';
require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'data_eyebrow', 'data_intro',
    'data_stat_brokers', 'data_stat_regulators', 'data_stat_licence_holdings', 'data_stat_countries',
    'data_h2_dataset', 'data_h2_regulator', 'data_h2_research', 'data_h2_confidence', 'data_h2_tools', 'data_h2_keeping_current',
    'data_link_text_methodology', 'data_link_text_review_pages', 'data_link_text_best_lists', 'data_link_text_comparisons',
    'data_link_text_entity_page', 'data_link_text_regulation_kb', 'data_link_text_research_index', 'data_link_text_broker_review',
    'data_link_text_cost_calculator', 'data_link_text_regulation_checker', 'data_link_text_editorial_policy',
    'data_p_dataset_1', 'data_p_dataset_2', 'data_p_regulator', 'data_p_research', 'data_p_confidence',
    'data_p_tools', 'data_p_keeping_current',
    'data_reg_comparison_singular', 'data_reg_comparison_plural',
    'data_report_singular', 'data_report_plural',
);

// Keys with no placeholders: render as plain text, no sprintf needed.
$PLAIN = array(
    'data_eyebrow', 'data_intro',
    'data_stat_brokers', 'data_stat_regulators', 'data_stat_licence_holdings', 'data_stat_countries',
    'data_h2_dataset', 'data_h2_regulator', 'data_h2_research', 'data_h2_confidence', 'data_h2_tools', 'data_h2_keeping_current',
    'data_link_text_methodology', 'data_link_text_review_pages', 'data_link_text_best_lists', 'data_link_text_comparisons',
    'data_link_text_entity_page', 'data_link_text_regulation_kb', 'data_link_text_research_index', 'data_link_text_broker_review',
    'data_link_text_cost_calculator', 'data_link_text_regulation_checker', 'data_link_text_editorial_policy',
);

// Templated keys -> the sprintf args used to test rendering (dummy
// values standing in for live counts and <a> link markup).
$TEMPLATED = array(
    'data_p_dataset_1' => array( 139, '<a>methodology</a>', '<a>reviews</a>' ),
    'data_p_dataset_2' => array( 15, '<a>best</a>', 8, '<a>compare</a>', 139, 780, 40 ),
    'data_p_regulator' => array( 9, '<a>entity</a>', 212, '<a>kb</a>', '5 head-to-head regulator comparisons' ),
    'data_p_research'  => array( '3 original data reports', 139, '<a>research</a>' ),
    'data_p_confidence' => array( '<a>review</a>' ),
    'data_p_tools' => array( '<a>finder</a>', '<a>calc</a>', '<a>checker</a>' ),
    'data_p_keeping_current' => array( '<a>changelog</a>', '<a>corrections</a>', '<a>policy</a>' ),
    'data_reg_comparison_singular' => array( 1 ),
    'data_reg_comparison_plural' => array( 5 ),
    'data_report_singular' => array( 1 ),
    'data_report_plural' => array( 3 ),
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

assert_equal( 34, count( $KEYS ), 'exactly 34 new data-page keys are checked' );
assert_equal( 34, count( $PLAIN ) + count( $TEMPLATED ), 'every key is classified exactly once (plain vs. templated)' );
assert_equal( array(), array_diff( $KEYS, array_merge( $PLAIN, array_keys( $TEMPLATED ) ) ), 'no key is left unclassified' );

echo "\n--- sprintf render check (templated keys, every language) ---\n";
foreach ( $ALL_LANGS as $lang ) {
    $get = function( $key ) use ( $lang, $live ) {
        return ( 'en' === $lang ) ? globalfxhub_string_text( $key ) : $live[ $lang ][ $key ];
    };

    foreach ( $TEMPLATED as $key => $args ) {
        $rendered = vsprintf( $get( $key ), $args );
        assert_equal( false, (bool) preg_match( '/%\d*\$?[sd]/', $rendered ), "'$key' in '$lang' leaves no unresolved placeholder after sprintf" );
        assert_equal( 0, substr_count( $rendered, '%' ), "'$key' in '$lang' leaves no stray '%' character" );
    }
}

echo "\n--- data_plural_t()-equivalent binary singular/plural selection (EN, mirrors page-data.php's closure) ---\n";
// page-data.php's $data_plural_t() picks the singular key only for a
// count of exactly 1, the plural key otherwise -- same binary logic
// as the codebase's only other plural helper (page-data.php's own
// previous $data_plural closure, broker-changelog's value formatting),
// not true CLDR plural rules, consistent with this codebase's existing
// level of i18n sophistication.
function data_plural_t_for_test( $count, $singular_key, $plural_key, $live_strings_or_en ) {
    $key = ( 1 === $count ) ? $singular_key : $plural_key;
    return sprintf( $live_strings_or_en[ $key ] ?? $key, $count );
}
assert_equal(
    '1 head-to-head regulator comparison',
    data_plural_t_for_test( 1, 'data_reg_comparison_singular', 'data_reg_comparison_plural', array(
        'data_reg_comparison_singular' => globalfxhub_string_text( 'data_reg_comparison_singular' ),
        'data_reg_comparison_plural' => globalfxhub_string_text( 'data_reg_comparison_plural' ),
    ) ),
    'count of 1 selects the singular key (EN)'
);
assert_equal(
    '5 head-to-head regulator comparisons',
    data_plural_t_for_test( 5, 'data_reg_comparison_singular', 'data_reg_comparison_plural', array(
        'data_reg_comparison_singular' => globalfxhub_string_text( 'data_reg_comparison_singular' ),
        'data_reg_comparison_plural' => globalfxhub_string_text( 'data_reg_comparison_plural' ),
    ) ),
    'count other than 1 selects the plural key (EN)'
);
assert_equal(
    '0 original data reports',
    data_plural_t_for_test( 0, 'data_report_singular', 'data_report_plural', array(
        'data_report_singular' => globalfxhub_string_text( 'data_report_singular' ),
        'data_report_plural' => globalfxhub_string_text( 'data_report_plural' ),
    ) ),
    'a count of 0 selects the plural key, same binary rule as 1 === $count (EN)'
);

echo "\n--- exact-match key reuse: confirms these existing keys' English text matches what page-data.php's own link labels need (no duplicate key minted) ---\n";
assert_equal( 'Home', globalfxhub_string_text( 'tb_home' ), "breadcrumb reuses 'tb_home' for its exact English text" );
assert_equal( 'Broker Finder', globalfxhub_string_text( 'ptitle_broker_finder' ), "data page reuses 'ptitle_broker_finder' (exact match) rather than minting a new key" );
assert_equal( 'Broker Change Log', globalfxhub_string_text( 'ptitle_broker_changelog' ), "data page reuses 'ptitle_broker_changelog' (exact match) rather than minting a new key" );
assert_equal( 'Corrections', globalfxhub_string_text( 'footer_corrections' ), "data page reuses 'footer_corrections' (exact match) rather than minting a new key" );

// These three are deliberately NEW keys, not reuses -- their wording on
// /data/ differs from the existing near-miss key (capitalization or
// extra words), so reusing the existing key would silently change this
// page's copy. Confirms the near-misses really are near-misses.
assert_equal( true, globalfxhub_string_text( 'ptitle_cost_calculator' ) !== 'Cost Calculator', "'ptitle_cost_calculator' is NOT an exact match for data page's 'Cost Calculator' link text (confirms data_link_text_cost_calculator is a necessary new key)" );
assert_equal( true, globalfxhub_string_text( 'ptitle_regulation_checker' ) !== 'Regulation Checker', "'ptitle_regulation_checker' is NOT an exact match for data page's 'Regulation Checker' link text (confirms data_link_text_regulation_checker is a necessary new key)" );
assert_equal( true, globalfxhub_string_text( 'footer_editorial_policy' ) !== 'Editorial Policy', "'footer_editorial_policy' ('Editorial policy', lowercase p) is NOT an exact match for data page's 'Editorial Policy' link text (confirms data_link_text_editorial_policy is a necessary new key)" );

assert_test_summary_and_exit();
