<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Customizer Performance Settings
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register performance Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tainacan_customizer_performance( $wp_customize ) {

    $wp_customize->add_section( 'tainacan_performance', array(
        'title'    => __( 'Performance', 'flavor' ),
        'priority' => 90,
    ) );

    // --- Disable Emojis ---
    $wp_customize->add_setting( 'tainacan_disable_emojis', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_disable_emojis', array(
        'label'   => __( 'Disable WordPress emoji scripts', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Disable Embeds ---
    $wp_customize->add_setting( 'tainacan_disable_embeds', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_disable_embeds', array(
        'label'   => __( 'Disable oEmbed discovery', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Local Gravatars ---
    $wp_customize->add_setting( 'tainacan_local_gravatars', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_local_gravatars', array(
        'label'   => __( 'Cache Gravatars locally', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Lazy Load Images ---
    $wp_customize->add_setting( 'tainacan_lazy_load_images', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_lazy_load_images', array(
        'label'   => __( 'Native lazy loading for images', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Lazy Load Iframes ---
    $wp_customize->add_setting( 'tainacan_lazy_load_iframes', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_lazy_load_iframes', array(
        'label'   => __( 'Lazy load iframes', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Preload Fonts ---
    $wp_customize->add_setting( 'tainacan_preload_fonts', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_preload_fonts', array(
        'label'   => __( 'Preload theme fonts', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Disable Dashicons Frontend ---
    $wp_customize->add_setting( 'tainacan_disable_dashicons_frontend', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_disable_dashicons_frontend', array(
        'label'   => __( 'Remove Dashicons from frontend for non-logged users', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );

    // --- Minify Dynamic CSS ---
    $wp_customize->add_setting( 'tainacan_minify_dynamic_css', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_minify_dynamic_css', array(
        'label'   => __( 'Minify inline dynamic CSS', 'flavor' ),
        'section' => 'tainacan_performance',
        'type'    => 'checkbox',
    ) );
}
add_action( 'customize_register', 'tainacan_customizer_performance' );

/**
 * Remove emoji scripts and styles.
 */
function tainacan_disable_emojis_action() {
    if ( ! get_theme_mod( 'tainacan_disable_emojis', false ) ) {
        return;
    }

    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
    remove_action( 'wp_print_styles', 'print_emoji_styles' );
    remove_action( 'admin_print_styles', 'print_emoji_styles' );
    remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
    remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );

    add_filter( 'tiny_mce_plugins', function ( $plugins ) {
        return is_array( $plugins ) ? array_diff( $plugins, array( 'wpemoji' ) ) : array();
    } );

    add_filter( 'wp_resource_hints', function ( $urls, $relation_type ) {
        if ( 'dns-prefetch' === $relation_type ) {
            $urls = array_filter( $urls, function ( $url ) {
                return false === strpos( $url, 'https://s.w.org/images/core/emoji/' );
            } );
        }
        return $urls;
    }, 10, 2 );
}
add_action( 'init', 'tainacan_disable_emojis_action' );

/**
 * Disable oEmbed / wp-embed.
 */
function tainacan_disable_embeds_action() {
    if ( ! get_theme_mod( 'tainacan_disable_embeds', false ) ) {
        return;
    }

    wp_deregister_script( 'wp-embed' );

    remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
    remove_action( 'wp_head', 'wp_oembed_add_host_js' );
    remove_filter( 'oembed_dataparse', 'wp_filter_oembed_result', 10 );
}
add_action( 'wp_enqueue_scripts', 'tainacan_disable_embeds_action' );

/**
 * Add loading="lazy" to iframes in post content.
 *
 * @param string $content Post content.
 * @return string Filtered content.
 */
function tainacan_lazy_load_iframes_filter( $content ) {
    if ( ! get_theme_mod( 'tainacan_lazy_load_iframes', true ) ) {
        return $content;
    }

    if ( empty( $content ) || false === stripos( $content, '<iframe' ) ) {
        return $content;
    }

    $content = preg_replace(
        '/<iframe(?![^>]*loading\s*=)([^>]*)>/i',
        '<iframe loading="lazy"$1>',
        $content
    );

    return $content;
}
add_filter( 'the_content', 'tainacan_lazy_load_iframes_filter', 99 );

/**
 * Remove Dashicons stylesheet on frontend for non-logged-in users.
 */
function tainacan_disable_dashicons_frontend_action() {
    if ( ! get_theme_mod( 'tainacan_disable_dashicons_frontend', false ) ) {
        return;
    }

    if ( ! is_user_logged_in() ) {
        wp_deregister_style( 'dashicons' );
    }
}
add_action( 'wp_enqueue_scripts', 'tainacan_disable_dashicons_frontend_action' );
