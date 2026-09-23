<?php
/**
 * Every recurring UI/homepage string, registered with Polylang (if active)
 * so translations entered under Languages > Translations actually get
 * served on the front end. The theme never hard-depends on Polylang: if
 * it's inactive, globalfxhub_t()/globalfxhub_te() just pass the original
 * English straight through.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_translatable_strings() {
    return array(
        'nav_reviews', 'nav_guides', 'nav_blog', 'nav_compare', 'nav_countries', 'nav_cta',

        'footer_company_heading', 'footer_about', 'footer_how_we_test', 'footer_why_trust_us',
        'footer_research_heading', 'footer_all_reviews', 'footer_compare_brokers',
        'footer_legal_heading', 'footer_advertiser_disclosure', 'footer_terms', 'footer_privacy',
        'footer_contact', 'footer_tagline', 'footer_legal_line', 'footer_disclosure_full',

        'hero1_eyebrow', 'hero1_h1', 'hero1_p', 'hero_search_label', 'hero_search_placeholder',
        'hero_search_button', 'hero2_eyebrow', 'hero2_h1', 'hero2_p', 'hero2_button',
        'hero3_eyebrow', 'hero3_h1', 'hero3_p', 'hero3_button',

        'trust1', 'trust2', 'trust3', 'trust4',

        'sec_overview_h2', 'sec_overview_p', 'sec_heatmap_h3', 'sec_movers_h3',
        'sec_blog_h2', 'sec_blog_p', 'sec_news_h2', 'sec_news_p',
        'sec_rankings_h2', 'sec_rankings_p', 'sec_method_h2', 'sec_method_p',
        'sec_guides_h2', 'sec_guides_p', 'sec_compare_h2', 'sec_compare_p',
        'sec_countries_h2', 'sec_countries_p',

        'method1_h3', 'method1_p', 'method2_h3', 'method2_p',
        'method3_h3', 'method3_p', 'method4_h3', 'method4_p',

        'link_how_we_score', 'link_all_articles', 'link_all_news', 'link_all_guides', 'link_open_compare',

        'table_broker', 'table_spread', 'table_platforms', 'table_score', 'btn_read_review',
    );
}

/**
 * The canonical English text for each key -- this is both the source
 * registered with Polylang and the fallback shown when no translation
 * exists yet (or Polylang isn't active).
 */
