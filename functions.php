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
 * Create a page at the given slug if it doesn't exist, and make sure it's
 * assigned the given template either way -- so this self-heals a page
 * that was created manually (e.g. via wp-admin) without its template set,
 * not just a missing page. Cheap no-op (one lookup, one meta read) once
 * everything's already correct; flushes rewrite rules only when it
 * actually changes something.
 */
function globalfxhub_ensure_templated_page( $slug, $title, $template ) {
    $page = get_page_by_path( $slug );
    if ( ! $page ) {
        $page_id = wp_insert_post( array(
            'post_title'  => $title,
            'post_name'   => $slug,
            'post_status' => 'publish',
            'post_type'   => 'page',
        ) );
        if ( ! $page_id || is_wp_error( $page_id ) ) {
            return;
        }
    } else {
        $page_id = $page->ID;
    }

    if ( $template !== get_post_meta( $page_id, '_wp_page_template', true ) ) {
        update_post_meta( $page_id, '_wp_page_template', $template );
        flush_rewrite_rules();
    }
}

/**
 * /reviews/ and /reviews/{slug}/ need a "Reviews" page on the
 * "Broker Reviews" template; /guides/ needs a "Guides" page on the
 * "Guides Index" template. Both self-create/self-repair on every load
 * via globalfxhub_ensure_templated_page() above.
 */
