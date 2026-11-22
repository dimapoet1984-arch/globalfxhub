<?php
/**
 * Original data reports at /research/{slug}/ -- the "original
 * research/data (unique, citable reports built from this site's own
 * broker dataset)" 15% content bucket from CLAUDE.md's 2026-11-09
 * content strategy, explicitly called out there as worth
 * disproportionate effort since these are the assets other sites
 * cite.
 *
 * New top-level section (like /learn/, /best/, /regulation/), not
 * nested under an existing one, because unlike this week's two prior
 * additions (/reviews/{slug}/fees/ reusing the review's own URL
 * space, /regulation/fca-vs-cysec/ reusing the regulator knowledge
 * base's URL space) a cross-broker statistical report isn't about any
 * one broker or regulator -- there's no existing page whose URL space
 * it belongs under. The primary nav's "Research" dropdown
 * (inc/i18n-nav-menu.php's globalfxhub_primary_nav_label_map(),
 * already translated into all 7 languages as 'nav_research') already
 * anticipates exactly this section; it had no link target until now.
 *
 * Every figure in this report is computed directly from the live
 * globalfxhub_get_brokers() dataset (see the verification harness run
 * before this content was written) and frozen into hand-written prose
 * at authoring time -- same precedent as every other hand-curated
 * editorial piece on this site. Re-verify and refresh the numbers
 * whenever broker platform data changes materially.
 *
 * Added one report at a time, same precedent as the broker/regulation
 * comparisons this week -- never a template mechanically generated
 * for every possible cross-section of the dataset.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_research_reports_rewrite_rules() {
    add_rewrite_rule( '^research/([^/]+)/?$', 'index.php?pagename=research&report=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_research_reports_rewrite_rules' );

function globalfxhub_research_reports_query_vars( $vars ) {
    $vars[] = 'report';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_research_reports_query_vars' );

function globalfxhub_get_research_reports() {
    return array(
        'forex-trading-platform-adoption-report' => array(
            'title'            => 'Forex Trading Platform Adoption Report',
            'meta_title'       => 'Forex Trading Platform Adoption Report (2026): MT4, MT5, cTrader and TradingView Across 139 Brokers',
            'meta_description' => 'How many forex brokers still support MT4, how many have moved to MT5, and how rare is cTrader or TradingView support -- an original data study across our full 139-broker research set.',
            'intro'            => "Which trading platforms a broker actually supports rarely gets the same scrutiny as its spread or its regulator -- so we pulled it directly from our own 139-broker research set and counted it. This report covers every confirmed platform across that full set: how common MT4 and MT5 still are, how many brokers run them side by side versus having moved on, and how rare cTrader and TradingView support actually is. Every figure below comes from the same platforms field used throughout this site's reviews, best-broker lists, and comparisons -- nothing here is a separate, newly-sourced claim.",
            'sections' => array(
                array(
                    'heading' => 'MT4 is still the most common platform, but MT5 has nearly caught up',
                    'body'    => "103 of the 139 brokers in our research set (74%) are confirmed to support MT4, and 97 (70%) are confirmed to support MT5. Despite MetaTrader 4 being the older of the two platforms, and despite MetaQuotes having stopped actively developing it in favor of MT5, it remains the more widely supported platform by a modest margin -- brokers have not abandoned MT4 at anywhere near the rate its \"legacy platform\" reputation might suggest.",
                ),
                array(
                    'heading' => 'Most brokers run MT4 and MT5 side by side -- few have actually dropped MT4',
                    'body'    => "84 of the 139 brokers support both MT4 and MT5 at once -- by far the most common setup. Only 13 of the 97 brokers that support MT5 (13%) have dropped MT4 entirely; the other 87% keep both running in parallel. 19 brokers still offer MT4 without having added MT5 at all, and 23 brokers (17% of the full set) offer neither platform, relying instead on cTrader, TradingView, or a proprietary platform of their own.",
                ),
                array(
                    'heading' => 'cTrader and TradingView remain minority platforms',
                    'body'    => "18 of the 139 brokers (13%) are confirmed to support cTrader, and 16 (12%) are confirmed to support TradingView -- both still a clear minority next to MT4/MT5, though far from negligible. The two overlap more than they compete: 7 brokers support both cTrader and TradingView, and only 1 of the 16 TradingView-supporting brokers does so without also offering MT4, meaning TradingView support is almost always additive to a broker's existing MetaTrader offering rather than a replacement for it.",
                ),
                array(
                    'heading' => 'TradingView support correlates strongly with our top-ranked brokers',
                    'body'    => "Splitting the dataset at our top 40 by overall rank shows a real gap: 30% of our top-40 brokers (12 of 40) support TradingView, against just 4% of the remaining 99 (4 brokers). MT4 shows a smaller version of the same pattern -- 83% of the top 40 support it, against 71% of the rest. This is a correlation, not a causal claim: supporting TradingView doesn't itself make a broker better-regulated or cheaper, and our ranking formula doesn't reward platform count directly (platforms is a flat function of how many platforms are confirmed, weighted at 10% of the overall score) -- but the two clearly tend to travel together in this dataset.",
                ),
                array(
                    'heading' => 'The five brokers confirmed to support all four major platforms',
                    'body'    => "Only 5 of the 139 brokers in our research set -- Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital -- are confirmed to support all four of MT4, MT5, cTrader, and TradingView at once. That's a genuinely rare combination: offering every major third-party platform means supporting four separate pieces of trading infrastructure rather than concentrating on one or two, and all five of these brokers happen to also run an ECN/no-dealing-desk-style execution model, though our dataset doesn't let us say whether that's a cause, a correlation, or a coincidence at this sample size.",
                ),
                array(
                    'heading' => 'Proprietary platforms are common individually, but fragmented as a category',
                    'body'    => "Beyond the big four, 22 of the 139 brokers confirm exactly one platform, and 5 of those are MT4-only. The rest of the dataset is a long tail of broker-specific proprietary platforms -- each one typically confirmed for exactly one broker, with no single proprietary platform used by more than a handful of brokers -- so \"proprietary\" is common as a category but fragmented in practice, unlike the real cross-broker standardisation MT4, MT5, cTrader, and TradingView each represent. 12 of the 139 brokers have no platform confirmed in our research at all; we're treating that as an honest data gap rather than guessing, consistent with how every other unconfirmed field on this site is handled.",
                ),
            ),
            'methodology' => array(
                'sample_size'       => "139 brokers -- our full currently-researched set. Every broker we've reviewed is included in every count and percentage in this report; none were excluded or sampled out.",
                'data_as_of'        => "Platform support is reviewed on the same rolling cycle as the rest of each broker's review data, most recently refreshed 8 October 2026.",
                'metric_definition' => '"Confirmed" means the specific platform (MT4, MT5, cTrader, TradingView, or a named proprietary platform) was independently verifiable from the broker\'s own site, account-opening flow, or app-store listing -- never inferred from broker type, account tier, or marketing copy alone.',
                'missing_data'      => '12 of the 139 brokers have no platform confirmed in our research. They stay in every denominator (139) and are counted as "no platform confirmed" rather than guessed at or silently dropped -- unconfirmed platform support is not the same claim as "no platforms," and this report doesn\'t collapse that distinction.',
                'source_hierarchy'  => "Primary: the broker's own platform-download page, account-opening flow, or app-store listing. Secondary (used only where primary confirmation wasn't available): a reputable third-party broker-comparison or review source, as already disclosed on that broker's own review page. A broker's own marketing claim, unverified against either of these, was never treated as confirmation on its own.",
                'compiled'          => '12 November 2026',
            ),
            'faqs' => array(
                array(
                    'q' => 'Is MT4 being phased out by forex brokers?',
                    'a' => 'Not yet, at least not among the brokers we\'ve researched: 74% still support it, and 87% of the brokers that have added MT5 keep MT4 running alongside it rather than dropping it. Only 13% of MT5-supporting brokers in our dataset have moved on from MT4 entirely.',
                ),
                array(
                    'q' => 'Which forex brokers support MT4, MT5, cTrader, and TradingView?',
                    'a' => 'Five brokers in our 139-broker research set are confirmed to support all four: Pepperstone, FP Markets, GO Markets, Fusion Markets, and ICM Capital.',
                ),
                array(
                    'q' => 'Does TradingView support mean a broker is better?',
                    'a' => "Not directly -- our scoring formula doesn't reward platform count on its own. But there's a real correlation in our data: 30% of our top-40-ranked brokers support TradingView, against just 4% of the remaining 99, so platform breadth and overall ranking clearly tend to move together even though one doesn't mechanically cause the other.",
                ),
                array(
                    'q' => 'How was this data collected?',
                    'a' => "From the platforms field already used throughout this site's broker reviews, best-broker lists, and comparisons -- the same 139-broker research set, not a separate survey. Where we couldn't independently confirm a broker's platform support, we counted it as unconfirmed rather than assuming either way; 12 of the 139 brokers fall into that category.",
                ),
            ),
        ),
        'minimum-deposit-report' => array(
            'title'            => 'Minimum Deposit Report',
            'meta_title'       => 'Minimum Deposit Requirements Across 139 Forex Brokers (2026): How Low Can You Actually Start?',
            'meta_description' => "What forex brokers actually require to open an account, from \$0 to \$10,800 -- an original data study across our full 139-broker research set, including which minimums are confirmed and which brokers don't publish one.",
            'intro'            => "How much money it actually takes to open an account rarely gets the same side-by-side treatment as spreads or regulation -- so we pulled the min_deposit_usd field already used throughout this site's reviews and best-broker lists and counted it across the full research set. This report covers every confirmed minimum deposit figure: where the typical broker lands, which brokers require nothing at all, which require four figures, and how much of the dataset doesn't have one single confirmed number at all. Nothing here is newly sourced -- it's the same field already disclosed on each broker's own review page.",
            'sections' => array(
                array(
                    'heading' => 'A $100 median among confirmed brokers, but a quarter require $10 or less',
                    'body'    => "79 of the 139 brokers in our research set (57%) have a confirmed minimum-deposit figure; the median among them is \$100. Underneath that median, though, 26 of those 79 (33%) require under \$50, and 20 (25%) require \$10 or less. The mean is \$415 -- far higher than the median -- because a handful of four-figure outliers pull it up; the median is the more representative number for what a typical account actually costs to open.",
                ),
                array(
                    'heading' => 'Ten brokers confirmed at a flat $0 minimum deposit',
                    'body'    => "ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX, and FXCC all confirm no minimum deposit at all. They span our rank list from 3rd to 100th, so a \$0 minimum tracks with neither being a top performer nor a weak one on its own.",
                ),
                array(
                    'heading' => "A small number of brokers require four-figure minimums -- and they're a different kind of broker",
                    'body'    => "EXANTE (\$10,800) and LMAX (\$10,000) sit well above the rest of the dataset, followed by GBE Brokers and EXCA Prime (both \$1,080) and Swissquote (\$1,000). These aren't retail-first operations competing on accessibility -- EXANTE and LMAX are positioned toward professional/institutional clients, and Swissquote is a Swiss private bank -- so their minimums reflect a different target client, not a worse deal for retail traders.",
                ),
                array(
                    'heading' => 'Minimum deposit carries no weight in our ranking formula, but our better-ranked brokers skew lower anyway',
                    'body'    => "min_deposit_usd isn't one of the nine weighted components in our scoring formula (regulation 30%, cost 20%, non-trading fees 10%, platforms 10%, execution 10%, product range 5%, deposits/withdrawals 5%, transparency 5%, track record 5%) -- a broker's minimum deposit has zero direct effect on its score. Even so, our top 40 ranked brokers (32 with a confirmed figure) show a \$50 median, against a \$100 median for the remaining 99 (47 confirmed) -- and 59% of the top 40's confirmed brokers sit under \$100, against 36% of the rest. That's a correlation, not a causal claim: the same brokers competing hardest on regulation and spread also tend to compete on accessibility, but a low minimum doesn't buy rank.",
                ),
                array(
                    'heading' => '60 of 139 brokers have no single confirmed minimum -- usually because there isn\'t one flat number to confirm',
                    'body'    => "Brokers like Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo, and HYCM don't have a confirmed figure in our research, most often because their real minimum varies by legal entity, account currency, or account type rather than being one number the broker publishes once. We count that honestly as unconfirmed rather than guessing at a figure or defaulting it to \$0.",
                ),
            ),
            'methodology' => array(
                'sample_size'       => "139 brokers -- the same full research set as every other report and comparison on this site.",
                'data_as_of'        => "Minimum deposit is reviewed on the same rolling cycle as the rest of each broker's review data, most recently refreshed 22 November 2026.",
                'metric_definition' => "\"Confirmed\" means a single minimum-deposit figure was independently verifiable from the broker's own account-opening page or published fee/account-types schedule -- never inferred from marketing language like \"start with what you're comfortable with\" or a generic account-tier description.",
                'missing_data'      => "60 of the 139 brokers have no single confirmed minimum-deposit figure in our research, most often because the real minimum varies by legal entity, account currency, or account type rather than being one flat number the broker discloses once. They stay in the full 139 denominator and are counted as unconfirmed rather than defaulted to \$0 or silently dropped.",
                'source_hierarchy'  => "Primary: the broker's own account-opening page or published fee/account-types schedule. Secondary (used only where primary confirmation wasn't available): a reputable third-party broker-comparison or review source, as already disclosed on that broker's own review page. A broker's marketing language alone, unverified against either of these, was never treated as a confirmed figure.",
                'compiled'          => '22 November 2026',
            ),
            'faqs' => array(
                array(
                    'q' => "What's the average minimum deposit required by forex brokers?",
                    'a' => "Across the 79 of 139 brokers in our research set with a confirmed figure, the median is \$100 and 33% require under \$50. The mean (\$415) is pulled much higher by a handful of four-figure outliers aimed at professional or private-banking clients, so the median is the more representative number.",
                ),
                array(
                    'q' => 'Which forex brokers have no minimum deposit?',
                    'a' => 'Ten confirmed: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX, and FXCC.',
                ),
                array(
                    'q' => 'Does a low minimum deposit mean a broker is better?',
                    'a' => "Not directly -- minimum deposit isn't one of the nine weighted components in our ranking formula. But our top-40 confirmed brokers do skew lower (a \$50 median) than the rest of the set (\$100 median), likely because brokers competing hardest on regulation and spread also tend to compete on accessibility.",
                ),
                array(
                    'q' => "Why don't all 139 brokers have a listed minimum deposit?",
                    'a' => "60 don't have one confirmed figure, usually because the actual minimum varies by legal entity, account currency, or account type -- common at multi-jurisdiction brokers -- rather than there being one flat number the broker publishes.",
                ),
            ),
        ),
        'eurusd-spread-report' => array(
            'title'            => 'EUR/USD Spread Report',
            'meta_title'       => 'EUR/USD Spread Report (2026): What Trading Costs Actually Look Like Across 139 Forex Brokers',
            'meta_description' => "The real spread distribution across our full broker dataset -- median, tightest, widest, and which well-known brokers don't publish a confirmed figure at all -- computed from the same field behind every cost score on this site.",
            'intro'            => "A broker's EUR/USD spread is the single number most cost comparisons reduce everything to, but side-by-side spread data across a real, broad set of brokers is rarer than it should be. We pulled the spread_eurusd field already used throughout this site's reviews, cost calculator, and scoring formula and counted it across the full 139-broker set. This report covers the real distribution: where the typical confirmed broker lands, how tight the tightest spreads actually get, and how much of the dataset has no confirmed figure at all.",
            'sections' => array(
                array(
                    'heading' => 'A 1.1-pip median among confirmed brokers, with a third under 1.0 pip',
                    'body'    => "67 of the 139 brokers in our research set (48%) have a confirmed EUR/USD spread; the median among them is 1.1 pips, and the mean is higher still (1.25 pips), pulled up by a handful of wide outliers. Underneath that median, 23 of the 67 (34%) confirm a spread under 1.0 pip, and 6 (9%) confirm under 0.5 pips.",
                ),
                array(
                    'heading' => 'One broker confirmed at a flat 0.0-pip EUR/USD spread -- with a caveat worth stating plainly',
                    'body'    => "FXView is the only broker in our dataset confirmed at a 0.0-pip headline EUR/USD spread. A 0.0-pip headline is common on ECN/raw-style accounts that charge a separate per-lot commission instead of building the cost into the spread -- it doesn't necessarily mean trading is free, just that the cost is structured differently. Confirm the full cost (spread plus any commission) directly before assuming the lowest headline number is automatically the cheapest account.",
                ),
                array(
                    'heading' => 'A handful of brokers confirm spreads of 2 pips or more',
                    'body'    => "LiteForex (2.0), TrioMarkets (2.4), Fortrade (2.5), Trading 212 (2.7), InstaForex (3.0), Trade360 (4.0), and OEXN (5.0) sit well above the rest of the confirmed set. A wider headline spread isn't automatically a red flag -- it can reflect a different account model or client base -- but it's a real, confirmed cost difference worth knowing before comparing these brokers to a tighter-spread one on anything else.",
                ),
                array(
                    'heading' => 'Our better-ranked brokers confirm tighter spreads -- and here the correlation is partly by design',
                    'body'    => "Brokers ranked in our top 40 (31 with a confirmed spread) show a 1.0-pip median, with 48% confirming under 1.0 pip; the remaining 99 (36 confirmed) show a 1.5-pip median, with only 22% under 1.0 pip. Unlike minimum deposit, which carries no weight in our formula, EUR/USD spread feeds directly into our cost score at a 20% weighting -- the second-heaviest single factor after regulation. So this pattern isn't just a correlation we're noting after the fact: a broker with a tighter confirmed spread mechanically scores better on this factor by construction, not merely tends to.",
                ),
                array(
                    'heading' => '72 of 139 brokers have no confirmed EUR/USD spread -- including some well-known, well-regulated names',
                    'body'    => "Interactive Brokers (rank 2), OANDA (rank 6), Vantage Markets, Ultima Markets, CMC Markets, and several others don't have a confirmed figure in our research, most often because of variable or market-maker-style pricing without one published headline number, rather than any gap in how well-regulated or well-known the broker is. Our cost score for these brokers is excluded from their overall average rather than guessed at or defaulted to the dataset's median.",
                ),
            ),
            'methodology' => array(
                'sample_size'       => "139 brokers -- the same full research set as every other report and comparison on this site.",
                'data_as_of'        => "EUR/USD spread is reviewed on the same rolling cycle as the rest of each broker's review data, most recently refreshed 22 November 2026.",
                'metric_definition' => "\"Confirmed\" means a single EUR/USD spread figure, in pips, was independently verifiable from the broker's own pricing page or published spread/account-types schedule -- never inferred from a \"from X pips\" marketing claim without corroboration, and never computed by us from raw tick data.",
                'missing_data'      => "72 of the 139 brokers have no single confirmed EUR/USD spread figure in our research, most often because of variable or market-maker-style pricing without one published headline number. They stay in the full 139 denominator and are counted as unconfirmed rather than defaulted to the dataset's median or silently dropped.",
                'source_hierarchy'  => "Primary: the broker's own pricing page or published spread/account-types schedule. Secondary (used only where primary confirmation wasn't available): a reputable third-party broker-comparison or review source, as already disclosed on that broker's own review page. A broker's \"from X pips\" marketing claim, unverified against either of these, was never treated as a confirmed figure.",
                'compiled'          => '22 November 2026',
            ),
            'faqs' => array(
                array(
                    'q' => "What's the average EUR/USD spread among forex brokers?",
                    'a' => "Across the 67 of 139 brokers in our research set with a confirmed figure, the median is 1.1 pips. The mean (1.25 pips) is pulled higher by a handful of wide outliers, so the median is the more representative number.",
                ),
                array(
                    'q' => 'Which broker has the tightest confirmed EUR/USD spread?',
                    'a' => "FXView, at a flat 0.0 pips -- though a 0.0-pip headline spread often means the cost moved to a separate commission instead, so it isn't automatically the cheapest account overall.",
                ),
                array(
                    'q' => 'Does a lower EUR/USD spread mean a broker scores better?',
                    'a' => 'Yes, directly -- unlike minimum deposit, EUR/USD spread is weighted 20% in our cost score, the second-heaviest single factor in our overall formula after regulation.',
                ),
                array(
                    'q' => "Why don't all 139 brokers have a confirmed EUR/USD spread?",
                    'a' => '72 don\'t, usually because of variable or market-maker-style pricing without one published headline figure -- including some well-known names like Interactive Brokers and OANDA.',
                ),
            ),
        ),
        'regulatory-licence-concentration-report' => array(
            'title'            => 'Regulatory Licence Concentration Report',
            'meta_title'       => 'Regulatory Licence Concentration Report (2026): How Many Tier-1 Licences Do Top Brokers Actually Hold?',
            'meta_description' => 'How confirmed Tier-1 regulatory licences are actually distributed across 139 forex brokers -- a median of 1, a handful holding 6 or more, and how heavily that concentrates among top-ranked brokers.',
            'intro'            => "Our regulation score caps out at 5.0 once a broker crosses a fixed threshold of confirmed Tier-1 regulators -- useful for ranking, but it flattens real differences above that threshold. This report looks at the raw, uncapped count instead: how many independently-verifiable Tier-1 licences (CySEC, FCA, and the other-regulators field used throughout this site) each of our 139 researched brokers actually holds, and how unevenly that's distributed.",
            'sections' => array(
                array(
                    'heading' => 'A median of 1 Tier-1 licence, with half the dataset holding one or none',
                    'body'    => 'Across all 139 brokers, the median confirmed Tier-1 licence count is 1, and the mean is 1.86 -- pulled up by a handful of brokers holding considerably more. 70 of the 139 brokers (50%) hold one Tier-1 licence or none at all, while 38 (27%) hold three or more.',
                ),
                array(
                    'heading' => '20 brokers confirm zero Tier-1 licences at all',
                    'body'    => "14% of our researched set holds no CySEC, FCA, or other-named Tier-1 regulator we could confirm. That doesn't necessarily mean entirely unregulated -- some of these brokers hold an offshore-only licence (Seychelles FSA and similar), which this site tracks and discloses separately but doesn't count toward the Tier-1 tally, consistent with how our regulation score treats offshore-only credentials.",
                ),
                array(
                    'heading' => 'Tier-1 licences concentrate heavily among top-ranked brokers',
                    'body'    => 'Across the full dataset, there are 259 total confirmed Tier-1 licence-holdings. Our top 15 ranked brokers (11% of the set) hold 65 of them -- 25% of the total, averaging 4.33 each. Our top 40 (29% of the set) hold 140 -- 54% of the total, averaging 3.5 each. The remaining 99 brokers (71% of the set) hold just 119 -- 46% of the total, averaging 1.2 each.',
                ),
                array(
                    'heading' => 'Interactive Brokers holds more confirmed Tier-1 licences than any other broker we\'ve researched',
                    'body'    => 'Interactive Brokers confirms 8 -- the highest in our dataset. ThinkMarkets and Plus500 follow at 6 each, with AvaTrade and OANDA at 5. No other broker we\'ve researched confirms more than 4.',
                ),
                array(
                    'heading' => "Why this count isn't capped the way our regulation score is",
                    'body'    => "Our regulation score caps at 5.0 out of 5 once a broker's confirmed Tier-1 count reaches 5 -- so Interactive Brokers' 8, ThinkMarkets' and Plus500's 6, and AvaTrade's and OANDA's 5 all score identically on regulation despite real differences in raw licence count above that threshold. That's a deliberate methodology choice (diminishing returns past a point, rather than rewarding licence-collecting indefinitely), but it means the score alone doesn't show what this report does.",
                ),
            ),
            'methodology' => array(
                'sample_size'       => "139 brokers -- the same full research set as every other report and comparison on this site.",
                'data_as_of'        => "Regulatory licence counts are reviewed on the same rolling cycle as the rest of each broker's review data, most recently refreshed 22 November 2026.",
                'metric_definition' => 'A Tier-1 licence count sums three fields already used in our disclosed scoring formula: a confirmed CySEC licence (1 if held), a confirmed FCA licence or disclosed FCA-linked entity (1 if held), and the broker\'s other-regulators count (named Tier-1 regulators such as ASIC, FSCA, DFSA, or CMA). Offshore-only licences (Seychelles FSA and similar) are tracked separately on this site and are not included in this count, matching how our regulation score treats them.',
                'missing_data'      => "This count only includes what we could independently confirm; it never estimates or assumes a licence a broker hasn't disclosed or that we couldn't verify against a regulator's own register or the broker's own disclosures.",
                'source_hierarchy'  => "Primary: the relevant regulator's own public register. Secondary (used only where primary confirmation wasn't available): the broker's own disclosures, as already reflected in that broker's review page. A broker's own marketing claim about its regulatory footprint, unverified against either of these, was never treated as a confirmed licence.",
                'compiled'          => '22 November 2026',
            ),
            'faqs' => array(
                array(
                    'q' => "What's the average number of Tier-1 regulatory licences a forex broker holds?",
                    'a' => 'Across our full 139-broker research set, the median is 1 and the mean is 1.86 -- the mean is higher because a handful of brokers hold considerably more than the typical one.',
                ),
                array(
                    'q' => 'Which broker holds the most confirmed Tier-1 licences?',
                    'a' => 'Interactive Brokers, with 8 confirmed -- the highest in our dataset. ThinkMarkets and Plus500 follow at 6 each.',
                ),
                array(
                    'q' => 'Do top-ranked brokers hold more Tier-1 licences?',
                    'a' => 'Yes, clearly. Our top 15 ranked brokers hold 25% of all 259 confirmed Tier-1 licence-holdings in our dataset; our top 40 hold 54%. The remaining 99 brokers hold the other 46%.',
                ),
                array(
                    'q' => "Does holding more than 5 Tier-1 licences improve a broker's regulation score further?",
                    'a' => "No -- our regulation score caps at 5.0 once a broker's confirmed count reaches 5, so a broker with 8 scores the same on regulation as one with exactly 5. This report tracks the raw count beyond that cap, which the score itself doesn't show.",
                ),
            ),
        ),
    );
}

/**
 * Per-language translations, same shape/purpose as the other two
 * hand-curated-content translation overlays added this week --
 * filled in once i18n/translate_research_reports.py's real
 * translate+review run completes for a given key.
 */
