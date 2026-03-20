<?php
/**
 * Tainacan Interface - Scroll to Top Customizer
 *
 * @package Tainacan_Interface
 * @since 2.10.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function tainacan_scroll_to_top_customizer( $wp_customize ) {

	$wp_customize->add_section( 'tainacan_scroll_to_top', array(
		'title'    => __( 'Scroll to Top', 'tainacan-interface' ),
		'priority' => 42,
	) );

	// Enable
	$wp_customize->add_setting( 'tainacan_scroll_top_enable', array(
		'default'           => true,
		'sanitize_callback' => 'tainacan_callback_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'tainacan_scroll_top_enable', array(
		'label'   => __( 'Show scroll to top button', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
		'type'    => 'checkbox',
	) );

	// Shape
	$wp_customize->add_setting( 'tainacan_scroll_top_shape', array(
		'default'           => 'circle',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'tainacan_scroll_top_shape', array(
		'label'   => __( 'Button shape', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
		'type'    => 'select',
		'choices' => array(
			'circle'  => __( 'Circle', 'tainacan-interface' ),
			'square'  => __( 'Square', 'tainacan-interface' ),
			'rounded' => __( 'Rounded square', 'tainacan-interface' ),
		),
	) );

	// Icon style
	$wp_customize->add_setting( 'tainacan_scroll_top_icon', array(
		'default'           => 'arrow-up',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'tainacan_scroll_top_icon', array(
		'label'   => __( 'Icon style', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
		'type'    => 'select',
		'choices' => array(
			'arrow-up'    => __( 'Arrow up (↑)', 'tainacan-interface' ),
			'chevron-up'  => __( 'Chevron up (^)', 'tainacan-interface' ),
			'caret-up'    => __( 'Caret up (▲)', 'tainacan-interface' ),
			'double-up'   => __( 'Double arrow (⇑)', 'tainacan-interface' ),
			'rocket'      => __( 'Rocket (🚀)', 'tainacan-interface' ),
			'top-text'    => __( 'Text "TOP"', 'tainacan-interface' ),
		),
	) );

	// Size
	$wp_customize->add_setting( 'tainacan_scroll_top_size', array(
		'default'           => 44,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'tainacan_scroll_top_size', array(
		'label'       => __( 'Button size (px)', 'tainacan-interface' ),
		'section'     => 'tainacan_scroll_to_top',
		'type'        => 'number',
		'input_attrs' => array( 'min' => 30, 'max' => 70, 'step' => 2 ),
	) );

	// Position
	$wp_customize->add_setting( 'tainacan_scroll_top_position', array(
		'default'           => 'right',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'tainacan_scroll_top_position', array(
		'label'   => __( 'Position', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
		'type'    => 'select',
		'choices' => array(
			'left'  => __( 'Bottom left', 'tainacan-interface' ),
			'right' => __( 'Bottom right', 'tainacan-interface' ),
		),
	) );

	// Background color
	$wp_customize->add_setting( 'tainacan_scroll_top_bg_color', array(
		'default'           => '#187181',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_scroll_top_bg_color', array(
		'label'   => __( 'Background color', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
	) ) );

	// Text/icon color
	$wp_customize->add_setting( 'tainacan_scroll_top_text_color', array(
		'default'           => '#ffffff',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_scroll_top_text_color', array(
		'label'   => __( 'Icon color', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
	) ) );

	// Hide on mobile
	$wp_customize->add_setting( 'tainacan_scroll_top_hide_mobile', array(
		'default'           => false,
		'sanitize_callback' => 'tainacan_callback_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'tainacan_scroll_top_hide_mobile', array(
		'label'   => __( 'Hide on mobile devices', 'tainacan-interface' ),
		'section' => 'tainacan_scroll_to_top',
		'type'    => 'checkbox',
	) );
}
add_action( 'customize_register', 'tainacan_scroll_to_top_customizer' );

/**
 * Output scroll to top CSS
 */
function tainacan_scroll_to_top_output_css() {
	if ( ! get_theme_mod( 'tainacan_scroll_top_enable', true ) ) {
		return;
	}

	$shape    = get_theme_mod( 'tainacan_scroll_top_shape', 'circle' );
	$size     = absint( get_theme_mod( 'tainacan_scroll_top_size', 44 ) );
	$position = get_theme_mod( 'tainacan_scroll_top_position', 'right' );
	$bg       = sanitize_hex_color( get_theme_mod( 'tainacan_scroll_top_bg_color', '#187181' ) );
	$color    = sanitize_hex_color( get_theme_mod( 'tainacan_scroll_top_text_color', '#ffffff' ) );
	$hide_mob = get_theme_mod( 'tainacan_scroll_top_hide_mobile', false );

	$radius = '50%';
	if ( $shape === 'square' ) $radius = '0';
	if ( $shape === 'rounded' ) $radius = '8px';

	$pos_css = $position === 'left' ? 'left: 20px; right: auto;' : 'right: 20px; left: auto;';

	$css = ".tainacan-scroll-to-top{position:fixed;bottom:20px;{$pos_css}z-index:9999;width:{$size}px;height:{$size}px;";
	$css .= "border-radius:{$radius};background:{$bg};color:{$color};border:none;cursor:pointer;";
	$css .= "display:flex;align-items:center;justify-content:center;font-size:" . round($size * 0.4) . "px;";
	$css .= "opacity:0;visibility:hidden;transition:opacity .3s,visibility .3s,transform .3s;transform:translateY(10px);";
	$css .= "box-shadow:0 2px 8px rgba(0,0,0,.15);}";
	$css .= ".tainacan-scroll-to-top.is-visible{opacity:1;visibility:visible;transform:translateY(0);}";
	$css .= ".tainacan-scroll-to-top:hover{opacity:.85;transform:translateY(-2px);}";

	if ( $hide_mob ) {
		$css .= "@media(max-width:767px){.tainacan-scroll-to-top{display:none!important;}}";
	}

	echo '<style id="tainacan-scroll-to-top-css">' . $css . '</style>' . "\n";
}
add_action( 'wp_head', 'tainacan_scroll_to_top_output_css', 20 );
