<?php
/**
 * Data Confidence: a per-field confidence rating (HIGH/MEDIUM/LOW/
 * UNRATED) plus source/source_type/last_verified/verified_by,
 * computed from the EXISTING broker data rather than stored as a new
 * array key on each of the 139 broker records.
 *
 * Deliberately additive and non-breaking: nothing here changes the
 * shape of globalfxhub_get_brokers()'s array or anything that reads
 * it today. globalfxhub_broker_field_confidence() is a pure function
 * -- same philosophy as globalfxhub_broker_scores() computing scores
 * fresh from the raw fields rather than hardcoding them, rather than
 * a one-time migration that would need hand-editing all 139 broker
 * entries (556+ fields across the 4 text fields alone) with real
 * risk of transcription error and no way to keep it in sync as those
 * fields are edited going forward.
 *
 * The classification signal comes from the hedge language this
 * dataset is ALREADY written in throughout -- "multiple independent
 * sources confirm", "not independently confirmed", "sources
 * disagree", "confirmed via [broker]'s own published page", etc. --
 * rather than fresh verification this environment can't perform
 * (network policy blocks live broker-site fetches, same constraint
 * noted throughout this project). This is a real limitation worth
 * stating plainly: a field with none of these phrases returns
 * 'UNRATED' rather than a guessed middle tier, and the two numeric
 * pricing fields (spread_eurusd, min_deposit_usd) currently have no
 * broker-specific verification date to draw on at all -- every one
 * of the 139 brokers relies on the same blanket, already-known-stale
 * "March 2026" pricing_checked fallback (globalfxhub_broker_review_dates()),
 * confirmed via grep: zero brokers currently set pricing_checked_override.
 * Rating those fields MEDIUM across the board is the honest answer,
 * not a limitation of the classifier -- it surfaces the exact
 * staleness problem the 2026-11-09 maintenance audit already flagged,
 * rather than hiding it behind a page that looks individually
 * verified when it isn't.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Fields this system currently rates. Text fields are classified from
 * their own hedge language; the two numeric pricing fields use the
 * blanket-date rule above instead. Deliberately a fixed, named list
 * rather than "every field" -- these are the fields this site's own
 * editorial content (comparisons, broker-research articles) already
 * treats as sourced facts worth citing a number from, so they're the
 * ones where a reader-facing confidence rating earns its keep first.
 */
function globalfxhub_data_confidence_fields() {
    return array(
        'spread_eurusd'          => 'numeric_blanket_date',
        'min_deposit_usd'        => 'numeric_blanket_date',
        'inactivity_fee'         => 'text',
        'overnight_financing'    => 'text',
        'currency_conversion_fee' => 'text',
        'execution_model'        => 'text',
    );
}

/**
 * LOW first, then HIGH, then MEDIUM -- checked in that order because
 * a sentence can contain both a confirmation phrase AND a hedge about
 * a different detail in the same field (e.g. "confirmed via X's own
 * page... though the exact figure for day traders wasn't verified"),
 * and the hedge is the one that should win: this site's whole stated
 * editorial standard is to flag the weakest link in a claim, not the
 * strongest-sounding phrase in it.
 */
function globalfxhub_data_confidence_low_patterns() {
    return array(
        '/sources?\s+disagree/i',
        '/\bnot\s+(independently\s+)?confirmed\b/i',
        '/(?:wasn|weren)\'?t\s+(?:independently\s+)?(?:cross-)?(?:verified|confirmed|corroborated|detailed|found|located|retrievable|retrieved|disclosed|resolved)/i',
        '/\bunconfirmed\b/i',
        '/\bconflict(s|ing)?\b/i',
        '/single[\s-]source/i',
        '/lower[\s-]confidence/i',
        '/treat\s+(this|with\s+caution|as\s+(unconfirmed|indicative))/i',
        '/\bnot\s+(specifically\s+|clearly\s+|explicitly\s+)?(found|confirmed|quantified|located|verified|traceable|disclosed)\b/i',
        '/no\s+[a-z\s-]*?\b(was|were)\b\s+(found|located|verified|confirmed|traced|disclosed)/i',
        '/not\s+independently\s+verified/i',
        '/could\s+not\s+be\s+(?:independently\s+)?(?:confirmed|verified|traced|located)/i',
        '/should\s+(?:be\s+)?reconfirmed/i',
        '/needs?\s+a\s+direct\s+check/i',
        '/\bno\s+sources?\s+(confirms?|described|explicitly|stated)/i',
        '/verify\s+directly/i',
    );
}

function globalfxhub_data_confidence_high_patterns() {
    return array(
        '/confirmed\s+via\s+[^.]*?\'s?\s+own\s+[a-z\s&-]*?(published|disclosures|page|policy|schedule|methodology|terms|agreement)/i',
        '/per\s+[^.]*?\'s?\s+own\s+[a-z\s&-]*?(published|disclosures|page|policy|schedule|methodology|terms|agreement|client agreement)/i',
        '/own\s+published\s+[a-z\s-]*(page|policy|schedule|methodology|terms|agreement)/i',
    );
}

