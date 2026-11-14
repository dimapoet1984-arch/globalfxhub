<?php
/**
 * Broker News: a short, factual bulletin feed of broker-specific
 * developments -- fines, enforcement actions, licence withdrawals, new
 * licences, acquisitions, executive moves, new platforms, product
 * launches, and operational issues (e.g. a broker's own domain or
 * client portal going down).
 *
 * Deliberately NOT auto-ingested from RSS/syndicated feeds. Every item
 * here is mined from this site's own deep-dive broker research (the
 * regulatory notes and blurbs already disclosed on each broker's own
 * review page), which already cites the underlying regulator action or
 * primary source. This is a curated, hand-maintained list -- add to it
 * as new broker-specific developments turn up in ongoing research,
 * the same way broker review data itself is maintained. It is not a
 * cron job, does not call any external API, and does not create
 * WordPress posts: it's a static array rendered directly by
 * page-news.php, exactly like globalfxhub_get_brokers() itself.
 *
 * Google's guidance on scaled content abuse specifically calls out mass-
 * generated or scraped low-originality content made primarily to
 * manipulate rankings. This feed is the deliberate alternative: low
 * volume, high factual density, every item traceable to a regulator
 * action or primary disclosure already documented on this site, and
 * nothing published just to hit a daily quota.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_broker_news_categories() {
    return array(
        'acquisition'         => 'Acquisition',
        'license'             => 'New licence',
        'license_withdrawal'  => 'Licence withdrawal',
        'fine'                => 'Fine',
        'enforcement'         => 'Enforcement',
        'platform'            => 'New platform',
        'executive'           => 'Executive move',
        'product'             => 'Product launch',
        'outage'              => 'Outage',
    );
}

/**
 * Each item's 'body' intentionally summarizes -- in 2-4 sentences --
 * facts already stated at greater length (with full sourcing caveats)
 * on the linked broker's own review page, rather than introducing any
 * new unsourced claim here. Dates with only month-level confidence say
 * so in the body text ("around September 2026") rather than claiming a
 * specific day we don't actually have.
 */
