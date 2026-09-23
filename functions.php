<?php
/**
 * GlobalFXHub theme functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Kill switch for the front-end language switcher while the Polylang
 * rollout is paused. Set to true to bring the <select> in header.php back
 * once the non-English pages are confirmed working -- this doesn't touch
 * Polylang itself, the languages you've added, or any string translations
 * already entered under Languages > Translations, so nothing is lost by
 * leaving this off.
 */
define( 'GLOBALFXHUB_LANG_SWITCHER_ENABLED', false );

require get_template_directory() . '/inc/candlestick-patterns.php';
require get_template_directory() . '/inc/translatable-strings.php';
require get_template_directory() . '/inc/market-data.php';
require get_template_directory() . '/inc/service-pages.php';

/**
 * Editorial bylines shown on articles, keyed by slug. These are
 * presentational only -- not WordPress user accounts -- and deliberately
 * carry no invented credentials (no claimed years of experience,
 * certifications, or track record), just a name, role, and a general
 * description of what they write about.
 */
function globalfxhub_get_authors() {
    return array(
        'markets-editor' => array(
            'name' => 'Elena Marsh',
            'role' => 'Markets Editor',
            'bio'  => 'Writes about broker regulation, market structure, and how to evaluate trading platforms for GlobalFXHub.',
        ),
        'technical-writer' => array(
            'name' => 'Tom Whitfield',
            'role' => 'Technical Analysis Writer',
            'bio'  => 'Covers chart reading and technical analysis for GlobalFXHub, including our candlestick pattern library.',
        ),
    );
}

/**
 * The byline to show for a post: the assigned persona if the post has a
 * "_byline" meta value naming one, otherwise the site-wide fallback that
 * was already used everywhere before bylines existed.
 */
function globalfxhub_get_post_byline( $post_id ) {
    $authors = globalfxhub_get_authors();
    $key = get_post_meta( $post_id, '_byline', true );
    if ( $key && isset( $authors[ $key ] ) ) {
        return $authors[ $key ];
    }
    return array(
        'name' => get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ),
        'role' => 'Independent market education',
        'bio'  => '',
    );
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
    /*
     * Cache-bust on the file's actual last-modified time rather than the
     * theme's static Version header -- that header sat at 1.0 through
     * many style.css edits this project, meaning every change shipped
     * under the exact same style.css?ver=1.0 URL and could be served
     * stale indefinitely by a browser or any caching layer in between.
     * filemtime() changes automatically with every edit, so this never
     * needs manual bumping again.
     */
    $style_path = get_stylesheet_directory() . '/style.css';
    $style_version = file_exists( $style_path ) ? filemtime( $style_path ) : wp_get_theme()->get( 'Version' );
    wp_enqueue_style(
        'globalfxhub-style',
        get_stylesheet_uri(),
        array( 'globalfxhub-fonts' ),
        $style_version
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
    echo '<li><a href="' . esc_url( home_url( '/reviews/' ) ) . '">' . esc_html( globalfxhub_t( 'nav_reviews' ) ) . '</a></li>';
    echo '<li><a href="' . esc_url( $blog_url ) . '">' . esc_html( globalfxhub_t( 'nav_blog' ) ) . '</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/compare/' ) ) . '">' . esc_html( globalfxhub_t( 'nav_compare' ) ) . '</a></li>';
    echo '<li><a href="' . esc_url( home_url( '/countries/' ) ) . '">' . esc_html( globalfxhub_t( 'nav_countries' ) ) . '</a></li>';
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
            return 0;
        }
    } else {
        $page_id = $page->ID;
    }

    if ( $template && $template !== get_post_meta( $page_id, '_wp_page_template', true ) ) {
        update_post_meta( $page_id, '_wp_page_template', $template );
        flush_rewrite_rules();
    }

    return $page_id;
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

