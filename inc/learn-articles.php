<?php
/**
 * The 100-article /learn/ topical-authority plan: ten thematic clusters
 * (fundamentals, risk, choosing a broker, regulation, fees, platforms,
 * orders/execution, analysis/economics, trading concepts, and
 * due-diligence/safety) rather than a flat pile of unrelated posts.
 *
 * Three things this deliberately does NOT do, because the whole point of
 * a topical cluster is that it's trustworthy, linkable content rather
 * than volume for its own sake:
 *  - No guaranteed-return, "turn $X into $Y", or "best strategy" framing
 *    anywhere -- see globalfxhub_learn_banned_phrases() below, which the
 *    standing test suite checks every article's content against.
 *  - No article is provisioned with empty content. globalfxhub_get_learn_articles()
 *    lists the full, fixed 100-title/slug/cluster roster up front (so
 *    the schedule and the internal-linking graph are stable from day
 *    one), but globalfxhub_ensure_learn_articles() only ever creates a
 *    post for an entry that actually has content -- a title with no
 *    body yet is simply skipped on that run, never published as a stub,
 *    and picked up automatically once its content is filled in.
 *  - No hard-coded publish dates that assume everything ships at once.
 *    Each article's position in the fixed 100-item list determines its
 *    scheduled date (3/day from GLOBALFXHUB_LEARN_SCHEDULE_START), and
 *    that date's publish/future status is computed fresh on every run --
 *    so content that lands later than its "natural" slot still
 *    publishes immediately instead of waiting, and content that's ready
 *    early still respects the 3/day cadence.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require get_template_directory() . '/inc/learn-content/forex-fundamentals.php';
require get_template_directory() . '/inc/learn-content/leverage-margin-risk.php';
require get_template_directory() . '/inc/learn-content/choosing-a-broker.php';
require get_template_directory() . '/inc/learn-content/broker-regulation.php';
require get_template_directory() . '/inc/learn-content/fees-and-costs.php';
require get_template_directory() . '/inc/learn-content/trading-platforms.php';
require get_template_directory() . '/inc/learn-content/orders-and-execution.php';
require get_template_directory() . '/inc/learn-content/analysis-and-economics.php';
require get_template_directory() . '/inc/learn-content/trading-concepts.php';
require get_template_directory() . '/inc/learn-content/safety-and-due-diligence.php';

define( 'GLOBALFXHUB_LEARN_SCHEDULE_START', '2026-10-08' );
define( 'GLOBALFXHUB_LEARN_PER_DAY', 3 );

function globalfxhub_learn_clusters() {
    return array(
        'forex-fundamentals' => array(
            'title' => 'Forex Fundamentals',
            'description' => 'The vocabulary and market mechanics every other cluster builds on -- pips, lots, spreads, liquidity, and how the market actually operates.',
        ),
        'leverage-margin-risk' => array(
            'title' => 'Leverage, Margin and Risk',
            'description' => 'What leverage and margin actually do to your account, and the position-sizing and risk math that keeps a small mistake from becoming a large one.',
        ),
        'choosing-a-broker' => array(
            'title' => 'Choosing a Broker',
            'description' => 'How brokers differ in practice -- execution model, business model, and the red flags worth checking before you deposit.',
        ),
        'broker-regulation' => array(
            'title' => 'Broker Regulation',
            'description' => 'What each major regulator (FCA, CySEC, ASIC, DFSA, FSCA, Seychelles FSA, CFTC/NFA) actually does and doesn\'t cover, and what "Tier-1" really means.',
        ),
        'fees-and-costs' => array(
            'title' => 'Fees and Costs',
            'description' => 'Every way a broker can charge you -- spread, commission, swap, inactivity, conversion -- and how to compare the real cost of two brokers.',
        ),
        'trading-platforms' => array(
            'title' => 'Trading Platforms',
            'description' => 'MT4, MT5, cTrader, TradingView and the rest -- what each one actually offers and how to pick between them.',
        ),
        'orders-and-execution' => array(
            'title' => 'Orders and Execution',
            'description' => 'Order types, how brokers actually fill them, and what slippage and requotes really are.',
        ),
        'analysis-and-economics' => array(
            'title' => 'Analysis and Economics',
            'description' => 'How interest rates, inflation, central banks, and major economic releases actually move currency prices.',
        ),
        'trading-concepts' => array(
            'title' => 'Trading Concepts',
            'description' => 'The chart-reading vocabulary -- support/resistance, trends, the most common indicators -- explained plainly, without strategy hype.',
        ),
        'safety-and-due-diligence' => array(
            'title' => 'Safety, Due Diligence and Broker Research',
            'description' => 'How to actually verify a broker before depositing: licence checks, fund segregation, compensation schemes, and what to do if something goes wrong.',
        ),
    );
}

/**
 * Phrases/patterns that have no place in educational content about a
 * leveraged, loss-making-for-most-retail-accounts product: guaranteed-
 * outcome framing, "turn $X into $Y" framing, and absolute-certainty
 * language. Checked by the standing test suite against every article's
 * content on every run, not just at authoring time -- so a future edit
 * that reintroduces one of these gets caught the same way a regression
 * in any other scored/disclosed content on this site would.
 */
