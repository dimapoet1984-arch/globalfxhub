<?php
/**
 * REST fields the translation pipeline needs for Track A content
 * (real WP posts/pages) that free Polylang doesn't expose on its own
 * -- see docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 2.1.
 *
 * gfh_lang           get/set a post's Polylang language (slug).
 * gfh_translations   get/set the post's translation group (lang slug
 *                     => post ID). Setting merges into the group's
 *                     CURRENT state under the same add_option() lock
 *                     globalfxhub_sync_templated_pages_across_languages()
 *                     uses, rather than overwriting from a snapshot --
 *                     the exact mistake the playbook's section 3.4
 *                     warns about (parallel language runs each
 *                     overwriting the other's link).
 * gfh_seo_title /
 * gfh_seo_desc       plain post meta the pipeline writes; nothing
 *                     reads these yet (no SEO plugin on this theme),
 *                     stored now so they're ready once an SEO layer
 *                     exists.
 *
 * All three register on both `post` and `page` since Track A content
 * (Learn/News/candlestick-patterns posts, the 11 container pages, the
 * 7 service pages) spans both post types.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_i18n_rest_connector_active() {
    return function_exists( 'pll_get_post_language' )
        && function_exists( 'pll_set_post_language' )
        && function_exists( 'pll_get_post_translations' )
        && function_exists( 'pll_save_post_translations' );
}

function globalfxhub_register_i18n_rest_fields() {
    if ( ! globalfxhub_i18n_rest_connector_active() ) {
        return;
    }

    foreach ( array( 'post', 'page' ) as $post_type ) {
        register_rest_field(
            $post_type,
            'gfh_lang',
            array(
                'get_callback'    => 'globalfxhub_rest_get_lang',
                'update_callback' => 'globalfxhub_rest_set_lang',
                'schema'          => array(
                    'description' => "The post's Polylang language slug.",
                    'type'        => 'string',
                    'context'     => array( 'view', 'edit' ),
                ),
            )
        );

        register_rest_field(
            $post_type,
            'gfh_translations',
            array(
                'get_callback'    => 'globalfxhub_rest_get_translations',
                'update_callback' => 'globalfxhub_rest_set_translations',
                'schema'          => array(
                    'description' => "The post's translation group, as {lang slug: post ID}.",
                    'type'        => 'object',
                    'context'     => array( 'view', 'edit' ),
                ),
            )
        );
    }
}
add_action( 'rest_api_init', 'globalfxhub_register_i18n_rest_fields' );

/**
 * register_meta()/register_post_meta() belong on 'init', not
 * 'rest_api_init' (WordPress's own recommendation) -- registered
 * once per post type here rather than with an empty-string post type,
 * which register_post_meta() doesn't treat as "every post type".
 */
function globalfxhub_register_i18n_seo_meta() {
    if ( ! globalfxhub_i18n_rest_connector_active() ) {
        return;
    }

    $auth_callback = function( $allowed, $meta_key, $post_id ) {
        return current_user_can( 'edit_post', $post_id );
    };

    foreach ( array( 'post', 'page' ) as $post_type ) {
        register_post_meta(
            $post_type,
            'gfh_seo_title',
            array(
                'single'        => true,
                'type'          => 'string',
                'show_in_rest'  => true,
                'auth_callback' => $auth_callback,
            )
        );
        register_post_meta(
            $post_type,
            'gfh_seo_desc',
            array(
                'single'        => true,
                'type'          => 'string',
                'show_in_rest'  => true,
                'auth_callback' => $auth_callback,
            )
        );
    }
}
add_action( 'init', 'globalfxhub_register_i18n_seo_meta', 30 );

function globalfxhub_clean_i18n_languages_cache() {
    if ( ! function_exists( 'PLL' ) ) {
        return;
    }
    $pll = PLL();
    if ( $pll && isset( $pll->model ) && $pll->model ) {
        $pll->model->clean_languages_cache();
    }
}

function globalfxhub_rest_get_lang( $object ) {
    $post_id = is_array( $object ) ? $object['id'] : $object->id;
    $lang = pll_get_post_language( $post_id, 'slug' );
    return $lang ? $lang : '';
}

function globalfxhub_rest_set_lang( $value, $object ) {
    if ( ! current_user_can( 'edit_post', $object->ID ) ) {
        return new WP_Error( 'rest_forbidden', 'Sorry, you are not allowed to edit this post.', array( 'status' => 403 ) );
    }
    if ( ! is_string( $value ) || '' === $value ) {
        return new WP_Error( 'gfh_invalid_lang', 'gfh_lang must be a non-empty Polylang language slug.', array( 'status' => 400 ) );
    }
    pll_set_post_language( $object->ID, $value );
    globalfxhub_clean_i18n_languages_cache();
    return true;
}

function globalfxhub_rest_get_translations( $object ) {
    $post_id = is_array( $object ) ? $object['id'] : $object->id;
    $translations = pll_get_post_translations( $post_id );
    return is_array( $translations ) ? $translations : array();
}

/**
 * Merges { lang: post_id } entries into the post's CURRENT translation
 * group under a process-wide lock, rather than replacing the group
 * outright -- see the file header and
 * docs/TRANSLATION_PIPELINE_ARCHITECTURE.md section 2.3. Reuses the
 * same add_option()-based lock
 * globalfxhub_sync_templated_pages_across_languages() already proved
 * live (functions.php): those functions are unconditionally defined
 * at theme load, and this callback only ever runs during an actual
 * REST request, well after functions.php has finished loading, so
 * they're always available here regardless of include order.
 */
function globalfxhub_rest_set_translations( $value, $object ) {
    if ( ! current_user_can( 'edit_post', $object->ID ) ) {
        return new WP_Error( 'rest_forbidden', 'Sorry, you are not allowed to edit this post.', array( 'status' => 403 ) );
    }
    if ( ! is_array( $value ) ) {
        return new WP_Error( 'gfh_invalid_translations', 'gfh_translations must be an object of {lang: post_id}.', array( 'status' => 400 ) );
    }
    if ( ! function_exists( 'globalfxhub_acquire_i18n_sync_lock' ) ) {
        return new WP_Error( 'gfh_lock_unavailable', 'Translation lock is not available yet.', array( 'status' => 503 ) );
    }

    $attempts = 0;
    while ( ! globalfxhub_acquire_i18n_sync_lock() ) {
        $attempts++;
        if ( $attempts > 20 ) {
            return new WP_Error( 'gfh_lock_timeout', 'Could not acquire the i18n sync lock -- another request is holding it.', array( 'status' => 409 ) );
        }
        usleep( 250000 ); // 250ms
    }

    try {
        $current = pll_get_post_translations( $object->ID );
        $current = is_array( $current ) ? $current : array();
        $merged  = array_merge( $current, array_map( 'intval', $value ) );
        pll_save_post_translations( $merged );
        globalfxhub_clean_i18n_languages_cache();
    } finally {
        globalfxhub_release_i18n_sync_lock();
    }

    return true;
}
