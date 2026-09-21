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
 * Auto-create the "Reviews" page on the "Broker Reviews" template if it
 * doesn't exist yet, so /reviews/ and /reviews/{slug}/ work without a
 * manual wp-admin step. Cheap no-op (one lookup query) once the page
 * exists; flushes rewrite rules once, right after actually creating it.
 */
function globalfxhub_ensure_reviews_page() {
    if ( get_page_by_path( 'reviews' ) ) {
        return;
    }
    $page_id = wp_insert_post( array(
        'post_title'  => 'Reviews',
        'post_name'   => 'reviews',
        'post_status' => 'publish',
        'post_type'   => 'page',
    ) );
    if ( $page_id && ! is_wp_error( $page_id ) ) {
        update_post_meta( $page_id, '_wp_page_template', 'page-reviews.php' );
        flush_rewrite_rules();
    }
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_reviews_page' );

/**
 * Pretty URLs for individual broker review pages: /reviews/{slug}/
 * The "Reviews" page itself is auto-created above; this just adds the
 * rewrite rule for the /{slug}/ suffix and the "broker" query var it feeds.
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

/**
 * SEO for /reviews/{slug}/ pages.
 *
 * All 15 broker URLs share one underlying "Reviews" Page object (the
 * broker itself is read from the "broker" query var, not a separate
 * post), so without this WordPress's defaults would give every one of
 * them the same <title>, the same canonical link (pointing at /reviews/
 * instead of its own URL), no meta description, and no structured data --
 * any of which can keep Google from indexing them as distinct, rankable
 * pages. This gives each broker's URL its own title, canonical, meta
 * description, and Review schema (using only our own disclosed-methodology
 * score -- never the third-party ratings shown further down the page).
 */
function globalfxhub_review_seo_title( $title_parts ) {
    $slug = get_query_var( 'broker' );
    if ( $slug ) {
        $broker = globalfxhub_get_broker_by_slug( $slug );
        if ( $broker ) {
            $title_parts['title'] = $broker['name'] . ' Review ' . date( 'Y' ) . ': Regulation, Fees & Platforms';
        }
    } elseif ( is_page( 'reviews' ) ) {
        $title_parts['title'] = 'Forex Broker Reviews ' . date( 'Y' ) . ': All CySEC-Regulated Brokers Rated';
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_review_seo_title' );

function globalfxhub_review_canonical( $canonical_url ) {
    $slug = get_query_var( 'broker' );
    if ( $slug ) {
        $broker = globalfxhub_get_broker_by_slug( $slug );
        if ( $broker ) {
            return home_url( '/reviews/' . $broker['slug'] . '/' );
        }
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_review_canonical' );

function globalfxhub_review_seo_head() {
    $slug = get_query_var( 'broker' );
    if ( ! $slug ) {
        if ( is_page( 'reviews' ) ) {
            echo '<meta name="description" content="' . esc_attr( 'In-depth, individually scored reviews of every CySEC-regulated forex broker in our rankings -- regulation, cost, platforms, and track record, built from a disclosed methodology.' ) . '">' . "\n";
        }
        return;
    }

    $broker = globalfxhub_get_broker_by_slug( $slug );
    if ( ! $broker ) {
        return;
    }

    $reg_label = ( '—' === $broker['cysec'] ) ? 'EU-regulated' : ( 'CySEC No. ' . $broker['cysec'] );
    $description = sprintf(
        '%s review: %s, %s minimum deposit, %s pip average EUR/USD spread. Scored %s/5 overall on regulation, cost, platforms and track record -- see the full breakdown and what other reviewers say.',
        $broker['name'],
        $reg_label,
        $broker['min_deposit_display'],
        $broker['spread_eurusd'],
        $broker['scores']['overall']
    );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";

    $schema = array(
        '@context'     => 'https://schema.org',
        '@type'        => 'Review',
        'itemReviewed' => array(
            '@type' => 'FinancialService',
            'name'  => $broker['name'],
        ),
        'reviewRating' => array(
            '@type'       => 'Rating',
            'ratingValue' => (string) $broker['scores']['overall'],
            'bestRating'  => '5',
            'worstRating' => '1',
        ),
        'author'    => array( '@type' => 'Organization', 'name' => 'GlobalFXHub' ),
        'publisher' => array( '@type' => 'Organization', 'name' => 'GlobalFXHub' ),
        'url'       => home_url( '/reviews/' . $broker['slug'] . '/' ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode( $schema ) . '</script>' . "\n";
}
add_action( 'wp_head', 'globalfxhub_review_seo_head', 5 );

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

/**
 * What the biggest independent forex broker review sites currently publish
 * for each broker, keyed by slug, compiled from indexed/search-visible
 * content (this environment cannot directly load these sites to confirm
 * live page content, so treat figures as a snapshot to spot-check
 * periodically, not a guaranteed-current feed).
 *
 * Each entry: source, url, rating (null if no current published figure
 * could be found -- shown as a plain link instead of a score), scale
 * (5 for star ratings, 99 for ForexBrokers.com's Trust Score), and
 * optional label/note/exclude_from_average (Trust Score measures
 * regulatory/company trust, not overall quality, so it's kept out of
 * the star-rating average and labelled distinctly).
 *
 * Investopedia is omitted: this environment could not access
 * investopedia.com at all (blocked outright) and found no confirmable
 * current rating or even a working review URL for any of these brokers.
 */
function globalfxhub_get_external_reviews() {
    $fpa_base = 'https://www.forexpeacearmy.com/forex-reviews/';
    $bc_base  = 'https://brokerchooser.com/broker-reviews/';
    $fb_base  = 'https://www.forexbrokers.com/reviews/';
    $df_base  = 'https://www.dailyforex.com/forex-brokers/';

    $data = array(
        'ig' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'ig', 'rating' => 99, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'ig-review', 'rating' => 4.4, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '109/ig-forex-brokers', 'rating' => 1.77, 'scale' => 5, 'note' => 'Single unverified search snippet, not independently page-confirmed.' ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'ig-markets-review', 'rating' => null, 'scale' => 5 ),
        ),
        'forex-com' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'forex-com', 'rating' => 99, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'forex.com-review', 'rating' => 4.4, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '66/forex-com-reviews', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'forex-review', 'rating' => null, 'scale' => 5 ),
        ),
        'avatrade' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'avatrade', 'rating' => 96, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'avatrade-review', 'rating' => 4.2, 'scale' => 5, 'note' => 'Low-confidence figure -- may reflect a sub-score rather than the page\'s headline rating.' ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '539/avatrade-review', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'avatrade-review', 'rating' => null, 'scale' => 5 ),
        ),
        'fxcm' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'fxcm', 'rating' => 95, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'fxcm-review', 'rating' => 4.2, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '78/fxcm-forex-broker', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'fxcm-review', 'rating' => null, 'scale' => 5 ),
        ),
        'xtb' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'xtb', 'rating' => 96, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true, 'note' => 'Also carries a separately stated 5/5 "Overall" star rating on this page.' ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'xtb-review', 'rating' => 4.9, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '3597/xtb-review', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'xtb-review', 'rating' => null, 'scale' => 5 ),
        ),
        'capital-com' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'capital-com', 'rating' => 89, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'capitalcom-review', 'rating' => 4.8, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '14357/capital-com-review', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'capital-review', 'rating' => null, 'scale' => 5 ),
        ),
        'pepperstone' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'pepperstone', 'rating' => 94, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'pepperstone-review', 'rating' => 4.4, 'scale' => 5, 'note' => 'Low-confidence figure -- could not be cross-confirmed as the page\'s headline rating.' ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '7523/pepperstone-review', 'rating' => 3.48, 'scale' => 5, 'note' => 'Single unverified search snippet, not independently page-confirmed.' ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'pepperstone-review', 'rating' => null, 'scale' => 5 ),
        ),
        'eightcap' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'eightcap', 'rating' => 87, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'eightcap-review', 'rating' => 4.4, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '12091/eightcap-forex-brokers', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'eightcap-review', 'rating' => null, 'scale' => 5 ),
        ),
        'etoro' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'etoro', 'rating' => 97, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'etoro-review', 'rating' => 4.8, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '3731/etoro-review', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'etoro-review', 'rating' => null, 'scale' => 5, 'note' => 'A 4.5/5 figure appears in an old (2018) review and is not shown as current.' ),
        ),
        'fxpro' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'fxpro', 'rating' => 93, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'fxpro-review', 'rating' => 4.1, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '3947/fxpro-review', 'rating' => 2.5, 'scale' => 5, 'note' => 'Single unverified search snippet, not independently page-confirmed.' ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'fxpro-review', 'rating' => null, 'scale' => 5 ),
        ),
        'xm' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'xm', 'rating' => 93, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'xm-review', 'rating' => 4.3, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '7214/xm-review', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'xm-review', 'rating' => null, 'scale' => 5 ),
        ),
        'plus500' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'plus500', 'rating' => 99, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'plus500-review', 'rating' => 4.5, 'scale' => 5, 'note' => 'Rating shown is for the "Plus500 CFD" listing specifically; BrokerChooser reviews Plus500\'s CFD, Invest, and Futures products separately.' ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '5374/plus500-review', 'rating' => 1.5, 'scale' => 5, 'note' => 'Single unverified search snippet, not independently page-confirmed.' ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'plus500-review', 'rating' => null, 'scale' => 5 ),
        ),
        'ic-markets' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'ic-markets', 'rating' => 83, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'ic-markets-review', 'rating' => 4.4, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '8264/ic-markets-forex-brokers', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'ic-markets-review', 'rating' => null, 'scale' => 5 ),
        ),
        'trading-212' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'trading-212', 'rating' => 82, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'trading-212-review', 'rating' => 4.5, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '8416/trading212-review', 'rating' => 2.5, 'scale' => 5, 'note' => 'Single unverified search snippet, not independently page-confirmed.' ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'trading-212-review', 'rating' => null, 'scale' => 5 ),
        ),
        'tickmill' => array(
            array( 'source' => 'ForexBrokers.com', 'url' => $fb_base . 'tickmill', 'rating' => 85, 'scale' => 99, 'label' => 'Trust Score', 'exclude_from_average' => true ),
            array( 'source' => 'BrokerChooser', 'url' => $bc_base . 'tickmill-review', 'rating' => 4.4, 'scale' => 5 ),
            array( 'source' => 'ForexPeaceArmy', 'url' => $fpa_base . '8718/tickmill-forex-brokers', 'rating' => null, 'scale' => 5 ),
            array( 'source' => 'DailyForex', 'url' => $df_base . 'tickmill-review', 'rating' => null, 'scale' => 5 ),
        ),
    );

    return $data;
}

/**
 * Real ratings for one broker plus a simple average normalized to /5,
 * across sources with a confirmed rating on a comparable (non-Trust-Score)
 * scale. Returns null for average when no such rating is on file.
 */
function globalfxhub_broker_external_reviews( $slug ) {
    $all = globalfxhub_get_external_reviews();
    $reviews = isset( $all[ $slug ] ) ? $all[ $slug ] : array();

    $sum = 0;
    $count = 0;
    foreach ( $reviews as $r ) {
        if ( null === $r['rating'] || ! empty( $r['exclude_from_average'] ) ) {
            continue;
        }
        $sum += ( $r['rating'] / $r['scale'] ) * 5;
        $count++;
    }

    return array(
        'reviews' => $reviews,
        'average' => $count > 0 ? round( $sum / $count, 1 ) : null,
    );
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