function globalfxhub_ensure_compare_page() {
    globalfxhub_ensure_templated_page( 'compare', 'Compare Brokers', 'page-compare.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_compare_page' );

/**
 * Polylang duplicates a page per language rather than translating one
 * page's content in place, so /reviews/, /guides/, and /compare/ each need
 * their own page object -- with the *same* page template assigned -- in
 * every active language, or switching language lands on an untemplated
 * stub with no content (this is exactly what happened to /compare/: its
 * German translation existed with no template, so the JS-driven comparison
 * tool never rendered, even though globalfxhub_get_brokers() itself has
 * nothing language-specific about it). Runs on init at priority 20, after
 * Polylang's own init (which registers the language taxonomy on init at
 * the default priority), so pll_*() calls are safe. Self-heals a
 * translation a human added via Polylang's own "+ add translation" link
 * too, not just one that's missing outright.
 */
function globalfxhub_sync_templated_pages_across_languages() {
    if ( ! function_exists( 'pll_languages_list' ) || ! function_exists( 'pll_set_post_language' )
        || ! function_exists( 'pll_get_post_translations' ) || ! function_exists( 'pll_default_language' ) ) {
        return;
    }

    $default_lang = pll_default_language();
    $languages = pll_languages_list();
    if ( ! $default_lang || empty( $languages ) ) {
        return;
    }

    $pages = array(
        'reviews' => array( 'Reviews', 'page-reviews.php' ),
        'guides'  => array( 'Guides', 'page-guides.php' ),
        'compare' => array( 'Compare Brokers', 'page-compare.php' ),
    );

    $changed = false;

    foreach ( $pages as $slug => $meta ) {
        list( $title, $template ) = $meta;

        $page = get_page_by_path( $slug );
        if ( ! $page ) {
            continue;
        }
        $page_id = $page->ID;

        if ( function_exists( 'pll_get_post_language' ) && ! pll_get_post_language( $page_id ) ) {
            pll_set_post_language( $page_id, $default_lang );
        }

        $translations = pll_get_post_translations( $page_id );

        foreach ( $languages as $lang ) {
            if ( $lang === $default_lang ) {
                continue;
            }

            $translated_id = ! empty( $translations[ $lang ] ) ? (int) $translations[ $lang ] : 0;
            if ( ! $translated_id || ! get_post( $translated_id ) ) {
                $translated_id = wp_insert_post( array(
                    'post_title'  => $title,
                    'post_name'   => $slug,
                    'post_status' => 'publish',
                    'post_type'   => 'page',
                ) );
                if ( ! $translated_id || is_wp_error( $translated_id ) ) {
                    continue;
                }
                pll_set_post_language( $translated_id, $lang );
                $translations[ $lang ] = $translated_id;
                $changed = true;
            }

            if ( $template && $template !== get_post_meta( $translated_id, '_wp_page_template', true ) ) {
                update_post_meta( $translated_id, '_wp_page_template', $template );
                $changed = true;
            }
        }

        $translations[ $default_lang ] = $page_id;
        if ( function_exists( 'pll_save_post_translations' ) ) {
            pll_save_post_translations( $translations );
        }
    }

    if ( $changed ) {
        flush_rewrite_rules();
    }
}
add_action( 'init', 'globalfxhub_sync_templated_pages_across_languages', 20 );

/**
 * /blog/ needs a WordPress "posts page" (Settings > Reading) set, or the
 * Blog nav link doesn't resolve to anything and no posts show there --
 * not because a post is missing, but because the "posts page" was never
 * configured. Self-heals by creating a "Blog" page and setting it as the
 * posts page, but only if page_for_posts is currently unset or points at
 * a page that no longer exists -- never overrides a deliberately chosen
 * existing setting.
 */
function globalfxhub_ensure_blog_page() {
    /*
     * page_for_posts is only honored by WordPress when the site is set to
     * show a static page at the front (Settings > Reading), rather than
     * the default "latest posts" mode -- which this theme needs anyway,
     * since front-page.php (the real homepage) and home.php (the blog
     * index) are meant to be two different pages. front-page.php ignores
     * its own page's content and always renders the custom homepage
     * regardless of this setting, so switching modes doesn't change what
     * visitors see at "/" -- it only makes a *separate* posts page
     * possible. Never touches page_on_front if it's already validly set.
     */
    if ( 'page' !== get_option( 'show_on_front' ) ) {
        update_option( 'show_on_front', 'page' );
    }
    $front_id = (int) get_option( 'page_on_front' );
    if ( ! $front_id || ! get_post( $front_id ) ) {
        $front_id = globalfxhub_ensure_templated_page( 'home', 'Home', '' );
        if ( $front_id ) {
            update_option( 'page_on_front', $front_id );
        }
    }

    $current = (int) get_option( 'page_for_posts' );
    if ( $current && get_post( $current ) ) {
        return;
    }

    $page = get_page_by_path( 'blog' );
    if ( ! $page ) {
        $page_id = wp_insert_post( array(
            'post_title'  => 'Blog',
            'post_name'   => 'blog',
            'post_status' => 'publish',
            'post_type'   => 'page',
        ) );
        if ( ! $page_id || is_wp_error( $page_id ) ) {
            return;
        }
    } else {
        $page_id = $page->ID;
    }

    update_option( 'page_for_posts', $page_id );
    flush_rewrite_rules();
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_blog_page' );

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

    $assets = get_template_directory_uri() . '/assets/guides/';

    $hero_images = array(
        'how-to-read-candlestick-patterns'        => $assets . 'candlesticks-hero.png',
        'how-to-start-trading-forex'               => $assets . 'start-trading-hero.png',
        'understanding-leverage-in-forex-trading'  => $assets . 'leverage-hero.png',
    );

    $body_images = array(
        'how-to-read-candlestick-patterns' => array(
            array(
                'anchor' => '<h2>Multi-candle patterns</h2>',
                'src'    => $assets . 'candlestick-anatomy.png',
                'alt'    => 'Diagram labeling the open, high, low, and close of a single candlestick',
                'caption'=> 'The four prices behind every candle.',
                'before' => true,
            ),
            array(
                'anchor' => '<h2>How traders actually use these</h2>',
                'src'    => $assets . 'candlestick-patterns.png',
                'alt'    => 'Reference grid of eight candlestick patterns: doji, hammer, shooting star, marubozu, bullish engulfing, bearish engulfing, morning star, evening star',
                'caption'=> 'Eight patterns worth being able to recognize on sight.',
                'before' => true,
            ),
        ),
        'how-to-start-trading-forex' => array(
            array(
                'anchor' => '<h2>1. Learn the basic vocabulary first</h2>',
                'src'    => $assets . 'start-trading-steps.png',
                'alt'    => 'Six-step process: learn the vocabulary, choose a regulated broker, practice on a demo, write a trading plan, start small, keep a journal',
                'caption'=> 'The order that keeps most avoidable mistakes off the table.',
                'before' => true,
            ),
        ),
        'understanding-leverage-in-forex-trading' => array(
            array(
                'anchor' => '<h2>The part that matters: it amplifies losses exactly as much as gains</h2>',
                'src'    => $assets . 'leverage-example.png',
                'alt'    => 'Bar chart comparing a $1,000 margin deposit to the $30,000 position it controls at 30:1 leverage',
                'caption'=> '30:1 leverage: a small deposit, a much larger position.',
                'before' => true,
            ),
            array(
                'anchor' => '<h2>Margin calls and stop-outs</h2>',
                'src'    => $assets . 'leverage-caps-chart.png',
                'alt'    => 'Bar chart of maximum leverage for EU retail clients by asset class: 30:1 major FX pairs, 20:1 non-major pairs/gold/indices, 10:1 other commodities, 5:1 equities, 2:1 crypto',
                'caption'=> 'Retail leverage caps under ESMA rules, by asset class.',
                'before' => true,
            ),
        ),
    );

    foreach ( $guides as &$guide ) {
        if ( empty( $body_images[ $guide['slug'] ] ) ) {
            continue;
        }
        foreach ( $body_images[ $guide['slug'] ] as $img ) {
            $figure = '<figure><img src="' . esc_url( $img['src'] ) . '" alt="' . esc_attr( $img['alt'] ) . '" loading="lazy"><figcaption>' . esc_html( $img['caption'] ) . '</figcaption></figure>' . "\n\n";
            $guide['content'] = str_replace( $img['anchor'], $figure . $img['anchor'], $guide['content'] );
        }
    }
    unset( $guide );

    $bylines = array(
        'how-to-start-trading-forex'               => 'markets-editor',
        'understanding-leverage-in-forex-trading'   => 'markets-editor',
        'how-to-read-candlestick-patterns'          => 'technical-writer',
    );

    foreach ( $guides as $guide ) {
        $existing = get_page_by_path( $guide['slug'], OBJECT, 'post' );
        if ( $existing ) {
            $post_id = $existing->ID;
        } else {
            $post_id = wp_insert_post( array(
                'post_title'   => $guide['title'],
                'post_name'    => $guide['slug'],
                'post_excerpt' => $guide['excerpt'],
                'post_content' => $guide['content'],
                'post_status'  => 'publish',
                'post_type'    => 'post',
                'post_category'=> array( $category_id ),
            ) );
            if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $hero_images[ $guide['slug'] ] ) ) {
                update_post_meta( $post_id, '_guide_hero_image', $hero_images[ $guide['slug'] ] );
            }
        }

        if ( $post_id && ! is_wp_error( $post_id ) && ! empty( $bylines[ $guide['slug'] ] )
            && ! get_post_meta( $post_id, '_byline', true ) ) {
            update_post_meta( $post_id, '_byline', $bylines[ $guide['slug'] ] );
        }
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
 * All broker URLs share one underlying "Reviews" Page object (the
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
    $deposit_label = $broker['min_deposit_display'] ? $broker['min_deposit_display'] . ' minimum deposit' : 'minimum deposit not independently confirmed';
    $spread_label  = null !== $broker['spread_eurusd'] ? $broker['spread_eurusd'] . ' pip average EUR/USD spread' : 'EUR/USD spread not independently confirmed';
    $description = sprintf(
        '%s review: %s, %s, %s. Scored %s/5 overall on regulation, cost, platforms and track record -- see the full breakdown and what other reviewers say.',
        $broker['name'],
        $reg_label,
        $deposit_label,
        $spread_label,
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

    if ( null !== $broker['scores']['regulation'] && $broker['scores']['regulation'] >= 4.0 ) {
        $pros[] = 'Wide regulatory footprint -- CySEC plus ' . $broker['other_reg_count'] . ' other regulator(s), including ' . implode( ', ', array_slice( $broker['other_reg'], 0, 2 ) );
    } elseif ( null !== $broker['scores']['regulation'] ) {
        $cons[] = 'Thinner regulatory footprint than most peers on this list';
    }

    if ( null === $broker['scores']['cost'] ) {
        $cons[] = 'Minimum deposit and/or EUR/USD spread could not be independently confirmed -- check current terms directly with the broker.';
    } elseif ( $broker['scores']['cost'] >= 4.0 ) {
        $pros[] = 'Low cost to start: ' . $broker['min_deposit_display'] . ' minimum deposit, ' . $broker['spread_eurusd'] . ' pip average EUR/USD spread';
    } elseif ( $broker['scores']['cost'] <= 2.5 ) {
        $cons[] = 'Higher cost than most peers on this list (spread and/or minimum deposit)';
    }

    if ( count( $broker['platforms'] ) >= 3 ) {
        $pros[] = 'Broad platform choice: ' . implode( ', ', $broker['platforms'] );
    } elseif ( ! $has_mt ) {
        $cons[] = 'No MetaTrader (MT4/MT5) support -- proprietary platform only';
    }

    if ( null === $broker['scores']['track_record'] ) {
        $cons[] = 'Founding year could not be independently confirmed, so operating history could not be scored.';
    } elseif ( $broker['scores']['track_record'] >= 4.0 ) {
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
        array( 'slug' => 'ig', 'name' => 'IG', 'entity' => 'IGM Forex Ltd', 'cysec' => '309/16', 'founded' => 1974, 'hq' => 'London, UK', 'min_deposit_usd' => 1, 'min_deposit_display' => '£1', 'spread_eurusd' => 0.6, 'platforms' => array('Proprietary', 'MT4', 'ProRealTime'), 'other_reg' => array('FCA', 'ASIC', '+9 more Tier-1'), 'other_reg_count' => 10, 'instruments' => '17,000+ across forex, indices, shares, commodities, crypto CFDs', 'blurb' => 'Long-established, publicly listed (LSE: IGG), one of the widest regulatory footprints of any broker on this list.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.76, 'platforms' => 3.4, 'track_record' => 5, 'overall' => 4.13 ), 'rank' => 1 ),
        array( 'slug' => 'avatrade', 'name' => 'AvaTrade', 'entity' => 'Ava Trade EU Ltd', 'cysec' => '—', 'founded' => 2006, 'hq' => 'Dublin, Ireland', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 0.93, 'platforms' => array('Proprietary (AvaTradeGO)', 'MT4', 'MT5', 'DupliTrade', 'ZuluTrade'), 'other_reg' => array('ASIC', 'JFSA (Japan)', 'CIRO (Canada)', 'Central Bank of Ireland'), 'other_reg_count' => 4, 'instruments' => 'CFDs across forex, stocks, indices, commodities, ETFs, bonds, crypto', 'blurb' => 'EU clients typically served under Central Bank of Ireland / MiFID passporting rather than a direct CySEC CIF licence -- worth confirming which entity applies to you.', 'cysec_note' => 'Regulated in the EU via MiFID passporting; confirm current entity with AvaTrade directly.', 'scores' => array( 'regulation' => 4.2, 'cost' => 4.61, 'platforms' => 5, 'track_record' => 2.39, 'overall' => 4.12 ), 'rank' => 2 ),
        array( 'slug' => 'thinkmarkets', 'name' => 'ThinkMarkets', 'entity' => 'TF Global Markets (Europe) Ltd', 'cysec' => '215/13', 'founded' => 2010, 'hq' => 'Melbourne, Australia / London, UK (group); European entity based in Cyprus', 'min_deposit_usd' => 0, 'min_deposit_display' => '$0 (Standard account); $500 for ThinkZero account', 'spread_eurusd' => 1.1, 'platforms' => array('MT4', 'MT5', 'TradingView', 'Proprietary (ThinkTrader)'), 'other_reg' => array('FCA', 'ASIC', 'FSCA', 'DFSA', 'FMA'), 'other_reg_count' => 5, 'instruments' => 'Up to 4,000 CFD instruments across forex (40+ pairs), indices, shares, commodities and crypto, varying by platform.', 'blurb' => 'ThinkMarkets is a dual-headquartered (Melbourne/London) group founded in 2010 by brothers Nauman and Faizan Anees, in which each of its major-jurisdiction licences (FCA, ASIC, FSCA, DFSA, FMA) sits in a different legal entity from its CySEC-licensed European arm.', 'cysec_note' => 'All of these are held by separate TF Global Markets group entities, not by TF Global Markets (Europe) Ltd (the CySEC entity): FCA by the UK entity, ASIC by the Australian entity, FSCA by the South African entity, DFSA by a Dubai entity, and FMA by a New Zealand entity. The group also holds non-Tier-1 licences in the Cayman Islands, Mauritius and Seychelles.', 'scores' => array( 'regulation' => 5, 'cost' => 4.56, 'platforms' => 4.2, 'track_record' => 2.06, 'overall' => 4.12 ), 'rank' => 3 ),
        array( 'slug' => 'forex-com', 'name' => 'FOREX.com', 'entity' => 'StoneX Europe Ltd', 'cysec' => '400/21', 'founded' => 1999, 'hq' => 'New Jersey, USA (EU ops via Cyprus)', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1, 'platforms' => array('Proprietary', 'MT4', 'MT5', 'TradingView'), 'other_reg' => array('NFA/CFTC (US)', 'FCA', 'ASIC'), 'other_reg_count' => 4, 'instruments' => '80+ FX pairs plus indices, commodities, shares CFDs', 'blurb' => 'Backed by NASDAQ-listed StoneX Group; strong educational content and platform variety.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.58, 'platforms' => 4.2, 'track_record' => 2.96, 'overall' => 3.83 ), 'rank' => 4 ),
        array( 'slug' => 'fxcm', 'name' => 'FXCM', 'entity' => 'FXCM EU Ltd', 'cysec' => '392/20', 'founded' => 1999, 'hq' => 'London, UK', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 1.3, 'platforms' => array('Trading Station (proprietary)', 'MT4', 'ZuluTrade', 'NinjaTrader'), 'other_reg' => array('FCA', 'ASIC', 'FSCA'), 'other_reg_count' => 3, 'instruments' => 'Forex, indices, commodities, crypto CFDs', 'blurb' => 'One of the longer-running retail forex names, with unusually wide platform choice including NinjaTrader for futures-style traders.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.47, 'platforms' => 4.2, 'track_record' => 2.96, 'overall' => 3.79 ), 'rank' => 5 ),
        array( 'slug' => 'cfi', 'name' => 'CFI', 'entity' => 'Credit Financier Invest (CFI) Ltd', 'cysec' => '—', 'founded' => 1998, 'hq' => 'Dubai, UAE', 'min_deposit_usd' => 0, 'min_deposit_display' => 'No minimum', 'spread_eurusd' => 0.4, 'platforms' => array('MT5', 'cTrader', 'TradingView', 'Proprietary (CFI Trading App)'), 'other_reg' => array('FCA', 'FSCA'), 'other_reg_count' => 2, 'instruments' => '1500+ instruments across forex, stocks, energies, metals, indices, ETFs, crypto, bonds and futures', 'blurb' => 'CFI Financial Group traces its roots to Credit Financier Invest SAL, founded in Beirut in 1998, was reorganized as CFI Financial Group in 2015, and moved its headquarters to Dubai in 2017, and has fully phased out MT4 in favor of MT5, cTrader and TradingView.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.84, 'platforms' => 4.2, 'track_record' => 3.04, 'overall' => 3.68 ), 'rank' => 6 ),
        array( 'slug' => 'plus500', 'name' => 'Plus500', 'entity' => 'Plus500CY Ltd', 'cysec' => '250/14', 'founded' => 2008, 'hq' => 'Haifa, Israel (EU ops via Cyprus)', 'min_deposit_usd' => 54, 'min_deposit_display' => '€50', 'spread_eurusd' => 1.3, 'platforms' => array('WebTrader (proprietary)'), 'other_reg' => array('FCA', 'ASIC', 'FSCA', 'MAS', 'FMA'), 'other_reg_count' => 5, 'instruments' => '2,800+ CFDs across forex, indices, commodities, shares, ETFs, crypto', 'blurb' => 'Publicly listed (LSE: PLUS), one of the broadest regulatory footprints here, but no MT4/MT5 support.', 'scores' => array( 'regulation' => 5, 'cost' => 4.47, 'platforms' => 1.8, 'track_record' => 2.22, 'overall' => 3.65 ), 'rank' => 7 ),
        array( 'slug' => 'pepperstone', 'name' => 'Pepperstone', 'entity' => 'Pepperstone EU Limited', 'cysec' => '388/20', 'founded' => 2010, 'hq' => 'Melbourne, Australia', 'min_deposit_usd' => 10, 'min_deposit_display' => '$10', 'spread_eurusd' => 1.1, 'platforms' => array('MT4', 'MT5', 'cTrader', 'TradingView'), 'other_reg' => array('ASIC', 'FCA', 'DFSA'), 'other_reg_count' => 3, 'instruments' => '1,300+ instruments across forex, indices, commodities, shares', 'blurb' => 'Added its CySEC licence in 2020 ahead of Brexit to secure EU client access; strong reputation among active/algo traders for platform choice.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.56, 'platforms' => 4.2, 'track_record' => 2.06, 'overall' => 3.64 ), 'rank' => 8 ),
        array( 'slug' => 'exness', 'name' => 'Exness', 'entity' => 'Exness (Cy) Ltd', 'cysec' => '178/12', 'founded' => 2008, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 10, 'min_deposit_display' => '$10 (varies by payment method; some methods allow as low as $1)', 'spread_eurusd' => 0.2, 'platforms' => array('MT4', 'MT5', 'Proprietary (Exness Terminal)'), 'other_reg' => array('FCA', 'FSCA', 'CMA'), 'other_reg_count' => 3, 'instruments' => '100+ instruments across forex, indices, commodities, stock and crypto CFDs', 'blurb' => 'One of the highest-volume retail forex/CFD brokers globally, Exness offers its own proprietary Exness Terminal alongside MT4/MT5 and holds licences from CySEC, the FCA and South Africa\'s FSCA.', 'cysec_note' => 'Exness Group also holds licences in Seychelles (FSA), Mauritius (FSC), BVI (FSC), Curacao (CBCS), Jordan and the UAE, but these are not on the approved Tier-1 list so are excluded here; note Exness (UK) Ltd\'s FCA-licensed entity does not serve retail clients.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.92, 'platforms' => 3.4, 'track_record' => 2.22, 'overall' => 3.62 ), 'rank' => 9 ),
        array( 'slug' => 'admirals', 'name' => 'Admirals', 'entity' => 'Admirals Europe Ltd', 'cysec' => '201/13', 'founded' => 2001, 'hq' => 'Tallinn, Estonia (group HQ; Admirals Europe Ltd is the Cyprus-regulated subsidiary)', 'min_deposit_usd' => 108, 'min_deposit_display' => '€100 (EU/Cyprus entity, Trade.MT5 account)', 'spread_eurusd' => 0.8, 'platforms' => array('MT4', 'MT5', 'TradingView', 'Proprietary (Admirals Platform)'), 'other_reg' => array('FCA', 'FSCA'), 'other_reg_count' => 2, 'instruments' => 'Wide range of CFDs and real assets across forex, stocks, ETFs, indices, commodities and bonds', 'blurb' => 'One of the older and larger MetaTrader-focused broker groups on this list, founded in Estonia in 2001 and rebranded from Admiral Markets to Admirals in 2021.', 'cysec_note' => 'Previously ASIC-regulated via its Australian unit, which was sold and rebranded in 2025, ending direct ASIC-regulated presence.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.66, 'platforms' => 4.2, 'track_record' => 2.8, 'overall' => 3.58 ), 'rank' => 10 ),
        array( 'slug' => 'fxopen', 'name' => 'FXOpen', 'entity' => 'FXOpen EU Ltd', 'cysec' => '194/13', 'founded' => 2003, 'hq' => 'London, UK (group); FXOpen EU Ltd, the CySEC entity, is based in Cyprus', 'min_deposit_usd' => 300, 'min_deposit_display' => '$300 / €300', 'spread_eurusd' => 1, 'platforms' => array('MT4', 'MT5', 'TradingView', 'Proprietary (TickTrader)'), 'other_reg' => array('FCA', 'ASIC'), 'other_reg_count' => 2, 'instruments' => 'Forex plus CFDs on indices, commodities, stocks and cryptocurrencies', 'blurb' => 'FXOpen began in 2003 as a trading-education outfit before launching brokerage services in 2005, and today operates as a multi-entity group regulated separately by CySEC (Cyprus), the FCA (UK) and ASIC (Australia).', 'cysec_note' => 'FCA and ASIC licences are held by separate FXOpen group entities (UK and Australia), not by FXOpen EU Ltd itself.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.54, 'platforms' => 4.2, 'track_record' => 2.63, 'overall' => 3.51 ), 'rank' => 11 ),
        array( 'slug' => 'eightcap', 'name' => 'Eightcap', 'entity' => 'Eightcap EU Ltd', 'cysec' => '246/14', 'founded' => 2009, 'hq' => 'Melbourne, Australia', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1, 'platforms' => array('MT4', 'MT5', 'TradingView'), 'other_reg' => array('ASIC', 'FCA', 'SCB'), 'other_reg_count' => 3, 'instruments' => '800+ across forex, indices, commodities, shares, crypto CFDs', 'blurb' => 'Newer to CySEC regulation than most peers here, with strong TradingView integration.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.58, 'platforms' => 3.4, 'track_record' => 2.14, 'overall' => 3.5 ), 'rank' => 12 ),
        array( 'slug' => 'fxpro', 'name' => 'FxPro', 'entity' => 'FxPro Financial Services Limited', 'cysec' => '078/07', 'founded' => 2006, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.44, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array('FCA', 'FSCA', 'SCB'), 'other_reg_count' => 3, 'instruments' => 'Forex, shares, indices, commodities, metals, futures CFDs', 'blurb' => 'One of the oldest CySEC licences on this list (issued 2007), reflecting a long operating history in Cyprus specifically.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.41, 'platforms' => 3.4, 'track_record' => 2.39, 'overall' => 3.5 ), 'rank' => 13 ),
        array( 'slug' => 'fp-markets', 'name' => 'FP Markets', 'entity' => 'First Prudential Markets Ltd', 'cysec' => '371/18', 'founded' => 2005, 'hq' => 'Sydney, Australia', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1, 'platforms' => array('MT4', 'MT5', 'cTrader', 'TradingView'), 'other_reg' => array('ASIC', 'FSCA'), 'other_reg_count' => 2, 'instruments' => '10,000+ instruments across forex, indices, commodities and shares CFDs (plus direct Australian share trading via the third-party IRESS platform)', 'blurb' => 'Founded in Australia in 2005 by Matt Murphie, FP Markets is regulated by ASIC and South Africa\'s FSCA in addition to CySEC, and is one of the few brokers here offering cTrader and TradingView alongside MT4/MT5.', 'cysec_note' => 'FP Markets group also holds a Seychelles FSA licence and a Saint Lucia registration, both offshore and excluded here.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.58, 'platforms' => 4.2, 'track_record' => 2.47, 'overall' => 3.49 ), 'rank' => 14 ),
        array( 'slug' => 'hf-markets', 'name' => 'HF Markets', 'entity' => 'HF Markets (Europe) Ltd', 'cysec' => '183/12', 'founded' => 2010, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 5, 'min_deposit_display' => '$5 (Premium account); Pro account requires $100/€100', 'spread_eurusd' => 1.2, 'platforms' => array('MT4', 'MT5', 'Proprietary (HFM App)'), 'other_reg' => array('FCA', 'DFSA', 'FSCA'), 'other_reg_count' => 3, 'instruments' => '500+ CFDs across forex, metals, indices, stocks, bonds, energies, commodities and cryptocurrencies.', 'blurb' => 'HF Markets (HFM), formerly HotForex, was founded in 2010 in Cyprus and rebranded to HFM in 2022, with group entities separately licensed by the FCA, DFSA and FSCA.', 'cysec_note' => 'FCA held by HF Markets (UK) Ltd, DFSA by HF Markets (DIFC) Ltd, and FSCA by HF Markets SA (PTY) Ltd -- separate group entities from the CySEC-licensed HF Markets (Europe) Ltd.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.52, 'platforms' => 3.4, 'track_record' => 2.06, 'overall' => 3.47 ), 'rank' => 15 ),
        array( 'slug' => 'capital-com', 'name' => 'Capital.com', 'entity' => 'Capital Com SV Investments Ltd', 'cysec' => '319/17', 'founded' => 2016, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 20, 'min_deposit_display' => '$20', 'spread_eurusd' => 0.6, 'platforms' => array('Proprietary', 'MT4', 'TradingView'), 'other_reg' => array('FCA', 'ASIC', 'FSCA'), 'other_reg_count' => 3, 'instruments' => 'Forex, indices, commodities, shares, crypto CFDs', 'blurb' => 'Newer than most on this list but grew fast; low minimum deposit and commission-free pricing on most instruments.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.76, 'platforms' => 3.4, 'track_record' => 1.57, 'overall' => 3.44 ), 'rank' => 16 ),
        array( 'slug' => 'xm', 'name' => 'XM', 'entity' => 'Trading Point of Financial Instruments Ltd', 'cysec' => '120/10', 'founded' => 2009, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 5, 'min_deposit_display' => '$5', 'spread_eurusd' => 1.1, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('ASIC', 'FCA', 'DFSA'), 'other_reg_count' => 3, 'instruments' => '1,000+ across forex, stocks, indices, commodities, metals, crypto', 'blurb' => 'One of the lowest minimum deposits on this list; large global client base and a long-running CySEC licence.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.56, 'platforms' => 2.6, 'track_record' => 2.14, 'overall' => 3.34 ), 'rank' => 17 ),
        array( 'slug' => 'markets-com', 'name' => 'Markets.com', 'entity' => 'Safecap Investments Ltd', 'cysec' => '092/08', 'founded' => 2008, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.3, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FSCA', 'FCA', 'ASIC'), 'other_reg_count' => 3, 'instruments' => 'Forex, indices, commodities, shares and crypto CFDs (Safecap/Markets.com is part of Finalto, a Playtech-owned group).', 'blurb' => 'Markets.com is operated by Safecap Investments Ltd, part of Finalto (owned by Playtech, a FTSE-listed company), and Safecap itself holds both CySEC and South African FSCA licences directly, while its FCA and ASIC licences sit in separate Finalto group entities.', 'cysec_note' => 'FSCA licence is held directly by Safecap Investments Ltd, the same CySEC entity. FCA (via Finalto Trading Ltd) and ASIC (via Finalto (Australia) Pty Ltd) are held by separate group entities under the Finalto/Playtech umbrella, not by Safecap itself.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.46, 'platforms' => 2.6, 'track_record' => 2.22, 'overall' => 3.32 ), 'rank' => 18 ),
        array( 'slug' => 'easymarkets', 'name' => 'easyMarkets', 'entity' => 'Easy Forex Trading Ltd', 'cysec' => '079/07', 'founded' => 2001, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 25, 'min_deposit_display' => '$25', 'spread_eurusd' => 1.8, 'platforms' => array('MT4', 'MT5', 'Proprietary (easyMarkets platform)'), 'other_reg' => array('ASIC', 'FSCA'), 'other_reg_count' => 2, 'instruments' => '300+ instruments across forex and CFDs', 'blurb' => 'One of the longer-established Cyprus brokers (founded 2001 as Easy Forex), easyMarkets is known for proprietary risk-management tools such as dealCancellation and Freeze Rate built into its own platform rather than relying only on MT4/MT5.', 'cysec_note' => 'Also holds licences via other group entities from the Seychelles FSA and BVI FSC (both excluded as non-Tier-1).', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.28, 'platforms' => 3.4, 'track_record' => 2.8, 'overall' => 3.3 ), 'rank' => 19 ),
        array( 'slug' => 'afterprime', 'name' => 'Afterprime', 'entity' => 'Claremont Square Capital Ltd', 'cysec' => '368/18', 'founded' => null, 'hq' => 'Sydney, Australia (group HQ, via Argamon Markets Pty Ltd); the CySEC entity, Claremont Square Capital Ltd (formerly Afterprime Europe Ltd), is registered in Nicosia (Lakatamia), Cyprus', 'min_deposit_usd' => 0, 'min_deposit_display' => 'No stated minimum (recommended ~$200)', 'spread_eurusd' => 0.9, 'platforms' => array('MT4', 'MT5', 'TraderEvolution'), 'other_reg' => array('ASIC'), 'other_reg_count' => 1, 'instruments' => 'Forex, metals, indices and commodities CFDs, traded on a 100% A-book/STP execution model', 'blurb' => 'Founded by the same pair who built and sold Global Prime (Jeremy Kinstlinger and Elan Bension), and runs a 100% A-book model where every client trade is hedged externally with Tier-1 liquidity providers rather than taken on as principal risk.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.64, 'platforms' => 3.4, 'track_record' => null, 'overall' => 3.27 ), 'rank' => 20 ),
        array( 'slug' => 'ingot-brokers', 'name' => 'INGOT Brokers', 'entity' => 'Ingot Brokers Europe Ltd', 'cysec' => '462/25', 'founded' => 2006, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('ASIC', 'CMA'), 'other_reg_count' => 2, 'instruments' => null, 'blurb' => 'Ingot Brokers Europe Ltd is a newly licensed CySEC entity (licence granted ~Nov 2025) of the wider INGOT group, which is also regulated by ASIC in Australia and the CMA in Kenya.', 'cysec_note' => 'ASIC and CMA licences are held by other entities within the wider INGOT group, not confirmed to be held directly by the CySEC entity, which as of late 2025 had obtained its CySEC licence but reportedly had not yet begun operations under it.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.96, 'platforms' => 2.6, 'track_record' => 2.39, 'overall' => 3.27 ), 'rank' => 21 ),
        array( 'slug' => 'go-markets', 'name' => 'GO Markets', 'entity' => 'Go Markets Ltd', 'cysec' => '322/17', 'founded' => 2006, 'hq' => 'Melbourne, Australia (group HQ); Go Markets Ltd, the CySEC entity, is Cyprus-based', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1, 'platforms' => array('MT4', 'MT5', 'cTrader', 'TradingView'), 'other_reg' => array('ASIC'), 'other_reg_count' => 1, 'instruments' => 'Forex plus CFDs on indices, commodities and shares', 'blurb' => 'GO Markets was founded in 2006 in Melbourne under ASIC regulation before adding a CySEC-licensed EU entity, and it offers a commission-free Standard account (spreads from ~1.0 pip) alongside a raw-spread \'GO Plus+\' ECN account.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.58, 'platforms' => 4.2, 'track_record' => 2.39, 'overall' => 3.23 ), 'rank' => 22 ),
        array( 'slug' => 'etoro', 'name' => 'eToro', 'entity' => 'eToro (Europe) Ltd', 'cysec' => '109/10', 'founded' => 2007, 'hq' => 'Tel Aviv / Limassol', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 1, 'platforms' => array('Proprietary (CopyTrader)'), 'other_reg' => array('FCA', 'ASIC', 'FSAS'), 'other_reg_count' => 3, 'instruments' => '5,000+ across forex, stocks, ETFs, commodities, indices, 100+ crypto', 'blurb' => 'Best known for social/copy trading. No MT4/MT5 -- everything happens on eToro\'s own platform.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.59, 'platforms' => 1.8, 'track_record' => 2.31, 'overall' => 3.22 ), 'rank' => 23 ),
        array( 'slug' => 'xs-markets', 'name' => 'XS Markets', 'entity' => 'XS Markets Ltd', 'cysec' => '412/22', 'founded' => 2010, 'hq' => null, 'min_deposit_usd' => 0, 'min_deposit_display' => 'No minimum', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('ASIC', 'FSCA'), 'other_reg_count' => 2, 'instruments' => null, 'blurb' => 'XS Markets Ltd is the CySEC-regulated Cyprus entity of the globally rebranded XS.com group (formerly XSMarkets, founded 2010), which also operates ASIC-licensed and FSCA-licensed entities alongside several smaller offshore ones that don\'t count as Tier-1.', 'scores' => array( 'regulation' => 2.6, 'cost' => 5, 'platforms' => 2.6, 'track_record' => 2.06, 'overall' => 3.21 ), 'rank' => 24 ),
        array( 'slug' => 'atfx', 'name' => 'ATFX', 'entity' => 'ATFX Global Markets (Cy) Ltd', 'cysec' => '285/15', 'founded' => 2017, 'hq' => 'Limassol, Cyprus (Cyprus entity); wider ATFX group headquartered in London, UK', 'min_deposit_usd' => 200, 'min_deposit_display' => '$200 (Standard account)', 'spread_eurusd' => 1.2, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'ASIC', 'FSCA'), 'other_reg_count' => 3, 'instruments' => '350+ CFDs across forex, indices, shares, commodities and cryptocurrencies', 'blurb' => 'Backed by a multi-entity global group holding FCA, ASIC and FSCA licences in addition to its Cyprus CIF licence.', 'scores' => array( 'regulation' => 3.4, 'cost' => 4.48, 'platforms' => 2.6, 'track_record' => 1.49, 'overall' => 3.18 ), 'rank' => 25 ),
        array( 'slug' => 'swissquote', 'name' => 'Swissquote', 'entity' => 'Swissquote Capital Markets Limited', 'cysec' => '422/22', 'founded' => 1996, 'hq' => 'Limassol, Cyprus (Cyprus entity); parent Swissquote Group headquartered in Gland, Switzerland', 'min_deposit_usd' => 1000, 'min_deposit_display' => '$1,000 (varies $1,000-$10,000 by account tier)', 'spread_eurusd' => 1.3, 'platforms' => array('MT4', 'MT5', 'Proprietary (Advanced Trader)'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => '80+ forex pairs and roughly 400-500 CFDs on indices, commodities, bonds and shares via the CySEC/EU entity; the wider Swissquote Bank platform (a different regulated entity) offers a much larger universe of stocks, ETFs, funds, options and bonds for investing.', 'blurb' => 'Swissquote is the CFD/forex arm of Swissquote Group, a Swiss stock-exchange-listed (SIX: SQN) online bank founded in 1996 and regulated by FINMA in Switzerland, with its EU retail CFD business run through the CySEC-licensed Swissquote Capital Markets Limited in Cyprus.', 'cysec_note' => 'FCA licence is held by a separate group entity, Swissquote Ltd (UK), not by Swissquote Capital Markets Ltd (Cyprus). The Swissquote Group\'s Swiss parent, Swissquote Bank Ltd, is regulated by FINMA (Switzerland) -- a Tier-1 banking regulator not on the standard list, noted here since it applies to the parent bank, not the CySEC CFD entity.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.29, 'platforms' => 3.4, 'track_record' => 3.2, 'overall' => 3.15 ), 'rank' => 26 ),
        array( 'slug' => 'topfx', 'name' => 'TopFX', 'entity' => 'Topfx Ltd', 'cysec' => '138/11', 'founded' => 2010, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 0, 'min_deposit_display' => 'No minimum', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => '600+ instruments across forex and CFDs', 'blurb' => 'TopFX is a CySEC- and FCA-regulated ECN broker based in Limassol with no stated minimum deposit and raw/ECN pricing (commission-based) rather than a traditional marked-up standard account.', 'scores' => array( 'regulation' => 1.8, 'cost' => 5, 'platforms' => 3.4, 'track_record' => 2.06, 'overall' => 3.13 ), 'rank' => 27 ),
        array( 'slug' => 'xtb', 'name' => 'XTB', 'entity' => 'XTB Limited', 'cysec' => '169/12', 'founded' => 2002, 'hq' => 'Warsaw, Poland', 'min_deposit_usd' => 0, 'min_deposit_display' => '$0', 'spread_eurusd' => 0.7, 'platforms' => array('xStation 5 (proprietary)'), 'other_reg' => array('FCA', 'KNF (Poland)'), 'other_reg_count' => 3, 'instruments' => '11,300+ across forex, indices, commodities, stocks, ETFs, crypto CFDs', 'blurb' => 'Publicly listed on the Warsaw Stock Exchange. No minimum deposit, no MT4/MT5 -- everything runs on its own xStation 5 platform.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.72, 'platforms' => 1.8, 'track_record' => 2.71, 'overall' => 3.1 ), 'rank' => 28 ),
        array( 'slug' => 'fortrade', 'name' => 'Fortrade', 'entity' => 'Fortrade Cyprus Ltd', 'cysec' => '385/20', 'founded' => 2013, 'hq' => 'London, UK', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 2.5, 'platforms' => array('MT4', 'Proprietary (Fortrader)'), 'other_reg' => array('ASIC', 'FCA', 'CIRO'), 'other_reg_count' => 3, 'instruments' => '300+ instruments including about 60 forex pairs, 15 cryptocurrencies and 150 stock CFDs', 'blurb' => 'Fortrade is regulated across four jurisdictions (CySEC, FCA, ASIC and CIRO) and offers its own proprietary Fortrader platform in addition to MT4.', 'scores' => array( 'regulation' => 3.4, 'cost' => 3.98, 'platforms' => 2.6, 'track_record' => 1.82, 'overall' => 3.1 ), 'rank' => 29 ),
        array( 'slug' => 'ic-markets', 'name' => 'IC Markets', 'entity' => 'IC Markets (EU) Ltd', 'cysec' => '362/18', 'founded' => 2007, 'hq' => 'Sydney, Australia', 'min_deposit_usd' => 200, 'min_deposit_display' => '$200', 'spread_eurusd' => 0.8, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array('ASIC'), 'other_reg_count' => 1, 'instruments' => 'Forex, metals, stocks, commodities, futures, crypto, indices', 'blurb' => 'Known for raw ECN-style pricing aimed at active and algorithmic traders; higher minimum deposit than most on this list.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.64, 'platforms' => 3.4, 'track_record' => 2.31, 'overall' => 3.07 ), 'rank' => 30 ),
        array( 'slug' => 'fusion-markets', 'name' => 'Fusion Markets', 'entity' => 'Fusion Markets EU Ltd', 'cysec' => '444/24', 'founded' => 2017, 'hq' => 'Melbourne, Australia', 'min_deposit_usd' => 0, 'min_deposit_display' => 'No minimum', 'spread_eurusd' => 0.9, 'platforms' => array('MT4', 'MT5', 'cTrader', 'TradingView'), 'other_reg' => array('ASIC'), 'other_reg_count' => 1, 'instruments' => '250+ instruments across forex, indices, commodities, shares and crypto CFDs', 'blurb' => 'Founded in 2017 by former Pepperstone executives Phil Horner and David Swinden, Fusion Markets is built around low-cost pricing and added its CySEC-licensed EU entity (444/24) as one of the newest licences in this group, issued in 2024.', 'cysec_note' => 'Fusion Markets also operates offshore entities regulated by Vanuatu\'s VFSC and the Seychelles FSA, excluded here as non-Tier-1.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.64, 'platforms' => 4.2, 'track_record' => 1.49, 'overall' => 3.07 ), 'rank' => 31 ),
        array( 'slug' => 'fxglobe', 'name' => 'FXGlobe', 'entity' => 'FXGLOBE Ltd', 'cysec' => '205/13', 'founded' => null, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 540, 'min_deposit_display' => '€500 (general minimum first funding); a "Pathfinder" entry account is advertised elsewhere from €250', 'spread_eurusd' => 0.7, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => 'Forex, stocks/equities, and other CFDs (site has a dedicated stocks section and a separate equities account type)', 'blurb' => 'FXGlobe runs three tiered account levels (Pathfinder, Navigator, Infinity) with rising minimum deposits, and the same brand also carries a South African FSCA licence through a separate related entity.', 'cysec_note' => 'The FSCA licence (FSP No. 52045) is held by a related entity, APLFX (Pty) Ltd, trading under the FXGlobe brand -- not by FXGLOBE Ltd, the CySEC entity.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.62, 'platforms' => 2.6, 'track_record' => null, 'overall' => 3.06 ), 'rank' => 32 ),
        array( 'slug' => 'ironfx', 'name' => 'IronFX', 'entity' => 'Notesco Financial Services Ltd', 'cysec' => '125/10', 'founded' => 2010, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100 (a lower-deposit Cent account is also offered)', 'spread_eurusd' => 1.2, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'FSCA'), 'other_reg_count' => 2, 'instruments' => '500+ instruments across roughly 6 asset classes: shares, indices, forex (80+ pairs), futures, metals and commodities', 'blurb' => 'IronFX/FXLift is a trade name of the Notesco group, which is dual-regulated for EU clients under CySEC (Notesco Financial Services Ltd, 125/10) and for UK clients under the FCA, plus an FSCA licence in South Africa.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.5, 'platforms' => 2.6, 'track_record' => 2.06, 'overall' => 3.06 ), 'rank' => 33 ),
        array( 'slug' => 'amana-capital', 'name' => 'Amana Capital', 'entity' => 'Amana Capital Ltd', 'cysec' => '155/11', 'founded' => 2010, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50 (Classic account, approximate — some sources list no stated minimum)', 'spread_eurusd' => 1.4, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'DFSA'), 'other_reg_count' => 2, 'instruments' => '360+ instruments across forex (66 pairs), 330+ share CFDs, indices, metals, energies, commodities and crypto', 'blurb' => 'Cyprus entity of a broader group with UK (FCA) and Dubai (DFSA) licensed affiliates alongside its CySEC licence.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.43, 'platforms' => 2.6, 'track_record' => 2.06, 'overall' => 3.04 ), 'rank' => 34 ),
        array( 'slug' => 'libertex', 'name' => 'Libertex', 'entity' => 'Indication Investments Ltd', 'cysec' => '164/12', 'founded' => 2012, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100 (varies by region/promo, as low as $10 on some entities)', 'spread_eurusd' => 0.5, 'platforms' => array('MT4', 'MT5', 'Proprietary (Libertex)'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => 'Roughly 1,000+ CFDs and real-share offerings spanning forex, indices, commodities, ETFs, bonds and cryptocurrencies.', 'blurb' => 'Libertex is operated by Indication Investments Ltd (CySEC 164/12, founded as a brand in 2012) and offers MT4/MT5 alongside its own Libertex platform, with a separate group entity also holding an FSCA license in South Africa.', 'cysec_note' => 'FSCA held by a separate Libertex group entity, not Indication Investments Ltd. Group also holds offshore licenses (Mauritius FSC, Vanuatu VFSC) which are excluded per the approved regulator list. No FCA license; UK/AU/US clients not accepted.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.78, 'platforms' => 3.4, 'track_record' => 1.9, 'overall' => 3.03 ), 'rank' => 35 ),
        array( 'slug' => 'doto', 'name' => 'Doto', 'entity' => 'Doto Europe Ltd', 'cysec' => '399/21', 'founded' => 2019, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 15, 'min_deposit_display' => '$15', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5', 'Proprietary (Doto Platform)'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => 'Forex, indices, commodities and a selection of cryptocurrency CFDs', 'blurb' => 'Doto Europe Ltd (formerly Vasby Capital Markets Ltd) is one entity within a multi-jurisdiction group also comprising Doto Global Ltd (Mauritius), Doto International Ltd (Seychelles) and Doto South Africa (Pty) Ltd (FSCA); reported EUR/USD spreads vary by account type so no single figure could be confirmed.', 'cysec_note' => 'FSCA licence is held via a separate but same-brand entity, Doto South Africa (Pty) Ltd; Doto Global Ltd is also FSC-Mauritius licensed and Doto International Ltd FSA-Seychelles licensed (both excluded as non-Tier-1).', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.99, 'platforms' => 3.4, 'track_record' => 1.33, 'overall' => 2.98 ), 'rank' => 36 ),
        array( 'slug' => 'justmarkets', 'name' => 'JustMarkets', 'entity' => 'JustMarkets Ltd', 'cysec' => '401/21', 'founded' => 2012, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 10, 'min_deposit_display' => '$10', 'spread_eurusd' => 1, 'platforms' => array('MT4', 'MT5', 'Proprietary (JM Mobile Trading App)'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => 'Currency pairs, indices, commodities, share CFDs and cryptocurrencies', 'blurb' => 'JustMarkets rebranded from JustForex in September 2022 at the same time it launched its CySEC-licensed EU entity, and advertises a $10 minimum deposit on its Standard account versus $200 for Pro/Raw Spread accounts.', 'cysec_note' => 'FSCA registration is associated with the JustMarkets brand generally; not confirmed to be the same legal entity as CySEC-licensed JustMarkets Ltd.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.6, 'platforms' => 3.4, 'track_record' => 1.9, 'overall' => 2.98 ), 'rank' => 37 ),
        array( 'slug' => 'octa', 'name' => 'Octa', 'entity' => 'Octa Markets Cyprus Ltd', 'cysec' => '372/18', 'founded' => 2011, 'hq' => 'Nicosia, Cyprus (Octa Markets Cyprus Ltd, the CySEC entity); the global Octa brand\'s other entities are headquartered in Saint Lucia and St. Vincent & the Grenadines', 'min_deposit_usd' => 25, 'min_deposit_display' => '$25', 'spread_eurusd' => 0.6, 'platforms' => array('MT4', 'MT5', 'cTrader', 'Proprietary (OctaTrader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Octa (rebranded from OctaFX in late 2023) is a global CFD broker reporting over 13 million clients across 180+ countries, with its EU-facing operations regulated by CySEC as Octa Markets Cyprus Ltd; client funds there are covered up to €20,000 via the Investor Compensation Fund.', 'scores' => array( 'regulation' => 1, 'cost' => 4.76, 'platforms' => 4.2, 'track_record' => 1.98, 'overall' => 2.96 ), 'rank' => 38 ),
        array( 'slug' => 'tickmill', 'name' => 'Tickmill', 'entity' => 'Tickmill Europe Ltd', 'cysec' => '278/15', 'founded' => 2014, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.7, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'FSCA'), 'other_reg_count' => 2, 'instruments' => 'Forex, indices, commodities, bonds CFDs', 'blurb' => 'No account maintenance or inactivity fees on CySEC-regulated accounts; wider standard spreads than ECN-focused peers.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.3, 'platforms' => 2.6, 'track_record' => 1.73, 'overall' => 2.94 ), 'rank' => 39 ),
        array( 'slug' => 'tiomarkets', 'name' => 'TioMarkets', 'entity' => 'TioMarkets CY Ltd', 'cysec' => '429/23', 'founded' => 2018, 'hq' => null, 'min_deposit_usd' => 10, 'min_deposit_display' => '$10', 'spread_eurusd' => 1.2, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'FSCA'), 'other_reg_count' => 2, 'instruments' => '300+ CFDs across forex, indices, stocks, and commodities', 'blurb' => 'TioMarkets is known for a very low ($10) minimum deposit and dual FCA/CySEC regulation, though its Cyprus entity (TioMarkets CY Ltd) is reported to be under CySEC examination for voluntary licence renunciation, worth flagging prominently.', 'cysec_note' => 'CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.52, 'platforms' => 2.6, 'track_record' => 1.41, 'overall' => 2.94 ), 'rank' => 40 ),
        array( 'slug' => 'gbe-brokers', 'name' => 'GBE Brokers', 'entity' => 'GBE Brokers Ltd', 'cysec' => '240/14', 'founded' => 2014, 'hq' => 'Limassol, Cyprus (with a branch office in Hamburg, Germany)', 'min_deposit_usd' => 1080, 'min_deposit_display' => '€1,000 / $1,000 / CHF 1,000 (Classic account)', 'spread_eurusd' => 0.7, 'platforms' => array('MT4', 'MT5', 'TradingView'), 'other_reg' => array('BaFin'), 'other_reg_count' => 1, 'instruments' => 'Forex plus CFDs on indices, commodities and stocks (1,000+ instruments claimed via MT5)', 'blurb' => 'GBE Brokers is one of the few CySEC brokers also directly registered with Germany\'s BaFin for its Hamburg branch, and its entry-level Classic account has a comparatively high $1,000 minimum deposit.', 'cysec_note' => 'BaFin registration covers GBE Brokers\' Hamburg, Germany branch.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.52, 'platforms' => 3.4, 'track_record' => 1.73, 'overall' => 2.92 ), 'rank' => 41 ),
        array( 'slug' => 'fbs', 'name' => 'FBS', 'entity' => 'Tradestone Ltd', 'cysec' => '331/17', 'founded' => 2009, 'hq' => null, 'min_deposit_usd' => 11, 'min_deposit_display' => '€10', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5', 'Proprietary (FBS Trader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex, indices, commodities, stocks and crypto CFDs', 'blurb' => 'FBS\'s CySEC-licensed EU entity (Tradestone Ltd) markets a low minimum deposit of about €10 for European clients, distinct from the $1 cent-account minimum FBS advertises through its non-EU offshore entities.', 'scores' => array( 'regulation' => 1, 'cost' => 5, 'platforms' => 3.4, 'track_record' => 2.14, 'overall' => 2.91 ), 'rank' => 42 ),
        array( 'slug' => 'jfd-brokers', 'name' => 'JFD Brokers', 'entity' => 'JFD Group Ltd', 'cysec' => '150/11', 'founded' => 2011, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 540, 'min_deposit_display' => '€500 (or equivalent in USD/GBP/CHF)', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('BaFin'), 'other_reg_count' => 1, 'instruments' => null, 'blurb' => 'JFD Group Ltd requires one of the higher standard minimum deposits in this set (€500/$500/£500/CHF500) and is part of a group whose German affiliate, JFD Bank AG, separately holds a BaFin licence.', 'cysec_note' => 'The BaFin licence is held by the German affiliate JFD Bank AG, a separate legal entity from the CySEC-licensed JFD Group Ltd; JFD\'s earlier FCA passporting registration lapsed after Brexit.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.8, 'platforms' => 2.6, 'track_record' => 1.98, 'overall' => 2.9 ), 'rank' => 43 ),
        array( 'slug' => 'oneroyal', 'name' => 'OneRoyal', 'entity' => 'Royal Financial Trading (Cy) Ltd', 'cysec' => '312/16', 'founded' => 2006, 'hq' => 'Sydney, Australia (group); Cyprus entity based in Limassol', 'min_deposit_usd' => 5, 'min_deposit_display' => '$5 (Classic/ECN accounts); $5,000 for Prime account', 'spread_eurusd' => 1.4, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('ASIC'), 'other_reg_count' => 1, 'instruments' => 'Forex, indices, commodities and share CFDs; group marketing cites 2,000+ instruments across brands, though the EU-facing offering is narrower.', 'blurb' => 'OneRoyal (formerly branded Royal/FXGiants) traces to a 2006-founded Sydney group whose Australian ASIC licence sits in a different legal entity than its CySEC-regulated European arm, Royal Financial Trading (Cy) Ltd.', 'cysec_note' => 'ASIC licence is held by a separate group entity, Royal Financial Trading Pty Ltd (Australia), not by the CySEC-licensed Cyprus entity. The group also holds VFSC (Vanuatu) and FSA (St. Vincent) licences via other entities -- both offshore/non-Tier-1, so excluded.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.44, 'platforms' => 2.6, 'track_record' => 2.39, 'overall' => 2.87 ), 'rank' => 44 ),
        array( 'slug' => 'iqbroker', 'name' => 'IQBroker', 'entity' => 'IQBroker Europe Ltd', 'cysec' => '247/14', 'founded' => 2013, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 10, 'min_deposit_display' => '$10', 'spread_eurusd' => 1.2, 'platforms' => array('Proprietary (Quadcode Markets platform)'), 'other_reg' => array('ASIC', 'SCB'), 'other_reg_count' => 2, 'instruments' => 'Forex, commodities, stock indices and cryptocurrencies via CFDs', 'blurb' => 'IQBroker Europe Ltd is the CySEC entity behind the rebranded Quadcode Markets platform (formerly IQ Option Europe), and it uses an in-house proprietary trading platform rather than MetaTrader or cTrader.', 'cysec_note' => 'ASIC and SCB registrations belong to other Quadcode/IQ Option group entities operating under the same brand, not confirmed to be the same legal entity as the CySEC-licensed IQBroker Europe Ltd.', 'scores' => array( 'regulation' => 2.6, 'cost' => 4.52, 'platforms' => 1.8, 'track_record' => 1.82, 'overall' => 2.86 ), 'rank' => 45 ),
        array( 'slug' => 'colmex-pro', 'name' => 'Colmex Pro', 'entity' => 'Colmex Pro Ltd', 'cysec' => '—', 'founded' => 2010, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 500, 'min_deposit_display' => '$500', 'spread_eurusd' => 1.5, 'platforms' => array('MT4', 'MT5', 'Proprietary (Colmex Pro 2.0)', 'Proprietary (MultiTrader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '4,000+ CFDs and 3,000+ real US stocks with direct market access (NYSE, NASDAQ, AMEX), plus forex, indices and commodities CFDs', 'blurb' => 'Distinguished by offering direct market access to real US stocks (not just CFDs) alongside standard forex/CFD trading, via its own proprietary Colmex Pro 2.0 and MultiTrader platforms.', 'scores' => array( 'regulation' => 1, 'cost' => 4.31, 'platforms' => 4.2, 'track_record' => 2.06, 'overall' => 2.85 ), 'rank' => 46 ),
        array( 'slug' => 'skilling', 'name' => 'Skilling', 'entity' => 'Skilling Ltd', 'cysec' => '357/18', 'founded' => 2016, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '€100/$100/£100 (Standard account); €5,000/$5,000 for Premium account', 'spread_eurusd' => 0.8, 'platforms' => array('MT4', 'cTrader', 'TradingView', 'Proprietary (Skilling Trader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '900-1,200+ CFDs across forex, indices, shares, commodities and crypto.', 'blurb' => 'Skilling, co-founded in 2016 by former IG/Nordea/Cinnober executives, was acquired by INFINOX in late 2025, after which the brand continued operating with access to the wider INFINOX group\'s technology and licences.', 'cysec_note' => 'Skilling also holds an FSA Seychelles licence via a separate Seychelles entity and an FCA UK branch authorisation obtained in 2020 -- not counted here since sourcing did not clearly confirm the FCA authorisation is held by the same legal entity as the CySEC licence. Skilling was acquired by INFINOX in late 2025.', 'scores' => array( 'regulation' => 1, 'cost' => 4.66, 'platforms' => 4.2, 'track_record' => 1.57, 'overall' => 2.85 ), 'rank' => 47 ),
        array( 'slug' => 'fxview', 'name' => 'FXView', 'entity' => 'Charlgate Ltd', 'cysec' => '367/18', 'founded' => 2018, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50 (non-EU); €200 (EU clients), RAW ECN account', 'spread_eurusd' => 0, 'platforms' => array('MT5', 'ActTrader'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => '500+ CFDs across forex, indices, commodities, 300+ share CFDs and cryptocurrencies', 'blurb' => 'Runs an ECN-only model (no MT4, only MT5 and ActTrader) with spreads from 0.0 pips plus commission, and recently added a full FSCA (South Africa) licence, though its earlier UK FCA licence has been revoked.', 'cysec_note' => 'Previously held a UK FCA licence (850138) which has since been revoked, so FCA is not listed as a current licence.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.99, 'platforms' => 2.6, 'track_record' => 1.41, 'overall' => 2.84 ), 'rank' => 48 ),
        array( 'slug' => 'naga', 'name' => 'NAGA', 'entity' => 'Naga Markets Europe Ltd', 'cysec' => '204/13', 'founded' => 2015, 'hq' => 'Hamburg, Germany (The NAGA Group AG, parent, listed on Frankfurt Stock Exchange as N4G0); Naga Markets Europe Ltd is the CySEC-regulated Cyprus entity', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 1.7, 'platforms' => array('MT4', 'MT5', 'Proprietary (NAGA Trader)'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => '4,000+ tradable assets across forex, stocks, ETFs, commodities, indices and crypto, with built-in social/copy trading (NAGA Autocopy)', 'blurb' => 'NAGA is the retail trading brand of Hamburg-based, Frankfurt Stock Exchange-listed The NAGA Group AG, built around integrated social and copy-trading features (NAGA Autocopy) alongside MT4/MT5.', 'cysec_note' => 'The wider NAGA brand also operates via other legal entities holding ADGM and Seychelles FSA licences for non-EU clients; separate companies from the CySEC-licensed Naga Markets Europe Ltd named here.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.31, 'platforms' => 3.4, 'track_record' => 1.65, 'overall' => 2.84 ), 'rank' => 49 ),
        array( 'slug' => 'fxcc', 'name' => 'FXCC', 'entity' => 'FX Central Clearing Ltd', 'cysec' => '121/10', 'founded' => 2010, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 0, 'min_deposit_display' => 'No minimum', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex, commodities and CFDs via STP/ECN execution', 'blurb' => 'One of the longer-established brokers in this group (founded 2010), FXCC runs its CySEC-regulated entity alongside a separate offshore entity licensed in Comoros, with only the CySEC entity segregating client funds.', 'cysec_note' => 'FXCC also operates a separate Comoros entity licensed by MISA (Mwali International Services Authorities, licence BFX2024085); Comoros is an offshore registry, not counted on the Tier-1 list, and per FXCC\'s own disclosures only the CySEC entity segregates client funds.', 'scores' => array( 'regulation' => 1, 'cost' => 5, 'platforms' => 2.6, 'track_record' => 2.06, 'overall' => 2.73 ), 'rank' => 50 ),
        array( 'slug' => 'fxprimus', 'name' => 'FXPrimus', 'entity' => 'Primus Global Ltd', 'cysec' => '261/14', 'founded' => 2009, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 15, 'min_deposit_display' => '$15 (PrimusCent); $1,000 (PrimusZERO)', 'spread_eurusd' => 1.5, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '120-140+ CFDs spanning forex majors/minors/exotics, indices, precious metals, energies, global equities/ETFs and a handful of crypto pairs.', 'blurb' => 'FXPrimus, founded in 2009 and based in Limassol, is regulated in the EU via Primus Global Ltd (CySEC 261/14) and offers MT4, MT5 and cTrader, with no currently confirmed Tier-1 regulator beyond CySEC.', 'cysec_note' => 'International entity (Primus Markets Intl Ltd) holds a Vanuatu VFSC license, excluded per the approved regulator list. Some sources report a prior FSCA (South Africa) license as revoked/no longer active, so it was not counted as currently held.', 'scores' => array( 'regulation' => 1, 'cost' => 4.4, 'platforms' => 3.4, 'track_record' => 2.14, 'overall' => 2.73 ), 'rank' => 51 ),
        array( 'slug' => 'squaredfinancial', 'name' => 'SquaredFinancial', 'entity' => 'Squared Financial (CY) Ltd', 'cysec' => '329/17', 'founded' => 2005, 'hq' => 'Nicosia/Limassol, Cyprus', 'min_deposit_usd' => 0, 'min_deposit_display' => '$0 (SquaredPro); $500-$5,000 (Elite account, figures vary by source)', 'spread_eurusd' => 1.2, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '2,000+ CFDs across forex, indices, shares, ETFs and commodities.', 'blurb' => 'Squared Financial (CY) Ltd formally began a voluntary, structured wind-down and surrender of its CySEC licence (329/17) in 2025 -- including a stop to onboarding new clients -- after CySEC had previously fined the firm EUR35,000 over CFD marketing practices to retail clients, and after reports of frozen partner funds.', 'cysec_note' => 'This entity began a voluntary, structured wind-down and surrender of its CySEC licence in 2025 and has reportedly stopped onboarding new clients, following a prior CySEC fine over CFD marketing practices and reports of frozen partner funds. CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it. Group also operates an offshore Seychelles entity, not Tier-1 and a separate legal entity from the CySEC one.', 'scores' => array( 'regulation' => 1, 'cost' => 4.52, 'platforms' => 2.6, 'track_record' => 2.47, 'overall' => 2.67 ), 'rank' => 52 ),
        array( 'slug' => 'capex-com', 'name' => 'CAPEX.com', 'entity' => 'Key Way Investments Ltd', 'cysec' => '292/16', 'founded' => 2016, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.8, 'platforms' => array('MT5', 'Proprietary (CAPEX WebTrader)'), 'other_reg' => array('FSCA'), 'other_reg_count' => 1, 'instruments' => 'Marketed as 5,000+ tradeable assets, including real stocks/ETFs alongside CFDs on forex, indices and commodities', 'blurb' => 'CAPEX.com (Key Way Investments Ltd) is one of the few brokers in this set marketing direct stock/ETF investing alongside CFDs, with a fixed EUR/USD spread of about 1.8 pips via its own WebTrader platform or MT5; CySEC also reached a roughly EUR120k settlement with Key Way Investments over past compliance issues.', 'cysec_note' => 'FSCA regulation applies to a separate group entity operating under the same CAPEX brand, plus non-Tier-1 licences (ADGM UAE, Seychelles FSA) for other group entities -- not the CySEC entity Key Way Investments Ltd itself.', 'scores' => array( 'regulation' => 1.8, 'cost' => 4.26, 'platforms' => 2.6, 'track_record' => 1.57, 'overall' => 2.65 ), 'rank' => 53 ),
        array( 'slug' => 'finansero', 'name' => 'Finansero', 'entity' => 'Global Trade CIF Ltd', 'cysec' => '190/13', 'founded' => 2012, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 200, 'min_deposit_display' => '$200 / €200', 'spread_eurusd' => 0.6, 'platforms' => array('MT4', 'Proprietary (XCITE)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '300+ assets across forex, indices, commodities and shares CFDs', 'blurb' => 'Finansero (Global Trade CIF Ltd) is headquartered in Nicosia rather than the more common Limassol, and offers its own proprietary XCITE platform alongside MT4, with a 9-tier retail account structure based on funding/volume.', 'scores' => array( 'regulation' => 1, 'cost' => 4.72, 'platforms' => 2.6, 'track_record' => 1.9, 'overall' => 2.62 ), 'rank' => 54 ),
        array( 'slug' => 'finalto', 'name' => 'Finalto', 'entity' => 'Finalto EU Ltd', 'cysec' => '264/15', 'founded' => null, 'hq' => 'London, UK (Finalto EU Ltd\'s registered office is in Nicosia, Cyprus)', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'ASIC'), 'other_reg_count' => 2, 'instruments' => 'Liquidity, prime-of-prime and white-label solutions across forex and CFDs on indices, commodities and equities, aimed at B2B/institutional clients rather than direct retail sign-ups', 'blurb' => 'Finalto operates primarily as a B2B liquidity and prime-of-prime provider rather than a direct-to-retail sign-up broker, with offices across London, Cyprus, Dubai, Singapore and Sydney and no publicly disclosed retail minimum deposit or spread figures.', 'scores' => array( 'regulation' => 2.6, 'cost' => null, 'platforms' => 2.6, 'track_record' => null, 'overall' => 2.6 ), 'rank' => 55 ),
        array( 'slug' => 'trading-212', 'name' => 'Trading 212', 'entity' => 'Trading 212 Markets Ltd', 'cysec' => '398/21', 'founded' => 2004, 'hq' => 'London, UK', 'min_deposit_usd' => 1.1, 'min_deposit_display' => '€1', 'spread_eurusd' => 2.7, 'platforms' => array('Proprietary'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => 'Forex, stocks, ETFs, commodities, indices, crypto CFDs', 'blurb' => 'Very low minimum deposit and a well-regarded no-frills app, but wider EUR/USD spreads and only one platform option.', 'scores' => array( 'regulation' => 1.8, 'cost' => 3.92, 'platforms' => 1.8, 'track_record' => 2.55, 'overall' => 2.59 ), 'rank' => 56 ),
        array( 'slug' => 'purple-trading', 'name' => 'Purple Trading', 'entity' => 'L.F. Investment Ltd', 'cysec' => '271/15', 'founded' => 2014, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => 1.3, 'platforms' => array('MT4', 'cTrader'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => 'CFDs on forex and commodities (fuller instrument range not independently confirmed)', 'blurb' => 'Purple Trading (L.F. Investment Ltd) reportedly paid CySEC roughly EUR150,000 to settle allegations of lapses in its retail CFD offering, according to Finance Magnates reporting; minimum deposit figures for its standard/STP account conflict across sources so are left unreported here.', 'cysec_note' => 'A UK FCA authorization number is cited by third-party sources; could not independently confirm whether this is a full permission or a passporting-era registration.', 'scores' => array( 'regulation' => 1.8, 'cost' => 3.96, 'platforms' => 2.6, 'track_record' => 1.73, 'overall' => 2.59 ), 'rank' => 57 ),
        array( 'slug' => 'fxoro', 'name' => 'FXORO', 'entity' => 'MCA Intelifunds Ltd', 'cysec' => '126/10', 'founded' => null, 'hq' => 'Limassol, Cyprus (Petrou Tsirou 82, Mesa Geitonia, 3076 Limassol)', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 2, 'platforms' => array('MT4', 'Proprietary (FXORO App)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'MCA Intelifunds Ltd (FXORO) is in the process of voluntarily renouncing CySEC licence 126/10 and has stated it will cease providing investment and ancillary services effective 24 October 2025.', 'cysec_note' => 'CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it.', 'scores' => array( 'regulation' => 1, 'cost' => 4.18, 'platforms' => 2.6, 'track_record' => null, 'overall' => 2.59 ), 'rank' => 58 ),
        array( 'slug' => 'iforex-europe', 'name' => 'iFOREX Europe', 'entity' => 'ICFD Ltd', 'cysec' => '143/11', 'founded' => 1996, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1.8, 'platforms' => array('Proprietary (iFOREX/FXnet platform)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '750+ CFD instruments including ~90 forex pairs, stock CFDs, commodities, indices, ETFs and cryptocurrencies.', 'blurb' => 'iFOREX is a long-running CFD brand dating to 1996 whose EU-facing entity, ICFD Ltd, has been CySEC-licensed since 2011 and trades through a proprietary platform rather than MetaTrader.', 'cysec_note' => 'No Tier-1/well-regarded regulator confirmed for ICFD Ltd itself; a BVI FSC license exists but belongs to a separate group entity (Formula Investment House Ltd), and BVI is not on the approved regulator list.', 'scores' => array( 'regulation' => 1, 'cost' => 4.26, 'platforms' => 1.8, 'track_record' => 3.2, 'overall' => 2.58 ), 'rank' => 59 ),
        array( 'slug' => 'errante', 'name' => 'Errante', 'entity' => 'Notely Trading Ltd', 'cysec' => '—', 'founded' => 2018, 'hq' => 'Nicosia, Cyprus (registered office: 30 Karpenisiou, 1077 Nicosia)', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 1.5, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '350+ instruments including roughly 55 forex pairs, 200+ stock CFDs, ~30 cryptocurrencies, plus indices and commodities', 'blurb' => 'Errante is a Cyprus-based broker established around 2018 that runs a separate, non-CySEC entity (Errante Securities (Seychelles) Ltd) for clients outside the EU/EEA.', 'scores' => array( 'regulation' => 1, 'cost' => 4.39, 'platforms' => 3.4, 'track_record' => 1.41, 'overall' => 2.58 ), 'rank' => 60 ),
        array( 'slug' => 'liteforex', 'name' => 'LiteForex', 'entity' => 'Liteforex (Europe) Ltd', 'cysec' => '093/08', 'founded' => 2005, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 2, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'LiteForex says it pioneered cent trading accounts with deposits as low as $1 for its international brand, but its CySEC-regulated Cyprus entity\'s Classic account requires a $50 minimum deposit with spreads from 2 pips.', 'cysec_note' => 'LiteForex/LiteFinance\'s other registered entities (Marshall Islands, St Vincent and the Grenadines) are not Tier-1 regulators.', 'scores' => array( 'regulation' => 1, 'cost' => 4.19, 'platforms' => 2.6, 'track_record' => 2.47, 'overall' => 2.57 ), 'rank' => 61 ),
        array( 'slug' => 'scope-markets', 'name' => 'Scope Markets', 'entity' => 'SM Capital Markets Ltd', 'cysec' => '339/17', 'founded' => 2014, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 200, 'min_deposit_display' => '$200 (EU); Standard account cited elsewhere at $500', 'spread_eurusd' => 0.7, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Marketing cites up to 40,000 instruments group-wide across forex (70+ pairs), indices, shares (2,000+), energies, metals and commodities, via a network of separately regulated regional entities.', 'blurb' => 'Scope Markets is a group brand where the CySEC-licensed EU entity (SM Capital Markets Ltd) is legally separate from its South African (FSCA) and Kenyan (CMA) sister entities and from an offshore Belize entity.', 'cysec_note' => 'The Scope Markets group holds FSCA (South Africa) and CMA Kenya authorisations, but these are held by separate group entities (Scope Markets SA (Pty) Ltd and SCFM Ltd respectively), not by SM Capital Markets Ltd (the CySEC entity). The group\'s Belize entity and Seychelles FSA licence are offshore/non-Tier-1 and excluded.', 'scores' => array( 'regulation' => 1, 'cost' => 4.68, 'platforms' => 2.6, 'track_record' => 1.73, 'overall' => 2.57 ), 'rank' => 62 ),
        array( 'slug' => 'gate-securities', 'name' => 'Gate Securities', 'entity' => 'Gate Securities (Cyprus) Ltd', 'cysec' => '395/20', 'founded' => 2019, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 200, 'min_deposit_display' => '€200/$200', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'CFDs on forex, EMFX (emerging-market currency pairs), NDFs, commodities, equities, indices and cryptocurrencies', 'blurb' => 'Gate Securities (Cyprus) Ltd was previously licensed as Sheer Markets (Cyprus) Ltd (est. 2019) before crypto exchange Gate.io acquired it and rebranded the CySEC entity as Gate Securities, and it markets a comparatively unusual NDF/EMFX CFD product line alongside standard forex and CFDs.', 'scores' => array( 'regulation' => 1, 'cost' => 4.93, 'platforms' => 2.6, 'track_record' => 1.33, 'overall' => 2.56 ), 'rank' => 63 ),
        array( 'slug' => 'lmax', 'name' => 'LMAX', 'entity' => 'LMAX Broker Europe Ltd', 'cysec' => '310/16', 'founded' => 2010, 'hq' => 'London, United Kingdom (LMAX Group parent, set up by Betfair/Goldman Sachs alumni); LMAX Broker Europe Ltd is the CySEC-licensed Cyprus entity serving EU retail clients', 'min_deposit_usd' => 10000, 'min_deposit_display' => '$10,000', 'spread_eurusd' => 0.3, 'platforms' => array('MT4', 'MT5', 'Proprietary (LMAX Trader)'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => null, 'blurb' => 'LMAX is a multilateral trading facility built in 2010 by Betfair and Goldman Sachs alumni that offers institutional-grade, no-dealing-desk execution, and is dual-regulated by the FCA (UK) and CySEC (Cyprus).', 'scores' => array( 'regulation' => 1.8, 'cost' => 3.03, 'platforms' => 3.4, 'track_record' => 2.06, 'overall' => 2.54 ), 'rank' => 64 ),
        array( 'slug' => 'triomarkets', 'name' => 'TrioMarkets', 'entity' => 'EDR Financial Ltd', 'cysec' => '—', 'founded' => 2014, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 2.4, 'platforms' => array('MT4', 'MT5', 'Proprietary (TrioTrader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Founded in 2014 by the Ghrenassia family (French-Israeli financial professionals), TrioMarkets more recently launched an in-house proprietary-trading arm, TrioFunded.', 'cysec_note' => 'Also regulated by the Financial Services Commission of Mauritius (excluded as non-Tier-1).', 'scores' => array( 'regulation' => 1, 'cost' => 4.02, 'platforms' => 3.4, 'track_record' => 1.73, 'overall' => 2.53 ), 'rank' => 65 ),
        array( 'slug' => 'orbex', 'name' => 'Orbex', 'entity' => 'ORBEX Ltd', 'cysec' => '124/10', 'founded' => 2011, 'hq' => 'Limassol, Cyprus (historically; EU operations wound down)', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100 (Starter account)', 'spread_eurusd' => 1.5, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex majors/minors plus CFDs on metals, indices and other assets (pre-exit product range); current offshore-entity offering not independently verified.', 'blurb' => 'Orbex has surrendered its CySEC license and ceased EU operations as of July 15, 2025 after roughly 14 years operating from Cyprus, now trading exclusively through offshore entities -- consistent with the \'under examination for voluntary renunciation\' status on file.', 'cysec_note' => 'Orbex surrendered its CySEC licence and ceased serving EU/EEA clients as of 15 July 2025; this review is kept for reference only and the broker should not be treated as a current option for EU-based traders. CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it. No Tier-1 regulator held. Following the CySEC exit, Orbex operates only through offshore entities (Mauritius, Seychelles, St Vincent & Grenadines), all excluded per the approved list.', 'scores' => array( 'regulation' => 1, 'cost' => 4.38, 'platforms' => 2.6, 'track_record' => 1.98, 'overall' => 2.53 ), 'rank' => 66 ),
        array( 'slug' => 'ems-brokers', 'name' => 'EMS Brokers', 'entity' => 'Fxnet Ltd', 'cysec' => '182/12', 'founded' => 2012, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 216, 'min_deposit_display' => '€200', 'spread_eurusd' => null, 'platforms' => array('MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex and CFDs, traded exclusively through MetaTrader 5 (no MT4 offering found)', 'blurb' => 'FXNET Limited, EMS Brokers\' operator, reached a EUR225,000 settlement with CySEC (disclosed November 2025) over 2021-2022 conduct failings including product governance, record-keeping, and CFD marketing-restriction compliance.', 'scores' => array( 'regulation' => 1, 'cost' => 4.92, 'platforms' => 1.8, 'track_record' => 1.9, 'overall' => 2.52 ), 'rank' => 67 ),
        array( 'slug' => 'forextb', 'name' => 'ForexTB', 'entity' => 'Forex TB Ltd', 'cysec' => '272/15', 'founded' => 2012, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 270, 'min_deposit_display' => 'EUR250 (Basic account)', 'spread_eurusd' => null, 'platforms' => array('MT4'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex pairs, indices, commodities, equities and cryptocurrency CFDs', 'blurb' => 'ForexTB (Forex TB Ltd) paid roughly EUR270,000 to settle a CySEC regulatory investigation and its 272/15 licence is now listed as under examination for voluntary renunciation.', 'cysec_note' => 'CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it.', 'scores' => array( 'regulation' => 1, 'cost' => 4.9, 'platforms' => 1.8, 'track_record' => 1.9, 'overall' => 2.51 ), 'rank' => 68 ),
        array( 'slug' => 'key-to-trading', 'name' => 'Key to Trading', 'entity' => 'Kleis EU LTD', 'cysec' => '436/23', 'founded' => 2023, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 108, 'min_deposit_display' => '€100', 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex, commodities, shares and indices CFDs', 'blurb' => 'Key to Trading (Kleis EU LTD) is the most recently licensed CySEC broker in this set, receiving licence 436/23 on 4 September 2023.', 'scores' => array( 'regulation' => 1, 'cost' => 4.96, 'platforms' => 2.6, 'track_record' => 1, 'overall' => 2.51 ), 'rank' => 69 ),
        array( 'slug' => 'equiti', 'name' => 'Equiti', 'entity' => 'Equiti Global Markets Ltd', 'cysec' => '415/22', 'founded' => 2008, 'hq' => 'London, UK', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array('FCA', 'CMA'), 'other_reg_count' => 2, 'instruments' => 'Forex, indices, commodities and share CFDs', 'blurb' => 'Equiti Capital was formerly Divisa Capital, founded in London in 2008, and the group rebranded to Equiti in 2016-2018 before adding its CySEC-licensed Cyprus entity (Equiti Global Markets Ltd) in 2022.', 'cysec_note' => 'Group entities also hold licences from Jordan\'s JSC and the UAE\'s SCA, neither on the Tier-1 list. Minimum deposit and spread figures vary noticeably by Equiti Group entity/region, so no single figure could be confirmed specifically for the Cyprus entity.', 'scores' => array( 'regulation' => 2.6, 'cost' => null, 'platforms' => 2.6, 'track_record' => 2.22, 'overall' => 2.49 ), 'rank' => 70 ),
        array( 'slug' => 'trust-capital', 'name' => 'Trust Capital', 'entity' => 'Trust Capital TC Ltd', 'cysec' => '369/18', 'founded' => null, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 250, 'min_deposit_display' => '$250', 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Trust Capital TC Ltd is Limassol-based and also operates a Seychelles-licensed subsidiary for clients outside the EU; founding year was inconsistent across sources so was left null, and platform/instrument details could not be confirmed.', 'scores' => array( 'regulation' => 1, 'cost' => 4.91, 'platforms' => 1, 'track_record' => null, 'overall' => 2.47 ), 'rank' => 71 ),
        array( 'slug' => 'xtrend', 'name' => 'XTrend', 'entity' => 'Rynat Trading Ltd', 'cysec' => '—', 'founded' => 2016, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50', 'spread_eurusd' => 0.2, 'platforms' => array('Proprietary (XTrend)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '100+ CFDs across forex (60+ pairs), metals, energies, stocks, indices and 26+ cryptocurrencies.', 'blurb' => 'XTrend relies solely on Rynat Trading Ltd\'s own proprietary web/app platform (no MT4/MT5) and, unusually, offers no demo account, requiring a funded live account to trade.', 'scores' => array( 'regulation' => 1, 'cost' => 4.91, 'platforms' => 1.8, 'track_record' => 1.57, 'overall' => 2.45 ), 'rank' => 72 ),
        array( 'slug' => 'lirunex', 'name' => 'Lirunex', 'entity' => 'Lirunex Ltd', 'cysec' => '338/17', 'founded' => 2017, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => 1.5, 'platforms' => array('MT4', 'MT5', 'Proprietary (Lirunex Trading App)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Lirunex Ltd holds a CySEC cross-border licence (338/17) alongside non-Tier-1 registrations in Mauritius and Labuan; sources give conflicting minimum-deposit figures, so no single number could be confidently confirmed.', 'cysec_note' => 'Lirunex also holds registrations with the Mauritius FSC and Labuan FSA, neither a Tier-1 regulator, so excluded.', 'scores' => array( 'regulation' => 1, 'cost' => 3.8, 'platforms' => 3.4, 'track_record' => 1.49, 'overall' => 2.42 ), 'rank' => 73 ),
        array( 'slug' => 'instaforex', 'name' => 'InstaForex', 'entity' => 'Instant Trading EU Ltd', 'cysec' => '266/15', 'founded' => 2007, 'hq' => 'Mesa Geitonia (Limassol), Cyprus', 'min_deposit_usd' => 215, 'min_deposit_display' => '€200', 'spread_eurusd' => 3, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex majors plus CFDs on BTC, ETH and XRP marketed as zero/commission-free spread products', 'blurb' => 'Instant Trading EU Ltd operates the InstaForex EU brand, offering a commission-free account with a fixed 3-pip EUR/USD spread and a €200 minimum deposit, distinct from the much lower-deposit InstaForex accounts marketed outside the EU.', 'scores' => array( 'regulation' => 1, 'cost' => 3.76, 'platforms' => 2.6, 'track_record' => 2.31, 'overall' => 2.41 ), 'rank' => 74 ),
        array( 'slug' => 'oqtima', 'name' => 'OQtima', 'entity' => 'Oqtima EU Ltd', 'cysec' => '406/21', 'founded' => 2022, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 100, 'min_deposit_display' => '$100', 'spread_eurusd' => 1, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '940+ CFDs covering forex, indices, commodities (metals/energy), share CFDs, cryptocurrencies and ETFs.', 'blurb' => 'OQtima is a young Cyprus-based broker (CySEC entity established around 2022) founded by former IC Markets/TMGM industry professionals, offering MT4/MT5 with no additional Tier-1 regulation confirmed.', 'cysec_note' => 'OQtima\'s only other regulated entity found (OQTIMA INT. LTD) is licensed by the Seychelles FSA, which is explicitly excluded per the approved regulator list.', 'scores' => array( 'regulation' => 1, 'cost' => 4.58, 'platforms' => 2.6, 'track_record' => 1.08, 'overall' => 2.41 ), 'rank' => 75 ),
        array( 'slug' => 'exca-prime', 'name' => 'EXCA Prime', 'entity' => 'Exclusive Change Capital Ltd', 'cysec' => '330/17', 'founded' => 2014, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 1080, 'min_deposit_display' => '€1,000', 'spread_eurusd' => null, 'platforms' => array('MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'CFDs on forex and shares across Standard, Exclusive and Shares account tiers (Shares account requires €10,000 minimum, leverage capped at 1:1)', 'blurb' => 'Exclusive Change Capital Ltd was registered in Cyprus in 2014 and traded for years as \'Exclusive Capital\' before rebranding to EXCA Prime, while keeping the same CySEC licence number (330/17, issued 8 August 2017).', 'scores' => array( 'regulation' => 1, 'cost' => 4.6, 'platforms' => 1.8, 'track_record' => 1.73, 'overall' => 2.39 ), 'rank' => 76 ),
        array( 'slug' => 'emporium-capital', 'name' => 'Emporium Capital', 'entity' => 'Emporium Capital K.A Ltd', 'cysec' => '358/18', 'founded' => 2017, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => 1000, 'min_deposit_display' => '$1,000', 'spread_eurusd' => null, 'platforms' => array('MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex and multi-asset CFDs (Stocks/Equity Indices) via a True STP execution model', 'blurb' => 'Trading as ECG Prime, Emporium Capital K.A Ltd positions itself as a boutique \'True STP\' broker offering portfolio-management services alongside standard CFD trading, with a Retail account minimum of $1,000 versus $50,000 for its Professional account tier.', 'scores' => array( 'regulation' => 1, 'cost' => 4.63, 'platforms' => 1.8, 'track_record' => 1.49, 'overall' => 2.35 ), 'rank' => 77 ),
        array( 'slug' => '1market', 'name' => '1Market', 'entity' => 'Exelcius Prime Ltd', 'cysec' => '366/18', 'founded' => 2021, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 500, 'min_deposit_display' => '$500', 'spread_eurusd' => null, 'platforms' => array('MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'CFDs on forex, shares, commodities and indices', 'blurb' => 'CySEC fined parent company Exelcius Prime Ltd EUR740,000 for multiple regulatory breaches and took enforcement action against former directors, and the 366/18 licence is currently listed as under examination for voluntary renunciation.', 'cysec_note' => 'CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it.', 'scores' => array( 'regulation' => 1, 'cost' => 4.81, 'platforms' => 1.8, 'track_record' => 1.16, 'overall' => 2.34 ), 'rank' => 78 ),
        array( 'slug' => 'finpros', 'name' => 'FinPros', 'entity' => 'Finquotes Financial (Cyprus) Ltd', 'cysec' => '418/22', 'founded' => 2020, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 50, 'min_deposit_display' => '$50 (ClassiQ account; $10 Cent account, $500 Pro, $20,000 Raw+)', 'spread_eurusd' => 1.5, 'platforms' => array('MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '400+ instruments across forex, crypto, indices, metals, energies and global stocks', 'blurb' => 'FinPros runs a tiered account structure from a $10 Cent account up to a $20,000 Raw+ account, and is dual-regulated by CySEC in Cyprus and the FSA in Seychelles.', 'cysec_note' => 'FinPros also holds a Securities Dealer licence (SD087) from the Seychelles FSA, an offshore regulator not counted on the Tier-1 list.', 'scores' => array( 'regulation' => 1, 'cost' => 4.39, 'platforms' => 1.8, 'track_record' => 1.24, 'overall' => 2.23 ), 'rank' => 79 ),
        array( 'slug' => 'trade360', 'name' => 'Trade360', 'entity' => 'Crowd Tech Ltd', 'cysec' => '—', 'founded' => 2013, 'hq' => null, 'min_deposit_usd' => 450, 'min_deposit_display' => '$450', 'spread_eurusd' => 4, 'platforms' => array('MT5', 'Proprietary (CrowdTrading/WebTrader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Multiple independent sources report that Crowd Tech Ltd voluntarily renounced its CySEC licence and ceased onboarding new EU clients in 2023 after a EUR230,000 CySEC settlement, so this broker\'s current operating/regulatory status is uncertain and should be independently reconfirmed.', 'cysec_note' => 'Multiple independent reports indicate this entity (Crowd Tech Ltd) voluntarily renounced its CySEC authorisation and stopped onboarding new EU clients in 2023 after a regulatory settlement -- current operating status is unclear and should be independently reconfirmed before relying on this broker. Historically also held an ASIC licence in Australia (via Sirius Financial Markets Pty), which was reportedly surrendered; Crowd Tech Ltd is also reported to have renounced/be renouncing its CySEC investment-firm authorisation and stopped taking new EU clients as of 2023 -- current live status is unclear and should be re-verified directly before publishing.', 'scores' => array( 'regulation' => 1, 'cost' => 3.32, 'platforms' => 2.6, 'track_record' => 1.82, 'overall' => 2.18 ), 'rank' => 80 ),
        array( 'slug' => 'exante', 'name' => 'EXANTE', 'entity' => 'EXT Ltd', 'cysec' => '165/12', 'founded' => 2011, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => 10800, 'min_deposit_display' => 'EUR10,000 (individual clients; EUR50,000 for corporate clients)', 'spread_eurusd' => 0.3, 'platforms' => array('Proprietary (EXANTE Terminal)'), 'other_reg' => array('FCA'), 'other_reg_count' => 1, 'instruments' => 'Multi-asset direct-market-access broker: stocks, ETFs, bonds, futures, options, forex and crypto across 50+ global exchanges', 'blurb' => 'EXANTE operates as a direct-market-access multi-asset broker rather than a typical spread-based retail FX broker, requiring a EUR10,000 minimum deposit and charging via commissions (FX spreads from about 0.3 pips) rather than marked-up spreads.', 'cysec_note' => 'Sources also cite an SFC (Hong Kong) licence and an MFSA (Malta) licence for other EXANTE group entities; not confirmed as held by EXT Ltd specifically, so not counted toward CySEC-entity regulation.', 'scores' => array( 'regulation' => 1.8, 'cost' => 2.88, 'platforms' => 1.8, 'track_record' => 1.98, 'overall' => 2.16 ), 'rank' => 81 ),
        array( 'slug' => 'oexn', 'name' => 'OEXN', 'entity' => 'OEXN Limited', 'cysec' => '423/22', 'founded' => 2023, 'hq' => 'Limassol, Cyprus (106 Gladstonos, Limassol, 3032)', 'min_deposit_usd' => 250, 'min_deposit_display' => '$250 / €250', 'spread_eurusd' => 5, 'platforms' => array('MT4', 'MT5', 'Proprietary (Multi-Asset Platform)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'OEXN is a newer CySEC-licensed broker (licence 423/22, firm founded 2023) that markets a wide standard-account EUR/USD spread (around 5 pips) alongside a separate VIP Raw ECN account advertised from 0.0 pips plus a $4/lot commission.', 'scores' => array( 'regulation' => 1, 'cost' => 2.95, 'platforms' => 3.4, 'track_record' => 1, 'overall' => 2.07 ), 'rank' => 82 ),
        array( 'slug' => 'b2prime', 'name' => 'B2Prime', 'entity' => 'B2B Prime Services EU Ltd', 'cysec' => '370/18', 'founded' => null, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5', 'cTrader'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Multi-asset CFD liquidity across forex, metals, commodities, shares, indices and cryptocurrencies, aimed at institutional/professional clients', 'blurb' => 'Positions itself as a Prime-of-Prime multi-asset liquidity provider for institutional and professional clients rather than a conventional retail broker, so standard retail deposit/spread figures do not apply.', 'cysec_note' => 'Also holds Seychelles FSA and Mauritius FSC licences, both excluded here as offshore registries rather than Tier-1 regulators.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 3.4, 'track_record' => null, 'overall' => 1.96 ), 'rank' => 83 ),
        array( 'slug' => 'robomarkets', 'name' => 'RoboMarkets', 'entity' => 'Robomarkets Ltd', 'cysec' => '191/13', 'founded' => 2012, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5', 'Proprietary (R StocksTrader)'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Historically forex, indices, commodities and crypto CFDs plus 8,000+ stock/ETF CFDs; however since 1 January 2025 Robomarkets Ltd (the CySEC entity) stopped serving EU retail clients for FX/CFDs and became an institutional-only broker, with EU retail stock/ETF business moved to the group\'s German (BaFin) entity.', 'blurb' => 'As of January 2025 the CySEC-licensed entity, Robomarkets Ltd, exited retail FX/CFD business entirely and now serves only institutional clients, with EU retail clients redirected to the group\'s German BaFin-regulated entity for stocks/ETFs.', 'cysec_note' => 'As of 1 January 2025 this CySEC entity stopped serving EU retail clients for forex/CFDs and now operates as an institutional-only broker; EU retail stock/ETF business moved to the group\'s separate German (BaFin-regulated) entity -- this is not a broker EU retail traders can currently open an account with. The wider RoboMarkets/RoboForex group also has a BaFin-licensed German entity and a Belize IFSC-licensed entity, both separate legal entities from Robomarkets Ltd (CySEC 191/13).', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 3.4, 'track_record' => 1.9, 'overall' => 1.94 ), 'rank' => 84 ),
        array( 'slug' => 'interstellar-fx', 'name' => 'Interstellar FX', 'entity' => 'The First Interstellar Capital Limited', 'cysec' => '166/12', 'founded' => 2012, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex majors, crosses and exotic pairs, plus spot gold and silver', 'blurb' => 'Interstellar FX (The First Interstellar Capital Limited) is a CySEC-licensed broker headquartered in Limassol offering MT4/MT5; minimum deposit and spread figures were inconsistent across secondary sources so were left unverified.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 2.6, 'track_record' => 1.9, 'overall' => 1.71 ), 'rank' => 85 ),
        array( 'slug' => 'doo-financial', 'name' => 'Doo Financial', 'entity' => 'Doo Financial Cyprus Limited', 'cysec' => '—', 'founded' => 2014, 'hq' => 'Singapore', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'CFDs on forex, commodities (gold, silver, crude oil), stocks and indices', 'blurb' => 'Doo Financial Cyprus Limited (incorporated 2022, CySEC licence obtained 2024) is the EU-regulated arm of Singapore-headquartered Doo Group, founded 2014, which is separately known internationally for its Doo Prime brand.', 'cysec_note' => 'The wider Doo Group (parent of the separate \'Doo Prime\' brand) is reported to hold FCA, FSCA and SCB (Bahamas) licences through other, distinct legal entities; these could not be confirmed as licences held specifically by Doo Financial Cyprus Limited itself.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 2.6, 'track_record' => 1.73, 'overall' => 1.67 ), 'rank' => 86 ),
        array( 'slug' => 'smartfx', 'name' => 'SmartFX', 'entity' => 'SSC Smart FX Ltd', 'cysec' => '316/16', 'founded' => 2016, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array('MT4', 'MT5'), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Forex, commodities and CFD trading, per company marketing; specific instrument count not independently confirmed.', 'blurb' => 'SSC Smart FX Ltd (CySEC 316/16) serves only professional and institutional clients, not retail traders, and should not be confused with the similarly-named retail brand \'SmartFX\' (smartfx.com), which is a separate, offshore Vanuatu-regulated entity.', 'cysec_note' => 'SSC Smart FX Ltd (the CySEC entity) does not offer services to retail clients -- its site targets professional/institutional clients only, so no public retail minimum deposit or spread figures could be confirmed. The similarly-named retail brand \'SmartFX\' at smartfx.com is a separate legal entity regulated only by the offshore Vanuatu FSC -- not to be confused with this CySEC entity.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 2.6, 'track_record' => 1.57, 'overall' => 1.62 ), 'rank' => 87 ),
        array( 'slug' => 'trade-com', 'name' => 'TRADE.com', 'entity' => 'Trade Capital Markets (TCM) Ltd', 'cysec' => '227/14', 'founded' => 2013, 'hq' => 'Nicosia, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Could not reliably verify operational details for Trade.com beyond its CySEC licence and Nicosia head office address -- search results conflated it with the similarly-named but separate broker \'Trading.com\', so figures were left null rather than risk misattribution.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 1, 'track_record' => 1.82, 'overall' => 1.23 ), 'rank' => 88 ),
        array( 'slug' => 'profitlevel', 'name' => 'ProfitLevel', 'entity' => 'BCM Begin Capital Markets CY Ltd', 'cysec' => '274/15', 'founded' => 2015, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'CySEC has settled with this operator multiple times over alleged compliance violations (including a reported EUR170,000 settlement), and public pricing/deposit figures for its ProfitLevel and CapitalPanda brands are inconsistent across sources -- treat with caution and verify directly before publishing.', 'cysec_note' => 'CySEC lists this licence as under examination for voluntary renunciation -- confirm current regulatory status directly with the broker and the CySEC register before relying on it.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 1, 'track_record' => 1.65, 'overall' => 1.19 ), 'rank' => 89 ),
        array( 'slug' => 'ozios', 'name' => 'OZIOS', 'entity' => 'APME FX Trading Europe Ltd', 'cysec' => '335/17', 'founded' => 2017, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'Licensed as a Forex Execution (STP) firm since 2017 under the CySEC-registered brand OZIOS; the \'Monestia\' brand name could not be independently verified from available sources and may be outdated or a naming error -- recommend confirming directly with the CySEC register entry before publishing.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 1, 'track_record' => 1.49, 'overall' => 1.14 ), 'rank' => 90 ),
        array( 'slug' => 'broctagon-prime', 'name' => 'Broctagon Prime', 'entity' => 'Broctagon Prime Ltd', 'cysec' => '320/17', 'founded' => 2017, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => 'Institutional multi-asset liquidity (spot FX, CFDs, commodities, crypto) supplied to other brokers and funds; not a direct-to-retail broker', 'blurb' => 'A business-to-business liquidity and technology provider, a subsidiary of Singapore\'s Broctagon Fintech Group, that supplies liquidity to at least six other CySEC-regulated brokers rather than serving retail clients directly -- retail account terms do not apply.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 1, 'track_record' => 1.49, 'overall' => 1.14 ), 'rank' => 91 ),
        array( 'slug' => 'vestofx', 'name' => 'VestoFX', 'entity' => 'EVBX LTD', 'cysec' => '409/22', 'founded' => 2020, 'hq' => 'Limassol, Cyprus', 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => null, 'blurb' => 'EVBX LTD (registered in Cyprus 13 April 2020, formerly Vstar & Soho Markets LTD) is CySEC\'s licensed entity for the domain vestofx.com/eu specifically; the near-identical domain vestofx.net is a separate, unlicensed site added to Malaysia\'s Securities Commission Investor Alert List in late 2025, so the two should not be conflated.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 1, 'track_record' => 1.24, 'overall' => 1.07 ), 'rank' => 92 ),
        array( 'slug' => 'trading-dot-com', 'name' => 'Trading.com', 'entity' => 'Trading.Com Markets EU Limited', 'cysec' => '256/14', 'founded' => null, 'hq' => null, 'min_deposit_usd' => null, 'min_deposit_display' => null, 'spread_eurusd' => null, 'platforms' => array(), 'other_reg' => array(), 'other_reg_count' => 0, 'instruments' => '1,400+ CFD instruments with leverage', 'blurb' => 'Could not verify founding year, headquarters city, minimum deposit, platforms, or spreads for Trading.com\'s EU entity from available sources -- only its instrument count and a promotional deposit-bonus offer were found.', 'scores' => array( 'regulation' => 1, 'cost' => null, 'platforms' => 1, 'track_record' => null, 'overall' => 1 ), 'rank' => 93 ),
    );
}
