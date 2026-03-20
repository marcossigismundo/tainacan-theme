<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Scroll Animations Customizer Settings
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register Scroll Animations customizer section and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tainacan_scroll_animations_customizer( $wp_customize ) {

	// Section
	$wp_customize->add_section( 'tainacan_scroll_animations', array(
		'title'    => __( 'Scroll Animations', 'flavor' ),
		'priority' => 39,
	) );

	// Enable scroll animations
	$wp_customize->add_setting( 'tainacan_scroll_animations_enable', array(
		'default'           => false,
		'sanitize_callback' => 'tainacan_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'tainacan_scroll_animations_enable', array(
		'label'   => __( 'Enable Scroll Animations', 'flavor' ),
		'section' => 'tainacan_scroll_animations',
		'type'    => 'checkbox',
	) );

	// Animation type
	$wp_customize->add_setting( 'tainacan_scroll_animation_type', array(
		'default'           => 'fade-up',
		'sanitize_callback' => 'tainacan_sanitize_scroll_animation_type',
	) );
	$wp_customize->add_control( 'tainacan_scroll_animation_type', array(
		'label'   => __( 'Animation Type', 'flavor' ),
		'section' => 'tainacan_scroll_animations',
		'type'    => 'select',
		'choices' => array(
			'fade-up'    => __( 'Fade Up', 'flavor' ),
			'fade-in'    => __( 'Fade In', 'flavor' ),
			'slide-up'   => __( 'Slide Up', 'flavor' ),
			'slide-left' => __( 'Slide Left', 'flavor' ),
			'zoom-in'    => __( 'Zoom In', 'flavor' ),
		),
	) );

	// Animation duration
	$wp_customize->add_setting( 'tainacan_scroll_animation_duration', array(
		'default'           => '400',
		'sanitize_callback' => 'tainacan_sanitize_scroll_animation_duration',
	) );
	$wp_customize->add_control( 'tainacan_scroll_animation_duration', array(
		'label'       => __( 'Animation Duration (ms)', 'flavor' ),
		'section'     => 'tainacan_scroll_animations',
		'type'        => 'select',
		'choices'     => array(
			'200' => '200ms',
			'400' => '400ms',
			'600' => '600ms',
			'800' => '800ms',
		),
	) );

	// Stagger delay
	$wp_customize->add_setting( 'tainacan_scroll_animation_delay', array(
		'default'           => '50',
		'sanitize_callback' => 'tainacan_sanitize_scroll_animation_delay',
	) );
	$wp_customize->add_control( 'tainacan_scroll_animation_delay', array(
		'label'       => __( 'Stagger Delay Between Items (ms)', 'flavor' ),
		'section'     => 'tainacan_scroll_animations',
		'type'        => 'select',
		'choices'     => array(
			'0'   => __( 'None', 'flavor' ),
			'50'  => '50ms',
			'100' => '100ms',
			'150' => '150ms',
		),
	) );

	// Animation targets
	$wp_customize->add_setting( 'tainacan_scroll_animation_targets', array(
		'default'           => 'cards-only',
		'sanitize_callback' => 'tainacan_sanitize_scroll_animation_targets',
	) );
	$wp_customize->add_control( 'tainacan_scroll_animation_targets', array(
		'label'   => __( 'Animation Targets', 'flavor' ),
		'section' => 'tainacan_scroll_animations',
		'type'    => 'select',
		'choices' => array(
			'cards-only'     => __( 'Cards Only', 'flavor' ),
			'all-sections'   => __( 'All Sections', 'flavor' ),
			'headings-cards' => __( 'Headings & Cards', 'flavor' ),
		),
	) );
}
add_action( 'customize_register', 'tainacan_scroll_animations_customizer' );

/**
 * Sanitize callbacks for scroll animation settings.
 */
function tainacan_sanitize_scroll_animation_type( $value ) {
	$valid = array( 'fade-up', 'fade-in', 'slide-up', 'slide-left', 'zoom-in' );
	return in_array( $value, $valid, true ) ? $value : 'fade-up';
}

