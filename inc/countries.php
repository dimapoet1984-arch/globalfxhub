<?php
/**
 * "Best broker in [country]" pages for all 27 EU member states.
 *
 * Every broker in globalfxhub_get_brokers() is CySEC-licensed, which
 * under MiFID passporting means every one of them is legally entitled to
 * serve retail clients anywhere in the EU/EEA -- so there is no real
 * per-country "availability" difference to report, and no live
 * traffic/market-share dataset exists anywhere in this codebase or
 * environment to measure country-level popularity with.
 *
 * Rather than invent country-specific rankings we can't back up, the
 * ranking here uses one real, already-present signal -- a broker's own
 * disclosed headquarters (the 'hq' field already on every broker record)
 * -- to surface brokers actually based in that country ahead of the rest,
 * then fills remaining slots with the same disclosed overall-score
 * ranking used everywhere else on the site. Every country page says so
 * plainly. This intentionally means most countries (the ones with no
 * broker headquartered there) show the same top-10 list; that's the
 * honest result of the data actually available, not a bug.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * All 27 EU member states. 'hq_match' is the plain-English country name
 * as it actually appears in broker 'hq' strings (see globalfxhub_get_brokers()),
 * used to detect a local headquarters -- confirmed present in the data for
 * Cyprus, Poland, Ireland, Germany, and Estonia; every other country
 * currently has no HQ match and falls back to the global ranking.
 *
 * 'paragraphs' are hand-written, fact-checked content: currency,
 * regulator, EU/eurozone status, and -- where a genuinely verifiable,
 * non-fabricated detail exists (a broker's real HQ, a documented
 * regulatory precedent) -- that detail. Nothing here is invented traffic
 * or market-share data.
 */
