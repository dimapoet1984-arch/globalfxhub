<?php
/**
 * Storage and lookup for Track B translations -- the ~900 virtual,
 * PHP-array-driven URLs (broker reviews, countries, best-lists,
 * regulation pages, compare pages, broker-news items) that have no
 * WP post behind them at all, so Polylang's post-duplication model
 * can't apply. See docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 3.
 *
 * globalfxhub_tf( $type, $id, $field, $english_value, $lang = null )
 *   mirrors globalfxhub_t()'s fallback contract (inc/translatable-strings.php):
 *   returns the stored translation for the current (or given)
 *   language if one exists, otherwise $english_value unchanged. Safe
 *   to call unconditionally from every Track B template, exactly like
 *   globalfxhub_t() already is for UI strings -- it never errors just
 *   because a translation doesn't exist yet.
 *
 * globalfxhub_tf_set( $type, $id, $field, $lang, $value )
 *   upserts one row. Used by the Track B write endpoint below, which
 *   is what i18n/translate_track_b.py (to be built) calls over REST.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'GLOBALFXHUB_TRACK_B_SCHEMA_VERSION', '2026-10-29-1' );

function globalfxhub_track_b_table_name() {
    global $wpdb;
    return $wpdb->prefix . 'globalfxhub_translations';
}

/**
 * Self-healing schema, same pattern as this theme's templated-page
 * provisioning and GLOBALFXHUB_REWRITE_RULES_VERSION: cheap no-op
 * after the first successful run, re-runs automatically if the
 * version constant above is ever bumped for a schema change.
 */
function globalfxhub_maybe_create_track_b_table() {
    if ( get_option( 'globalfxhub_track_b_schema_version' ) === GLOBALFXHUB_TRACK_B_SCHEMA_VERSION ) {
        return;
    }

    global $wpdb;
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $table_name      = globalfxhub_track_b_table_name();
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE {$table_name} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        content_type VARCHAR(40) NOT NULL,
        content_id VARCHAR(80) NOT NULL,
        field VARCHAR(60) NOT NULL,
        lang VARCHAR(10) NOT NULL,
        value LONGTEXT NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY  (id),
        UNIQUE KEY content_field_lang (content_type, content_id, field, lang),
        KEY content_lookup (content_type, content_id, lang)
    ) {$charset_collate};";

    dbDelta( $sql );

    update_option( 'globalfxhub_track_b_schema_version', GLOBALFXHUB_TRACK_B_SCHEMA_VERSION );
}
add_action( 'init', 'globalfxhub_maybe_create_track_b_table', 5 );

/**
 * Same fallback contract as globalfxhub_t(): never errors, never
 * returns an empty/missing value -- falls back to the English source
 * whenever no translation row exists (not translated yet, Polylang
 * inactive, or the current language IS English).
 */
function globalfxhub_tf( $content_type, $content_id, $field, $english_value, $lang = null ) {
    if ( null === $lang ) {
        $lang = function_exists( 'pll_current_language' ) ? pll_current_language() : 'en';
    }
    if ( ! $lang || 'en' === $lang ) {
        return $english_value;
    }

    global $wpdb;
    $table = globalfxhub_track_b_table_name();
    $value = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT value FROM {$table} WHERE content_type = %s AND content_id = %s AND field = %s AND lang = %s",
            $content_type,
            $content_id,
            $field,
            $lang
        )
    );

    return ( null !== $value && '' !== $value ) ? $value : $english_value;
}

/**
 * Echoes the escaped translation -- the globalfxhub_te() counterpart
 * to globalfxhub_tf(), for templates printing straight into HTML.
 */
function globalfxhub_tfe( $content_type, $content_id, $field, $english_value, $lang = null ) {
    echo esc_html( globalfxhub_tf( $content_type, $content_id, $field, $english_value, $lang ) );
}

/**
 * Upserts one translation row. $value may be deleted (passed as an
 * empty string) to intentionally clear a bad translation back to the
 * English fallback -- an empty value is treated as "not translated"
 * by globalfxhub_tf() above, so writing '' here and relying on that
 * fallback is the supported way to revert one field.
 */
function globalfxhub_tf_set( $content_type, $content_id, $field, $lang, $value ) {
    global $wpdb;
    $table = globalfxhub_track_b_table_name();

    return false !== $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO {$table} (content_type, content_id, field, lang, value, updated_at)
             VALUES (%s, %s, %s, %s, %s, %s)
             ON DUPLICATE KEY UPDATE value = VALUES(value), updated_at = VALUES(updated_at)",
            $content_type,
            $content_id,
            $field,
            $lang,
            $value,
            current_time( 'mysql' )
        )
    );
}

/**
 * The pipeline's write path (i18n/translate_track_b.py). There's no
 * WP post for register_rest_field() to attach to -- see
 * docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 3 -- so this is a
 * small dedicated route instead, authenticated the same way as every
 * other REST write this theme does (WordPress Application Passwords
 * via the standard REST cookie/Basic-auth stack), gated on the same
 * capability a page edit would require.
 *
 * POST /wp-json/globalfxhub/v1/track-b-translation
 * Body: { "content_type": "broker", "content_id": "ig-group", "field": "review_summary", "lang": "es", "value": "..." }
 */
function globalfxhub_register_track_b_rest_routes() {
    register_rest_route(
        'globalfxhub/v1',
        '/track-b-translation',
        array(
            'methods'             => 'POST',
            'callback'            => 'globalfxhub_rest_set_track_b_translation',
            'permission_callback' => function () {
                return current_user_can( 'edit_pages' );
            },
            'args'                => array(
                'content_type' => array( 'required' => true, 'type' => 'string' ),
                'content_id'   => array( 'required' => true, 'type' => 'string' ),
                'field'        => array( 'required' => true, 'type' => 'string' ),
                'lang'         => array( 'required' => true, 'type' => 'string' ),
                'value'        => array( 'required' => true, 'type' => 'string' ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'globalfxhub_register_track_b_rest_routes' );

function globalfxhub_rest_set_track_b_translation( WP_REST_Request $request ) {
    $ok = globalfxhub_tf_set(
        $request->get_param( 'content_type' ),
        $request->get_param( 'content_id' ),
        $request->get_param( 'field' ),
        $request->get_param( 'lang' ),
        $request->get_param( 'value' )
    );

    if ( ! $ok ) {
        return new WP_Error( 'gfh_track_b_write_failed', 'Could not write the translation row.', array( 'status' => 500 ) );
    }

    return array( 'success' => true );
}