function globalfxhub_research_report_translations() {
    return array(
        'forex-trading-platform-adoption-report' => array(
            'es' => array(
                'title' => 'Informe sobre la adopción de plataformas de trading de forex',
                'meta_title' => 'Informe sobre la adopción de plataformas de trading de forex (2026): MT4, MT5, cTrader y TradingView en 139 brókers',
                'meta_description' => '¿Cuántos brókers de forex siguen ofreciendo MT4, cuántos han pasado a MT5 y qué tan poco frecuente es el soporte de cTrader o TradingView? Un estudio de datos original sobre nuestro conjunto completo de 139 brókers analizados.',
                'intro' => 'Las plataformas de trading que admite realmente un bróker rara vez reciben el mismo escrutinio que su spread o su regulador, así que las extrajimos directamente de nuestro propio conjunto de 139 brókers analizados y las contamos. Este informe cubre todas las plataformas confirmadas en ese conjunto completo: qué tan comunes siguen siendo MT4 y MT5, cuántos brókers las ofrecen en paralelo frente a los que ya las dejaron atrás y qué tan poco frecuente es realmente el soporte de cTrader y TradingView. Todas las cifras siguientes provienen del mismo campo de plataformas que se utiliza en los análisis, listas de mejores brókers y comparativas de este sitio: ninguna constituye una afirmación nueva ni procede de una fuente separada.',
                'section_1_heading' => 'MT4 sigue siendo la plataforma más común, pero MT5 casi la ha alcanzado',
                'section_1_body' => '103 de los 139 brókers de nuestro conjunto de análisis (74%) tienen confirmado el soporte de MT4, y 97 (70%) tienen confirmado el soporte de MT5. Aunque MetaTrader 4 sea la más antigua de las dos plataformas, y aunque MetaQuotes haya dejado de desarrollarla activamente para centrarse en MT5, sigue siendo la más ampliamente admitida, por un margen modesto: los brókers no han abandonado MT4 ni de lejos al ritmo que podría sugerir su reputación de «plataforma heredada».',
                'section_2_heading' => 'La mayoría de los brókers ejecutan MT4 y MT5 en paralelo; pocos han eliminado realmente MT4',
                'section_2_body' => '84 de los 139 brókers admiten MT4 y MT5 al mismo tiempo, con diferencia la configuración más habitual. Solo 13 de los 97 brókers que admiten MT5 (13%) han eliminado por completo MT4; el 87% restante mantiene ambas plataformas en funcionamiento paralelo. 19 brókers siguen ofreciendo MT4 sin haber añadido nunca MT5, y 23 brókers (17% del conjunto completo) no ofrecen ninguna de las dos, recurriendo a cTrader, TradingView o una plataforma propia.',
                'section_3_heading' => 'cTrader y TradingView siguen siendo plataformas minoritarias',
                'section_3_body' => '18 de los 139 brókers (13%) tienen confirmado el soporte de cTrader, y 16 (12%) tienen confirmado el soporte de TradingView; ambas siguen siendo una clara minoría frente a MT4/MT5, aunque están lejos de ser insignificantes. Se solapan más de lo que compiten: 7 brókers admiten tanto cTrader como TradingView, y solo 1 de los 16 brókers que admiten TradingView lo hace sin ofrecer también MT4. Lo que significa que el soporte de TradingView casi siempre se suma a la oferta de MetaTrader existente de un bróker, en lugar de sustituirla.',
                'section_4_heading' => 'El soporte de TradingView se correlaciona fuertemente con nuestros brókers mejor clasificados',
                'section_4_body' => 'Al dividir el conjunto de datos entre nuestros 40 brókers mejor clasificados según el ranking general, se observa una diferencia real: el 30% de nuestros 40 mejores brókers (12 de 40) admite TradingView, frente a solo el 4% de los 99 restantes (4 brókers). MT4 muestra una versión más pequeña del mismo patrón: el 83% de los 40 primeros lo admite, frente al 71% del resto. Se trata de una correlación, no de una afirmación causal: el soporte de TradingView no convierte por sí mismo a un bróker en uno mejor regulado o más barato, y nuestra fórmula de puntuación no premia directamente el número de plataformas (el campo de plataformas es una función sencilla de cuántas plataformas están confirmadas, con un peso del 10% en la puntuación general). Aun así, en este conjunto de datos, ambas variables tienden claramente a ir de la mano.',
                'section_5_heading' => 'Los cinco brókers con confirmación de soporte para las cuatro plataformas principales',
                'section_5_body' => 'Solo 5 de los 139 brókers de nuestro conjunto de análisis (Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital) tienen confirmado el soporte simultáneo de MT4, MT5, cTrader y TradingView. Es una combinación genuinamente poco frecuente: ofrecer todas las grandes plataformas de terceros significa dar soporte a cuatro infraestructuras de trading distintas, en lugar de concentrarse en una o dos. Además, los cinco brókers también utilizan un modelo de ejecución de tipo ECN/sin dealing desk, aunque nuestro conjunto de datos no nos permite determinar si eso es una causa, una correlación o una coincidencia con este tamaño de muestra.',
                'section_6_heading' => 'Las plataformas propias son comunes de forma individual, pero fragmentadas como categoría',
                'section_6_body' => 'Más allá de las cuatro grandes, 22 de los 139 brókers confirman exactamente una plataforma, y 5 de ellos solo ofrecen MT4. El resto del conjunto de datos es una larga cola de plataformas propias de cada bróker: cada una suele estar confirmada para un único bróker, sin que ninguna plataforma propia sea utilizada por más de un puñado de brókers. Por eso, «propia» es una categoría frecuente, pero fragmentada en la práctica, a diferencia de la verdadera estandarización entre brókers que representan MT4, MT5, cTrader y TradingView. 12 de los 139 brókers no tienen ninguna plataforma confirmada en nuestro análisis; tratamos esto como una laguna de datos honesta en lugar de adivinar, en línea con el modo en que se gestiona cualquier otro campo no confirmado de este sitio.',
                'faq_1_q' => '¿Están los brókers de forex dejando de ofrecer MT4?',
                'faq_1_a' => 'Todavía no, al menos entre los brókers que hemos analizado: el 74% sigue admitiéndola, y el 87% de los brókers que han añadido MT5 mantiene MT4 en funcionamiento junto a ella en lugar de eliminarla. Solo el 13% de los brókers de nuestro conjunto de datos que admiten MT5 han dejado MT4 por completo.',
                'faq_2_q' => '¿Qué brókers de forex admiten MT4, MT5, cTrader y TradingView?',
                'faq_2_a' => 'Cinco brókers de nuestro conjunto de 139 analizados tienen confirmado el soporte de las cuatro: Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital.',
                'faq_3_q' => '¿Significa el soporte de TradingView que un bróker es mejor?',
                'faq_3_a' => 'No directamente: nuestra fórmula de puntuación no premia por sí sola el número de plataformas. Pero en nuestros datos existe una correlación real: el 30% de nuestros 40 brókers mejor clasificados admite TradingView, frente a solo el 4% de los 99 restantes. Así, la variedad de plataformas y el ranking general claramente tienden a moverse juntos, aunque una cosa no cause mecánicamente la otra.',
                'faq_4_q' => '¿Cómo se recopilaron estos datos?',
                'faq_4_a' => 'A partir del campo de plataformas que ya se utiliza en los análisis de brókers, listas de mejores brókers y comparativas de este sitio: el mismo conjunto de 139 brókers analizados, no una encuesta independiente. Cuando no pudimos confirmar de forma independiente el soporte de plataformas de un bróker, lo contamos como no confirmado en lugar de suponer en ningún sentido; 12 de los 139 brókers entran en esa categoría.',
            ),
            'de' => array(
                'title' => 'Bericht zur Verbreitung von Forex-Handelsplattformen',
                'meta_title' => 'Bericht zur Verbreitung von Forex-Handelsplattformen (2026): MT4, MT5, cTrader und TradingView bei 139 Brokern',
                'meta_description' => 'Wie viele Forex-Broker noch MT4 unterstützen, wie viele auf MT5 umgestiegen sind und wie selten die Unterstützung von cTrader oder TradingView ist -- eine eigenständige Datenstudie über unser gesamtes Recherche-Set mit 139 Brokern.',
                'intro' => 'Welche Handelsplattformen ein Broker tatsächlich unterstützt, wird selten so genau geprüft wie sein Spread oder seine Regulierung -- deshalb haben wir diese Angaben direkt aus unserem eigenen Recherche-Set mit 139 Brokern erhoben und ausgezählt. Dieser Bericht erfasst jede bestätigte Plattform in diesem gesamten Set: wie verbreitet MT4 und MT5 noch sind, wie viele Broker beide parallel betreiben und wie viele bereits darauf verzichtet haben, und wie selten die Unterstützung von cTrader und TradingView tatsächlich ist. Alle folgenden Zahlen stammen aus demselben Feld zu Plattformen, das auf dieser Website in allen Testberichten, Listen der besten Broker und Vergleichen verwendet wird -- hier wird also nichts Neues oder separat Belegtes behauptet.',
                'section_1_heading' => 'MT4 ist nach wie vor die verbreitetste Plattform, doch MT5 hat fast aufgeholt',
                'section_1_body' => '103 der 139 Broker in unserem Recherche-Set (74%) unterstützen nachweislich MT4, und 97 (70%) unterstützen nachweislich MT5. Obwohl MetaTrader 4 die ältere der beiden Plattformen ist und MetaQuotes die Weiterentwicklung zugunsten von MT5 eingestellt hat, wird MT4 mit einem knappen Vorsprung weiterhin häufiger unterstützt -- die Broker haben MT4 bei Weitem nicht in dem Maße aufgegeben, wie es der Ruf als "veraltete Plattform" vermuten ließe.',
                'section_2_heading' => 'Die meisten Broker betreiben MT4 und MT5 parallel -- nur wenige haben MT4 tatsächlich abgeschafft',
                'section_2_body' => '84 der 139 Broker unterstützen MT4 und MT5 gleichzeitig -- mit Abstand die häufigste Konstellation. Nur 13 der 97 Broker, die MT5 unterstützen (13%), haben MT4 vollständig eingestellt; die übrigen 87% betreiben beide Plattformen parallel. 19 Broker bieten weiterhin MT4 an, ohne MT5 hinzugefügt zu haben, und 23 Broker (17% des gesamten Sets) bieten keine der beiden Plattformen an und setzen stattdessen auf cTrader, TradingView oder eine eigene proprietäre Plattform.',
                'section_3_heading' => 'cTrader und TradingView bleiben Minderheitenplattformen',
                'section_3_body' => '18 der 139 Broker (13%) unterstützen nachweislich cTrader, und 16 (12%) unterstützen nachweislich TradingView -- beide bleiben neben MT4/MT5 eindeutig in der Minderheit, sind aber alles andere als unbedeutend. Die beiden überschneiden sich stärker, als sie miteinander konkurrieren: 7 Broker unterstützen sowohl cTrader als auch TradingView, und nur 1 der 16 Broker mit TradingView-Unterstützung tut dies, ohne zugleich MT4 anzubieten. Die TradingView-Unterstützung ergänzt also fast immer das bestehende MetaTrader-Angebot eines Brokers, anstatt es zu ersetzen.',
                'section_4_heading' => 'Die Unterstützung von TradingView korreliert stark mit unseren bestplatzierten Brokern',
                'section_4_body' => 'Teilt man den Datensatz anhand unserer Top 40 nach Gesamtrang auf, zeigt sich eine deutliche Lücke: 30% unserer Top-40-Broker (12 von 40) unterstützen TradingView, gegenüber nur 4% der übrigen 99 (4 Broker). MT4 zeigt ein kleineres Abbild desselben Musters -- 83% der Top 40 unterstützen es, gegenüber 71% der übrigen. Dies ist eine Korrelation, keine kausale Aussage: Die Unterstützung von TradingView macht einen Broker nicht selbst besser reguliert oder günstiger, und unsere Bewertungsformel belohnt die Anzahl der Plattformen nicht direkt (das Kriterium Plattformen ist eine einfache Funktion der Anzahl bestätigter Plattformen und fließt mit 10% in die Gesamtbewertung ein) -- doch in diesem Datensatz tendieren beide Merkmale klar dazu, gemeinsam aufzutreten.',
                'section_5_heading' => 'Die fünf Broker, deren Unterstützung aller vier großen Plattformen bestätigt ist',
                'section_5_body' => 'Nur 5 der 139 Broker in unserem Recherche-Set -- Pepperstone, FP Markets, GO Markets, Fusion Markets und ICM Capital -- unterstützen nachweislich alle vier Plattformen MT4, MT5, cTrader und TradingView gleichzeitig. Das ist eine tatsächlich seltene Kombination: Jede große Drittanbieter-Plattform anzubieten bedeutet, vier getrennte Handelsinfrastrukturen zu unterstützen, statt sich auf ein oder zwei zu konzentrieren. Alle fünf dieser Broker betreiben zudem ein Ausführungsmodell im Stil eines ECN bzw. ohne Dealing Desk. Ob dies eine Ursache, eine Korrelation oder ein Zufall ist, lässt sich bei dieser Stichprobengröße mit unserem Datensatz jedoch nicht sagen.',
                'section_6_heading' => 'Proprietäre Plattformen sind einzeln verbreitet, als Kategorie jedoch zersplittert',
                'section_6_body' => 'Abseits der großen vier bestätigen 22 der 139 Broker genau eine Plattform, und 5 davon bieten ausschließlich MT4. Der Rest des Datensatzes besteht aus einer langen Reihe brokerspezifischer proprietärer Plattformen -- jede davon typischerweise nur bei einem einzigen Broker bestätigt, wobei keine einzelne proprietäre Plattform von mehr als einigen wenigen Brokern genutzt wird. "Proprietär" ist als Kategorie also weit verbreitet, in der Praxis aber zersplittert, anders als die echte brokerübergreifende Standardisierung, die MT4, MT5, cTrader und TradingView jeweils darstellen. 12 der 139 Broker haben in unserer Recherche gar keine Plattform bestätigt; wir werten dies als ehrliche Datenlücke statt zu raten, im Einklang damit, wie wir jedes andere unbestätigte Feld auf dieser Website behandeln.',
                'faq_1_q' => 'Wird MT4 von Forex-Brokern allmählich abgeschafft?',
                'faq_1_a' => 'Noch nicht, zumindest nicht bei den von uns recherchierten Brokern: 74% unterstützen es weiterhin, und 87% der Broker, die MT5 ergänzt haben, betreiben MT4 weiterhin parallel, anstatt es abzuschalten. Nur 13% der MT5-unterstützenden Broker in unserem Datensatz haben MT4 vollständig hinter sich gelassen.',
                'faq_2_q' => 'Welche Forex-Broker unterstützen MT4, MT5, cTrader und TradingView?',
                'faq_2_a' => 'Fünf Broker in unserem Recherche-Set mit 139 Brokern unterstützen nachweislich alle vier: Pepperstone, FP Markets, GO Markets, Fusion Markets und ICM Capital.',
                'faq_3_q' => 'Bedeutet die Unterstützung von TradingView, dass ein Broker besser ist?',
                'faq_3_a' => 'Nicht direkt -- unsere Bewertungsformel belohnt die Anzahl der Plattformen nicht für sich allein. Doch in unseren Daten gibt es eine echte Korrelation: 30% unserer Top-40-Broker unterstützen TradingView, gegenüber nur 4% der übrigen 99. Die Breite des Plattformangebots und der Gesamtrang tendieren also klar gemeinsam dazu, obwohl das eine das andere nicht mechanisch verursacht.',
                'faq_4_q' => 'Wie wurden diese Daten erhoben?',
                'faq_4_a' => 'Aus dem Feld zu Plattformen, das auf dieser Website in allen Testberichten, Listen der besten Broker und Vergleichen verwendet wird -- also aus demselben Recherche-Set mit 139 Brokern, nicht aus einer separaten Umfrage. Konnten wir die Plattformunterstützung eines Brokers nicht unabhängig bestätigen, haben wir ihn als unbestätigt gezählt, statt in die eine oder andere Richtung zu annehmen; 12 der 139 Broker fallen in diese Kategorie.',
            ),
            'ar' => array(
                'title' => 'تقرير اعتماد منصات تداول الفوركس',
                'meta_title' => 'تقرير اعتماد منصات تداول الفوركس (2026): MT4 وMT5 وcTrader وTradingView عبر 139 وسيطًا',
                'meta_description' => 'كم عدد وسطاء الفوركس الذين ما زالوا يدعمون MT4، وكم منهم انتقل إلى MT5، ومدى ندرة دعم cTrader أو TradingView -- دراسة بيانات أصلية تشمل مجموعة أبحاثنا الكاملة المكونة من 139 وسيطًا.',
                'intro' => 'نادرًا ما تحظى المنصات التي يدعمها الوسيط بالقدر نفسه من التدقيق الذي يحظى به الفرق السعري (سبريد) أو الجهة المنظمة له، لذلك استخرجنا هذه البيانات مباشرة من مجموعة أبحاثنا المكونة من 139 وسيطًا وأحصيناها. يغطي هذا التقرير كل منصة مؤكدة في المجموعة الكاملة: مدى انتشار MT4 وMT5 حتى الآن، وعدد الوسطاء الذين يشغّلونهما جنبًا إلى جنب مقابل من تخلّوا عنهما، ومدى ندرة دعم cTrader وTradingView فعليًا. وتأتي جميع الأرقام الواردة أدناه من حقل المنصات نفسه المستخدم في جميع مراجعات الموقع وقوائم أفضل الوسطاء والمقارنات، ولا يوجد هنا أي ادعاء جديد أو مصدر منفصل.',
                'section_1_heading' => 'يبقى MT4 المنصة الأكثر شيوعًا، لكن MT5 قارب اللحاق به',
                'section_1_body' => 'يُؤكَّد دعم 103 من أصل 139 وسيطًا في مجموعة أبحاثنا (74%) لمنصة MT4، ويُؤكَّد دعم 97 وسيطًا (70%) لمنصة MT5. وعلى الرغم من أن MetaTrader 4 هي الأقدم بين المنصتين، وعلى الرغم من توقف MetaQuotes عن التطوير النشط لها لصالح MT5، فإنها ما زالت الأوسع دعمًا بفارق طفيف، إذ لم يتخلَّ الوسطاء عن MT4 بالمعدل الذي قد يوحي به وصفها بـ«المنصة القديمة».',
                'section_2_heading' => 'يشغّل معظم الوسطاء MT4 وMT5 جنبًا إلى جنب، وقلة منهم تخلّت فعليًا عن MT4',
                'section_2_body' => 'يدعم 84 وسيطًا من أصل 139 المنصتين MT4 وMT5 معًا، وهو الترتيب الأكثر شيوعًا بفارق كبير. ولم يتخلَّ عن MT4 تمامًا سوى 13 وسيطًا من أصل 97 وسيطًا يدعمون MT5 (13%)، بينما يحتفظ الباقون بالمنصتين تعملان بالتوازي (87%). ويقدّم 19 وسيطًا منصة MT4 دون أن يضيفوا MT5 مطلقًا، ولا يقدّم 23 وسيطًا (17% من المجموعة الكاملة) أيًّا من المنصتين، بل يعتمدون بدلًا من ذلك على cTrader أو TradingView أو منصة خاصة بهم.',
                'section_3_heading' => 'تبقى cTrader وTradingView منصتين للأقلية من الوسطاء',
                'section_3_body' => 'يُؤكَّد دعم 18 وسيطًا من أصل 139 (13%) لمنصة cTrader، ويُؤكَّد دعم 16 وسيطًا (12%) لمنصة TradingView، وهما ما زالتا أقلية واضحة مقارنة بـ MT4/MT5، رغم أنهما ليستا هامشيتين. والمنصتان تتداخلان أكثر مما تتنافسان: يدعم 7 وسطاء المنصتين cTrader وTradingView معًا، ولا يقدّم سوى وسيط واحد من أصل 16 وسيطًا يدعمون TradingView، دون أن يقدّم MT4 أيضًا، ما يعني أن دعم TradingView يكاد يكون دائمًا إضافة إلى عرض MetaTrader القائم لدى الوسيط، لا بديلًا عنه.',
                'section_4_heading' => 'يرتبط دعم TradingView ارتباطًا قويًا بأفضل وسطائنا تصنيفًا',
                'section_4_body' => 'عند تقسيم مجموعة البيانات بين أفضل 40 وسيطًا وفق الترتيب العام وبقية المجموعة، يظهر فرق حقيقي: 30% من أفضل 40 وسيطًا لدينا (12 من 40) يدعمون TradingView، مقابل 4% فقط من الـ 99 المتبقين (4 وسطاء). ويظهر MT4 النمط نفسه بصورة أضعف، إذ يدعمه 83% من أفضل 40 مقابل 71% من الباقين. وهذا ارتباط وليس علاقة سببية: فدعم TradingView لا يجعل الوسيط أفضل تنظيمًا أو أرخص بذاته، كما أن معادلة الترتيب لدينا لا تكافئ عدد المنصات مباشرة (إذ يُحتسب حقل المنصات بوصفه دالة ثابتة لعدد المنصات المؤكدة، بوزن 10% من التقييم الإجمالي)، لكن الأمرين يميلان بوضوح إلى الظهور معًا في هذه البيانات.',
                'section_5_heading' => 'الوسطاء الخمسة المؤكد دعمهم للمنصات الأربع الرئيسية',
                'section_5_body' => 'من بين 139 وسيطًا في مجموعة أبحاثنا، لا يؤكد دعم جميع المنصات الأربع MT4 وMT5 وcTrader وTradingView معًا سوى 5 وسطاء، هم Pepperstone وFP Markets وGO Markets وFusion Markets وICM Capital. وهذه تركيبة نادرة فعلًا: فتقديم كل منصة رئيسية من أطراف ثالثة يعني دعم أربع بنى تحتية مستقلة للتداول بدلًا من التركيز على واحدة أو اثنتين، ويعمل جميع هؤلاء الوسطاء الخمسة أيضًا بنموذج تنفيذ من نوع ECN/بلا مكتب تعامل، غير أن بياناتنا لا تتيح لنا تحديد ما إذا كان ذلك سببًا أو ارتباطًا أو مصادفة عند حجم العينة الحالي.',
                'section_6_heading' => 'المنصات الخاصة شائعة بشكل فردي، لكنها مجزّأة كفئة',
                'section_6_body' => 'بعيدًا عن المنصات الأربع الكبرى، يؤكد 22 وسيطًا من أصل 139 منصة واحدة فقط، ومن بينهم 5 وسطاء يقدّمون MT4 فقط. أما بقية المجموعة فهي ذيل طويل من المنصات الخاصة بكل وسيط، إذ تُؤكَّد كل منصة منها عادةً لوسيط واحد، ولا تستخدم أي منصة خاصة أكثر من حفنة من الوسطاء، لذلك فإن «الخاصة» شائعة كفئة لكنها مجزّأة عمليًا، على عكس التوحيد الحقيقي بين الوسطاء الذي تمثله كل من MT4 وMT5 وcTrader وTradingView. ولا تتوفر معلومات عن المنصات لدى 12 وسيطًا من أصل 139 في أبحاثنا؛ ونعتبر ذلك فجوة بيانات صريحة بدلًا من التخمين، بما يتسق مع طريقة التعامل مع أي حقل غير مؤكد على هذا الموقع.',
                'faq_1_q' => 'هل يجري التخلص التدريجي من MT4 لدى وسطاء الفوركس؟',
                'faq_1_a' => 'ليس بعد، على الأقل بين الوسطاء الذين درسناهم: ما زال 74% منهم يدعمونها، ويحتفظ 87% من الوسطاء الذين أضافوا MT5 بـ MT4 تعمل إلى جانبها بدلًا من التخلي عنها. ولم يتخلَّ عن MT4 تمامًا سوى 13% من الوسطاء الداعمين لـ MT5 في مجموعة بياناتنا.',
                'faq_2_q' => 'أي وسطاء الفوركس يدعمون MT4 وMT5 وcTrader وTradingView؟',
                'faq_2_a' => 'يؤكد دعم جميع المنصات الأربع خمسة وسطاء في مجموعة أبحاثنا المكونة من 139 وسيطًا، وهم Pepperstone وFP Markets وGO Markets وFusion Markets وICM Capital.',
                'faq_3_q' => 'هل يعني دعم TradingView أن الوسيط أفضل؟',
                'faq_3_a' => 'ليس بشكل مباشر، فمعادلة التقييم لدينا لا تكافئ عدد المنصات بذاته. لكن هناك ارتباط حقيقي في بياناتنا: يدعم 30% من وسطائنا الـ 40 الأعلى تصنيفًا منصة TradingView، مقابل 4% فقط من الـ 99 المتبقين، لذلك يميل تنوع المنصات والترتيب العام بوضوح إلى التحرك معًا، رغم أن أحدهما لا يسبب الآخر بشكل آلي.',
                'faq_4_q' => 'كيف جُمعت هذه البيانات؟',
                'faq_4_a' => 'من حقل المنصات المستخدم أصلًا في جميع مراجعات وسطاء الموقع وقوائم أفضل الوسطاء والمقارنات، أي مجموعة الأبحاث نفسها المكونة من 139 وسيطًا، وليست استبيانًا منفصلًا. وحين تعذّر علينا التحقق بشكل مستقل من دعم منصة لدى وسيط ما، احتسبناه غير مؤكد بدلًا من افتراض أي شيء؛ ويقع 12 وسيطًا من أصل 139 ضمن هذه الفئة.',
            ),
            'fr' => array(
                'title' => 'Rapport sur l\'adoption des plateformes de trading forex',
                'meta_title' => 'Rapport sur l\'adoption des plateformes de trading forex (2026) : MT4, MT5, cTrader et TradingView chez 139 courtiers',
                'meta_description' => 'Combien de courtiers forex prennent encore en charge MT4, combien sont passés à MT5, et à quel point le support de cTrader ou de TradingView reste rare : une étude de données originale portant sur l\'ensemble de notre base de recherche de 139 courtiers.',
                'intro' => 'Les plateformes de trading qu\'un courtier prend réellement en charge font rarement l\'objet du même examen que son spread ou son régulateur. Nous avons donc extrait ces informations directement de notre propre base de recherche de 139 courtiers et les avons comptabilisées. Ce rapport couvre chaque plateforme confirmée dans l\'ensemble de cette base : la fréquence de MT4 et de MT5, le nombre de courtiers qui les exploitent en parallèle par rapport à ceux qui sont passés à autre chose, ainsi que la rareté réelle du support de cTrader et de TradingView. Tous les chiffres ci-dessous proviennent du même champ « plateformes » utilisé dans l\'ensemble des analyses, des listes de meilleurs courtiers et des comparatifs de ce site. Rien ici ne constitue une affirmation distincte ou nouvellement sourcée.',
                'section_1_heading' => 'MT4 reste la plateforme la plus répandue, mais MT5 a presque rattrapé son retard',
                'section_1_body' => '103 des 139 courtiers de notre base de recherche (74 %) sont confirmés comme prenant en charge MT4, et 97 (70 %) sont confirmés comme prenant en charge MT5. Bien que MetaTrader 4 soit la plus ancienne des deux plateformes, et bien que MetaQuotes ait cessé de la développer activement au profit de MT5, elle reste la plus largement prise en charge, avec une marge modeste. Les courtiers n\'ont pas abandonné MT4 au rythme que sa réputation de « plateforme héritée » pourrait laisser supposer.',
                'section_2_heading' => 'La plupart des courtiers exploitent MT4 et MT5 en parallèle, rares sont ceux qui ont réellement abandonné MT4',
                'section_2_body' => '84 des 139 courtiers prennent en charge MT4 et MT5 simultanément, ce qui constitue de loin la configuration la plus courante. Seuls 13 des 97 courtiers prenant en charge MT5 (13 %) ont entièrement abandonné MT4 ; les 87 % restants conservent les deux plateformes en parallèle. 19 courtiers proposent encore MT4 sans avoir ajouté MT5, et 23 courtiers (17 % de l\'ensemble) ne proposent aucune de ces deux plateformes, s\'appuyant plutôt sur cTrader, TradingView ou une plateforme propriétaire.',
                'section_3_heading' => 'cTrader et TradingView restent des plateformes minoritaires',
                'section_3_body' => '18 des 139 courtiers (13 %) sont confirmés comme prenant en charge cTrader, et 16 (12 %) sont confirmés comme prenant en charge TradingView. Ces deux plateformes restent nettement minoritaires face à MT4 et MT5, sans pour autant être négligeables. Elles se recoupent davantage qu\'elles ne se concurrencent : 7 courtiers prennent en charge à la fois cTrader et TradingView, et seul 1 des 16 courtiers proposant TradingView le fait sans proposer également MT4. Le support de TradingView vient donc presque toujours en complément de l\'offre MetaTrader existante d\'un courtier, et non en remplacement.',
                'section_4_heading' => 'Le support de TradingView est fortement corrélé à nos courtiers les mieux classés',
                'section_4_body' => 'En scindant la base selon nos 40 premiers courtiers au classement général, on observe un écart réel : 30 % de nos 40 meilleurs courtiers (12 sur 40) prennent en charge TradingView, contre seulement 4 % des 99 autres (4 courtiers). MT4 présente une version plus modeste du même schéma : 83 % des 40 premiers le prennent en charge, contre 71 % des autres. Il s\'agit d\'une corrélation et non d\'un lien de causalité : prendre en charge TradingView ne rend pas, en soi, un courtier mieux régulé ni moins cher, et notre formule de classement ne récompense pas directement le nombre de plateformes (le champ « plateformes » est une fonction simple du nombre de plateformes confirmées, pondérée à 10 % de la note globale). Ces deux éléments tendent néanmoins clairement à aller de pair dans cette base.',
                'section_5_heading' => 'Les cinq courtiers confirmés comme prenant en charge les quatre grandes plateformes',
                'section_5_body' => 'Seuls 5 des 139 courtiers de notre base de recherche (Pepperstone, FP Markets, GO Markets, Fusion Markets et ICM Capital) sont confirmés comme prenant en charge simultanément les quatre plateformes MT4, MT5, cTrader et TradingView. Il s\'agit d\'une combinaison véritablement rare : proposer chaque grande plateforme tierce implique de faire fonctionner quatre infrastructures de trading distinctes plutôt que de se concentrer sur une ou deux. Par ailleurs, ces cinq courtiers exploitent tous un modèle d\'exécution de type ECN/sans dealing desk. Notre base ne permet toutefois pas de dire s\'il s\'agit d\'une cause, d\'une corrélation ou d\'une coïncidence, compte tenu de la taille de l\'échantillon.',
                'section_6_heading' => 'Les plateformes propriétaires sont courantes individuellement, mais fragmentées en tant que catégorie',
                'section_6_body' => 'Au-delà des quatre grandes, 22 des 139 courtiers ne confirment qu\'une seule plateforme, et 5 d\'entre eux ne proposent que MT4. Le reste de la base se compose d\'une longue traîne de plateformes propriétaires propres à chaque courtier, chacune n\'étant généralement confirmée que pour un seul courtier, aucune plateforme propriétaire n\'étant utilisée par plus d\'une poignée de courtiers. Le terme « propriétaire » désigne donc une catégorie courante, mais fragmentée dans les faits, contrairement à la véritable standardisation inter-courtiers que représentent MT4, MT5, cTrader et TradingView. 12 des 139 courtiers n\'ont aucune plateforme confirmée dans notre recherche. Nous considérons cela comme une lacune honnête des données plutôt que de deviner, conformément à la manière dont tout autre champ non confirmé est traité sur ce site.',
                'faq_1_q' => 'Les courtiers forex abandonnent-ils progressivement MT4 ?',
                'faq_1_a' => 'Pas encore, du moins parmi les courtiers que nous avons étudiés : 74 % prennent encore en charge MT4, et 87 % des courtiers ayant ajouté MT5 conservent MT4 en parallèle plutôt que de l\'abandonner. Seuls 13 % des courtiers prenant en charge MT5 dans notre base ont entièrement abandonné MT4.',
                'faq_2_q' => 'Quels courtiers forex prennent en charge MT4, MT5, cTrader et TradingView ?',
                'faq_2_a' => 'Cinq courtiers de notre base de 139 courtiers sont confirmés comme prenant en charge les quatre : Pepperstone, FP Markets, GO Markets, Fusion Markets et ICM Capital.',
                'faq_3_q' => 'Le support de TradingView signifie-t-il qu\'un courtier est meilleur ?',
                'faq_3_a' => 'Pas directement : notre formule de notation ne récompense pas le nombre de plateformes en soi. Mais nos données révèlent une corrélation réelle : 30 % de nos courtiers les mieux classés (top 40) prennent en charge TradingView, contre seulement 4 % des 99 autres. La diversité des plateformes et le classement général tendent donc clairement à aller de pair, même si l\'un ne cause pas mécaniquement l\'autre.',
                'faq_4_q' => 'Comment ces données ont-elles été collectées ?',
                'faq_4_a' => 'À partir du champ « plateformes » déjà utilisé dans l\'ensemble des analyses de courtiers, des listes de meilleurs courtiers et des comparatifs de ce site, sur la même base de recherche de 139 courtiers, et non à partir d\'une enquête distincte. Lorsque nous n\'avons pas pu confirmer de manière indépendante la prise en charge de plateformes par un courtier, nous l\'avons comptabilisé comme non confirmé plutôt que de supposer dans un sens ou dans l\'autre. 12 des 139 courtiers entrent dans cette catégorie.',
            ),
            'pl' => array(
                'title' => 'Raport o popularności platform handlowych w forex',
                'meta_title' => 'Raport o popularności platform handlowych w forex (2026): MT4, MT5, cTrader i TradingView u 139 brokerów',
                'meta_description' => 'Ilu brokerów forex nadal obsługuje MT4, ilu przeszło na MT5, a jak rzadkie jest wsparcie dla cTrader lub TradingView -- oryginalne badanie danych obejmujące pełny zbiór 139 brokerów.',
                'intro' => 'To, jakie platformy handlowe faktycznie obsługuje dany broker, rzadko podlega takiej samej kontroli jak jego spread czy regulator -- dlatego zebraliśmy te dane bezpośrednio z naszego własnego zbioru 139 brokerów i je policzyliśmy. Raport obejmuje wszystkie potwierdzone platformy w całym tym zbiorze: jak powszechne są nadal MT4 i MT5, ilu brokerów oferuje je równolegle, a ilu już z nich zrezygnowało, oraz jak rzadkie jest faktycznie wsparcie dla cTrader i TradingView. Wszystkie dane poniżej pochodzą z tego samego pola platforms, którego używamy w recenzjach, zestawieniach najlepszych brokerów i porównaniach na tej stronie -- nie stanowią odrębnych, nowo pozyskanych twierdzeń.',
                'section_1_heading' => 'MT4 nadal jest najpopularniejszą platformą, ale MT5 niemal ją dogonił',
                'section_1_body' => '103 spośród 139 brokerów w naszym zbiorze badawczym (74%) ma potwierdzone wsparcie dla MT4, a 97 (70%) -- dla MT5. Mimo że MetaTrader 4 jest starszą z tych dwóch platform i mimo że firma MetaQuotes przestała aktywnie rozwijać ją na rzecz MT5, wciąż jest ona szerzej obsługiwana, choć jedynie niewielką przewagą -- brokerzy nie porzucili MT4 w tempie, jakiego mogłaby sugerować jej reputacja „platformy przestarzałej”.',
                'section_2_heading' => 'Większość brokerów oferuje MT4 i MT5 równolegle -- niewielu całkowicie zrezygnowało z MT4',
                'section_2_body' => '84 spośród 139 brokerów obsługuje jednocześnie MT4 i MT5 -- zdecydowanie najczęstszy układ. Tylko 13 z 97 brokerów wspierających MT5 (13%) całkowicie zrezygnowało z MT4; pozostałe 87% utrzymuje obie platformy równolegle. 19 brokerów nadal oferuje MT4 bez dodania MT5, a 23 brokerów (17% całego zbioru) nie oferuje żadnej z tych platform, polegając zamiast tego na cTrader, TradingView lub własnej, zastrzeżonej platformie.',
                'section_3_heading' => 'cTrader i TradingView pozostają platformami mniejszościowymi',
                'section_3_body' => '18 spośród 139 brokerów (13%) ma potwierdzone wsparcie dla cTrader, a 16 (12%) -- dla TradingView. Obie platformy wciąż stanowią wyraźną mniejszość obok MT4/MT5, choć nie są znikome. Częściej się uzupełniają, niż konkurują: 7 brokerów obsługuje zarówno cTrader, jak i TradingView, a tylko 1 spośród 16 brokerów wspierających TradingView robi to bez oferowania MT4. Oznacza to, że wsparcie dla TradingView niemal zawsze stanowi dodatek do istniejącej oferty MetaTrader, a nie jej zamiennik.',
                'section_4_heading' => 'Wsparcie dla TradingView silnie koreluje z naszymi najwyżej ocenianymi brokerami',
                'section_4_body' => 'Podział zbioru na 40 najwyżej sklasyfikowanych brokerów według ogólnego rankingu pokazuje wyraźną różnicę: 30% z naszej czołowej czterdziestki (12 z 40) wspiera TradingView, wobec zaledwie 4% pozostałych 99 brokerów (4 brokerów). MT4 wykazuje mniejszą wersję tego samego wzorca -- 83% czołowej czterdziestki je obsługuje, wobec 71% pozostałych. Jest to korelacja, a nie twierdzenie o związku przyczynowym: wsparcie dla TradingView nie czyni samo w sobie brokera lepiej regulowanym ani tańszym, a nasza formuła rankingowa nie nagradza bezpośrednio liczby platform (platforms to funkcja liczby potwierdzonych platform, z wagą 10% ogólnej oceny) -- jednak w tym zbiorze obie cechy wyraźnie mają tendencję do współwystępowania.',
                'section_5_heading' => 'Pięciu brokerów, u których potwierdzono wsparcie dla wszystkich czterech głównych platform',
                'section_5_body' => 'Tylko 5 spośród 139 brokerów w naszym zbiorze badawczym -- Pepperstone, FP Markets, GO Markets, Fusion Markets i ICM Capital -- ma potwierdzone wsparcie dla wszystkich czterech platform: MT4, MT5, cTrader i TradingView jednocześnie. To naprawdę rzadkie połączenie: oferowanie każdej z głównych zewnętrznych platform oznacza obsługę czterech odrębnych rozwiązań infrastrukturalnych zamiast koncentracji na jednej lub dwóch. Wszyscy pięciu ci brokerzy działają też w modelu realizacji zleceń w stylu ECN/no-dealing-desk, jednak nasz zbiór nie pozwala stwierdzić, czy jest to przyczyna, korelacja, czy przypadek przy tak małej próbie.',
                'section_6_heading' => 'Platformy zastrzeżone są powszechne pojedynczo, ale w skali kategorii rozdrobnione',
                'section_6_body' => 'Poza czterema głównymi platformami 22 spośród 139 brokerów potwierdza dokładnie jedną platformę, a 5 z nich to wyłącznie MT4. Reszta zbioru to długi ogon zastrzeżonych platform poszczególnych brokerów -- każda zazwyczaj potwierdzona dla jednego brokera, a żadna zastrzeżona platforma nie jest używana przez więcej niż kilku brokerów. „Zastrzeżona” jest więc w tym sensie kategorią powszechną, ale w praktyce rozdrobnioną, w przeciwieństwie do rzeczywistej standaryzacji między brokerami, jaką reprezentują MT4, MT5, cTrader i TradingView. 12 spośród 139 brokerów nie ma w naszych badaniach potwierdzonej żadnej platformy; traktujemy to jako uczciwą lukę w danych, zamiast zgadywać, zgodnie z tym, jak obsługujemy każde inne niepotwierdzone pole na tej stronie.',
                'faq_1_q' => 'Czy brokerzy forex wycofują MT4?',
                'faq_1_a' => 'Jeszcze nie, przynajmniej nie wśród brokerów, których przebadaliśmy: 74% nadal ją obsługuje, a 87% brokerów, którzy dodali MT5, utrzymuje MT4 obok niej, zamiast z niej rezygnować. Tylko 13% brokerów wspierających MT5 w naszym zbiorze całkowicie porzuciło MT4.',
                'faq_2_q' => 'Którzy brokerzy forex obsługują MT4, MT5, cTrader i TradingView?',
                'faq_2_a' => 'Pięciu z 139 brokerów w naszym zbiorze badawczym ma potwierdzone wsparcie dla wszystkich czterech platform: Pepperstone, FP Markets, GO Markets, Fusion Markets i ICM Capital.',
                'faq_3_q' => 'Czy wsparcie dla TradingView oznacza, że broker jest lepszy?',
                'faq_3_a' => 'Nie bezpośrednio -- nasza formuła oceny nie nagradza samej liczby platform. Jest jednak wyraźna korelacja w naszych danych: 30% brokerów z czołowej czterdziestki w naszym rankingu wspiera TradingView, wobec zaledwie 4% pozostałych 99, więc szeroki wybór platform i ogólny ranking wyraźnie idą w parze, choć jedno nie powoduje mechanicznie drugiego.',
                'faq_4_q' => 'Jak zebrano te dane?',
                'faq_4_a' => 'Z pola platforms, które już wykorzystujemy w recenzjach brokerów, zestawieniach najlepszych brokerów i porównaniach na tej stronie -- ten sam zbiór 139 brokerów, a nie odrębne badanie ankietowe. Gdy nie mogliśmy niezależnie potwierdzić wsparcia platform przez brokera, traktowaliśmy je jako niepotwierdzone, zamiast zakładać cokolwiek; 12 spośród 139 brokerów należy do tej kategorii.',
            ),
            'it' => array(
                'title' => 'Report sull\'adozione delle piattaforme di trading forex',
                'meta_title' => 'Report sull\'adozione delle piattaforme di trading forex (2026): MT4, MT5, cTrader e TradingView su 139 broker',
                'meta_description' => 'Quanti broker forex supportano ancora MT4, quanti sono passati a MT5 e quanto è raro il supporto a cTrader o TradingView -- uno studio di dati originale sull\'intero campione di ricerca di 139 broker.',
                'intro' => 'Quali piattaforme di trading supporta effettivamente un broker raramente riceve la stessa attenzione dello spread o del regolatore -- così le abbiamo estratte direttamente dal nostro campione di ricerca di 139 broker e le abbiamo contate. Questo report copre ogni piattaforma confermata nell\'intero campione: quanto sono ancora diffuse MT4 e MT5, quanti broker le gestiscono in parallelo e quanti invece hanno voltato pagina, e quanto sia davvero raro il supporto a cTrader e TradingView. Ogni dato qui riportato proviene dallo stesso campo delle piattaforme usato in tutte le recensioni, nelle liste dei migliori broker e nei confronti di questo sito -- non si tratta di un\'affermazione separata né di dati di nuova raccolta.',
                'section_1_heading' => 'MT4 è ancora la piattaforma più diffusa, ma MT5 è quasi al pari',
                'section_1_body' => '103 dei 139 broker del nostro campione di ricerca (74%) risultano confermati come supportati per MT4, e 97 (70%) per MT5. Nonostante MetaTrader 4 sia la più vecchia delle due piattaforme, e nonostante MetaQuotes abbia smesso di svilupparla attivamente a favore di MT5, resta la piattaforma più supportata con un margine contenuto -- i broker non hanno abbandonato MT4 nemmeno lontanamente al ritmo che la sua reputazione di «piattaforma legacy» potrebbe far pensare.',
                'section_2_heading' => 'La maggior parte dei broker gestisce MT4 e MT5 in parallelo -- pochi hanno davvero abbandonato MT4',
                'section_2_body' => '84 dei 139 broker supportano contemporaneamente MT4 e MT5 -- di gran lunga la configurazione più comune. Solo 13 dei 97 broker che supportano MT5 (13%) hanno eliminato del tutto MT4; gli altri, l\'87%, le mantengono entrambe in funzione in parallelo. 19 broker offrono ancora MT4 senza aver mai aggiunto MT5, e 23 broker (17% dell\'intero campione) non offrono nessuna delle due, affidandosi invece a cTrader, TradingView o a una piattaforma proprietaria.',
                'section_3_heading' => 'cTrader e TradingView restano piattaforme minoritarie',
                'section_3_body' => '18 dei 139 broker (13%) risultano confermati come supportati per cTrader, e 16 (12%) per TradingView -- entrambe ancora nettamente minoritarie rispetto a MT4/MT5, sebbene tutt\'altro che trascurabili. Le due si sovrappongono più di quanto competano: 7 broker supportano sia cTrader sia TradingView, e solo 1 dei 16 broker che supportano TradingView non offre anche MT4. Ciò significa che il supporto a TradingView si aggiunge quasi sempre all\'offerta MetaTrader già esistente di un broker, anziché sostituirla.',
                'section_4_heading' => 'Il supporto a TradingView è fortemente correlato ai nostri broker meglio classificati',
                'section_4_body' => 'Dividendo il campione in base ai primi 40 broker per classifica complessiva emerge un divario reale: il 30% dei nostri primi 40 (12 su 40) supporta TradingView, contro appena il 4% dei restanti 99 (4 broker). MT4 mostra una versione più contenuta dello stesso schema -- l\'83% dei primi 40 lo supporta, contro il 71% degli altri. Si tratta di una correlazione, non di un nesso causale: supportare TradingView non rende di per sé un broker meglio regolamentato o più economico, e la nostra formula di classifica non premia direttamente il numero di piattaforme (che è una semplice funzione del numero di piattaforme confermate, con un peso del 10% sul punteggio complessivo) -- ma in questo campione le due caratteristiche tendono chiaramente a presentarsi insieme.',
                'section_5_heading' => 'I cinque broker confermati come supportati da tutte e quattro le piattaforme principali',
                'section_5_body' => 'Solo 5 dei 139 broker del nostro campione di ricerca -- Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital -- risultano confermati come supportati contemporaneamente per tutte e quattro le piattaforme MT4, MT5, cTrader e TradingView. Si tratta di una combinazione davvero rara: offrire ogni principale piattaforma di terze parti significa supportare quattro infrastrutture di trading distinte invece di concentrarsi su una o due, e tutti e cinque questi broker adottano anche un modello di esecuzione in stile ECN/no-dealing-desk. Il nostro campione, però, non ci permette di dire se si tratti di una causa, di una correlazione o di una coincidenza, data la dimensione ridotta del campione.',
                'section_6_heading' => 'Le piattaforme proprietarie sono comuni singolarmente, ma frammentate come categoria',
                'section_6_body' => 'Oltre alle quattro grandi, 22 dei 139 broker confermano esattamente una piattaforma, e 5 di questi offrono solo MT4. Il resto del campione è una lunga coda di piattaforme proprietarie specifiche di ciascun broker -- ognuna tipicamente confermata per un solo broker, senza alcuna piattaforma proprietaria usata da più di qualche broker -- quindi «proprietaria» è una categoria comune ma frammentata nella pratica, a differenza della reale standardizzazione tra broker rappresentata da MT4, MT5, cTrader e TradingView. 12 dei 139 broker non hanno alcuna piattaforma confermata nella nostra ricerca; trattiamo questo dato come una lacuna onesta anziché indovinare, coerentemente con come gestiamo ogni campo non confermato su questo sito.',
                'faq_1_q' => 'I broker forex stanno abbandonando MT4?',
                'faq_1_a' => 'Non ancora, almeno non tra i broker che abbiamo analizzato: il 74% lo supporta ancora, e l\'87% dei broker che hanno aggiunto MT5 mantiene MT4 in funzione accanto a MT5, anziché abbandonarlo. Solo il 13% dei broker che supportano MT5 nel nostro campione ha abbandonato del tutto MT4.',
                'faq_2_q' => 'Quali broker forex supportano MT4, MT5, cTrader e TradingView?',
                'faq_2_a' => 'Cinque broker del nostro campione di 139 risultano confermati come supportati per tutte e quattro: Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital.',
                'faq_3_q' => 'Il supporto a TradingView significa che un broker è migliore?',
                'faq_3_a' => 'Non direttamente -- la nostra formula di punteggio non premia da sola il numero di piattaforme. Però nei nostri dati esiste una correlazione reale: il 30% dei nostri broker classificati tra i primi 40 supporta TradingView, contro appena il 4% dei restanti 99, quindi l\'ampiezza delle piattaforme e la classifica complessiva tendono chiaramente a muoversi insieme, anche se nessuna delle due causa meccanicamente l\'altra.',
                'faq_4_q' => 'Come sono stati raccolti questi dati?',
                'faq_4_a' => 'Dal campo delle piattaforme già utilizzato in tutte le recensioni dei broker, nelle liste dei migliori broker e nei confronti di questo sito -- lo stesso campione di 139 broker, non un sondaggio separato. Quando non siamo riusciti a confermare in modo indipendente il supporto alle piattaforme di un broker, lo abbiamo contato come non confermato anziché presumere in un senso o nell\'altro; 12 dei 139 broker rientrano in questa categoria.',
            ),
            'pt' => array(
                'title' => 'Relatório de Adoção de Plataformas de Negociação de Forex',
                'meta_title' => 'Relatório de Adoção de Plataformas de Forex (2026): MT4, MT5, cTrader e TradingView em 139 corretoras',
                'meta_description' => 'Quantas corretoras de forex ainda suportam o MT4, quantas já passaram para o MT5 e quão rara é a oferta de cTrader ou TradingView -- um estudo de dados original sobre todo o nosso conjunto de investigação de 139 corretoras.',
                'intro' => 'As plataformas de negociação que uma corretora suporta raramente merecem o mesmo escrutínio que o seu spread ou o seu regulador -- por isso extraímos esses dados diretamente do nosso próprio conjunto de investigação de 139 corretoras e contámo-los. Este relatório abrange todas as plataformas confirmadas nesse conjunto completo: a frequência com que o MT4 e o MT5 continuam a ser utilizados, quantas corretoras os executam em simultâneo face às que já avançaram, e quão rara é, na prática, a oferta de cTrader e TradingView. Todos os valores abaixo provêm do mesmo campo de plataformas utilizado em todas as análises, listas de melhores corretoras e comparações deste site -- nada aqui constitui uma afirmação separada e recém-obtida.',
                'section_1_heading' => 'O MT4 continua a ser a plataforma mais comum, mas o MT5 quase o alcançou',
                'section_1_body' => '103 das 139 corretoras do nosso conjunto de investigação (74%) têm confirmado o suporte ao MT4, e 97 (70%) têm confirmado o suporte ao MT5. Apesar de o MetaTrader 4 ser a mais antiga das duas plataformas, e apesar de a MetaQuotes ter deixado de desenvolver ativamente a versão em favor do MT5, continua a ser a plataforma com maior suporte, por uma margem modesta -- as corretoras não abandonaram o MT4 sequer perto da taxa que a reputação de «plataforma legada» poderia sugerir.',
                'section_2_heading' => 'A maioria das corretoras executa o MT4 e o MT5 em paralelo -- poucas abandonaram realmente o MT4',
                'section_2_body' => '84 das 139 corretoras suportam o MT4 e o MT5 em simultâneo -- de longe a configuração mais comum. Apenas 13 das 97 corretoras que suportam o MT5 (13%) abandonaram totalmente o MT4; as restantes 87% mantêm ambas as plataformas em funcionamento em paralelo. 19 corretoras continuam a oferecer o MT4 sem terem acrescentado o MT5, e 23 corretoras (17% do conjunto completo) não oferecem nenhuma das duas plataformas, recorrendo antes ao cTrader, ao TradingView ou a uma plataforma proprietária de que dispõem.',
                'section_3_heading' => 'O cTrader e o TradingView continuam a ser plataformas minoritárias',
                'section_3_body' => '18 das 139 corretoras (13%) têm confirmado o suporte ao cTrader, e 16 (12%) têm confirmado o suporte ao TradingView -- ambas ainda claramente minoritárias face ao MT4/MT5, embora longe de irrelevantes. As duas sobrepõem-se mais do que competem: 7 corretoras suportam simultaneamente o cTrader e o TradingView, e apenas 1 das 16 corretoras que suportam o TradingView o faz sem oferecer também o MT4, o que significa que o suporte ao TradingView é quase sempre um complemento da oferta MetaTrader já existente de uma corretora, e não um substituto.',
                'section_4_heading' => 'O suporte ao TradingView está fortemente correlacionado com as nossas corretoras mais bem classificadas',
                'section_4_body' => 'Dividindo o conjunto de dados pelas nossas 40 principais corretoras segundo a classificação global, surge uma diferença real: 30% das nossas 40 principais corretoras (12 em 40) suportam o TradingView, contra apenas 4% das restantes 99 (4 corretoras). O MT4 mostra uma versão mais ligeira do mesmo padrão -- 83% das 40 principais suportam-no, contra 71% das restantes. Trata-se de uma correlação, e não de uma afirmação de causalidade: suportar o TradingView não torna, por si só, uma corretora mais bem regulada ou mais barata, e a nossa fórmula de classificação não recompensa diretamente o número de plataformas (o campo de plataformas é uma função simples do número de plataformas confirmadas, ponderada em 10% da pontuação global) -- mas as duas tendem claramente a andar juntas neste conjunto de dados.',
                'section_5_heading' => 'As cinco corretoras com suporte confirmado às quatro principais plataformas',
                'section_5_body' => 'Apenas 5 das 139 corretoras do nosso conjunto de investigação -- Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital -- têm confirmado o suporte simultâneo às quatro plataformas MT4, MT5, cTrader e TradingView. Trata-se de uma combinação genuinamente rara: oferecer todas as principais plataformas de terceiros significa suportar quatro infraestruturas de negociação distintas, em vez de concentrar-se numa ou duas, e todas estas cinco corretoras executam também um modelo de execução de ordens do tipo ECN/sem mesa de dealing, embora o nosso conjunto de dados não permita afirmar se se trata de causa, correlação ou coincidência, dada a dimensão reduzida da amostra.',
                'section_6_heading' => 'As plataformas proprietárias são comuns individualmente, mas fragmentadas enquanto categoria',
                'section_6_body' => 'Para além das quatro principais, 22 das 139 corretoras confirmam exatamente uma plataforma, e 5 delas são apenas MT4. O restante conjunto de dados é uma longa cauda de plataformas proprietárias específicas de cada corretora -- cada uma normalmente confirmada para uma única corretora, sem nenhuma plataforma proprietária utilizada por mais do que um punhado de corretoras -- pelo que «proprietária» é comum como categoria, mas fragmentada na prática, ao contrário da verdadeira padronização transversal que o MT4, o MT5, o cTrader e o TradingView representam. 12 das 139 corretoras não têm qualquer plataforma confirmada na nossa investigação; tratamos isso como uma lacuna honesta nos dados, em vez de adivinhar, em consonância com a forma como qualquer outro campo não confirmado deste site é tratado.',
                'faq_1_q' => 'O MT4 está a ser descontinuado pelas corretoras de forex?',
                'faq_1_a' => 'Ainda não, pelo menos entre as corretoras que investigámos: 74% continuam a suportá-lo, e 87% das corretoras que acrescentaram o MT5 mantêm o MT4 em funcionamento em paralelo, em vez de o abandonarem. Apenas 13% das corretoras que suportam o MT5 no nosso conjunto de dados abandonaram totalmente o MT4.',
                'faq_2_q' => 'Que corretoras de forex suportam o MT4, o MT5, o cTrader e o TradingView?',
                'faq_2_a' => 'Cinco corretoras do nosso conjunto de investigação de 139 têm confirmado o suporte às quatro: Pepperstone, FP Markets, GO Markets, Fusion Markets e ICM Capital.',
                'faq_3_q' => 'O suporte ao TradingView significa que uma corretora é melhor?',
                'faq_3_a' => 'Não diretamente -- a nossa fórmula de pontuação não recompensa o número de plataformas por si só. Mas existe uma correlação real nos nossos dados: 30% das nossas 40 principais corretoras suportam o TradingView, contra apenas 4% das restantes 99, pelo que a amplitude de plataformas e a classificação global tendem claramente a acompanhar-se, embora uma não cause mecanicamente a outra.',
                'faq_4_q' => 'Como foram recolhidos estes dados?',
                'faq_4_a' => 'A partir do campo de plataformas já utilizado em todas as análises, listas de melhores corretoras e comparações deste site -- o mesmo conjunto de investigação de 139 corretoras, e não um inquérito separado. Quando não conseguimos confirmar de forma independente o suporte de plataformas de uma corretora, contabilizámo-lo como não confirmado, em vez de presumir qualquer dos dois cenários; 12 das 139 corretoras enquadram-se nessa categoria.',
            ),
        ),
        'minimum-deposit-report' => array(
            'es' => array(
                'title' => 'Informe sobre el depósito mínimo',
                'meta_title' => 'Requisitos de depósito mínimo en 139 brókers de forex (2026): ¿Qué tan bajo se puede empezar realmente?',
                'meta_description' => 'Lo que los brókers de forex exigen realmente para abrir una cuenta, desde $0 hasta $10,800: un estudio de datos original sobre nuestro conjunto completo de 139 brókers analizados, que incluye qué mínimos están confirmados y qué brókers no publican ninguno.',
                'intro' => 'Cuánto dinero se necesita realmente para abrir una cuenta rara vez recibe el mismo tratamiento comparativo que los spreads o la regulación, así que extrajimos el campo min_deposit_usd que ya se utiliza en las reseñas y en las listas de mejores brókers de este sitio y lo contamos en todo el conjunto de investigación. Este informe cubre cada cifra de depósito mínimo confirmada: dónde se sitúa el bróker típico, qué brókers no exigen nada, cuáles exigen cifras de cuatro dígitos y qué parte del conjunto no tiene ni una sola cifra confirmada. Nada de esto procede de fuentes nuevas: es el mismo campo que ya aparece publicado en la página de análisis de cada bróker.',
                'section_1_heading' => 'Una mediana de $100 entre los brókers confirmados, pero una cuarta parte exige $10 o menos',
                'section_1_body' => '79 de los 139 brókers de nuestro conjunto de investigación (57%) tienen una cifra de depósito mínimo confirmada; la mediana entre ellos es de $100. Por debajo de esa mediana, sin embargo, 26 de esos 79 (33%) exigen menos de $50, y 20 (25%) exigen $10 o menos. La media es de $415, muy por encima de la mediana, porque un puñado de valores atípicos de cuatro cifras la elevan; la mediana es la cifra más representativa de lo que cuesta realmente abrir una cuenta típica.',
                'section_2_heading' => 'Diez brókers confirmados con un depósito mínimo fijo de $0',
                'section_2_body' => 'ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX y FXCC confirman que no tienen ningún depósito mínimo. Ocupan posiciones en nuestra lista de clasificación que van del 3.º al 100.º, por lo que un mínimo de $0 no indica, por sí solo, que un bróker sea de los mejores ni de los más débiles.',
                'section_3_heading' => 'Unos pocos brókers exigen mínimos de cuatro cifras, y son un tipo de bróker distinto',
                'section_3_body' => 'EXANTE ($10,800) y LMAX ($10,000) se sitúan muy por encima del resto del conjunto, seguidos por GBE Brokers y EXCA Prime (ambos con $1,080) y Swissquote ($1,000). No se trata de operaciones orientadas primero al minorista que compiten por accesibilidad: EXANTE y LMAX están orientados a clientes profesionales e institucionales, y Swissquote es un banco privado suizo, por lo que sus mínimos reflejan un cliente objetivo distinto, no una peor oferta para los traders minoristas.',
                'section_4_heading' => 'El depósito mínimo no pesa en nuestra fórmula de clasificación, pero nuestros brókers mejor clasificados tienden a exigir menos de todos modos',
                'section_4_body' => 'min_deposit_usd no es uno de los nueve componentes ponderados de nuestra fórmula de puntuación (regulación 30%, costo 20%, comisiones no asociadas al trading 10%, plataformas 10%, ejecución de órdenes 10%, gama de productos 5%, depósitos y retiradas de fondos 5%, transparencia 5%, trayectoria 5%), por lo que el depósito mínimo de un bróker no tiene ningún efecto directo sobre su puntuación. Aun así, los 40 primeros brókers de nuestra clasificación (32 con cifra confirmada) muestran una mediana de $50, frente a una mediana de $100 para los 99 restantes (47 confirmados), y el 59% de los brókers confirmados del top 40 se sitúan por debajo de $100, frente al 36% del resto. Se trata de una correlación, no de una afirmación causal: los mismos brókers que compiten con más fuerza en regulación y spread tienden también a competir en accesibilidad, pero un mínimo bajo no garantiza una mejor posición en el ranking.',
                'section_5_heading' => '60 de los 139 brókers no tienen un mínimo único confirmado, generalmente porque no existe una cifra fija que confirmar',
                'section_5_body' => 'Brókers como Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo y HYCM no tienen una cifra confirmada en nuestra investigación, sobre todo porque su mínimo real varía según la entidad legal, la divisa de la cuenta o el tipo de cuenta, en lugar de ser una sola cifra que el bróker publique una vez. Registramos esto honestamente como no confirmado, en lugar de adivinar una cifra o asignarle $0 por defecto.',
                'faq_1_q' => '¿Cuál es el depósito mínimo medio que exigen los brókers de forex?',
                'faq_1_a' => 'Entre los 79 de los 139 brókers de nuestro conjunto de investigación con cifra confirmada, la mediana es de $100 y el 33% exige menos de $50. La media ($415) queda muy por encima debido a un puñado de valores atípicos de cuatro cifras orientados a clientes profesionales o de banca privada, por lo que la mediana es la cifra más representativa.',
                'faq_2_q' => '¿Qué brókers de forex no tienen depósito mínimo?',
                'faq_2_a' => 'Diez confirmados: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX y FXCC.',
                'faq_3_q' => '¿Un depósito mínimo bajo significa que un bróker es mejor?',
                'faq_3_a' => 'No directamente: el depósito mínimo no es uno de los nueve componentes ponderados de nuestra fórmula de clasificación. Pero los 40 primeros brókers con cifra confirmada sí tienden a exigir menos (una mediana de $50) que el resto del conjunto (mediana de $100), probablemente porque los brókers que compiten con más fuerza en regulación y spread también tienden a competir en accesibilidad.',
                'faq_4_q' => '¿Por qué no todos los 139 brókers tienen un depósito mínimo publicado?',
                'faq_4_a' => '60 no tienen una cifra confirmada, generalmente porque el mínimo real varía según la entidad legal, la divisa de la cuenta o el tipo de cuenta, algo habitual en brókers con presencia en varias jurisdicciones, en lugar de existir una cifra fija que el bróker publique.',
            ),
            'de' => array(
                'title' => 'Bericht zur Mindesteinzahlung',
                'meta_title' => 'Mindesteinzahlung bei 139 Forex-Brokern (2026): Wie wenig können Sie tatsächlich einzahlen, um zu starten?',
                'meta_description' => 'Was Forex-Broker tatsächlich verlangen, um ein Konto zu eröffnen, von $0 bis $10,800 -- eine eigenständige Datenstudie über unser gesamtes Research-Set mit 139 Brokern, einschließlich der bestätigten Mindestbeträge und der Broker, die keinen Mindestbetrag veröffentlichen.',
                'intro' => 'Wie viel Geld man tatsächlich benötigt, um ein Konto zu eröffnen, wird selten so direkt gegenübergestellt wie Spreads oder Regulierung -- deshalb haben wir das Feld min_deposit_usd, das auf dieser Website bereits in den Testberichten und Listen der besten Broker verwendet wird, ausgewertet und über das gesamte Research-Set gezählt. Dieser Bericht erfasst jede bestätigte Mindesteinzahlung: wo der typische Broker liegt, welche Broker gar nichts verlangen, welche vierstellige Beträge fordern und wie viel des Datensatzes überhaupt keine einzige bestätigte Zahl enthält. Nichts davon wurde neu erhoben -- es handelt sich um dasselbe Feld, das bereits auf der Testseite jedes Brokers angegeben ist.',
                'section_1_heading' => 'Ein Median von $100 unter den bestätigten Brokern, doch ein Viertel verlangt $10 oder weniger',
                'section_1_body' => '79 der 139 Broker in unserem Research-Set (57%) haben eine bestätigte Mindesteinzahlung; der Median unter ihnen liegt bei $100. Unterhalb dieses Medians verlangen jedoch 26 der 79 (33%) weniger als $50, und 20 (25%) verlangen $10 oder weniger. Der Mittelwert liegt bei $415 -- deutlich über dem Median -- weil einige wenige vierstellige Ausreißer ihn nach oben ziehen; der Median ist daher der repräsentativere Wert dafür, was die Eröffnung eines Kontos typischerweise kostet.',
                'section_2_heading' => 'Zehn Broker bestätigen eine pauschale Mindesteinzahlung von $0',
                'section_2_body' => 'ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX und FXCC bestätigen allesamt, dass gar keine Mindesteinzahlung erforderlich ist. Sie verteilen sich über unsere Rangliste vom 3. bis zum 100. Platz, sodass eine Mindesteinzahlung von $0 für sich genommen weder auf einen Spitzenreiter noch auf einen schwachen Broker hindeutet.',
                'section_3_heading' => 'Wenige Broker verlangen vierstellige Mindestbeträge -- und sie sind eine andere Art von Broker',
                'section_3_body' => 'EXANTE ($10,800) und LMAX ($10,000) liegen deutlich über dem übrigen Datensatz, gefolgt von GBE Brokers und EXCA Prime (beide $1,080) sowie Swissquote ($1,000). Dabei handelt es sich nicht um Privatkundenangebote, die über Zugänglichkeit konkurrieren: EXANTE und LMAX richten sich an professionelle und institutionelle Kunden, und Swissquote ist eine Schweizer Privatbank. Ihre Mindestbeträge spiegeln daher eine andere Zielkundschaft wider und bedeuten keinen schlechteren Deal für Privatanleger.',
                'section_4_heading' => 'Die Mindesteinzahlung fließt nicht in unsere Rangliste ein, doch besser platzierte Broker liegen dennoch tendenziell niedriger',
                'section_4_body' => 'min_deposit_usd ist keine der neun gewichteten Komponenten unserer Bewertungsformel (Regulierung 30%, Kosten 20%, handelsunabhängige Gebühren 10%, Plattformen 10%, Orderausführung 10%, Produktangebot 5%, Einzahlungen/Auszahlungen 5%, Transparenz 5%, Erfolgsbilanz 5%) -- die Mindesteinzahlung eines Brokers hat daher keinerlei direkten Einfluss auf seine Bewertung. Dennoch zeigen unsere 40 bestplatzierten Broker (32 mit bestätigter Angabe) einen Median von $50, während die übrigen 99 (47 bestätigt) einen Median von $100 aufweisen -- und 59% der bestätigten Broker unter den Top 40 liegen unter $100, gegenüber 36% der übrigen. Dies ist eine Korrelation, keine kausale Aussage: Dieselben Broker, die sich am stärksten über Regulierung und Spread profilieren, konkurrieren oft auch über Zugänglichkeit, doch ein niedriger Mindestbetrag verschafft keinen besseren Rang.',
                'section_5_heading' => '60 von 139 Brokern haben keine einzige bestätigte Mindesteinzahlung -- meist weil es keinen einheitlichen Betrag gibt, der sich bestätigen ließe',
                'section_5_body' => 'Broker wie Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo und HYCM haben in unserer Recherche keine bestätigte Angabe, meist weil ihre tatsächliche Mindesteinzahlung je nach Rechtsträger, Kontowährung oder Kontotyp variiert, statt dass der Broker eine einzige Zahl veröffentlicht. Wir werten dies bewusst als unbestätigt, anstatt einen Betrag zu schätzen oder pauschal $0 anzusetzen.',
                'faq_1_q' => 'Wie hoch ist die durchschnittliche Mindesteinzahlung bei Forex-Brokern?',
                'faq_1_a' => 'Unter den 79 von 139 Brokern in unserem Research-Set mit bestätigter Angabe liegt der Median bei $100, und 33% verlangen weniger als $50. Der Mittelwert ($415) wird durch einige wenige vierstellige Ausreißer deutlich nach oben gezogen, die sich an professionelle Kunden oder Privatbankkunden richten; der Median ist daher der repräsentativere Wert.',
                'faq_2_q' => 'Welche Forex-Broker haben keine Mindesteinzahlung?',
                'faq_2_a' => 'Zehn bestätigt: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX und FXCC.',
                'faq_3_q' => 'Bedeutet eine niedrige Mindesteinzahlung, dass ein Broker besser ist?',
                'faq_3_a' => 'Nicht direkt -- die Mindesteinzahlung ist keine der neun gewichteten Komponenten unserer Bewertungsformel. Allerdings liegen die bestätigten Broker unter den Top 40 tendenziell niedriger (Median $50) als der Rest des Datensatzes (Median $100), vermutlich weil Broker, die sich am stärksten über Regulierung und Spread profilieren, oft auch über Zugänglichkeit konkurrieren.',
                'faq_4_q' => 'Warum haben nicht alle 139 Broker eine angegebene Mindesteinzahlung?',
                'faq_4_a' => 'Bei 60 Brokern gibt es keine einzige bestätigte Angabe, meist weil die tatsächliche Mindesteinzahlung je nach Rechtsträger, Kontowährung oder Kontotyp variiert -- was bei Brokern mit mehreren Rechtsgebieten üblich ist -- statt dass ein einheitlicher Betrag veröffentlicht wird.',
            ),
            'ar' => array(
                'title' => 'تقرير الحد الأدنى للإيداع',
                'meta_title' => 'متطلبات الحد الأدنى للإيداع عبر 139 وسيطاً في الفوركس (2026): ما أقل مبلغ يمكنك البدء به فعلياً؟',
                'meta_description' => 'ما يشترطه وسطاء الفوركس فعلياً لفتح حساب، من $0 إلى $10,800 -- دراسة بيانات أصلية تشمل مجموعة البحث الكاملة المكونة من 139 وسيطاً، بما في ذلك الحدود الدنيا المؤكدة والوسطاء الذين لا ينشرون حداً أدنى.',
                'intro' => 'نادراً ما يحظى المبلغ الفعلي اللازم لفتح حساب بالمعالجة المقارنة ذاتها التي تحظى بها الفروق السعرية أو التنظيم، لذلك استخرجنا حقل min_deposit_usd المستخدم أصلاً في مراجعات هذا الموقع وقوائم أفضل الوسطاء، وأحصيناه عبر مجموعة البحث كاملة. يغطي هذا التقرير كل رقم مؤكد للحد الأدنى للإيداع: أين يقع الوسيط النموذجي، وأي الوسطاء لا يشترطون شيئاً على الإطلاق، وأيها يشترط مبالغ بأربعة أرقام، وما نسبة مجموعة البيانات التي لا تملك رقماً مؤكداً واحداً. لم يُجمع أي من هذه البيانات حديثاً، فهي الحقل نفسه المعلن أصلاً في صفحة مراجعة كل وسيط.',
                'section_1_heading' => 'الوسيط الإحصائي البالغ $100 بين الوسطاء المؤكدين، لكن ربعهم يشترط $10 أو أقل',
                'section_1_body' => 'يملك 79 من أصل 139 وسيطاً في مجموعة البحث (57%) رقماً مؤكداً للحد الأدنى للإيداع، ويبلغ الوسيط بينها $100. ومع ذلك، فإن 26 من هذه الـ 79 (33%) تشترط أقل من $50، و20 منها (25%) تشترط $10 أو أقل. يبلغ المتوسط الحسابي $415، وهو أعلى بكثير من الوسيط، لأن عدداً قليلاً من القيم الشاذة ذات الأرقام الأربعة يرفعه؛ لذلك يُعد الوسيط أدق تمثيلاً لما يكلفه فتح حساب نموذجي.',
                'section_2_heading' => 'عشرة وسطاء أكدوا حداً أدنى ثابتاً للإيداع قدره $0',
                'section_2_body' => 'تؤكد ThinkMarkets وCFI وOANDA وFusion Markets وXS Markets وXTB وAfterprime وSquaredFinancial وTopFX وFXCC جميعها عدم اشتراط أي حد أدنى للإيداع. وتتوزع هذه الشركات في قائمة ترتيبنا من المركز الثالث حتى المركز المئة، لذلك فإن شرط $0 لا يدل بمفرده على أن الوسيط من الأفضل أداءً أو الأضعف.',
                'section_3_heading' => 'عدد قليل من الوسطاء يشترط حدوداً دنيا بأربعة أرقام، وهم من نوع مختلف من الوسطاء',
                'section_3_body' => 'يقع كل من EXANTE ($10,800) وLMAX ($10,000) أعلى بكثير من بقية مجموعة البيانات، يليهما GBE Brokers وEXCA Prime (كلاهما بمبلغ $1,080) ثم Swissquote ($1,000). هذه الشركات ليست عمليات موجهة أولاً إلى المتداولين الأفراد وتنافس على سهولة الوصول، فـ EXANTE وLMAX موجهتان نحو العملاء المحترفين والمؤسسيين، وSwissquote بنك خاص سويسري؛ لذا تعكس حدودها الدنيا عميلاً مستهدفاً مختلفاً، ولا يعني ذلك صفقة أسوأ للمتداولين الأفراد.',
                'section_4_heading' => 'لا يدخل الحد الأدنى للإيداع في معادلة تقييمنا للترتيب، لكن الوسطاء الأعلى ترتيباً يميلون إلى الانخفاض على أي حال',
                'section_4_body' => 'لا يُعد min_deposit_usd أحد المكونات التسعة الموزونة في معادلة التقييم لدينا (التنظيم 30%، التكلفة 20%، رسوم غير متعلقة بالتداول 10%، المنصات 10%، تنفيذ الصفقات 10%، نطاق المنتجات 5%، الإيداعات والسحوبات 5%، الشفافية 5%، السجل التاريخي 5%)، إذ لا يملك الحد الأدنى للإيداع لدى أي وسيط أي تأثير مباشر على تقييمه. ومع ذلك، تُظهر أفضل 40 شركة مصنفة لدينا (32 منها لديها رقم مؤكد) وسيطاً قدره $50، مقابل وسيط قدره $100 للـ 99 الباقية (47 منها مؤكدة)، ويقع 59% من الشركات المؤكدة ضمن أفضل 40 تحت $100، مقابل 36% من بقية الشركات. هذا ارتباط وليس ادعاءً سببياً: فالوسطاء الذين يتنافسون بشدة على التنظيم والفروق السعرية يميلون أيضاً إلى المنافسة على سهولة الوصول، لكن انخفاض الحد الأدنى لا يرفع الترتيب.',
                'section_5_heading' => '60 من أصل 139 وسيطاً لا يملكون حداً أدنى مؤكداً واحداً، غالباً لأنه لا يوجد رقم ثابت واحد يمكن تأكيده',
                'section_5_body' => 'وسطاء مثل Interactive Brokers وVantage Markets وUltima Markets وCMC Markets وSaxo وHYCM لا يملكون رقماً مؤكداً في بحثنا، ويرجع ذلك في الغالب إلى أن حدهم الأدنى الفعلي يختلف باختلاف الكيان القانوني أو عملة الحساب أو نوع الحساب، وليس رقماً واحداً ينشره الوسيط مرة واحدة. نحتسب هذه الحالات بصدق على أنها غير مؤكدة، بدلاً من تخمين رقم أو اعتبارها $0.',
                'faq_1_q' => 'ما متوسط الحد الأدنى للإيداع الذي يشترطه وسطاء الفوركس؟',
                'faq_1_a' => 'بين الـ 79 من أصل 139 وسيطاً في مجموعة بحثنا الذين لديهم رقم مؤكد، يبلغ الوسيط $100، وتشترط 33% منهم أقل من $50. ويرتفع المتوسط الحسابي ($415) بشكل كبير بفعل عدد قليل من القيم ذات الأرقام الأربعة الموجهة إلى العملاء المحترفين أو عملاء الخدمات المصرفية الخاصة، لذلك يُعد الوسيط الرقم الأدق تمثيلاً.',
                'faq_2_q' => 'أي وسطاء الفوركس لا يشترطون حداً أدنى للإيداع؟',
                'faq_2_a' => 'عشرة وسطاء مؤكدون: ThinkMarkets وCFI وOANDA وFusion Markets وXS Markets وXTB وAfterprime وSquaredFinancial وTopFX وFXCC.',
                'faq_3_q' => 'هل يعني انخفاض الحد الأدنى للإيداع أن الوسيط أفضل؟',
                'faq_3_a' => 'ليس بشكل مباشر، فالحد الأدنى للإيداع ليس أحد المكونات التسعة الموزونة في معادلة الترتيب لدينا. لكن الشركات الـ 40 الأولى المؤكدة لدينا تميل إلى الانخفاض (وسيط قدره $50) مقارنة ببقية المجموعة (وسيط قدره $100)، ويرجع ذلك على الأرجح إلى أن الوسطاء الذين يتنافسون بشدة على التنظيم والفروق السعرية يميلون أيضاً إلى المنافسة على سهولة الوصول.',
                'faq_4_q' => 'لماذا لا يملك جميع الوسطاء الـ 139 حداً أدنى للإيداع مدرجاً؟',
                'faq_4_a' => 'لا يملك 60 منهم رقماً مؤكداً واحداً، غالباً لأن الحد الأدنى الفعلي يختلف باختلاف الكيان القانوني أو عملة الحساب أو نوع الحساب، وهو أمر شائع لدى الوسطاء العاملين في عدة ولايات قضائية، بدلاً من وجود رقم ثابت واحد ينشره الوسيط.',
            ),
            'fr' => array(
                'title' => 'Rapport sur le dépôt minimum',
                'meta_title' => 'Exigences de dépôt minimum chez 139 courtiers forex (2026) : à quel montant pouvez-vous réellement commencer ?',
                'meta_description' => 'Ce que les courtiers forex exigent réellement pour ouvrir un compte, de 0 $ à 10 800 $ : une étude de données originale portant sur l\'ensemble de nos 139 courtiers analysés, incluant les minimums confirmés et les courtiers qui ne publient aucun montant.',
                'intro' => 'Le montant réel nécessaire pour ouvrir un compte fait rarement l\'objet du même traitement comparatif que les spreads ou la réglementation. Nous avons donc extrait le champ min_deposit_usd déjà utilisé dans l\'ensemble des analyses et des listes des meilleurs courtiers de ce site, puis l\'avons compilé sur l\'ensemble de notre base de recherche. Ce rapport couvre chaque montant de dépôt minimum confirmé : où se situe le courtier typique, quels courtiers n\'exigent rien, lesquels demandent des montants à quatre chiffres, et quelle part de la base ne dispose d\'aucun montant confirmé. Rien ici n\'est nouvellement sourcé : il s\'agit du même champ déjà communiqué sur la page d\'analyse de chaque courtier.',
                'section_1_heading' => 'Une médiane de 100 $ parmi les courtiers confirmés, mais un quart exige 10 $ ou moins',
                'section_1_body' => '79 des 139 courtiers de notre base de recherche (57 %) disposent d\'un montant de dépôt minimum confirmé ; la médiane parmi eux est de 100 $. Sous cette médiane, toutefois, 26 de ces 79 (33 %) exigent moins de 50 $, et 20 (25 %) exigent 10 $ ou moins. La moyenne s\'élève à 415 $, bien au-dessus de la médiane, car quelques valeurs extrêmes à quatre chiffres la tirent vers le haut ; la médiane reflète donc mieux le coût typique d\'ouverture d\'un compte.',
                'section_2_heading' => 'Dix courtiers confirmés à un dépôt minimum forfaitaire de 0 $',
                'section_2_body' => 'ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX et FXCC confirment tous l\'absence totale de dépôt minimum. Ils se répartissent dans notre classement entre le 3e et le 100e rang : un dépôt minimum de 0 $ ne signale donc pas, à lui seul, un courtier de tête ni un courtier faible.',
                'section_3_heading' => 'Un petit nombre de courtiers exige des minimums à quatre chiffres, et ce sont des profils différents',
                'section_3_body' => 'EXANTE (10 800 $) et LMAX (10 000 $) se situent nettement au-dessus du reste de la base, suivis de GBE Brokers et EXCA Prime (tous deux à 1 080 $) puis de Swissquote (1 000 $). Il ne s\'agit pas de structures ciblant d\'abord le grand public et misant sur l\'accessibilité : EXANTE et LMAX s\'adressent prioritairement à une clientèle professionnelle ou institutionnelle, et Swissquote est une banque privée suisse. Leurs minimums reflètent donc une clientèle cible différente, et non des conditions moins avantageuses pour les particuliers.',
                'section_4_heading' => 'Le dépôt minimum ne pèse pas dans notre formule de classement, mais nos courtiers les mieux classés ont tout de même tendance à afficher des montants plus bas',
                'section_4_body' => 'min_deposit_usd ne figure pas parmi les neuf composantes pondérées de notre formule de notation (réglementation 30 %, coûts 20 %, frais hors trading 10 %, plateformes 10 %, exécution des ordres 10 %, gamme de produits 5 %, dépôts/retraits 5 %, transparence 5 %, historique 5 %) : le dépôt minimum d\'un courtier n\'a donc aucun effet direct sur sa note. Néanmoins, nos 40 courtiers les mieux classés (dont 32 avec un montant confirmé) affichent une médiane de 50 $, contre 100 $ pour les 99 autres (dont 47 confirmés), et 59 % des courtiers confirmés du top 40 se situent sous 100 $, contre 36 % pour le reste. Il s\'agit d\'une corrélation, non d\'un lien de causalité : les courtiers qui se disputent le plus fortement sur la réglementation et le spread tendent aussi à rivaliser sur l\'accessibilité, mais un dépôt minimum bas n\'achète pas un meilleur classement.',
                'section_5_heading' => '60 courtiers sur 139 n\'ont aucun minimum confirmé, généralement parce qu\'il n\'existe pas de montant forfaitaire unique à confirmer',
                'section_5_body' => 'Des courtiers comme Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo et HYCM ne disposent d\'aucun montant confirmé dans notre recherche, le plus souvent parce que leur minimum réel varie selon l\'entité juridique, la devise du compte ou le type de compte, plutôt que d\'être un chiffre unique publié une fois par le courtier. Nous les comptabilisons honnêtement comme non confirmés, plutôt que de deviner un montant ou de leur attribuer par défaut 0 $.',
                'faq_1_q' => 'Quel est le dépôt minimum moyen exigé par les courtiers forex ?',
                'faq_1_a' => 'Parmi les 79 courtiers sur 139 de notre base de recherche disposant d\'un montant confirmé, la médiane est de 100 $ et 33 % exigent moins de 50 $. La moyenne (415 $) est nettement tirée vers le haut par quelques valeurs extrêmes à quatre chiffres destinées à une clientèle professionnelle ou de banque privée ; la médiane reste donc la valeur la plus représentative.',
                'faq_2_q' => 'Quels courtiers forex n\'exigent aucun dépôt minimum ?',
                'faq_2_a' => 'Dix courtiers sont confirmés : ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX et FXCC.',
                'faq_3_q' => 'Un dépôt minimum bas signifie-t-il qu\'un courtier est meilleur ?',
                'faq_3_a' => 'Pas directement : le dépôt minimum ne fait pas partie des neuf composantes pondérées de notre formule de classement. Toutefois, nos 40 courtiers confirmés les mieux classés affichent des montants plus bas (médiane de 50 $) que le reste de la base (médiane de 100 $), probablement parce que les courtiers qui se disputent le plus fortement sur la réglementation et le spread rivalisent aussi sur l\'accessibilité.',
                'faq_4_q' => 'Pourquoi les 139 courtiers n\'affichent-ils pas tous un dépôt minimum ?',
                'faq_4_a' => '60 d\'entre eux ne disposent d\'aucun montant confirmé, généralement parce que le minimum réel varie selon l\'entité juridique, la devise du compte ou le type de compte, situation courante chez les courtiers opérant dans plusieurs juridictions, plutôt qu\'à l\'existence d\'un montant forfaitaire unique publié par le courtier.',
            ),
            'pl' => array(
                'title' => 'Raport o minimalnym depozycie',
                'meta_title' => 'Minimalne depozyty u 139 brokerów forex (2026): jak nisko można faktycznie zacząć?',
                'meta_description' => 'Czego faktycznie wymagają brokerzy forex przy otwieraniu konta, od $0 do $10,800 -- oryginalne badanie danych obejmujące cały nasz zbiór 139 brokerów, w tym które minima są potwierdzone i którzy brokerzy nie publikują żadnego.',
                'intro' => 'Ile pieniędzy faktycznie trzeba mieć, by otworzyć konto, rzadko doczekuje się takiego samego zestawienia obok siebie jak w przypadku spreadów czy regulacji -- dlatego zebraliśmy pole min_deposit_usd, które już wykorzystujemy w recenzjach i zestawieniach najlepszych brokerów na tej stronie, i policzyliśmy je w całym zbiorze badawczym. Raport obejmuje każdą potwierdzoną kwotę minimalnego depozytu: gdzie plasuje się typowy broker, którzy brokerzy nie wymagają niczego, którzy wymagają kwot czterocyfrowych oraz jaka część zbioru nie ma ani jednej potwierdzonej wartości. Nic tutaj nie zostało pozyskane na nowo -- to samo pole jest już ujawnione na stronie recenzji każdego brokera.',
                'section_1_heading' => 'Mediana $100 wśród potwierdzonych brokerów, ale co czwarty wymaga $10 lub mniej',
                'section_1_body' => '79 spośród 139 brokerów w naszym zbiorze badawczym (57%) ma potwierdzoną kwotę minimalnego depozytu; ich mediana wynosi $100. Pod tą medianą kryje się jednak fakt, że 26 z tych 79 (33%) wymaga mniej niż $50, a 20 (25%) wymaga $10 lub mniej. Średnia wynosi $415 -- znacznie powyżej mediany -- ponieważ kilka czterocyfrowych wartości skrajnych podbija ją w górę; mediana lepiej oddaje, ile faktycznie kosztuje otwarcie typowego konta.',
                'section_2_heading' => 'Dziesięciu brokerów potwierdziło stały minimalny depozyt na poziomie $0',
                'section_2_body' => 'ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX i FXCC potwierdzają brak jakiegokolwiek minimalnego depozytu. Zajmują one miejsca w naszym rankingu od 3. do 100., więc depozyt $0 sam w sobie nie świadczy ani o tym, że broker jest czołowy, ani o tym, że jest słaby.',
                'section_3_heading' => 'Niewielka liczba brokerów wymaga czterocyfrowych minimów -- i to brokerzy innego typu',
                'section_3_body' => 'EXANTE ($10,800) i LMAX ($10,000) znacznie przewyższają resztę zbioru, a za nimi plasują się GBE Brokers i EXCA Prime (oba po $1,080) oraz Swissquote ($1,000). Nie są to podmioty nastawione przede wszystkim na klientów detalicznych, konkurujące dostępnością -- EXANTE i LMAX są ukierunkowane na klientów profesjonalnych i instytucjonalnych, a Swissquote jest szwajcarskim bankiem prywatnym -- więc ich minima odzwierciedlają inny docelowy typ klienta, a nie gorszą ofertę dla traderów detalicznych.',
                'section_4_heading' => 'Minimalny depozyt nie ma wagi w naszym wzorze rankingowym, ale nasi wyżej sklasyfikowani brokerzy i tak częściej mają niższe minima',
                'section_4_body' => 'min_deposit_usd nie jest jednym z dziewięciu ważonych komponentów naszego wzoru oceny (regulacja 30%, koszty 20%, opłaty niezwiązane z handlem 10%, platformy 10%, realizacja zleceń 10%, zakres produktów 5%, depozyty/wypłaty 5%, przejrzystość 5%, historia działalności 5%) -- minimalny depozyt brokera nie ma zatem żadnego bezpośredniego wpływu na jego ocenę. Mimo to nasi top 40 brokerów (32 z potwierdzoną kwotą) pokazują medianę $50, wobec mediany $100 dla pozostałych 99 (47 potwierdzonych) -- a 59% potwierdzonych brokerów z top 40 ma depozyt poniżej $100, wobec 36% pozostałych. To korelacja, a nie twierdzenie o przyczynowości: ci sami brokerzy, którzy najmocniej konkurują regulacją i spreadem, często konkurują także dostępnością, ale niski minimalny depozyt nie zapewnia wyższej pozycji w rankingu.',
                'section_5_heading' => '60 spośród 139 brokerów nie ma ani jednego potwierdzonego minimum -- zwykle dlatego, że nie ma jednej stałej kwoty do potwierdzenia',
                'section_5_body' => 'Brokerzy tacy jak Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo i HYCM nie mają potwierdzonej wartości w naszym badaniu, najczęściej dlatego, że ich rzeczywiste minimum zależy od podmiotu prawnego, waluty konta lub typu konta, a nie od jednej liczby publikowanej przez brokera. Traktujemy to uczciwie jako niepotwierdzone, zamiast zgadywać kwotę lub domyślnie przyjmować $0.',
                'faq_1_q' => 'Jaki jest średni minimalny depozyt wymagany przez brokerów forex?',
                'faq_1_a' => 'Wśród 79 z 139 brokerów w naszym zbiorze badawczym z potwierdzoną kwotą mediana wynosi $100, a 33% wymaga mniej niż $50. Średnia ($415) jest znacznie zawyżona przez kilka czterocyfrowych wartości skrajnych, dotyczących klientów profesjonalnych lub klientów bankowości prywatnej, dlatego mediana lepiej oddaje sytuację typową.',
                'faq_2_q' => 'Którzy brokerzy forex nie mają minimalnego depozytu?',
                'faq_2_a' => 'Dziesięciu potwierdzonych: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX i FXCC.',
                'faq_3_q' => 'Czy niski minimalny depozyt oznacza, że broker jest lepszy?',
                'faq_3_a' => 'Nie bezpośrednio -- minimalny depozyt nie jest jednym z dziewięciu ważonych komponentów naszego wzoru rankingowego. Jednak nasi potwierdzeni brokerzy z top 40 częściej mają niższe minima (mediana $50) niż pozostały zbiór (mediana $100), prawdopodobnie dlatego, że brokerzy najmocniej konkurujący regulacją i spreadem często konkurują także dostępnością.',
                'faq_4_q' => 'Dlaczego nie wszyscy 139 brokerów ma podany minimalny depozyt?',
                'faq_4_a' => '60 z nich nie ma jednej potwierdzonej kwoty, zwykle dlatego, że rzeczywiste minimum zależy od podmiotu prawnego, waluty konta lub typu konta -- co jest typowe u brokerów działających w wielu jurysdykcjach -- zamiast od jednej stałej kwoty publikowanej przez brokera.',
            ),
            'it' => array(
                'title' => 'Report sul deposito minimo',
                'meta_title' => 'Requisiti di deposito minimo presso 139 forex broker (2026): quanto si può davvero iniziare con poco?',
                'meta_description' => 'Quanto richiedono realmente i forex broker per aprire un conto, da 0 a 10.800 dollari: uno studio di dati originale sull\'intero campione di ricerca di 139 broker, con indicazione dei minimi confermati e dei broker che non ne pubblicano uno.',
                'intro' => 'Quanto denaro serve davvero per aprire un conto raramente riceve lo stesso confronto diretto riservato a spread o regolamentazione. Per questo abbiamo preso il campo min_deposit_usd già utilizzato in tutte le recensioni e nelle liste dei migliori broker di questo sito e lo abbiamo analizzato sull\'intero campione di ricerca. Questo report riguarda ogni importo di deposito minimo confermato: dove si colloca il broker tipico, quali broker non richiedono nulla, quali ne chiedono di importo a quattro cifre e quanta parte del campione non ha un unico importo confermato. Nulla di quanto qui riportato è stato reperito di recente: si tratta dello stesso dato già indicato nella pagina di recensione di ciascun broker.',
                'section_1_heading' => 'Mediana di 100 dollari tra i broker confermati, ma un quarto richiede 10 dollari o meno',
                'section_1_body' => '79 dei 139 broker del nostro campione di ricerca (57%) hanno un importo di deposito minimo confermato; la mediana tra di essi è di 100 dollari. Sotto questa mediana, però, 26 di quei 79 (33%) richiedono meno di 50 dollari e 20 (25%) richiedono 10 dollari o meno. La media è di 415 dollari, molto superiore alla mediana, perché alcuni pochi valori estremi a quattro cifre la spingono verso l\'alto; la mediana è il dato più rappresentativo del costo tipico per aprire un conto.',
                'section_2_heading' => 'Dieci broker confermano un deposito minimo fisso di 0 dollari',
                'section_2_body' => 'ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX e FXCC confermano tutti l\'assenza di un deposito minimo. Occupano posizioni nella nostra classifica che vanno dal 3° al 100° posto, quindi un minimo di 0 dollari non indica di per sé né un broker di vertice né uno debole.',
                'section_3_heading' => 'Pochi broker richiedono minimi a quattro cifre, e si tratta di un tipo di broker diverso',
                'section_3_body' => 'EXANTE (10.800 dollari) e LMAX (10.000 dollari) si collocano ben al di sopra del resto del campione, seguiti da GBE Brokers ed EXCA Prime (entrambi 1.080 dollari) e Swissquote (1.000 dollari). Non si tratta di operatori rivolti prioritariamente al retail, che competono sull\'accessibilità: EXANTE e LMAX sono posizionati verso clienti professionali o istituzionali, mentre Swissquote è una banca privata svizzera. I loro minimi riflettono quindi un cliente target diverso, non un\'offerta peggiore per i trader retail.',
                'section_4_heading' => 'Il deposito minimo non pesa nella nostra classifica, ma i broker meglio posizionati tendono comunque a richiedere importi più bassi',
                'section_4_body' => 'min_deposit_usd non è una delle nove componenti ponderate della nostra formula di punteggio (regolamentazione 30%, costi 20%, commissioni non di trading 10%, piattaforme 10%, esecuzione degli ordini 10%, gamma di prodotti 5%, depositi/prelievi 5%, trasparenza 5%, track record 5%): il deposito minimo di un broker non ha quindi alcun effetto diretto sul suo punteggio. Tuttavia, i nostri primi 40 broker in classifica (32 con un importo confermato) mostrano una mediana di 50 dollari, contro una mediana di 100 dollari per i restanti 99 (47 confermati), e il 59% dei broker confermati tra i primi 40 si colloca sotto i 100 dollari, contro il 36% del resto. Si tratta di una correlazione, non di una relazione causale: i broker che competono con più forza su regolamentazione e spread tendono anche a competere sull\'accessibilità, ma un minimo basso non garantisce una posizione migliore in classifica.',
                'section_5_heading' => '60 broker su 139 non hanno un unico minimo confermato, di solito perché non esiste un importo fisso da confermare',
                'section_5_body' => 'Broker come Interactive Brokers, Vantage Markets, Ultima Markets, CMC Markets, Saxo e HYCM non hanno un importo confermato nella nostra ricerca, più spesso perché il loro minimo reale varia in base all\'entità giuridica, alla valuta del conto o al tipo di conto, anziché essere un unico importo pubblicato una volta per tutte dal broker. Lo registriamo come non confermato, senza ipotizzare un importo né attribuire un valore di 0 dollari.',
                'faq_1_q' => 'Qual è il deposito minimo medio richiesto dai forex broker?',
                'faq_1_a' => 'Tra i 79 broker su 139 del nostro campione con un importo confermato, la mediana è di 100 dollari e il 33% richiede meno di 50 dollari. La media (415 dollari) è spinta molto più in alto da alcuni pochi valori estremi a quattro cifre riservati a clienti professionali o di private banking, quindi la mediana è il dato più rappresentativo.',
                'faq_2_q' => 'Quali forex broker non richiedono un deposito minimo?',
                'faq_2_a' => 'Dieci confermati: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX e FXCC.',
                'faq_3_q' => 'Un deposito minimo basso significa che un broker è migliore?',
                'faq_3_a' => 'Non direttamente: il deposito minimo non è una delle nove componenti ponderate della nostra formula di classifica. Tuttavia, i nostri primi 40 broker confermati tendono a richiedere importi più bassi (mediana di 50 dollari) rispetto al resto del campione (mediana di 100 dollari), probabilmente perché i broker che competono di più su regolamentazione e spread tendono anche a competere sull\'accessibilità.',
                'faq_4_q' => 'Perché non tutti i 139 broker hanno un deposito minimo indicato?',
                'faq_4_a' => '60 non hanno un importo confermato, di solito perché il minimo effettivo varia in base all\'entità giuridica, alla valuta del conto o al tipo di conto, situazione comune presso i broker operativi in più giurisdizioni, anziché un unico importo fisso pubblicato dal broker.',
            ),
            'pt' => array(
                'title' => 'Relatório sobre Depósito Mínimo',
                'meta_title' => 'Requisitos de Depósito Mínimo em 139 Corretoras de Forex (2026): Com quanto pode realmente começar?',
                'meta_description' => 'O que as corretoras de forex efetivamente exigem para abrir uma conta, de $0 a $10,800 -- um estudo de dados original sobre o nosso conjunto completo de 139 corretoras, incluindo os mínimos confirmados e as corretoras que não publicam nenhum.',
                'intro' => 'Quanto dinheiro é efetivamente necessário para abrir uma conta raramente recebe o mesmo tratamento comparativo que os spreads ou a regulação -- por isso, extraímos o campo min_deposit_usd já utilizado em todas as análises e listas das melhores corretoras deste site e contabilizámo-lo em todo o conjunto de investigação. Este relatório abrange todos os valores de depósito mínimo confirmados: onde se situa a corretora típica, quais as corretoras que não exigem nada, quais exigem valores de quatro algarismos e que parte do conjunto de dados não tem sequer um único valor confirmado. Nada aqui foi obtido de novo -- trata-se do mesmo campo já divulgado na análise de cada corretora.',
                'section_1_heading' => 'Mediana de 100 $ entre as corretoras confirmadas, mas um quarto exige 10 $ ou menos',
                'section_1_body' => '79 das 139 corretoras do nosso conjunto de investigação (57%) têm um valor de depósito mínimo confirmado; a mediana entre elas é de 100 $. Abaixo desse valor, no entanto, 26 dessas 79 (33%) exigem menos de 50 $, e 20 (25%) exigem 10 $ ou menos. A média é de 415 $ -- muito superior à mediana -- porque um pequeno número de valores atípicos de quatro algarismos a puxa para cima; a mediana é o valor mais representativo do custo típico de abertura de uma conta.',
                'section_2_heading' => 'Dez corretoras confirmadas com depósito mínimo fixo de 0 $',
                'section_2_body' => 'ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX e FXCC confirmam não ter qualquer depósito mínimo. Estendem-se pela nossa lista de classificação, do 3.º ao 100.º lugar, pelo que um mínimo de 0 $ não indica, por si só, que uma corretora seja nem uma das melhores nem uma das mais fracas.',
                'section_3_heading' => 'Um pequeno número de corretoras exige mínimos de quatro algarismos -- e são corretoras de um tipo diferente',
                'section_3_body' => 'EXANTE (10,800 $) e LMAX (10,000 $) situam-se bem acima do restante conjunto de dados, seguidas da GBE Brokers e da EXCA Prime (ambas com 1,080 $) e da Swissquote (1,000 $). Não se trata de operações viradas para o investidor de retalho, que competem pela acessibilidade -- a EXANTE e a LMAX posicionam-se junto de clientes profissionais/institucionais, e a Swissquote é um banco privado suíço --, pelo que os seus mínimos refletem um cliente-alvo diferente, e não uma pior oferta para os investidores de retalho.',
                'section_4_heading' => 'O depósito mínimo não tem peso na nossa fórmula de classificação, mas as nossas corretoras mais bem classificadas tendem, ainda assim, para valores mais baixos',
                'section_4_body' => 'O min_deposit_usd não é uma das nove componentes ponderadas da nossa fórmula de pontuação (regulação 30%, custo 20%, taxas não relacionadas à negociação 10%, plataformas de negociação 10%, execução de ordens 10%, gama de produtos 5%, depósitos/levantamentos 5%, transparência 5%, histórico de atuação 5%) -- o depósito mínimo de uma corretora não tem qualquer efeito direto na sua pontuação. Mesmo assim, as nossas 40 corretoras melhor classificadas (32 com valor confirmado) apresentam uma mediana de 50 $, contra uma mediana de 100 $ para as restantes 99 (47 confirmadas) -- e 59% das confirmadas no top 40 situam-se abaixo de 100 $, contra 36% das restantes. Trata-se de uma correlação, não de uma afirmação causal: as mesmas corretoras que competem com mais força em regulação e spread tendem também a competir em acessibilidade, mas um mínimo baixo não garante uma melhor classificação.',
                'section_5_heading' => '60 das 139 corretoras não têm um único mínimo confirmado -- geralmente porque não existe um valor fixo único a confirmar',
                'section_5_body' => 'Corretoras como a Interactive Brokers, a Vantage Markets, a Ultima Markets, a CMC Markets, a Saxo e a HYCM não têm um valor confirmado na nossa investigação, sobretudo porque o seu mínimo real varia consoante a entidade jurídica, a moeda da conta ou o tipo de conta, e não corresponde a um único valor que a corretora publique de uma vez. Registamos isso com honestidade como não confirmado, em vez de adivinhar um valor ou de o assumir como 0 $.',
                'faq_1_q' => 'Qual é o depósito mínimo médio exigido pelas corretoras de forex?',
                'faq_1_a' => 'Entre as 79 das 139 corretoras do nosso conjunto de investigação com valor confirmado, a mediana é de 100 $ e 33% exigem menos de 50 $. A média (415 $) é puxada muito para cima por um pequeno número de valores atípicos de quatro algarismos, dirigidos a clientes profissionais ou de banca privada, pelo que a mediana é o valor mais representativo.',
                'faq_2_q' => 'Que corretoras de forex não têm depósito mínimo?',
                'faq_2_a' => 'Dez confirmadas: ThinkMarkets, CFI, OANDA, Fusion Markets, XS Markets, XTB, Afterprime, SquaredFinancial, TopFX e FXCC.',
                'faq_3_q' => 'Um depósito mínimo baixo significa que uma corretora é melhor?',
                'faq_3_a' => 'Não diretamente -- o depósito mínimo não é uma das nove componentes ponderadas da nossa fórmula de classificação. Mas as nossas 40 principais corretoras confirmadas tendem a apresentar valores mais baixos (mediana de 50 $) do que o restante conjunto (mediana de 100 $), possivelmente porque as corretoras que competem com mais força em regulação e spread tendem também a competir em acessibilidade.',
                'faq_4_q' => 'Porque é que nem todas as 139 corretoras têm um depósito mínimo indicado?',
                'faq_4_a' => '60 não têm um único valor confirmado, geralmente porque o mínimo real varia consoante a entidade jurídica, a moeda da conta ou o tipo de conta -- situação comum em corretoras com presença em várias jurisdições -- em vez de existir um valor fixo que a corretora publique.',
            ),
        ),
        'eurusd-spread-report' => array(
            'es' => array(
                'title' => 'Informe de spread EUR/USD',
                'meta_title' => 'Informe de spread EUR/USD (2026): cuánto cuesta realmente operar en 139 brókers de forex',
                'meta_description' => 'La distribución real de spreads en todo nuestro conjunto de brókers -- la mediana, el spread más ajustado, el más amplio y qué brókers conocidos no publican una cifra confirmada -- calculada a partir del mismo campo que sustenta cada puntuación de costes de este sitio.',
                'intro' => 'El spread EUR/USD de un bróker es el único número al que la mayoría de las comparaciones de costes reducen todo, pero los datos de spreads comparados lado a lado en un conjunto amplio y real de brókers son más escasos de lo que deberían. Extrajimos el campo spread_eurusd que ya se utiliza en las reseñas, la calculadora de costes y la fórmula de puntuación de este sitio y lo contamos en el conjunto completo de 139 brókers. Este informe cubre la distribución real: dónde se sitúa el bróker confirmado típico, qué tan ajustados llegan a ser realmente los spreads más estrechos y qué parte del conjunto no tiene ninguna cifra confirmada.',
                'section_1_heading' => 'Una mediana de 1,1 pips entre los brókers confirmados, con un tercio por debajo de 1,0 pip',
                'section_1_body' => '67 de los 139 brókers de nuestro conjunto de investigación (48%) tienen un spread EUR/USD confirmado; la mediana entre ellos es de 1,1 pips, y la media es aún más alta (1,25 pips), elevada por un puñado de valores atípicos amplios. Por debajo de esa mediana, 23 de los 67 (34%) confirman un spread inferior a 1,0 pip, y 6 (9%) confirman menos de 0,5 pips.',
                'section_2_heading' => 'Un único bróker tiene confirmado un spread EUR/USD plano de 0,0 pips, con una advertencia que conviene señalar con claridad',
                'section_2_body' => 'FXView es el único bróker de nuestro conjunto de datos con un spread EUR/USD confirmado de 0,0 pips. Un spread anunciado de 0,0 pips es habitual en cuentas tipo ECN/raw que cobran una comisión aparte por lote en lugar de incorporar el coste en el spread; no significa necesariamente que operar sea gratis, sino que el coste está estructurado de otra manera. Confirma directamente el coste total (spread más cualquier comisión) antes de dar por hecho que la cifra anunciada más baja corresponde automáticamente a la cuenta más barata.',
                'section_3_heading' => 'Un puñado de brókers confirma spreads de 2 pips o más',
                'section_3_body' => 'LiteForex (2,0), TrioMarkets (2,4), Fortrade (2,5), Trading 212 (2,7), InstaForex (3,0), Trade360 (4,0) y OEXN (5,0) se sitúan muy por encima del resto del conjunto confirmado. Un spread anunciado más amplio no es automáticamente una señal de alarma -- puede reflejar un modelo de cuenta distinto o una base de clientes diferente --, pero es una diferencia de coste real y confirmada que conviene conocer antes de comparar estos brókers con uno de spread más ajustado en cualquier otro aspecto.',
                'section_4_heading' => 'Nuestros brókers mejor clasificados confirman spreads más ajustados, y aquí la correlación es en parte por diseño',
                'section_4_body' => 'Los brókers clasificados en nuestro top 40 (31 con spread confirmado) muestran una mediana de 1,0 pip, con un 48% que confirma menos de 1,0 pip; los 99 restantes (36 confirmados) muestran una mediana de 1,5 pips, con solo un 22% por debajo de 1,0 pip. A diferencia del depósito mínimo, que no tiene peso en nuestra fórmula, el spread EUR/USD influye directamente en nuestra puntuación de costes con una ponderación del 20%, el segundo factor individual más pesado después de la regulación. Por tanto, este patrón no es solo una correlación que señalamos a posteriori: un bróker con un spread confirmado más ajustado puntúa mejor en este factor por construcción, no solo tiende a hacerlo.',
                'section_5_heading' => '72 de 139 brókers no tienen un spread EUR/USD confirmado, incluidos algunos nombres conocidos y bien regulados',
                'section_5_body' => 'Interactive Brokers (puesto 2), OANDA (puesto 6), Vantage Markets, Ultima Markets, CMC Markets y varios más no tienen una cifra confirmada en nuestra investigación, sobre todo por una fijación de precios variable o de estilo market maker sin una cifra de spread anunciada, y no por ninguna carencia en cuanto a regulación o notoriedad del bróker. Nuestra puntuación de costes para estos brókers se excluye de su media general en lugar de estimarse o asignarles la mediana del conjunto.',
                'faq_1_q' => '¿Cuál es el spread EUR/USD medio entre los brókers de forex?',
                'faq_1_a' => 'Entre los 67 de 139 brókers de nuestro conjunto de investigación con una cifra confirmada, la mediana es de 1,1 pips. La media (1,25 pips) está elevada por un puñado de valores atípicos amplios, así que la mediana es la cifra más representativa.',
                'faq_2_q' => '¿Qué bróker tiene el spread EUR/USD confirmado más ajustado?',
                'faq_2_a' => 'FXView, con 0,0 pips planos, aunque un spread anunciado de 0,0 pips suele significar que el coste pasa a una comisión aparte, por lo que no es automáticamente la cuenta más barata en conjunto.',
                'faq_3_q' => '¿Un spread EUR/USD más bajo implica que un bróker puntúa mejor?',
                'faq_3_a' => 'Sí, directamente: a diferencia del depósito mínimo, el spread EUR/USD tiene una ponderación del 20% en nuestra puntuación de costes, el segundo factor individual más pesado de nuestra fórmula general después de la regulación.',
                'faq_4_q' => '¿Por qué no todos los 139 brókers tienen un spread EUR/USD confirmado?',
                'faq_4_a' => '72 no lo tienen, normalmente por una fijación de precios variable o de estilo market maker sin una cifra de spread anunciada, incluidos algunos nombres conocidos como Interactive Brokers y OANDA.',
            ),
            'de' => array(
                'title' => 'EUR/USD-Spread-Bericht',
                'meta_title' => 'EUR/USD-Spread-Bericht (2026): Was der Handel tatsächlich kostet – verglichen bei 139 Forex-Brokern',
                'meta_description' => 'Die tatsächliche Spread-Verteilung über unseren gesamten Broker-Datensatz – Median, engste und weiteste Spreads sowie welche bekannten Broker überhaupt keinen bestätigten Wert veröffentlichen – berechnet aus demselben Feld, das hinter jeder Kostenbewertung auf dieser Seite steht.',
                'intro' => 'Der EUR/USD-Spread eines Brokers ist die einzelne Zahl, auf die sich die meisten Kostenvergleiche reduzieren, doch vergleichbare Spread-Daten über eine echte, breite Gruppe von Brokern sind seltener, als sie sein sollten. Wir haben das Feld spread_eurusd ausgewertet, das bereits in den Testberichten, im Kostenrechner und in der Bewertungsformel dieser Seite verwendet wird, und es über den gesamten Datensatz von 139 Brokern ausgezählt. Dieser Bericht zeigt die tatsächliche Verteilung: wo der typische bestätigte Broker liegt, wie eng die engsten Spreads tatsächlich sind und wie viel des Datensatzes gar keinen bestätigten Wert hat.',
                'section_1_heading' => 'Ein Median von 1.1 Pips unter den bestätigten Brokern, wobei ein Drittel unter 1.0 Pip liegt',
                'section_1_body' => '67 der 139 Broker in unserem Datensatz (48%) haben einen bestätigten EUR/USD-Spread; der Median liegt bei 1.1 Pips, und der Mittelwert liegt noch höher (1.25 Pips), was durch einige wenige weite Ausreißer nach oben gezogen wird. Unterhalb dieses Medians bestätigen 23 der 67 (34%) einen Spread unter 1.0 Pip und 6 (9%) unter 0.5 Pips.',
                'section_2_heading' => 'Ein Broker bestätigte einen festen Spread von 0.0 Pips für EUR/USD – mit einem Vorbehalt, der klar benannt werden sollte',
                'section_2_body' => 'FXView ist der einzige Broker in unserem Datensatz, der einen Headline-Spread von 0.0 Pips für EUR/USD bestätigt hat. Ein Headline-Spread von 0.0 Pips ist bei ECN/Raw-Konten üblich, die stattdessen eine separate Kommission pro Lot berechnen, anstatt die Kosten in den Spread einzurechnen. Das bedeutet nicht zwingend, dass der Handel kostenlos ist, sondern lediglich, dass die Kosten anders strukturiert sind. Bestätigen Sie die gesamten Kosten (Spread plus eventuelle Kommission) direkt, bevor Sie davon ausgehen, dass die niedrigste Headline-Zahl automatisch das günstigste Konto ist.',
                'section_3_heading' => 'Einige wenige Broker bestätigen Spreads von 2 Pips oder mehr',
                'section_3_body' => 'LiteForex (2.0), TrioMarkets (2.4), Fortrade (2.5), Trading 212 (2.7), InstaForex (3.0), Trade360 (4.0) und OEXN (5.0) liegen deutlich über dem Rest des bestätigten Datensatzes. Ein breiterer Headline-Spread ist nicht automatisch ein Warnsignal – er kann ein anderes Kontomodell oder eine andere Kundenbasis widerspiegeln –, aber es handelt sich um einen echten, bestätigten Kostenunterschied, den Sie kennen sollten, bevor Sie diese Broker bei anderen Kriterien mit einem Broker mit engerem Spread vergleichen.',
                'section_4_heading' => 'Unsere besser platzierten Broker bestätigen engere Spreads – und hier ist die Korrelation teilweise beabsichtigt',
                'section_4_body' => 'Broker, die in unseren Top 40 platziert sind (31 mit bestätigtem Spread), zeigen einen Median von 1.0 Pips, wobei 48% einen Wert unter 1.0 Pip bestätigen; die übrigen 99 (36 bestätigt) zeigen einen Median von 1.5 Pips, wobei nur 22% unter 1.0 Pip liegen. Anders als die Mindesteinzahlung, die in unserer Formel keine Gewichtung hat, fließt der EUR/USD-Spread mit einer Gewichtung von 20% direkt in unsere Kostenbewertung ein – der zweitwichtigste Einzelfaktor nach der Regulierung. Dieses Muster ist also nicht nur eine Korrelation, die wir nachträglich feststellen: Ein Broker mit einem engeren bestätigten Spread erzielt bei diesem Faktor konstruktionsbedingt automatisch eine bessere Bewertung und nicht nur tendenziell.',
                'section_5_heading' => '72 von 139 Brokern haben keinen bestätigten EUR/USD-Spread – darunter einige bekannte, gut regulierte Namen',
                'section_5_body' => 'Interactive Brokers (Rang 2), OANDA (Rang 6), Vantage Markets, Ultima Markets, CMC Markets und einige andere haben in unserer Recherche keinen bestätigten Wert, meist wegen variabler oder Market-Maker-artiger Preisgestaltung ohne einen veröffentlichten Headline-Wert – und nicht wegen einer Lücke bei Regulierung oder Bekanntheit des Brokers. Unsere Kostenbewertung für diese Broker wird aus deren Gesamtdurchschnitt ausgeschlossen, statt sie zu schätzen oder auf den Median des Datensatzes zu setzen.',
                'faq_1_q' => 'Wie hoch ist der durchschnittliche EUR/USD-Spread bei Forex-Brokern?',
                'faq_1_a' => 'Bei den 67 von 139 Brokern in unserem Datensatz mit bestätigtem Wert liegt der Median bei 1.1 Pips. Der Mittelwert (1.25 Pips) wird durch einige wenige weite Ausreißer nach oben gezogen, daher ist der Median die repräsentativere Zahl.',
                'faq_2_q' => 'Welcher Broker hat den engsten bestätigten EUR/USD-Spread?',
                'faq_2_a' => 'FXView, mit festen 0.0 Pips – allerdings bedeutet ein Headline-Spread von 0.0 Pips oft, dass die Kosten stattdessen in eine separate Kommission verlagert wurden, daher ist es nicht automatisch das insgesamt günstigste Konto.',
                'faq_3_q' => 'Erzielt ein Broker mit einem niedrigeren EUR/USD-Spread eine bessere Bewertung?',
                'faq_3_a' => 'Ja, direkt – anders als die Mindesteinzahlung wird der EUR/USD-Spread mit 20% in unserer Kostenbewertung gewichtet, dem zweitwichtigsten Einzelfaktor in unserer Gesamtformel nach der Regulierung.',
                'faq_4_q' => 'Warum haben nicht alle 139 Broker einen bestätigten EUR/USD-Spread?',
                'faq_4_a' => '72 haben keinen, meist wegen variabler oder Market-Maker-artiger Preisgestaltung ohne einen veröffentlichten Headline-Wert – darunter einige bekannte Namen wie Interactive Brokers und OANDA.',
            ),
            'ar' => array(
                'title' => 'تقرير الفرق السعري لزوج EUR/USD',
                'meta_title' => 'تقرير الفرق السعري لزوج EUR/USD (2026): كيف تبدو تكاليف التداول فعلياً عبر 139 وسيطاً للفوركس',
                'meta_description' => 'التوزيع الفعلي للفرق السعري عبر مجموعة بياناتنا الكاملة للوسطاء، بما في ذلك الوسيط والأضيق والأوسع، والوسطاء المعروفون الذين لا ينشرون رقماً مؤكداً على الإطلاق، محسوباً من الحقل نفسه الذي يقوم عليه كل تقييم للتكلفة في هذا الموقع.',
                'intro' => 'الفرق السعري لزوج EUR/USD لدى الوسيط هو الرقم الوحيد الذي تختزل إليه معظم مقارنات التكاليف كل شيء، لكن بيانات الفروق السعرية جنباً إلى جنب عبر مجموعة واسعة وحقيقية من الوسطاء نادرة أكثر مما ينبغي. استخرجنا حقل spread_eurusd المستخدم أصلاً في مراجعات هذا الموقع وحاسبة التكاليف ومعادلة التقييم، وأحصيناه عبر المجموعة الكاملة البالغة 139 وسيطاً. يغطي هذا التقرير التوزيع الفعلي: أين يقع الوسيط المؤكد المعتاد، ومدى ضيق أضيق الفروق السعرية فعلياً، ومقدار ما في مجموعة البيانات بلا رقم مؤكد على الإطلاق.',
                'section_1_heading' => 'وسيط قدره 1.1 بيب بين الوسطاء المؤكدين، مع ثلث دون 1.0 بيب',
                'section_1_body' => 'يملك 67 من الوسطاء الـ139 في مجموعة بحثنا (48%) فرقاً سعرياً مؤكداً لزوج EUR/USD؛ والوسيط بينهم هو 1.1 بيب، والمتوسط الحسابي أعلى من ذلك (1.25 بيب)، إذ ترفعه بعض القيم الشاذة الواسعة. وتحت هذا الوسيط، يؤكد 23 من أصل 67 (34%) فرقاً أقل من 1.0 بيب، ويؤكد 6 (9%) فرقاً أقل من 0.5 بيب.',
                'section_2_heading' => 'وسيط واحد أكد فرقاً سعرياً ثابتاً قدره 0.0 بيب لزوج EUR/USD، مع تحفظ يستحق الذكر بوضوح',
                'section_2_body' => 'FXView هو الوسيط الوحيد في مجموعة بياناتنا المؤكد عند فرق سعري عنواني قدره 0.0 بيب لزوج EUR/USD. ويشيع العنوان 0.0 بيب في الحسابات من نوع ECN/الخام التي تفرض عمولة منفصلة لكل لوت بدلاً من إدراج التكلفة ضمن الفرق السعري، وهذا لا يعني بالضرورة أن التداول مجاني، بل أن التكلفة مهيكلة بطريقة مختلفة. تحقق من التكلفة الكاملة (الفرق السعري مع أي عمولة) مباشرة قبل افتراض أن أدنى رقم عنواني هو تلقائياً أرخص حساب.',
                'section_3_heading' => 'عدد قليل من الوسطاء يؤكدون فروقاً سعرية قدرها 2 بيب أو أكثر',
                'section_3_body' => 'تقع LiteForex (2.0) وTrioMarkets (2.4) وFortrade (2.5) وTrading 212 (2.7) وInstaForex (3.0) وTrade360 (4.0) وOEXN (5.0) أعلى بكثير من بقية المجموعة المؤكدة. والفرق السعري العنواني الأوسع ليس بالضرورة علامة تحذير، فقد يعكس نموذج حساب مختلفاً أو قاعدة عملاء مختلفة، لكنه فرق تكلفة مؤكد وحقيقي يستحق المعرفة قبل مقارنة هؤلاء الوسطاء بوسيط ذي فرق أضيق في أي جانب آخر.',
                'section_4_heading' => 'وسطاؤنا الأعلى ترتيباً يؤكدون فروقاً أضيق، والارتباط هنا جزئياً متعمد',
                'section_4_body' => 'يُظهر الوسطاء المصنفون ضمن أفضل 40 لدينا (31 منهم لديهم فرق سعري مؤكد) وسيطاً قدره 1.0 بيب، مع تأكيد 48% منهم لفرق أقل من 1.0 بيب؛ أما الـ99 المتبقون (36 مؤكداً) فيظهر وسيطهم 1.5 بيب، مع 22% فقط دون 1.0 بيب. وعلى خلاف الحد الأدنى للإيداع، الذي لا يملك أي وزن في معادلتنا، يدخل الفرق السعري لزوج EUR/USD مباشرة في تقييم التكلفة لدينا بوزن قدره 20%، وهو ثاني أكبر عامل منفرد بعد التنظيم. لذا فهذا النمط ليس مجرد ارتباط نلاحظه بعد وقوعه: فالوسيط ذو الفرق المؤكد الأضيق يحصل آلياً على تقييم أفضل في هذا العامل بحكم التصميم، لا مجرد أنه يميل إلى ذلك.',
                'section_5_heading' => '72 من أصل 139 وسيطاً لا يملكون فرقاً سعرياً مؤكداً لزوج EUR/USD، بينهم أسماء معروفة وخاضعة لتنظيم جيد',
                'section_5_body' => 'لا يملك Interactive Brokers (المرتبة 2) وOANDA (المرتبة 6) وVantage Markets وUltima Markets وCMC Markets وعدد من الوسطاء الآخرين رقماً مؤكداً في بحثنا، ويرجع ذلك غالباً إلى تسعير متغير أو على طريقة صانع السوق دون رقم عنواني منشور واحد، لا إلى أي نقص في مستوى التنظيم أو الشهرة لدى الوسيط. ويُستبعد تقييم التكلفة لهؤلاء الوسطاء من متوسطهم الإجمالي بدلاً من تخمينه أو إسناده افتراضياً إلى وسيط مجموعة البيانات.',
                'faq_1_q' => 'ما متوسط الفرق السعري لزوج EUR/USD لدى وسطاء الفوركس؟',
                'faq_1_a' => 'عبر الـ67 من أصل 139 وسيطاً في مجموعة بحثنا الذين لديهم رقم مؤكد، يبلغ الوسيط 1.1 بيب. ويرفع المتوسط الحسابي (1.25 بيب) بعض القيم الشاذة الواسعة، لذا فالوسيط هو الرقم الأكثر تمثيلاً.',
                'faq_2_q' => 'أي وسيط يملك أضيق فرق سعري مؤكد لزوج EUR/USD؟',
                'faq_2_a' => 'FXView، عند 0.0 بيب ثابتة، لكن الفرق السعري العنواني 0.0 بيب يعني غالباً أن التكلفة انتقلت إلى عمولة منفصلة بدلاً من ذلك، لذا فهو ليس تلقائياً الحساب الأرخص إجمالاً.',
                'faq_3_q' => 'هل يعني الفرق السعري الأدنى لزوج EUR/USD أن الوسيط يحصل على تقييم أفضل؟',
                'faq_3_a' => 'نعم، بشكل مباشر، فعلى خلاف الحد الأدنى للإيداع، يُرجَّح الفرق السعري لزوج EUR/USD بنسبة 20% في تقييم التكلفة لدينا، وهو ثاني أكبر عامل منفرد في معادلتنا الإجمالية بعد التنظيم.',
                'faq_4_q' => 'لماذا لا يملك جميع الوسطاء الـ139 فرقاً سعرياً مؤكداً لزوج EUR/USD؟',
                'faq_4_a' => '72 وسيطاً لا يملكونه، ويرجع ذلك غالباً إلى تسعير متغير أو على طريقة صانع السوق دون رقم عنواني منشور واحد، ومنهم أسماء معروفة مثل Interactive Brokers وOANDA.',
            ),
            'fr' => array(
                'title' => 'Rapport sur le spread EUR/USD',
                'meta_title' => 'Rapport sur le spread EUR/USD (2026) : à combien revient réellement le trading chez 139 courtiers forex',
                'meta_description' => 'La distribution réelle des spreads sur l\'ensemble de notre base de courtiers -- médiane, spread le plus serré, le plus large, et les courtiers bien connus qui ne publient aucun chiffre confirmé -- calculée à partir du même champ que chaque note de coût publiée sur ce site.',
                'intro' => 'Le spread EUR/USD d\'un courtier est le chiffre unique auquel la plupart des comparatifs de coûts ramènent tout, pourtant les données de spread comparatives sur un ensemble réel et large de courtiers sont plus rares qu\'elles ne devraient l\'être. Nous avons extrait le champ spread_eurusd déjà utilisé dans l\'ensemble des analyses, du calculateur de coûts et de la formule de notation de ce site, puis nous l\'avons compté sur l\'ensemble des 139 courtiers. Ce rapport présente la distribution réelle : où se situe le courtier confirmé typique, jusqu\'où descendent réellement les spreads les plus serrés, et quelle part de la base n\'a aucun chiffre confirmé.',
                'section_1_heading' => 'Une médiane de 1,1 pip parmi les courtiers confirmés, avec un tiers sous 1,0 pip',
                'section_1_body' => '67 des 139 courtiers de notre base d\'étude (48 %) ont un spread EUR/USD confirmé ; la médiane parmi eux est de 1,1 pip, et la moyenne est encore plus élevée (1,25 pip), tirée vers le haut par quelques valeurs extrêmes élevées. En dessous de cette médiane, 23 des 67 (34 %) confirment un spread inférieur à 1,0 pip, et 6 (9 %) confirment un spread inférieur à 0,5 pip.',
                'section_2_heading' => 'Un seul courtier affiche un spread EUR/USD fixe de 0,0 pip, avec une mise en garde qu\'il convient d\'énoncer clairement',
                'section_2_body' => 'FXView est le seul courtier de notre base à afficher un spread EUR/USD de 0,0 pip. Un affichage de 0,0 pip est courant sur les comptes de type ECN/raw qui facturent une commission distincte par lot au lieu d\'intégrer le coût dans le spread ; cela ne signifie pas nécessairement que le trading est gratuit, mais que le coût est structuré différemment. Vérifiez le coût total (spread plus commission éventuelle) directement auprès du courtier avant de considérer que le chiffre affiché le plus bas correspond automatiquement au compte le moins cher.',
                'section_3_heading' => 'Quelques courtiers affichent des spreads confirmés de 2 pips ou plus',
                'section_3_body' => 'LiteForex (2,0), TrioMarkets (2,4), Fortrade (2,5), Trading 212 (2,7), InstaForex (3,0), Trade360 (4,0) et OEXN (5,0) se situent nettement au-dessus du reste de l\'ensemble confirmé. Un spread affiché plus large n\'est pas automatiquement un signal d\'alerte : il peut refléter un modèle de compte ou une clientèle différente. Il s\'agit néanmoins d\'un écart de coût réel et confirmé, à connaître avant de comparer ces courtiers à un courtier aux spreads plus serrés sur d\'autres critères.',
                'section_4_heading' => 'Nos courtiers les mieux classés affichent des spreads plus serrés, et cette corrélation est en partie par conception',
                'section_4_body' => 'Les courtiers classés dans notre top 40 (31 avec un spread confirmé) affichent une médiane de 1,0 pip, avec 48 % confirmant un spread inférieur à 1,0 pip ; les 99 autres (36 confirmés) affichent une médiane de 1,5 pip, avec seulement 22 % sous 1,0 pip. Contrairement au dépôt minimum, qui ne pèse pas dans notre formule, le spread EUR/USD entre directement dans notre note de coût avec une pondération de 20 %, soit le deuxième facteur individuel le plus lourd après la réglementation. Ce schéma n\'est donc pas seulement une corrélation que nous constatons après coup : un courtier au spread confirmé plus serré obtient mécaniquement une meilleure note sur ce critère, par construction et non simplement par tendance.',
                'section_5_heading' => '72 des 139 courtiers n\'ont aucun spread EUR/USD confirmé, dont certains noms bien connus et bien réglementés',
                'section_5_body' => 'Interactive Brokers (rang 2), OANDA (rang 6), Vantage Markets, Ultima Markets, CMC Markets et plusieurs autres n\'ont pas de chiffre confirmé dans notre étude, le plus souvent en raison d\'une tarification variable ou de type market maker sans chiffre de spread affiché unique, et non en raison d\'un manque de réglementation ou de notoriété du courtier. Pour ces courtiers, notre note de coût est exclue de leur moyenne globale, plutôt que d\'être estimée ou ramenée à la médiane de la base.',
                'faq_1_q' => 'Quel est le spread EUR/USD moyen chez les courtiers forex ?',
                'faq_1_a' => 'Parmi les 67 courtiers sur 139 de notre base d\'étude qui affichent un chiffre confirmé, la médiane est de 1,1 pip. La moyenne (1,25 pip) est tirée vers le haut par quelques valeurs extrêmes élevées ; la médiane est donc le chiffre le plus représentatif.',
                'faq_2_q' => 'Quel courtier affiche le spread EUR/USD confirmé le plus serré ?',
                'faq_2_a' => 'FXView, avec un spread fixe de 0,0 pip, mais un spread affiché de 0,0 pip signifie souvent que le coût est reporté sur une commission distincte, ce qui ne fait donc pas automatiquement de ce compte le moins cher dans l\'absolu.',
                'faq_3_q' => 'Un spread EUR/USD plus bas signifie-t-il qu\'un courtier obtient une meilleure note ?',
                'faq_3_a' => 'Oui, directement : contrairement au dépôt minimum, le spread EUR/USD est pondéré à 20 % dans notre note de coût, soit le deuxième facteur individuel le plus lourd de notre formule globale après la réglementation.',
                'faq_4_q' => 'Pourquoi les 139 courtiers n\'ont-ils pas tous un spread EUR/USD confirmé ?',
                'faq_4_a' => '72 d\'entre eux n\'en ont pas, généralement en raison d\'une tarification variable ou de type market maker sans chiffre de spread unique publié, y compris certains noms bien connus comme Interactive Brokers et OANDA.',
            ),
            'pl' => array(
                'title' => 'Raport o spreadzie EUR/USD',
                'meta_title' => 'Raport o spreadzie EUR/USD (2026): Jak naprawdę wyglądają koszty handlu u 139 brokerów forex',
                'meta_description' => 'Rzeczywisty rozkład spreadów w całym naszym zbiorze danych o brokerach: mediana, najwęższy i najszerszy spread oraz informacja, którzy znani brokerzy w ogóle nie podają potwierdzonej wartości, obliczone na podstawie tego samego pola, na którym opiera się każda ocena kosztów na tej stronie.',
                'intro' => 'Spread EUR/USD brokera to pojedyncza liczba, do której najczęściej sprowadza się każde porównanie kosztów, a zestawienie spreadów w rzeczywistym, szerokim zbiorze brokerów jest rzadsze, niż powinno być. Pobraliśmy pole spread_eurusd, którego ten serwis używa we wszystkich recenzjach, kalkulatorze kosztów i wzorze oceny, i zliczyliśmy je dla pełnego zbioru 139 brokerów. Raport przedstawia rzeczywisty rozkład: gdzie znajduje się typowy potwierdzony broker, jak wąskie są najwęższe spready i jaka część zbioru nie ma w ogóle potwierdzonej wartości.',
                'section_1_heading' => 'Mediana 1,1 pipsa wśród potwierdzonych brokerów, a co trzeci poniżej 1,0 pipsa',
                'section_1_body' => '67 spośród 139 brokerów w naszym zbiorze badawczym (48%) ma potwierdzony spread EUR/USD; mediana wśród nich wynosi 1,1 pipsa, a średnia jest jeszcze wyższa (1,25 pipsa), podciągnięta w górę przez kilka skrajnie szerokich wartości. Co więcej, 23 spośród 67 (34%) potwierdza spread poniżej 1,0 pipsa, a 6 (9%) poniżej 0,5 pipsa.',
                'section_2_heading' => 'Jeden broker potwierdził stały spread EUR/USD na poziomie 0,0 pipsa, z zastrzeżeniem, które warto wyraźnie podkreślić',
                'section_2_body' => 'FXView jest jedynym brokerem w naszym zbiorze, który potwierdził nagłówkowy spread EUR/USD na poziomie 0,0 pipsa. Nagłówkowy spread na poziomie 0,0 pipsa jest częsty na kontach ECN/raw, które pobierają osobną prowizję za lot zamiast wliczać koszt w spread. Nie oznacza to koniecznie, że handel jest bezpłatny, a jedynie że koszt jest ustrukturyzowany inaczej. Przed uznaniem, że najniższa nagłówkowa wartość automatycznie oznacza najtańsze konto, należy bezpośrednio sprawdzić pełny koszt (spread oraz ewentualną prowizję).',
                'section_3_heading' => 'Kilku brokerów potwierdza spready na poziomie 2 pipsów lub więcej',
                'section_3_body' => 'LiteForex (2,0), TrioMarkets (2,4), Fortrade (2,5), Trading 212 (2,7), InstaForex (3,0), Trade360 (4,0) i OEXN (5,0) plasują się wyraźnie powyżej reszty potwierdzonego zbioru. Szerszy nagłówkowy spread nie jest automatycznie sygnałem ostrzegawczym; może odzwierciedlać inny model konta lub inną bazę klientów. Stanowi jednak realną, potwierdzoną różnicę w kosztach, o której warto wiedzieć, zanim porówna się tych brokerów z brokerem o węższym spreadzie pod jakimkolwiek innym względem.',
                'section_4_heading' => 'Wyżej oceniani brokerzy potwierdzają węższe spready, a ta zależność jest po części zamierzona',
                'section_4_body' => 'Brokerzy z naszej czołowej 40 (31 z potwierdzonym spreadem) wykazują medianę 1,0 pipsa, przy czym 48% potwierdza wartość poniżej 1,0 pipsa; pozostała grupa 99 brokerów (36 z potwierdzonym spreadem) wykazuje medianę 1,5 pipsa, a tylko 22% poniżej 1,0 pipsa. W przeciwieństwie do minimalnego depozytu, który nie ma wpływu na nasz wzór, spread EUR/USD bezpośrednio wpływa na ocenę kosztów z wagą 20%, będąc drugim co do wagi pojedynczym czynnikiem po regulacji. Ten wzorzec nie jest więc jedynie korelacją zauważoną po fakcie: broker z węższym potwierdzonym spreadem z konstrukcji otrzymuje lepszą ocenę w tym kryterium, a nie jedynie zwykle ją otrzymuje.',
                'section_5_heading' => '72 spośród 139 brokerów nie ma potwierdzonego spreadu EUR/USD, w tym niektóre znane i dobrze uregulowane firmy',
                'section_5_body' => 'Interactive Brokers (pozycja 2), OANDA (pozycja 6), Vantage Markets, Ultima Markets, CMC Markets i kilku innych nie mają potwierdzonej wartości w naszym badaniu, najczęściej ze względu na zmienne lub oparte na modelu market makera ustalanie cen bez jednej opublikowanej nagłówkowej wartości, a nie z powodu jakichkolwiek braków w zakresie regulacji czy rozpoznawalności danego brokera. Ocena kosztów tych brokerów jest wyłączona z ich średniej ogólnej, zamiast być szacowana lub zastępowana medianą zbioru danych.',
                'faq_1_q' => 'Jaki jest średni spread EUR/USD u brokerów forex?',
                'faq_1_a' => 'Wśród 67 z 139 brokerów w naszym zbiorze badawczym, dla których podano potwierdzoną wartość, mediana wynosi 1,1 pipsa. Średnia (1,25 pipsa) jest podciągnięta w górę przez kilka skrajnie szerokich wartości, dlatego mediana jest bardziej reprezentatywną liczbą.',
                'faq_2_q' => 'Który broker ma najwęższy potwierdzony spread EUR/USD?',
                'faq_2_a' => 'FXView, przy stałym poziomie 0,0 pipsa. Nagłówkowe 0,0 pipsa często oznacza jednak, że koszt przeniesiono do osobnej prowizji, dlatego nie jest to automatycznie najtańsze konto ogółem.',
                'faq_3_q' => 'Czy niższy spread EUR/USD oznacza lepszą ocenę brokera?',
                'faq_3_a' => 'Tak, bezpośrednio. W przeciwieństwie do minimalnego depozytu spread EUR/USD ma w naszej ocenie kosztów wagę 20%, będąc drugim co do wagi pojedynczym czynnikiem w naszym ogólnym wzorze po regulacji.',
                'faq_4_q' => 'Dlaczego nie wszyscy 139 brokerów ma potwierdzony spread EUR/USD?',
                'faq_4_a' => '72 z nich go nie mają, zwykle ze względu na zmienne lub oparte na modelu market makera ustalanie cen bez jednej opublikowanej nagłówkowej wartości, w tym niektóre znane nazwy, takie jak Interactive Brokers i OANDA.',
            ),
            'it' => array(
                'title' => 'Report sugli spread EUR/USD',
                'meta_title' => 'Report sugli spread EUR/USD (2026): quanto costa davvero il trading tra 139 broker forex',
                'meta_description' => 'La distribuzione reale degli spread nel nostro intero dataset di broker -- mediana, valori più bassi, più alti e quali broker noti non pubblicano affatto un dato confermato -- calcolata dallo stesso campo alla base di ogni punteggio sui costi di questo sito.',
                'intro' => 'Lo spread EUR/USD di un broker è il singolo numero a cui la maggior parte dei confronti sui costi riduce tutto, eppure i dati comparativi sugli spread per un insieme reale e ampio di broker sono più rari di quanto dovrebbero essere. Abbiamo estratto il campo spread_eurusd già utilizzato in tutte le recensioni, nel calcolatore dei costi e nella formula di punteggio di questo sito, e lo abbiamo conteggiato sull\'intero insieme di 139 broker. Questo report illustra la distribuzione reale: dove si colloca il broker tipico con un dato confermato, quanto possono essere davvero stretti gli spread più bassi e quanta parte del dataset non ha alcun dato confermato.',
                'section_1_heading' => 'Una mediana di 1,1 pip tra i broker confermati, con un terzo sotto 1,0 pip',
                'section_1_body' => '67 dei 139 broker nel nostro insieme di ricerca (48%) hanno uno spread EUR/USD confermato; la mediana tra questi è di 1,1 pip, mentre la media è ancora più alta (1,25 pip), trascinata verso l\'alto da alcuni valori anomali molto ampi. Al di sotto di questa mediana, 23 dei 67 (34%) confermano uno spread inferiore a 1,0 pip e 6 (9%) confermano valori inferiori a 0,5 pip.',
                'section_2_heading' => 'Un solo broker conferma uno spread EUR/USD piatto di 0,0 pip -- con un\'avvertenza da segnalare chiaramente',
                'section_2_body' => 'FXView è l\'unico broker del nostro dataset confermato con uno spread EUR/USD dichiarato di 0,0 pip. Un dato dichiarato di 0,0 pip è comune sui conti ECN/raw, che addebitano una commissione separata per lotto invece di incorporare il costo nello spread: non significa necessariamente che il trading sia gratuito, ma solo che il costo è strutturato in modo diverso. Il costo complessivo (spread più eventuali commissioni) va verificato direttamente prima di dare per scontato che il valore più basso dichiarato corrisponda automaticamente al conto più economico.',
                'section_3_heading' => 'Alcuni broker confermano spread di 2 pip o più',
                'section_3_body' => 'LiteForex (2.0), TrioMarkets (2.4), Fortrade (2.5), Trading 212 (2.7), InstaForex (3.0), Trade360 (4.0) e OEXN (5.0) si collocano ben al di sopra del resto dell\'insieme confermato. Uno spread dichiarato più ampio non è automaticamente un campanello d\'allarme: può riflettere un modello di conto o una base di clienti diversi, ma rappresenta una differenza di costo reale e confermata, che conviene conoscere prima di confrontare questi broker con uno a spread più stretto su qualsiasi altro aspetto.',
                'section_4_heading' => 'I nostri broker meglio classificati confermano spread più stretti -- e qui la correlazione è in parte voluta',
                'section_4_body' => 'I broker classificati tra i nostri primi 40 (31 con uno spread confermato) mostrano una mediana di 1,0 pip, con il 48% che conferma valori inferiori a 1,0 pip; i restanti 99 (36 confermati) mostrano una mediana di 1,5 pip, con solo il 22% sotto 1,0 pip. A differenza del deposito minimo, che non pesa nella nostra formula, lo spread EUR/USD incide direttamente sul nostro punteggio sui costi con un peso del 20%, il secondo fattore singolo più pesante dopo la regolamentazione. Quindi questo schema non è solo una correlazione che notiamo a posteriori: un broker con uno spread confermato più stretto ottiene meccanicamente un punteggio migliore su questo fattore per costruzione, non semplicemente tende a ottenerlo.',
                'section_5_heading' => '72 broker su 139 non hanno uno spread EUR/USD confermato -- inclusi alcuni nomi noti e ben regolamentati',
                'section_5_body' => 'Interactive Brokers (rank 2), OANDA (rank 6), Vantage Markets, Ultima Markets, CMC Markets e diversi altri non hanno un dato confermato nella nostra ricerca, più spesso a causa di prezzi variabili o di un modello in stile market maker senza un unico valore dichiarato, e non per una carenza nel livello di regolamentazione o di notorietà del broker. Il punteggio sui costi per questi broker viene escluso dalla loro media complessiva, anziché stimato o sostituito con la mediana del dataset.',
                'faq_1_q' => 'Qual è lo spread medio EUR/USD tra i broker forex?',
                'faq_1_a' => 'Tra i 67 broker su 139 nel nostro insieme di ricerca con un dato confermato, la mediana è di 1,1 pip. La media (1,25 pip) è trascinata verso l\'alto da alcuni valori anomali molto ampi, quindi la mediana è il dato più rappresentativo.',
                'faq_2_q' => 'Quale broker ha lo spread EUR/USD confermato più stretto?',
                'faq_2_a' => 'FXView, con un valore piatto di 0,0 pip -- anche se uno spread dichiarato di 0,0 pip spesso significa che il costo è stato spostato in una commissione separata, quindi non è automaticamente il conto più economico in assoluto.',
                'faq_3_q' => 'Uno spread EUR/USD più basso significa che un broker ottiene un punteggio migliore?',
                'faq_3_a' => 'Sì, direttamente -- a differenza del deposito minimo, lo spread EUR/USD ha un peso del 20% nel nostro punteggio sui costi, il secondo fattore singolo più pesante della nostra formula complessiva dopo la regolamentazione.',
                'faq_4_q' => 'Perché non tutti i 139 broker hanno uno spread EUR/USD confermato?',
                'faq_4_a' => '72 non ce l\'hanno, di solito a causa di prezzi variabili o di un modello in stile market maker senza un unico valore dichiarato -- inclusi alcuni nomi noti come Interactive Brokers e OANDA.',
            ),
            'pt' => array(
                'title' => 'Relatório de Spread EUR/USD',
                'meta_title' => 'Relatório de Spread EUR/USD (2026): Quanto Custa Realmente Negociar em 139 Corretoras de Forex',
                'meta_description' => 'A distribuição real dos spreads em todo o nosso conjunto de dados de corretoras -- mediana, valor mais baixo, valor mais alto e quais as corretoras conhecidas que não publicam um valor confirmado -- calculada a partir do mesmo campo que sustenta cada pontuação de custos deste site.',
                'intro' => 'O spread EUR/USD de uma corretora é o único número ao qual a maioria das comparações de custos acaba por reduzir tudo, mas os dados comparativos de spread para um conjunto real e amplo de corretoras são mais raros do que deveriam ser. Recolhemos o campo spread_eurusd já utilizado em todas as análises, na calculadora de custos e na fórmula de pontuação deste site, e contabilizámo-lo em todo o conjunto de 139 corretoras. Este relatório apresenta a distribuição real: onde se situa a corretora típica com valor confirmado, quão baixos chegam a ser os spreads mais reduzidos e que parte do conjunto de dados não tem qualquer valor confirmado.',
                'section_1_heading' => 'Uma mediana de 1,1 pips entre as corretoras com valor confirmado, com um terço abaixo de 1,0 pip',
                'section_1_body' => '67 das 139 corretoras do nosso conjunto de investigação (48%) têm um spread EUR/USD confirmado; a mediana entre elas é de 1,1 pips, e a média é ainda mais elevada (1,25 pips), puxada para cima por um punhado de valores extremos altos. Abaixo dessa mediana, 23 das 67 (34%) confirmam um spread inferior a 1,0 pip, e 6 (9%) confirmam um spread inferior a 0,5 pips.',
                'section_2_heading' => 'Uma corretora confirmou um spread EUR/USD fixo de 0,0 pips -- com uma ressalva que convém deixar clara',
                'section_2_body' => 'A FXView é a única corretora do nosso conjunto de dados confirmada com um spread EUR/USD anunciado de 0,0 pips. Um valor anunciado de 0,0 pips é comum em contas do tipo ECN/raw, que cobram uma comissão separada por lote em vez de incorporar o custo no spread -- isto não significa necessariamente que a negociação seja gratuita, apenas que o custo está estruturado de forma diferente. Confirme o custo total (spread mais qualquer comissão) diretamente antes de assumir que o valor anunciado mais baixo corresponde automaticamente à conta mais barata.',
                'section_3_heading' => 'Um punhado de corretoras confirma spreads de 2 pips ou mais',
                'section_3_body' => 'LiteForex (2,0), TrioMarkets (2,4), Fortrade (2,5), Trading 212 (2,7), InstaForex (3,0), Trade360 (4,0) e OEXN (5,0) situam-se bem acima do restante conjunto confirmado. Um spread anunciado mais elevado não é automaticamente um sinal de alerta -- pode refletir um modelo de conta ou uma base de clientes diferente --, mas representa uma diferença de custo real e confirmada que vale a pena conhecer antes de comparar estas corretoras com outra de spread mais reduzido em qualquer outro aspeto.',
                'section_4_heading' => 'As nossas corretoras melhor classificadas confirmam spreads mais reduzidos -- e aqui a correlação é, em parte, intencional',
                'section_4_body' => 'As corretoras classificadas entre as nossas 40 primeiras (31 com spread confirmado) apresentam uma mediana de 1,0 pip, com 48% a confirmar um valor inferior a 1,0 pip; as restantes 99 (36 confirmadas) apresentam uma mediana de 1,5 pips, com apenas 22% abaixo de 1,0 pip. Ao contrário do depósito mínimo, que não tem peso na nossa fórmula, o spread EUR/USD entra diretamente na nossa pontuação de custos com uma ponderação de 20% -- o segundo fator individual mais pesado, logo a seguir à regulação. Por isso, este padrão não é apenas uma correlação que registamos depois do facto: uma corretora com um spread confirmado mais reduzido obtém, por construção, uma pontuação melhor neste fator, e não apenas tende a obtê-la.',
                'section_5_heading' => '72 das 139 corretoras não têm spread EUR/USD confirmado -- incluindo alguns nomes conhecidos e bem regulamentados',
                'section_5_body' => 'A Interactive Brokers (classificação 2), a OANDA (classificação 6), a Vantage Markets, a Ultima Markets, a CMC Markets e várias outras não têm um valor confirmado na nossa investigação, sobretudo devido a preços variáveis ou a um modelo de preços de tipo market maker sem um valor anunciado único publicado, e não por qualquer lacuna na forma como estas corretoras são bem regulamentadas ou conhecidas. A nossa pontuação de custos para estas corretoras é excluída da média global, em vez de ser estimada ou substituída pela mediana do conjunto de dados.',
                'faq_1_q' => 'Qual é o spread EUR/USD médio entre as corretoras de forex?',
                'faq_1_a' => 'Entre as 67 de 139 corretoras do nosso conjunto de investigação com valor confirmado, a mediana é de 1,1 pips. A média (1,25 pips) é puxada para cima por um punhado de valores extremos altos, pelo que a mediana é o número mais representativo.',
                'faq_2_q' => 'Qual é a corretora com o spread EUR/USD confirmado mais reduzido?',
                'faq_2_a' => 'A FXView, com um valor fixo de 0,0 pips -- embora um spread anunciado de 0,0 pips signifique frequentemente que o custo passa para uma comissão separada, pelo que não é automaticamente a conta mais barata do conjunto.',
                'faq_3_q' => 'Um spread EUR/USD mais baixo significa que a corretora obtém melhor pontuação?',
                'faq_3_a' => 'Sim, diretamente -- ao contrário do depósito mínimo, o spread EUR/USD tem uma ponderação de 20% na nossa pontuação de custos, o segundo fator individual mais pesado na nossa fórmula global, logo a seguir à regulação.',
                'faq_4_q' => 'Porque é que nem todas as 139 corretoras têm um spread EUR/USD confirmado?',
                'faq_4_a' => '72 não o têm, normalmente devido a preços variáveis ou a um modelo de tipo market maker sem um valor anunciado único publicado -- incluindo alguns nomes conhecidos, como a Interactive Brokers e a OANDA.',
            ),
        ),
    );
}

