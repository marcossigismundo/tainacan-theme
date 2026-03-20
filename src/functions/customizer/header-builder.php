<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Header Builder Customizer Module
 *
 * Provides a multi-row header builder with sticky and transparent header support.
 *
 * @package suspended Tainacan_Interface
 * @since 2.9.0
 */

/**
 * Sanitize checkbox value.
 *
 * @param mixed $checked The checkbox value.
 * @return bool
 */
function tainacan_sanitize_checkbox( $checked ) {
    return ( ( isset( $checked ) && true === $checked ) ? true : false );
}

/**
 * Register Header Builder customizer settings and controls.
 *
 * @param WP_Customize_Manager $wp_customize The customizer manager instance.
 */
function tainacan_header_builder_customizer( $wp_customize ) {

    // =========================================================================
    // Panel: Header Builder
    // =========================================================================
    $wp_customize->add_panel( 'tainacan_header_builder', array(
        'title'    => __( 'Header Builder', 'flavor-flavors' ),
        'priority' => 25,
    ) );

    // =========================================================================
    // Section: Header Top Row
    // =========================================================================
    $wp_customize->add_section( 'tainacan_header_top_row', array(
        'title' => __( 'Header Top Row', 'flavor-flavors' ),
        'panel' => 'tainacan_header_builder',
    ) );

    // Enable top row.
    $wp_customize->add_setting( 'tainacan_header_top_enable', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_top_enable', array(
        'label'   => __( 'Enable Top Row', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
        'type'    => 'checkbox',
    ) );

    // Top row left element.
    $top_row_choices = array(
        'none'   => __( 'None', 'flavor-flavors' ),
        'social' => __( 'Social Icons', 'flavor-flavors' ),
        'menu'   => __( 'Menu', 'flavor-flavors' ),
        'text'   => __( 'Custom Text', 'flavor-flavors' ),
        'search' => __( 'Search', 'flavor-flavors' ),
    );

    $wp_customize->add_setting( 'tainacan_header_top_left', array(
        'default'           => 'social',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_top_left', array(
        'label'   => __( 'Left Element', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
        'type'    => 'select',
        'choices' => $top_row_choices,
    ) );

    // Top row center element.
    $wp_customize->add_setting( 'tainacan_header_top_center', array(
        'default'           => 'none',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_top_center', array(
        'label'   => __( 'Center Element', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
        'type'    => 'select',
        'choices' => $top_row_choices,
    ) );

    // Top row right element.
    $wp_customize->add_setting( 'tainacan_header_top_right', array(
        'default'           => 'text',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_top_right', array(
        'label'   => __( 'Right Element', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
        'type'    => 'select',
        'choices' => $top_row_choices,
    ) );

    // Top row custom text.
    $wp_customize->add_setting( 'tainacan_header_top_text', array(
        'default'           => '',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_top_text', array(
        'label'   => __( 'Custom Text', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
        'type'    => 'text',
    ) );

    // Top row background color.
    $wp_customize->add_setting( 'tainacan_header_top_bg_color', array(
        'default'           => '#f8f9fa',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_header_top_bg_color', array(
        'label'   => __( 'Background Color', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
    ) ) );

    // Top row text color.
    $wp_customize->add_setting( 'tainacan_header_top_text_color', array(
        'default'           => '#6c757d',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_header_top_text_color', array(
        'label'   => __( 'Text Color', 'flavor-flavors' ),
        'section' => 'tainacan_header_top_row',
    ) ) );

    // =========================================================================
    // Section: Header Main Row
    // =========================================================================
    $wp_customize->add_section( 'tainacan_header_main_row', array(
        'title' => __( 'Header Main Row', 'flavor-flavors' ),
        'panel' => 'tainacan_header_builder',
    ) );

    // Main row height.
    $wp_customize->add_setting( 'tainacan_header_main_height', array(
        'default'           => 80,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_main_height', array(
        'label'       => __( 'Row Height (px)', 'flavor-flavors' ),
        'section'     => 'tainacan_header_main_row',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 50,
            'max'  => 200,
            'step' => 1,
        ),
    ) );

    // Main row background color.
    $wp_customize->add_setting( 'tainacan_header_main_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_header_main_bg_color', array(
        'label'   => __( 'Background Color', 'flavor-flavors' ),
        'section' => 'tainacan_header_main_row',
    ) ) );

    // Main row layout.
    $wp_customize->add_setting( 'tainacan_header_main_layout', array(
        'default'           => 'logo-left',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_main_layout', array(
        'label'   => __( 'Layout', 'flavor-flavors' ),
        'section' => 'tainacan_header_main_row',
        'type'    => 'select',
        'choices' => array(
            'logo-left'   => __( 'Logo Left', 'flavor-flavors' ),
            'logo-center' => __( 'Logo Center', 'flavor-flavors' ),
            'logo-right'  => __( 'Logo Right', 'flavor-flavors' ),
        ),
    ) );

    // =========================================================================
    // Section: Header Bottom Row
    // =========================================================================
    $wp_customize->add_section( 'tainacan_header_bottom_row', array(
        'title' => __( 'Header Bottom Row', 'flavor-flavors' ),
        'panel' => 'tainacan_header_builder',
    ) );

    // Enable bottom row.
    $wp_customize->add_setting( 'tainacan_header_bottom_enable', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_bottom_enable', array(
        'label'   => __( 'Enable Bottom Row', 'flavor-flavors' ),
        'section' => 'tainacan_header_bottom_row',
        'type'    => 'checkbox',
    ) );

    // Bottom row content.
    $wp_customize->add_setting( 'tainacan_header_bottom_content', array(
        'default'           => 'menu',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_bottom_content', array(
        'label'   => __( 'Content', 'flavor-flavors' ),
        'section' => 'tainacan_header_bottom_row',
        'type'    => 'select',
        'choices' => array(
            'menu'       => __( 'Menu', 'flavor-flavors' ),
            'breadcrumb' => __( 'Breadcrumb', 'flavor-flavors' ),
            'search'     => __( 'Search', 'flavor-flavors' ),
        ),
    ) );

    // Bottom row background color.
    $wp_customize->add_setting( 'tainacan_header_bottom_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_header_bottom_bg_color', array(
        'label'   => __( 'Background Color', 'flavor-flavors' ),
        'section' => 'tainacan_header_bottom_row',
    ) ) );

    // Bottom row border.
    $wp_customize->add_setting( 'tainacan_header_bottom_border', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_bottom_border', array(
        'label'   => __( 'Show Bottom Border', 'flavor-flavors' ),
        'section' => 'tainacan_header_bottom_row',
        'type'    => 'checkbox',
    ) );

    // =========================================================================
    // Section: Sticky Header
    // =========================================================================
    $wp_customize->add_section( 'tainacan_header_sticky', array(
        'title' => __( 'Sticky Header', 'flavor-flavors' ),
        'panel' => 'tainacan_header_builder',
    ) );

    // Enable sticky header.
    $wp_customize->add_setting( 'tainacan_header_sticky_enable', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_sticky_enable', array(
        'label'   => __( 'Enable Sticky Header', 'flavor-flavors' ),
        'section' => 'tainacan_header_sticky',
        'type'    => 'checkbox',
    ) );

    // Sticky rows.
    $wp_customize->add_setting( 'tainacan_header_sticky_rows', array(
        'default'           => 'main-only',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_sticky_rows', array(
        'label'   => __( 'Sticky Rows', 'flavor-flavors' ),
        'section' => 'tainacan_header_sticky',
        'type'    => 'select',
        'choices' => array(
            'all'      => __( 'All Rows', 'flavor-flavors' ),
            'main-only' => __( 'Main Row Only', 'flavor-flavors' ),
            'top-main' => __( 'Top + Main Rows', 'flavor-flavors' ),
        ),
    ) );

    // Enable shrink animation.
    $wp_customize->add_setting( 'tainacan_header_sticky_shrink', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_sticky_shrink', array(
        'label'   => __( 'Enable Shrink Animation', 'flavor-flavors' ),
        'section' => 'tainacan_header_sticky',
        'type'    => 'checkbox',
    ) );

    // Shrink height.
    $wp_customize->add_setting( 'tainacan_header_sticky_shrink_height', array(
        'default'           => 60,
        'sanitize_callback' => 'absint',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_sticky_shrink_height', array(
        'label'       => __( 'Shrink Height (px)', 'flavor-flavors' ),
        'section'     => 'tainacan_header_sticky',
        'type'        => 'number',
        'input_attrs' => array(
            'min'  => 40,
            'max'  => 120,
            'step' => 1,
        ),
    ) );

    // Sticky background color.
    $wp_customize->add_setting( 'tainacan_header_sticky_bg_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_header_sticky_bg_color', array(
        'label'   => __( 'Sticky Background Color', 'flavor-flavors' ),
        'section' => 'tainacan_header_sticky',
    ) ) );

    // Sticky shadow.
    $wp_customize->add_setting( 'tainacan_header_sticky_shadow', array(
        'default'           => true,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_sticky_shadow', array(
        'label'   => __( 'Show Shadow', 'flavor-flavors' ),
        'section' => 'tainacan_header_sticky',
        'type'    => 'checkbox',
    ) );

    // =========================================================================
    // Section: Transparent Header
    // =========================================================================
    $wp_customize->add_section( 'tainacan_header_transparent', array(
        'title' => __( 'Transparent Header', 'flavor-flavors' ),
        'panel' => 'tainacan_header_builder',
    ) );

    // Enable transparent header.
    $wp_customize->add_setting( 'tainacan_header_transparent_enable', array(
        'default'           => false,
        'sanitize_callback' => 'tainacan_sanitize_checkbox',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_transparent_enable', array(
        'label'   => __( 'Enable Transparent Header', 'flavor-flavors' ),
        'section' => 'tainacan_header_transparent',
        'type'    => 'checkbox',
    ) );

    // Transparent pages.
    $wp_customize->add_setting( 'tainacan_header_transparent_pages', array(
        'default'           => 'homepage',
        'sanitize_callback' => 'sanitize_text_field',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( 'tainacan_header_transparent_pages', array(
        'label'   => __( 'Apply On', 'flavor-flavors' ),
        'section' => 'tainacan_header_transparent',
        'type'    => 'select',
        'choices' => array(
            'homepage'     => __( 'Homepage Only', 'flavor-flavors' ),
            'collections'  => __( 'Collections', 'flavor-flavors' ),
            'all-archives' => __( 'All Archives', 'flavor-flavors' ),
            'custom'       => __( 'Custom (via filter)', 'flavor-flavors' ),
        ),
    ) );

    // Transparent text color.
    $wp_customize->add_setting( 'tainacan_header_transparent_text_color', array(
        'default'           => '#ffffff',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'tainacan_header_transparent_text_color', array(
        'label'   => __( 'Text Color', 'flavor-flavors' ),
        'section' => 'tainacan_header_transparent',
    ) ) );

    // Alternative logo for transparent state.
    $wp_customize->add_setting( 'tainacan_header_transparent_logo_alt', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
        'transport'         => 'refresh',
    ) );
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'tainacan_header_transparent_logo_alt', array(
        'label'   => __( 'Alternative Logo for Transparent Header', 'flavor-flavors' ),
        'section' => 'tainacan_header_transparent',
    ) ) );
}
add_action( 'customize_register', 'tainacan_header_builder_customizer' );

