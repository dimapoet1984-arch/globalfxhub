<?php
/**
 * Regulator entity pages: /regulators/{slug}/ -- a live, structured
 * roster (which of our 139 researched brokers currently hold this
 * regulator's licence, under which entity where that's safely
 * attributable, and what licence number if any) plus the regulator's
 * own key facts (jurisdiction, leverage caps, compensation scheme,
 * official register, complaint process).
 *
 * Deliberately NOT a duplicate of /regulation/{slug}/ (inc/regulation.php):
 * that page is a prose explainer of what the regulator is and how to
 * verify a licence yourself. This page is the data companion -- a
 * computed roster and a facts panel, cross-linked both ways rather
 * than merged into one page, the same relationship /regulation/
 * {slug}/ already has with /regulation/fca-vs-cysec/ (inc/regulation-
 * comparisons.php). The counts below are computed fresh from
 * globalfxhub_get_brokers() on every load, never hardcoded, so they
 * can't drift the way a written-once stat could.
 *
 * Five regulators total. CySEC, FCA, and Seychelles FSA have a
 * structured per-broker licence-number field (consistently populated
 * or explicitly null); ASIC and FSCA only have a bare "holds this
 * licence" flag from the free-text 'other_reg' list, with no per-
 * broker licence number -- their roster rows and facts panel are
 * honest about that gap (see 'tracks_licence_numbers' below and
 * globalfxhub_regulator_entity_bearing_slugs()) rather than guessing
 * a number or implying one exists. Other regulators that show up in
 * 'other_reg' (DFSA, FMA, MAS, CMA, ...) have smaller sample counts
 * in this dataset and still need their own regulator-facts research
 * before a page is worth adding -- left for a follow-up.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_get_regulator_entities() {
    return array(
        'cysec' => array(
            'name'               => 'CySEC',
            'full_name'          => 'Cyprus Securities and Exchange Commission',
            'jurisdiction'       => 'Cyprus, with full EU/EEA passporting rights under MiFID II',
            'leverage_note'      => 'ESMA-aligned retail leverage caps: up to 30:1 on major FX pairs, down to 2:1 on crypto CFDs.',
            'compensation_scheme' => 'Investor Compensation Fund (ICF)',
            'compensation_cap'   => '&euro;20,000 per eligible retail client',
            'complaint_process'  => 'A client disputes a CySEC-licensed firm directly with the firm first; if unresolved, CySEC itself and the Financial Ombudsman of the Republic of Cyprus both take complaints against licensed Cyprus Investment Firms.',
            'register_url'       => 'https://www.cysec.gov.cy/en-GB/entities/investment-firms/cypriot/',
            'register_label'     => "CySEC's official register of Cypriot Investment Firms",
            'broker_field'        => 'cysec',
            'tracks_licence_numbers' => true,
            'related_regulation_slug' => 'cysec',
            'related_best_slug'  => 'cysec-regulated',
        ),
        'fca' => array(
            'name'               => 'FCA',
            'full_name'          => 'Financial Conduct Authority',
            'jurisdiction'       => 'United Kingdom -- UK clients only since Brexit; no automatic EU passporting',
            'leverage_note'      => 'Retained the same ESMA-style retail leverage caps post-Brexit: up to 30:1 on major FX pairs, down to 2:1 on crypto CFDs.',
            'compensation_scheme' => 'Financial Services Compensation Scheme (FSCS)',
            'compensation_cap'   => '&pound;85,000 per eligible retail client',
            'complaint_process'  => "A client disputes an FCA-authorised firm directly with the firm first; if unresolved within 8 weeks, the UK's Financial Ombudsman Service can review the complaint independently and its decisions are binding on the firm.",
            'register_url'       => 'https://register.fca.org.uk/s/',
            'register_label'     => 'the official FCA Financial Services Register',
            'broker_field'        => 'fca',
            'tracks_licence_numbers' => true,
            'related_regulation_slug' => 'fca',
            'related_best_slug'  => 'fca-regulated',
        ),
        'seychelles-fsa' => array(
            'name'               => 'Seychelles FSA',
            'full_name'          => 'Seychelles Financial Services Authority',
            'jurisdiction'       => 'Seychelles -- an offshore licence typically covering clients outside the EU/UK',
            'leverage_note'      => "Materially less prescriptive than MiFID or FCA rules -- part of why offshore entities can advertise much higher leverage than an EU/UK sister brand under the same brand name.",
            'compensation_scheme' => null,
            'compensation_cap'   => null,
            'complaint_process'  => "A client disputes a Seychelles-licensed firm directly with the firm; the FSA itself can take regulatory action on a licensed Securities Dealer, but there is no independent ombudsman scheme equivalent to the UK's or Cyprus's, and no statutory compensation fund if the firm simply can't pay.",
            'register_url'       => 'https://fsaseychelles.sc/',
            'register_label'     => 'the official FSA Seychelles register',
            'broker_field'        => 'seychelles',
            'tracks_licence_numbers' => true,
            'related_regulation_slug' => 'seychelles-fsa',
            'related_best_slug'  => null,
        ),
        'asic' => array(
            'name'               => 'ASIC',
            'full_name'          => 'Australian Securities and Investments Commission',
            'jurisdiction'       => 'Australia -- licenses firms as Australian Financial Services (AFS) Licensees',
            'leverage_note'      => "Binding retail CFD leverage caps under ASIC's Product Intervention Order (in force since 29 March 2021, extended to 23 May 2027): 30:1 on major FX pairs, 20:1 on minor FX pairs/gold/major indices, 10:1 on other commodities/minor indices, 5:1 on shares, 2:1 on crypto-assets, plus mandatory negative balance protection.",
            'compensation_scheme' => 'Compensation Scheme of Last Resort (CSLR) -- scope uncertain for CFDs',
            'compensation_cap'   => 'Pays unpaid AFCA determinations up to AU$150,000, but its confirmed scope is personal financial advice, securities dealing for retail clients, and credit -- whether a CFD/forex broker dispute counts as "securities dealing" isn\'t clearly confirmed from public guidance, so this isn\'t presented as a confirmed safety net for a CFD/forex-specific loss.',
            'complaint_process'  => "A client disputes an AFS Licensee directly with the firm first; unresolved complaints can go to the Australian Financial Complaints Authority (AFCA), an independent external dispute resolution body whose determinations are binding on the firm.",
            'register_url'       => 'https://www.asic.gov.au/online-services/search-asic-s-registers/professional-registers/',
            'register_label'     => "ASIC's Professional Registers Search (search by AFS Licence number or entity name)",
            'broker_field'        => null,
            'tracks_licence_numbers' => false,
            'related_regulation_slug' => null,
            'related_learn_slug' => 'asic-forex-regulation-explained',
            'related_best_slug'  => 'asic-regulated',
        ),
        'fsca' => array(
            'name'               => 'FSCA',
            'full_name'          => 'Financial Sector Conduct Authority (South Africa)',
            'jurisdiction'       => 'South Africa -- licenses firms as Financial Services Providers (FSPs), typically authorised as Over-the-Counter Derivative Providers (ODPs) for this industry',
            'leverage_note'      => "No confirmed leverage cap found in FSCA public guidance -- its Joint Standard 2 of 2020 covers margin for non-centrally-cleared OTC derivatives, a different mechanism from a retail leverage ceiling. FSCA-regulated brokers commonly advertise leverage up to 1:500 or higher.",
            'compensation_scheme' => null,
            'compensation_cap'   => null,
            'complaint_process'  => "A client disputes an FSCA-licensed FSP directly with the firm first; if unresolved, the FAIS Ombud -- an independent, free dispute-resolution body -- can investigate and issue a determination, which can be sent back for reconsideration via the Financial Services Tribunal. For complaints received on or after 1 July 2024, the Ombud can award up to R3.5 million in compensation (up from an R800,000 cap that applied to older complaints).",
            'register_url'       => 'https://www.fsca.co.za/Fais/Search_FSP.htm',
            'register_label'     => "the FSCA's FSP register (search by name or FSP number)",
            'broker_field'        => null,
            'tracks_licence_numbers' => false,
            'related_regulation_slug' => null,
            'related_learn_slug' => 'fsca-forex-regulation-explained',
            'related_best_slug'  => 'fsca-regulated',
        ),
    );
}

/**
 * Which of the five regulators a broker holds, using the exact same
 * conditions globalfxhub_broker_regulation_label() and the
 * 'cysec-regulated'/'fca-regulated' best-list filters (inc/best.php)
 * already use for cysec/fca -- so "holds a CySEC licence" means the
 * same thing on every page that claims it, by construction rather
 * than by two separately-written conditions drifting apart.
 *
 * 'asic' and 'fsca' have no per-broker licence-number field -- just
 * a bare "holds this licence" signal from the free-text 'other_reg'
 * list, matched against the exact strings that list consistently
 * uses ('ASIC', 'FSCA'; confirmed by a full-dataset scan, no near-
 * miss spellings). See globalfxhub_get_regulator_entities()'s
 * 'tracks_licence_numbers' flag for how the roster template reflects
 * that difference rather than implying a confirmed-but-missing number.
 */
