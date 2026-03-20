<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Form Elements - WordPress Customizer Module
 *
 * @package Tainacan Interface
 * @since 2.9.0
 */

add_action( 'customize_register', 'tainacan_form_elements_customizer' );

function tainacan_form_elements_customizer( $wp_customize ) {

    // Section: Form Elements
    $wp_customize->add_section( 'tainacan_form_elements', array(
        'title'    => __( 'Form Elements', 'flavor' ),
        'priority' => 38,
    ) );

    // Input height
    $wp_customize->add_setting( 'tainacan_form_input_height', array(
        'default'           => 40,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_form_input_height', array(
        'label'       => __( 'Input Height (px)', 'flavor' ),
        'section'     => 'tainacan_form_elements',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 30,
            'max' => 60,
        ),
    ) );

    // Border radius
    $wp_customize->add_setting( 'tainacan_form_border_radius', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_form_border_radius', array(
        'label'       => __( 'Border Radius (px)', 'flavor' ),
        'section'     => 'tainacan_form_elements',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 25,
        ),
    ) );

    // Border color
    $wp_customize->add_setting( 'tainacan_form_border_color', array(
        'default'           => '#dee2e6',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_form_border_color', array(
        'label'   => __( 'Border Color', 'flavor' ),
        'section' => 'tainacan_form_elements',
    ) ) );

    // Focus color
    $wp_customize->add_setting( 'tainacan_form_focus_color', array(
        'default'           => '#187181',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_form_focus_color', array(
        'label'       => __( 'Focus Ring Color', 'flavor' ),
        'section'     => 'tainacan_form_elements',
    ) ) );

    // Background color
    $wp_customize->add_setting( 'tainacan_form_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_form_bg_color', array(
        'label'   => __( 'Background Color', 'flavor' ),
        'section' => 'tainacan_form_elements',
    ) ) );

    // Text color
    $wp_customize->add_setting( 'tainacan_form_text_color', array(
        'default'           => '#495057',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_form_text_color', array(
        'label'   => __( 'Text Color', 'flavor' ),
        'section' => 'tainacan_form_elements',
    ) ) );

    // Placeholder color
    $wp_customize->add_setting( 'tainacan_form_placeholder_color', array(
        'default'           => '#adb5bd',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_form_placeholder_color', array(
        'label'   => __( 'Placeholder Color', 'flavor' ),
        'section' => 'tainacan_form_elements',
    ) ) );

    // Button style
    $wp_customize->add_setting( 'tainacan_form_button_style', array(
        'default'           => 'filled',
        'sanitize_callback' => 'tainacan_sanitize_select',
    ) );
    $wp_customize->add_control( 'tainacan_form_button_style', array(
        'label'   => __( 'Button Style', 'flavor' ),
        'section' => 'tainacan_form_elements',
        'type'    => 'select',
        'choices' => array(
            'filled'   => __( 'Filled', 'flavor' ),
            'outlined' => __( 'Outlined', 'flavor' ),
            'rounded'  => __( 'Rounded', 'flavor' ),
        ),
    ) );

    // Button border radius
    $wp_customize->add_setting( 'tainacan_form_button_radius', array(
        'default'           => 4,
        'sanitize_callback' => 'absint',
    ) );
    $wp_customize->add_control( 'tainacan_form_button_radius', array(
        'label'       => __( 'Button Border Radius (px)', 'flavor' ),
        'section'     => 'tainacan_form_elements',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 0,
            'max' => 25,
        ),
    ) );
}

/**
 * Output form elements CSS.
 */
add_action( 'wp_head', 'tainacan_form_elements_output_css', 20 );