/**
 * Check whether the current page should display a transparent header.
 *
 * The result can be further filtered via the `tainacan_is_transparent_header_page` filter,
 * which is especially useful when the setting is set to "custom".
 *
 * @return bool
 */
function tainacan_is_transparent_header_page() {
    $enabled = get_theme_mod( 'tainacan_header_transparent_enable', false );

    if ( ! $enabled ) {
        return false;
    }

    $pages = get_theme_mod( 'tainacan_header_transparent_pages', 'homepage' );

    switch ( $pages ) {
        case 'homepage':
            $is_transparent = is_front_page();
            break;

        case 'collections':
            $is_transparent = is_post_type_archive( 'tainacan-collection' ) || is_singular( 'tainacan-collection' );
            break;

        case 'all-archives':
            $is_transparent = is_archive() || is_front_page();
            break;

        case 'custom':
            $is_transparent = false;
            break;

        default:
            $is_transparent = false;
            break;
    }

    /**
     * Filter whether the current page should use a transparent header.
     *
     * @param bool   $is_transparent Whether the page qualifies.
     * @param string $pages          The selected transparent-pages setting value.
     */
    return apply_filters( 'tainacan_is_transparent_header_page', $is_transparent, $pages );
}

/**
 * Add header-related classes to the body element.
 *
 * @param array $classes Existing body classes.
 * @return array Modified body classes.
 */
