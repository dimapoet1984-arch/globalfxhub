<?php
/**
 * 'gfh_comparison': the real WP custom post type broker-vs-broker
 * editorial comparisons are authored as, so they go through WordPress's
 * normal draft -> review -> publish workflow and actually show up in
 * wp-admin -- replacing the hardcoded PHP array
 * globalfxhub_get_editorial_comparisons() used to return directly (see
 * inc/broker-comparison-editorial.php, which now queries this post
 * type instead).
 *
 * Deliberately ONE post meta field (gfh_cmp_data, a JSON-encoded blob
 * of the full structured content) rather than one meta key per field.
 * A flat meta key per array item (choose_a_points.0, choose_a_points.1,
 * faqs.0.q, ...) is possible but fragile over REST -- WordPress's
 * built-in array/object post-meta REST schema validation is easy to
 * trip on a nested shape like this (an array of {q,a} objects), and a
 * single JSON string sidesteps that entirely while still being fully
 * REST-readable/writable (show_in_rest => true below) for the
 * create/update script in i18n/create_comparison_posts.py. The meta
 * box below is the human-facing form: it reads/writes the SAME JSON
 * blob, decoded into individual fields for editing, so nobody actually
 * touches raw JSON in wp-admin.
 *
 * Translations are NOT handled through this post type or through
 * Polylang per-language posts -- they stay in the existing flat
 * {key: {lang: {field: text}}} PHP array
 * (globalfxhub_editorial_comparison_translations()) and the
 * i18n/translate_comparison_editorial.py pipeline, keyed by this
 * post's slug (post_name). This post type only replaces where the
 * ENGLISH source content lives and how it's authored/reviewed; nothing
 * about i18n changes.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_register_comparison_post_type() {
    register_post_type( 'gfh_comparison', array(
        'label'        => 'Broker Comparisons',
        'labels'       => array(
            'name'               => 'Broker Comparisons',
            'singular_name'      => 'Broker Comparison',
            'add_new_item'       => 'Add New Comparison',
            'edit_item'          => 'Edit Comparison',
            'all_items'          => 'All Comparisons',
            'view_item'          => 'View Comparison',
            'search_items'       => 'Search Comparisons',
            'not_found'          => 'No comparisons found',
            'not_found_in_trash' => 'No comparisons found in Trash',
        ),
        'public'       => false,
        'show_ui'      => true,
        'show_in_menu' => true,
        'menu_icon'    => 'dashicons-chart-bar',
        'supports'     => array( 'title' ),
        'show_in_rest' => true,
        'rest_base'    => 'gfh-comparisons',
        'capability_type' => 'post',
        'map_meta_cap' => true,
    ) );

    register_post_meta( 'gfh_comparison', 'gfh_cmp_data', array(
        'single'        => true,
        'type'          => 'string',
        'show_in_rest'  => true,
        'auth_callback' => function( $allowed, $meta_key, $post_id ) {
            return current_user_can( 'edit_post', $post_id );
        },
    ) );
}
add_action( 'init', 'globalfxhub_register_comparison_post_type' );

/**
 * The fields a comparison's gfh_cmp_data JSON blob holds, in the order
 * the meta box renders them. 'list' fields are newline-separated in
 * the textarea (one point per line); 'faqs' is special-cased (Q:/A:
 * pairs, blank-line separated).
 */
function globalfxhub_comparison_field_defs() {
    return array(
        array( 'key' => 'a_slug', 'label' => 'Broker A slug', 'type' => 'text', 'note' => "This comparison's lower-rank broker (e.g. \"avatrade\") -- must match an existing broker slug." ),
        array( 'key' => 'b_slug', 'label' => 'Broker B slug', 'type' => 'text', 'note' => 'The other broker (e.g. "pepperstone").' ),
        array( 'key' => 'meta_title', 'label' => 'Meta title', 'type' => 'text' ),
        array( 'key' => 'meta_description', 'label' => 'Meta description', 'type' => 'textarea_small' ),
        array( 'key' => 'intro', 'label' => 'Intro', 'type' => 'textarea' ),
        array( 'key' => 'verdict', 'label' => 'Verdict', 'type' => 'textarea' ),
        array( 'key' => 'choose_a_heading', 'label' => 'Choose-A heading', 'type' => 'text' ),
        array( 'key' => 'choose_a_points', 'label' => 'Choose-A points (one per line)', 'type' => 'list' ),
        array( 'key' => 'choose_b_heading', 'label' => 'Choose-B heading', 'type' => 'text' ),
        array( 'key' => 'choose_b_points', 'label' => 'Choose-B points (one per line)', 'type' => 'list' ),
        array( 'key' => 'worth_knowing_heading', 'label' => 'Worth-knowing heading', 'type' => 'text' ),
        array( 'key' => 'worth_knowing', 'label' => 'Worth knowing', 'type' => 'textarea' ),
        array( 'key' => 'faqs', 'label' => 'FAQs (Q: / A: pairs, one blank line between each)', 'type' => 'faqs' ),
    );
}

