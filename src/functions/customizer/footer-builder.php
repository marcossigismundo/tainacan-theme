<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Footer Builder - WordPress Customizer Module
 *
 * @package suspended Tainacan Interface
 * @since 2.9.0
 */

add_action( 'customize_register', 'tainacan_footer_builder_customizer' );

function tainacan_footer_builder_customizer( $wp_customize ) {

    // Panel: Footer Builder
    $wp_customize->add_panel( 'tainacan_footer_builder', array(
        'title'    => __( 'Footer Builder', 'flavor' ),
        'priority' => 40,
    ) );

    // =========================================================================
    // Section: Footer Top Widgets
    // =========================================================================
    $wp_customize->add_section( 'tainacan_footer_widgets', array(
        'title' => __( 'Footer Top Widgets', 'flavor' ),
        'panel' => 'tainacan_footer_builder',
    ) );

    // Enable footer widgets
    $wp_customize->add_setting( 'tainacan_footer_widgets_enable', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_footer_widgets_enable', array(
        'label'   => __( 'Enable Footer Widgets Area', 'flavor' ),
        'section' => 'tainacan_footer_widgets',
        'type'    => 'checkbox',
    ) );

    // Columns
    $wp_customize->add_setting( 'tainacan_footer_widgets_columns', array(
        'default'           => '3',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_footer_widgets_columns', array(
        'label'   => __( 'Number of Columns', 'flavor' ),
        'section' => 'tainacan_footer_widgets',
        'type'    => 'select',
        'choices' => array(
            '1' => __( '1 Column', 'flavor' ),
            '2' => __( '2 Columns', 'flavor' ),
            '3' => __( '3 Columns', 'flavor' ),
            '4' => __( '4 Columns', 'flavor' ),
        ),
    ) );

    // Background color
    $wp_customize->add_setting( 'tainacan_footer_widgets_bg_color', array(
        'default'           => '#2d2d2d',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_footer_widgets_bg_color', array(
        'label'   => __( 'Background Color', 'flavor' ),
        'section' => 'tainacan_footer_widgets',
    ) ) );

    // Text color
    $wp_customize->add_setting( 'tainacan_footer_widgets_text_color', array(
        'default'           => '#e0e0e0',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_footer_widgets_text_color', array(
        'label'   => __( 'Text Color', 'flavor' ),
        'section' => 'tainacan_footer_widgets',
    ) ) );

    // Heading color
    $wp_customize->add_setting( 'tainacan_footer_widgets_heading_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_footer_widgets_heading_color', array(
        'label'   => __( 'Heading Color', 'flavor' ),
        'section' => 'tainacan_footer_widgets',
    ) ) );

    // Link color
    $wp_customize->add_setting( 'tainacan_footer_widgets_link_color', array(
        'default'           => '#8abcc4',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_footer_widgets_link_color', array(
        'label'   => __( 'Link Color', 'flavor' ),
        'section' => 'tainacan_footer_widgets',
    ) ) );

    // Padding
    $wp_customize->add_setting( 'tainacan_footer_widgets_padding', array(
        'default'           => 50,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_footer_widgets_padding', array(
        'label'       => __( 'Vertical Padding (px)', 'flavor' ),
        'section'     => 'tainacan_footer_widgets',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 20,
            'max' => 100,
        ),
    ) );

    // =========================================================================
    // Section: Footer Bottom Bar
    // =========================================================================
    $wp_customize->add_section( 'tainacan_footer_bar', array(
        'title' => __( 'Footer Bottom Bar', 'flavor' ),
        'panel' => 'tainacan_footer_builder',
    ) );

    // Layout
    $wp_customize->add_setting( 'tainacan_footer_bar_layout', array(
        'default'           => 'left-right',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_footer_bar_layout', array(
        'label'   => __( 'Layout', 'flavor' ),
        'section' => 'tainacan_footer_bar',
        'type'    => 'select',
        'choices' => array(
            'left-right'     => __( 'Left - Right', 'flavor' ),
            'centered'       => __( 'Centered', 'flavor' ),
            'three-columns'  => __( 'Three Columns', 'flavor' ),
        ),
    ) );

    // Background color
    $wp_customize->add_setting( 'tainacan_footer_bar_bg_color', array(
        'default'           => '#222222',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_footer_bar_bg_color', array(
        'label'   => __( 'Background Color', 'flavor' ),
        'section' => 'tainacan_footer_bar',
    ) ) );

    // Text color
    $wp_customize->add_setting( 'tainacan_footer_bar_text_color', array(
        'default'           => '#aaaaaa',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_footer_bar_text_color', array(
        'label'   => __( 'Text Color', 'flavor' ),
        'section' => 'tainacan_footer_bar',
    ) ) );

    // Left content
    $wp_customize->add_setting( 'tainacan_footer_bar_left_content', array(
        'default'           => 'copyright',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_footer_bar_left_content', array(
        'label'   => __( 'Left Content', 'flavor' ),
        'section' => 'tainacan_footer_bar',
        'type'    => 'select',
        'choices' => array(
            'copyright'   => __( 'Copyright', 'flavor' ),
            'menu'        => __( 'Menu', 'flavor' ),
            'custom-text' => __( 'Custom Text', 'flavor' ),
        ),
    ) );

    // Right content
    $wp_customize->add_setting( 'tainacan_footer_bar_right_content', array(
        'default'           => 'social',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_footer_bar_right_content', array(
        'label'   => __( 'Right Content', 'flavor' ),
        'section' => 'tainacan_footer_bar',
        'type'    => 'select',
        'choices' => array(
            'social'      => __( 'Social Icons', 'flavor' ),
            'menu'        => __( 'Menu', 'flavor' ),
            'custom-text' => __( 'Custom Text', 'flavor' ),
            'none'        => __( 'None', 'flavor' ),
        ),
    ) );

    // Copyright text
    $wp_customize->add_setting( 'tainacan_footer_bar_copyright_text', array(
        'default'           => '&copy; {year} {site_name}',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tainacan_footer_bar_copyright_text', array(
        'label'       => __( 'Copyright Text', 'flavor' ),
        'description' => __( 'Use {year} and {site_name} as placeholders.', 'flavor' ),
        'section'     => 'tainacan_footer_bar',
        'type'        => 'text',
    ) );

    // Custom text
    $wp_customize->add_setting( 'tainacan_footer_bar_custom_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
    ) );
    $wp_customize->add_control( 'tainacan_footer_bar_custom_text', array(
        'label'   => __( 'Custom Text', 'flavor' ),
        'section' => 'tainacan_footer_bar',
        'type'    => 'text',
    ) );

    // =========================================================================
    // Section: Footer Visibility
    // =========================================================================
    $wp_customize->add_section( 'tainacan_footer_visibility', array(
        'title' => __( 'Footer Visibility', 'flavor' ),
        'panel' => 'tainacan_footer_builder',
    ) );

    // Hide widgets on mobile
    $wp_customize->add_setting( 'tainacan_footer_widgets_hide_mobile', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_footer_widgets_hide_mobile', array(
        'label'   => __( 'Hide Footer Widgets on Mobile', 'flavor' ),
        'section' => 'tainacan_footer_visibility',
        'type'    => 'checkbox',
    ) );

    // Footer reveal effect
    $wp_customize->add_setting( 'tainacan_footer_reveal_effect', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_footer_reveal_effect', array(
        'label'       => __( 'Footer Reveal on Scroll Effect', 'flavor' ),
        'description' => __( 'Footer is revealed as the user scrolls to the bottom of the page.', 'flavor' ),
        'section'     => 'tainacan_footer_visibility',
        'type'        => 'checkbox',
    ) );
}

