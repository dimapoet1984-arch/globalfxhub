<?php
/**
 * GlobalFXHub theme functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_setup() {
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'globalfxhub' ),
        'footer_company'  => __( 'Footer — Company', 'globalfxhub' ),
        'footer_research' => __( 'Footer — Research', 'globalfxhub' ),
        'footer_legal'    => __( 'Footer — Legal', 'globalfxhub' ),
    ) );
}
add_action( 'after_setup_theme', 'globalfxhub_setup' );

function globalfxhub_scripts() {
    wp_enqueue_style(
        'globalfxhub-fonts',
        'https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,500&family=IBM+Plex+Sans:wght@400;500;600&family=IBM+Plex+Mono:wght@400;500&display=swap',
        array(),
        null
    );
    wp_enqueue_style(
        'globalfxhub-style',
        get_stylesheet_uri(),
        array( 'globalfxhub-fonts' ),
        wp_get_theme()->get( 'Version' )
    );
}
add_action( 'wp_enqueue_scripts', 'globalfxhub_scripts' );

/**
 * Fallback menu output if no menu is assigned yet in
 * Appearance > Menus, so the nav never renders empty.
 */
function globalfxhub_fallback_menu() {
    $blog_url = get_permalink( get_option( 'page_for_posts' ) );
    if ( ! $blog_url ) {
        $blog_url = home_url( '/blog/' );
    }
    echo '<ul class="nav__links" id="navLinks">';
    echo '<li><a href="' . esc_url( home_url( '/reviews/' ) ) . '">Reviews</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/guides/' ) ) . '">Guides</a></li>';
    echo '<li><a href="' . esc_url( $blog_url ) . '">Blog</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/compare/' ) ) . '">Compare</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/countries/' ) ) . '">Countries</a></li>';
    echo '</ul>';
}

/**
 * Rough reading-time estimate (whole minutes, minimum 1) at ~200 wpm.
 */
function globalfxhub_reading_time( $content ) {
    $word_count = str_word_count( wp_strip_all_tags( $content ) );
    $minutes = (int) ceil( $word_count / 200 );
    return max( 1, $minutes );
}

/**
 * Simple excerpt length/trim helpers for the blog index
 */
function globalfxhub_trim_excerpt( $text, $length = 20 ) {
    $text = wp_strip_all_tags( $text );
    $words = preg_split( '/\s+/', $text );
    if ( count( $words ) > $length ) {
        $words = array_slice( $words, 0, $length );
        return implode( ' ', $words ) . '…';
    }
    return $text;
}
/**
 * Pretty URLs for individual broker review pages: /reviews/{slug}/
 * Requires a Page with slug "reviews" using the "Broker Reviews" template
 * (Appearance/Pages: create "Reviews" page, assign that template, then
 * resave Settings > Permalinks once so this rule takes effect).
 */
