<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Sidebar Settings - WordPress Customizer Module
 *
 * @package Tainacan Interface
 * @since 2.9.0
 */

add_action( 'customize_register', 'tainacan_sidebar_settings_customizer' );

function tainacan_sidebar_settings_customizer( $wp_customize ) {

    // Section: Sidebar Settings
    $wp_customize->add_section( 'tainacan_sidebar_settings', array(
        'title'    => __( 'Sidebar Settings', 'flavor' ),
        'priority' => 35,
    ) );

    // Sidebar style
    $wp_customize->add_setting( 'tainacan_sidebar_style', array(
        'default'           => 'default',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_style', array(
        'label'   => __( 'Sidebar Style', 'flavor' ),
        'section' => 'tainacan_sidebar_settings',
        'type'    => 'select',
        'choices' => array(
            'default'  => __( 'Default', 'flavor' ),
            'bordered' => __( 'Bordered', 'flavor' ),
            'shadow'   => __( 'Shadow', 'flavor' ),
            'minimal'  => __( 'Minimal', 'flavor' ),
        ),
    ) );

    // Sticky sidebar
    $wp_customize->add_setting( 'tainacan_sidebar_sticky', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_sticky', array(
        'label'       => __( 'Make Sidebar Sticky on Scroll', 'flavor' ),
        'section'     => 'tainacan_sidebar_settings',
        'type'        => 'checkbox',
    ) );

    // Sticky offset
    $wp_customize->add_setting( 'tainacan_sidebar_sticky_offset', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_sticky_offset', array(
        'label'       => __( 'Sticky Top Offset (px)', 'flavor' ),
        'section'     => 'tainacan_sidebar_settings',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 200,
        ),
    ) );

    // Sidebar width
    $wp_customize->add_setting( 'tainacan_sidebar_width', array(
        'default'           => 'default',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_width', array(
        'label'   => __( 'Sidebar Width', 'flavor' ),
        'section' => 'tainacan_sidebar_settings',
        'type'    => 'select',
        'choices' => array(
            'narrow'  => __( 'Narrow (25%)', 'flavor' ),
            'default' => __( 'Default (33%)', 'flavor' ),
            'wide'    => __( 'Wide (40%)', 'flavor' ),
        ),
    ) );

    // Mobile position
    $wp_customize->add_setting( 'tainacan_sidebar_mobile_position', array(
        'default'           => 'after-content',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_mobile_position', array(
        'label'   => __( 'Mobile Sidebar Position', 'flavor' ),
        'section' => 'tainacan_sidebar_settings',
        'type'    => 'select',
        'choices' => array(
            'before-content' => __( 'Before Content', 'flavor' ),
            'after-content'  => __( 'After Content', 'flavor' ),
            'hidden'         => __( 'Hidden', 'flavor' ),
        ),
    ) );

    // Background color
    $wp_customize->add_setting( 'tainacan_sidebar_bg_color', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_sidebar_bg_color', array(
        'label'   => __( 'Background Color', 'flavor' ),
        'section' => 'tainacan_sidebar_settings',
    ) ) );

    // Padding
    $wp_customize->add_setting( 'tainacan_sidebar_padding', array(
        'default'           => 15,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_padding', array(
        'label'       => __( 'Padding (px)', 'flavor' ),
        'section'     => 'tainacan_sidebar_settings',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 40,
        ),
    ) );

    // Border radius
    $wp_customize->add_setting( 'tainacan_sidebar_border_radius', array(
        'default'           => 0,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_sidebar_border_radius', array(
        'label'       => __( 'Border Radius (px)', 'flavor' ),
        'section'     => 'tainacan_sidebar_settings',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 20,
        ),
    ) );
}

/**
 * Output sidebar CSS.
 */
add_action( 'wp_head', 'tainacan_sidebar_output_css', 20 );

function tainacan_sidebar_output_css() {
    $style         = get_theme_mod( 'tainacan_sidebar_style', 'default' );
    $sticky        = get_theme_mod( 'tainacan_sidebar_sticky', false );
    $sticky_offset = absint( get_theme_mod( 'tainacan_sidebar_sticky_offset', 80 ) );
    $width         = get_theme_mod( 'tainacan_sidebar_width', 'default' );
    $mobile_pos    = get_theme_mod( 'tainacan_sidebar_mobile_position', 'after-content' );
    $bg_color      = get_theme_mod( 'tainacan_sidebar_bg_color', '' );
    $padding       = absint( get_theme_mod( 'tainacan_sidebar_padding', 15 ) );
    $border_radius = absint( get_theme_mod( 'tainacan_sidebar_border_radius', 0 ) );

    // Determine width percentage
    $width_value = '33.333%';
    switch ( $width ) {
        case 'narrow':  $width_value = '25%'; break;
        case 'default': $width_value = '33.333%'; break;
        case 'wide':    $width_value = '40%'; break;
    }

    $css = '<style id="tainacan-sidebar-css">';

    // Sidebar width
    $css .= '.sidebar-area { width:' . esc_attr( $width_value ) . '; }';
    $css .= '.content-with-sidebar { width:' . esc_attr( (float) str_replace( '%', '', '100' ) - (float) str_replace( '%', '', $width_value ) ) . '%; }';

    // Padding
    $css .= '.sidebar-area .widget-area { padding:' . esc_attr( $padding ) . 'px; }';

    // Border radius
    if ( $border_radius > 0 ) {
        $css .= '.sidebar-area .widget-area { border-radius:' . esc_attr( $border_radius ) . 'px; }';
    }

    // Background color
    if ( ! empty( $bg_color ) ) {
        $css .= '.sidebar-area .widget-area { background-color:' . esc_attr( $bg_color ) . '; }';
    }

    // Sidebar styles
    switch ( $style ) {
        case 'bordered':
            $css .= '.sidebar-area .widget-area { border: 1px solid #dee2e6; }';
            break;
        case 'shadow':
            $css .= '.sidebar-area .widget-area { box-shadow: 0 2px 8px rgba(0,0,0,0.08); }';
            break;
        case 'minimal':
            $css .= '.sidebar-area .widget-area { border: none; background: transparent; }';
            break;
    }

    // Sticky sidebar
    if ( $sticky ) {
        $css .= '.sidebar-area .widget-area { position: sticky; top:' . esc_attr( $sticky_offset ) . 'px; }';
    }

    // Mobile sidebar position
    if ( 'hidden' === $mobile_pos ) {
        $css .= '@media (max-width: 768px) { .sidebar-area { display: none; } }';
    } elseif ( 'before-content' === $mobile_pos ) {
        $css .= '@media (max-width: 768px) { .sidebar-area { order: -1; } }';
    }

    $css .= '</style>';

    echo $css;
}

/**
 * Add sidebar-related body classes.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
add_filter( 'body_class', 'tainacan_sidebar_body_classes' );

function tainacan_sidebar_body_classes( $classes ) {
    $style      = get_theme_mod( 'tainacan_sidebar_style', 'default' );
    $sticky     = get_theme_mod( 'tainacan_sidebar_sticky', false );
    $width      = get_theme_mod( 'tainacan_sidebar_width', 'default' );
    $mobile_pos = get_theme_mod( 'tainacan_sidebar_mobile_position', 'after-content' );

    $classes[] = 'sidebar-style-' . sanitize_html_class( $style );
    $classes[] = 'sidebar-width-' . sanitize_html_class( $width );
    $classes[] = 'sidebar-mobile-' . sanitize_html_class( $mobile_pos );

    if ( $sticky ) {
        $classes[] = 'sidebar-sticky';
    }

    return $classes;
}