function globalfxhub_get_broker_news_items() {
    $items = array(
        array(
            'date'        => '2026-09-24',
            'broker_slug' => 'ironfx',
            'category'    => 'outage',
            'item_slug'   => 'ironfx-domain-outage-september-2026',
            'headline'    => "IronFX's main website and client portal go offline; broker points traders to a temporary .co domain",
            'body'        => 'IronFX\'s primary ironfx.com domain and client portal became unreachable starting around 24 September 2026. The broker said on its Japanese X account that this was a domain-related technical issue, that restoration work was already under way, and pointed visitors to a temporary ironfx.co address in the meantime; Google and other search engines continued indexing the .com domain despite it being unreachable. Sister brand FXLift, run by the same parent group, Notesco, continued operating normally throughout. No independent report confirming the .com domain and client portal are fully restored was found as of this writing -- for a broker already carrying a weak Trustpilot score and a history of client withdrawal disputes (see this review\'s customer service note), an extended client-portal outage specifically means affected traders cannot log in to check balances, trade, or request withdrawals through the main channel until it\'s resolved.',
            // Items with an 'item_slug' get their own dedicated page at
            // /news/brokers/{item_slug}/ (see the rewrite rule and
            // globalfxhub_get_broker_news_item_by_slug() below) instead
            // of linking straight to the broker's review page -- reserved
            // for a development substantial enough to warrant real
            // standalone analysis, not the default for every item.
            'article' => array(
                'paragraphs' => array(
                    'IronFX\'s primary domain, ironfx.com, and its client portal went unreachable starting around 24 September 2026. The broker confirmed the outage itself -- on its Japanese X/Twitter account, not a global press release -- saying it was a domain-related technical issue and that restoration work was under way, and it pointed traders to a temporary stand-in domain, ironfx.co, in the meantime. Google and other search engines kept indexing the dead .com domain even though it wasn\'t resolving.',
                    'As of this writing, no independent report confirming the .com domain or client portal has been restored was found. That\'s over two weeks with no public "we\'re back" confirmation located in the sources checked for this article. If you\'re an affected client, check the .co address or IronFX\'s own X account directly for current status rather than relying on this page alone.',
                    'A broker\'s main website isn\'t just marketing -- for existing clients, the client portal is the account. An extended outage there means people can\'t log in to check balances, place trades, or request withdrawals through the primary channel. That lands harder on IronFX specifically than it would on a broker with a clean track record: Trustpilot already shows a weak score (around 2/5 on 717 reviews for the main entity, 2.5/5 on 78 for the EU entity) dominated by withdrawal and refund complaints, and Finance Magnates has separately reported on a pattern of clients citing withdrawal hurdles tied to a CySEC investigation -- mostly 2015-2022 complaints, so a pattern rather than necessarily a current one, but it\'s the backdrop this outage lands on. A Cyprus Audit Office report once put money owed to IronFX clients at EUR176 million; IronFX called that figure "grossly inflated" and disputed it, and this research could not confirm which figure was accurate.',
                    'Separately from the outage, IronFX\'s UK entity (Notesco UK Limited) has had a genuinely mixed recent financial picture: revenue more than doubled to $1.44 million in 2024 (from $702K in 2023), though net profit dipped slightly to $124K from $125K. 2022 was weaker, with UK revenue falling 25% to $643,795. Finance Magnates has also reported a departure by the group\'s CFO, George Kyriakoudes, though a specific date for that wasn\'t confirmed in sources checked.',
                    'IronFX\'s regulatory footprint is unchanged from what this site already lists: CySEC licence 125/10, FCA licence 585561, and an FSCA (South Africa) licence -- no report tying this outage to a regulatory action was found. One notable detail: sister brand FXLift, run by the same parent group (Notesco, BVI), was separately reported as continuing to operate normally throughout, which is a point in favour of this being brand/domain-specific infrastructure trouble rather than a company-wide shutdown -- though that inference isn\'t a confirmation either way.',
                    'Bottom line: a real, multi-week domain and portal outage with no independently confirmed fix as of this writing. IronFX\'s own explanation (a domain/DNS issue) is plausible, and the unaffected sister brand supports that reading -- but there\'s no independent confirmation either way, and it\'s landing on a broker with pre-existing, documented trust problems around withdrawals. An affected trader should verify current status directly rather than assume either the best or worst case.',
                ),
                'sources' => array(
                    array( 'label' => 'Finance Magnates (via Coinspectator): "IronFX.com Domain Goes Dark, Temporary .Co Fills the Gap"', 'url' => 'https://coinspectator.com/mainstream/2026/09/24/ironfx-com-domain-goes-dark-temporary-co-fills-the-gap/' ),
                    array( 'label' => 'FXEmpire: "IronFX Posts Huge Drop in Revenues"', 'url' => 'https://www.fxempire.com/news/article/ironfx-posts-huge-drop-revenues-407645' ),
                    array( 'label' => 'Finance Magnates: "How Did IronFX Clients\' Withdrawal Issues Trigger a CySEC Investigation?"', 'url' => 'https://www.financemagnates.com/forex/analysis/how-did-ironfx-clients-withdrawal-issues-trigger-a-cysec-investigation/' ),
                    array( 'label' => 'Finance Magnates: "IronFX UK Doubles Revenue to $1.4 Million in 2024, Net Profit Slips Slightly"', 'url' => 'https://www.financemagnates.com/forex/ironfx-uk-doubles-revenue-to-14-million-in-2024-net-profit-slips-slightly/' ),
                ),
            ),
        ),
        array(
            'date'        => '2026-09-17',
            'broker_slug' => 'eurotrade',
            'category'    => 'license_withdrawal',
            'headline'    => 'CySEC withdraws Eurotrade\'s (Eurotrader) EU licence over governance failings',
            'body'        => 'CySEC withdrew Eurotrade Investments RGB Ltd\'s Cyprus Investment Firm authorisation (licence 279/15) on 17 September 2026, citing failures to maintain the required minimum of two persons effectively directing the firm and adequate organisational arrangements. The broker, which trades as Eurotrader, is no longer EU/MiFID-regulated as a result; an FSCA (South Africa) licence remains.',
        ),
        array(
            'date'        => '2026-06-09',
            'broker_slug' => 'traders-trust',
            'category'    => 'license_withdrawal',
            'headline'    => 'Traders Trust exits CySEC regulation at its own request',
            'body'        => 'CySEC withdrew TTCM Traders Trust Capital Markets Ltd\'s Cyprus Investment Firm authorisation (licence 107/09) on 9 June 2026, at the firm\'s own request -- a voluntary exit rather than a contested enforcement action. The licence had previously been suspended once before, in 2017, and later reinstated. Traders Trust\'s Seychelles FSA and Bermuda licences remain in place.',
        ),
        array(
            'date'        => '2025-10-24',
            'broker_slug' => 'fxoro',
            'category'    => 'license_withdrawal',
            'headline'    => 'FXORO to cease EU services as CySEC licence renunciation completes',
            'body'        => 'MCA Intelifunds Ltd (FXORO) has stated it will cease providing investment and ancillary services effective 24 October 2025, as part of a voluntary renunciation of CySEC licence 126/10. The wind-down follows a EUR360,000 CySEC fine imposed in April 2024 over a September 2022 inspection that found failures to act in clients\' best interest and inadequate compliance policies.',
        ),
        array(
            'date'        => '2025-07-15',
            'broker_slug' => 'orbex',
            'category'    => 'license_withdrawal',
            'headline'    => 'Orbex surrenders CySEC licence, exits EU/EEA after 14 years in Cyprus',
            'body'        => 'Orbex surrendered its CySEC licence and ceased serving EU/EEA clients as of 15 July 2025, ending roughly 14 years of operating from Cyprus. The broker now trades exclusively through offshore entities in Mauritius, Seychelles, and St Vincent & the Grenadines -- none of which carry Tier-1 status.',
        ),
        array(
            'date'        => '2024-05-20',
            'broker_slug' => 'fxtm',
            'category'    => 'license_withdrawal',
            'headline'    => 'FXTM\'s EU entity formally loses CySEC authorisation',
            'body'        => 'CySEC formally withdrew the CySEC Investment Firm authorisation of Forextime Ltd -- FXTM\'s former EU entity -- effective 20 May 2024, following that entity\'s voluntary renunciation and its earlier cessation of EU operations on 31 December 2023. FXTM continues to operate globally through Exinity Limited, licensed by Mauritius\'s FSC, and still holds direct FCA (UK) and FSCA (South Africa) authorisation.',
        ),
        array(
            'date'        => '2025-01-01',
            'broker_slug' => 'squaredfinancial',
            'category'    => 'license_withdrawal',
            'headline'    => 'SquaredFinancial begins voluntary wind-down of its CySEC licence',
            'body'        => 'Squared Financial (CY) Ltd began a voluntary, structured wind-down and surrender of its CySEC licence (329/17) during 2025, including halting new client onboarding. The wind-down follows an earlier CySEC fine of EUR35,000 over CFD marketing practices and reports of frozen partner funds.',
        ),
        array(
            'date'        => '2025-08-07',
            'broker_slug' => '1market',
            'category'    => 'fine',
            'headline'    => 'CySEC fines 1Market\'s parent EUR740,000 over nine separate violations',
            'body'        => 'CySEC fined Exelcius Prime Ltd, the parent of 1Market, EUR740,000 on 7 August 2024 for nine separate violations -- including unauthorized investment advice, board-governance failures, and inadequate conflict-of-interest management -- and suspended the firm\'s CIF authorisation while fining and banning several named directors from management duties. The 366/18 licence is now under examination for voluntary renunciation.',
        ),
        array(
            'date'        => '2024-04-01',
            'broker_slug' => 'fxoro',
            'category'    => 'fine',
            'headline'    => 'CySEC fines FXORO\'s operator EUR360,000 over client-treatment failures',
            'body'        => 'CySEC fined MCA Intelifunds Ltd (FXORO) EUR360,000 in April 2024 following a September 2022 inspection that found failures to act fairly and professionally in clients\' best interest, inadequate compliance policies, and a failure to warn clients adequately about inappropriate products.',
        ),
        array(
            'date'        => '2025-07-03',
            'broker_slug' => 'broctagon-prime',
            'category'    => 'fine',
            'headline'    => 'CySEC settles with institutional liquidity provider Broctagon Prime for EUR50,000',
            'body'        => 'CySEC announced a EUR50,000 settlement with Broctagon Prime Ltd around 3 July 2025, following a roughly four-year probe into 2021 conduct concerning information provided to clients. Broctagon Prime is a business-to-business liquidity provider supplying at least six other CySEC-regulated brokers rather than serving retail clients directly; as is standard for CySEC settlements, this was not an admission of wrongdoing.',
        ),
        array(
            'date'        => '2023-01-01',
            'broker_slug' => 'profitlevel',
            'category'    => 'fine',
            'headline'    => 'ProfitLevel\'s operator settles with CySEC three times, then exits',
            'body'        => 'CySEC settled with BCM Begin Capital Markets CY Ltd -- operator of the ProfitLevel and CapitalPanda brands -- three times during 2022-2023, with settlements reported around EUR170,000, then EUR100,000, then a further EUR50,000 tied specifically to providing investment services to Slovenian residents without authorisation. The firm voluntarily renounced its CySEC licence in 2023 and ProfitLevel\'s services closed shortly after.',
        ),
        array(
            'date'        => '2021-04-01',
            'broker_slug' => 'forextb',
            'category'    => 'fine',
            'headline'    => 'FCA fines ForexTB operator and bars it from UK business',
            'body'        => 'The FCA fined Forex TB Limited GBP276,100 for unfair customer treatment -- including pressuring clients into CFD trading and improperly reclassifying retail clients as "Professional Clients" to strip them of retail protections -- and prohibited the firm from UK business from April 2021, with all FCA permissions lost by October 2023. Separately, ForexTB paid roughly EUR270,000 to settle a CySEC investigation, and that CySEC licence is now under examination for voluntary renunciation.',
        ),
        array(
            'date'        => '2025-06-10',
            'broker_slug' => 'go4rex',
            'category'    => 'enforcement',
            'headline'    => 'Chile\'s securities regulator blacklists Go4rex for unlicensed activity',
            'body'        => 'Chile\'s Comisión para el Mercado Financiero added go4rex.com to its official blacklist on 10 June 2025 for offering financial services without being registered or licensed in the country -- a confirmed regulator action, alongside separate, independent reports of a recurring "pay to withdraw" complaint pattern involving this broker.',
        ),
        array(
            'date'        => '2024-08-08',
            'broker_slug' => 'ventezo',
            'category'    => 'enforcement',
            'headline'    => 'Malaysia\'s SC adds Ventezo to its Investor Alert List',
            'body'        => 'Malaysia\'s Securities Commission added Ventezo to its official Investor Alert List on 8 August 2024 for unregistered and unlicensed activity. Independent trackers separately found Ventezo\'s claimed NFA (US) registration to be false and its cited SVG FSA registration to be a category that doesn\'t actually cover online trading -- consistent with reports that the broker is now out of business.',
        ),
        array(
            'date'        => '2025-05-01',
            'broker_slug' => 'vt-markets',
            'category'    => 'enforcement',
            'headline'    => 'Four Tier-1 regulators warn against VT Markets in two years',
            'body'        => 'The UK\'s FCA (2023), Denmark\'s DFSA (2024, explicitly stating Danish clients have no investor protection), France\'s AMF (2023-2025, listing several VT-linked domains as unlicensed), and Italy\'s Consob (May 2025) have each separately published public warnings against VT Markets entities or domains. VT Markets\' only confirmed regulatory credential on this site is a Seychelles FSA Securities Dealer licence.',
        ),
        array(
            'date'        => '2025-01-01',
            'broker_slug' => 'fxcentrum',
            'category'    => 'enforcement',
            'headline'    => 'UK and Spanish regulators warn against FXCentrum',
            'body'        => 'The UK\'s FCA and Spain\'s CNMV have each publicly stated that FXCentrum provides financial services without proper authorisation in their jurisdictions. FXCentrum\'s only disclosed regulatory credential is an offshore Seychelles FSA licence held via WTG Ltd.',
        ),
        array(
            'date'        => '2025-07-01',
            'broker_slug' => 'pu-prime',
            'category'    => 'enforcement',
            'headline'    => 'Philippines SEC issues cease-and-desist against a PU Prime-branded entity',
            'body'        => 'The Philippines SEC issued a cease-and-desist order in July 2025 against a PU Prime-branded entity for offering unregistered securities, and the UK FCA has repeatedly warned against a separate offshore PU Prime entity. Ontario\'s securities regulator separately stated, via an IOSCO alert around March 2026, that puprime.com and puprime.net aren\'t registered to trade securities there.',
        ),
        array(
            'date'        => '2026-09-01',
            'broker_slug' => 'pu-prime',
            'category'    => 'license',
            'headline'    => 'PU Prime launches a new ASIC-licensed Australian entity',
            'body'        => 'A new PU Prime entity, PU Prime Trading Pty Ltd, launched under Australian ASIC licence (AFSL 410681) around September 2026, extending the group\'s regulatory footprint beyond its existing Seychelles FSA Securities Dealer licence (SD050). One review suggests the new entity wasn\'t yet accepting registrations as recently as June 2026, so current live status is worth confirming directly.',
        ),
        array(
            'date'        => '2023-01-01',
            'broker_slug' => 'noor-capital-uk',
            'category'    => 'acquisition',
            'headline'    => 'Abu Dhabi\'s Noor Capital acquires House of Borse, renames it',
            'body'        => 'Noor Capital, part of Abu Dhabi\'s Al Sayegh Brothers Group, acquired UK broker House of Borse in 2023 and renamed it Noor Capital UK Limited -- reported as the largest UK financial-sector acquisition by a UAE firm that year. The firm\'s FCA authorisation (FRN 631382) carried over under the new ownership.',
        ),
        array(
            'date'        => '2024-01-01',
            'broker_slug' => 'saxo',
            'category'    => 'acquisition',
            'headline'    => 'J. Safra Sarasin Group takes majority stake in Saxo Bank',
            'body'        => 'J. Safra Sarasin Group acquired a majority stake in Saxo Bank A/S during 2024, buying out Geely and Mandatum. Saxo has separately been retrenching from the Asia-Pacific region since mid-2024, stopping new client onboarding in Australia, Hong Kong, and Japan, and agreeing to sell the majority of its Australian business.',
        ),
        array(
            'date'        => '2026-02-01',
            'broker_slug' => 'vantos-markets',
            'category'    => 'executive',
            'headline'    => 'Capital Index renamed Vantos Markets after ownership change',
            'body'        => 'UK broker Capital Index, founded by Robert Woolfe in 2014, was renamed Vantos Markets in February 2026 following a change in ownership to BVI-incorporated Vinalytics Limited. Existing directors, including Woolfe as managing director, were retained, along with the firm\'s FCA authorisation (FRN 709693); FY2025 financials show turnover down roughly 31% year-on-year.',
        ),
        array(
            'date'        => '2024-02-01',
            'broker_slug' => 'gildencrest-capital',
            'category'    => 'executive',
            'headline'    => 'TeraFX rebrands to Gildencrest Capital under Turkish ownership',
            'body'        => 'FCA-authorised broker TeraFX (Tera Europe Ltd, authorised since 2011) rebranded to Gildencrest Capital on 1 February 2024, with parent Turkish investment group Tera Yatirim. The firm\'s financials have been volatile since: revenue grew 52% to GBP7.55m in 2023 before falling back to GBP3.65m the following year.',
        ),
        array(
            'date'        => '2026-09-12',
            'broker_slug' => 'stonex-trading',
            'category'    => 'platform',
            'headline'    => 'City Index brand retired in the UK, replaced by StoneX Trading',
            'body'        => 'StoneX Group formally retired the City Index brand in the UK on 12 September 2026, replacing it with StoneX Trading -- the same FCA entity (StoneX Financial Ltd), with existing client accounts and platforms carried over unchanged. City Index itself traced back to 1983, making it one of the UK\'s oldest spread-betting and CFD brands.',
        ),
        array(
            'date'        => '2023-01-01',
            'broker_slug' => 'daman-markets',
            'category'    => 'product',
            'headline'    => 'Daman Markets launches AED-denominated accounts for UAE traders',
            'body'        => 'Daman Markets, a late-2023 spin-off of the long-established Daman Securities group, launched AED-denominated trading accounts explicitly marketed as avoiding currency-conversion charges for UAE-based traders, alongside standard forex/CFD trading via MT5 and direct access to DFM- and ADX-listed UAE equities.',
        ),
    );

    usort( $items, function( $a, $b ) {
        return strcmp( $b['date'], $a['date'] );
    } );

    return $items;
}