function globalfxhub_ensure_reviews_page() {
    globalfxhub_ensure_templated_page( 'reviews', 'Reviews', 'page-reviews.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_reviews_page' );

function globalfxhub_ensure_guides_page() {
    globalfxhub_ensure_templated_page( 'guides', 'Guides', 'page-guides.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_guides_page' );

/**
 * Seed the "Guides" category and its starter articles as real posts (so
 * they get the normal post editor, categories, and single.php rendering)
 * if they don't already exist. Each check is a cheap single lookup once
 * seeded, so this is safe to leave running on every load.
 */
function globalfxhub_ensure_guides_content() {
    $term = term_exists( 'Guides', 'category' );
    if ( ! $term ) {
        $term = wp_insert_term( 'Guides', 'category', array( 'slug' => 'guides' ) );
    }
    if ( is_wp_error( $term ) || empty( $term['term_id'] ) ) {
        return;
    }
    $category_id = (int) $term['term_id'];

    $guides = array(
        array(
            'slug'    => 'how-to-read-candlestick-patterns',
            'title'   => 'How to Read Candlestick Patterns',
            'excerpt' => 'What a candle actually shows, and the handful of patterns worth learning first -- doji, hammer, engulfing, and the morning/evening star.',
            'content' => "<p>A candlestick chart shows four prices for every time period you're looking at: the open, high, low, and close. That's it -- but arranged the right way, those four numbers tell you a lot more than a simple line chart ever could.</p>\n\n<h2>The anatomy of a candle</h2>\n<p>Each candle has a <strong>body</strong> and, usually, two <strong>wicks</strong> (also called shadows). The body is the range between the open and close price. The wicks show the highest and lowest prices reached during that period, even if price didn't stay there.</p>\n<p>Color tells you direction: a candle where price closed <em>higher</em> than it opened is usually shown in green or white (\"bullish\"). One that closed <em>lower</em> than it opened is usually red or black (\"bearish\"). A long body means strong, decisive movement in one direction. A tiny body with long wicks means the price moved a lot during the period but ended up close to where it started -- a tug of war.</p>\n\n<h2>Single-candle patterns worth knowing</h2>\n<ul>\n<li><strong>Doji</strong> -- open and close are almost identical, so the body is a thin line. Signals indecision: neither buyers nor sellers won that round.</li>\n<li><strong>Hammer</strong> -- a small body near the top of the candle's range with a long lower wick, appearing after a downtrend. Suggests sellers pushed price down but buyers stepped in hard before the close.</li>\n<li><strong>Shooting star</strong> -- the mirror image of a hammer: a small body near the bottom with a long upper wick, appearing after an uptrend. Suggests buyers pushed higher but lost control before the close.</li>\n<li><strong>Marubozu</strong> -- a candle with little or no wick at all, just a long body. Shows one side was in full control the entire period.</li>\n</ul>\n\n<h2>Multi-candle patterns</h2>\n<ul>\n<li><strong>Bullish engulfing</strong> -- a small bearish candle followed by a larger bullish candle whose body completely covers the first. Often read as a potential reversal after a downtrend.</li>\n<li><strong>Bearish engulfing</strong> -- the opposite: a small bullish candle followed by a larger bearish candle that swallows it, after an uptrend.</li>\n<li><strong>Morning star</strong> -- a three-candle pattern (bearish, then a small indecisive candle, then a strong bullish candle), read as a potential bottom.</li>\n<li><strong>Evening star</strong> -- the same idea in reverse, read as a potential top.</li>\n</ul>\n\n<h2>How traders actually use these</h2>\n<p>No candlestick pattern works in isolation, and none of them predict the future with any reliability on their own. What experienced traders do is treat a pattern as one piece of evidence -- more meaningful when it shows up at an existing support or resistance level, alongside rising or falling volume, or in the context of the broader trend, than it is sitting in the middle of nowhere on the chart.</p>\n<p>Treat candlestick reading as a skill you layer onto a broader trading plan and risk management approach, not a shortcut that replaces one. This article is educational only and isn't a recommendation to trade any particular instrument.</p>",
        ),
        array(
            'slug'    => 'how-to-start-trading-forex',
            'title'   => 'How to Start Trading Forex',
            'excerpt' => 'A step-by-step starting point for beginners: the basic vocabulary, choosing a regulated broker, practicing on a demo account, and the mistakes that sink most new traders.',
            'content' => "<p>Forex (foreign exchange) trading means buying one currency while simultaneously selling another, aiming to profit from the change in their exchange rate. It's the largest and most liquid financial market in the world -- and also one where most beginners lose money quickly, usually for avoidable reasons. Here's a sensible order to actually learn it.</p>\n\n<h2>1. Learn the basic vocabulary first</h2>\n<p>Before opening any account, get comfortable with a few terms: a <strong>pip</strong> is the smallest standard price move in a currency pair; a <strong>lot</strong> is a standardized trade size; the <strong>spread</strong> is the small gap between the buy and sell price a broker charges you; <strong>margin</strong> is the deposit required to open a leveraged position. You don't need to master these overnight, but trading before you understand them is how avoidable losses happen.</p>\n\n<h2>2. Choose a regulated broker</h2>\n<p>Where you trade matters as much as how you trade. Look for a broker regulated by a recognized authority (CySEC, FCA, ASIC, and similar), check their fee structure (spreads, commissions, minimum deposit), and confirm which platforms they support before funding anything. See our <a href=\"/reviews/\">broker reviews</a> and <a href=\"/compare/\">comparison tool</a> if you want a starting point built from a disclosed methodology.</p>\n\n<h2>3. Practice on a demo account</h2>\n<p>Every broker worth using offers a free demo account with virtual funds. Use it -- not for a day, but for long enough to make real mistakes without real consequences: to get a feel for how fast prices move, how spreads change during news events, and how it actually feels to watch an open position lose money.</p>\n\n<h2>4. Write down an actual trading plan</h2>\n<p>Before you risk real money, decide -- in writing -- what you'll trade, what would make you enter a position, what would make you exit (both in profit and at a loss), and how much of your account you're willing to risk on any single trade. A plan you wrote in a calm moment protects you from decisions you'd make in a stressful one.</p>\n\n<h2>5. Start small, and size your risk properly</h2>\n<p>When you do trade with real money, start with an amount you can genuinely afford to lose, and size each position so that no single trade risks more than a small percentage of your account. Use stop-loss orders. It's far better to survive one hundred small mistakes while you're learning than to make one large one.</p>\n\n<h2>6. Keep a trading journal</h2>\n<p>Record every trade: why you entered, why you exited, and what you'd do differently. Patterns in your own behavior -- not just in the market -- are usually the most useful thing a journal reveals.</p>\n\n<h2>Common beginner mistakes</h2>\n<ul>\n<li>Using far more leverage than the position actually warrants.</li>\n<li>Trading without a stop-loss, \"hoping\" a losing position turns around.</li>\n<li>Revenge trading -- increasing size right after a loss to \"win it back.\"</li>\n<li>Risking money that was never meant to be risked in the first place.</li>\n</ul>\n<p>Forex and CFD trading carries a high level of risk and isn't suitable for everyone. This guide is educational only, not personalized financial advice, and most retail accounts lose money trading CFDs.</p>",
        ),
        array(
            'slug'    => 'understanding-leverage-in-forex-trading',
            'title'   => 'Understanding Leverage in Forex Trading',
            'excerpt' => 'What leverage and margin actually mean, why regulators cap it for retail clients, and how to think about the risk before you use it.',
            'content' => "<p>Leverage lets you control a larger position than the cash you've actually deposited would normally allow. It's one of the reasons forex trading attracts so much attention -- and one of the fastest ways to lose more than you expected if you don't understand exactly what it's doing to your risk.</p>\n\n<h2>Leverage and margin, concretely</h2>\n<p>Say a broker offers 30:1 leverage. Depositing $1,000 as <strong>margin</strong> lets you open a position worth up to $30,000. The $1,000 isn't a fee -- it's collateral the broker holds against the trade. Your profit or loss, though, is calculated on the full $30,000 position, not on your $1,000 margin.</p>\n\n<h2>The part that matters: it amplifies losses exactly as much as gains</h2>\n<p>If that $30,000 position moves 1% in your favor, you've made $300 -- a 30% return on your $1,000 margin. If it moves 1% against you, you've lost $300 -- also 30% of your margin, gone on a 1% market move. Leverage doesn't change how much the market moved; it changes how much that movement is worth to your account, in both directions equally.</p>\n\n<h2>Why leverage is capped for retail clients</h2>\n<p>Because of exactly that asymmetric risk to inexperienced traders, regulators that oversee CySEC-licensed and other EU-passported brokers apply leverage limits to retail client accounts under ESMA's product intervention rules: typically 30:1 on major currency pairs, 20:1 on non-major pairs, gold, and major indices, 10:1 on other commodities, 5:1 on individual equities, and 2:1 on crypto CFDs. Some brokers offer much higher leverage to clients who qualify as \"professional\" under stricter criteria, and some offshore entities advertise far higher leverage outside these regulatory regimes -- which is also a reason to check exactly which entity of a broker you'd actually be signing up with.</p>\n\n<h2>Margin calls and stop-outs</h2>\n<p>As losses on a leveraged position grow, they eat into your margin. Brokers set a margin call level (a warning) and a stop-out level (where they start closing your positions automatically) to stop your losses from exceeding your deposited funds. Relying on this as a safety net instead of managing your own risk is a common and costly mistake -- by the time a stop-out triggers, a large share of the account is often already gone.</p>\n\n<h2>Using leverage responsibly</h2>\n<ul>\n<li>Just because a broker offers high leverage doesn't mean using the maximum is a good idea.</li>\n<li>Calculate the dollar loss of a realistic adverse move before entering a trade, not after.</li>\n<li>Use stop-loss orders as your own risk control, rather than relying on a broker's stop-out level.</li>\n<li>Position size based on how much you're willing to lose, then let that determine your leverage -- not the other way around.</li>\n</ul>\n<p>CFDs and leveraged forex products are complex instruments that carry a high risk of losing money rapidly due to leverage; most retail investor accounts lose money trading them. This guide is educational only and is not personalized financial advice.</p>",
        ),
    );

    foreach ( $guides as $guide ) {
        if ( get_page_by_path( $guide['slug'], OBJECT, 'post' ) ) {
            continue;
        }
        $post_id = wp_insert_post( array(
            'post_title'   => $guide['title'],
            'post_name'    => $guide['slug'],
            'post_excerpt' => $guide['excerpt'],
            'post_content' => $guide['content'],
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_category'=> array( $category_id ),
        ) );
    }
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_guides_content' );

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
