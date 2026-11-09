<?php
/**
 * Editorial comparison content for specific, hand-curated broker pairs --
 * layered on top of the existing data-driven /compare/{a}-vs-{b}/ table
 * (inc/broker-vs.php), not a replacement for it. The table already
 * covers the mechanical side-by-side facts; this adds the "which
 * broker should YOU actually pick, and why" narrative a reader
 * searching "X vs Y" is really looking for -- see CLAUDE.md's content
 * strategy ("editorially curated comparisons, not generic database
 * dumps").
 *
 * Deliberately NOT a template applied to all ~780 eligible pairs --
 * each entry here is a hand-written piece grounded in that specific
 * pair's actual confirmed data (see globalfxhub_get_brokers()), added
 * one at a time as the editorial calendar reaches that pair. A pair
 * with no entry here just shows the existing data table, unchanged.
 *
 * Every claim below traces back to a field already in this broker's
 * own array -- nothing here is a new, separately-sourced fact.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Editorial comparisons involving a given broker (either side), for
 * contextual linking from that broker's own review page -- a reader
 * already looking at this broker is exactly who an editorially-curated
 * "X vs Y" verdict is for, unlike a sitewide link to the bare /compare/
 * tool. Relies on each entry's explicit 'a_slug'/'b_slug' rather than
 * parsing them back out of the array key, since a broker slug can
 * itself contain a hyphen (e.g. "ic-markets"), making the key
 * ambiguous to split.
 */
function globalfxhub_editorial_comparisons_for_broker( $slug ) {
    $matches = array();
    foreach ( globalfxhub_get_editorial_comparisons() as $key => $entry ) {
        if ( ! in_array( $slug, array( $entry['a_slug'], $entry['b_slug'] ), true ) ) {
            continue;
        }
        $other_slug = ( $entry['a_slug'] === $slug ) ? $entry['b_slug'] : $entry['a_slug'];
        $other = globalfxhub_get_broker_by_slug( $other_slug );
        if ( ! $other ) {
            continue;
        }
        $matches[] = array(
            'url'   => home_url( '/compare/' . $key . '/' ),
            'title' => $entry['meta_title'] ?? ( $entry['a_slug'] . ' vs ' . $entry['b_slug'] ),
            'other' => $other,
        );
    }
    return $matches;
}

function globalfxhub_get_editorial_comparisons() {
    return array(
        'pepperstone-ic-markets' => array(
            'a_slug'           => 'pepperstone',
            'b_slug'           => 'ic-markets',
            'meta_title'       => 'Pepperstone vs IC Markets (2026): Which Broker Should You Choose?',
            'meta_description' => 'Pepperstone vs IC Markets compared on regulation, cost, platforms, and track record -- using our disclosed methodology, not marketing claims. See which fits your trading style.',
            'intro' => 'Pepperstone and IC Markets are both built around the same pitch: tight ECN-style pricing aimed at active and algorithmic traders, not a mass-market retail product. That similarity is exactly why they get compared so often -- but the two brokers actually differ in ways that matter more than their shared "built for serious traders" positioning suggests.',
            'verdict' => 'Pepperstone holds the wider Tier-1 regulatory footprint (CySEC, FCA, ASIC, and DFSA, against IC Markets\' CySEC and ASIC), a far lower minimum deposit ($10 vs $200), and one more supported platform (TradingView, alongside MT4/MT5/cTrader). IC Markets edges ahead on our on-file headline EUR/USD spread (0.8 pips against Pepperstone\'s 1.1), and has a longer operating history (founded 2007 vs 2010) -- though both brokers also offer a separate raw-spread-plus-commission tier not captured by that single headline figure, so check each broker\'s current account terms for your specific setup. Under our disclosed methodology, Pepperstone scores 3.64/5 overall against IC Markets\' 3.07/5, driven mainly by the regulation category (30% of the total score, and Pepperstone\'s biggest advantage).',
            'choose_a_heading' => 'Choose Pepperstone if...',
            'choose_a_points' => array(
                'You want the broader regulatory footprint -- Pepperstone holds CySEC, FCA, ASIC, and DFSA authorisation, against IC Markets\' CySEC and ASIC.',
                'You\'re starting with a small account -- Pepperstone\'s $10 minimum deposit is a fraction of IC Markets\' $200.',
                'You trade on TradingView -- Pepperstone supports it directly; IC Markets does not.',
                'An explicit no-inactivity-fee policy matters to you -- multiple independent sources confirm Pepperstone charges none, with no unresolved conflict in our research.',
            ),
            'choose_b_heading' => 'Choose IC Markets if...',
            'choose_b_points' => array(
                'Raw headline spread is your priority and you can meet the $200 minimum -- IC Markets\' 0.8-pip EUR/USD spread is tighter than Pepperstone\'s 1.1 pips on the comparable tier.',
                'A longer operating history matters to you -- IC Markets has operated since 2007, three years longer than Pepperstone.',
                'You don\'t need DFSA coverage or TradingView, and ASIC plus CySEC is enough regulatory footprint for your needs.',
            ),
            'worth_knowing_heading' => 'Worth knowing before you decide',
            'worth_knowing' => 'IC Markets\' EU entity publishes a formal Inactivity Fees Policy (an account is defined as inactive after 6 continuous months with no executed trade), but multiple independent comparison sites report that no fee is actually charged in practice -- a genuine, unresolved conflict between the broker\'s own published policy and third-party reporting that we could not resolve in our research. If dormancy risk matters to you, confirm the current position directly with IC Markets before relying on either source. Pepperstone\'s no-inactivity-fee claim, by contrast, is consistently corroborated across the sources we checked.',
            'faqs' => array(
                array(
                    'q' => 'Is Pepperstone or IC Markets better for beginners?',
                    'a' => 'Pepperstone\'s $10 minimum deposit makes it far more accessible to start with than IC Markets\' $200, and its wider regulatory footprint (adding FCA and DFSA alongside CySEC and ASIC) gives a beginner more regulatory coverage to fall back on. Neither broker\'s account structure is explicitly designed for beginners over active traders, though.',
                ),
                array(
                    'q' => 'Which has the lower trading cost, Pepperstone or IC Markets?',
                    'a' => 'On our on-file headline EUR/USD spread, IC Markets is tighter at 0.8 pips against Pepperstone\'s 1.1 pips. Both also offer a separate raw-spread-plus-commission tier (Pepperstone\'s Razor, IC Markets\' Raw Spread) with broadly similar commission levels -- check each broker\'s own current fee schedule for your specific account type before relying on either headline figure.',
                ),
            ),
        ),
    );
}
