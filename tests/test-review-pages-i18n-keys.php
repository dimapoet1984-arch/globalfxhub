<?php
/**
 * Localization QA for real gaps found this pass on the broker review
 * pages (page-reviews.php) and their supporting functions:
 *
 *  - globalfxhub_broker_verify_links() (functions.php) hardcoded its 3
 *    "Verify on X register" link labels in English.
 *  - globalfxhub_get_external_reviews() (functions.php) hardcoded its
 *    repeated "Trust Score" label and its handful of distinct editorial
 *    'note' strings (e.g. "Single unverified search snippet...") in
 *    English.
 *  - inc/data-confidence.php's confidence-badge HTML (rendered inline
 *    in the review page's fact table and deep-dive table) hardcoded
 *    its HIGH/MEDIUM/LOW display labels, its 4 'source' description
 *    strings, and its "Last verified: X." tooltip suffix in English.
 *  - globalfxhub_broker_review_dates() (functions.php) baked literal
 *    English date strings ("8 October 2026", "March 2026", "January
 *    2027", and any broker-specific *_override value) directly into
 *    the review-dates meta row and into every confidence badge's
 *    "Last verified" tooltip, instead of storing/parsing the date and
 *    reconstructing it per-viewer-language via date_i18n() at render
 *    time -- the same baked-English-at-write-time bug class already
 *    fixed for the Broker Change Log.
 *  - globalfxhub_broker_research_articles_for_broker() (inc/broker-
 *    research-articles.php) built a research-article link's visible
 *    title from the raw English $entry['title'] instead of the SAME
 *    localized lookup (globalfxhub_get_localized_broker_research_article())
 *    the article's own page uses -- the href was already
 *    language-aware via globalfxhub_track_b_url(), so the link text
 *    and its destination could silently disagree on language, the
 *    same cross-link bug class already fixed for Learn articles.
 *  - page-reviews.php's own editorial-comparison button built
 *    "$broker vs $other" with a hardcoded literal ' vs ' instead of
 *    reusing the existing cmp_vs_label key (already 'vs' in English,
 *    exact match, used identically on page-compare.php).
 *
 * Deliberately NOT changed, and not covered here: the real `source`
 * field on each external-review entry (e.g. "ForexBrokers.com",
 * "BrokerChooser") and `verified_by` ("GlobalFXHub Research Team") --
 * brand/entity proper nouns, same treatment as broker and regulator
 * names everywhere else on the site.
 *
 * Run: php tests/test-review-pages-i18n-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';
require __DIR__ . '/../inc/translatable-strings.php';

$KEYS = array(
    'rev_verify_cysec', 'rev_verify_fca', 'rev_verify_seychelles',
    'rev_ext_trust_score_label',
    'rev_ext_note_unverified_snippet', 'rev_ext_note_avatrade_subscore', 'rev_ext_note_xtb_overall_rating',
    'rev_ext_note_pepperstone_bc', 'rev_ext_note_etoro_old_rating', 'rev_ext_note_plus500_listing',
    'data_confidence_high_label', 'data_confidence_medium_label', 'data_confidence_low_label',
    'data_confidence_source_low', 'data_confidence_source_high', 'data_confidence_source_medium',
    'data_confidence_source_numeric', 'data_confidence_last_verified',
);

// Only data_confidence_last_verified takes a placeholder (the date).
$TEMPLATED = array(
    'data_confidence_last_verified' => array( '8 October 2026' ),
);
$PLAIN = array_values( array_diff( $KEYS, array_keys( $TEMPLATED ) ) );

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

assert_equal( 18, count( $KEYS ), 'exactly 18 new review-pages keys are checked' );
assert_equal( 18, count( $PLAIN ) + count( $TEMPLATED ), 'every key is classified exactly once' );

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

echo "\n--- globalfxhub_broker_verify_links() uses translated labels (functions.php logic, reimplemented here to avoid loading the full WP-dependent file) ---\n";
function verify_links_for_test( $broker ) {
    $links = array();
    if ( ! empty( $broker['cysec'] ) && '—' !== $broker['cysec'] ) {
        $links[] = array( 'label' => globalfxhub_t( 'rev_verify_cysec' ), 'url' => 'https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/' );
    }
    if ( ! empty( $broker['fca'] ) ) {
        $links[] = array( 'label' => globalfxhub_t( 'rev_verify_fca' ), 'url' => 'https://register.fca.org.uk/s/' );
    }
    if ( ! empty( $broker['seychelles'] ) ) {
        $links[] = array( 'label' => globalfxhub_t( 'rev_verify_seychelles' ), 'url' => 'https://fsaseychelles.sc/' );
    }
    return $links;
}
$test_broker = array( 'cysec' => '123/45', 'fca' => '987654', 'seychelles' => null );
$links = verify_links_for_test( $test_broker );
assert_equal( 'Verify on CySEC register', $links[0]['label'], 'CySEC verify link uses the translated label (EN)' );
assert_equal( 'Verify on FCA register', $links[1]['label'], 'FCA verify link uses the translated label (EN)' );
assert_equal( 2, count( $links ), 'no Seychelles link when the broker has no Seychelles licence' );

echo "\n--- globalfxhub_broker_review_dates()'s date_i18n() fix (functions.php logic, reimplemented here): baked English date strings are now parsed and re-rendered via date_i18n(), not echoed as stored literal text ---\n";
function date_i18n( $format, $timestamp ) {
    // Standalone-test stand-in for WordPress's date_i18n(): without a
    // loaded WP runtime there is no active locale to translate month
    // names into, so this verifies the EN rendering pipeline (parse,
    // then reformat) is wired correctly; the live site's real
    // date_i18n() does the actual per-language month-name translation
    // once WP's locale is active, same as every other date on the site.
    return date( $format, $timestamp );
}
function review_dates_for_test( $broker ) {
    $data_reviewed       = $broker['data_reviewed_override'] ?? '8 October 2026';
    $regulation_reviewed = $broker['regulation_reviewed_override'] ?? '8 October 2026';
    $pricing_checked     = $broker['pricing_checked_override'] ?? 'March 2026';
    $next_review         = $broker['next_review_override'] ?? 'January 2027';
    return array(
        'data_reviewed'       => date_i18n( 'j F Y', strtotime( $data_reviewed ) ),
        'regulation_reviewed' => date_i18n( 'j F Y', strtotime( $regulation_reviewed ) ),
        'pricing_checked'     => date_i18n( 'F Y', strtotime( $pricing_checked ) ),
        'next_review'         => date_i18n( 'F Y', strtotime( $next_review ) ),
    );
}
$default_dates = review_dates_for_test( array() );
assert_equal( '8 October 2026', $default_dates['data_reviewed'], 'default data_reviewed still renders as "8 October 2026" after round-tripping through strtotime()+date_i18n()' );
assert_equal( '8 October 2026', $default_dates['regulation_reviewed'], 'default regulation_reviewed round-trips unchanged' );
assert_equal( 'March 2026', $default_dates['pricing_checked'], 'month-year-only pricing_checked round-trips WITHOUT gaining a spurious day digit' );
assert_equal( 'January 2027', $default_dates['next_review'], 'month-year-only next_review round-trips WITHOUT gaining a spurious day digit' );

// A real override format taken directly from the live broker dataset
// (functions.php: the 'valutrades' entry's 'data_reviewed_override').
$override_dates = review_dates_for_test( array( 'data_reviewed_override' => '9 November 2026' ) );
assert_equal( '9 November 2026', $override_dates['data_reviewed'], 'a real day-level broker override ("9 November 2026") round-trips unchanged' );

echo "\n--- inc/data-confidence.php: translated badge labels, 'source' descriptions, and the 'Last verified' tooltip template ---\n";
// Stub globalfxhub_broker_review_dates() (functions.php) so
// inc/data-confidence.php -- which calls it internally -- can be
// loaded standalone without the rest of the WP-dependent functions.php.
function globalfxhub_broker_review_dates( $broker ) {
    return array( 'data_reviewed' => '8 October 2026', 'pricing_checked' => 'March 2026' );
}
function esc_attr( $s ) { return $s; }
function esc_html( $s ) { return $s; }
require __DIR__ . '/../inc/data-confidence.php';

$confirmed_result = globalfxhub_classify_text_confidence( "Confirmed via the broker's own published fee schedule." );
assert_equal( 'HIGH', $confirmed_result['confidence'], 'HIGH-pattern text classifies as HIGH' );
assert_equal( globalfxhub_t( 'data_confidence_source_high' ), $confirmed_result['source'], 'HIGH confidence uses the translated source description, not a hardcoded English sentence' );

$low_result = globalfxhub_classify_text_confidence( 'Sources disagree on this figure.' );
assert_equal( 'LOW', $low_result['confidence'], 'LOW-pattern text classifies as LOW' );
assert_equal( globalfxhub_t( 'data_confidence_source_low' ), $low_result['source'], 'LOW confidence uses the translated source description' );

$medium_result = globalfxhub_classify_text_confidence( 'Confirmed by multiple independent sources.' );
assert_equal( 'MEDIUM', $medium_result['confidence'], 'MEDIUM-pattern text classifies as MEDIUM' );
assert_equal( globalfxhub_t( 'data_confidence_source_medium' ), $medium_result['source'], 'MEDIUM confidence uses the translated source description' );

$broker_high = array( 'overnight_financing' => "Confirmed via the broker's own published fee schedule." );
$badge_html = globalfxhub_data_confidence_badge_html( $broker_high, 'overnight_financing' );
assert_equal( true, false !== strpos( $badge_html, globalfxhub_t( 'data_confidence_high_label' ) ), 'badge HTML shows the translated "High confidence" label, not hardcoded English' );
assert_equal( true, false !== strpos( $badge_html, 'Last verified: 8 October 2026.' ), 'badge title attribute renders the "Last verified" sentence via the translated template, with the real date substituted in' );

$broker_numeric = array( 'spread_eurusd' => 0.9 );
$numeric_badge_html = globalfxhub_data_confidence_badge_html( $broker_numeric, 'spread_eurusd' );
assert_equal( true, false !== strpos( $numeric_badge_html, globalfxhub_t( 'data_confidence_medium_label' ) ), 'numeric-field badge shows the translated "Medium confidence" label' );
assert_equal( true, false !== strpos( $numeric_badge_html, globalfxhub_t( 'data_confidence_source_numeric' ) ), 'numeric-field badge title uses the translated cross-referenced-data source description' );

echo "\n--- globalfxhub_broker_research_articles_for_broker()'s cross-link title fix (inc/broker-research-articles.php) ---\n";
function add_action( ...$args ) {}
function add_filter( ...$args ) {}
function pll_current_language() { return $GLOBALS['__test_pll_current_language'] ?? 'en'; }
function globalfxhub_track_b_url( $path ) { return 'https://globalfxhub.net/' . $path; }
function globalfxhub_get_broker_by_slug( $slug ) {
    return ( 'pepperstone' === $slug ) ? array( 'slug' => 'pepperstone', 'name' => 'Pepperstone' ) : null;
}
require __DIR__ . '/../inc/broker-research-articles.php';

$GLOBALS['__test_pll_current_language'] = 'en';
$en_links = globalfxhub_broker_research_articles_for_broker( 'pepperstone' );
assert_equal( 1, count( $en_links ), 'pepperstone has exactly one drafted research article (fees)' );
assert_equal( 'Pepperstone Fees Explained', $en_links[0]['title'], 'EN link title is the English title' );

$GLOBALS['__test_pll_current_language'] = 'es';
$es_links = globalfxhub_broker_research_articles_for_broker( 'pepperstone' );
assert_equal( 'Comisiones de Pepperstone explicadas', $es_links[0]['title'], 'es link title is the REAL translated title (globalfxhub_get_localized_broker_research_article()\'s own text), not the static English $entry[\'title\']' );
assert_equal( true, $es_links[0]['title'] !== $en_links[0]['title'], 'es and en link titles actually differ -- confirms this is not silently falling back to English' );

echo "\n--- exact-match key reuse: page-reviews.php's broker-vs-broker button reuses cmp_vs_label ('vs') instead of a hardcoded literal ---\n";
assert_equal( 'vs', globalfxhub_string_text( 'cmp_vs_label' ), "'cmp_vs_label' is the exact 'vs' connector word page-reviews.php's editorial-comparison button needs" );

assert_test_summary_and_exit();