function globalfxhub_broker_regulator_flags( $broker ) {
    return array(
        'cysec'          => null !== $broker['cysec'],
        'fca'            => ! empty( $broker['fca'] ) || ! empty( $broker['fca_note'] ),
        'seychelles-fsa' => ! empty( $broker['seychelles'] ) || ( ! empty( $broker['seychelles_note'] ) && array_key_exists( 'seychelles', $broker ) && null === $broker['seychelles'] ),
        'asic'           => in_array( 'ASIC', $broker['other_reg'] ?? array(), true ),
        'fsca'           => in_array( 'FSCA', $broker['other_reg'] ?? array(), true ),
    );
}

/**
 * Licence number this broker discloses for the given regulator, or
 * null if unconfirmed/not applicable. Only 'cysec' and 'fca' and
 * 'seychelles' are real array keys with a number-or-null value; the
 * '—' sentinel on 'cysec' means "EU-regulated, number not confirmed".
 */
function globalfxhub_broker_regulator_licence_number( $broker, $regulator_slug ) {
    $field_map = array( 'cysec' => 'cysec', 'fca' => 'fca', 'seychelles-fsa' => 'seychelles' );
    if ( ! isset( $field_map[ $regulator_slug ] ) ) {
        return null;
    }
    $value = $broker[ $field_map[ $regulator_slug ] ] ?? null;
    if ( null === $value || '—' === $value ) {
        return null;
    }
    return $value;
}

