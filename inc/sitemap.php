<?php
/**
 * XML sitemap at /sitemap.xml covering every real, indexable URL on
 * the site.
 *
 * WordPress core's own /wp-sitemap.xml (built in since 5.5, still
 * active -- nothing here disables it) only knows about native
 * posts/pages: the Learn guides, FX Market News articles, and the
 * seven service pages. Every broker review, country page, best-list,
 * compare/vs-page, regulation page, broker-news item, and tool page
 * on this site is a virtual route rendered from a PHP data array via
 * a rewrite rule (see functions.php and the various inc/*.php files
 * that register them), never a wp_insert_post()-backed object -- core
 * has no way to discover any of them, so its sitemap is missing the
 * large majority of this site's actual content.
 *
 * This file is built from the exact same data-accessor functions and
 * eligibility rules every other page on the site already uses
 * (globalfxhub_top_ranked_broker_slugs(40) for which vs-pages and
 * alternatives pages exist, globalfxhub_vs_pair()/
 * globalfxhub_broker_alternatives() to confirm a pairing or broker is
 * actually in bound), so it can never list a URL that doesn't
 * actually resolve.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_sitemap_rewrite_rules() {
    add_rewrite_rule( '^sitemap\.xml$', 'index.php?globalfxhub_sitemap=1', 'top' );
}
add_action( 'init', 'globalfxhub_sitemap_rewrite_rules' );

function globalfxhub_sitemap_query_vars( $vars ) {
    $vars[] = 'globalfxhub_sitemap';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_sitemap_query_vars' );

/**
 * Returns every indexable URL as array( array( 'loc' => ..., 'priority' => ... ), ... ).
 */
function globalfxhub_sitemap_urls() {
    $urls = array();
    $add  = function( $loc, $priority = 0.6 ) use ( &$urls ) {
        $urls[] = array( 'loc' => $loc, 'priority' => $priority );
    };

    $add( home_url( '/' ), 1.0 );

    // --- Reviews ---
    $add( home_url( '/reviews/' ), 0.8 );
    $brokers = globalfxhub_get_brokers();
    foreach ( $brokers as $b ) {
        $add( home_url( '/reviews/' . $b['slug'] . '/' ), 0.7 );
    }
    $top40 = globalfxhub_top_ranked_broker_slugs( 40 );
    foreach ( $top40 as $slug ) {
        if ( globalfxhub_broker_alternatives( $slug ) ) {
            $add( home_url( '/reviews/' . $slug . '/alternatives/' ), 0.4 );
        }
    }

    // --- Compare (static vs-pages: both brokers must be top-40) ---
    $add( home_url( '/compare/' ), 0.6 );
    foreach ( $top40 as $i => $a_slug ) {
        foreach ( $top40 as $j => $b_slug ) {
            if ( $j <= $i ) {
                continue;
            }
            if ( globalfxhub_vs_pair( $a_slug, $b_slug ) ) {
                $add( home_url( '/compare/' . $a_slug . '-vs-' . $b_slug . '/' ), 0.4 );
            }
        }
    }

    // --- Best Brokers ---
    $add( home_url( '/best/' ), 0.8 );
    foreach ( array_keys( globalfxhub_get_best_lists() ) as $slug ) {
        $add( home_url( '/best/' . $slug . '/' ), 0.7 );
    }

    // --- Countries ---
    $add( home_url( '/countries/' ), 0.8 );
    foreach ( array_keys( globalfxhub_get_all_countries() ) as $slug ) {
        $add( home_url( '/countries/' . $slug . '/' ), 0.6 );
    }

    // --- Regulation ---
    $add( home_url( '/regulation/' ), 0.7 );
    foreach ( array_keys( globalfxhub_get_regulation_pages() ) as $slug ) {
        $add( home_url( '/regulation/' . $slug . '/' ), 0.6 );
    }

    // --- Standalone tools ---
    $add( home_url( '/broker-finder/' ), 0.7 );
    $add( home_url( '/cost-calculator/' ), 0.6 );
    $add( home_url( '/broker-changelog/' ), 0.6 );
    $add( home_url( '/regulation-checker/' ), 0.6 );

    // --- Learn ---
    $add( home_url( '/learn/' ), 0.8 );

    // --- News ---
    $add( home_url( '/news/' ), 0.7 );
    $add( home_url( '/news/markets/' ), 0.6 );
    $add( home_url( '/news/brokers/' ), 0.6 );
    foreach ( globalfxhub_get_broker_news_items() as $item ) {
        $add( home_url( '/news/brokers/' . globalfxhub_broker_news_item_slug( $item ) . '/' ), 0.5 );
    }

    // --- Native posts (Learn guides + FX Market News articles) --
    // every published post, whatever its category, since single.php
    // renders all of them; get_permalink() is used rather than a
    // guessed URL pattern so this can never drift from the site's
    // actual permalink settings.
    $native_post_ids = get_posts( array(
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ) );
    foreach ( $native_post_ids as $post_id ) {
        $add( get_permalink( $post_id ), 0.6 );
    }

    // --- Service pages (About, How We Test, Why Trust Us, Advertiser
    // Disclosure, Terms, Privacy, Contact) ---
    foreach ( array_keys( globalfxhub_get_service_pages() ) as $slug ) {
        $page = get_page_by_path( $slug );
        if ( $page ) {
            $add( get_permalink( $page->ID ), 0.4 );
        }
    }

    return $urls;
}

function globalfxhub_render_sitemap() {
    header( 'Content-Type: application/xml; charset=UTF-8' );
    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    foreach ( globalfxhub_sitemap_urls() as $u ) {
        echo "  <url>\n";
        echo '    <loc>' . esc_url( $u['loc'] ) . "</loc>\n";
        echo '    <priority>' . esc_html( number_format( $u['priority'], 1 ) ) . "</priority>\n";
        echo "  </url>\n";
    }
    echo '</urlset>' . "\n";
    exit;
}

function globalfxhub_sitemap_template_redirect() {
    if ( get_query_var( 'globalfxhub_sitemap' ) ) {
        globalfxhub_render_sitemap();
    }
}
add_action( 'template_redirect', 'globalfxhub_sitemap_template_redirect' );

/**
 * Advertises /sitemap.xml in WordPress's own virtual robots.txt --
 * no theme code previously touched robots.txt at all, so this is the
 * only line controlling it. Helps crawlers that respect this
 * convention (Bing, etc.) discover the sitemap on their own; Google
 * Search Console itself ignores robots.txt for sitemap discovery and
 * needs the URL submitted directly.
 */
function globalfxhub_robots_txt_sitemap( $output, $public ) {
    return rtrim( $output ) . "\nSitemap: " . home_url( '/sitemap.xml' ) . "\n";
}
add_filter( 'robots_txt', 'globalfxhub_robots_txt_sitemap', 10, 2 );
