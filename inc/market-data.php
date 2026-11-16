<?php
/**
 * Live market data via Twelve Data (https://twelvedata.com), fetched on a
 * schedule rather than per pageview -- normal site traffic never spends an
 * API credit, only the scheduled refresh does. This is the deliberate
 * choice behind every design decision in this file: one batched request
 * per refresh (all symbols in a single call), a conservative default
 * interval, and a hard minimum-gap guard so a misfiring cron event can
 * never fetch twice in quick succession.
 *
 * Requires the site's Twelve Data API key to be defined in wp-config.php
 * (never in this repo, since it's a public theme):
 *   define( 'GLOBALFXHUB_TWELVEDATA_API_KEY', 'xxxxxxxxxxxx' );
 * Until that constant exists, or if a fetch ever fails, every symbol
 * quietly falls back to the last successfully fetched value, or -- if
 * nothing has ever been fetched -- to the static illustrative figures
 * below, so the page never breaks or shows blanks.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBALFXHUB_MARKET_DATA_OPTION', 'globalfxhub_market_data' );

/**
 * The instruments shown across the ticker, heatmap, and movers list.
 * 'decimals'/'thousands' control display formatting only.
 *
 * Deliberately capped at 8 symbols: Twelve Data's free tier enforces an
 * 8-API-credit-per-minute limit (confirmed live -- a 12-symbol batch
 * came back HTTP 429, "12 API credits were used, with the current limit
 * being 8"), and since the /quote endpoint costs 1 credit per symbol
 * even when batched into one request, 8 symbols in one call is the most
 * this plan can ever fetch in a single minute. Don't add a 9th without
 * either dropping one of these or upgrading the Twelve Data plan --
 * otherwise every scheduled fetch will 429 again, permanently, not just
 * occasionally.
 *
 * Also confirmed live: this plan only serves forex pairs plus XAU/USD
 * (gold) -- XAG/USD (silver) and WTI/USD (oil) both came back HTTP 403
 * "symbol is not available with your plan", not a bad symbol name. So
 * this list sticks to forex majors + gold; adding any other commodity
 * back will need a higher Twelve Data tier, confirmed via the debug
 * endpoint before relying on it.
 */
function globalfxhub_market_symbols() {
    return array(
        'EUR/USD' => array( 'label' => 'EUR/USD', 'category' => 'Major', 'decimals' => 4, 'thousands' => false, 'in_ticker' => true ),
        'GBP/USD' => array( 'label' => 'GBP/USD', 'category' => 'Major', 'decimals' => 4, 'thousands' => false, 'in_ticker' => true ),
        'USD/JPY' => array( 'label' => 'USD/JPY', 'category' => 'Major', 'decimals' => 2, 'thousands' => false, 'in_ticker' => true ),
        'AUD/USD' => array( 'label' => 'AUD/USD', 'category' => 'Major', 'decimals' => 4, 'thousands' => false, 'in_ticker' => true ),
        'USD/CAD' => array( 'label' => 'USD/CAD', 'category' => 'Major', 'decimals' => 4, 'thousands' => false, 'in_ticker' => true ),
        'USD/CHF' => array( 'label' => 'USD/CHF', 'category' => 'Major', 'decimals' => 4, 'thousands' => false, 'in_ticker' => false ),
        'NZD/USD' => array( 'label' => 'NZD/USD', 'category' => 'Major', 'decimals' => 4, 'thousands' => false, 'in_ticker' => false ),
        'XAU/USD' => array( 'label' => 'XAU/USD', 'category' => 'Gold',  'decimals' => 2, 'thousands' => true,  'in_ticker' => true ),
    );
}

/**
 * Static illustrative figures, used until a symbol has a real fetched
 * quote to show. Kept close to what the page originally hardcoded so the
 * transition to live data isn't a visual jump.
 */
function globalfxhub_market_fallback_data() {
    return array(
        'EUR/USD' => array( 'close' => 1.0834,    'percent_change' => 0.12 ),
        'GBP/USD' => array( 'close' => 1.2651,    'percent_change' => -0.08 ),
        'USD/JPY' => array( 'close' => 149.32,    'percent_change' => 0.21 ),
        'AUD/USD' => array( 'close' => 0.6598,    'percent_change' => -0.04 ),
        'USD/CAD' => array( 'close' => 1.3572,    'percent_change' => 0.06 ),
        'USD/CHF' => array( 'close' => 0.8821,    'percent_change' => 0.18 ),
        'NZD/USD' => array( 'close' => 0.6104,    'percent_change' => -0.22 ),
        'XAU/USD' => array( 'close' => 2412.80,   'percent_change' => 1.42 ),
    );
}

