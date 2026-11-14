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
 * Scoped to the three regulators this dataset has structured per-
 * broker fields for (a licence-number field, consistently populated
 * or explicitly null) -- cysec, fca, seychelles. Other regulators
 * that show up in a broker's 'other_reg' list (ASIC, FSCA, DFSA, ...)
 * have only a bare "holds this licence" flag with no per-broker
 * licence number, so a page for those would need new regulator-facts
 * research, not just data assembly -- left for a follow-up rather
 * than shipped thin or guessed.
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
            'related_regulation_slug' => 'seychelles-fsa',
            'related_best_slug'  => null,
        ),
    );
}

/**
 * Which of the three structured regulators a broker holds, using the
 * exact same conditions globalfxhub_broker_regulation_label() and the
 * 'cysec-regulated'/'fca-regulated' best-list filters (inc/best.php)
 * already use -- so "holds a CySEC licence" means the same thing on
 * every page that claims it, by construction rather than by two
 * separately-written conditions drifting apart.
 */
function globalfxhub_broker_regulator_flags( $broker ) {
    return array(
        'cysec'          => null !== $broker['cysec'],
        'fca'            => ! empty( $broker['fca'] ) || ! empty( $broker['fca_note'] ),
        'seychelles-fsa' => ! empty( $broker['seychelles'] ) || ( ! empty( $broker['seychelles_note'] ) && array_key_exists( 'seychelles', $broker ) && null === $broker['seychelles'] ),
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
 * The live roster for one regulator: every broker in our researched
 * set that currently holds it, with a licence number where disclosed
 * and a legal entity name ONLY when this broker holds exactly this
 * one of the three structured regulators -- a broker holding both
 * CySEC and FCA has a single 'entity' field that's reliably the
 * CySEC/EU entity (confirmed against the raw data), so showing it
 * again under "FCA" would misattribute a legal entity to the wrong
 * licence. Rather than guess which of a multi-entity broker's names
 * belongs to which regulator, this just doesn't claim one -- the
 * broker's own review page has the full multi-entity breakdown.
 */
function globalfxhub_regulator_entity_brokers( $regulator_slug ) {
    $rows = array();
    foreach ( globalfxhub_get_brokers() as $broker ) {
        $flags = globalfxhub_broker_regulator_flags( $broker );
        if ( empty( $flags[ $regulator_slug ] ) ) {
            continue;
        }
        $held_count = count( array_filter( $flags ) );
        $rows[] = array(
            'slug'            => $broker['slug'],
            'name'            => $broker['name'],
            'licence_number'  => globalfxhub_broker_regulator_licence_number( $broker, $regulator_slug ),
            'entity'          => ( 1 === $held_count && ! empty( $broker['entity'] ) ) ? $broker['entity'] : null,
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
        echo '<meta name="description" content="' . esc_attr( 'Which of our 139 researched forex/CFD brokers currently hold a licence from each regulator -- CySEC, FCA, and Seychelles FSA -- with jurisdiction, compensation cover, and the official register for each.' ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_regulator_entity_seo_head', 5 );
