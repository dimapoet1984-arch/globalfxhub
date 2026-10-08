<?php
/**
 * Broker Change Log: a genuine, dated record of when this site's own
 * published broker data actually changed -- not a reconstructed history
 * of what a broker itself did in the past.
 *
 * This is a meaningfully different kind of claim than anything else this
 * research model produces. Every other tool on this site restates a
 * CURRENT fact with a disclosed "last reviewed" date (e.g. "CySEC No.
 * 388/20, regulation last reviewed 8 October 2026"). A change log
 * instead asserts that something was DIFFERENT before a specific date --
 * "EUR/USD spread changed from 1.2 to 1.1 pips on 8 October 2026" -- and
 * that claim is only true if there's an actual prior snapshot on record
 * to compare against. There is no such historical record for any broker
 * before this mechanism first runs: this session's research produced one
 * dated snapshot, not a time series. So this intentionally does NOT
 * fabricate a plausible-looking log of past changes (no invented "April
 * -- spread changed" entries) -- that would be exactly the kind of
 * unverifiable, made-up-looking-precise content this site has avoided
 * everywhere else.
 *
 * What it does instead: on every page load (globalfxhub_get_brokers()'s
 * current, hardcoded values) it diffs against the last snapshot stored
 * in wp_options and, if nothing in a tracked field differs, does
 * nothing. The first time any broker is seen, it gets exactly one
 * honest "Baseline recorded" entry -- no claim about what came before.
 * From that point on, whenever this theme's own broker data is actually
 * edited and deployed (a corrected spread, a new licence number, a
 * platform added or dropped), the next page load detects the real diff
 * and appends a dated, specific entry automatically. That's the
 * mechanism that makes future entries genuinely original and citable:
 * they're generated from an actual before/after diff of published data,
 * not written to sound plausible.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBALFXHUB_BROKER_CHANGELOG_OPTION', 'globalfxhub_broker_changelog' );
define( 'GLOBALFXHUB_BROKER_SNAPSHOT_OPTION', 'globalfxhub_broker_snapshots' );

/**
 * The fields worth a trader's attention when they change: pricing,
 * platform availability, and every regulatory identifier also shown on
 * the review page and the Regulation Checker. Deliberately narrower than
 * every field in the broker array -- editorial prose fields (blurb,
 * account_types, etc.) change wording often without the underlying fact
 * changing, which would make the log noisy rather than useful.
 */
function globalfxhub_changelog_tracked_fields() {
    return array(
        'spread_eurusd'       => 'EUR/USD advertised spread',
        'min_deposit_display' => 'Minimum deposit',
        'platforms'           => 'Trading platforms',
        'fca'                 => 'FCA licence number',
        'cysec'               => 'CySEC licence number',
        'seychelles'          => 'FSA Seychelles licence number',
        'other_reg'           => 'Other regulators on file',
        'entity'              => 'Regulated entity name',
    );
}

function globalfxhub_broker_changelog_snapshot( $broker ) {
    $snapshot = array();
    foreach ( array_keys( globalfxhub_changelog_tracked_fields() ) as $field ) {
        $value = $broker[ $field ] ?? null;
        if ( is_array( $value ) ) {
            sort( $value );
        }
        $snapshot[ $field ] = $value;
    }
    return $snapshot;
}

function globalfxhub_changelog_format_value( $value ) {
    if ( null === $value || '' === $value ) {
        return 'not on file';
    }
    if ( is_array( $value ) ) {
        return empty( $value ) ? 'none on file' : implode( ', ', $value );
    }
    return (string) $value;
}

function globalfxhub_changelog_diff_summary( $label, $old_value, $new_value ) {
    return $label . ' changed from ' . globalfxhub_changelog_format_value( $old_value ) . ' to ' . globalfxhub_changelog_format_value( $new_value ) . '.';
}