/**
 * Fetches all symbols in ONE batched request (Twelve Data's /quote
 * endpoint accepts a comma-separated symbol list) and merges successful
 * results into the stored option -- a symbol that errors or isn't found
 * simply keeps its previous cached value rather than wiping it out.
 *
 * Guards against ever running too often, independent of whatever
 * schedule calls it: at most once every 20 minutes, no matter the
 * trigger. This is a safety net, not the primary throttle -- the primary
 * throttle is the cron interval below.
 */
function globalfxhub_fetch_market_data( $force = false ) {
    if ( ! defined( 'GLOBALFXHUB_TWELVEDATA_API_KEY' ) || ! GLOBALFXHUB_TWELVEDATA_API_KEY ) {
        return array( 'ok' => false, 'reason' => 'GLOBALFXHUB_TWELVEDATA_API_KEY is not defined (or is empty) in wp-config.php.' );
    }

    $last_attempt = (int) get_option( 'globalfxhub_market_data_last_attempt', 0 );
    if ( ! $force && ( time() - $last_attempt ) < 20 * MINUTE_IN_SECONDS ) {
        return array( 'ok' => false, 'reason' => 'Skipped: fetched ' . ( time() - $last_attempt ) . 's ago, under the 20-minute minimum gap.' );
    }
    update_option( 'globalfxhub_market_data_last_attempt', time(), false );

    $symbols = array_keys( globalfxhub_market_symbols() );
    $url = add_query_arg(
        array(
            'symbol' => implode( ',', $symbols ),
            'apikey' => GLOBALFXHUB_TWELVEDATA_API_KEY,
        ),
        'https://api.twelvedata.com/quote'
    );

    $response = wp_remote_get( $url, array( 'timeout' => 15 ) );
    if ( is_wp_error( $response ) ) {
        $msg = 'GlobalFXHub market data fetch failed: ' . $response->get_error_message();
        error_log( $msg );
        return array( 'ok' => false, 'reason' => $msg );
    }
    $code = (int) wp_remote_retrieve_response_code( $response );
    $raw_body = wp_remote_retrieve_body( $response );
    if ( 200 !== $code ) {
        $msg = 'GlobalFXHub market data fetch failed: HTTP ' . $code . ' -- ' . substr( $raw_body, 0, 300 );
        error_log( $msg );
        return array( 'ok' => false, 'reason' => $msg, 'http_code' => $code, 'raw_body' => $raw_body );
    }

    $body = json_decode( $raw_body, true );
    if ( ! is_array( $body ) ) {
        return array( 'ok' => false, 'reason' => 'Response was not valid JSON.', 'raw_body' => $raw_body );
    }

    // A single-symbol request returns the quote object directly; a
    // multi-symbol request returns an object keyed by symbol. Normalize
    // to the latter shape either way.
    if ( isset( $body['symbol'] ) ) {
        $body = array( $body['symbol'] => $body );
    }

    $parsed = array();
    $skipped = array();
    foreach ( $symbols as $symbol ) {
        $quote = isset( $body[ $symbol ] ) ? $body[ $symbol ] : null;
        if ( ! is_array( $quote ) || isset( $quote['code'] ) || ! isset( $quote['close'] ) || ! is_numeric( $quote['close'] ) ) {
            $skipped[ $symbol ] = is_array( $quote ) ? $quote : 'not present in response';
            continue;
        }
        $parsed[ $symbol ] = array(
            'close'          => (float) $quote['close'],
            'percent_change' => isset( $quote['percent_change'] ) && is_numeric( $quote['percent_change'] ) ? (float) $quote['percent_change'] : null,
        );
    }

    if ( empty( $parsed ) ) {
        $msg = 'GlobalFXHub market data fetch: no symbols parsed from response -- check symbol names against a live Twelve Data /quote response.';
        error_log( $msg );
        return array( 'ok' => false, 'reason' => $msg, 'raw_body' => $raw_body, 'skipped' => $skipped );
    }

    $stored = get_option( GLOBALFXHUB_MARKET_DATA_OPTION, array() );
    $existing_quotes = ( is_array( $stored ) && ! empty( $stored['quotes'] ) ) ? $stored['quotes'] : array();
    $merged_quotes = array_merge( $existing_quotes, $parsed );

    update_option( GLOBALFXHUB_MARKET_DATA_OPTION, array(
        'quotes'       => $merged_quotes,
        'last_updated' => time(),
    ), false );

    return array( 'ok' => true, 'parsed' => $parsed, 'skipped' => $skipped );
}

