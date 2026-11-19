<?php
/**
 * Structural QA for the editorial comparisons drafted from the Nov 19
 * - Dec 31 content calendar's commercial-comparison bucket
 * (inc/broker-comparison-editorial.php).
 *
 * Covers, for every comparison in $NEW_COMPARISONS below:
 *  - the entry has every required field, non-empty, with a_slug/b_slug
 *    in the site's established lower-rank-first convention (matching
 *    the /compare/{a}-vs-{b}/ URL order and the existing
 *    pepperstone-ic-markets / exness-xm entries' own rank-ordering).
 *  - the verdict actually cites the real scores/figures it claims (spot
 *    checks against functions.php's broker records).
 *  - globalfxhub_editorial_comparisons_for_broker() surfaces the entry
 *    from BOTH brokers' own review pages -- the site's built-in
 *    mechanism for 2 of the 3 required inbound links, with no extra
 *    wiring needed.
 *  - the pair is also in page-compare.php's $vs_preset_pairs list (the
 *    3rd inbound link), matching the same convention every other
 *    editorial comparison already follows.
 *  - globalfxhub_get_localized_editorial_comparison() gracefully falls
 *    back to the English entry when no translation exists yet.
 *  - i18n/translate_comparison_editorial.py's COMPARISONS dict mirrors
 *    the PHP content EXACTLY, field for field -- the two are
 *    hand-kept-in-sync per that script's own header comment, and this
 *    is the drift check that would catch it if they ever disagree.
 *
 * Run: php tests/test-broker-comparison-editorial.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

function globalfxhub_get_broker_by_slug( $slug ) {
    $brokers = array(
        'avatrade'    => array( 'slug' => 'avatrade', 'name' => 'AvaTrade' ),
        'pepperstone' => array( 'slug' => 'pepperstone', 'name' => 'Pepperstone' ),
        'fp-markets'  => array( 'slug' => 'fp-markets', 'name' => 'FP Markets' ),
        'ic-markets'  => array( 'slug' => 'ic-markets', 'name' => 'IC Markets' ),
    );
    return $brokers[ $slug ] ?? null;
}
function globalfxhub_track_b_url( $path ) { return 'https://globalfxhub.net/' . $path; }

require __DIR__ . '/../inc/broker-comparison-editorial.php';

// Per-comparison expectations: a/b slug+name (rank-ordered), and a
// couple of real figures each verdict must actually cite (spot-checks
// against functions.php's broker records, not a full re-derivation).
$NEW_COMPARISONS = array(
    'avatrade-pepperstone' => array(
        'a_slug' => 'avatrade', 'a_name' => 'AvaTrade',
        'b_slug' => 'pepperstone', 'b_name' => 'Pepperstone',
        'verdict_must_contain' => array( '4.12', '3.64', '0.93', '1.1' ),
    ),
    'fp-markets-ic-markets' => array(
        'a_slug' => 'fp-markets', 'a_name' => 'FP Markets',
        'b_slug' => 'ic-markets', 'b_name' => 'IC Markets',
        'verdict_must_contain' => array( '3.49', '3.07', '0.8', '1.0' ),
    ),
);

$all = globalfxhub_get_editorial_comparisons();
$compare_source = file_get_contents( __DIR__ . '/../page-compare.php' );

foreach ( $NEW_COMPARISONS as $key => $expect ) {
    echo "\n=== $key ===\n";
    assert_equal( true, isset( $all[ $key ] ), "'$key' is registered in globalfxhub_get_editorial_comparisons()" );
    $entry = $all[ $key ];

    assert_equal( $expect['a_slug'], $entry['a_slug'], "'$key' a_slug is '{$expect['a_slug']}' (lower rank, same convention as every existing entry)" );
    assert_equal( $expect['b_slug'], $entry['b_slug'], "'$key' b_slug is '{$expect['b_slug']}'" );

    $required_scalar_fields = array(
        'meta_title', 'meta_description', 'intro', 'verdict',
        'choose_a_heading', 'choose_b_heading', 'worth_knowing_heading', 'worth_knowing',
    );
    foreach ( $required_scalar_fields as $field ) {
        assert_equal( true, isset( $entry[ $field ] ) && '' !== trim( $entry[ $field ] ), "'$key': '$field' is present and non-empty" );
    }

    assert_equal( true, count( $entry['choose_a_points'] ) >= 2, "'$key': choose_a_points has at least 2 points" );
    assert_equal( true, count( $entry['choose_b_points'] ) >= 2, "'$key': choose_b_points has at least 2 points" );
    foreach ( array_merge( $entry['choose_a_points'], $entry['choose_b_points'] ) as $i => $point ) {
        assert_equal( true, '' !== trim( $point ), "'$key': choose point #$i is non-empty" );
    }

    assert_equal( true, count( $entry['faqs'] ) >= 2, "'$key': at least 2 FAQs" );
    foreach ( $entry['faqs'] as $i => $faq ) {
        assert_equal( true, isset( $faq['q'] ) && '' !== trim( $faq['q'] ), "'$key': faqs[$i]['q'] is non-empty" );
        assert_equal( true, isset( $faq['a'] ) && '' !== trim( $faq['a'] ), "'$key': faqs[$i]['a'] is non-empty" );
    }

    foreach ( $expect['verdict_must_contain'] as $figure ) {
        assert_equal( true, false !== strpos( $entry['verdict'], $figure ), "'$key': verdict cites real figure '$figure'" );
    }

    // Cross-link check: both review pages surface this entry, and the
    // auto-generated URL matches a_slug-vs-b_slug order.
    $from_a = globalfxhub_editorial_comparisons_for_broker( $expect['a_slug'] );
    $from_b = globalfxhub_editorial_comparisons_for_broker( $expect['b_slug'] );
    $expected_url = 'https://globalfxhub.net/compare/' . $expect['a_slug'] . '-vs-' . $expect['b_slug'] . '/';

    $match_a = array_values( array_filter( $from_a, function( $c ) use ( $expect ) { return $c['other']['name'] === $expect['b_name']; } ) );
    assert_equal( 1, count( $match_a ), "'$key': {$expect['a_slug']}'s review page links to this comparison, naming {$expect['b_name']} as the other broker" );
    if ( $match_a ) {
        assert_equal( $expected_url, $match_a[0]['url'], "'$key': auto-generated URL matches a_slug-vs-b_slug order" );
    }

    $match_b = array_values( array_filter( $from_b, function( $c ) use ( $expect ) { return $c['other']['name'] === $expect['a_name']; } ) );
    assert_equal( 1, count( $match_b ), "'$key': {$expect['b_slug']}'s review page links to this comparison, naming {$expect['a_name']} as the other broker" );
    if ( $match_b ) {
        assert_equal( $expected_url, $match_b[0]['url'], "'$key': both sides link to the exact same canonical URL" );
    }

    assert_equal(
        true,
        false !== strpos( $compare_source, "array( '" . $expect['a_slug'] . "', '" . $expect['b_slug'] . "' )" ),
        "'$key': page-compare.php's \$vs_preset_pairs includes array( '{$expect['a_slug']}', '{$expect['b_slug']}' ) -- the 3rd inbound link"
    );

    // English fallback (no translation run yet).
    $localized = globalfxhub_get_localized_editorial_comparison( $key );
    assert_equal( $entry['meta_title'], $localized['meta_title'], "'$key': with no translation on file, the localized getter returns the English entry unchanged" );
    assert_equal( $entry['verdict'], $localized['verdict'], "'$key': verdict is unchanged (English fallback)" );

    // Parity check against the Python translation script's mirror.
    $python_json = shell_exec( 'cd ' . escapeshellarg( __DIR__ . '/..' ) . " && python3 -c \"import sys, json; sys.path.insert(0, 'i18n'); import translate_comparison_editorial as m; print(json.dumps(m.COMPARISONS.get('$key')))\" 2>&1" );
    $python_entry = json_decode( trim( (string) $python_json ), true );
    assert_equal( true, is_array( $python_entry ), "'$key': i18n/translate_comparison_editorial.py's COMPARISONS['$key'] exists and is valid JSON (raw: " . substr( (string) $python_json, 0, 150 ) . ')' );

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

        assert_equal( array_keys( $expected_flat ), array_keys( $python_entry ), "'$key': Python dict has exactly the same flattened keys, in the same order, as the PHP entry" );
        foreach ( $expected_flat as $field_key => $expected_value ) {
            assert_equal( $expected_value, $python_entry[ $field_key ] ?? null, "'$key': Python COMPARISONS['$field_key'] matches the PHP entry exactly (no copy-paste drift)" );
        }
    }
}

assert_equal( null, globalfxhub_get_localized_editorial_comparison( 'no-such-pair' ), 'an unknown key returns null, not a fatal error' );

assert_test_summary_and_exit();
