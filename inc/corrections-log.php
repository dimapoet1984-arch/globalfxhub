<?php
/**
 * A public log of MATERIAL corrections -- ones that would have changed
 * what a reader took away (a wrong licence number, a materially
 * outdated regulatory status, a cost figure that was actually wrong
 * rather than just re-checked and reconfirmed). Deliberately does not
 * log trivial fixes (typos, rewording, formatting) -- those would make
 * this log noisy rather than useful, and aren't what a reader checking
 * "has this site gotten things wrong before, and did it own up to it"
 * actually wants to see.
 *
 * This is distinct from inc/broker-changelog.php, which auto-detects
 * when a tracked FIELD VALUE changes (a new spread, a new licence
 * number) regardless of whether the old value was ever wrong -- most
 * changelog entries are just "this changed because the real-world fact
 * changed," not "we got this wrong." A correction is specifically the
 * latter: something we published was incorrect, and we're saying so.
 *
 * Starts empty. Entries are added by hand here -- by a human editor,
 * never fabricated or backfilled to look more established than this
 * site's actual track record -- each time a material correction is
 * actually made. See docs on globalfxhub_correction_entry_shape() below
 * for exactly what to fill in.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Each entry: 'date' (when the correction was made), 'page' (title +
 * URL of the affected page), 'previous' (what we had published),
 * 'corrected' (what it was changed to), 'reason' (why -- what we found
 * and how), 'source' (the URL/citation that prompted or confirms the
 * correction). Newest first.
 */
function globalfxhub_get_corrections() {
    return array(
        // Example shape for the next real correction -- remove this
        // comment and add a real array entry when one actually happens:
        // array(
        //     'date'      => '12 November 2026',
        //     'page'      => array( 'title' => 'Pepperstone review', 'url' => home_url( '/reviews/pepperstone/' ) ),
        //     'previous'  => 'Listed as holding an FCA licence.',
        //     'corrected' => 'FCA licence removed -- the FCA entity belongs to a separate group company, not the one reviewed here.',
        //     'reason'    => 'A reader flagged the mismatch; re-checked directly against the FCA register and found the licence number didn\'t resolve to this broker\'s reviewed entity.',
        //     'source'    => 'https://register.fca.org.uk/',
        // ),
    );
}

function globalfxhub_ensure_corrections_page() {
    globalfxhub_ensure_templated_page( 'corrections', 'Corrections Log', 'page-corrections.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_corrections_page' );
