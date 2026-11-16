<?php
/**
 * Localization QA: every globalfxhub_t()/globalfxhub_te() key actually
 * referenced in page-reviews.php must (a) have a real English source
 * in globalfxhub_string_text() -- not silently fall back to printing
 * the raw key name -- and (b) have a Spanish translation, so the
 * Reviews page never silently falls back to English UI chrome again
 * (the exact bug this fix addresses: "Broker Reviews", "Read review",
 * "Visit Broker", "Not confirmed", "Avg. spread", "Platforms",
 * "Score" were all hardcoded English on every non-English page).
 *
 * Spanish is checked strictly (fails the build). The other six live
 * languages (de, ar, it, pt, fr, pl) are checked too, but only as a
 * warning for now -- their Reviews-page translations are a tracked
 * follow-up, not yet done, so failing on them would block unrelated
 * work for a known, already-reported gap.
 *
 * This is scoped to page-reviews.php for now; add more files to
 * TEMPLATE_FILES as their own hardcoded-English audits get fixed the
 * same way.
 *
 * Run: php tests/test-reviews-page-translation-keys.php
 */

define( 'ABSPATH', __DIR__ . '/' );

require __DIR__ . '/test-helpers.php';

require __DIR__ . '/../inc/translatable-strings.php';

$TEMPLATE_FILES = array( 'page-reviews.php' );
$STRICT_LANG = 'es';
$WARN_ONLY_LANGS = array( 'de', 'ar', 'it', 'pt', 'fr', 'pl' );

$live = globalfxhub_live_string_translations();

$all_keys = array();
foreach ( $TEMPLATE_FILES as $file ) {
    $path = __DIR__ . '/../' . $file;
    $source = file_get_contents( $path );
    // Require the string literal to be the whole argument (immediately
    // closed by ")") so a dynamic call like
    // globalfxhub_te( 'fees' === $x ? 'key_a' : 'key_b' ) doesn't match
    // its ternary CONDITION ('fees') as if it were a translation key.
    preg_match_all( "/globalfxhub_te?\\(\\s*'([a-zA-Z0-9_]+)'\\s*\\)/", $source, $matches );
    foreach ( $matches[1] as $key ) {
        $all_keys[ $key ] = true;
    }
}
// Keys only reachable through a dynamic expression (e.g. a ternary
// picking between two keys), which the regex above can't see --
// tracked explicitly instead so they still get checked.
$known_dynamic_keys = array(
    'rev_research_eyebrow_fees',
    'rev_research_eyebrow_regulation',
);
foreach ( $known_dynamic_keys as $key ) {
    $all_keys[ $key ] = true;
}

$all_keys = array_keys( $all_keys );
sort( $all_keys );

echo 'Found ' . count( $all_keys ) . " translation keys referenced across " . implode( ', ', $TEMPLATE_FILES ) . ".\n\n";

foreach ( $all_keys as $key ) {
    $en = globalfxhub_string_text( $key );
    // globalfxhub_string_text() returns the key itself when it has no
    // registered English source -- that's the exact "falls back
    // silently to the raw key" failure mode.
    assert_equal( true, $en !== $key, "'$key' has a registered English source (not just echoing the raw key)" );
    assert_equal( true, isset( $live[ $STRICT_LANG ][ $key ] ) && '' !== $live[ $STRICT_LANG ][ $key ], "'$key' has a non-empty '$STRICT_LANG' translation" );
}

echo "\n--- Warn-only: other live languages (not yet translated for these keys, tracked as a follow-up) ---\n";
foreach ( $WARN_ONLY_LANGS as $lang ) {
    $missing = array();
    foreach ( $all_keys as $key ) {
        if ( ! isset( $live[ $lang ][ $key ] ) || '' === $live[ $lang ][ $key ] ) {
            $missing[] = $key;
        }
    }
    if ( $missing ) {
        echo "  WARN: $lang is missing " . count( $missing ) . ' of ' . count( $all_keys ) . " keys (falls back to English on this page for now)\n";
    } else {
        echo "  ok: $lang has every key\n";
    }
}

assert_test_summary_and_exit();