/**
 * Every Broker News item is its own entity with its own page at
 * /news/brokers/{slug}/ -- not merely a card that forwards to the
 * linked broker's review page. An item can set its own 'item_slug'
 * (used for IronFX's domain-outage item, where a more readable,
 * descriptive slug reads better than a bare date), but defaults to
 * broker_slug + date, which this dataset's 24 items confirm is always
 * unique (no broker has two items dated the same day).
 */
function globalfxhub_broker_news_item_slug( $item ) {
    return ! empty( $item['item_slug'] ) ? $item['item_slug'] : sanitize_title( $item['broker_slug'] . '-' . $item['date'] );
}

function globalfxhub_get_broker_news_item_by_slug( $slug ) {
    foreach ( globalfxhub_get_broker_news_items() as $item ) {
        if ( globalfxhub_broker_news_item_slug( $item ) === $slug ) {
            return $item;
        }
    }
    return null;
}

/**
 * Pretty URL for a single Broker News item: /news/brokers/{slug}/ --
 * same rewrite-rule + query-var pattern as every other data-driven
 * route on this site (reviews/{slug}/, countries/{slug}/, etc.), never
 * a pre-generated wp_insert_post() page. 'brokers' is a hierarchical
 * child page under 'news' (see globalfxhub_ensure_child_page() in
 * functions.php), so its own pagename query var is 'news/brokers' --
 * WordPress's own convention for a 2-level page path.
 */