function tainacan_form_elements_output_css() {
    $input_height     = absint( get_theme_mod( 'tainacan_form_input_height', 40 ) );
    $border_radius    = absint( get_theme_mod( 'tainacan_form_border_radius', 4 ) );
    $border_color     = get_theme_mod( 'tainacan_form_border_color', '#dee2e6' );
    $focus_color      = get_theme_mod( 'tainacan_form_focus_color', '#187181' );
    $bg_color         = get_theme_mod( 'tainacan_form_bg_color', '#ffffff' );
    $text_color       = get_theme_mod( 'tainacan_form_text_color', '#495057' );
    $placeholder_color = get_theme_mod( 'tainacan_form_placeholder_color', '#adb5bd' );
    $button_style     = get_theme_mod( 'tainacan_form_button_style', 'filled' );
    $button_radius    = absint( get_theme_mod( 'tainacan_form_button_radius', 4 ) );

    $css = '<style id="tainacan-form-elements-css">';

    // Input, textarea, select base styles
    $css .= 'input[type="text"],input[type="email"],input[type="url"],input[type="password"],';
    $css .= 'input[type="search"],input[type="number"],input[type="tel"],input[type="date"],';
    $css .= 'input[type="datetime-local"],input[type="month"],input[type="week"],input[type="time"],';
    $css .= 'textarea,select,.form-control {';
    $css .= 'height:' . esc_attr( $input_height ) . 'px;';
    $css .= 'border-radius:' . esc_attr( $border_radius ) . 'px;';
    $css .= 'border:1px solid ' . esc_attr( $border_color ) . ';';
    $css .= 'background-color:' . esc_attr( $bg_color ) . ';';
    $css .= 'color:' . esc_attr( $text_color ) . ';';
    $css .= 'transition:border-color 0.2s ease,box-shadow 0.2s ease;';
    $css .= '}';

    // Textarea height override (auto height)
    $css .= 'textarea,.form-control[rows] { height: auto; min-height:' . esc_attr( $input_height ) . 'px; }';

    // Placeholder styles
    $css .= '::placeholder { color:' . esc_attr( $placeholder_color ) . '; opacity:1; }';
    $css .= '::-webkit-input-placeholder { color:' . esc_attr( $placeholder_color ) . '; }';
    $css .= '::-moz-placeholder { color:' . esc_attr( $placeholder_color ) . '; opacity:1; }';
    $css .= ':-ms-input-placeholder { color:' . esc_attr( $placeholder_color ) . '; }';

    // Focus styles
    $css .= 'input[type="text"]:focus,input[type="email"]:focus,input[type="url"]:focus,';
    $css .= 'input[type="password"]:focus,input[type="search"]:focus,input[type="number"]:focus,';
    $css .= 'input[type="tel"]:focus,input[type="date"]:focus,input[type="datetime-local"]:focus,';
    $css .= 'input[type="month"]:focus,input[type="week"]:focus,input[type="time"]:focus,';
    $css .= 'textarea:focus,select:focus,.form-control:focus {';
    $css .= 'border-color:' . esc_attr( $focus_color ) . ';';
    $css .= 'box-shadow:0 0 0 3px ' . esc_attr( $focus_color ) . '33;';
    $css .= 'outline:none;';
    $css .= '}';

    // Select dropdown arrow
    $css .= 'select {';
    $css .= 'appearance:none;-webkit-appearance:none;';
    $css .= 'background-image:url("data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'12\' height=\'12\' viewBox=\'0 0 12 12\'%3E%3Cpath fill=\'%23' . ltrim( esc_attr( $text_color ), '#' ) . '\' d=\'M6 8L1 3h10z\'/%3E%3C/svg%3E");';
    $css .= 'background-repeat:no-repeat;background-position:right 12px center;';
    $css .= 'padding-right:36px;';
    $css .= '}';

    // Button base styles
    $css .= '.btn,button,input[type="submit"],input[type="button"],input[type="reset"],.wp-block-button__link {';
    $css .= 'border-radius:' . esc_attr( $button_radius ) . 'px;';
    $css .= 'cursor:pointer;';
    $css .= 'transition:all 0.2s ease;';
    $css .= 'display:inline-flex;align-items:center;justify-content:center;';
    $css .= 'height:' . esc_attr( $input_height ) . 'px;';
    $css .= 'padding:0 20px;';
    $css .= 'font-size:inherit;line-height:1;';
    $css .= '}';

    // Button style variations
    switch ( $button_style ) {
        case 'outlined':
            $css .= '.btn,button,input[type="submit"],input[type="button"],input[type="reset"],.wp-block-button__link {';
            $css .= 'background:transparent;';
            $css .= 'border:2px solid ' . esc_attr( $focus_color ) . ';';
            $css .= 'color:' . esc_attr( $focus_color ) . ';';
            $css .= '}';
            $css .= '.btn:hover,button:hover,input[type="submit"]:hover,input[type="button"]:hover,.wp-block-button__link:hover {';
            $css .= 'background-color:' . esc_attr( $focus_color ) . ';';
            $css .= 'color:#ffffff;';
            $css .= '}';
            break;
        case 'rounded':
            $css .= '.btn,button,input[type="submit"],input[type="button"],input[type="reset"],.wp-block-button__link {';
            $css .= 'border-radius:50px;';
            $css .= '}';
            break;
        case 'filled':
        default:
            // Filled is the default WordPress/theme behavior; no extra overrides needed.
            break;
    }

    // Checkbox and radio styling
    $css .= 'input[type="checkbox"],input[type="radio"] {';
    $css .= 'accent-color:' . esc_attr( $focus_color ) . ';';
    $css .= '}';

    // Search form specific
    $css .= '.search-form .search-field {';
    $css .= 'height:' . esc_attr( $input_height ) . 'px;';
    $css .= 'border-radius:' . esc_attr( $border_radius ) . 'px;';
    $css .= '}';

    // WooCommerce form compatibility
    $css .= '.woocommerce form .input-text,.woocommerce form .form-row input {';
    $css .= 'height:' . esc_attr( $input_height ) . 'px;';
    $css .= 'border-radius:' . esc_attr( $border_radius ) . 'px;';
    $css .= 'border-color:' . esc_attr( $border_color ) . ';';
    $css .= '}';

    $css .= '</style>';

    echo $css;
}