function globalfxhub_get_research_report( $slug ) {
    $reports = globalfxhub_get_research_reports();
    return $reports[ $slug ] ?? null;
}

function globalfxhub_get_localized_research_report( $slug ) {
    $entry = globalfxhub_get_research_report( $slug );
    if ( ! $entry ) {
        return null;
    }
    $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    $translations = globalfxhub_research_report_translations();
    $flat = $translations[ $slug ][ $lang ] ?? array();
    if ( empty( $flat ) ) {
        return $entry;
    }
    $get = function( $id, $fallback ) use ( $flat ) {
        return $flat[ $id ] ?? $fallback;
    };

    $entry['title']            = $get( 'title', $entry['title'] );
    $entry['meta_title']       = $get( 'meta_title', $entry['meta_title'] );
    $entry['meta_description'] = $get( 'meta_description', $entry['meta_description'] );
    $entry['intro']            = $get( 'intro', $entry['intro'] );

    $entry['sections'] = array_values( array_map(
        function( $i, $section ) use ( $get ) {
            $n = $i + 1;
            return array(
                'heading' => $get( "section_{$n}_heading", $section['heading'] ),
                'body'    => $get( "section_{$n}_body", $section['body'] ),
            );
        },
        array_keys( $entry['sections'] ), $entry['sections']
    ) );

    $entry['faqs'] = array_values( array_map(
        function( $i, $faq ) use ( $get ) {
            $n = $i + 1;
            return array(
                'q' => $get( "faq_{$n}_q", $faq['q'] ),
                'a' => $get( "faq_{$n}_a", $faq['a'] ),
            );
        },
        array_keys( $entry['faqs'] ), $entry['faqs']
    ) );

    return $entry;
}