function globalfxhub_broker_news_rewrite_rules() {
    add_rewrite_rule( '^news/brokers/([^/]+)/?$', 'index.php?pagename=news/brokers&broker_news_item=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_broker_news_rewrite_rules' );

function globalfxhub_broker_news_query_vars( $vars ) {
    $vars[] = 'broker_news_item';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_broker_news_query_vars' );

function globalfxhub_broker_news_seo_title( $title_parts ) {
    $slug = get_query_var( 'broker_news_item' );
    if ( $slug ) {
        $item = globalfxhub_get_broker_news_item_by_slug( $slug );
        if ( $item ) {
            $title_parts['title'] = $item['headline'] . ' | Broker News';
        }
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_broker_news_seo_title' );

function globalfxhub_broker_news_canonical( $canonical_url ) {
    $slug = get_query_var( 'broker_news_item' );
    if ( $slug && globalfxhub_get_broker_news_item_by_slug( $slug ) ) {
        return home_url( '/news/brokers/' . $slug . '/' );
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_broker_news_canonical' );

function globalfxhub_broker_news_seo_head() {
    $slug = get_query_var( 'broker_news_item' );
    if ( ! $slug ) {
        return;
    }
    $item = globalfxhub_get_broker_news_item_by_slug( $slug );
    if ( ! $item ) {
        return;
    }
    echo '<meta name="description" content="' . esc_attr( globalfxhub_trim_excerpt( $item['body'], 34 ) ) . '">' . "\n";
}
add_action( 'wp_head', 'globalfxhub_broker_news_seo_head', 5 );
