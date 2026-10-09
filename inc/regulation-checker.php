<?php
/**
 * Broker Regulation Checker (/regulation-checker/): search any researched
 * broker by name and get its regulation dossier -- legal entity on file,
 * FCA/CySEC numbers, named Tier-1 regulators (ASIC, DFSA, etc.), offshore
 * entities, which regulator covers which geography, direct register
 * links, and when the regulation data was last reviewed.
 *
 * This is almost entirely a reshaping of facts the site already carries
 * per broker (the same cysec/fca/seychelles/other_reg fields the review
 * page, /best/cysec-regulated, /best/fca-regulated, and the Broker Finder
 * all already read from) plus globalfxhub_broker_review_dates(), which
 * already exists. The one genuinely new piece is the regulator-code to
 * geography map below.
 *
 * FMA, CMA, SCB, and FSAS were originally left unmapped here, since each
 * abbreviation has more than one real-world regulator it could plausibly
 * refer to in the abstract (e.g. "FMA" is also Austria's and
 * Liechtenstein's Finanzmarktaufsicht, not just New Zealand's). They've
 * since been resolved by checking, broker by broker, which specific
 * regulator each of this dataset's current holders of that code actually
 * holds -- not by assuming the abbreviation's meaning in general:
 *  - FMA: all four current holders (ThinkMarkets, Plus500, CMC Markets,
 *    Axi) are confirmed, independently, as licensed derivatives issuers
 *    on New Zealand's Financial Service Providers Register (FSP623289,
 *    FSP486026, FSP41187, FSP518226 respectively) -- New Zealand's
 *    Financial Markets Authority.
 *  - CMA: all three current holders (Exness, INGOT Brokers, Equiti) are
 *    confirmed as licensed by Kenya's Capital Markets Authority (INGOT's
 *    own announcement cites licence No. 173; Equiti's Kenyan subsidiary,
 *    EGM Securities Ltd, was the CMA's first-ever online forex broker
 *    licensee in 2018).
 *  - SCB: all three current holders (Eightcap, FxPro, IQBroker) are
 *    confirmed as licensed by the Securities Commission of The Bahamas,
 *    each under the Commission's "SIA-F" numbering (SIA-F220, SIA-F184,
 *    SIA-F219 respectively) -- FxPro's own licences page confirms
 *    SIA-F184 directly.
 *  - FSAS: eToro is the only current holder, and eToro's own regulation
 *    disclosure names it directly: "eToro (Seychelles) Ltd... licenced
 *    by the Financial Services Authority Seychelles ('FSAS')... License
 *    number: SD076" -- the exact same licence already carried in this
 *    site's data under eToro's dedicated 'seychelles' field, confirming
 *    FSAS is that broker's Seychelles FSA licence restated, not a
 *    separate jurisdiction.
 * If a broker is ever added that carries one of these four codes, its
 * specific holder should be re-verified the same way before trusting
 * this map for it -- the resolution above is per confirmed holder, not a
 * claim that the abbreviation always means the same thing everywhere.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Keyed by the exact string as it appears in a broker's 'other_reg'
 * array. Register URLs point at each regulator's own public register or
 * its landing page when no single canonical search URL exists -- same
 * confidence precedent as the CySEC/FCA/Seychelles links already
 * hardcoded in globalfxhub_broker_verify_links().
 *
 * FMA, CMA, SCB, and FSAS were resolved per the research cited in the
 * file header comment above -- verified against this dataset's actual
 * current holders of each code, not assumed from the abbreviation alone.
 */