function globalfxhub_research_reports_seo_title( $title_parts ) {
    $slug = get_query_var( 'report' );
    if ( $slug ) {
        $entry = globalfxhub_get_localized_research_report( $slug );
        if ( $entry ) {
            $title_parts['title'] = $entry['meta_title'];
        }
    } elseif ( is_page_template( 'page-research.php' ) ) {
        // The per-report $entry['meta_title'] above is already translated
        // via globalfxhub_get_localized_research_report() -> only the hub
        // title (no report slug) was still hardcoded English.
        $title_parts['title'] = sprintf( globalfxhub_t( 'seo_research_hub_title' ), date( 'Y' ) );
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_research_reports_seo_title' );

function globalfxhub_research_reports_canonical( $canonical_url ) {
    $slug = get_query_var( 'report' );
    if ( $slug && globalfxhub_get_research_report( $slug ) ) {
        return globalfxhub_track_b_url( 'research/' . $slug . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_research_reports_canonical' );

function globalfxhub_research_reports_seo_head() {
    $slug = get_query_var( 'report' );
    if ( ! $slug ) {
        if ( is_page_template( 'page-research.php' ) ) {
            echo '<meta name="description" content="' . esc_attr( 'Original data studies and reports built directly from our own 139-broker research set -- platform adoption, regulation, fees, and more, computed from the same dataset behind every review and ranking on this site.' ) . '">' . "\n";
        }
        return;
    }
    $entry = globalfxhub_get_localized_research_report( $slug );
    if ( ! $entry ) {
        return;
    }
    echo '<meta name="description" content="' . esc_attr( $entry['meta_description'] ) . '">' . "\n";

    if ( ! empty( $entry['faqs'] ) ) {
        $faq_schema = array(
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => array_map( function( $faq ) {
                return array(
                    '@type'          => 'Question',
                    'name'           => $faq['q'],
                    'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $faq['a'] ),
                );
            }, $entry['faqs'] ),
        );
        echo '<script type="application/ld+json">' . wp_json_encode( $faq_schema ) . '</script>' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_research_reports_seo_head', 5 );