function globalfxhub_string_text( $key ) {
    $strings = array(
        'nav_reviews' => 'Reviews',
        'nav_guides' => 'Guides',
        'nav_blog' => 'Blog',
        'nav_compare' => 'Compare',
        'nav_countries' => 'Countries',
        'nav_cta' => 'See rankings',

        'footer_company_heading' => 'Company',
        'footer_about' => 'About',
        'footer_how_we_test' => 'How we test',
        'footer_why_trust_us' => 'Why trust us',
        'footer_research_heading' => 'Research',
        'footer_all_reviews' => 'All reviews',
        'footer_compare_brokers' => 'Compare brokers',
        'footer_legal_heading' => 'Legal',
        'footer_advertiser_disclosure' => 'Advertiser disclosure',
        'footer_terms' => 'Terms of use',
        'footer_privacy' => 'Privacy policy',
        'footer_contact' => 'Contact',
        'footer_tagline' => 'Independent forex and CFD broker research. We test with real accounts and real money.',
        'footer_legal_line' => 'Independent forex & CFD broker research',
        'footer_disclosure_full' => 'Advertiser disclosure & risk warning: Broker names, CySEC licence numbers, and trading conditions referenced on this site describe real, independently operating companies, compiled from public regulatory registers and broker disclosures as of March 2026 -- figures change, and should be verified directly with the broker and on the CySEC register before you rely on them. Scores are calculated using a disclosed methodology, not hands-on account testing. GlobalFXHub does not currently have referral or advertising relationships with any broker listed; if that changes, this section will disclose it. CFDs are complex instruments carrying a high risk of losing money rapidly due to leverage -- most retail investor accounts lose money trading CFDs. This is not financial advice.',

        'hero1_eyebrow' => '2026 ANNUAL RANKINGS',
        'hero1_h1' => 'Find a forex broker you can actually trust.',
        'hero1_p' => 'We open real accounts, trade with real money, and score every broker on execution, cost, and platform quality.',
        'hero_search_label' => 'QUICK BROKER SEARCH',
        'hero_search_placeholder' => 'Search a broker by name…',
        'hero_search_button' => 'Search',
        'hero2_eyebrow' => 'NEW THIS MONTH',
        'hero2_h1' => 'Track the market with our live heatmap.',
        'hero2_p' => "See which currency pairs, metals, and energy markets are moving right now, and how brokers' spreads compare across each.",
        'hero2_button' => 'View market overview',
        'hero3_eyebrow' => 'FREE EDUCATION',
        'hero3_h1' => 'New to trading? Start with the basics.',
        'hero3_p' => 'Plain-English guides on forex, commodities, oil, and gold -- no jargon, no sales pitch.',
        'hero3_button' => 'Browse the guides',

        'trust1' => 'CySEC brokers researched',
        'trust2' => 'Scoring factors, fully disclosed',
        'trust3' => 'Free educational guides & articles',
        'trust4' => 'Paid for by broker rankings',

        'sec_overview_h2' => 'Market overview',
        'sec_overview_p' => "A snapshot of today's forex, metals, and energy markets. Updated throughout the trading day.",
        'sec_heatmap_h3' => 'Market heatmap',
        'sec_movers_h3' => "Today's movers",
        'sec_blog_h2' => 'From the blog',
        'sec_blog_p' => 'Plain-English explainers on how the forex and commodities markets actually work.',
        'sec_news_h2' => 'Latest news',
        'sec_news_p' => 'Broker developments and market-moving headlines, in brief.',
        'sec_rankings_h2' => 'Top 15 CySEC-regulated forex brokers',
        'sec_rankings_p' => 'Real, currently CySEC-authorised brokers, scored with a disclosed methodology based on public data -- not hands-on testing. See "How we score" below.',
        'sec_method_h2' => 'How we score CySEC brokers',
        'sec_method_p' => "This is a disclosed, data-based methodology -- not a claim of hands-on account testing. Here's exactly how each score is built.",
        'sec_guides_h2' => 'Guides for your trading style',
        'sec_guides_p' => 'Start wherever you are -- new to forex, switching platforms, or optimizing for cost.',
        'sec_compare_h2' => 'Compare brokers head-to-head',
        'sec_compare_p' => 'Put two brokers side by side on cost, platforms, and regulation.',
        'sec_countries_h2' => 'Best brokers by country',
        'sec_countries_p' => 'Regulation and available brokers vary a lot by where you live -- start here.',

        'method1_h3' => 'Regulatory footprint',
        'method1_p' => 'Ranked by how many Tier-1 regulators (CySEC plus others, e.g. FCA, ASIC) each broker holds. More independent oversight scores higher.',
        'method2_h3' => 'Cost',
        'method2_p' => 'Average EUR/USD spread and minimum deposit, each ranked against the other 14 brokers on this list. Lower cost and lower barrier to entry score higher.',
        'method3_h3' => 'Platform breadth',
        'method3_p' => 'Number of distinct trading platforms supported (MT4, MT5, cTrader, TradingView, proprietary). More choice scores higher.',
        'method4_h3' => 'Track record',
        'method4_p' => 'Years in operation, ranked relatively across the list. Longer-operating brokers score higher.',

        'link_how_we_score' => 'How we score →',
        'link_all_articles' => 'All articles →',
        'link_all_news' => 'All news →',
        'link_all_guides' => 'All guides →',
        'link_open_compare' => 'Open the compare tool →',

        'table_broker' => 'Broker',
        'table_spread' => 'Avg. spread',
        'table_platforms' => 'Platforms',
        'table_score' => 'Score',
        'btn_read_review' => 'Read review',
    );

    return isset( $strings[ $key ] ) ? $strings[ $key ] : $key;
}

function globalfxhub_register_strings() {
    if ( ! function_exists( 'pll_register_string' ) ) {
        return;
    }
    foreach ( globalfxhub_translatable_strings() as $key ) {
        $text = globalfxhub_string_text( $key );
        pll_register_string( $key, $text, 'GlobalFXHub Theme' );
    }
}
add_action( 'init', 'globalfxhub_register_strings' );

/**
 * Get/echo a registered string translated into the current language (via
 * Polylang's Languages > Translations), or the original English if
 * Polylang isn't active or no translation has been entered yet.
 */
function globalfxhub_t( $key ) {
    $text = globalfxhub_string_text( $key );
    return function_exists( 'pll__' ) ? pll__( $text ) : $text;
}
function globalfxhub_te( $key ) {
    echo esc_html( globalfxhub_t( $key ) );
}
