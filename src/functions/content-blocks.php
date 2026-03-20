<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Content Blocks / Hooks System
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register the tainacan_content_block custom post type.
 */
function tainacan_register_content_block_cpt() {
    $labels = array(
        'name'               => __( 'Content Blocks', 'flavor' ),
        'singular_name'      => __( 'Content Block', 'flavor' ),
        'add_new'            => __( 'Add New', 'flavor' ),
        'add_new_item'       => __( 'Add New Content Block', 'flavor' ),
        'edit_item'          => __( 'Edit Content Block', 'flavor' ),
        'new_item'           => __( 'New Content Block', 'flavor' ),
        'view_item'          => __( 'View Content Block', 'flavor' ),
        'search_items'       => __( 'Search Content Blocks', 'flavor' ),
        'not_found'          => __( 'No content blocks found.', 'flavor' ),
        'not_found_in_trash' => __( 'No content blocks found in Trash.', 'flavor' ),
        'all_items'          => __( 'Content Blocks', 'flavor' ),
    );

    $args = array(
        'labels'          => $labels,
        'public'          => false,
        'show_ui'         => true,
        'show_in_menu'    => 'themes.php',
        'capability_type' => 'page',
        'supports'        => array( 'title', 'editor' ),
        'menu_icon'       => 'dashicons-layout',
        'show_in_rest'    => true,
    );

    register_post_type( 'tainacan_content_block', $args );
}
add_action( 'init', 'tainacan_register_content_block_cpt' );

/**
 * Add meta boxes for content block settings.
 */
function tainacan_content_block_meta_boxes() {
    add_meta_box(
        'tainacan_cb_settings',
        __( 'Content Block Settings', 'flavor' ),
        'tainacan_content_block_meta_box_render',
        'tainacan_content_block',
        'side',
        'default'
    );
}
add_action( 'add_meta_boxes', 'tainacan_content_block_meta_boxes' );

/**
 * Render the content block meta box.
 *
 * @param WP_Post $post Current post object.
 */