function globalfxhub_learn_banned_phrases() {
    return array(
        '/guaranteed (profit|return|income|win)/i',
        '/turn \$?\d+[\w,]* into \$?\d+/i',
        '/\bget rich\b/i',
        '/risk[-\s]?free/i',
        '/\bsure[-\s]?fire\b/i',
        '/\b100% (accurate|win rate|success)/i',
        '/best strategy (to|that) (guarantee|always)/i',
        '/can\'t lose/i',
    );
}

/**
 * The fixed 100-title/slug/cluster roster, in the exact order and
 * wording of the original cluster plan. 'content' and 'excerpt' are
 * filled in per-article as each cluster's draft is written and
 * reviewed; an entry with an empty 'content' is a real, placed title
 * waiting on its draft, not a stub to be published.
 */
function globalfxhub_get_learn_articles() {
    static $articles = null;
    if ( null !== $articles ) {
        return $articles;
    }

    $roster = array(
        // Cluster 1 -- Forex fundamentals
        array( 'slug' => 'what-is-forex-trading', 'title' => "What Is Forex Trading? Complete Beginner's Guide", 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'how-does-the-forex-market-work', 'title' => 'How Does the Forex Market Work?', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'what-are-currency-pairs', 'title' => 'What Are Currency Pairs? Majors, Minors and Exotics Explained', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'what-is-a-pip-in-forex', 'title' => 'What Is a Pip in Forex?', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'what-is-a-lot-in-forex', 'title' => 'What Is a Lot in Forex?', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'bid-vs-ask-price-in-forex', 'title' => 'Bid vs Ask Price in Forex', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'what-is-the-forex-spread', 'title' => 'What Is the Forex Spread?', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'what-is-slippage-in-forex-trading', 'title' => 'What Is Slippage in Forex Trading?', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'what-is-forex-liquidity', 'title' => 'What Is Forex Liquidity?', 'cluster' => 'forex-fundamentals' ),
        array( 'slug' => 'forex-market-hours', 'title' => 'Forex Market Hours: London, New York, Tokyo and Sydney Sessions', 'cluster' => 'forex-fundamentals' ),

        // Cluster 2 -- Leverage, margin and risk
        array( 'slug' => 'what-is-leverage-in-forex', 'title' => 'What Is Leverage in Forex?', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'forex-margin-explained', 'title' => 'Forex Margin Explained', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'margin-call-vs-stop-out', 'title' => "Margin Call vs Stop Out: What's the Difference?", 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'how-much-leverage-should-a-beginner-use', 'title' => 'How Much Leverage Should a Beginner Use?', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'why-high-leverage-is-dangerous', 'title' => 'Why High Leverage Is Dangerous', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'negative-balance-protection-explained', 'title' => 'Negative Balance Protection Explained', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'how-to-calculate-forex-position-size', 'title' => 'How to Calculate Forex Position Size', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'forex-risk-to-reward-ratio-explained', 'title' => 'Forex Risk-to-Reward Ratio Explained', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'how-much-should-you-risk-per-forex-trade', 'title' => 'How Much Should You Risk Per Forex Trade?', 'cluster' => 'leverage-margin-risk' ),
        array( 'slug' => 'forex-drawdown-explained', 'title' => 'Forex Drawdown Explained: How Much Is Too Much?', 'cluster' => 'leverage-margin-risk' ),

        // Cluster 3 -- Choosing a broker
        array( 'slug' => 'how-to-choose-a-forex-broker', 'title' => 'How to Choose a Forex Broker', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'how-to-check-whether-a-forex-broker-is-regulated', 'title' => 'How to Check Whether a Forex Broker Is Regulated', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'regulated-vs-unregulated-forex-brokers', 'title' => 'Regulated vs Unregulated Forex Brokers', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'what-happens-if-your-forex-broker-goes-bankrupt', 'title' => 'What Happens If Your Forex Broker Goes Bankrupt?', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'how-forex-brokers-make-money', 'title' => 'How Forex Brokers Make Money', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'market-maker-vs-ecn-vs-stp-brokers', 'title' => 'Market Maker vs ECN vs STP Brokers', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'dealing-desk-vs-no-dealing-desk-brokers', 'title' => 'Dealing Desk vs No-Dealing-Desk Brokers', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'what-is-an-ecn-forex-broker', 'title' => 'What Is an ECN Forex Broker?', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'what-is-an-stp-forex-broker', 'title' => 'What Is an STP Forex Broker?', 'cluster' => 'choosing-a-broker' ),
        array( 'slug' => 'red-flags-before-depositing-with-a-forex-broker', 'title' => '15 Red Flags to Check Before Depositing With a Forex Broker', 'cluster' => 'choosing-a-broker' ),

        // Cluster 4 -- Broker regulation
        array( 'slug' => 'forex-broker-regulation-explained', 'title' => 'Forex Broker Regulation Explained: Complete Guide', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'fca-forex-regulation-explained', 'title' => 'FCA Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'cysec-forex-regulation-explained', 'title' => 'CySEC Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'asic-forex-regulation-explained', 'title' => 'ASIC Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'dfsa-forex-regulation-explained', 'title' => 'DFSA Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'fsca-forex-regulation-explained', 'title' => 'FSCA Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'seychelles-fsa-forex-regulation-explained', 'title' => 'Seychelles FSA Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'cftc-and-nfa-forex-regulation-explained', 'title' => 'CFTC and NFA Forex Regulation Explained', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'offshore-forex-brokers-risks-and-regulations', 'title' => 'Offshore Forex Brokers: Risks and Regulations', 'cluster' => 'broker-regulation' ),
        array( 'slug' => 'tier-1-forex-regulators-explained', 'title' => 'Tier-1 Forex Regulators: What Does "Tier 1" Actually Mean?', 'cluster' => 'broker-regulation' ),

        // Cluster 5 -- Fees and costs
        array( 'slug' => 'forex-broker-fees-explained', 'title' => 'Forex Broker Fees Explained', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'spread-vs-commission-which-is-cheaper', 'title' => 'Spread vs Commission: Which Is Cheaper?', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'raw-spread-vs-standard-forex-accounts', 'title' => 'Raw Spread vs Standard Forex Accounts', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'forex-swap-fees-explained', 'title' => 'Forex Swap Fees Explained', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'what-is-an-overnight-financing-fee', 'title' => 'What Is an Overnight Financing Fee?', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'forex-broker-inactivity-fees-explained', 'title' => 'Forex Broker Inactivity Fees Explained', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'currency-conversion-fees-at-forex-brokers', 'title' => 'Currency Conversion Fees at Forex Brokers', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'forex-deposit-and-withdrawal-fees-explained', 'title' => 'Forex Deposit and Withdrawal Fees Explained', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'hidden-forex-broker-fees-to-watch-for', 'title' => 'Hidden Forex Broker Fees to Watch For', 'cluster' => 'fees-and-costs' ),
        array( 'slug' => 'how-to-compare-the-true-cost-of-two-forex-brokers', 'title' => 'How to Compare the True Cost of Two Forex Brokers', 'cluster' => 'fees-and-costs' ),

        // Cluster 6 -- Trading platforms
        array( 'slug' => 'metatrader-4-guide-for-beginners', 'title' => 'MetaTrader 4 Guide for Beginners', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'metatrader-5-guide-for-beginners', 'title' => 'MetaTrader 5 Guide for Beginners', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'mt4-vs-mt5-which-is-better', 'title' => 'MT4 vs MT5: Which Is Better?', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'ctrader-explained', 'title' => 'cTrader Explained: Complete Guide', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'ctrader-vs-metatrader-5', 'title' => 'cTrader vs MetaTrader 5', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'tradingview-for-forex-trading', 'title' => 'TradingView for Forex Trading: Complete Guide', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'tradingview-vs-mt5-for-forex-traders', 'title' => 'TradingView vs MT5 for Forex Traders', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'best-forex-trading-platform-features', 'title' => 'Best Forex Trading Platform Features to Look For', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'what-are-expert-advisors-in-metatrader', 'title' => 'What Are Expert Advisors in MetaTrader?', 'cluster' => 'trading-platforms' ),
        array( 'slug' => 'desktop-vs-web-vs-mobile-forex-platforms', 'title' => 'Desktop vs Web vs Mobile Forex Trading Platforms', 'cluster' => 'trading-platforms' ),

        // Cluster 7 -- Orders and execution
        array( 'slug' => 'forex-order-types-explained', 'title' => 'Forex Order Types Explained', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'market-order-vs-limit-order', 'title' => 'Market Order vs Limit Order', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'stop-order-vs-limit-order', 'title' => 'Stop Order vs Limit Order', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'stop-loss-orders-explained', 'title' => 'Stop Loss Orders Explained', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'take-profit-orders-explained', 'title' => 'Take Profit Orders Explained', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'trailing-stop-loss-explained', 'title' => 'Trailing Stop Loss Explained', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'what-is-forex-execution-speed', 'title' => 'What Is Forex Execution Speed?', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'what-causes-slippage-in-forex', 'title' => 'What Causes Slippage in Forex?', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'requotes-in-forex-trading-explained', 'title' => 'Requotes in Forex Trading Explained', 'cluster' => 'orders-and-execution' ),
        array( 'slug' => 'how-forex-brokers-execute-your-trades', 'title' => 'How Forex Brokers Execute Your Trades', 'cluster' => 'orders-and-execution' ),

        // Cluster 8 -- Analysis and economics
        array( 'slug' => 'fundamental-analysis-in-forex', 'title' => "Fundamental Analysis in Forex: Beginner's Guide", 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'technical-analysis-in-forex', 'title' => "Technical Analysis in Forex: Beginner's Guide", 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'how-interest-rates-affect-currency-prices', 'title' => 'How Interest Rates Affect Currency Prices', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'how-inflation-affects-forex-markets', 'title' => 'How Inflation Affects Forex Markets', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'how-central-banks-affect-forex-markets', 'title' => 'How Central Banks Affect Forex Markets', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'how-nonfarm-payrolls-affect-forex', 'title' => 'How Nonfarm Payrolls Affect Forex', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'how-cpi-data-affects-currency-markets', 'title' => 'How CPI Data Affects Currency Markets', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'how-gdp-affects-forex-markets', 'title' => 'How GDP Affects Forex Markets', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'what-is-the-economic-calendar', 'title' => 'What Is the Economic Calendar and How Do Traders Use It?', 'cluster' => 'analysis-and-economics' ),
        array( 'slug' => 'risk-on-vs-risk-off-markets-explained', 'title' => 'Risk-On vs Risk-Off Markets Explained', 'cluster' => 'analysis-and-economics' ),

        // Cluster 9 -- Trading concepts
        array( 'slug' => 'support-and-resistance-in-forex', 'title' => 'Support and Resistance in Forex', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'forex-trends-explained', 'title' => 'Forex Trends Explained', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'how-to-read-forex-candlestick-charts', 'title' => 'How to Read Forex Candlestick Charts', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'moving-averages-in-forex-explained', 'title' => 'Moving Averages in Forex Explained', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'rsi-indicator-explained', 'title' => 'RSI Indicator Explained', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'macd-indicator-explained', 'title' => 'MACD Indicator Explained', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'bollinger-bands-explained', 'title' => 'Bollinger Bands Explained', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'fibonacci-retracement-in-forex', 'title' => 'Fibonacci Retracement in Forex', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'forex-breakouts-explained', 'title' => 'Forex Breakouts Explained', 'cluster' => 'trading-concepts' ),
        array( 'slug' => 'forex-volatility-explained', 'title' => 'Forex Volatility Explained', 'cluster' => 'trading-concepts' ),

        // Cluster 10 -- Safety / due diligence / broker research
        array( 'slug' => 'how-to-verify-a-forex-brokers-license', 'title' => "How to Verify a Forex Broker's License", 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'how-to-read-a-forex-brokers-regulatory-disclosure', 'title' => "How to Read a Forex Broker's Regulatory Disclosure", 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'clone-forex-brokers-impersonation-scams', 'title' => 'Clone Forex Brokers: How Broker Impersonation Scams Work', 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'how-to-research-a-forex-broker-before-depositing', 'title' => 'How to Research a Forex Broker Before Depositing Money', 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'what-is-segregation-of-client-funds', 'title' => 'What Is Segregation of Client Funds?', 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'investor-compensation-schemes-explained-for-beginners', 'title' => 'Investor Compensation Schemes Explained', 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'what-happens-to-your-money-if-a-broker-fails', 'title' => 'What Happens to Your Money If a Broker Fails?', 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'why-your-brokers-legal-entity-matters', 'title' => "Why Your Broker's Legal Entity Matters", 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'forex-broker-complaints-where-and-how-to-complain', 'title' => 'Forex Broker Complaints: Where and How to Complain', 'cluster' => 'safety-and-due-diligence' ),
        array( 'slug' => 'forex-broker-due-diligence-checklist', 'title' => 'Forex Broker Due-Diligence Checklist: 25 Things to Verify Before Depositing', 'cluster' => 'safety-and-due-diligence' ),
    );

    // Per-article content/excerpt/byline is merged in from the content
    // map below -- kept as a separate, append-only map rather than
    // inline in the roster above so each cluster's draft can land as its
    // own, independently reviewable change.
    $content_map = globalfxhub_get_learn_article_content_map();
    foreach ( $roster as &$article ) {
        $extra = $content_map[ $article['slug'] ] ?? array();
        $article['excerpt'] = $extra['excerpt'] ?? '';
        $article['content'] = $extra['content'] ?? '';
        $article['byline']  = $extra['byline'] ?? '';
    }
    unset( $article );

    $articles = $roster;
    return $articles;
}