function tainacan_sanitize_scroll_animation_duration( $value ) {
	$valid = array( '200', '400', '600', '800' );
	return in_array( (string) $value, $valid, true ) ? $value : '400';
}

function tainacan_sanitize_scroll_animation_delay( $value ) {
	$valid = array( '0', '50', '100', '150' );
	return in_array( (string) $value, $valid, true ) ? $value : '50';
}

function tainacan_sanitize_scroll_animation_targets( $value ) {
	$valid = array( 'cards-only', 'all-sections', 'headings-cards' );
	return in_array( $value, $valid, true ) ? $value : 'cards-only';
}

/**
 * Output CSS for scroll animation initial and visible states.
 */
function tainacan_scroll_animation_output_css() {
	if ( ! get_theme_mod( 'tainacan_scroll_animations_enable', false ) ) {
		return;
	}

	$type     = get_theme_mod( 'tainacan_scroll_animation_type', 'fade-up' );
	$duration = get_theme_mod( 'tainacan_scroll_animation_duration', '400' );

	// Build initial (hidden) state based on animation type
	$initial_styles = '';
	$visible_styles = '';

	switch ( $type ) {
		case 'fade-up':
			$initial_styles = 'opacity: 0; transform: translateY(30px);';
			$visible_styles = 'opacity: 1; transform: translateY(0);';
			break;
		case 'fade-in':
			$initial_styles = 'opacity: 0;';
			$visible_styles = 'opacity: 1;';
			break;
		case 'slide-up':
			$initial_styles = 'opacity: 0; transform: translateY(60px);';
			$visible_styles = 'opacity: 1; transform: translateY(0);';
			break;
		case 'slide-left':
			$initial_styles = 'opacity: 0; transform: translateX(40px);';
			$visible_styles = 'opacity: 1; transform: translateX(0);';
			break;
		case 'zoom-in':
			$initial_styles = 'opacity: 0; transform: scale(0.85);';
			$visible_styles = 'opacity: 1; transform: scale(1);';
			break;
	}

	$duration_s = intval( $duration ) / 1000;

	echo '<style id="tainacan-scroll-animations-css">' . "\n";
	echo '.tainacan-animate {' . "\n";
	echo '  ' . $initial_styles . "\n";
	echo '  transition: all ' . esc_attr( $duration_s ) . 's ease-out;' . "\n";
	echo '  will-change: opacity, transform;' . "\n";
	echo '}' . "\n";
	echo '.tainacan-animate.is-visible {' . "\n";
	echo '  ' . $visible_styles . "\n";
	echo '}' . "\n";
	echo '@media (prefers-reduced-motion: reduce) {' . "\n";
	echo '  .tainacan-animate {' . "\n";
	echo '    opacity: 1 !important;' . "\n";
	echo '    transform: none !important;' . "\n";
	echo '    transition: none !important;' . "\n";
	echo '  }' . "\n";
	echo '}' . "\n";
	echo '</style>' . "\n";
}
add_action( 'wp_head', 'tainacan_scroll_animation_output_css' );

/**
 * Conditionally enqueue scroll animations JS when animations are enabled.
 */
function tainacan_enqueue_scroll_animations() {
	if ( ! get_theme_mod( 'tainacan_scroll_animations_enable', false ) ) {
		return;
	}

	wp_enqueue_script(
		'tainacan-scroll-animations',
		get_template_directory_uri() . '/assets/js/scroll-animations.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script( 'tainacan-scroll-animations', 'tainacanAnimations', array(
		'type'    => sanitize_text_field( get_theme_mod( 'tainacan_scroll_animation_type', 'fade-up' ) ),
		'duration' => intval( get_theme_mod( 'tainacan_scroll_animation_duration', '400' ) ),
		'delay'   => intval( get_theme_mod( 'tainacan_scroll_animation_delay', '50' ) ),
		'targets' => sanitize_text_field( get_theme_mod( 'tainacan_scroll_animation_targets', 'cards-only' ) ),
	) );
}
add_action( 'wp_enqueue_scripts', 'tainacan_enqueue_scroll_animations' );