function globalfxhub_regulation_checker_geography_map() {
    return array(
        'ASIC'            => array( 'geography' => 'Australia', 'register_url' => 'https://asic.gov.au/online-services/search-asic-registers/' ),
        'FSCA'            => array( 'geography' => 'South Africa', 'register_url' => 'https://www.fsca.co.za/Fais/Search_FSP.htm' ),
        'DFSA'            => array( 'geography' => 'Dubai International Financial Centre (UAE)', 'register_url' => 'https://www.dfsa.ae/public-register' ),
        'MAS'             => array( 'geography' => 'Singapore', 'register_url' => 'https://eservices.mas.gov.sg/fid' ),
        'BaFin'           => array( 'geography' => 'Germany', 'register_url' => 'https://www.bafin.de/EN/PublikationenDaten/Datenbanken/datenbanken_node_en.html' ),
        'NFA/CFTC (US)'   => array( 'geography' => 'United States', 'register_url' => 'https://www.nfa.futures.org/basicnet/' ),
        'KNF (Poland)'    => array( 'geography' => 'Poland', 'register_url' => 'https://www.knf.gov.pl/en/' ),
        'JFSA (Japan)'    => array( 'geography' => 'Japan', 'register_url' => 'https://www.fsa.go.jp/en/' ),
        'CIRO'            => array( 'geography' => 'Canada', 'register_url' => 'https://www.ciro.ca/' ),
        'FMA'             => array( 'geography' => 'New Zealand', 'register_url' => 'https://fsp-register.companiesoffice.govt.nz/' ),
        'CMA'             => array( 'geography' => 'Kenya', 'register_url' => 'https://www.cma.or.ke/' ),
        'SCB'             => array( 'geography' => 'The Bahamas', 'register_url' => 'https://www.scb.gov.bs/registrant-licensee-search/' ),
        'FSAS'            => array( 'geography' => 'Seychelles', 'register_url' => 'https://fsaseychelles.sc/' ),
    );
}

/**
 * Builds the full regulation dossier for one broker. Every value here
 * traces straight back to an existing field; nothing is inferred beyond
 * the disclosed, conservative geography map above.
 */
function globalfxhub_broker_regulation_dossier( $broker ) {
    $geo_map = globalfxhub_regulation_checker_geography_map();

    $fca = null;
    if ( ! empty( $broker['fca'] ) ) {
        $fca = array(
            'number'       => $broker['fca'],
            'confirmed'    => true,
            'note'         => null,
            'register_url' => 'https://register.fca.org.uk/s/',
        );
    } elseif ( ! empty( $broker['fca_note'] ) ) {
        $fca = array(
            'number'       => null,
            'confirmed'    => false,
            'note'         => $broker['fca_note'],
            'register_url' => 'https://register.fca.org.uk/s/',
        );
    }

    $cysec = null;
    if ( null !== $broker['cysec'] ) {
        $cysec = array(
            'number'       => ( '—' === $broker['cysec'] ) ? null : $broker['cysec'],
            'passported'   => ( '—' === $broker['cysec'] ),
            'note'         => $broker['cysec_note'] ?? null,
            'register_url' => 'https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/',
        );
    }

    $seychelles = null;
    if ( ! empty( $broker['seychelles'] ) ) {
        $seychelles = array(
            'number'       => $broker['seychelles'],
            'confirmed'    => true,
            'note'         => null,
            'register_url' => 'https://fsaseychelles.sc/',
        );
    } elseif ( ! empty( $broker['seychelles_note'] ) ) {
        $seychelles = array(
            'number'       => null,
            'confirmed'    => false,
            'note'         => $broker['seychelles_note'],
            'register_url' => 'https://fsaseychelles.sc/',
        );
    }

    $other_regulators = array();
    foreach ( (array) ( $broker['other_reg'] ?? array() ) as $code ) {
        $mapped = $geo_map[ $code ] ?? null;
        $other_regulators[] = array(
            'code'         => $code,
            'geography'    => $mapped['geography'] ?? null,
            'register_url' => $mapped['register_url'] ?? null,
        );
    }

    return array(
        'slug'             => $broker['slug'],
        'name'             => $broker['name'],
        'entity'           => $broker['entity'] ?? null,
        'hq'               => $broker['hq'] ?? null,
        'fca'              => $fca,
        'cysec'            => $cysec,
        'seychelles'       => $seychelles,
        'other_regulators' => $other_regulators,
        'regulation_label' => globalfxhub_broker_regulation_label( $broker ),
        'review_dates'     => globalfxhub_broker_review_dates( $broker ),
        'review_url'       => home_url( '/reviews/' . $broker['slug'] . '/' ),
    );
}

function globalfxhub_regulation_checker_payload() {
    return array_map( 'globalfxhub_broker_regulation_dossier', globalfxhub_get_brokers() );
}

function globalfxhub_ensure_regulation_checker_page() {
    globalfxhub_ensure_templated_page( 'regulation-checker', 'Broker Regulation Checker', 'page-regulation-checker.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_regulation_checker_page' );
