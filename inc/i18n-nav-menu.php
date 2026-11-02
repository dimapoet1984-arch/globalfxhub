<?php
/**
 * Translates the 'primary' wp_nav_menu's labels and URLs, and gives
 * footer.php's hardcoded fallback links (no real nav_menu is
 * assigned to the footer locations) a single language-aware URL
 * resolver to call instead of bare home_url().
 *
 * Confirmed live 2026-11-02: the 'primary' menu is one shared menu
 * across every language (no per-language menu assigned in Polylang's
 * own Appearance > Menus language column), so Polylang's native
 * per-item link translation never engages here -- every nav link on
 * /es/, /de/, /ar/ rendered as the bare English URL with an English
 * label, even though real per-language pages already exist for every
 * one of them (globalfxhub_do_sync_templated_pages_across_languages(),
 * functions.php, keeps a per-language stub for every container page
 * on every init). Fixing this the way the rest of this theme already
 * resolves language (a filter at render time, matching
 * inc/i18n-track-b-routing.php's approach) avoids ever having to
 * create or maintain a second, third, ... nav_menu object per
 * language in wp-admin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Resolves a theme-hardcoded nav/footer link to the current
 * language's equivalent, falling back gracefully (never a 404) when
 * no translation exists yet -- same philosophy as
 * inc/i18n-track-b-routing.php's container-language fallback.
 *
 * $english_slug: the real page's own stable English slug (e.g.
 * "reviews"), when the link points to an actual WP page. Omit it for
 * a Track B virtual route (e.g. "news/markets") that has no page
 * object of its own -- this prefixes the current language straight
 * onto $fallback_path instead, matching
 * globalfxhub_track_b_routing_rewrite_rules()'s own URL convention
 * for those routes exactly.
 */
function globalfxhub_nav_url( $fallback_path, $english_slug = null ) {
    if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_default_language' ) ) {
        return home_url( $fallback_path );
    }
    $lang    = pll_current_language();
    $default = pll_default_language();
    if ( ! $lang || $lang === $default ) {
        return home_url( $fallback_path );
    }

    if ( $english_slug && function_exists( 'pll_get_post' ) ) {
        $page = get_page_by_path( $english_slug );
        if ( $page ) {
            $translated_id = pll_get_post( $page->ID, $lang );
            if ( $translated_id ) {
                $permalink = get_permalink( $translated_id );
                if ( $permalink ) {
                    return $permalink;
                }
            }
        }
    }

    // No real (or no yet-existing) translation to resolve to -- prefix
    // the language code directly onto the English path, matching
    // inc/i18n-track-b-routing.php's own URL convention exactly
    // (confirmed live 2026-11-02: an earlier version of this function
    // called a `pll_home_url()` helper that doesn't actually exist in
    // this Polylang install, which silently fell through to the bare,
    // un-prefixed home_url() below for every single nav item -- labels
    // translated, but not one URL did. Plain string prefixing needs no
    // Polylang function this theme hasn't already proven live.)
    return home_url( '/' . $lang . $fallback_path );
}

/**
 * English nav-item label -> [ translatable-strings.php key, real
 * page slug (or null for a dropdown toggle with no link of its own) ].
 * Matched against the 'primary' menu's own item titles, which are
 * plain text set in wp-admin's Menu editor -- NOT already routed
 * through globalfxhub_te() -- so this is the one place that mapping
 * needs to live.
 */
function globalfxhub_primary_nav_label_map() {
    return array(
        'Reviews'            => array( 'nav_reviews', 'reviews' ),
        'Best Brokers'       => array( 'nav_best', 'best' ),
        'Tools'              => array( 'nav_tools', null ),
        'Research'           => array( 'nav_research', null ),
        'Broker Finder'      => array( 'nav_finder', 'broker-finder' ),
        'Compare'            => array( 'nav_compare', 'compare' ),
        'Cost Calculator'    => array( 'nav_cost_calculator', 'cost-calculator' ),
        'Regulation Checker' => array( 'nav_regulation_checker', 'regulation-checker' ),
        'Change Log'         => array( 'nav_changelog', 'broker-changelog' ),
        'Learn'              => array( 'nav_learn', 'learn' ),
        'News'               => array( 'nav_news', 'news' ),
        'Regulation'         => array( 'nav_regulation', 'regulation' ),
        'Countries'          => array( 'nav_countries', 'countries' ),
    );
}

function globalfxhub_translate_primary_nav_items( $items, $args ) {
    if ( empty( $args->theme_location ) || 'primary' !== $args->theme_location ) {
        return $items;
    }
    if ( ! function_exists( 'pll_current_language' ) || ! function_exists( 'pll_default_language' ) ) {
        return $items;
    }
    $lang = pll_current_language();
    if ( ! $lang || $lang === pll_default_language() ) {
        return $items;
    }

    $label_map = globalfxhub_primary_nav_label_map();
    foreach ( $items as $item ) {
        if ( ! isset( $label_map[ $item->title ] ) ) {
            continue;
        }
        list( $key, $slug ) = $label_map[ $item->title ];
        $item->title = globalfxhub_t( $key );
        if ( $slug ) {
            $item->url = globalfxhub_nav_url( '/' . $slug . '/', $slug );
        }
    }
    return $items;
}
add_filter( 'wp_nav_menu_objects', 'globalfxhub_translate_primary_nav_items', 10, 2 );
