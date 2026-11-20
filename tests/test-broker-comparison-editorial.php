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
 *    the content EXACTLY, field for field -- hand-kept-in-sync per
 *    that script's own header comment, and this is the drift check
 *    that would catch it if they ever disagree.
 *
 * Separately, $DRAFT_COMPARISONS below covers the two newest
 * comparisons (hf-markets-xm, plus500-cmc-markets), which are
 * genuinely in WordPress 'draft' status on the live site right now --
 * same structural/parity checks, but asserting ABSENCE from every
 * publish-only mechanism (globalfxhub_get_editorial_comparisons(),
 * the review-page cross-links, the page-compare.php preset list)
 * instead of presence.
 *
 * Run: php tests/test-broker-comparison-editorial.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

$GLOBALS['__test_pll_lang'] = null;
function pll_current_language() { return $GLOBALS['__test_pll_lang']; }

function globalfxhub_get_broker_by_slug( $slug ) {
    $brokers = array(
        'avatrade'    => array( 'slug' => 'avatrade', 'name' => 'AvaTrade' ),
        'pepperstone' => array( 'slug' => 'pepperstone', 'name' => 'Pepperstone' ),
        'fp-markets'  => array( 'slug' => 'fp-markets', 'name' => 'FP Markets' ),
        'ic-markets'  => array( 'slug' => 'ic-markets', 'name' => 'IC Markets' ),
        'oanda'       => array( 'slug' => 'oanda', 'name' => 'OANDA' ),
        'ig'          => array( 'slug' => 'ig', 'name' => 'IG' ),
        'hf-markets'  => array( 'slug' => 'hf-markets', 'name' => 'HF Markets' ),
        'xm'          => array( 'slug' => 'xm', 'name' => 'XM' ),
        'plus500'     => array( 'slug' => 'plus500', 'name' => 'Plus500' ),
        'cmc-markets' => array( 'slug' => 'cmc-markets', 'name' => 'CMC Markets' ),
    );
    return $brokers[ $slug ] ?? null;
}
function globalfxhub_track_b_url( $path ) { return 'https://globalfxhub.net/' . $path; }

/**
 * Stand-ins for get_posts()/get_post_meta(), simulating the live
 * 'gfh_comparison' CPT (inc/broker-comparison-cpt.php) this standalone
 * harness has no real WP/database to query. Fixture shape is
 * {slug: {status, data}} -- the 5 pre-existing comparisons (status
 * 'publish', content snapshotted via a one-off `php -r` dump of the
 * old hardcoded array before this architecture change) plus the 2
 * newest drafts (status 'draft', content matching
 * i18n/data/new-draft-comparisons.json, the actual input fed to
 * i18n/create_comparison_posts.py when they were created live) --
 * i.e. this harness simulates the REAL current state of the live site
 * as of both changes.
 */
