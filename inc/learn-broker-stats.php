<?php
/**
 * Task 23 of the content audit ("add unique data"): a handful of Learn
 * articles talk about a concept in the abstract (inactivity fees exist;
 * brokers use different execution models; some regulators are
 * stricter than others) without ever citing this site's own 139-broker
 * research to back it up -- an original stat or table this site is
 * uniquely positioned to provide, that no generic "what is an
 * inactivity fee" article elsewhere on the web can.
 *
 * Every number here is computed fresh from globalfxhub_get_brokers()
 * on every request, using the SAME classification logic the live
 * scoring functions already use (globalfxhub_score_non_trading_fees(),
 * globalfxhub_score_execution(), the regulation-count logic in
 * globalfxhub_broker_scores()) -- never a hand-typed snapshot that
 * would drift out of sync with the dataset or go stale as broker data
 * changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_stat_tier1_vs_offshore() {
    $brokers = globalfxhub_get_brokers();
    $tier1 = 0;
    foreach ( $brokers as $b ) {
        $cysec_held = ( null !== $b['cysec'] ) ? 1 : 0;
        $fca_held   = ( ! empty( $b['fca'] ) || ! empty( $b['fca_note'] ) ) ? 1 : 0;
        if ( $cysec_held || $fca_held || (int) $b['other_reg_count'] > 0 ) {
            $tier1++;
        }
    }
    return array( 'total' => count( $brokers ), 'tier1' => $tier1, 'offshore_only' => count( $brokers ) - $tier1 );
}

function globalfxhub_stat_inactivity_fee_prevalence() {
    $brokers = globalfxhub_get_brokers();
    $no_fee = 0;
    $confirmed_fee = 0;
    $unconfirmed = 0;
    foreach ( $brokers as $b ) {
        $text = $b['inactivity_fee'] ?? null;
        if ( empty( $text ) || false !== stripos( $text, 'not found' ) ) {
            $unconfirmed++;
        } elseif ( preg_match( '/\bno inactivity fee|does not charge an? inactivity|not charge an inactivity fee/i', $text ) ) {
            $no_fee++;
        } else {
            $confirmed_fee++;
        }
    }
    return array( 'total' => count( $brokers ), 'no_fee' => $no_fee, 'confirmed_fee' => $confirmed_fee, 'unconfirmed' => $unconfirmed );
}

function globalfxhub_stat_execution_model_disclosure() {
    $brokers = globalfxhub_get_brokers();
    $no_dealing_desk = 0;
    $dealing_desk = 0;
    $unconfirmed = 0;
    foreach ( $brokers as $b ) {
        $model = $b['execution_model'] ?? '';
        $has_ndd = (bool) preg_match( '/\b(ecn|stp|dma|no[\s-]?dealing[\s-]?desk)\b/i', $model );
        $has_dd  = (bool) preg_match( '/\b(market[\s-]?maker|dealing[\s-]?desk)\b/i', $model ) && ! preg_match( '/no[\s-]?dealing[\s-]?desk/i', $model );
        if ( $has_ndd ) {
            $no_dealing_desk++;
        }
        if ( $has_dd ) {
            $dealing_desk++;
        }
        if ( ! $has_ndd && ! $has_dd ) {
            $unconfirmed++;
        }
    }
    return array( 'total' => count( $brokers ), 'no_dealing_desk' => $no_dealing_desk, 'dealing_desk' => $dealing_desk, 'unconfirmed' => $unconfirmed );
}

/**
 * Appends one dynamically-computed stat paragraph to a small, fixed
 * set of Learn articles whose topic directly matches a stat above --
 * keyed by post_name, inserted once per request (never written into
 * stored post_content, so it can never drift from the live dataset
 * the way a hand-pasted number would).
 */
function globalfxhub_learn_broker_stats_content( $content ) {
    if ( ! is_singular( 'post' ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }
    $slug = get_post_field( 'post_name', get_the_ID() );

    if ( 'tier-1-forex-regulators-explained' === $slug ) {
        $s = globalfxhub_stat_tier1_vs_offshore();
        $content .= '<h2>What this looks like across our own research</h2><p>Of the ' . esc_html( $s['total'] ) . ' brokers we currently research, ' . esc_html( $s['tier1'] ) . ' hold at least one Tier-1-grade regulator (FCA, CySEC, ASIC, FSCA, DFSA, or CMA) alongside or instead of an offshore licence, while ' . esc_html( $s['offshore_only'] ) . ' rely solely on an offshore regulator such as the Seychelles FSA. See our full <a href="/reviews/">broker reviews</a> for the specific licence held by any individual broker.</p>';
    } elseif ( 'forex-broker-inactivity-fees-explained' === $slug ) {
        $s = globalfxhub_stat_inactivity_fee_prevalence();
        $content .= '<h2>What this looks like across our own research</h2><p>Across the ' . esc_html( $s['total'] ) . ' brokers we research, ' . esc_html( $s['confirmed_fee'] ) . ' are confirmed to charge an inactivity fee, ' . esc_html( $s['no_fee'] ) . ' are confirmed NOT to charge one, and the remaining ' . esc_html( $s['unconfirmed'] ) . ' we could not independently confirm either way -- it is genuinely a coin flip you need to check per broker, not something you can assume from the broker\'s size or reputation. See our <a href="/reviews/">individual broker reviews</a> for a specific broker\'s confirmed policy.</p>';
    } elseif ( 'market-maker-vs-ecn-vs-stp-brokers' === $slug ) {
        $s = globalfxhub_stat_execution_model_disclosure();
        $content .= '<h2>What this looks like across our own research</h2><p>Of the ' . esc_html( $s['total'] ) . ' brokers we research, ' . esc_html( $s['no_dealing_desk'] ) . ' disclose a no-dealing-desk-style model (ECN, STP, or DMA) for at least one account type, and ' . esc_html( $s['dealing_desk'] ) . ' disclose a market-maker/dealing-desk model; the remaining ' . esc_html( $s['unconfirmed'] ) . ' either weren\'t independently confirmed one way or the other, or have sources that disagree (so a broker can appear in more than one count above). See our <a href="/reviews/">broker reviews</a> for which model a specific broker discloses.</p>';
    }

    return $content;
}
add_filter( 'the_content', 'globalfxhub_learn_broker_stats_content', 15 );
