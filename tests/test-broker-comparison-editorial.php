<?php
/**
 * Structural QA for the new 'avatrade-pepperstone' editorial comparison
 * (inc/broker-comparison-editorial.php), added as the first drafted
 * piece from the Nov 19 - Dec 31 content calendar's commercial-
 * comparison bucket.
 *
 * Covers:
 *  - the entry itself has every required field, non-empty, with a_slug
 *    'avatrade' / b_slug 'pepperstone' (lower-ranked-first convention,
 *    matching the /compare/avatrade-vs-pepperstone/ URL order and the
 *    existing pepperstone-ic-markets / exness-xm entries' own
 *    rank-ordering).
 *  - globalfxhub_editorial_comparisons_for_broker() surfaces this entry
 *    from BOTH avatrade's and pepperstone's own review pages -- the
 *    site's built-in mechanism for 2 of the 3 required inbound links,
 *    with no extra wiring needed.
 *  - the new pair is also in page-compare.php's $vs_preset_pairs list
 *    (the 3rd inbound link), matching the same convention every other
 *    editorial comparison already follows.
 *  - globalfxhub_get_localized_editorial_comparison() gracefully falls
 *    back to the English entry (no translation exists yet -- this
 *    content hasn't been through i18n/translate_comparison_editorial.py
 *    yet).
 *  - i18n/translate_comparison_editorial.py's COMPARISONS dict mirrors
 *    inc/broker-comparison-editorial.php's PHP content EXACTLY, field
 *    for field -- the two are hand-kept-in-sync per that script's own
 *    header comment, and this is the drift check that would catch it
 *    if they ever disagree.
 *
 * Run: php tests/test-broker-comparison-editorial.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

function globalfxhub_get_broker_by_slug( $slug ) {
    $brokers = array(
        'avatrade'    => array( 'slug' => 'avatrade', 'name' => 'AvaTrade' ),
        'pepperstone' => array( 'slug' => 'pepperstone', 'name' => 'Pepperstone' ),
    );
    return $brokers[ $slug ] ?? null;
}
function globalfxhub_track_b_url( $path ) { return 'https://globalfxhub.net/' . $path; }

require __DIR__ . '/../inc/broker-comparison-editorial.php';

$KEY = 'avatrade-pepperstone';
$all = globalfxhub_get_editorial_comparisons();

assert_equal( true, isset( $all[ $KEY ] ), "'$KEY' is registered in globalfxhub_get_editorial_comparisons()" );
$entry = $all[ $KEY ];

assert_equal( 'avatrade', $entry['a_slug'], 'a_slug is avatrade (rank 1, lower-ranked-first, same convention as the existing entries)' );
assert_equal( 'pepperstone', $entry['b_slug'], 'b_slug is pepperstone (rank 8)' );

$REQUIRED_SCALAR_FIELDS = array(
    'meta_title', 'meta_description', 'intro', 'verdict',
    'choose_a_heading', 'choose_b_heading', 'worth_knowing_heading', 'worth_knowing',
);
foreach ( $REQUIRED_SCALAR_FIELDS as $field ) {
    assert_equal( true, isset( $entry[ $field ] ) && '' !== trim( $entry[ $field ] ), "'$field' is present and non-empty" );
}

assert_equal( true, count( $entry['choose_a_points'] ) >= 2, 'choose_a_points has at least 2 points' );
assert_equal( true, count( $entry['choose_b_points'] ) >= 2, 'choose_b_points has at least 2 points' );
foreach ( $entry['choose_a_points'] as $i => $point ) {
    assert_equal( true, '' !== trim( $point ), "choose_a_points[$i] is non-empty" );
}
foreach ( $entry['choose_b_points'] as $i => $point ) {
    assert_equal( true, '' !== trim( $point ), "choose_b_points[$i] is non-empty" );
}

assert_equal( true, count( $entry['faqs'] ) >= 2, 'at least 2 FAQs' );
foreach ( $entry['faqs'] as $i => $faq ) {
    assert_equal( true, isset( $faq['q'] ) && '' !== trim( $faq['q'] ), "faqs[$i]['q'] is non-empty" );
    assert_equal( true, isset( $faq['a'] ) && '' !== trim( $faq['a'] ), "faqs[$i]['a'] is non-empty" );
}

// Every factual number quoted in the verdict/points must trace back to
// the real broker records (functions.php) -- spot-checking the figures
// used, rather than re-deriving them, since functions.php itself is too
// large/WP-dependent to load standalone here.
assert_equal( true, false !== strpos( $entry['verdict'], '4.12' ), 'verdict cites AvaTrade\'s real overall score (4.12/5)' );
assert_equal( true, false !== strpos( $entry['verdict'], '3.64' ), 'verdict cites Pepperstone\'s real overall score (3.64/5)' );
assert_equal( true, false !== strpos( $entry['verdict'], '0.93' ), 'verdict cites AvaTrade\'s real on-file EUR/USD spread (0.93 pips)' );
assert_equal( true, false !== strpos( $entry['verdict'], '1.1' ), 'verdict cites Pepperstone\'s real on-file EUR/USD spread (1.1 pips)' );

echo "\n--- cross-link check: globalfxhub_editorial_comparisons_for_broker() surfaces this entry from BOTH brokers' own review pages ---\n";
$from_avatrade = globalfxhub_editorial_comparisons_for_broker( 'avatrade' );
$from_pepperstone = globalfxhub_editorial_comparisons_for_broker( 'pepperstone' );

assert_equal( 1, count( $from_avatrade ), 'avatrade has exactly one editorial comparison (this one)' );
assert_equal( 'Pepperstone', $from_avatrade[0]['other']['name'], "avatrade's review page links to this comparison, naming Pepperstone as the other broker" );
assert_equal( 'https://globalfxhub.net/compare/avatrade-vs-pepperstone/', $from_avatrade[0]['url'], 'the auto-generated URL matches a_slug-vs-b_slug order' );

assert_equal( 1, count( $from_pepperstone ), 'pepperstone has exactly one editorial comparison (this one)' );
assert_equal( 'AvaTrade', $from_pepperstone[0]['other']['name'], "pepperstone's review page links to this comparison, naming AvaTrade as the other broker" );
assert_equal( $from_avatrade[0]['url'], $from_pepperstone[0]['url'], 'both sides link to the exact same canonical URL' );

echo "\n--- page-compare.php's preset-pairs list includes this pair (the 3rd inbound link), same convention as every other editorial comparison ---\n";
$compare_source = file_get_contents( __DIR__ . '/../page-compare.php' );
assert_equal( true, false !== strpos( $compare_source, "array( 'avatrade', 'pepperstone' )" ), "page-compare.php's \$vs_preset_pairs includes array( 'avatrade', 'pepperstone' )" );

echo "\n--- globalfxhub_get_localized_editorial_comparison() falls back to English (no translation run yet) ---\n";
$localized = globalfxhub_get_localized_editorial_comparison( $KEY );
assert_equal( $entry['meta_title'], $localized['meta_title'], 'with no pll_current_language() and no translation on file, the localized getter returns the English entry unchanged' );
assert_equal( $entry['verdict'], $localized['verdict'], 'verdict is unchanged (English fallback)' );

assert_equal( null, globalfxhub_get_localized_editorial_comparison( 'no-such-pair' ), 'an unknown key returns null, not a fatal error' );

echo "\n--- parity check: i18n/translate_comparison_editorial.py's COMPARISONS dict mirrors the PHP entry exactly ---\n";
$python_json = shell_exec( 'cd ' . escapeshellarg( __DIR__ . '/..' ) . " && python3 -c \"import sys, json; sys.path.insert(0, 'i18n'); import translate_comparison_editorial as m; print(json.dumps(m.COMPARISONS.get('$KEY')))\" 2>&1" );
$python_entry = json_decode( trim( (string) $python_json ), true );

assert_equal( true, is_array( $python_entry ), "i18n/translate_comparison_editorial.py's COMPARISONS['$KEY'] exists and is valid JSON (raw output: " . substr( (string) $python_json, 0, 200 ) . ')' );

if ( is_array( $python_entry ) ) {
    $expected_flat = array(
        'meta_title' => $entry['meta_title'],
        'meta_description' => $entry['meta_description'],
        'intro' => $entry['intro'],
        'verdict' => $entry['verdict'],
        'choose_a_heading' => $entry['choose_a_heading'],
    );
    foreach ( $entry['choose_a_points'] as $i => $point ) {
        $expected_flat[ 'choose_a_point_' . ( $i + 1 ) ] = $point;
    }
    $expected_flat['choose_b_heading'] = $entry['choose_b_heading'];
    foreach ( $entry['choose_b_points'] as $i => $point ) {
        $expected_flat[ 'choose_b_point_' . ( $i + 1 ) ] = $point;
    }
    $expected_flat['worth_knowing_heading'] = $entry['worth_knowing_heading'];
    $expected_flat['worth_knowing'] = $entry['worth_knowing'];
    foreach ( $entry['faqs'] as $i => $faq ) {
        $expected_flat[ 'faq_' . ( $i + 1 ) . '_q' ] = $faq['q'];
        $expected_flat[ 'faq_' . ( $i + 1 ) . '_a' ] = $faq['a'];
    }

    assert_equal( array_keys( $expected_flat ), array_keys( $python_entry ), 'the Python dict has exactly the same flattened keys, in the same order, as the PHP entry' );
    foreach ( $expected_flat as $field_key => $expected_value ) {
        assert_equal( $expected_value, $python_entry[ $field_key ] ?? null, "Python COMPARISONS['$KEY']['$field_key'] matches the PHP entry exactly (no copy-paste drift)" );
    }
}

assert_test_summary_and_exit();