/**
 * Diffs the current dataset against the last stored snapshot and
 * records any real difference, per broker, per tracked field -- one
 * entry per field per run, dated today, never backdated. A broker seen
 * for the first time gets a single honest baseline entry instead of any
 * diff (there is nothing to diff against). Writes to wp_options only
 * when something actually changed, same idle-cost-free pattern as the
 * rest of this theme's self-healing provisioning.
 */
function globalfxhub_broker_changelog_sync() {
    $snapshots = get_option( GLOBALFXHUB_BROKER_SNAPSHOT_OPTION, array() );
    $changelog = get_option( GLOBALFXHUB_BROKER_CHANGELOG_OPTION, array() );
    if ( ! is_array( $snapshots ) ) {
        $snapshots = array();
    }
    if ( ! is_array( $changelog ) ) {
        $changelog = array();
    }

    $today = function_exists( 'date_i18n' ) ? date_i18n( 'j F Y' ) : date( 'j F Y' );
    $labels = globalfxhub_changelog_tracked_fields();
    $changed_anything = false;

    foreach ( globalfxhub_get_brokers() as $broker ) {
        $slug = $broker['slug'];
        $new_snapshot = globalfxhub_broker_changelog_snapshot( $broker );

        if ( ! isset( $snapshots[ $slug ] ) ) {
            if ( ! isset( $changelog[ $slug ] ) ) {
                $changelog[ $slug ] = array();
            }
            $changelog[ $slug ][] = array(
                'date'    => $today,
                'field'   => null,
                'summary' => 'Baseline recorded -- change tracking starts here. No prior change history is available for this broker.',
            );
            $snapshots[ $slug ] = $new_snapshot;
            $changed_anything = true;
            continue;
        }

        $old_snapshot = $snapshots[ $slug ];
        $new_entries = array();
        foreach ( $labels as $field => $label ) {
            $old_value = $old_snapshot[ $field ] ?? null;
            $new_value = $new_snapshot[ $field ] ?? null;
            if ( $old_value === $new_value ) {
                continue;
            }
            $new_entries[] = array(
                'date'    => $today,
                'field'   => $field,
                'summary' => globalfxhub_changelog_diff_summary( $label, $old_value, $new_value ),
            );
        }

        if ( ! empty( $new_entries ) ) {
            if ( ! isset( $changelog[ $slug ] ) ) {
                $changelog[ $slug ] = array();
            }
            foreach ( $new_entries as $entry ) {
                $changelog[ $slug ][] = $entry;
            }
            $snapshots[ $slug ] = $new_snapshot;
            $changed_anything = true;
        }
    }

    if ( $changed_anything ) {
        update_option( GLOBALFXHUB_BROKER_SNAPSHOT_OPTION, $snapshots, false );
        update_option( GLOBALFXHUB_BROKER_CHANGELOG_OPTION, $changelog, false );
    }
}
add_action( 'after_setup_theme', 'globalfxhub_broker_changelog_sync' );

/**
 * Newest first -- the order a reader (or a search engine) actually
 * wants: what changed most recently.
 */
function globalfxhub_get_broker_changelog( $slug ) {
    $changelog = get_option( GLOBALFXHUB_BROKER_CHANGELOG_OPTION, array() );
    $entries = $changelog[ $slug ] ?? array();
    return array_reverse( $entries );
}

function globalfxhub_broker_changelog_payload() {
    $out = array();
    foreach ( globalfxhub_get_brokers() as $b ) {
        $out[] = array(
            'slug'       => $b['slug'],
            'name'       => $b['name'],
            'entries'    => globalfxhub_get_broker_changelog( $b['slug'] ),
            'review_url' => home_url( '/reviews/' . $b['slug'] . '/' ),
        );
    }
    return $out;
}

function globalfxhub_ensure_broker_changelog_page() {
    globalfxhub_ensure_templated_page( 'broker-changelog', 'Broker Change Log', 'page-broker-changelog.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_broker_changelog_page' );