/**
 * Regulators whose per-broker 'entity' field is confirmed (against
 * the raw data) to reliably name the entity holding THAT specific
 * licence -- cysec/fca/seychelles-fsa only. 'asic'/'fsca' are always
 * secondary licences layered on top of one of these three (no broker
 * holds ASIC/FSCA as its only regulator in this dataset), and have
 * no field of their own confirming which entity holds them, so their
 * roster never claims one -- see globalfxhub_regulator_entity_brokers().
 */
function globalfxhub_regulator_entity_bearing_slugs() {
    return array( 'cysec', 'fca', 'seychelles-fsa' );
}

/**
 * The live roster for one regulator: every broker in our researched
 * set that currently holds it, with a licence number where disclosed
 * (cysec/fca/seychelles-fsa only -- see globalfxhub_broker_regulator_licence_number())
 * and a legal entity name ONLY when both (a) this regulator is one of
 * the three entity-bearing ones above, AND (b) this broker holds
 * exactly one of those three -- a broker holding both CySEC and FCA
 * has a single 'entity' field that's reliably the CySEC/EU entity
 * (confirmed against the raw data), so showing it again under "FCA"
 * would misattribute a legal entity to the wrong licence. Rather than
 * guess which of a multi-entity broker's names belongs to which
 * regulator, this just doesn't claim one -- the broker's own review
 * page has the full multi-entity breakdown.
 */
