<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Customizer Link Styles
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register link styles Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tainacan_customizer_link_styles( $wp_customize ) {

    $wp_customize->add_section( 'tainacan_link_styles', array(
        'title'    => __( 'Link Styles', 'flavor' ),
        'priority' => 37,
    ) );

    // --- Link Color ---
    $wp_customize->add_setting( 'tainacan_link_color', array(
        'default'           => '#187181',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_link_color', array(
        'label'   => __( 'Link color', 'flavor' ),
        'section' => 'tainacan_link_styles',
    ) ) );

    // --- Link Hover Color ---
    $wp_customize->add_setting( 'tainacan_link_hover_color', array(
        'default'           => '#125a68',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_link_hover_color', array(
        'label'   => __( 'Link hover color', 'flavor' ),
        'section' => 'tainacan_link_styles',
    ) ) );

    // --- Link Style ---
    $wp_customize->add_setting( 'tainacan_link_style', array(
        'default'           => 'underline-hover',
        'sanitize_callback' => 'tainacan_sanitize_link_style',
    ) );
    $wp_customize->add_control( 'tainacan_link_style', array(
        'label'   => __( 'Link decoration style', 'flavor' ),
        'section' => 'tainacan_link_styles',
        'type'    => 'select',
        'choices' => array(
            'none'           => __( 'None', 'flavor' ),
            'underline'      => __( 'Always underlined', 'flavor' ),
            'underline-hover' => __( 'Underline on hover', 'flavor' ),
            'color-only'     => __( 'Color change on hover', 'flavor' ),
            'highlight'      => __( 'Background highlight on hover', 'flavor' ),
        ),
    ) );

    // --- Smooth Transition ---
    $wp_customize->add_setting( 'tainacan_link_transition', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_link_transition', array(
        'label'   => __( 'Smooth hover transition', 'flavor' ),
        'section' => 'tainacan_link_styles',
        'type'    => 'checkbox',
    ) );
}
add_action( 'customize_register', 'tainacan_customizer_link_styles' );

/**
 * Sanitize link style select.
 *
 * @param string $value Value to sanitize.
 * @return string Sanitized value.
 */
function tainacan_sanitize_link_style( $value ) {
    $valid = array( 'none', 'underline', 'underline-hover', 'color-only', 'highlight' );
    return in_array( $value, $valid, true ) ? $value : 'underline-hover';
}

/**
 * Output link styles CSS.
 */
function tainacan_link_styles_output_css() {
    $link_color  = get_theme_mod( 'tainacan_link_color', '#187181' );
    $hover_color = get_theme_mod( 'tainacan_link_hover_color', '#125a68' );
    $style       = get_theme_mod( 'tainacan_link_style', 'underline-hover' );
    $transition  = get_theme_mod( 'tainacan_link_transition', true );

    $selectors       = '.entry-content a, .tainacan-item-metadata a, article a';
    $hover_selectors = '.entry-content a:hover, .tainacan-item-metadata a:hover, article a:hover';

    $css = '';

    // Base styles.
    $css .= $selectors . '{';
    $css .= 'color:' . esc_attr( $link_color ) . ';';

    if ( $transition ) {
        $css .= 'transition: color .2s ease, text-decoration-color .2s ease, background-color .2s ease;';
    }

    switch ( $style ) {
        case 'none':
            $css .= 'text-decoration:none;';
            break;
        case 'underline':
            $css .= 'text-decoration:underline;';
            break;
        case 'underline-hover':
            $css .= 'text-decoration:none;';
            break;
        case 'color-only':
            $css .= 'text-decoration:none;';
            break;
        case 'highlight':
            $css .= 'text-decoration:none;border-radius:2px;';
            break;
    }

    $css .= '}';

    // Hover styles.
    $css .= $hover_selectors . '{';
    $css .= 'color:' . esc_attr( $hover_color ) . ';';

    switch ( $style ) {
        case 'none':
            $css .= 'text-decoration:none;';
            break;
        case 'underline':
            $css .= 'text-decoration:underline;';
            break;
        case 'underline-hover':
            $css .= 'text-decoration:underline;';
            break;
        case 'color-only':
            $css .= 'text-decoration:none;';
            break;
        case 'highlight':
            $css .= 'background-color:rgba(24,113,129,0.08);text-decoration:none;';
            break;
    }

    $css .= '}';

    if ( ! empty( $css ) ) {
        echo '<style id="tainacan-link-styles">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
}
add_action( 'wp_head', 'tainacan_link_styles_output_css', 20 );