function globalfxhub_add_comparison_meta_box() {
    add_meta_box(
        'gfh_comparison_data',
        'Comparison content',
        'globalfxhub_render_comparison_meta_box',
        'gfh_comparison',
        'normal',
        'high'
    );
}
add_action( 'add_meta_boxes', 'globalfxhub_add_comparison_meta_box' );

function globalfxhub_render_comparison_meta_box( $post ) {
    wp_nonce_field( 'gfh_comparison_save', 'gfh_comparison_nonce' );

    $json = get_post_meta( $post->ID, 'gfh_cmp_data', true );
    $data = $json ? json_decode( $json, true ) : array();
    if ( ! is_array( $data ) ) {
        $data = array();
    }

    foreach ( globalfxhub_comparison_field_defs() as $field ) {
        $key = $field['key'];
        echo '<p><label for="gfh_cmp_' . esc_attr( $key ) . '"><strong>' . esc_html( $field['label'] ) . '</strong></label>';
        if ( ! empty( $field['note'] ) ) {
            echo '<br><span style="color:#666;font-size:12px;">' . esc_html( $field['note'] ) . '</span>';
        }
        echo '</p>';

        if ( 'text' === $field['type'] ) {
            $value = isset( $data[ $key ] ) ? $data[ $key ] : '';
            echo '<input type="text" style="width:100%;" id="gfh_cmp_' . esc_attr( $key ) . '" name="gfh_cmp[' . esc_attr( $key ) . ']" value="' . esc_attr( $value ) . '">';
        } elseif ( 'textarea_small' === $field['type'] ) {
            $value = isset( $data[ $key ] ) ? $data[ $key ] : '';
            echo '<textarea style="width:100%;" rows="2" id="gfh_cmp_' . esc_attr( $key ) . '" name="gfh_cmp[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
        } elseif ( 'textarea' === $field['type'] ) {
            $value = isset( $data[ $key ] ) ? $data[ $key ] : '';
            echo '<textarea style="width:100%;" rows="5" id="gfh_cmp_' . esc_attr( $key ) . '" name="gfh_cmp[' . esc_attr( $key ) . ']">' . esc_textarea( $value ) . '</textarea>';
        } elseif ( 'list' === $field['type'] ) {
            $points = isset( $data[ $key ] ) && is_array( $data[ $key ] ) ? $data[ $key ] : array();
            echo '<textarea style="width:100%;" rows="5" id="gfh_cmp_' . esc_attr( $key ) . '" name="gfh_cmp[' . esc_attr( $key ) . ']">' . esc_textarea( implode( "\n", $points ) ) . '</textarea>';
        } elseif ( 'faqs' === $field['type'] ) {
            $faqs = isset( $data[ $key ] ) && is_array( $data[ $key ] ) ? $data[ $key ] : array();
            $lines = array();
            foreach ( $faqs as $faq ) {
                $lines[] = 'Q: ' . ( $faq['q'] ?? '' );
                $lines[] = 'A: ' . ( $faq['a'] ?? '' );
                $lines[] = '';
            }
            echo '<textarea style="width:100%;" rows="8" id="gfh_cmp_' . esc_attr( $key ) . '" name="gfh_cmp[' . esc_attr( $key ) . ']">' . esc_textarea( trim( implode( "\n", $lines ) ) ) . '</textarea>';
        }
    }
}