/**
 * Separated from the roster above so content can be filled in cluster by
 * cluster: each call_user_func below is expected to return
 * array( slug => array( 'excerpt' => ..., 'content' => ..., 'byline' => ... ) )
 * for that cluster's 10 articles once drafted, or an empty array before
 * that cluster's content has landed.
 */
function globalfxhub_get_learn_article_content_map() {
    $map = array();
    foreach ( globalfxhub_learn_content_providers() as $provider ) {
        if ( function_exists( $provider ) ) {
            $map = array_merge( $map, call_user_func( $provider ) );
        }
    }
    return $map;
}

function globalfxhub_learn_content_providers() {
    return array(
        'globalfxhub_learn_content_forex_fundamentals',
        'globalfxhub_learn_content_leverage_margin_risk',
        'globalfxhub_learn_content_choosing_a_broker',
        'globalfxhub_learn_content_broker_regulation',
        'globalfxhub_learn_content_fees_and_costs',
        'globalfxhub_learn_content_trading_platforms',
        'globalfxhub_learn_content_orders_and_execution',
        'globalfxhub_learn_content_analysis_and_economics',
        'globalfxhub_learn_content_trading_concepts',
        'globalfxhub_learn_content_safety_and_due_diligence',
    );
}

/**
 * Index (0-based, by canonical roster order) -> scheduled date/status.
 * The date is fixed by position in the list, computed fresh from
 * GLOBALFXHUB_LEARN_SCHEDULE_START every time -- never stored -- so
 * content that lands after its "natural" day still gets a sensible,
 * immediately-publishable date instead of an artificial delay.
 */