/**
 * Refreshes at most once an hour: 8 symbols x 24 refreshes/day = 192
 * credits/day on Twelve Data's quote endpoint (1 credit per symbol,
 * whether requested individually or batched in one call) -- comfortably
 * inside the free plan's daily allowance. The 8-symbol cap itself is set
 * by the per-minute limit (see globalfxhub_market_symbols() above), not
 * this daily one. Tighten or loosen the refresh cadence by changing
 * HOUR_IN_SECONDS below; the 20-minute guard in the fetch function itself
 * stops any interval you pick here from firing faster than that.
 */
function globalfxhub_market_data_cron_schedules( $schedules ) {
    $schedules['globalfxhub_hourly'] = array(
        'interval' => HOUR_IN_SECONDS,
        'display'  => 'Every hour (GlobalFXHub market data)',
    );
    return $schedules;
}
add_filter( 'cron_schedules', 'globalfxhub_market_data_cron_schedules' );

function globalfxhub_schedule_market_data_cron() {
    if ( ! wp_next_scheduled( 'globalfxhub_fetch_market_data_event' ) ) {
        wp_schedule_event( time(), 'globalfxhub_hourly', 'globalfxhub_fetch_market_data_event' );
    }
}
add_action( 'wp', 'globalfxhub_schedule_market_data_cron' );
add_action( 'globalfxhub_fetch_market_data_event', 'globalfxhub_fetch_market_data' );

function globalfxhub_unschedule_market_data_cron() {
    wp_clear_scheduled_hook( 'globalfxhub_fetch_market_data_event' );
}
add_action( 'switch_theme', 'globalfxhub_unschedule_market_data_cron' );

/**
 * What templates actually read. Every symbol always has a value: real, if
 * we've ever fetched it successfully, otherwise the static illustrative
 * one -- so the ticker/heatmap/movers never show blanks, whether that's
 * because the API key isn't configured yet, a fetch failed, or the
 * scheduled refresh just hasn't run yet on a fresh install.
 */
function globalfxhub_get_market_snapshot() {
    $fallback = globalfxhub_market_fallback_data();
    $stored   = get_option( GLOBALFXHUB_MARKET_DATA_OPTION, array() );
    $quotes   = ( is_array( $stored ) && ! empty( $stored['quotes'] ) ) ? $stored['quotes'] : array();

    $out = array();
    foreach ( globalfxhub_market_symbols() as $symbol => $meta ) {
        $live = isset( $quotes[ $symbol ] ) ? $quotes[ $symbol ] : null;
        $data = $live ? $live : $fallback[ $symbol ];
        $out[ $symbol ] = array_merge( $meta, array(
            'symbol'         => $symbol,
            'close'          => $data['close'],
            'percent_change' => $data['percent_change'],
            'is_live'        => (bool) $live,
        ) );
    }

    return array(
        'symbols'      => $out,
        'last_updated' => ( is_array( $stored ) && ! empty( $stored['last_updated'] ) ) ? (int) $stored['last_updated'] : null,
        'is_live'      => ! empty( $quotes ),
    );
}

/**
 * Formatting helpers shared by the ticker, heatmap, and movers list.
 */
function globalfxhub_format_price( $value, array $meta ) {
    $formatted = number_format( (float) $value, $meta['decimals'] );
    return $formatted;
}

function globalfxhub_format_change( $percent_change ) {
    if ( null === $percent_change ) {
        return 'n/a';
    }
    $sign = $percent_change >= 0 ? '+' : "\u{2212}";
    return $sign . number_format( abs( $percent_change ), 2 ) . '%';
}

/**
 * Heatmap cell background: green for gains, red/brick for losses, a
 * darker shade the bigger the move (capped so an outsized move doesn't
 * produce an illegibly dark or oversaturated color).
 */
function globalfxhub_heat_color( $percent_change ) {
    if ( null === $percent_change ) {
        return '#5c6b80';
    }
    $magnitude = min( abs( $percent_change ), 2.0 ) / 2.0; // 0..1
    if ( $percent_change >= 0 ) {
        // light green (#3f8a70) -> dark green (#1f5a48)
        return $magnitude >= 0.5 ? '#1f5a48' : '#3f8a70';
    }
    // light brick (#c15a44) -> dark brick (#8a3f2e)
    return $magnitude >= 0.5 ? '#8a3f2e' : '#c15a44';
}