function globalfxhub_data_confidence_medium_patterns() {
    return array(
        '/multiple\s+independent\s+sources/i',
        '/consistently\s+corroborated/i',
        '/confirmed\s+by\s+multiple/i',
    );
}

/**
 * Classifies a single free-text field's confidence from its own
 * hedge language. Returns null for an empty/missing field (nothing
 * to rate) -- never a guessed tier for absent data.
 */
function globalfxhub_classify_text_confidence( $text ) {
    if ( empty( $text ) || ! is_string( $text ) ) {
        return null;
    }

    foreach ( globalfxhub_data_confidence_low_patterns() as $pattern ) {
        if ( preg_match( $pattern, $text ) ) {
            return array(
                'confidence'  => 'LOW',
                'source_type' => 'unconfirmed',
                'source'      => 'Third-party reporting with an explicit, unresolved gap or conflict in our research.',
            );
        }
    }

    foreach ( globalfxhub_data_confidence_high_patterns() as $pattern ) {
        if ( preg_match( $pattern, $text ) ) {
            return array(
                'confidence'  => 'HIGH',
                'source_type' => 'broker_official',
                'source'      => "Confirmed directly against the broker's own published disclosure.",
            );
        }
    }

    foreach ( globalfxhub_data_confidence_medium_patterns() as $pattern ) {
        if ( preg_match( $pattern, $text ) ) {
            return array(
                'confidence'  => 'MEDIUM',
                'source_type' => 'third_party_multi',
                'source'      => 'Corroborated across multiple independent third-party sources.',
            );
        }
    }

    // No explicit hedge or confirmation phrase found -- flagged for
    // manual review rather than defaulting to a false middle ground.
    return array(
        'confidence'  => 'UNRATED',
        'source_type' => 'unclassified',
        'source'      => null,
    );
}

/**
 * Returns this broker's confidence rating for one field, or null if
 * the field has no value at all (nothing to rate) or isn't one of
 * globalfxhub_data_confidence_fields(). See this file's header for
 * why the two numeric pricing fields use a different, blanket-date
 * rule instead of text classification.
 */
function globalfxhub_broker_field_confidence( $broker, $field ) {
    $fields = globalfxhub_data_confidence_fields();
    if ( ! isset( $fields[ $field ] ) ) {
        return null;
    }
    $value = $broker[ $field ] ?? null;
    if ( null === $value || '' === $value ) {
        return null;
    }

    $dates = globalfxhub_broker_review_dates( $broker );

    if ( 'numeric_blanket_date' === $fields[ $field ] ) {
        return array(
            'confidence'    => 'MEDIUM',
            'source_type'   => 'aggregated_third_party',
            'source'        => "Cross-referenced from publicly available broker fee schedules and third-party comparison data -- not individually re-verified against this broker's current live pricing page.",
            'last_verified' => $dates['pricing_checked'],
            'verified_by'   => 'GlobalFXHub Research Team',
        );
    }

    $result = globalfxhub_classify_text_confidence( $value );
    if ( null === $result ) {
        return null;
    }
    $result['last_verified'] = $dates['data_reviewed'];
    $result['verified_by']   = 'UNRATED' === $result['confidence'] ? null : 'GlobalFXHub Research Team';
    return $result;
}

/**
 * Every rated field for one broker, keyed by field name -- the
 * shape a review page would actually loop over to render a confidence
 * badge per row. Skips any field with no value (null entries), same
 * as globalfxhub_broker_field_confidence() itself.
 */
function globalfxhub_broker_data_confidence_report( $broker ) {
    $report = array();
    foreach ( array_keys( globalfxhub_data_confidence_fields() ) as $field ) {
        $rating = globalfxhub_broker_field_confidence( $broker, $field );
        if ( null !== $rating ) {
            $report[ $field ] = $rating;
        }
    }
    return $report;
}

/**
 * Small inline badge for a rated field, meant to sit right after the
 * value it describes in a fact table. Returns '' for a field with no
 * rating at all (UNRATED or null) rather than cluttering every row --
 * UNRATED means "flagged for manual review", not "known weak", so it
 * isn't shown as a reader-facing claim about the data.
 */
function globalfxhub_data_confidence_badge_html( $broker, $field ) {
    $rating = globalfxhub_broker_field_confidence( $broker, $field );
    if ( null === $rating || 'UNRATED' === $rating['confidence'] ) {
        return '';
    }
    $labels = array(
        'HIGH'   => 'High confidence',
        'MEDIUM' => 'Medium confidence',
        'LOW'    => 'Low confidence',
    );
    $title = $rating['source'];
    if ( ! empty( $rating['last_verified'] ) ) {
        $title .= ' Last verified: ' . $rating['last_verified'] . '.';
    }
    return sprintf(
        ' <span class="confidence-badge confidence-badge--%s" title="%s">%s</span>',
        esc_attr( strtolower( $rating['confidence'] ) ),
        esc_attr( $title ),
        esc_html( $labels[ $rating['confidence'] ] )
    );
}
