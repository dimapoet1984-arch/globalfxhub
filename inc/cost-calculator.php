<?php
/**
 * True Cost Calculator (/cost-calculator/): estimates annual EUR/USD
 * trading cost at every researched broker from the trader's own inputs
 * (account size, trades/month, average trade size, holding period),
 * rather than quoting a single generic "our average spread" figure.
 *
 * What this does and doesn't cover, on purpose:
 *  - Spread cost is the one EUR/USD trading-cost component this site
 *    has a confirmed, structured, numeric figure for per broker
 *    (spread_eurusd, the same field the sitewide "cost" score is built
 *    from) -- so it's the only component this calculator computes.
 *  - Round-turn commission (common on ECN/Razor-style accounts) and
 *    overnight financing/swap are real costs, but they live in this
 *    site's broker data as free-text research notes (execution_model,
 *    overnight_financing), not as a structured number we could average
 *    or multiply with confidence across 139 differently-worded entries.
 *    Rather than regex-guess a number out of that prose -- which risks
 *    exactly the "generating numbers from unstructured data without a
 *    validation layer" problem already fixed elsewhere on this site --
 *    the calculator discloses both as real, uncounted costs instead of
 *    silently treating them as zero. A broker with a very low confirmed
 *    spread is NOT necessarily the lowest true cost once its commission
 *    is counted; the disclosure says so explicitly.
 *  - 74 of 139 researched brokers have no independently confirmed
 *    EUR/USD spread; those are excluded from the computed comparison
 *    (never assumed to be mid-pack or free), same convention as
 *    /best/low-cost.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * EUR/USD pip value per standard lot (100,000 units) in USD. Universal
 * forex arithmetic, not a broker-specific or site-specific figure: since
 * USD is EUR/USD's quote currency, one pip (0.0001) on a 100,000-unit
 * position is 0.0001 x 100,000 = $10, independent of which broker is
 * involved.
 */
function globalfxhub_cost_calculator_pip_value_per_lot() {
    return 10.0;
}

function globalfxhub_cost_calculator_holding_periods() {
    return array(
        'intraday' => 'Intraday -- closed the same trading day',
        'swing'    => 'A few days to a few weeks (swing trading)',
        'position' => '1+ months (position trading)',
    );
}

/**
 * Null when the broker's EUR/USD spread isn't independently confirmed --
 * callers must treat that as "cannot estimate", never as zero cost.
 */
function globalfxhub_broker_estimated_eurusd_cost( $broker, $trade_size_lots, $trades_per_month ) {
    if ( null === $broker['spread_eurusd'] || $trade_size_lots <= 0 || $trades_per_month <= 0 ) {
        return null;
    }
    $pip_value = globalfxhub_cost_calculator_pip_value_per_lot();
    $cost_per_trade = $broker['spread_eurusd'] * $pip_value * $trade_size_lots;
    $monthly = $cost_per_trade * $trades_per_month;
    $annual = $monthly * 12;
    return array(
        'cost_per_trade' => round( $cost_per_trade, 2 ),
        'monthly'        => round( $monthly, 2 ),
        'annual'         => round( $annual, 2 ),
    );
}

/**
 * How many standard lots of margin a given account size supports at the
 * EU retail leverage cap on major pairs (30:1 -- the same figure already
 * disclosed in globalfxhub_country_leverage_rules_html()), used only for
 * a soft feasibility note, never to block a calculation.
 */
function globalfxhub_cost_calculator_max_lots_at_cap( $account_size, $leverage_cap = 30 ) {
    if ( $account_size <= 0 ) {
        return 0.0;
    }
    return round( ( $account_size * $leverage_cap ) / 100000, 2 );
}

/**
 * Lean payload for the client-side calculator: only brokers with a
 * confirmed EUR/USD spread are included in the comparable set, but the
 * excluded count is reported too so the page can disclose it rather than
 * silently showing a shorter list with no explanation.
 */
function globalfxhub_cost_calculator_payload() {
    $brokers = globalfxhub_get_brokers();
    $comparable = array();
    $excluded_count = 0;

    foreach ( $brokers as $b ) {
        if ( null === $b['spread_eurusd'] ) {
            $excluded_count++;
            continue;
        }
        $comparable[] = array(
            'slug'             => $b['slug'],
            'name'             => $b['name'],
            'rank'             => $b['rank'],
            'spread_eurusd'    => $b['spread_eurusd'],
            'regulation_label' => globalfxhub_broker_regulation_label( $b ),
            'review_url'       => home_url( '/reviews/' . $b['slug'] . '/' ),
        );
    }

    return array(
        'brokers'        => $comparable,
        'excluded_count' => $excluded_count,
        'total_count'    => count( $brokers ),
    );
}

function globalfxhub_ensure_cost_calculator_page() {
    globalfxhub_ensure_templated_page( 'cost-calculator', 'True Cost Calculator', 'page-cost-calculator.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_cost_calculator_page' );