function globalfxhub_learn_article_schedule( $index ) {
    $day = (int) floor( $index / GLOBALFXHUB_LEARN_PER_DAY );
    $date = date( 'Y-m-d', strtotime( GLOBALFXHUB_LEARN_SCHEDULE_START . ' +' . $day . ' days' ) );
    $today = date( 'Y-m-d' );
    $status = ( $date <= $today ) ? 'publish' : 'future';
    return array( 'date' => $date, 'status' => $status );
}

/**
 * Creates a post for every roster entry that has real content and
 * doesn't already exist, scheduled per globalfxhub_learn_article_schedule().
 * Self-healing/idempotent like the rest of this theme's provisioning:
 * safe to run on every page load, a no-op once everything that has
 * content is already created.
 */
function globalfxhub_ensure_learn_articles() {
    $term = term_exists( 'Learn', 'category' );
    if ( ! $term ) {
        $term = wp_insert_term( 'Learn', 'category', array( 'slug' => 'learn' ) );
    }
    if ( is_wp_error( $term ) || empty( $term['term_id'] ) ) {
        return;
    }
    $category_id = (int) $term['term_id'];

    $articles = globalfxhub_get_learn_articles();
    foreach ( $articles as $index => $article ) {
        if ( empty( $article['content'] ) ) {
            continue; // not drafted yet -- never publish a stub
        }
        if ( get_page_by_path( $article['slug'], OBJECT, 'post' ) ) {
            continue; // already provisioned
        }

        $schedule = globalfxhub_learn_article_schedule( $index );
        $post_id = wp_insert_post( array(
            'post_title'    => $article['title'],
            'post_name'     => $article['slug'],
            'post_excerpt'  => $article['excerpt'],
            'post_content'  => $article['content'],
            'post_status'   => $schedule['status'],
            'post_type'     => 'post',
            'post_category' => array( $category_id ),
            'post_date'     => $schedule['date'] . ' 09:00:00',
            'post_date_gmt' => get_gmt_from_date( $schedule['date'] . ' 09:00:00' ),
        ) );

        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_learn_cluster', $article['cluster'] );
            if ( ! empty( $article['byline'] ) ) {
                update_post_meta( $post_id, '_byline', $article['byline'] );
            }
        }
    }
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_learn_articles' );

