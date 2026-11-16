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
 * Nine regulators total. CySEC, FCA, and Seychelles FSA have a
 * structured per-broker licence-number field (consistently populated
 * or explicitly null); the other six (ASIC, FSCA, DFSA, FMA-NZ, MAS,
 * CMA-Kenya) only have a bare "holds this licence" flag from the
 * free-text 'other_reg' list, with no per-broker licence number --
 * their roster rows and facts panel are honest about that gap (see
 * 'tracks_licence_numbers' below and
 * globalfxhub_regulator_entity_bearing_slugs()) rather than guessing
 * a number or implying one exists.
 *
 * DFSA/FMA-NZ/MAS/CMA-Kenya have small sample counts in this dataset
 * (10/4/5/3 of 139 brokers respectively) and, except for DFSA, no
 * dedicated Learn-cluster explainer -- that cluster's "Broker
 * regulation" roster is a fixed, already-full 10-article list
 * (inc/learn-articles.php), so rather than unilaterally expanding it,
 * their 'related_learn_slug' points at the general
 * forex-broker-regulation-explained overview instead of a dedicated
 * piece. No new /best/{slug}-regulated/ list was added for any of
 * the four either: with only 3-9 brokers, a separate ranked list
 * would just repeat this page's own roster table.
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
        'dfsa' => array(
            'name'               => 'DFSA',
            'full_name'          => 'Dubai Financial Services Authority',
            'jurisdiction'       => 'Dubai International Financial Centre (DIFC) -- a defined financial free zone, distinct from the UAE\'s wider federal regulators',
            'leverage_note'      => "Raised minimum margin requirements for retail clients effective 6 December 2021: 3.33% margin (30:1 leverage) on major FX pairs, 5% (20:1) on gold, and 10% (10:1) on oil -- confirmed against this site's own UAE country-page research. A 2025 industry report instead cited a 50:1 retail cap on majors, which conflicts with the 2021 margin rule; treat that higher figure as unconfirmed or possibly outdated. These limits don't apply to clients who qualify for and elect Professional classification, who can reportedly access much higher leverage.",
            'compensation_scheme' => null,
            'compensation_cap'   => null,
            'complaint_process'  => "DFSA-authorised firms must have their own adequate complaint-handling mechanism, and a client disputes the firm directly first. The DFSA itself will consider a written complaint about specific matters (integrity and fair dealing, corporate governance failures, or misuse of the DIFC/DFSA name), but it isn't a general-purpose ombudsman for every type of client dispute. No DFSA-administered investor compensation scheme for retail CFD/forex client losses was confirmed -- its framework centres on licensing, capital, and client-money segregation rather than a standing compensation fund.",
            'register_url'       => 'https://www.dfsa.ae/public-register/firms',
            'register_label'     => "the DFSA's public register of authorised firms",
            'broker_field'        => null,
            'tracks_licence_numbers' => false,
            'related_regulation_slug' => null,
            'related_learn_slug' => 'dfsa-forex-regulation-explained',
            'related_best_slug'  => null,
        ),
        // Keyed 'fma-nz', not 'fma': Austria's regulator is ALSO named
        // "FMA" (Finanzmarktaufsicht -- see inc/countries.php's
        // 'austria' entry), a completely different body. The four
        // brokers holding this licence in our dataset (thinkmarkets,
        // plus500, cmc-markets, axi) are all UK/AU/Israel-HQ'd with no
        // Austria connection, confirming this is New Zealand's FMA --
        // but the bare acronym alone is genuinely ambiguous, so the
        // key and the 'other_reg' match below are kept explicit.
        'fma-nz' => array(
            'name'               => 'FMA (NZ)',
            'full_name'          => 'Financial Markets Authority (New Zealand)',
            'jurisdiction'       => 'New Zealand -- forex/CFD trading is regulated as derivatives trading, requiring a Derivatives Issuer (DI) licence',
            'leverage_note'      => "No leverage cap currently in force -- licensed Derivatives Issuers set their own limits, which the FMA has found range from 30:1 to 500:1 across licensees. The FMA proposed (2024) capping retail CFD leverage at up to 30:1; as of this research that proposal's final status wasn't confirmed, so treat any cap as not yet in effect unless you confirm otherwise directly with the FMA or the broker.",
            'compensation_scheme' => null,
            'compensation_cap'   => null,
            'complaint_process'  => "A client disputes a registered Financial Service Provider (FSP) directly first, then can escalate to that FSP's Dispute Resolution Scheme (DRS) -- e.g. Financial Services Complaints Ltd (FSCL) -- found by searching the Financial Service Providers Register (FSPR). There's no statutory compensation fund; a DRS investigates and can resolve a dispute, but doesn't guarantee a payout. Being on the FSPR itself is a weaker signal than it sounds -- historically a firm could register without any real New Zealand presence, so registration alone isn't proof of being actively supervised the way a DI licence is.",
            'register_url'       => 'https://fsp-register.companiesoffice.govt.nz/',
            'register_label'     => "the Financial Service Providers Register (FSPR)",
            'broker_field'        => null,
            'tracks_licence_numbers' => false,
            'related_regulation_slug' => null,
            'related_learn_slug' => 'forex-broker-regulation-explained',
            'related_best_slug'  => null,
        ),
        'mas' => array(
            'name'               => 'MAS',
            'full_name'          => 'Monetary Authority of Singapore',
            'jurisdiction'       => 'Singapore -- leveraged forex/CFD trading requires a Capital Markets Services (CMS) licence under the Securities and Futures Act',
            'leverage_note'      => "MAS cut the retail forex leverage cap from 50:1 to 20:1 in 2019 -- confirmed against this site's own Singapore country-page research, though not independently re-confirmed against the current MAS rulebook for this page. A Singapore \"accredited investor\" (meeting the jurisdiction's wealth/experience criteria) can reportedly access up to 50:1, with correspondingly reduced regulatory protection. Offshore brokers serving Singapore residents outside MAS's licensing regime aren't bound by this cap at all.",
            'compensation_scheme' => null,
            'compensation_cap'   => null,
            'complaint_process'  => "The Financial Industry Disputes Resolution Centre (FIDREC) is Singapore's independent dispute-resolution body for retail financial disputes, including against CMS licensees. We couldn't confirm FIDREC's specific claim limits or process details for a forex/CFD dispute from public guidance, so check directly with FIDREC or MAS before assuming a specific compensation amount. No MAS-administered investor compensation scheme specific to CFD/forex broker insolvency was confirmed -- its framework centres on licensing conditions, segregated client accounts, and a mandatory Customer Knowledge Assessment rather than a standing compensation fund.",
            'register_url'       => 'https://eservices.mas.gov.sg/fid',
            'register_label'     => "the MAS Financial Institutions Directory (FID)",
            'broker_field'        => null,
            'tracks_licence_numbers' => false,
            'related_regulation_slug' => null,
            'related_learn_slug' => 'forex-broker-regulation-explained',
            'related_best_slug'  => null,
        ),
        'cma-kenya' => array(
            'name'               => 'CMA (Kenya)',
            'full_name'          => "Capital Markets Authority of Kenya",
            'jurisdiction'       => 'Kenya -- licenses "non-dealing" online forex brokers, "dealing" forex brokers (market makers), and forex money managers as separate categories, each with its own minimum-capital requirement',
            'leverage_note'      => "Commonly reported at a maximum of 1:400, repeated consistently across industry sources but not confirmed against the CMA's own published regulations directly.",
            'compensation_scheme' => null,
            'compensation_cap'   => null,
            'complaint_process'  => "A client reports a dispute to the CMA directly (and to the police for suspected fraud). Whether Kenya's Investor Compensation Fund (ICF) -- designed for failures of licensed stockbrokers and dealers -- extends to online forex broker clients specifically is something our sources genuinely disagreed on, so this isn't presented as a confirmed safety net either way.",
            'register_url'       => 'https://www.cma.or.ke/',
            'register_label'     => "the CMA's published list of licensed online forex brokers, dealers, and money managers",
            'broker_field'        => null,
            'tracks_licence_numbers' => false,
            'related_regulation_slug' => null,
            'related_learn_slug' => 'forex-broker-regulation-explained',
            'related_best_slug'  => null,
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
        'dfsa'           => in_array( 'DFSA', $broker['other_reg'] ?? array(), true ),
        'fma-nz'         => in_array( 'FMA', $broker['other_reg'] ?? array(), true ),
        'mas'            => in_array( 'MAS', $broker['other_reg'] ?? array(), true ),
        'cma-kenya'      => in_array( 'CMA', $broker['other_reg'] ?? array(), true ),
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
        $all_entities = globalfxhub_get_regulator_entities();
        $names = implode( ', ', array_column( $all_entities, 'name' ) );
        $desc = 'Which of our 139 researched forex/CFD brokers currently hold a licence from each of these ' . count( $all_entities ) . ' regulators -- ' . $names . ' -- with jurisdiction, compensation cover, and the official register for each.';
        echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
    }
}
add_action( 'wp_head', 'globalfxhub_regulator_entity_seo_head', 5 );
