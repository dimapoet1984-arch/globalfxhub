<?php
/**
 * "Best brokers" commercial rankings: filtered, re-sorted views of the
 * same 139-broker dataset and the same disclosed absolute-rubric scores
 * used everywhere else on the site -- never a separate, looser standard
 * just because the page is commercially framed. Each list's filter and
 * sort key is fixed and disclosed in its own intro copy, same spirit as
 * /countries/.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_get_best_lists() {
    return array(
        'overall' => array(
            'title'      => 'Best Forex & CFD Brokers Overall',
            'intro'      => 'Every broker in our researched set, ranked by overall score under our nine-category disclosed methodology -- the same ranking used sitewide, not a separate "best of" standard.',
            'sort_label' => 'Overall score',
            'filter'     => null,
            'sort_key'   => function( $b ) { return $b['scores']['overall'] ?? 0; },
        ),
        'beginners' => array(
            'title'      => 'Best Forex Brokers for Beginners',
            'intro'      => 'Filtered to brokers with a confirmed minimum deposit of $250 or less and a regulation score of at least 3.0 (meaning at least two independently-verifiable regulators, or one Tier-1 regulator), then ranked by overall score.',
            'sort_label' => 'Overall score',
            'filter'     => function( $b ) { return null !== $b['min_deposit_usd'] && $b['min_deposit_usd'] <= 250 && ( $b['scores']['regulation'] ?? 0 ) >= 3.0; },
            'sort_key'   => function( $b ) { return $b['scores']['overall'] ?? 0; },
        ),
        'low-cost' => array(
            'title'      => 'Best Low-Cost Forex Brokers',
            'intro'      => 'Filtered to brokers with a confirmed EUR/USD spread, ranked by our trading-costs score -- spread only, not minimum deposit (a low minimum makes a broker more accessible, not cheaper to actually trade with).',
            'sort_label' => 'Trading costs score',
            'filter'     => function( $b ) { return null !== $b['scores']['cost']; },
            'sort_key'   => function( $b ) { return $b['scores']['cost']; },
        ),
        'ecn-brokers' => array(
            'title'      => 'Best ECN/STP Forex Brokers',
            'intro'      => 'Filtered to brokers whose execution model is confirmed as ECN, STP, or no-dealing-desk somewhere on their review page, then ranked by our execution/trading-conditions score.',
            'sort_label' => 'Execution score',
            'filter'     => function( $b ) { return ! empty( $b['execution_model'] ) && preg_match( '/\b(ecn|stp|no[\s-]?dealing[\s-]?desk|dma)\b/i', $b['execution_model'] ); },
            'sort_key'   => function( $b ) { return $b['scores']['execution'] ?? 0; },
        ),
        'cysec-regulated' => array(
            'title'      => 'Best CySEC-Regulated Brokers',
            'intro'      => 'Filtered to brokers holding a direct CySEC licence or EU/MiFID-passported regulation, ranked by overall score. CySEC-regulated brokers carry EU investor-compensation-scheme protection -- see our <a href="' . home_url( '/regulation/cysec/' ) . '">CySEC regulation page</a> for what that actually covers.',
            'sort_label' => 'Overall score',
            'filter'     => function( $b ) { return null !== $b['cysec']; },
            'sort_key'   => function( $b ) { return $b['scores']['overall'] ?? 0; },
        ),
        'fca-regulated' => array(
            'title'      => 'Best FCA-Regulated (UK) Brokers',
            'intro'      => 'Filtered to brokers holding a direct UK Financial Conduct Authority licence, ranked by overall score. See our <a href="' . home_url( '/regulation/fca/' ) . '">FCA regulation page</a> for what FSCS protection actually covers.',
            'sort_label' => 'Overall score',
            'filter'     => function( $b ) { return ! empty( $b['fca'] ) || ! empty( $b['fca_note'] ); },
            'sort_key'   => function( $b ) { return $b['scores']['overall'] ?? 0; },
        ),
        'multi-asset' => array(
            'title'      => 'Best Multi-Asset Brokers',
            'intro'      => 'Ranked by our product-range score -- the breadth of confirmed asset classes (forex, indices, shares, commodities, crypto, bonds, ETFs, and more) each broker offers.',
            'sort_label' => 'Product range score',
            'filter'     => null,
            'sort_key'   => function( $b ) { return $b['scores']['product_range'] ?? 0; },
        ),
        'mt4-mt5' => array(
            'title'      => 'Best MT4 / MT5 Brokers',
            'intro'      => 'Filtered to brokers confirmed to support MetaTrader 4 or MetaTrader 5, ranked by overall score.',
            'sort_label' => 'Overall score',
            'filter'     => function( $b ) { return in_array( 'MT4', $b['platforms'], true ) || in_array( 'MT5', $b['platforms'], true ); },
            'sort_key'   => function( $b ) { return $b['scores']['overall'] ?? 0; },
        ),
    );
}

/**
 * Returns a best-list's brokers, already filtered and sorted
 * descending, with ties broken by the existing overall rank so the
 * order is stable rather than arbitrary.
 */
function globalfxhub_get_best_list_brokers( $slug, $limit = 15 ) {
    $lists = globalfxhub_get_best_lists();
    if ( ! isset( $lists[ $slug ] ) ) {
        return array();
    }
    $list = $lists[ $slug ];
    $brokers = globalfxhub_get_brokers();

    if ( $list['filter'] ) {
        $brokers = array_values( array_filter( $brokers, $list['filter'] ) );
    }

    usort( $brokers, function( $a, $b ) use ( $list ) {
        $sa = call_user_func( $list['sort_key'], $a );
        $sb = call_user_func( $list['sort_key'], $b );
        if ( $sa === $sb ) {
            return $a['rank'] <=> $b['rank'];
        }
        return $sb <=> $sa;
    } );

    return array_slice( $brokers, 0, $limit );
}
