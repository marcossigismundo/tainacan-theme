<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Theme Settings Export/Import/Reset
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Add submenu page under Appearance.
 */
function tainacan_settings_page_menu() {
    add_theme_page(
        __( 'Theme Settings', 'flavor' ),
        __( 'Theme Settings', 'flavor' ),
        'edit_theme_options',
        'tainacan_settings_page',
        'tainacan_settings_page_render'
    );
}
add_action( 'admin_menu', 'tainacan_settings_page_menu' );

/**
 * Render the settings page.
 */
function tainacan_settings_page_render() {
    // Show admin notices / settings errors.
    settings_errors( 'tainacan_settings' );
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'Theme Settings', 'flavor' ); ?></h1>

        <!-- Export -->
        <div class="card">
            <h2><?php esc_html_e( 'Export Settings', 'flavor' ); ?></h2>
            <p><?php esc_html_e( 'Download a JSON file containing all current Customizer settings.', 'flavor' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                <input type="hidden" name="action" value="tainacan_export_settings" />
                <?php wp_nonce_field( 'tainacan_export_settings_nonce', '_tainacan_export_nonce' ); ?>
                <?php submit_button( __( 'Export Settings', 'flavor' ), 'primary', 'submit', false ); ?>
            </form>
        </div>

        <!-- Import -->
        <div class="card">
            <h2><?php esc_html_e( 'Import Settings', 'flavor' ); ?></h2>
            <p><?php esc_html_e( 'Upload a previously exported JSON file to restore settings.', 'flavor' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
                <input type="hidden" name="action" value="tainacan_import_settings" />
                <?php wp_nonce_field( 'tainacan_import_settings_nonce', '_tainacan_import_nonce' ); ?>
                <p>
                    <input type="file" name="tainacan_import_file" accept=".json" required />
                </p>
                <?php submit_button( __( 'Import Settings', 'flavor' ), 'primary', 'submit', false ); ?>
            </form>
        </div>

        <!-- Reset -->
        <div class="card">
            <h2><?php esc_html_e( 'Reset Settings', 'flavor' ); ?></h2>
            <p><?php esc_html_e( 'Remove all Customizer settings and restore theme defaults. This cannot be undone.', 'flavor' ); ?></p>
            <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'Are you sure you want to reset ALL theme settings to their defaults? This cannot be undone.', 'flavor' ) ); ?>');">
                <input type="hidden" name="action" value="tainacan_reset_settings" />
                <?php wp_nonce_field( 'tainacan_reset_settings_nonce', '_tainacan_reset_nonce' ); ?>
                <?php submit_button( __( 'Reset All Settings', 'flavor' ), 'delete', 'submit', false ); ?>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Handle export.
 */
function tainacan_handle_export_settings() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to do this.', 'flavor' ) );
    }

    check_admin_referer( 'tainacan_export_settings_nonce', '_tainacan_export_nonce' );

    $mods     = get_theme_mods();
    $filename = 'tainacan-settings-' . gmdate( 'Y-m-d' ) . '.json';

    header( 'Content-Type: application/json; charset=utf-8' );
    header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
    header( 'Cache-Control: no-cache, no-store, must-revalidate' );
    header( 'Pragma: no-cache' );
    header( 'Expires: 0' );

    echo wp_json_encode( $mods, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
    exit;
}
add_action( 'admin_post_tainacan_export_settings', 'tainacan_handle_export_settings' );

/**
 * Handle import.
 */
function tainacan_handle_import_settings() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to do this.', 'flavor' ) );
    }

    check_admin_referer( 'tainacan_import_settings_nonce', '_tainacan_import_nonce' );

    $redirect_url = admin_url( 'themes.php?page=tainacan_settings_page' );

    if ( empty( $_FILES['tainacan_import_file']['tmp_name'] ) ) {
        add_settings_error( 'tainacan_settings', 'no_file', __( 'No file was uploaded.', 'flavor' ), 'error' );
        set_transient( 'settings_errors', get_settings_errors(), 30 );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    $file_contents = file_get_contents( $_FILES['tainacan_import_file']['tmp_name'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
    $data          = json_decode( $file_contents, true );

    if ( ! is_array( $data ) || json_last_error() !== JSON_ERROR_NONE ) {
        add_settings_error( 'tainacan_settings', 'invalid_json', __( 'The uploaded file does not contain valid JSON.', 'flavor' ), 'error' );
        set_transient( 'settings_errors', get_settings_errors(), 30 );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    // Validate expected structure: must be an associative array (not a numeric array).
    if ( array_keys( $data ) === range( 0, count( $data ) - 1 ) ) {
        add_settings_error( 'tainacan_settings', 'invalid_structure', __( 'The uploaded file does not have the expected settings structure.', 'flavor' ), 'error' );
        set_transient( 'settings_errors', get_settings_errors(), 30 );
        wp_safe_redirect( $redirect_url );
        exit;
    }

    foreach ( $data as $key => $value ) {
        set_theme_mod( sanitize_key( $key ), $value );
    }

    add_settings_error( 'tainacan_settings', 'import_success', __( 'Settings imported successfully.', 'flavor' ), 'success' );
    set_transient( 'settings_errors', get_settings_errors(), 30 );
    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'admin_post_tainacan_import_settings', 'tainacan_handle_import_settings' );

/**
 * Handle reset.
 */
function tainacan_handle_reset_settings() {
    if ( ! current_user_can( 'edit_theme_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to do this.', 'flavor' ) );
    }

    check_admin_referer( 'tainacan_reset_settings_nonce', '_tainacan_reset_nonce' );

    remove_theme_mods();

    $redirect_url = admin_url( 'themes.php?page=tainacan_settings_page' );
    add_settings_error( 'tainacan_settings', 'reset_success', __( 'All theme settings have been reset to defaults.', 'flavor' ), 'success' );
    set_transient( 'settings_errors', get_settings_errors(), 30 );
    wp_safe_redirect( $redirect_url );
    exit;
}
add_action( 'admin_post_tainacan_reset_settings', 'tainacan_handle_reset_settings' );
