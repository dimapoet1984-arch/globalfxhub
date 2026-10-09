<?php
/**
 * Email capture tied to the Broker Change Log (inc/broker-changelog.php):
 * a trader can subscribe on /broker-changelog/ to get a digest email
 * only when this site's own published broker data actually changes --
 * reusing the change log's own real diff detection rather than a
 * generic "subscribe to our newsletter" box with nothing specific
 * behind it.
 *
 * Deliberately single opt-in (no confirmation-email round trip) to keep
 * this to a first, honestly-scoped iteration -- every digest email
 * carries a one-click unsubscribe link, and the signup form on
 * page-broker-changelog.php says plainly what a subscriber is signing
 * up for. Subscribers are stored in wp_options, the same low-overhead
 * persistence the change log itself already uses -- no new database
 * table, no third-party email service.
 *
 * Sending happens synchronously, inline with whichever page load first
 * detects a real data change (globalfxhub_broker_changelog_sync()'s
 * 'globalfxhub_broker_changelog_new_entries' action below) -- a known,
 * disclosed simplification: on a large subscriber list this would be
 * better moved to WP-Cron/a queue, but a genuine change-log update is
 * an occasional event, not a high-frequency one, so that complexity
 * isn't justified yet.
 *
 * Form submission handling itself lives in page-broker-changelog.php,
 * the same place every other page in this theme reads its own $_GET
 * (page-reviews.php's ?search=, page-countries.php's ?country=) rather
 * than a separate admin-post handler -- this file only holds the data
 * and email logic, not request handling.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBALFXHUB_CHANGELOG_SUBSCRIBERS_OPTION', 'globalfxhub_changelog_subscribers' );

function globalfxhub_changelog_subscribers() {
    $subscribers = get_option( GLOBALFXHUB_CHANGELOG_SUBSCRIBERS_OPTION, array() );
    return is_array( $subscribers ) ? $subscribers : array();
}

/**
 * Adds an email, deduped case-insensitively, with its own random
 * unsubscribe token. Returns a status string -- 'subscribed',
 * 'already_subscribed', or 'invalid' -- rather than a bare boolean, so
 * the form can show the right message without guessing why.
 */
function globalfxhub_changelog_subscribe( $email ) {
    $email = sanitize_email( (string) $email );
    if ( ! $email || ! is_email( $email ) ) {
        return 'invalid';
    }
    $subscribers = globalfxhub_changelog_subscribers();
    foreach ( $subscribers as $s ) {
        if ( strtolower( $s['email'] ) === strtolower( $email ) ) {
            return 'already_subscribed';
        }
    }
    $subscribers[] = array(
        'email'         => $email,
        'token'         => wp_generate_password( 32, false ),
        'subscribed_at' => function_exists( 'date_i18n' ) ? date_i18n( 'j F Y' ) : date( 'j F Y' ),
    );
    update_option( GLOBALFXHUB_CHANGELOG_SUBSCRIBERS_OPTION, $subscribers, false );
    return 'subscribed';
}

/**
 * Removes a subscriber by their own unique unsubscribe token. An
 * unknown/already-used token is a silent no-op -- never an error
 * message, which would confirm or deny whether a given token or email
 * exists in the list.
 */
function globalfxhub_changelog_unsubscribe( $token ) {
    $token = (string) $token;
    if ( '' === $token ) {
        return false;
    }
    $subscribers = globalfxhub_changelog_subscribers();
    $remaining = array_values( array_filter( $subscribers, function( $s ) use ( $token ) {
        return $s['token'] !== $token;
    } ) );
    if ( count( $remaining ) === count( $subscribers ) ) {
        return false;
    }
    update_option( GLOBALFXHUB_CHANGELOG_SUBSCRIBERS_OPTION, $remaining, false );
    return true;
}

/**
 * Builds the digest email body for one run's worth of genuine changes
 * ($entries is the exact array the 'globalfxhub_broker_changelog_new_
 * entries' action below receives -- real field diffs only, see
 * globalfxhub_broker_changelog_sync()). Reuses the same broker name and
 * review-URL every other page already shows rather than writing
 * separate email-only copy.
 */
function globalfxhub_changelog_digest_body( $entries, $unsubscribe_url ) {
    $count = count( $entries );
    $lines = array();
    $lines[] = $count . ' broker data update' . ( 1 === $count ? '' : 's' ) . ' on GlobalFXHub:';
    $lines[] = '';
    foreach ( $entries as $entry ) {
        $lines[] = '- ' . $entry['broker_name'] . ': ' . $entry['summary'];
        $lines[] = '  ' . $entry['review_url'];
    }
    $lines[] = '';
    $lines[] = 'See the full change log: ' . home_url( '/broker-changelog/' );
    $lines[] = '';
    $lines[] = 'You are getting this because you subscribed to broker data change alerts on GlobalFXHub. Unsubscribe any time: ' . $unsubscribe_url;
    return implode( "\n", $lines );
}

/**
 * Fired by globalfxhub_broker_changelog_sync() exactly once per run
 * that detects at least one genuine field change -- never for a
 * baseline-only run. Sends one email per current subscriber, each with
 * their own unsubscribe link, rather than a single BCC blast.
 */
function globalfxhub_notify_changelog_subscribers( $entries ) {
    if ( empty( $entries ) ) {
        return;
    }
    $subscribers = globalfxhub_changelog_subscribers();
    if ( empty( $subscribers ) ) {
        return;
    }
    $count = count( $entries );
    $subject = $count . ' broker data update' . ( 1 === $count ? '' : 's' ) . ' on GlobalFXHub';
    foreach ( $subscribers as $subscriber ) {
        $unsubscribe_url = home_url( '/broker-changelog/?unsubscribe=' . $subscriber['token'] );
        wp_mail( $subscriber['email'], $subject, globalfxhub_changelog_digest_body( $entries, $unsubscribe_url ) );
    }
}
add_action( 'globalfxhub_broker_changelog_new_entries', 'globalfxhub_notify_changelog_subscribers' );
