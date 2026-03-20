<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Customizer Breadcrumb Settings
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * Register breadcrumb Customizer controls.
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager instance.
 */
function tainacan_customizer_breadcrumb_settings( $wp_customize ) {

    $wp_customize->add_section( 'tainacan_breadcrumb_settings', array(
        'title'    => __( 'Breadcrumb Settings', 'flavor' ),
        'priority' => 36,
    ) );

    // --- Enable ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_enable', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
    ) );
    $wp_customize->add_control( 'tainacan_breadcrumb_enable', array(
        'label'   => __( 'Enable breadcrumbs', 'flavor' ),
        'section' => 'tainacan_breadcrumb_settings',
        'type'    => 'checkbox',
    ) );

    // --- Separator ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_separator', array(
        'default'           => 'rsaquo',
        'sanitize_callback' => 'tainacan_sanitize_breadcrumb_separator',
    ) );
    $wp_customize->add_control( 'tainacan_breadcrumb_separator', array(
        'label'   => __( 'Separator', 'flavor' ),
        'section' => 'tainacan_breadcrumb_settings',
        'type'    => 'select',
        'choices' => array(
            'rsaquo'  => '› (rsaquo)',
            'raquo'   => '» (raquo)',
            'slash'   => '/ (slash)',
            'arrow'   => '→ (arrow)',
            'chevron' => '> (chevron)',
            'pipe'    => '| (pipe)',
        ),
    ) );

    // --- Font Size ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_font_size', array(
        'default'           => 13,
        'sanitize_callback' => 'tainacan_sanitize_breadcrumb_font_size',
    ) );
    $wp_customize->add_control( 'tainacan_breadcrumb_font_size', array(
        'label'       => __( 'Font size (px)', 'flavor' ),
        'section'     => 'tainacan_breadcrumb_settings',
        'type'        => 'number',
        'input_attrs' => array(
            'min' => 11,
            'max' => 16,
        ),
    ) );

    // --- Text Color ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_text_color', array(
        'default'           => '#6c757d',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_breadcrumb_text_color', array(
        'label'   => __( 'Text color', 'flavor' ),
        'section' => 'tainacan_breadcrumb_settings',
    ) ) );

    // --- Link Color ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_link_color', array(
        'default'           => '#187181',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_breadcrumb_link_color', array(
        'label'   => __( 'Link color', 'flavor' ),
        'section' => 'tainacan_breadcrumb_settings',
    ) ) );

    // --- Separator Color ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_separator_color', array(
        'default'           => '#adb5bd',
        'sanitize_callback' => 'sanitize_hex_color',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_breadcrumb_separator_color', array(
        'label'   => __( 'Separator color', 'flavor' ),
        'section' => 'tainacan_breadcrumb_settings',
    ) ) );

    // --- SEO Source ---
    $wp_customize->add_setting( 'tainacan_breadcrumb_seo_source', array(
        'default'           => 'auto',
        'sanitize_callback' => 'tainacan_sanitize_breadcrumb_seo_source',
    ) );
    $wp_customize->add_control( 'tainacan_breadcrumb_seo_source', array(
        'label'   => __( 'Breadcrumb source', 'flavor' ),
        'section' => 'tainacan_breadcrumb_settings',
        'type'    => 'select',
        'choices' => array(
            'auto'     => __( 'Auto-detect', 'flavor' ),
            'theme'    => __( 'Theme built-in', 'flavor' ),
            'yoast'    => __( 'Yoast SEO', 'flavor' ),
            'rankmath' => __( 'Rank Math', 'flavor' ),
            'navxt'    => __( 'Breadcrumb NavXT', 'flavor' ),
        ),
    ) );
}
add_action( 'customize_register', 'tainacan_customizer_breadcrumb_settings' );

/**
 * Sanitize breadcrumb separator.
 *
 * @param string $value Value to sanitize.
 * @return string
 */
function tainacan_sanitize_breadcrumb_separator( $value ) {
    $valid = array( 'rsaquo', 'raquo', 'slash', 'arrow', 'chevron', 'pipe' );
    return in_array( $value, $valid, true ) ? $value : 'rsaquo';
}

/**
 * Sanitize breadcrumb font size.
 *
 * @param int $value Value to sanitize.
 * @return int
 */
function tainacan_sanitize_breadcrumb_font_size( $value ) {
    $value = absint( $value );
    if ( $value < 11 ) {
        return 11;
    }
    if ( $value > 16 ) {
        return 16;
    }
    return $value;
}

/**
 * Sanitize breadcrumb SEO source.
 *
 * @param string $value Value to sanitize.
 * @return string
 */