function tainacan_content_block_meta_box_render( $post ) {
    wp_nonce_field( 'tainacan_cb_meta_nonce', '_tainacan_cb_nonce' );

    $hook      = get_post_meta( $post->ID, '_tainacan_cb_hook', true );
    $condition = get_post_meta( $post->ID, '_tainacan_cb_display_condition', true );
    $priority  = get_post_meta( $post->ID, '_tainacan_cb_priority', true );
    $roles     = get_post_meta( $post->ID, '_tainacan_cb_user_roles', true );
    $active    = get_post_meta( $post->ID, '_tainacan_cb_active', true );

    // Defaults.
    if ( '' === $hook ) {
        $hook = 'tainacan-interface-before-content';
    }
    if ( '' === $condition ) {
        $condition = 'all';
    }
    if ( '' === $priority ) {
        $priority = 10;
    }
    if ( '' === $roles ) {
        $roles = 'all';
    }
    if ( '' === $active ) {
        $active = '1';
    }

    $hooks = array(
        'wp_head'                              => __( 'wp_head', 'flavor' ),
        'wp_body_open'                         => __( 'wp_body_open', 'flavor' ),
        'tainacan-interface-before-content'    => __( 'Before content', 'flavor' ),
        'tainacan-interface-after-content'     => __( 'After content', 'flavor' ),
        'tainacan-interface-before-footer'     => __( 'Before footer', 'flavor' ),
        'wp_footer'                            => __( 'wp_footer', 'flavor' ),
    );

    $conditions = array(
        'all'         => __( 'All pages', 'flavor' ),
        'homepage'    => __( 'Homepage only', 'flavor' ),
        'collections' => __( 'Collections', 'flavor' ),
        'items'       => __( 'Items', 'flavor' ),
        'archives'    => __( 'Archives', 'flavor' ),
        'custom'      => __( 'Custom (by slug)', 'flavor' ),
    );

    $role_options = array(
        'all'        => __( 'All visitors', 'flavor' ),
        'logged-in'  => __( 'Logged-in users', 'flavor' ),
        'logged-out' => __( 'Logged-out users', 'flavor' ),
    );

    ?>
    <p>
        <label for="tainacan_cb_active">
            <input type="checkbox" id="tainacan_cb_active" name="tainacan_cb_active" value="1" <?php checked( $active, '1' ); ?> />
            <?php esc_html_e( 'Active', 'flavor' ); ?>
        </label>
    </p>

    <p>
        <label for="tainacan_cb_hook"><strong><?php esc_html_e( 'Hook / Position', 'flavor' ); ?></strong></label><br/>
        <select id="tainacan_cb_hook" name="tainacan_cb_hook" style="width:100%;">
            <?php foreach ( $hooks as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $hook, $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="tainacan_cb_display_condition"><strong><?php esc_html_e( 'Display Condition', 'flavor' ); ?></strong></label><br/>
        <select id="tainacan_cb_display_condition" name="tainacan_cb_display_condition" style="width:100%;">
            <?php foreach ( $conditions as $key => $label ) : ?>
                <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $condition, $key ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <label for="tainacan_cb_priority"><strong><?php esc_html_e( 'Priority', 'flavor' ); ?></strong></label><br/>
        <input type="number" id="tainacan_cb_priority" name="tainacan_cb_priority" value="<?php echo esc_attr( $priority ); ?>" min="1" max="100" style="width:100%;" />
    </p>

    <p>
        <strong><?php esc_html_e( 'Visibility', 'flavor' ); ?></strong><br/>
        <?php foreach ( $role_options as $key => $label ) : ?>
            <label>
                <input type="radio" name="tainacan_cb_user_roles" value="<?php echo esc_attr( $key ); ?>" <?php checked( $roles, $key ); ?> />
                <?php echo esc_html( $label ); ?>
            </label><br/>
        <?php endforeach; ?>
    </p>
    <?php
}

/**
 * Save content block meta box data.
 *
 * @param int $post_id Post ID.
 */
function tainacan_content_block_save_meta( $post_id ) {
    // Nonce verification.
    if ( ! isset( $_POST['_tainacan_cb_nonce'] ) || ! wp_verify_nonce( $_POST['_tainacan_cb_nonce'], 'tainacan_cb_meta_nonce' ) ) {
        return;
    }

    // Check autosave.
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check permissions.
    if ( ! current_user_can( 'edit_page', $post_id ) ) {
        return;
    }

    // Hook.
    if ( isset( $_POST['tainacan_cb_hook'] ) ) {
        $valid_hooks = array(
            'wp_head',
            'wp_body_open',
            'tainacan-interface-before-content',
            'tainacan-interface-after-content',
            'tainacan-interface-before-footer',
            'wp_footer',
        );
        $hook = sanitize_text_field( wp_unslash( $_POST['tainacan_cb_hook'] ) );
        if ( in_array( $hook, $valid_hooks, true ) ) {
            update_post_meta( $post_id, '_tainacan_cb_hook', $hook );
        }
    }

    // Display condition.
    if ( isset( $_POST['tainacan_cb_display_condition'] ) ) {
        $valid_conditions = array( 'all', 'homepage', 'collections', 'items', 'archives', 'custom' );
        $condition = sanitize_text_field( wp_unslash( $_POST['tainacan_cb_display_condition'] ) );
        if ( in_array( $condition, $valid_conditions, true ) ) {
            update_post_meta( $post_id, '_tainacan_cb_display_condition', $condition );
        }
    }

    // Priority.
    if ( isset( $_POST['tainacan_cb_priority'] ) ) {
        $priority = absint( $_POST['tainacan_cb_priority'] );
        $priority = max( 1, min( 100, $priority ) );
        update_post_meta( $post_id, '_tainacan_cb_priority', $priority );
    }

    // User roles.
    if ( isset( $_POST['tainacan_cb_user_roles'] ) ) {
        $valid_roles = array( 'all', 'logged-in', 'logged-out' );
        $roles = sanitize_text_field( wp_unslash( $_POST['tainacan_cb_user_roles'] ) );
        if ( in_array( $roles, $valid_roles, true ) ) {
            update_post_meta( $post_id, '_tainacan_cb_user_roles', $roles );
        }
    }

    // Active.
    $active = isset( $_POST['tainacan_cb_active'] ) ? '1' : '0';
    update_post_meta( $post_id, '_tainacan_cb_active', $active );
}
add_action( 'save_post_tainacan_content_block', 'tainacan_content_block_save_meta' );

/**
 * Check if the current page matches the display condition.
 *
 * @param string $condition Condition key.
 * @return bool
 */
function tainacan_cb_check_display_condition( $condition ) {
    switch ( $condition ) {
        case 'all':
            return true;

        case 'homepage':
            return is_front_page() || is_home();

        case 'collections':
            return function_exists( 'tainacan_get_collection_id' ) && tainacan_get_collection_id();

        case 'items':
            return is_singular( 'tainacan-item' ) || ( function_exists( 'tainacan_get_item' ) && tainacan_get_item() );

        case 'archives':
            return is_archive();

        case 'custom':
            return true; // Custom conditions can be extended via filter.

        default:
            return false;
    }
}

/**
 * Check user role visibility.
 *
 * @param string $roles Role key.
 * @return bool
 */
function tainacan_cb_check_user_roles( $roles ) {
    switch ( $roles ) {
        case 'all':
            return true;
        case 'logged-in':
            return is_user_logged_in();
        case 'logged-out':
            return ! is_user_logged_in();
        default:
            return true;
    }
}

/**
 * Render content blocks for a given hook.
 *
 * @param string $hook The action hook name.
 */
function tainacan_render_content_blocks( $hook ) {
    $blocks = get_posts( array(
        'post_type'      => 'tainacan_content_block',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'meta_query'     => array( // phpcs:ignore WordPress.DB.SlowDBQuery
            array(
                'key'   => '_tainacan_cb_hook',
                'value' => $hook,
            ),
            array(
                'key'   => '_tainacan_cb_active',
                'value' => '1',
            ),
        ),
        'meta_key'       => '_tainacan_cb_priority', // phpcs:ignore WordPress.DB.SlowDBQuery
        'orderby'        => 'meta_value_num',
        'order'          => 'ASC',
    ) );

    if ( empty( $blocks ) ) {
        return;
    }

    foreach ( $blocks as $block ) {
        $condition = get_post_meta( $block->ID, '_tainacan_cb_display_condition', true );
        $roles     = get_post_meta( $block->ID, '_tainacan_cb_user_roles', true );

        if ( ! tainacan_cb_check_display_condition( $condition ) ) {
            continue;
        }

        if ( ! tainacan_cb_check_user_roles( $roles ) ) {
            continue;
        }

        echo '<div class="tainacan-content-block tainacan-content-block--' . esc_attr( $block->ID ) . '">';
        echo apply_filters( 'the_content', $block->post_content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo '</div>';
    }
}

/**
 * Hook content blocks into each supported action.
 */
function tainacan_init_content_block_hooks() {
    $hooks = array(
        'wp_head',
        'wp_body_open',
        'tainacan-interface-before-content',
        'tainacan-interface-after-content',
        'tainacan-interface-before-footer',
        'wp_footer',
    );

    foreach ( $hooks as $hook ) {
        add_action( $hook, function () use ( $hook ) {
            tainacan_render_content_blocks( $hook );
        } );
    }
}
add_action( 'init', 'tainacan_init_content_block_hooks' );
