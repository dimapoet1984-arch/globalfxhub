<?php
/**
 * Broker Finder (/broker-finder/): a faceted filter over the exact same
 * 139-broker dataset and the exact same disclosed nine-category scores
 * used everywhere else on the site -- never a separate, looser dataset
 * just because it's framed as a "tool".
 *
 * Two kinds of filters are used here:
 *  - Facts already on file (min deposit, platforms, EU/CySEC or Tier-1
 *    regulation, country eligibility) -- reused as-is, or turned into a
 *    small set of disclosed keyword/structural rules (instrument tags,
 *    platform tags) when the underlying field is free text rather than
 *    already structured. No new broker fact is invented to make a facet
 *    work; where a fact isn't confirmed, the broker is excluded from a
 *    filter that requires it rather than guessed into matching.
 *  - "Trading style" doesn't add a new fact about a broker at all -- it
 *    re-weights the same nine already-disclosed score categories
 *    (globalfxhub_broker_scores()) with a disclosed, mechanical rule
 *    (favored categories x1.6, de-emphasized categories x0.5, weights
 *    renormalized back to 100%), the same way "beginner" or "low-cost"
 *    already do as fixed filters on /best/.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Ordered so forex (the thing every broker on this site offers in some
 * form) leads the checkbox list. Keys double as regex-match categories
 * below and as JS-side object keys, so keep them stable.
 */
function globalfxhub_finder_instrument_categories() {
    return array(
        'forex'       => 'Forex',
        'indices'     => 'Indices',
        'shares'      => 'Shares / stocks',
        'commodities' => 'Commodities (incl. metals, energies)',
        'crypto'      => 'Crypto',
        'bonds'       => 'Bonds',
        'etfs'        => 'ETFs',
        'futures'     => 'Futures',
    );
}

/**
 * The 'instruments' field is free text written per-broker during
 * research (e.g. "17,000+ across forex, indices, shares, commodities,
 * crypto CFDs"), not a structured list -- so this matches the same
 * vocabulary that text was actually written in. A broker whose text
 * doesn't mention a category is treated as not offering it; this can
 * under-count in principle (if a broker offers something our research
 * notes simply didn't phrase in these terms) but never over-claims.
 */
function globalfxhub_broker_instrument_tags( $broker ) {
    $text = (string) ( $broker['instruments'] ?? '' );
    $patterns = array(
        'forex'       => '/\bforex\b|\bfx\s+pairs?\b|\bcurrency\s+pairs?\b/i',
        'indices'     => '/\bindices\b/i',
        'shares'      => '/\bshares?\b|\bstocks?\b/i',
        'commodities' => '/\bcommodit(?:y|ies)\b|\bmetals\b|\benergies\b/i',
        'crypto'      => '/\bcrypto/i',
        'bonds'       => '/\bbonds\b/i',
        'etfs'        => '/\bETFs?\b/i',
        'futures'     => '/\bfutures\b/i',
    );
    $tags = array();
    foreach ( $patterns as $key => $pattern ) {
        if ( preg_match( $pattern, $text ) ) {
            $tags[] = $key;
        }
    }
    return $tags;
}

/**
 * MT4/MT5/cTrader/TradingView are named consistently across the
 * 'platforms' arrays, so those four are matched exactly; every other
 * value (the various in-house "Proprietary (...)" apps, plus a handful
 * of named third-party ones like TWS or SaxoTraderPRO) collapses into
 * one honest "proprietary / other" bucket rather than a long tail of
 * one-broker options nobody would filter on.
 */
function globalfxhub_finder_platform_categories() {
    return array(
        'MT4'         => 'MetaTrader 4',
        'MT5'         => 'MetaTrader 5',
        'cTrader'     => 'cTrader',
        'TradingView' => 'TradingView',
        'other'       => 'Proprietary / other platform',
    );
}

function globalfxhub_broker_platform_tags( $broker ) {
    $standard = array( 'MT4', 'MT5', 'cTrader', 'TradingView' );
    $tags = array();
    foreach ( (array) ( $broker['platforms'] ?? array() ) as $platform ) {
        if ( in_array( $platform, $standard, true ) ) {
            $tags[] = $platform;
        } else {
            $tags[] = 'other';
        }
    }
    return array_values( array_unique( $tags ) );
}

/**
 * Deposit bands match the actual minimums seen in the dataset ($1 up to
 * several hundred); a broker with no confirmed min_deposit_usd is
 * excluded once any cap is chosen, same convention as /best/low-cost
 * only ranking brokers with a confirmed spread -- "not confirmed" never
 * silently counts as "meets the criteria".
 */