/**
 * Parses the "Q: ...\nA: ...\n\nQ: ...\nA: ..." textarea format back
 * into the faqs[{q,a}] shape -- a blank line (or a new "Q:" line
 * without a preceding "A:") starts the next pair. Silently drops a
 * trailing Q with no A rather than erroring, since the admin is free
 * to leave a half-written FAQ while still drafting.
 */
function globalfxhub_parse_comparison_faqs( $raw ) {
    $faqs = array();
    $current_q = null;
    foreach ( preg_split( '/\r\n|\r|\n/', $raw ) as $line ) {
        $line = trim( $line );
        if ( '' === $line ) {
            continue;
        }
        if ( 0 === stripos( $line, 'Q:' ) ) {
            $current_q = trim( substr( $line, 2 ) );
        } elseif ( 0 === stripos( $line, 'A:' ) && null !== $current_q ) {
            $faqs[] = array( 'q' => $current_q, 'a' => trim( substr( $line, 2 ) ) );
            $current_q = null;
        }
    }
    return $faqs;
}

function globalfxhub_save_comparison_meta_box( $post_id ) {
    if ( ! isset( $_POST['gfh_comparison_nonce'] ) || ! wp_verify_nonce( $_POST['gfh_comparison_nonce'], 'gfh_comparison_save' ) ) {
        return;
    }
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }
    if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
    }
    if ( ! isset( $_POST['gfh_cmp'] ) || ! is_array( $_POST['gfh_cmp'] ) ) {
        return;
    }

    $raw = wp_unslash( $_POST['gfh_cmp'] );
    $data = array(
        'a_slug'                => sanitize_title( $raw['a_slug'] ?? '' ),
        'b_slug'                => sanitize_title( $raw['b_slug'] ?? '' ),
        'meta_title'            => sanitize_text_field( $raw['meta_title'] ?? '' ),
        'meta_description'      => sanitize_textarea_field( $raw['meta_description'] ?? '' ),
        'intro'                 => sanitize_textarea_field( $raw['intro'] ?? '' ),
        'verdict'               => sanitize_textarea_field( $raw['verdict'] ?? '' ),
        'choose_a_heading'      => sanitize_text_field( $raw['choose_a_heading'] ?? '' ),
        'choose_a_points'       => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw['choose_a_points'] ?? '' ) ) ) ),
        'choose_b_heading'      => sanitize_text_field( $raw['choose_b_heading'] ?? '' ),
        'choose_b_points'       => array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', $raw['choose_b_points'] ?? '' ) ) ) ),
        'worth_knowing_heading' => sanitize_text_field( $raw['worth_knowing_heading'] ?? '' ),
        'worth_knowing'         => sanitize_textarea_field( $raw['worth_knowing'] ?? '' ),
        'faqs'                  => globalfxhub_parse_comparison_faqs( $raw['faqs'] ?? '' ),
    );

    update_post_meta( $post_id, 'gfh_cmp_data', wp_json_encode( $data ) );
}
add_action( 'save_post_gfh_comparison', 'globalfxhub_save_comparison_meta_box' );

/**
 * Admin list table: surface each comparison's target slug pair and
 * scheduled/target date at a glance, since the default title column
 * alone doesn't show which broker pair or which calendar week a draft
 * is for.
 */
function globalfxhub_comparison_admin_columns( $columns ) {
    $columns['gfh_cmp_pair'] = 'Pair';
    return $columns;
}
add_filter( 'manage_gfh_comparison_posts_columns', 'globalfxhub_comparison_admin_columns' );

function globalfxhub_comparison_admin_column_content( $column, $post_id ) {
    if ( 'gfh_cmp_pair' !== $column ) {
        return;
    }
    $json = get_post_meta( $post_id, 'gfh_cmp_data', true );
    $data = $json ? json_decode( $json, true ) : null;
    if ( is_array( $data ) && ! empty( $data['a_slug'] ) && ! empty( $data['b_slug'] ) ) {
        echo esc_html( $data['a_slug'] . ' vs ' . $data['b_slug'] );
    }
}
add_action( 'manage_gfh_comparison_posts_custom_column', 'globalfxhub_comparison_admin_column_content', 10, 2 );