/**
 * Appends a "More in this cluster" block linking to sibling articles --
 * but only the ones actually published, checked live rather than baked
 * in at creation time, so an early article in a cluster never links to
 * a sibling that doesn't exist yet.
 */
function globalfxhub_learn_cluster_footer_filter( $content ) {
    if ( ! is_single() || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }
    $post_id = get_the_ID();
    $cluster_slug = get_post_meta( $post_id, '_learn_cluster', true );
    if ( ! $cluster_slug ) {
        return $content;
    }

    $clusters = globalfxhub_learn_clusters();
    if ( ! isset( $clusters[ $cluster_slug ] ) ) {
        return $content;
    }

    $current_slug = get_post_field( 'post_name', $post_id );
    $siblings = array_filter( globalfxhub_get_learn_articles(), function( $a ) use ( $cluster_slug, $current_slug ) {
        return $a['cluster'] === $cluster_slug && $a['slug'] !== $current_slug;
    } );

    $links = array();
    foreach ( $siblings as $sibling ) {
        $sibling_post = get_page_by_path( $sibling['slug'], OBJECT, 'post' );
        if ( $sibling_post && 'publish' === $sibling_post->post_status ) {
            $links[] = '<li><a href="' . esc_url( home_url( '/learn/' . $sibling['slug'] . '/' ) ) . '">' . esc_html( $sibling['title'] ) . '</a></li>';
        }
    }
    if ( empty( $links ) ) {
        return $content;
    }

    $footer = "\n" . '<div class="learn-cluster-related"><h2>More in ' . esc_html( $clusters[ $cluster_slug ]['title'] ) . '</h2><ul>' . implode( '', $links ) . '</ul></div>';
    return $content . $footer;
}
add_filter( 'the_content', 'globalfxhub_learn_cluster_footer_filter' );