function globalfxhub_finder_deposit_bands() {
    return array(
        ''     => 'Any amount',
        '100'  => '$100 or less',
        '250'  => '$250 or less',
        '500'  => '$500 or less',
        '1000' => '$1,000 or less',
    );
}

/**
 * Same two predicates already used for the /best/cysec-regulated and
 * /best/fca-regulated lists in inc/best.php -- reused here rather than
 * re-derived, so "regulation preference" never disagrees with what
 * those pages already say about the same broker.
 */
function globalfxhub_finder_regulation_options() {
    return array(
        ''      => array(
            'label' => 'Any, including offshore-only',
            'note'  => 'No regulation filter applied.',
        ),
        'eu'    => array(
            'label' => 'At least CySEC / EU-passported',
            'note'  => 'Holds a direct CySEC licence or EU/MiFID-passported regulation -- see our ' . '<a href="' . home_url( '/regulation/cysec/' ) . '">CySEC regulation page</a> for what that protection actually covers.',
        ),
        'tier1' => array(
            'label' => 'Tier-1 regulator held directly (FCA, ASIC, etc.)',
            'note'  => 'Holds a direct UK FCA licence, or another Tier-1 regulator (e.g. ASIC) is listed among its confirmed licences.',
        ),
    );
}

function globalfxhub_broker_meets_regulation_option( $broker, $option ) {
    if ( '' === $option ) {
        return true;
    }
    if ( 'eu' === $option ) {
        return null !== $broker['cysec'];
    }
    if ( 'tier1' === $option ) {
        return ! empty( $broker['fca'] ) || ! empty( $broker['fca_note'] ) || (int) $broker['other_reg_count'] > 0;
    }
    return true;
}

/**
 * Trading style never adds a new broker fact -- it re-weights the same
 * nine disclosed score categories from globalfxhub_broker_scores().
 * 'boost' categories are weighted x1.6, 'reduce' categories x0.5, every
 * other category keeps its normal weight, then all nine are renormalized
 * back to sum to 100% (see globalfxhub_finder_style_weights()). The
 * reasoning for each style's choices is in its own 'note', shown next to
 * the picker so this isn't a hidden scoring change.
 */
function globalfxhub_finder_trading_styles() {
    return array(
        'any'               => array(
            'label' => 'No particular style / not sure',
            'note'  => 'Uses our standard overall-score weighting (regulation 30%, cost 20%, the rest 50% across six more categories) -- the same ranking used sitewide.',
            'boost' => array(),
            'reduce'=> array(),
        ),
        'beginner'          => array(
            'label' => 'New to trading',
            'note'  => 'Weighted more toward platforms & tools, non-trading fees, and transparency (clarity matters most starting out); weighted less toward execution sophistication and product-range breadth, which matter more once a strategy is established.',
            'boost' => array( 'platforms', 'non_trading_fees', 'transparency' ),
            'reduce'=> array( 'execution', 'product_range' ),
        ),
        'active-trading'    => array(
            'label' => 'Active / frequent trading (day trading, scalping)',
            'note'  => 'Weighted more toward trading costs and execution quality, since both compound with trade frequency; weighted less toward track record and deposit/withdrawal convenience.',
            'boost' => array( 'cost', 'execution' ),
            'reduce'=> array( 'track_record', 'deposits_withdrawals' ),
        ),
        'long-term-investor'=> array(
            'label' => 'Long-term / occasional trading',
            'note'  => 'Weighted more toward regulation & client protection, track record, and product range, since safety and breadth matter more than shaving fractions of a pip off an occasional trade; weighted less toward execution speed and cost.',
            'boost' => array( 'regulation', 'track_record', 'product_range' ),
            'reduce'=> array( 'execution', 'cost' ),
        ),
    );
}

/**
 * The base weights here are a literal copy of globalfxhub_broker_scores()'s
 * $weights array -- kept in sync manually since PHP has no clean way to
 * import a local variable from inside another function; test-broker-
 * finder.php asserts the two stay identical.
 */