function globalfxhub_get_countries() {
    return array(
        'austria' => array(
            'name' => 'Austria', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1995,
            'regulator' => 'FMA (Finanzmarktaufsicht)', 'hq_match' => 'Austria',
            'paragraphs' => array(
                'Austria joined the EU in 1995 and has used the euro since the currency\'s launch. Retail CFD and forex trading is supervised domestically by the FMA (Finanzmarktaufsicht), which enforces the same EU-wide leverage caps and risk-warning rules that apply to every CySEC-licensed broker passporting into the country.',
                'Austrian retail investors are historically more exposed to traditional bank-distributed savings and investment products than to leveraged CFD trading, so the market is smaller and more niche than in several larger EU economies -- worth knowing before comparing marketing claims from any provider.',
            ),
        ),
        'belgium' => array(
            'name' => 'Belgium', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'FSMA (Financial Services and Markets Authority)', 'hq_match' => 'Belgium',
            'paragraphs' => array(
                'A founding EU and eurozone member, Belgium is supervised domestically by the FSMA. Belgium was notably ahead of the curve on retail derivatives protection: the FSMA restricted the distribution of binary options and certain highly leveraged OTC derivatives to retail clients back in 2016, two years before ESMA\'s EU-wide leverage caps and marketing rules took effect in 2018.',
                'That early, stricter stance is part of why Belgian regulators are generally viewed as conservative on retail leveraged trading -- any broker marketing into Belgium is bound by both the domestic FSMA rules and the EU-wide ESMA framework.',
            ),
        ),
        'bulgaria' => array(
            'name' => 'Bulgaria', 'currency' => 'BGN', 'eurozone' => false, 'eu_since' => 2007,
            'regulator' => 'FSC (Financial Supervision Commission)', 'hq_match' => 'Bulgaria',
            'paragraphs' => array(
                'Bulgaria joined the EU in 2007 and still uses its own currency, the lev (BGN), which is pegged to the euro; the country has targeted future eurozone entry. Domestic oversight of financial markets sits with the FSC, though in practice most retail forex and CFD trading in Bulgaria happens through brokers passporting in from elsewhere in the EU, CySEC-licensed firms among the most common.',
                'No broker in our current CySEC-licensed dataset is headquartered in Bulgaria, so the ranking below reflects our overall EU-wide scoring rather than any Bulgaria-specific presence.',
            ),
        ),
        'croatia' => array(
            'name' => 'Croatia', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2013,
            'regulator' => 'HANFA (Croatian Financial Services Supervisory Agency)', 'hq_match' => 'Croatia',
            'paragraphs' => array(
                'Croatia is the EU\'s newest member state, joining in 2013, and the most recent to adopt the euro, switching from the kuna in January 2023. HANFA supervises domestic financial markets, working alongside the EU-wide ESMA framework that governs every CySEC-passported broker operating there.',
                'As with several smaller EU markets, no broker in our dataset is headquartered in Croatia, so rankings below reflect overall broker quality rather than a confirmed local market footprint.',
            ),
        ),
        'cyprus' => array(
            'name' => 'Cyprus', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'CySEC (Cyprus Securities and Exchange Commission)', 'hq_match' => 'Cyprus',
            'paragraphs' => array(
                'Cyprus is the regulatory home base for this entire site: CySEC is the licensing authority behind every broker in our rankings, and the large majority of them run their EU-regulated entity directly out of Limassol or Nicosia. For Cypriot residents, that means many of the brokers reviewed here aren\'t just passporting in from elsewhere -- they\'re headquartered locally.',
                'Cyprus joined the EU in 2004 and the eurozone in 2008. Because so much of the CFD/forex brokerage industry is physically based there, CySEC has built specific supervisory expertise in this sector that most national regulators, overseeing far fewer such firms, haven\'t needed to develop to the same degree.',
            ),
        ),
        'czechia' => array(
            'name' => 'Czechia', 'currency' => 'CZK', 'eurozone' => false, 'eu_since' => 2004,
            'regulator' => 'ČNB (Czech National Bank)', 'hq_match' => 'Czech',
            'paragraphs' => array(
                'Czechia joined the EU in 2004 and has not adopted the euro, retaining the koruna (CZK). The Czech National Bank (ČNB) acts as both central bank and financial supervisor, and enforces the same EU-wide leverage limits and risk disclosures on any broker serving Czech retail clients.',
                'No broker in our current dataset is headquartered in Czechia, so the ranking below uses our overall EU-wide scoring.',
            ),
        ),
        'denmark' => array(
            'name' => 'Denmark', 'currency' => 'DKK', 'eurozone' => false, 'eu_since' => 1973,
            'regulator' => 'Finanstilsynet (Danish FSA)', 'hq_match' => 'Denmark',
            'paragraphs' => array(
                'Denmark joined the EU in 1973 and negotiated a formal opt-out from the euro, keeping the krone (DKK), which is tightly pegged to the euro via the ERM II exchange rate mechanism. Finanstilsynet is the domestic financial supervisor, working within the same EU-wide ESMA rules on leverage and marketing that apply across the bloc.',
                'No broker in our current dataset is headquartered in Denmark, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local presence.',
            ),
        ),
        'estonia' => array(
            'name' => 'Estonia', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'Estonian Financial Supervision Authority (Finantsinspektsioon)', 'hq_match' => 'Estonia',
            'paragraphs' => array(
                'Estonia joined the EU in 2004 and adopted the euro in 2011. It\'s also one of the few countries on this list that\'s genuinely home to one of our reviewed brokers: Admirals (formerly Admiral Markets) was founded in Tallinn and still lists Estonia as its group headquarters, even though its EU retail clients are served through its CySEC-regulated Cyprus subsidiary.',
                'Estonia\'s broader reputation for e-government and digital-first business services (e-Residency, fully digital company formation) is part of why a number of fintech and brokerage firms have chosen to establish a presence there, even when their regulated EU retail entity sits elsewhere.',
            ),
        ),
        'finland' => array(
            'name' => 'Finland', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1995,
            'regulator' => 'FIN-FSA (Finanssivalvonta)', 'hq_match' => 'Finland',
            'paragraphs' => array(
                'Finland joined the EU in 1995 and was one of the original eurozone members. FIN-FSA supervises domestic financial markets and enforces the same EU-wide ESMA leverage caps and marketing restrictions that apply to every broker passporting into the country.',
                'No broker in our current dataset is headquartered in Finland, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local footprint.',
            ),
        ),
        'france' => array(
            'name' => 'France', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'AMF (Autorité des marchés financiers)', 'hq_match' => 'France',
            'paragraphs' => array(
                'A founding EU and eurozone member, France is supervised by the AMF, one of the more assertive regulators in Europe on retail derivatives. France restricted the advertising of high-risk CFDs and binary options to retail investors via electronic communications back in 2016-2017, ahead of the EU-wide ESMA restrictions that followed in 2018.',
                'That precedent is part of why French retail investors have long been shown prominent risk warnings on CFD marketing -- a pattern the rest of the EU later adopted. Any broker in our rankings serving French clients must still comply with both AMF rules and the bloc-wide ESMA framework.',
            ),
        ),
        'germany' => array(
            'name' => 'Germany', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'BaFin (Bundesanstalt für Finanzdienstleistungsaufsicht)', 'hq_match' => 'Germany',
            'paragraphs' => array(
                'Germany, a founding EU and eurozone member, has one of the largest retail trading populations in the EU and is supervised by BaFin. It\'s also genuinely home to one of our reviewed brokers: NAGA is headquartered in Hamburg, and its parent, The NAGA Group AG, is listed on the Frankfurt Stock Exchange, even though its EU retail CFD business runs through a CySEC-regulated Cyprus entity.',
                'Germany\'s size and its well-developed retail brokerage culture mean most large CySEC-licensed brokers maintain German-language sites and support specifically for this market, on top of the EU-wide leverage and marketing rules that apply everywhere.',
            ),
        ),
        'greece' => array(
            'name' => 'Greece', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1981,
            'regulator' => 'HCMC (Hellenic Capital Market Commission)', 'hq_match' => 'Greece',
            'paragraphs' => array(
                'Greece joined the EU in 1981 and the eurozone in 2001. The HCMC supervises domestic financial markets, and -- being geographically and culturally close to Cyprus -- Greek retail traders are especially likely to already be familiar with CySEC-licensed brokers, many of which run Greek-language sites and support.',
                'No broker in our current dataset is headquartered in Greece itself, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'hungary' => array(
            'name' => 'Hungary', 'currency' => 'HUF', 'eurozone' => false, 'eu_since' => 2004,
            'regulator' => 'MNB (Magyar Nemzeti Bank)', 'hq_match' => 'Hungary',
            'paragraphs' => array(
                'Hungary joined the EU in 2004 and has not adopted the euro, retaining the forint (HUF). The Magyar Nemzeti Bank (MNB) acts as both central bank and financial regulator, enforcing the same EU-wide ESMA leverage caps and risk-disclosure rules on any broker serving Hungarian retail clients.',
                'No broker in our current dataset is headquartered in Hungary, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'ireland' => array(
            'name' => 'Ireland', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1973,
            'regulator' => 'Central Bank of Ireland', 'hq_match' => 'Ireland',
            'paragraphs' => array(
                'Ireland joined the EU in 1973 and the eurozone at its launch. It\'s genuinely home to one of the longest-established brokers in our rankings: AvaTrade is headquartered in Dublin, regulated there by the Central Bank of Ireland, with EU clients typically served through MiFID passporting rather than a separate CySEC licence.',
                'Ireland\'s an unusual case among the countries here in that respect -- most of our dataset is CySEC-licensed via Cyprus specifically, while Ireland has its own well-regarded domestic financial regulator that some brokers use as their primary EU base instead.',
            ),
        ),
        'italy' => array(
            'name' => 'Italy', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'CONSOB (Commissione Nazionale per le Società e la Borsa)', 'hq_match' => 'Italy',
            'paragraphs' => array(
                'A founding EU and eurozone member, Italy is supervised by CONSOB, which has taken an active enforcement role against unauthorized forex/CFD providers: since 2019 it has published and periodically updated a public blacklist of websites offering financial services in Italy without the required authorization.',
                'That doesn\'t affect properly CySEC-licensed, EU-passported brokers like the ones reviewed on this site, but it\'s a useful reminder to always verify a broker\'s actual licence status before depositing funds, rather than relying on marketing claims alone.',
            ),
        ),
        'latvia' => array(
            'name' => 'Latvia', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'Latvijas Banka', 'hq_match' => 'Latvia',
            'paragraphs' => array(
                'Latvia joined the EU in 2004 and the eurozone in 2014. Financial supervision, previously handled by a standalone regulator (the FCMC), was folded into the central bank, Latvijas Banka, in 2023. The same EU-wide ESMA leverage and marketing rules apply to any broker serving Latvian retail clients.',
                'No broker in our current dataset is headquartered in Latvia, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'lithuania' => array(
            'name' => 'Lithuania', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'Bank of Lithuania', 'hq_match' => 'Lithuania',
            'paragraphs' => array(
                'Lithuania joined the EU in 2004 and the eurozone in 2015. The Bank of Lithuania handles financial supervision alongside its central-banking role, and has also positioned the country as a notable fintech licensing hub in the Baltics -- though for CFD/forex brokers specifically, CySEC (via Cyprus) remains the far more common EU licence of choice among the firms reviewed here.',
                'No broker in our current dataset is headquartered in Lithuania itself, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'luxembourg' => array(
            'name' => 'Luxembourg', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'CSSF (Commission de Surveillance du Secteur Financier)', 'hq_match' => 'Luxembourg',
            'paragraphs' => array(
                'A founding EU and eurozone member, Luxembourg is supervised by the CSSF. Its financial sector is heavily weighted toward fund administration, private banking, and cross-border wealth management rather than retail CFD/forex trading, which is a comparatively small niche in the domestic market.',
                'No broker in our current dataset is headquartered in Luxembourg, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'malta' => array(
            'name' => 'Malta', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'MFSA (Malta Financial Services Authority)', 'hq_match' => 'Malta',
            'paragraphs' => array(
                'Malta joined the EU in 2004 and the eurozone in 2008. The MFSA has built its own reputation as a licensing hub for online financial and gaming businesses, in some ways a smaller parallel to Cyprus\'s role for CFD brokers specifically -- though CySEC remains the dominant EU licence among the brokers reviewed on this site.',
                'No broker in our current dataset is headquartered in Malta itself, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'netherlands' => array(
            'name' => 'Netherlands', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1958,
            'regulator' => 'AFM (Autoriteit Financiële Markten)', 'hq_match' => 'Netherlands',
            'paragraphs' => array(
                'A founding EU and eurozone member, the Netherlands is supervised by the AFM, which actively enforces the EU-wide requirement that CFD marketing display a clear percentage-of-retail-accounts-lose-money risk warning, along with the standard ESMA leverage caps.',
                'No broker in our current dataset is headquartered in the Netherlands, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local presence.',
            ),
        ),
        'poland' => array(
            'name' => 'Poland', 'currency' => 'PLN', 'eurozone' => false, 'eu_since' => 2004,
            'regulator' => 'KNF (Komisja Nadzoru Finansowego)', 'hq_match' => 'Poland',
            'paragraphs' => array(
                'Poland joined the EU in 2004 and has not adopted the euro, retaining the złoty (PLN). It\'s genuinely home to one of the largest brokers in our entire rankings: XTB was founded in Warsaw and remains listed on the Warsaw Stock Exchange, making it Poland\'s own home-grown entry in the CFD/forex brokerage industry, even though EU retail clients are served through its CySEC-licensed entity.',
                'The KNF regulates domestic financial markets and, alongside the EU-wide ESMA framework, applies its own scrutiny to leveraged retail products -- worth knowing given how prominent Polish-founded brokers are in this space.',
            ),
        ),
        'portugal' => array(
            'name' => 'Portugal', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1986,
            'regulator' => 'CMVM (Comissão do Mercado de Valores Mobiliários)', 'hq_match' => 'Portugal',
            'paragraphs' => array(
                'Portugal joined the EU in 1986 and the eurozone at its launch. The CMVM supervises domestic securities markets and enforces the same EU-wide ESMA leverage caps and marketing restrictions that apply to any broker serving Portuguese retail clients.',
                'No broker in our current dataset is headquartered in Portugal, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local footprint.',
            ),
        ),
        'romania' => array(
            'name' => 'Romania', 'currency' => 'RON', 'eurozone' => false, 'eu_since' => 2007,
            'regulator' => 'ASF (Autoritatea de Supraveghere Financiară)', 'hq_match' => 'Romania',
            'paragraphs' => array(
                'Romania joined the EU in 2007 and has not adopted the euro, retaining the leu (RON). The ASF supervises domestic non-banking financial markets, working alongside the EU-wide ESMA framework that governs any CySEC-passported broker serving Romanian retail clients.',
                'No broker in our current dataset is headquartered in Romania, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'slovakia' => array(
            'name' => 'Slovakia', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'NBS (Národná banka Slovenska)', 'hq_match' => 'Slovakia',
            'paragraphs' => array(
                'Slovakia joined the EU in 2004 and the eurozone in 2009. The National Bank of Slovakia (NBS) handles financial supervision alongside its central-banking role, applying the same EU-wide ESMA leverage caps and marketing rules as the rest of the bloc.',
                'No broker in our current dataset is headquartered in Slovakia, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'slovenia' => array(
            'name' => 'Slovenia', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 2004,
            'regulator' => 'ATVP (Agencija za trg vrednostnih papirjev)', 'hq_match' => 'Slovenia',
            'paragraphs' => array(
                'Slovenia joined the EU in 2004 and the eurozone in 2007. The ATVP supervises domestic securities markets, working within the same EU-wide ESMA leverage and marketing framework that applies to every CySEC-licensed broker passporting into the country.',
                'No broker in our current dataset is headquartered in Slovenia, so the ranking below reflects our overall EU-wide scoring.',
            ),
        ),
        'spain' => array(
            'name' => 'Spain', 'currency' => 'EUR', 'eurozone' => true, 'eu_since' => 1986,
            'regulator' => 'CNMV (Comisión Nacional del Mercado de Valores)', 'hq_match' => 'Spain',
            'paragraphs' => array(
                'Spain joined the EU in 1986 and the eurozone at its launch. Like Italy\'s CONSOB, the CNMV maintains and regularly updates a public warning list of unauthorized firms offering forex and CFD services to Spanish residents without the required licence.',
                'That enforcement stance doesn\'t affect properly CySEC-licensed, EU-passported brokers like the ones reviewed on this site, but it underlines why checking a broker\'s actual regulatory status -- not just its marketing -- matters before depositing funds anywhere.',
            ),
        ),
        'sweden' => array(
            'name' => 'Sweden', 'currency' => 'SEK', 'eurozone' => false, 'eu_since' => 1995,
            'regulator' => 'Finansinspektionen', 'hq_match' => 'Sweden',
            'paragraphs' => array(
                'Sweden joined the EU in 1995 and, like Denmark, has not adopted the euro, retaining the krona (SEK) following a 2003 referendum. Finansinspektionen supervises domestic financial markets and enforces the same EU-wide ESMA leverage caps and marketing restrictions as the rest of the bloc.',
                'No broker in our current dataset is headquartered in Sweden, so the ranking below reflects our overall EU-wide scoring rather than a confirmed local presence.',
            ),
        ),
    );
}

function globalfxhub_get_country_by_slug( $slug ) {
    $countries = globalfxhub_get_countries();
    return isset( $countries[ $slug ] ) ? array_merge( array( 'slug' => $slug ), $countries[ $slug ] ) : null;
}

/**
 * Ranks brokers for a given country: any broker actually HEADQUARTERED
 * there is surfaced first (sorted by their own overall rank), then the
 * list is filled up to $limit with the next-best brokers from the
 * site-wide ranking. Each returned broker carries an 'is_local_hq' flag
 * so the template can label it distinctly.
 *
 * Matching only looks at the primary clause of the 'hq' field (the part
 * before the first "(" or ";"), not the whole string -- several brokers'
 * hq text reads like "Melbourne, Australia (group); ... Cyprus entity
 * based in Limassol" or "Limassol, Cyprus (with a branch office in
 * Hamburg, Germany)", where the country named later is a *branch* or a
 * *regulated entity*, not the actual headquarters. Matching the full
 * string would wrongly label a dozen-plus Australia/UK/Israel/US-based
 * brokers as "headquartered in Cyprus" just because their CySEC entity
 * is mentioned there.
 *
 * When no broker is headquartered in the country (the common case), this
 * simply returns the global top $limit -- an honest reflection of having
 * no country-specific signal, not a placeholder to be replaced later.
 */
function globalfxhub_country_broker_ranking( $country, $limit = 10 ) {
    $all = globalfxhub_get_brokers();
    usort( $all, function( $a, $b ) {
        return $a['rank'] <=> $b['rank'];
    } );

    $local = array();
    $rest  = array();
    foreach ( $all as $broker ) {
        $primary_hq = ! empty( $broker['hq'] ) ? preg_split( '/[(;]/', $broker['hq'], 2 )[0] : '';
        $broker['is_local_hq'] = '' !== $primary_hq && false !== stripos( $primary_hq, $country['hq_match'] );
        if ( $broker['is_local_hq'] ) {
            $local[] = $broker;
        } else {
            $rest[] = $broker;
        }
    }

    return array_slice( array_merge( $local, $rest ), 0, $limit );
}

/**
 * /countries/{slug}/ needs a "Countries" page on the "Countries Index"
 * template; self-creates/self-repairs on every load like the other
 * templated pages.
 */
function globalfxhub_ensure_countries_page() {
    globalfxhub_ensure_templated_page( 'countries', 'Countries', 'page-countries.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_countries_page' );

/**
 * Pretty URLs for individual country pages: /countries/{slug}/ -- same
 * rewrite-rule + query-var pattern as /reviews/{slug}/.
 */
function globalfxhub_country_rewrite_rules() {
    add_rewrite_rule( '^countries/([^/]+)/?$', 'index.php?pagename=countries&country=$matches[1]', 'top' );
}
add_action( 'init', 'globalfxhub_country_rewrite_rules' );

function globalfxhub_country_query_vars( $vars ) {
    $vars[] = 'country';
    return $vars;
}
add_filter( 'query_vars', 'globalfxhub_country_query_vars' );

/**
 * SEO for /countries/{slug}/ pages: distinct title, canonical, and meta
 * description per country, mirroring how /reviews/{slug}/ is handled --
 * otherwise every country URL would share the "Countries" page's single
 * default title and description.
 */
function globalfxhub_country_seo_title( $title_parts ) {
    $slug = get_query_var( 'country' );
    if ( $slug ) {
        $country = globalfxhub_get_country_by_slug( $slug );
        if ( $country ) {
            $title_parts['title'] = 'Best Forex & CFD Brokers in ' . $country['name'] . ' ' . date( 'Y' );
        }
    } elseif ( is_page( 'countries' ) ) {
        $title_parts['title'] = 'Best Forex Brokers by EU Country ' . date( 'Y' );
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_country_seo_title' );

function globalfxhub_country_canonical( $canonical_url ) {
    $slug = get_query_var( 'country' );
    if ( $slug ) {
        $country = globalfxhub_get_country_by_slug( $slug );
        if ( $country ) {
            return home_url( '/countries/' . $slug . '/' );
        }
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_country_canonical' );

function globalfxhub_country_seo_head() {
    $slug = get_query_var( 'country' );
    if ( ! $slug ) {
        if ( is_page( 'countries' ) ) {
            echo '<meta name="description" content="' . esc_attr( 'Top-ranked CySEC-regulated forex and CFD brokers for every EU member state, with what\'s actually different -- regulator, currency, and local broker presence -- about trading in each one.' ) . '">' . "\n";
        }
        return;
    }
    $country = globalfxhub_get_country_by_slug( $slug );
    if ( ! $country ) {
        return;
    }
    $description = sprintf(
        'The top CySEC-regulated forex and CFD brokers available to retail traders in %s, ranked by our disclosed methodology -- plus what\'s specific to %s: currency, regulator, and EU rules.',
        $country['name'],
        $country['name']
    );
    echo '<meta name="description" content="' . esc_attr( $description ) . '">' . "\n";
}
add_action( 'wp_head', 'globalfxhub_country_seo_head', 5 );