/**
 * Makes the Learn <-> reviews/tools relationship two-way. Every Learn
 * article links out to tools and reviews already; nothing linked back
 * in, so these 100 freshly-published articles got no internal links
 * from the site's highest-traffic pages. A small, disclosed lookup
 * table -- never hardcoded per broker, and every target slug checked
 * against the real roster below -- rather than picked ad hoc per call
 * site.
 */
function globalfxhub_review_section_learn_links() {
    return array(
        'regulation'          => array( 'slug' => 'how-to-check-whether-a-forex-broker-is-regulated', 'label' => 'How to check whether a forex broker is regulated' ),
        'fees'                => array( 'slug' => 'forex-broker-fees-explained', 'label' => 'Forex broker fees explained' ),
        'investor_protection' => array( 'slug' => 'investor-compensation-schemes-explained-for-beginners', 'label' => 'Investor compensation schemes explained' ),
    );
}

function globalfxhub_tool_learn_links() {
    return array(
        'broker-finder'       => array( 'slug' => 'how-to-choose-a-forex-broker', 'label' => 'How to choose a forex broker' ),
        'compare'             => array( 'slug' => 'how-to-choose-a-forex-broker', 'label' => 'How to choose a forex broker' ),
        'cost-calculator'     => array( 'slug' => 'how-to-compare-the-true-cost-of-two-forex-brokers', 'label' => 'How to compare the true cost of two forex brokers' ),
        'regulation-checker'  => array( 'slug' => 'how-to-verify-a-forex-brokers-license', 'label' => "How to verify a forex broker's license" ),
        'broker-changelog'    => array( 'slug' => 'forex-broker-due-diligence-checklist', 'label' => 'Forex broker due-diligence checklist' ),
    );
}

/**
 * Resolves a lookup-table entry (from either function above) to a
 * renderable link, or null if that slug isn't a real, currently
 * published article -- so a typo or a not-yet-published slug silently
 * omits the link rather than rendering a dead one.
 */
function globalfxhub_resolve_learn_link( $entry ) {
    if ( empty( $entry['slug'] ) ) {
        return null;
    }
    $post = get_page_by_path( $entry['slug'], OBJECT, 'post' );
    if ( ! $post || 'publish' !== $post->post_status ) {
        return null;
    }
    return array(
        'url'   => home_url( '/learn/' . $entry['slug'] . '/' ),
        'label' => $entry['label'],
    );
}