function globalfxhub_finder_style_weights( $style_key ) {
    $base = array(
        'regulation'           => 0.30,
        'cost'                 => 0.20,
        'non_trading_fees'     => 0.10,
        'platforms'            => 0.10,
        'execution'            => 0.10,
        'product_range'        => 0.05,
        'deposits_withdrawals' => 0.05,
        'transparency'         => 0.05,
        'track_record'         => 0.05,
    );

    $styles = globalfxhub_finder_trading_styles();
    if ( ! isset( $styles[ $style_key ] ) ) {
        return $base;
    }
    $style = $styles[ $style_key ];

    $weights = $base;
    foreach ( $style['boost'] as $cat ) {
        if ( isset( $weights[ $cat ] ) ) {
            $weights[ $cat ] *= 1.6;
        }
    }
    foreach ( $style['reduce'] as $cat ) {
        if ( isset( $weights[ $cat ] ) ) {
            $weights[ $cat ] *= 0.5;
        }
    }
    $total = array_sum( $weights );
    if ( $total > 0 ) {
        foreach ( $weights as $cat => $w ) {
            $weights[ $cat ] = $w / $total;
        }
    }
    return $weights;
}

/**
 * Same null-safe weighted-average pattern as the main overall score in
 * globalfxhub_broker_scores(): a category with no confirmed value is
 * dropped from both the numerator and the weight total, rather than
 * treated as a zero.
 */
function globalfxhub_finder_style_score( $broker, $style_key ) {
    $weights = globalfxhub_finder_style_weights( $style_key );
    $scores = $broker['scores'] ?? array();

    $weight_sum = 0.0;
    $score_sum  = 0.0;
    foreach ( $weights as $cat => $w ) {
        if ( isset( $scores[ $cat ] ) && null !== $scores[ $cat ] ) {
            $score_sum  += $scores[ $cat ] * $w;
            $weight_sum += $w;
        }
    }
    return $weight_sum > 0 ? round( $score_sum / $weight_sum, 2 ) : null;
}

/**
 * The 27 EU countries already modeled in inc/countries.php, plus one
 * explicit "outside the EU" option -- the Finder deliberately doesn't
 * pretend to cover client availability anywhere else, since that's the
 * one region this site has actually built out regulatory/passporting
 * detail for.
 */
function globalfxhub_finder_country_options() {
    $countries = globalfxhub_get_countries();
    $options = array();
    foreach ( $countries as $slug => $country ) {
        $options[ $slug ] = globalfxhub_country_flag_emoji( $country['iso'] ) . ' ' . $country['name'];
    }
    return $options;
}

/**
 * A broker only counts as available in an EU country once it holds a
 * CySEC licence or EU/MiFID-passported regulation -- the same predicate
 * that already excludes offshore-only brokers from every /countries/
 * page (see test-offshore-leak.php). Picking "outside the EU" applies no
 * country filter at all, since availability there isn't modeled on this
 * site.
 */
function globalfxhub_broker_meets_country( $broker, $country_slug ) {
    if ( '' === $country_slug ) {
        return true;
    }
    return null !== $broker['cysec'];
}

/**
 * The lean, JSON-ready payload the Finder's client-side filtering runs
 * against -- precomputes every derived tag/score server-side (in PHP,
 * where it's covered by the same test suite as the rest of the scoring
 * logic) rather than duplicating that logic in JavaScript.
 */
function globalfxhub_finder_broker_payload() {
    $brokers = globalfxhub_get_brokers();
    $styles = array_keys( globalfxhub_finder_trading_styles() );

    return array_map( function( $b ) use ( $styles ) {
        $style_scores = array();
        foreach ( $styles as $style_key ) {
            $style_scores[ $style_key ] = globalfxhub_finder_style_score( $b, $style_key );
        }
        return array(
            'slug'              => $b['slug'],
            'name'              => $b['name'],
            'rank'              => $b['rank'],
            'min_deposit_usd'   => $b['min_deposit_usd'],
            'min_deposit_display'=> $b['min_deposit_display'],
            'platforms'         => $b['platforms'],
            'platform_tags'     => globalfxhub_broker_platform_tags( $b ),
            'instrument_tags'   => globalfxhub_broker_instrument_tags( $b ),
            'instruments_text'  => $b['instruments'],
            'regulation_label'  => globalfxhub_broker_regulation_label( $b ),
            'eu_eligible'       => null !== $b['cysec'],
            'meets_tier1'       => globalfxhub_broker_meets_regulation_option( $b, 'tier1' ),
            'overall'           => $b['scores']['overall'] ?? null,
            'style_scores'      => $style_scores,
            'review_url'        => home_url( '/reviews/' . $b['slug'] . '/' ),
        );
    }, $brokers );
}

function globalfxhub_ensure_broker_finder_page() {
    globalfxhub_ensure_templated_page( 'broker-finder', 'Broker Finder', 'page-broker-finder.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_broker_finder_page' );