function globalfxhub_review_rewrite_rules() {
    add_rewrite_rule( '^reviews/([^/]+)/?$', 'index.php?pagename=reviews&broker=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_review_rewrite_rules' );

function globalfxhub_review_query_vars( $vars ) {
    $vars[] = 'broker';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_review_query_vars' );

add_action( 'after_switch_theme', 'flush_rewrite_rules' );

function globalfxhub_get_broker_by_slug( $slug ) {
    foreach ( globalfxhub_get_brokers() as $broker ) {
        if ( $broker['slug'] === $slug ) {
            return $broker;
        }
    }
    return null;
}

/**
 * Pros/cons derived from the same scored data used in the rankings and
 * compare tool, so a review page never asserts anything they don't.
 */
function globalfxhub_broker_pros_cons( $broker ) {
    $pros = array();
    $cons = array();
    $has_mt = in_array( 'MT4', $broker['platforms'], true ) || in_array( 'MT5', $broker['platforms'], true );

    if ( $broker['scores']['regulation'] >= 4.0 ) {
        $pros[] = 'Wide regulatory footprint -- CySEC plus ' . $broker['other_reg_count'] . ' other regulator(s), including ' . implode( ', ', array_slice( $broker['other_reg'], 0, 2 ) );
    } else {
        $cons[] = 'Thinner regulatory footprint than most peers on this list';
    }

    if ( $broker['scores']['cost'] >= 4.0 ) {
        $pros[] = 'Low cost to start: ' . $broker['min_deposit_display'] . ' minimum deposit, ' . $broker['spread_eurusd'] . ' pip average EUR/USD spread';
    } elseif ( $broker['scores']['cost'] <= 2.5 ) {
        $cons[] = 'Higher cost than most peers on this list (spread and/or minimum deposit)';
    }

    if ( count( $broker['platforms'] ) >= 3 ) {
        $pros[] = 'Broad platform choice: ' . implode( ', ', $broker['platforms'] );
    } elseif ( ! $has_mt ) {
        $cons[] = 'No MetaTrader (MT4/MT5) support -- proprietary platform only';
    }

    if ( $broker['scores']['track_record'] >= 4.0 ) {
        $pros[] = 'Long operating history, trading since ' . $broker['founded'];
    } elseif ( $broker['scores']['track_record'] <= 2.0 ) {
        $cons[] = 'Shorter operating history than most peers, trading since ' . $broker['founded'];
    }

    if ( empty( $cons ) ) {
        $cons[] = 'No standout weaknesses versus peers in our researched criteria -- still confirm current terms directly with the broker.';
    }

    return array( 'pros' => $pros, 'cons' => $cons );
}

function globalfxhub_get_brokers() {
    return array(
        array( 'slug' => 'ig', 'name' => 'IG', 'entity' => 'IGM Forex Ltd', 'cysec' => '309/16', 'founded' => 1974, 'hq' => 'London, UK', 'min_deposit_usd' => 1, 'min_deposit_display' => '£1', 'spread_eurusd' => 0.6, 'platforms' => array('Proprietary', 'MT4', 'ProRealTime'), 'other_reg' => array('FCA', 'ASIC', '+9 more Tier-1'), 'other_reg_count' => 10, 'instruments' => '17,000+ across forex, indices, shares, commodities, crypto CFDs', 'blurb' => 'Long-established, publicly listed (LSE: IGG), one of the widest regulatory footprints of any broker on this list.', 'scores' => array('regulation' => 5.0, 'cost' => 4.86, 'platforms' => 3.86, 'track_record' => 5.0, 'overall' => 4.7), 'rank' => 1 ),
        array( 'slug' => 'forex-com', 'name' => 'FOREX.com', 'entity' => 'StoneX Europe Ltd', 'cysec' => '400/21', 'founded' => 1999, 'hq' => 'New Jersey, USA (EU ops via Cyprus)', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.0, 'platforms' => array('Proprietary', 'MT4', 'MT5', 'TradingView'), 'other_reg' => array('NFA/CFTC (US)', 'FCA', 'ASIC'), 'other_reg_count' => 4, 'instruments' => '80+ FX pairs plus indices, commodities, shares CFDs', 'blurb' => 'Backed by NASDAQ-listed StoneX Group; strong educational content and platform variety.', 'scores' => array('regulation' => 4.43, 'cost' => 3.0, 'platforms' => 4.71, 'track_record' => 4.71, 'overall' => 4.1), 'rank' => 2 ),
        array( 'slug' => 'avatrade', 'name' => 'AvaTrade', 'entity' => 'Ava Trade EU Ltd', 'cysec' => '—', 'founded' => 2006, 'hq' => 'Dublin, Ireland', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 0.93, 'platforms' => array('Proprietary (AvaTradeGO)', 'MT4', 'MT5', 'DupliTrade', 'ZuluTrade'), 'other_reg' => array('ASIC', 'JFSA (Japan)', 'CIRO (Canada)', 'Central Bank of Ireland'), 'other_reg_count' => 4, 'instruments' => 'CFDs across forex, stocks, indices, commodities, ETFs, bonds, crypto', 'blurb' => 'EU clients typically served under Central Bank of Ireland / MiFID passporting rather than a direct CySEC CIF licence -- worth confirming which entity applies to you.', 'cysec_note' => 'Regulated in the EU via MiFID passporting; confirm current entity with AvaTrade directly.', 'scores' => array('regulation' => 4.43, 'cost' => 3.15, 'platforms' => 5.0, 'track_record' => 3.57, 'overall' => 4.0), 'rank' => 3 ),
        array( 'slug' => 'fxcm', 'name' => 'FXCM', 'entity' => 'FXCM EU Ltd', 'cysec' => '392/20', 'founded' => 1999, 'hq' => 'London, UK', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 1.3, 'platforms' => array('Trading Station (proprietary)', 'MT4', 'ZuluTrade', 'NinjaTrader'), 'other_reg' => array('FCA', 'ASIC', 'FSCA'), 'other_reg_count' => 3, 'instruments' => 'Forex, indices, commodities, crypto CFDs', 'blurb' => 'One of the longer-running retail forex names, with unusually wide platform choice including NinjaTrader for futures-style traders.', 'scores' => array('regulation' => 3.86, 'cost' => 2.71, 'platforms' => 4.71, 'track_record' => 4.71, 'overall' => 3.9), 'rank' => 4 ),
        array( 'slug' => 'xtb', 'name' => 'XTB', 'entity' => 'XTB Limited', 'cysec' => '169/12', 'founded' => 2002, 'hq' => 'Warsaw, Poland', 'min_deposit_usd' => 0, 'min_deposit_display' => '$0', 'spread_eurusd' => 0.7, 'platforms' => array('xStation 5 (proprietary)'), 'other_reg' => array('FCA', 'KNF (Poland)'), 'other_reg_count' => 3, 'instruments' => '11,300+ across forex, indices, commodities, stocks, ETFs, crypto CFDs', 'blurb' => 'Publicly listed on the Warsaw Stock Exchange. No minimum deposit, no MT4/MT5 -- everything runs on its own xStation 5 platform.', 'scores' => array('regulation' => 3.86, 'cost' => 4.71, 'platforms' => 1.86, 'track_record' => 4.14, 'overall' => 3.8), 'rank' => 5 ),
        array( 'slug' => 'capital-com', 'name' => 'Capital.com', 'entity' => 'Capital Com SV Investments Ltd', 'cysec' => '319/17', 'founded' => 2016, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 20, 'min_deposit_display' => '$20', 'spread_eurusd' => 0.6, 'platforms' => array('Proprietary', 'MT4', 'TradingView'), 'other_reg' => array('FCA', 'ASIC', 'FSCA'), 'other_reg_count' => 3, 'instruments' => 'Forex, indices, commodities, shares, crypto CFDs', 'blurb' => 'Newer than most on this list but grew fast; low minimum deposit and commission-free pricing on most instruments.', 'scores' => array('regulation' => 3.86, 'cost' => 4.29, 'platforms' => 3.86, 'track_record' => 1.0, 'overall' => 3.4), 'rank' => 6 ),
        array( 'slug' => 'pepperstone', 'name' => 'Pepperstone', 'entity' => 'Pepperstone EU Limited', 'cysec' => 'licensed 2020', 'founded' => 2010, 'hq' => 'Melbourne, Australia', 'min_deposit_usd' => 10, 'min_deposit_display' => '$10', 'spread_eurusd' => 1.1, 'platforms' => array('MT4', 'MT5', 'cTrader', 'TradingView'), 'other_reg' => array('ASIC', 'FCA', 'DFSA'), 'other_reg_count' => 3, 'instruments' => '1,300+ instruments across forex, indices, commodities, shares', 'blurb' => 'Added its CySEC licence in 2020 ahead of Brexit to secure EU client access; strong reputation among active/algo traders for platform choice.', 'scores' => array('regulation' => 3.86, 'cost' => 3.29, 'platforms' => 4.71, 'track_record' => 1.57, 'overall' => 3.4), 'rank' => 7 ),
        array( 'slug' => 'eightcap', 'name' => 'Eightcap', 'entity' => 'Eightcap EU Ltd', 'cysec' => '246/14', 'founded' => 2009, 'hq' => 'Melbourne, Australia', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.0, 'platforms' => array('MT4', 'MT5', 'TradingView'), 'other_reg' => array('ASIC', 'FCA', 'SCB'), 'other_reg_count' => 3, 'instruments' => '800+ across forex, indices, commodities, shares, crypto CFDs', 'blurb' => 'Newer to CySEC regulation than most peers here, with strong TradingView integration.', 'scores' => array('regulation' => 3.86, 'cost' => 3.0, 'platforms' => 3.86, 'track_record' => 2.14, 'overall' => 3.3), 'rank' => 8 ),
        array( 'slug' => 'etoro', 'name' => 'eToro', 'entity' => 'eToro (Europe) Ltd', 'cysec' => '109/10', 'founded' => 2007, 'hq' => 'Tel Aviv / Limassol', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 1.0, 'platforms' => array('Proprietary (CopyTrader)'), 'other_reg' => array('FCA', 'ASIC', 'FSAS'), 'other_reg_count' => 3, 'instruments' => '5,000+ across forex, stocks, ETFs, commodities, indices, 100+ crypto', 'blurb' => 'Best known for social/copy trading. No MT4/MT5 -- everything happens on eToro\'s own platform.', 'scores' => array('regulation' => 3.86, 'cost' => 3.43, 'platforms' => 1.86, 'track_record' => 3.0, 'overall' => 3.2), 'rank' => 9 ),
        array( 'slug' => 'fxpro', 'name' => 'FxPro', 'entity' => 'FxPro Financial Services Limited', 'cysec' => '078/07', 'founded' => 2006, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.44, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array('FCA', 'FSCA', 'SCB'), 'other_reg_count' => 3, 'instruments' => 'Forex, shares, indices, commodities, metals, futures CFDs', 'blurb' => 'One of the oldest CySEC licences on this list (issued 2007), reflecting a long operating history in Cyprus specifically.', 'scores' => array('regulation' => 3.86, 'cost' => 2.0, 'platforms' => 3.86, 'track_record' => 3.57, 'overall' => 3.2), 'rank' => 10 ),
        array( 'slug' => 'xm', 'name' => 'XM', 'entity' => 'Trading Point of Financial Instruments Ltd', 'cysec' => '120/10', 'founded' => 2009, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 5, 'min_deposit_display' => '$5', 'spread_eurusd' => 1.1, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('ASIC', 'FCA', 'DFSA'), 'other_reg_count' => 3, 'instruments' => '1,000+ across forex, stocks, indices, commodities, metals, crypto', 'blurb' => 'One of the lowest minimum deposits on this list; large global client base and a long-running CySEC licence.', 'scores' => array('regulation' => 3.86, 'cost' => 3.42, 'platforms' => 2.43, 'track_record' => 2.14, 'overall' => 3.1), 'rank' => 11 ),
        array( 'slug' => 'plus500', 'name' => 'Plus500', 'entity' => 'Plus500CY Ltd', 'cysec' => '250/14', 'founded' => 2008, 'hq' => 'Haifa, Israel (EU ops via Cyprus)', 'min_deposit_usd' => 54, 'min_deposit_display' => '€50', 'spread_eurusd' => 1.3, 'platforms' => array('WebTrader (proprietary)'), 'other_reg' => array('FCA', 'ASIC', 'FSCA', 'MAS', 'FMA'), 'other_reg_count' => 5, 'instruments' => '2,800+ CFDs across forex, indices, commodities, shares, ETFs, crypto', 'blurb' => 'Publicly listed (LSE: PLUS), one of the broadest regulatory footprints here, but no MT4/MT5 support.', 'scores' => array('regulation' => 4.71, 'cost' => 2.42, 'platforms' => 1.86, 'track_record' => 2.43, 'overall' => 3.0), 'rank' => 12 ),
        array( 'slug' => 'ic-markets', 'name' => 'IC Markets', 'entity' => 'IC Markets (EU) Ltd', 'cysec' => '362/18', 'founded' => 2007, 'hq' => 'Sydney, Australia', 'min_deposit_usd' => 200, 'min_deposit_display' => '$200', 'spread_eurusd' => 0.8, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array('ASIC'), 'other_reg_count' => 1, 'instruments' => 'Forex, metals, stocks, commodities, futures, crypto, indices', 'blurb' => 'Known for raw ECN-style pricing aimed at active and algorithmic traders; higher minimum deposit than most on this list.', 'scores' => array('regulation' => 1.29, 'cost' => 2.57, 'platforms' => 3.86, 'track_record' => 3.0, 'overall' => 2.5), 'rank' => 13 ),
        array( 'slug' => 'trading-212', 'name' => 'Trading 212', 'entity' => 'Trading 212 UK Ltd (EU clients via MiFID passporting)', 'cysec' => '—', 'founded' => 2004, 'hq' => 'London, UK', 'min_deposit_usd' => 1.1, 'min_deposit_display' => '€1', 'spread_eurusd' => 2.7, 'platforms' => array('Proprietary'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => 'Forex, stocks, ETFs, commodities, indices, crypto CFDs', 'blurb' => 'Very low minimum deposit and a well-regarded no-frills app, but wider EUR/USD spreads and only one platform option.', 'cysec_note' => 'Serves EU/Cypriot clients via FCA authorisation and MiFID passporting rather than a separate CySEC CIF.', 'scores' => array('regulation' => 1.29, 'cost' => 2.71, 'platforms' => 1.86, 'track_record' => 3.86, 'overall' => 2.3), 'rank' => 14 ),
        array( 'slug' => 'tickmill', 'name' => 'Tickmill', 'entity' => 'Tickmill Europe Ltd', 'cysec' => '278/15', 'founded' => 2014, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.7, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'FSCA'), 'other_reg_count' => 2, 'instruments' => 'Forex, indices, commodities, bonds CFDs', 'blurb' => 'No account maintenance or inactivity fees on CySEC-regulated accounts; wider standard spreads than ECN-focused peers.', 'scores' => array('regulation' => 1.57, 'cost' => 1.86, 'platforms' => 2.43, 'track_record' => 1.29, 'overall' => 1.8), 'rank' => 15 ),
    );
}