function tainacan_sanitize_breadcrumb_seo_source( $value ) {
    $valid = array( 'auto', 'theme', 'yoast', 'rankmath', 'navxt' );
    return in_array( $value, $valid, true ) ? $value : 'auto';
}

/**
 * Get the separator character based on the stored key.
 *
 * @return string HTML entity or character.
 */
function tainacan_get_breadcrumb_separator_char() {
    $key = get_theme_mod( 'tainacan_breadcrumb_separator', 'rsaquo' );

    $map = array(
        'rsaquo'  => '&rsaquo;',
        'raquo'   => '&raquo;',
        'slash'   => '/',
        'arrow'   => '&rarr;',
        'chevron' => '&gt;',
        'pipe'    => '|',
    );

    return isset( $map[ $key ] ) ? $map[ $key ] : '&rsaquo;';
}

/**
 * Render the breadcrumb.
 *
 * Checks the chosen source and falls back gracefully.
 */
function tainacan_render_breadcrumb() {
    if ( ! get_theme_mod( 'tainacan_breadcrumb_enable', true ) ) {
        return;
    }

    $source = get_theme_mod( 'tainacan_breadcrumb_seo_source', 'auto' );

    echo '<nav class="tainacan-breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', 'flavor' ) . '">';

    $rendered = false;

    if ( 'yoast' === $source || ( 'auto' === $source && ! $rendered ) ) {
        if ( function_exists( 'yoast_breadcrumb' ) ) {
            yoast_breadcrumb( '<div class="tainacan-breadcrumb__inner">', '</div>' );
            $rendered = true;
        }
    }

    if ( 'rankmath' === $source || ( 'auto' === $source && ! $rendered ) ) {
        if ( function_exists( 'rank_math_the_breadcrumbs' ) ) {
            echo '<div class="tainacan-breadcrumb__inner">';
            rank_math_the_breadcrumbs();
            echo '</div>';
            $rendered = true;
        }
    }

    if ( 'navxt' === $source || ( 'auto' === $source && ! $rendered ) ) {
        if ( function_exists( 'bcn_display' ) ) {
            echo '<div class="tainacan-breadcrumb__inner">';
            bcn_display();
            echo '</div>';
            $rendered = true;
        }
    }

    // Theme fallback.
    if ( 'theme' === $source || ! $rendered ) {
        $separator = tainacan_get_breadcrumb_separator_char();
        echo '<div class="tainacan-breadcrumb__inner">';
        echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'flavor' ) . '</a>';
        echo ' <span class="tainacan-breadcrumb__sep">' . $separator . '</span> '; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

        if ( is_singular() ) {
            the_title( '<span class="tainacan-breadcrumb__current">', '</span>' );
        } elseif ( is_archive() ) {
            echo '<span class="tainacan-breadcrumb__current">';
            the_archive_title();
            echo '</span>';
        } elseif ( is_search() ) {
            /* translators: %s: search query */
            echo '<span class="tainacan-breadcrumb__current">' . sprintf( esc_html__( 'Search results for: %s', 'flavor' ), get_search_query() ) . '</span>';
        } elseif ( is_404() ) {
            echo '<span class="tainacan-breadcrumb__current">' . esc_html__( 'Page not found', 'flavor' ) . '</span>';
        }

        echo '</div>';
    }

    echo '</nav>';
}

/**
 * Output breadcrumb CSS.
 */
function tainacan_breadcrumb_output_css() {
    if ( ! get_theme_mod( 'tainacan_breadcrumb_enable', true ) ) {
        return;
    }

    $font_size  = get_theme_mod( 'tainacan_breadcrumb_font_size', 13 );
    $text_color = get_theme_mod( 'tainacan_breadcrumb_text_color', '#6c757d' );
    $link_color = get_theme_mod( 'tainacan_breadcrumb_link_color', '#187181' );
    $sep_color  = get_theme_mod( 'tainacan_breadcrumb_separator_color', '#adb5bd' );

    $css  = '.tainacan-breadcrumb{font-size:' . absint( $font_size ) . 'px;color:' . esc_attr( $text_color ) . ';margin-bottom:1em;}';
    $css .= '.tainacan-breadcrumb a{color:' . esc_attr( $link_color ) . ';text-decoration:none;}';
    $css .= '.tainacan-breadcrumb a:hover{text-decoration:underline;}';
    $css .= '.tainacan-breadcrumb__sep{color:' . esc_attr( $sep_color ) . ';margin:0 .35em;}';
    $css .= '.tainacan-breadcrumb__current{color:' . esc_attr( $text_color ) . ';}';

    echo '<style id="tainacan-breadcrumb-styles">' . $css . '</style>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
add_action( 'wp_head', 'tainacan_breadcrumb_output_css', 20 );
