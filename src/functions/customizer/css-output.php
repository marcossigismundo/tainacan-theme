<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Customizer CSS Output Mode
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register CSS output mode Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tainacan_customizer_css_output( $wp_customize ) {

    $wp_customize->add_section( 'tainacan_css_output', array(
        'title'    => __( 'CSS Output Mode', 'flavor' ),
        'priority' => 91,
    ) );

    $wp_customize->add_setting( 'tainacan_css_output_mode', array(
        'default'           => 'inline',
        'sanitize_callback' => 'tainacan_sanitize_css_output_mode',
    ) );

    $wp_customize->add_control( 'tainacan_css_output_mode', array(
        'label'   => __( 'Dynamic CSS output', 'flavor' ),
        'section' => 'tainacan_css_output',
        'type'    => 'select',
        'choices' => array(
            'inline' => __( 'Inline (default)', 'flavor' ),
            'file'   => __( 'External file', 'flavor' ),
        ),
    ) );
}
add_action( 'customize_register', 'tainacan_customizer_css_output' );

/**
 * Sanitize the CSS output mode select.
 *
 * @param string $value Value to sanitize.
 * @return string Sanitized value.
 */
function tainacan_sanitize_css_output_mode( $value ) {
    $valid = array( 'inline', 'file' );
    return in_array( $value, $valid, true ) ? $value : 'inline';
}

/**
 * Get the path to the dynamic CSS file.
 *
 * @return string Absolute file path.
 */
function tainacan_get_dynamic_css_file_path() {
    $upload_dir = wp_upload_dir();
    return trailingslashit( $upload_dir['basedir'] ) . 'tainacan-dynamic.css';
}

/**
 * Get the URL of the dynamic CSS file.
 *
 * @return string URL.
 */
function tainacan_get_dynamic_css_file_url() {
    $upload_dir = wp_upload_dir();
    return trailingslashit( $upload_dir['baseurl'] ) . 'tainacan-dynamic.css';
}

/**
 * Capture all dynamic CSS output via output buffering.
 *
 * This fires the actions that normally print inline CSS and captures the output.
 *
 * @return string Collected CSS string.
 */
function tainacan_get_all_dynamic_css() {
    ob_start();

    // Fire known theme dynamic-CSS hooks / functions.
    if ( function_exists( 'tainacan_dynamic_css' ) ) {
        tainacan_dynamic_css();
    }
    if ( function_exists( 'tainacan_pastel_colors_css' ) ) {
        tainacan_pastel_colors_css();
    }
    if ( function_exists( 'tainacan_header_builder_css' ) ) {
        tainacan_header_builder_css();
    }
    if ( function_exists( 'tainacan_footer_builder_css' ) ) {
        tainacan_footer_builder_css();
    }
    if ( function_exists( 'tainacan_sidebar_css' ) ) {
        tainacan_sidebar_css();
    }
    if ( function_exists( 'tainacan_form_builder_css' ) ) {
        tainacan_form_builder_css();
    }

    $css = ob_get_clean();

    // Strip <style> tags if present.
    $css = preg_replace( '/<\/?style[^>]*>/i', '', $css );

    // Optionally minify.
    if ( get_theme_mod( 'tainacan_minify_dynamic_css', false ) ) {
        $css = tainacan_minify_css_string( $css );
    }

    return trim( $css );
}

/**
 * Very basic CSS minification.
 *
 * @param string $css CSS string.
 * @return string Minified CSS.
 */
function tainacan_minify_css_string( $css ) {
    // Remove comments.
    $css = preg_replace( '#/\*.*?\*/#s', '', $css );
    // Remove whitespace.
    $css = preg_replace( '/\s+/', ' ', $css );
    // Remove spaces around selectors / braces.
    $css = str_replace( array( ' {', '{ ', ' }', '} ', '; ', ': ', ', ' ), array( '{', '{', '}', '}', ';', ':', ',' ), $css );

    return trim( $css );
}

/**
 * Write the dynamic CSS to an external file.
 *
 * Called after the Customizer saves.
 */
function tainacan_write_dynamic_css_file() {
    if ( 'file' !== get_theme_mod( 'tainacan_css_output_mode', 'inline' ) ) {
        // Clean up any existing file when switching back to inline.
        $file = tainacan_get_dynamic_css_file_path();
        if ( file_exists( $file ) ) {
            wp_delete_file( $file );
        }
        return;
    }

    $css  = tainacan_get_all_dynamic_css();
    $file = tainacan_get_dynamic_css_file_path();

    // Use WP_Filesystem for writing.
    global $wp_filesystem;
    if ( empty( $wp_filesystem ) ) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        WP_Filesystem();
    }

    if ( $wp_filesystem ) {
        $wp_filesystem->put_contents( $file, $css, FS_CHMOD_FILE );
    }
}
add_action( 'customize_save_after', 'tainacan_write_dynamic_css_file' );

/**
 * Enqueue external dynamic CSS file when in "file" mode, otherwise fall back to inline.
 */
function tainacan_enqueue_dynamic_css_file() {
    if ( 'file' !== get_theme_mod( 'tainacan_css_output_mode', 'inline' ) ) {
        return; // Inline mode — existing behaviour handles it.
    }

    $file_path = tainacan_get_dynamic_css_file_path();
    $file_url  = tainacan_get_dynamic_css_file_url();

    if ( file_exists( $file_path ) ) {
        $version = filemtime( $file_path );
        wp_enqueue_style( 'tainacan-dynamic-css', $file_url, array(), $version );
    }
    // If the file doesn't exist, do nothing — the existing inline output remains as fallback.
}
add_action( 'wp_enqueue_scripts', 'tainacan_enqueue_dynamic_css_file', 20 );
