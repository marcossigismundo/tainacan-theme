<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Advanced Pagination Customizer Settings
 *
 * Provides load-more and infinite-scroll pagination for blog/archive pages.
 * Does NOT apply to Tainacan items list (the plugin handles its own pagination).
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register Pagination customizer section and controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tainacan_pagination_advanced_customizer( $wp_customize ) {

	// Section
	$wp_customize->add_section( 'tainacan_pagination_settings', array(
		'title'    => __( 'Pagination Settings', 'flavor' ),
		'priority' => 40,
	) );

	// Pagination type
	$wp_customize->add_setting( 'tainacan_pagination_type', array(
		'default'           => 'standard',
		'sanitize_callback' => 'tainacan_sanitize_pagination_type',
	) );
	$wp_customize->add_control( 'tainacan_pagination_type', array(
		'label'   => __( 'Pagination Type', 'flavor' ),
		'section' => 'tainacan_pagination_settings',
		'type'    => 'select',
		'choices' => array(
			'standard'        => __( 'Standard (Page Numbers)', 'flavor' ),
			'load-more'       => __( 'Load More Button', 'flavor' ),
			'infinite-scroll' => __( 'Infinite Scroll', 'flavor' ),
		),
	) );

	// Load more button text
	$wp_customize->add_setting( 'tainacan_pagination_load_more_text', array(
		'default'           => __( 'Load More', 'flavor' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'tainacan_pagination_load_more_text', array(
		'label'   => __( 'Load More Button Text', 'flavor' ),
		'section' => 'tainacan_pagination_settings',
		'type'    => 'text',
	) );

	// Loading text
	$wp_customize->add_setting( 'tainacan_pagination_loading_text', array(
		'default'           => __( 'Loading...', 'flavor' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'tainacan_pagination_loading_text', array(
		'label'   => __( 'Loading Text', 'flavor' ),
		'section' => 'tainacan_pagination_settings',
		'type'    => 'text',
	) );

	// No more items text
	$wp_customize->add_setting( 'tainacan_pagination_no_more_text', array(
		'default'           => __( 'No more items', 'flavor' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'tainacan_pagination_no_more_text', array(
		'label'   => __( 'No More Items Text', 'flavor' ),
		'section' => 'tainacan_pagination_settings',
		'type'    => 'text',
	) );

	// Scroll threshold (px)
	$wp_customize->add_setting( 'tainacan_pagination_scroll_threshold', array(
		'default'           => 300,
		'sanitize_callback' => 'tainacan_sanitize_scroll_threshold',
	) );
	$wp_customize->add_control( 'tainacan_pagination_scroll_threshold', array(
		'label'       => __( 'Infinite Scroll Threshold (px)', 'flavor' ),
		'description' => __( 'Distance from bottom of page to trigger loading (100-500px).', 'flavor' ),
		'section'     => 'tainacan_pagination_settings',
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 100,
			'max'  => 500,
			'step' => 10,
		),
	) );

	// Animate new items
	$wp_customize->add_setting( 'tainacan_pagination_animation', array(
		'default'           => true,
		'sanitize_callback' => 'tainacan_sanitize_checkbox',
	) );
	$wp_customize->add_control( 'tainacan_pagination_animation', array(
		'label'   => __( 'Animate New Items', 'flavor' ),
		'section' => 'tainacan_pagination_settings',
		'type'    => 'checkbox',
	) );
}
add_action( 'customize_register', 'tainacan_pagination_advanced_customizer' );

/**
 * Sanitize pagination type.
 *
 * @param string $value The value to sanitize.
 * @return string
 */
function tainacan_sanitize_pagination_type( $value ) {
	$valid = array( 'standard', 'load-more', 'infinite-scroll' );
	return in_array( $value, $valid, true ) ? $value : 'standard';
}

/**
 * Sanitize scroll threshold value.
 *
 * @param int $value The value to sanitize.
 * @return int
 */
function tainacan_sanitize_scroll_threshold( $value ) {
	$value = absint( $value );
	if ( $value < 100 ) {
		return 100;
	}
	if ( $value > 500 ) {
		return 500;
	}
	return $value;
}

/**
 * Sanitize checkbox (reuse existing if available, define if not).
 */
if ( ! function_exists( 'tainacan_sanitize_checkbox' ) ) {
	function tainacan_sanitize_checkbox( $value ) {
		return ( isset( $value ) && true == $value ) ? true : false;
	}
}

/**
 * Enqueue infinite scroll / load-more JS on blog and archive pages.
 */
function tainacan_enqueue_pagination_advanced() {
	$type = get_theme_mod( 'tainacan_pagination_type', 'standard' );

	// Only enqueue for non-standard pagination on appropriate pages
	if ( 'standard' === $type ) {
		return;
	}

	// Only on blog/archive pages, not Tainacan item lists
	if ( ! is_home() && ! is_archive() && ! is_search() ) {
		return;
	}

	// Skip Tainacan archive pages (the plugin handles its own pagination)
	if ( function_exists( 'tainacan_get_api_posttype' ) && is_post_type_archive( 'tainacan-item' ) ) {
		return;
	}

	// Determine next page URL
	$next_page_url = get_next_posts_page_link();
	global $wp_query;
	$has_next = ( $wp_query->current_post + 1 < $wp_query->found_posts ) && ( $wp_query->max_num_pages > 1 );

	if ( get_query_var( 'paged' ) ) {
		$current_page = get_query_var( 'paged' );
	} else {
		$current_page = 1;
	}

	$has_next_page = $current_page < $wp_query->max_num_pages;

	wp_enqueue_script(
		'tainacan-infinite-scroll',
		get_template_directory_uri() . '/assets/js/infinite-scroll.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script( 'tainacan-infinite-scroll', 'tainacanPagination', array(
		'type'         => sanitize_text_field( $type ),
		'loadMoreText' => sanitize_text_field( get_theme_mod( 'tainacan_pagination_load_more_text', __( 'Load More', 'flavor' ) ) ),
		'loadingText'  => sanitize_text_field( get_theme_mod( 'tainacan_pagination_loading_text', __( 'Loading...', 'flavor' ) ) ),
		'noMoreText'   => sanitize_text_field( get_theme_mod( 'tainacan_pagination_no_more_text', __( 'No more items', 'flavor' ) ) ),
		'threshold'    => intval( get_theme_mod( 'tainacan_pagination_scroll_threshold', 300 ) ),
		'animate'      => (bool) get_theme_mod( 'tainacan_pagination_animation', true ),
		'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
		'nonce'        => wp_create_nonce( 'tainacan_pagination_nonce' ),
		'nextPageUrl'  => $has_next_page ? get_next_posts_page_link() : '',
		'maxPages'     => intval( $wp_query->max_num_pages ),
		'currentPage'  => intval( $current_page ),
	) );
}
add_action( 'wp_enqueue_scripts', 'tainacan_enqueue_pagination_advanced' );
