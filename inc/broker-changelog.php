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
 * Maps each tracked field to its translatable-string key (in
 * inc/translatable-strings.php), used only to render an entry's
 * summary in the current viewer's language -- globalfxhub_changelog_
 * tracked_fields()'s own English labels above stay as the write-time
 * fallback baked into 'summary' for entries that predate 'old'/'new'
 * being persisted (see globalfxhub_changelog_localized_summary()).
 */
function globalfxhub_changelog_field_translation_keys() {
    return array(
        'spread_eurusd'       => 'cl_field_spread_eurusd',
        'min_deposit_display' => 'cl_field_min_deposit_display',
        'platforms'           => 'cl_field_platforms',
        'fca'                 => 'cl_field_fca',
        'cysec'               => 'cl_field_cysec',
        'seychelles'          => 'cl_field_seychelles',
        'other_reg'           => 'cl_field_other_reg',
        'entity'              => 'cl_field_entity',
    );
}

/**
 * Same job as globalfxhub_changelog_format_value() above, but for
 * display: translates the "not on file"/"none on file" fallbacks.
 * Platform names, regulator codes, licence numbers etc. are left as
 * literal data either way -- same reasoning as leaving entity/regulator
 * names untranslated elsewhere on this site.
 */
function globalfxhub_changelog_format_value_t( $value ) {
    if ( null === $value || '' === $value ) {
        return globalfxhub_t( 'cl_value_not_on_file' );
    }
    if ( is_array( $value ) ) {
        return empty( $value ) ? globalfxhub_t( 'cl_value_none_on_file' ) : implode( ', ', $value );
    }
    return (string) $value;
}

/**
 * Reconstructs an entry's summary in the current viewer's language from
 * its stored 'field'/'old'/'new' rather than echoing the English
 * sentence baked into 'summary' at write time. Falls back to that
 * stored 'summary' for a baseline entry's unrecognized-field case, and
 * for any entry written before 'old'/'new' were persisted (there's
 * nothing to reconstruct from, so the honest fallback is the English
 * text actually recorded, not a guess).
 */
function globalfxhub_changelog_localized_summary( $entry ) {
    if ( null === ( $entry['field'] ?? null ) ) {
        return globalfxhub_t( 'cl_baseline_summary' );
    }
    $field_keys = globalfxhub_changelog_field_translation_keys();
    if ( ! array_key_exists( 'old', $entry ) || ! array_key_exists( 'new', $entry ) || ! isset( $field_keys[ $entry['field'] ] ) ) {
        return $entry['summary'];
    }
    return sprintf(
        globalfxhub_t( 'cl_diff_template' ),
        globalfxhub_t( $field_keys[ $entry['field'] ] ),
        globalfxhub_changelog_format_value_t( $entry['old'] ),
        globalfxhub_changelog_format_value_t( $entry['new'] )
    );
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

    // Stored alongside the already-localized 'date' below purely as a
    // write-time fallback: a language-neutral ISO date lets render time
    // reformat the date in the CURRENT viewer's language/locale via
    // date_i18n() -- the same pattern used everywhere else on this site
    // (broker news, regulator pages, ...) -- rather than baking in
    // whichever language happened to be active on the page load that
    // first detected this change.
    $today_raw = date( 'Y-m-d' );
    $today = function_exists( 'date_i18n' ) ? date_i18n( 'j F Y' ) : date( 'j F Y' );
    $labels = globalfxhub_changelog_tracked_fields();
    $changed_anything = false;
    // Genuine field-diff entries detected THIS run only -- deliberately
    // excludes the "Baseline recorded" case below, since a broker simply
    // entering tracking for the first time isn't a change a subscriber
    // asked to hear about. Handed to the
    // 'globalfxhub_broker_changelog_new_entries' action at the end of
    // this function (see inc/changelog-subscribers.php), which is what
    // actually emails anyone subscribed to change alerts.
    $run_entries_for_notification = array();

    foreach ( globalfxhub_get_brokers() as $broker ) {
        $slug = $broker['slug'];
        $new_snapshot = globalfxhub_broker_changelog_snapshot( $broker );

        if ( ! isset( $snapshots[ $slug ] ) ) {
            if ( ! isset( $changelog[ $slug ] ) ) {
                $changelog[ $slug ] = array();
            }
            $changelog[ $slug ][] = array(
                'date'     => $today,
                'date_raw' => $today_raw,
                'field'    => null,
                'summary'  => 'Baseline recorded -- change tracking starts here. No prior change history is available for this broker.',
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
                'date'     => $today,
                'date_raw' => $today_raw,
                'field'    => $field,
                'old'      => $old_value,
                'new'      => $new_value,
                'summary'  => globalfxhub_changelog_diff_summary( $label, $old_value, $new_value ),
            );
        }

        if ( ! empty( $new_entries ) ) {
            if ( ! isset( $changelog[ $slug ] ) ) {
                $changelog[ $slug ] = array();
            }
            foreach ( $new_entries as $entry ) {
                $changelog[ $slug ][] = $entry;
                $run_entries_for_notification[] = array(
                    'broker_name' => $broker['name'],
                    'review_url'  => globalfxhub_track_b_url( 'reviews/' . $slug . '/' ),
                    'summary'     => $entry['summary'],
                );
            }
            $snapshots[ $slug ] = $new_snapshot;
            $changed_anything = true;
        }
    }

    if ( $changed_anything ) {
        update_option( GLOBALFXHUB_BROKER_SNAPSHOT_OPTION, $snapshots, false );
        update_option( GLOBALFXHUB_BROKER_CHANGELOG_OPTION, $changelog, false );
    }

    if ( ! empty( $run_entries_for_notification ) ) {
        do_action( 'globalfxhub_broker_changelog_new_entries', $run_entries_for_notification );
    }
}
add_action( 'after_setup_theme', 'globalfxhub_broker_changelog_sync' );

/**
 * Newest first -- the order a reader (or a search engine) actually
 * wants: what changed most recently.
 */
function globalfxhub_get_broker_changelog( $slug ) {
    $changelog = get_option( GLOBALFXHUB_BROKER_CHANGELOG_OPTION, array() );
    $entries = array_reverse( $changelog[ $slug ] ?? array() );
    return array_map( function( $entry ) {
        return array(
            'date'    => ! empty( $entry['date_raw'] ) ? date_i18n( 'j F Y', strtotime( $entry['date_raw'] ) ) : $entry['date'],
            'summary' => globalfxhub_changelog_localized_summary( $entry ),
        );
    }, $entries );
}

function globalfxhub_broker_changelog_payload() {
    $out = array();
    foreach ( globalfxhub_get_brokers() as $b ) {
        $out[] = array(
            'slug'       => $b['slug'],
            'name'       => $b['name'],
            'entries'    => globalfxhub_get_broker_changelog( $b['slug'] ),
            'review_url' => globalfxhub_track_b_url( 'reviews/' . $b['slug'] . '/' ),
        );
    }
    return $out;
}

function globalfxhub_ensure_broker_changelog_page() {
    globalfxhub_ensure_templated_page( 'broker-changelog', 'Broker Change Log', 'page-broker-changelog.php' );
}
add_action( 'after_setup_theme', 'globalfxhub_ensure_broker_changelog_page' );