function globalfxhub_regulator_entity_brokers( $regulator_slug ) {
    $entity_bearing_slugs = globalfxhub_regulator_entity_bearing_slugs();
    $rows = array();
    foreach ( globalfxhub_get_brokers() as $broker ) {
        $flags = globalfxhub_broker_regulator_flags( $broker );
        if ( empty( $flags[ $regulator_slug ] ) ) {
            continue;
        }
        $entity_bearing_held = 0;
        foreach ( $entity_bearing_slugs as $s ) {
            if ( ! empty( $flags[ $s ] ) ) {
                $entity_bearing_held++;
            }
        }
        $show_entity = in_array( $regulator_slug, $entity_bearing_slugs, true ) && 1 === $entity_bearing_held;
        $rows[] = array(
            'slug'            => $broker['slug'],
            'name'            => $broker['name'],
            'licence_number'  => globalfxhub_broker_regulator_licence_number( $broker, $regulator_slug ),
            'entity'          => ( $show_entity && ! empty( $broker['entity'] ) ) ? $broker['entity'] : null,
            'review_url'      => home_url( '/reviews/' . $broker['slug'] . '/' ),
            'rank'            => $broker['rank'],
        );
    }
    usort( $rows, function( $a, $b ) { return $a['rank'] <=> $b['rank']; } );
    return $rows;
}

/**
 * Aggregate stat for one regulator -- an original, dynamically-
 * computed figure (not written once and left to go stale) safe to
 * cite anywhere on the site.
 */
function globalfxhub_regulator_entity_stats( $regulator_slug ) {
    $total = count( globalfxhub_get_brokers() );
    $held = count( globalfxhub_regulator_entity_brokers( $regulator_slug ) );
    return array(
        'held'    => $held,
        'total'   => $total,
        'percent' => $total > 0 ? round( $held / $total * 100 ) : 0,
    );
}

/**
 * SEO for /regulators/ and /regulators/{slug}/, same "one page object
 * serves many distinct URLs" treatment already applied to /regulation/
 * {slug}/, /research/{slug}/, etc.
 */
function globalfxhub_regulator_entity_seo_title( $title_parts ) {
    $slug = get_query_var( 'regulator' );
    if ( $slug ) {
        $entities = globalfxhub_get_regulator_entities();
        if ( isset( $entities[ $slug ] ) ) {
            $title_parts['title'] = $entities[ $slug ]['name'] . ' Regulated Brokers & Licence Register | GlobalFXHub';
        }
    } elseif ( is_page( 'regulators' ) ) {
        $title_parts['title'] = 'Regulators: Licensed Broker Rosters by Regulator';
    }
    return $title_parts;
}
add_filter( 'document_title_parts', 'globalfxhub_regulator_entity_seo_title' );

function globalfxhub_regulator_entity_canonical( $canonical_url ) {
    $slug = get_query_var( 'regulator' );
    if ( $slug ) {
        $entities = globalfxhub_get_regulator_entities();
        if ( isset( $entities[ $slug ] ) ) {
            return home_url( '/regulators/' . $slug . '/' );
        }
    }
    return $canonical_url;
}
add_filter( 'get_canonical_url', 'globalfxhub_regulator_entity_canonical' );

function globalfxhub_regulator_entity_seo_head() {
    $slug = get_query_var( 'regulator' );
    if ( $slug ) {
        $entities = globalfxhub_get_regulator_entities();
        if ( isset( $entities[ $slug ] ) ) {
            $stats = globalfxhub_regulator_entity_stats( $slug );
            $desc = $entities[ $slug ]['full_name'] . ': jurisdiction, compensation cover, and the ' . $stats['held'] . ' of ' . $stats['total'] . ' brokers in our researched set currently holding this licence, with entity and licence-number detail where disclosed.';
            echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
        }
        return;
    }
    if ( is_page( 'regulators' ) ) {
        echo '<meta name="description" content="' . esc_attr( 'Which of our 139 researched forex/CFD brokers currently hold a licence from each regulator -- CySEC, FCA, Seychelles FSA, ASIC, and FSCA -- with jurisdiction, compensation cover, and the official register for each.' ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_regulator_entity_seo_head', 5 );