/**
 * Output footer builder CSS.
 */
add_action( 'wp_head', 'tainacan_footer_builder_output_css', 20 );

function tainacan_footer_builder_output_css() {
    $widgets_bg       = get_theme_mod( 'tainacan_footer_widgets_bg_color', '#2d2d2d' );
    $widgets_text     = get_theme_mod( 'tainacan_footer_widgets_text_color', '#e0e0e0' );
    $widgets_heading  = get_theme_mod( 'tainacan_footer_widgets_heading_color', '#ffffff' );
    $widgets_link     = get_theme_mod( 'tainacan_footer_widgets_link_color', '#8abcc4' );
    $widgets_padding  = absint( get_theme_mod( 'tainacan_footer_widgets_padding', 50 ) );
    $widgets_columns  = get_theme_mod( 'tainacan_footer_widgets_columns', '3' );

    $bar_bg           = get_theme_mod( 'tainacan_footer_bar_bg_color', '#222222' );
    $bar_text         = get_theme_mod( 'tainacan_footer_bar_text_color', '#aaaaaa' );
    $bar_layout       = get_theme_mod( 'tainacan_footer_bar_layout', 'left-right' );

    $hide_mobile      = get_theme_mod( 'tainacan_footer_widgets_hide_mobile', false );
    $reveal_effect    = get_theme_mod( 'tainacan_footer_reveal_effect', false );

    $column_width = '33.333%';
    switch ( $widgets_columns ) {
        case '1': $column_width = '100%'; break;
        case '2': $column_width = '50%'; break;
        case '3': $column_width = '33.333%'; break;
        case '4': $column_width = '25%'; break;
    }

    $css = '<style id="tainacan-footer-builder-css">';

    // Footer widgets area
    $css .= '.tainacan-footer-widgets-area {';
    $css .= 'background-color:' . esc_attr( $widgets_bg ) . ';';
    $css .= 'color:' . esc_attr( $widgets_text ) . ';';
    $css .= 'padding:' . esc_attr( $widgets_padding ) . 'px 0;';
    $css .= '}';

    $css .= '.tainacan-footer-widgets-area .widget-column {';
    $css .= 'width:' . esc_attr( $column_width ) . ';';
    $css .= '}';

    $css .= '.tainacan-footer-widgets-area h1,.tainacan-footer-widgets-area h2,.tainacan-footer-widgets-area h3,';
    $css .= '.tainacan-footer-widgets-area h4,.tainacan-footer-widgets-area h5,.tainacan-footer-widgets-area h6,';
    $css .= '.tainacan-footer-widgets-area .widget-title {';
    $css .= 'color:' . esc_attr( $widgets_heading ) . ';';
    $css .= '}';

    $css .= '.tainacan-footer-widgets-area a {';
    $css .= 'color:' . esc_attr( $widgets_link ) . ';';
    $css .= '}';

    // Footer bottom bar
    $css .= '.tainacan-footer-bar {';
    $css .= 'background-color:' . esc_attr( $bar_bg ) . ';';
    $css .= 'color:' . esc_attr( $bar_text ) . ';';
    $css .= '}';

    $css .= '.tainacan-footer-bar a {';
    $css .= 'color:' . esc_attr( $bar_text ) . ';';
    $css .= '}';

    if ( 'centered' === $bar_layout ) {
        $css .= '.tainacan-footer-bar .footer-bar-inner {';
        $css .= 'justify-content:center;text-align:center;flex-direction:column;align-items:center;';
        $css .= '}';
    } elseif ( 'three-columns' === $bar_layout ) {
        $css .= '.tainacan-footer-bar .footer-bar-inner {';
        $css .= 'display:grid;grid-template-columns:1fr 1fr 1fr;';
        $css .= '}';
    }

    // Hide widgets on mobile
    if ( $hide_mobile ) {
        $css .= '@media (max-width: 768px) {';
        $css .= '.tainacan-footer-widgets-area { display: none; }';
        $css .= '}';
    }

    // Reveal effect
    if ( $reveal_effect ) {
        $css .= '.tainacan-footer-reveal { position: fixed; bottom: 0; left: 0; right: 0; z-index: -1; }';
        $css .= '.tainacan-footer-reveal ~ .site-content { margin-bottom: var(--footer-height, 400px); }';
    }

    $css .= '</style>';

    echo $css;
}

/**
 * Get footer copyright text with placeholders replaced.
 *
 * @return string
 */
function tainacan_get_footer_copyright() {
    $text = get_theme_mod( 'tainacan_footer_bar_copyright_text', '&copy; {year} {site_name}' );

    $text = str_replace( '{year}', date( 'Y' ), $text );
    $text = str_replace( '{site_name}', get_bloginfo( 'name' ), $text );

    return wp_kses_post( $text );
}