function tainacan_header_body_classes( $classes ) {
    // Sticky header class.
    if ( get_theme_mod( 'tainacan_header_sticky_enable', false ) ) {
        $classes[] = 'has-sticky-header';
    }

    // Transparent header class.
    if ( tainacan_is_transparent_header_page() ) {
        $classes[] = 'has-transparent-header';
    }

    // Layout class.
    $layout    = get_theme_mod( 'tainacan_header_main_layout', 'logo-left' );
    $classes[] = 'header-layout-' . sanitize_html_class( $layout );

    return $classes;
}
add_filter( 'body_class', 'tainacan_header_body_classes' );

/**
 * Output inline CSS for the Header Builder settings.
 *
 * Hooked to `wp_head` at priority 20 so it loads after the main stylesheet.
 */
function tainacan_header_builder_output_css() {
    // Main row.
    $main_height   = absint( get_theme_mod( 'tainacan_header_main_height', 80 ) );
    $main_bg_color = sanitize_hex_color( get_theme_mod( 'tainacan_header_main_bg_color', '#ffffff' ) );

    // Top row.
    $top_enabled    = get_theme_mod( 'tainacan_header_top_enable', false );
    $top_bg_color   = sanitize_hex_color( get_theme_mod( 'tainacan_header_top_bg_color', '#f8f9fa' ) );
    $top_text_color = sanitize_hex_color( get_theme_mod( 'tainacan_header_top_text_color', '#6c757d' ) );

    // Bottom row.
    $bottom_enabled  = get_theme_mod( 'tainacan_header_bottom_enable', false );
    $bottom_bg_color = sanitize_hex_color( get_theme_mod( 'tainacan_header_bottom_bg_color', '#ffffff' ) );
    $bottom_border   = get_theme_mod( 'tainacan_header_bottom_border', true );

    // Sticky.
    $sticky_enabled       = get_theme_mod( 'tainacan_header_sticky_enable', false );
    $sticky_bg_color      = sanitize_hex_color( get_theme_mod( 'tainacan_header_sticky_bg_color', '#ffffff' ) );
    $sticky_shadow        = get_theme_mod( 'tainacan_header_sticky_shadow', true );
    $sticky_shrink        = get_theme_mod( 'tainacan_header_sticky_shrink', true );
    $sticky_shrink_height = absint( get_theme_mod( 'tainacan_header_sticky_shrink_height', 60 ) );

    // Transparent.
    $transparent_text_color = sanitize_hex_color( get_theme_mod( 'tainacan_header_transparent_text_color', '#ffffff' ) );

    ob_start();
    ?>
<style id="tainacan-header-builder-css">
/* Header Main Row */
.tainacan-header-main-row {
    min-height: <?php echo esc_attr( $main_height ); ?>px;
    background-color: <?php echo esc_attr( $main_bg_color ); ?>;
    transition: min-height 0.3s ease, background-color 0.3s ease;
}

<?php if ( $top_enabled ) : ?>
/* Header Top Row */
.tainacan-header-top-row {
    background-color: <?php echo esc_attr( $top_bg_color ); ?>;
    color: <?php echo esc_attr( $top_text_color ); ?>;
}
.tainacan-header-top-row a {
    color: <?php echo esc_attr( $top_text_color ); ?>;
}
<?php endif; ?>

<?php if ( $bottom_enabled ) : ?>
/* Header Bottom Row */
.tainacan-header-bottom-row {
    background-color: <?php echo esc_attr( $bottom_bg_color ); ?>;
    <?php if ( $bottom_border ) : ?>
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
    <?php endif; ?>
}
<?php endif; ?>

<?php if ( $sticky_enabled ) : ?>
/* Sticky Header */
.header-is-sticky .tainacan-site-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: <?php echo esc_attr( $sticky_bg_color ); ?>;
    <?php if ( $sticky_shadow ) : ?>
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    <?php endif; ?>
}

<?php if ( $sticky_shrink ) : ?>
/* Shrunk Header */
.header-is-shrunk .tainacan-header-main-row {
    min-height: <?php echo esc_attr( $sticky_shrink_height ); ?>px;
}
<?php endif; ?>
<?php endif; ?>

/* Transparent Header */
.has-transparent-header .tainacan-site-header {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: transparent;
    color: <?php echo esc_attr( $transparent_text_color ); ?>;
}
.has-transparent-header .tainacan-site-header a {
    color: <?php echo esc_attr( $transparent_text_color ); ?>;
}
.has-transparent-header.header-scrolled .tainacan-site-header {
    background-color: <?php echo esc_attr( $main_bg_color ); ?>;
    color: inherit;
}
.has-transparent-header.header-scrolled .tainacan-site-header a {
    color: inherit;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .tainacan-header-main-row,
    .tainacan-site-header {
        transition: none !important;
    }
}
</style>
    <?php
    echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- CSS is escaped per-value above.
}
add_action( 'wp_head', 'tainacan_header_builder_output_css', 20 );
