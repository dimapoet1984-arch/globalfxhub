<?php
/**
 * REST fields for Polylang's TERM-level translation API -- the exact
 * parallel to inc/i18n-rest-connector.php's post-level gfh_lang/
 * gfh_translations, needed so Track A's translated posts can be
 * assigned to the correctly-translated category instead of falling
 * back to WordPress's default "Uncategorized" term.
 *
 * Confirmed live 2026-10-30: that fallback is exactly what's been
 * happening. i18n/translate.py never read or set a category at all,
 * and no REST-exposed way existed to look up or create a translated
 * category term even if it had -- so all 392 posts translate.py had
 * created so far landed in category id 1 (Uncategorized, en) instead
 * of their source post's real category (Learn/News/Candlestick
 * Patterns) translated into that post's own language.
 *
 * gfh_term_lang            get/set a term's Polylang language (slug).
 * gfh_term_translations    get/set the term's translation group (lang
 *                           slug => term ID), merged under the SAME
 *                           process-wide lock inc/i18n-rest-connector.php
 *                           uses for posts (see that file's own
 *                           comment for why a merge, not an overwrite,
 *                           matters: parallel per-language translate.py
 *                           runs must not each clobber the other's link).
 *
 * Registered on 'category' only -- the only taxonomy Track A content
 * (Learn/News/Candlestick Patterns posts) actually uses.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_i18n_term_rest_connector_active() {
    return function_exists( 'pll_get_term_language' )
        && function_exists( 'pll_set_term_language' )
        && function_exists( 'pll_get_term_translations' )
        && function_exists( 'pll_save_term_translations' );
}

function globalfxhub_register_i18n_term_rest_fields() {
    if ( ! globalfxhub_i18n_term_rest_connector_active() ) {
        return;
    }

    register_rest_field(
        'category',
        'gfh_term_lang',
        array(
            'get_callback'    => 'globalfxhub_rest_get_term_lang',
            'update_callback' => 'globalfxhub_rest_set_term_lang',
            'schema'          => array(
                'description' => "The category's Polylang language slug.",
                'type'        => 'string',
                'context'     => array( 'view', 'edit' ),
            ),
        )
    );

    register_rest_field(
        'category',
        'gfh_term_translations',
        array(
            'get_callback'    => 'globalfxhub_rest_get_term_translations',
            'update_callback' => 'globalfxhub_rest_set_term_translations',
            'schema'          => array(
                'description' => "The category's translation group, as {lang slug: term ID}.",
                'type'        => 'object',
                'context'     => array( 'view', 'edit' ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'globalfxhub_register_i18n_term_rest_fields' );

function globalfxhub_rest_get_term_lang( $object ) {
    $term_id = is_array( $object ) ? $object['id'] : $object->id;
    $lang = pll_get_term_language( $term_id, 'slug' );
    return $lang ? $lang : '';
}

function globalfxhub_rest_set_term_lang( $value, $object ) {
    if ( ! current_user_can( 'edit_term', $object->term_id ) ) {
        return new WP_Error( 'rest_forbidden', 'Sorry, you are not allowed to edit this term.', array( 'status' => 403 ) );
    }
    if ( ! is_string( $value ) || '' === $value ) {
        return new WP_Error( 'gfh_invalid_term_lang', 'gfh_term_lang must be a non-empty Polylang language slug.', array( 'status' => 400 ) );
    }
    pll_set_term_language( $object->term_id, $value );
    return true;
}

function globalfxhub_rest_get_term_translations( $object ) {
    $term_id = is_array( $object ) ? $object['id'] : $object->id;
    $translations = pll_get_term_translations( $term_id );
    return is_array( $translations ) ? $translations : array();
}

/**
 * Merges { lang: term_id } entries into the term's CURRENT translation
 * group under a process-wide lock, rather than replacing the group
 * outright -- same reasoning as globalfxhub_rest_set_translations()
 * in inc/i18n-rest-connector.php (see that function's own comment).
 * Reuses that exact same lock: it's a single global mutex already
 * proven live for the post-level version of this same race, and term
 * translations need the identical protection for the identical reason.
 */
function globalfxhub_rest_set_term_translations( $value, $object ) {
    if ( ! current_user_can( 'edit_term', $object->term_id ) ) {
        return new WP_Error( 'rest_forbidden', 'Sorry, you are not allowed to edit this term.', array( 'status' => 403 ) );
    }
    if ( ! is_array( $value ) ) {
        return new WP_Error( 'gfh_invalid_term_translations', 'gfh_term_translations must be an object of {lang: term_id}.', array( 'status' => 400 ) );
    }
    if ( ! function_exists( 'globalfxhub_acquire_i18n_sync_lock' ) ) {
        return new WP_Error( 'gfh_lock_unavailable', 'Translation lock is not available yet.', array( 'status' => 503 ) );
    }

    $attempts = 0;
    while ( ! globalfxhub_acquire_i18n_sync_lock() ) {
        $attempts++;
        if ( $attempts > 280 ) {
            return new WP_Error( 'gfh_lock_timeout', 'Could not acquire the i18n sync lock -- another request is holding it.', array( 'status' => 409 ) );
        }
        usleep( 250000 ); // 250ms
    }

    try {
        $current = pll_get_term_translations( $object->term_id );
        $current = is_array( $current ) ? $current : array();
        $merged  = array_merge( $current, array_map( 'intval', $value ) );
        pll_save_term_translations( $merged );
    } finally {
        globalfxhub_release_i18n_sync_lock();
    }

    return true;
}