$GLOBALS['__test_wp_comparison_posts'] = json_decode(
    file_get_contents( __DIR__ . '/fixtures/gfh-comparison-posts.json' ),
    true
);
$GLOBALS['__test_wp_comparison_status'] = array();
function get_posts( $args ) {
    if ( 'gfh_comparison' !== ( $args['post_type'] ?? null ) ) {
        return array();
    }
    $requested_status = $args['post_status'] ?? 'publish';
    $posts = array();
    $id = 1;
    foreach ( $GLOBALS['__test_wp_comparison_posts'] as $slug => $entry ) {
        $actual_status = $GLOBALS['__test_wp_comparison_status'][ $slug ] ?? $entry['status'];
        if ( $actual_status === $requested_status ) {
            $posts[] = (object) array( 'ID' => $id, 'post_name' => $slug );
        }
        $id++;
    }
    return $posts;
}
function get_post_meta( $post_id, $key, $single = false ) {
    if ( 'gfh_cmp_data' !== $key ) {
        return '';
    }
    $slugs = array_keys( $GLOBALS['__test_wp_comparison_posts'] );
    $slug = $slugs[ $post_id - 1 ] ?? null;
    if ( null === $slug ) {
        return '';
    }
    return wp_json_encode( $GLOBALS['__test_wp_comparison_posts'][ $slug ]['data'] );
}
function wp_json_encode( $data ) { return json_encode( $data ); }

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
    'oanda-ig' => array(
        'a_slug' => 'oanda', 'a_name' => 'OANDA',
        'b_slug' => 'ig', 'b_name' => 'IG',
        'verdict_must_contain' => array( '4.13', '4.03', '0.6', '5.0', '3.4' ),
    ),
    'hf-markets-xm' => array(
        'a_slug' => 'hf-markets', 'a_name' => 'HF Markets',
        'b_slug' => 'xm', 'b_name' => 'XM',
        'verdict_must_contain' => array( '3.47', '3.34', '3.4', '1.1', '1.2' ),
    ),
    'plus500-cmc-markets' => array(
        'a_slug' => 'plus500', 'a_name' => 'Plus500',
        'b_slug' => 'cmc-markets', 'b_name' => 'CMC Markets',
        'verdict_must_contain' => array( '3.65', '3.62', '5.0', '4.2', '1.3' ),
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

    // English fallback when no language is resolved at all (e.g. Polylang inactive).
    $GLOBALS['__test_pll_lang'] = null;
    $localized = globalfxhub_get_localized_editorial_comparison( $key );
    assert_equal( $entry['meta_title'], $localized['meta_title'], "'$key': with no resolved language, the localized getter returns the English entry unchanged" );
    assert_equal( $entry['verdict'], $localized['verdict'], "'$key': verdict is unchanged (English fallback)" );

    // Real translations: each of the 7 live languages now has actual
    // translated content (the "i18n translate comparison editorial"
    // workflow has run for this key) -- every field resolves, differs
    // from the English source, and every FAQ/point count matches.
    foreach ( array( 'es', 'de', 'ar', 'fr', 'pl', 'it', 'pt' ) as $lang ) {
        $GLOBALS['__test_pll_lang'] = $lang;
        $loc = globalfxhub_get_localized_editorial_comparison( $key );
        assert_equal( true, '' !== trim( $loc['meta_title'] ), "'$key'/'$lang': localized meta_title is non-empty" );
        assert_equal( true, $loc['meta_title'] !== $entry['meta_title'], "'$key'/'$lang': localized meta_title actually differs from English (real translation, not a silent fallback)" );
        assert_equal( true, $loc['verdict'] !== $entry['verdict'], "'$key'/'$lang': localized verdict differs from English" );
        assert_equal( count( $entry['choose_a_points'] ), count( $loc['choose_a_points'] ), "'$key'/'$lang': choose_a_points count matches the English structure" );
        assert_equal( count( $entry['choose_b_points'] ), count( $loc['choose_b_points'] ), "'$key'/'$lang': choose_b_points count matches the English structure" );
        assert_equal( count( $entry['faqs'] ), count( $loc['faqs'] ), "'$key'/'$lang': faqs count matches the English structure" );
        foreach ( $loc['choose_a_points'] as $i => $point ) {
            assert_equal( true, '' !== trim( $point ), "'$key'/'$lang': choose_a_points[$i] is non-empty" );
        }
        foreach ( $loc['faqs'] as $i => $faq ) {
            assert_equal( true, '' !== trim( $faq['q'] ) && '' !== trim( $faq['a'] ), "'$key'/'$lang': faqs[$i] is fully non-empty" );
        }
        // a_slug/b_slug never get translated (no 'a_slug'/'b_slug' key in
        // the translation map), so the overlay must leave them as-is.
        assert_equal( $entry['a_slug'], $loc['a_slug'], "'$key'/'$lang': a_slug is untouched by the translation overlay" );
        assert_equal( $entry['b_slug'], $loc['b_slug'], "'$key'/'$lang': b_slug is untouched by the translation overlay" );
    }
    $GLOBALS['__test_pll_lang'] = null;

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

echo "\n--- oanda-ig: honesty check -- OANDA's real cost score is null (unconfirmed spread), so the entry must say so rather than fabricate a cost figure for it ---\n";
$oanda_ig = $all['oanda-ig'];
$oanda_ig_full_text = $oanda_ig['verdict'] . ' ' . implode( ' ', $oanda_ig['choose_b_points'] ) . ' ' . $oanda_ig['worth_knowing'];
assert_equal( true, false !== strpos( $oanda_ig_full_text, "isn't confirmed" ) || false !== strpos( $oanda_ig_full_text, 'unconfirmed' ), "oanda-ig entry explicitly flags OANDA's spread as unconfirmed somewhere (verdict, choose points, or worth-knowing), rather than inventing a cost score for it" );
assert_equal( false, (bool) preg_match( '/OANDA[^.]*cost score of \d/', $oanda_ig['verdict'] ), 'the verdict never attributes a specific numeric cost score to OANDA (it has none on file)' );

echo "\n--- CPT-querying behavior: globalfxhub_get_editorial_comparisons() asks for 'publish'-status posts only, and a draft is excluded ---\n";
$GLOBALS['__test_wp_comparison_status']['avatrade-pepperstone'] = 'draft';
$draft_excluded = globalfxhub_get_editorial_comparisons( true );
assert_equal( false, isset( $draft_excluded['avatrade-pepperstone'] ), "a fixture post marked 'draft' is excluded from globalfxhub_get_editorial_comparisons()'s results" );
assert_equal( true, isset( $draft_excluded['oanda-ig'] ), "a still-'publish' fixture post is unaffected by a sibling draft" );
unset( $GLOBALS['__test_wp_comparison_status']['avatrade-pepperstone'] );
globalfxhub_get_editorial_comparisons( true ); // restore the cache to all-published for anything that runs after

// --- The two newest comparisons (hf-markets-xm, plus500-cmc-markets)
// are genuinely in 'draft' status on the live site right now -- per
// the user's explicit instruction, new content goes into WordPress
// drafts, not straight to publish. So these get a DIFFERENT set of
// checks than $NEW_COMPARISONS above: structural completeness and the
// Python translation-script mirror are still checked (both already
// exist and need to be correct before a human publishes), but the
// publish-only mechanisms (appearing in globalfxhub_get_editorial_
// comparisons(), the review-page cross-links, the page-compare.php
// preset list) are checked for ABSENCE, since a draft must not
// surface through any of them yet. ---
// No comparisons are currently sitting in draft -- hf-markets-xm and
// plus500-cmc-markets (the last two drafted) were reviewed in wp-admin
// and published, so they moved up into $NEW_COMPARISONS above. This
// loop stays in place, empty, as the harness for whatever's drafted
// next.
$DRAFT_COMPARISONS = array();

foreach ( $DRAFT_COMPARISONS as $key => $expect ) {
    echo "\n=== $key (draft) ===\n";

    assert_equal( 'draft', $GLOBALS['__test_wp_comparison_posts'][ $key ]['status'] ?? null, "'$key' fixture is marked 'draft', matching its real status on the live site" );
    assert_equal( false, isset( $all[ $key ] ), "'$key' does NOT appear in globalfxhub_get_editorial_comparisons()'s published results while draft" );

    $from_a = globalfxhub_editorial_comparisons_for_broker( $expect['a_slug'] );
    assert_equal( 0, count( $from_a ), "'$key': {$expect['a_slug']}'s review page does NOT yet link to this still-draft comparison" );
    $from_b = globalfxhub_editorial_comparisons_for_broker( $expect['b_slug'] );
    assert_equal( 0, count( $from_b ), "'$key': {$expect['b_slug']}'s review page does NOT yet link to this still-draft comparison" );

    assert_equal( false, false !== strpos( $compare_source, "array( '" . $expect['a_slug'] . "', '" . $expect['b_slug'] . "' )" ), "'$key': page-compare.php's \$vs_preset_pairs does NOT yet include this still-draft pair" );

    $entry = $GLOBALS['__test_wp_comparison_posts'][ $key ]['data'];
    $required_scalar_fields = array(
        'meta_title', 'meta_description', 'intro', 'verdict',
        'choose_a_heading', 'choose_b_heading', 'worth_knowing_heading', 'worth_knowing',
    );
    foreach ( $required_scalar_fields as $field ) {
        assert_equal( true, isset( $entry[ $field ] ) && '' !== trim( $entry[ $field ] ), "'$key': '$field' is present and non-empty" );
    }
    assert_equal( $expect['a_slug'], $entry['a_slug'], "'$key': a_slug is '{$expect['a_slug']}' (lower rank)" );
    assert_equal( $expect['b_slug'], $entry['b_slug'], "'$key': b_slug is '{$expect['b_slug']}'" );
    assert_equal( true, count( $entry['choose_a_points'] ) >= 2, "'$key': choose_a_points has at least 2 points" );
    assert_equal( true, count( $entry['choose_b_points'] ) >= 2, "'$key': choose_b_points has at least 2 points" );
    assert_equal( true, count( $entry['faqs'] ) >= 2, "'$key': at least 2 FAQs" );
    foreach ( $entry['faqs'] as $i => $faq ) {
        assert_equal( true, '' !== trim( $faq['q'] ) && '' !== trim( $faq['a'] ), "'$key': faqs[$i] is fully non-empty" );
    }
    foreach ( $expect['verdict_must_contain'] as $figure ) {
        assert_equal( true, false !== strpos( $entry['verdict'], $figure ), "'$key': verdict cites real figure '$figure'" );
    }

    // Parity check: i18n/translate_comparison_editorial.py's
    // COMPARISONS dict must mirror the exact content this draft was
    // CREATED with (i18n/data/new-draft-comparisons.json, the actual
    // input i18n/create_comparison_posts.py sent to the REST API) --
    // there's no PHP array source anymore for new content, so this is
    // the drift check's new anchor.
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

        assert_equal( array_keys( $expected_flat ), array_keys( $python_entry ), "'$key': Python dict has exactly the same flattened keys, in the same order, as the draft's authored content" );
        foreach ( $expected_flat as $field_key => $expected_value ) {
            assert_equal( $expected_value, $python_entry[ $field_key ] ?? null, "'$key': Python COMPARISONS['$field_key'] matches the draft's authored content exactly (no copy-paste drift)" );
        }
    }
}

assert_test_summary_and_exit();
