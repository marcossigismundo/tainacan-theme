<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Quick View Modal for Tainacan Items
 *
 * Provides an AJAX-powered modal to preview Tainacan items without leaving the page.
 *
 * @package flavor/flavor
 * @since 2.9.0
 */

/**
 * AJAX handler for quick view requests.
 *
 * Returns JSON with item title, thumbnail, description, metadata, and permalink.
 */
function tainacan_quick_view_ajax_handler() {
	// Verify nonce
	if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'tainacan_quick_view_nonce' ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed.', 'flavor' ) ), 403 );
	}

	// Validate item ID
	if ( ! isset( $_POST['item_id'] ) ) {
		wp_send_json_error( array( 'message' => __( 'No item ID provided.', 'flavor' ) ), 400 );
	}

	$item_id = absint( $_POST['item_id'] );

	if ( ! $item_id ) {
		wp_send_json_error( array( 'message' => __( 'Invalid item ID.', 'flavor' ) ), 400 );
	}

	$post = get_post( $item_id );

	if ( ! $post || 'publish' !== $post->post_status ) {
		wp_send_json_error( array( 'message' => __( 'Item not found.', 'flavor' ) ), 404 );
	}

	// Get thumbnail URL
	$thumbnail_url = '';
	if ( has_post_thumbnail( $item_id ) ) {
		$thumbnail_url = get_the_post_thumbnail_url( $item_id, 'medium_large' );
	}

	// Get description (excerpt or trimmed content)
	$description = '';
	if ( ! empty( $post->post_excerpt ) ) {
		$description = wp_strip_all_tags( $post->post_excerpt );
	} else {
		$description = wp_trim_words( wp_strip_all_tags( $post->post_content ), 40, '...' );
	}

	// Get Tainacan metadata (first 5)
	$metadata = array();
	if ( function_exists( 'tainacan_get_the_metadata' ) ) {
		// Use Tainacan's built-in metadata function
		$metadata = tainacan_quick_view_get_tainacan_metadata( $item_id );
	} else {
		// Fallback: get relevant post meta
		$metadata = tainacan_quick_view_get_post_meta_fallback( $item_id );
	}

	// Limit to first 5 metadata entries
	$metadata = array_slice( $metadata, 0, 5 );

	// Build response
	$response = array(
		'title'         => get_the_title( $item_id ),
		'thumbnail_url' => esc_url( $thumbnail_url ),
		'description'   => $description,
		'metadata'      => $metadata,
		'permalink'     => get_permalink( $item_id ),
	);

	wp_send_json_success( $response );
}
add_action( 'wp_ajax_tainacan_quick_view', 'tainacan_quick_view_ajax_handler' );
add_action( 'wp_ajax_nopriv_tainacan_quick_view', 'tainacan_quick_view_ajax_handler' );

/**
 * Get Tainacan metadata for an item using the plugin's API.
 *
 * @param int $item_id The item post ID.
 * @return array Array of metadata arrays with 'label' and 'value' keys.
 */
function tainacan_quick_view_get_tainacan_metadata( $item_id ) {
	$metadata = array();

	if ( ! class_exists( '\Tainacan\Repositories\Items' ) ) {
		return $metadata;
	}

	$items_repo = \Tainacan\Repositories\Items::get_instance();
	$item       = $items_repo->fetch( $item_id );

	if ( ! $item instanceof \Tainacan\Entities\Item ) {
		return $metadata;
	}

	$item_metadata_list = $item->get_metadata();

	$count = 0;
	foreach ( $item_metadata_list as $item_metadata ) {
		if ( $count >= 5 ) {
			break;
		}

		$metadatum = $item_metadata->get_metadatum();

		if ( ! $metadatum || 'yes' === $metadatum->get_private() ) {
			continue;
		}

		$value = $item_metadata->get_value_as_string();

		if ( empty( $value ) ) {
			continue;
		}

		$metadata[] = array(
			'label' => $metadatum->get_name(),
			'value' => wp_strip_all_tags( $value ),
		);

		$count++;
	}

	return $metadata;
}

/**
 * Fallback: get post meta data when Tainacan plugin is not available.
 *
 * @param int $item_id The item post ID.
 * @return array Array of metadata arrays with 'label' and 'value' keys.
 */
function tainacan_quick_view_get_post_meta_fallback( $item_id ) {
	$metadata    = array();
	$all_meta    = get_post_meta( $item_id );
	$skip_prefix = array( '_', 'tainacan_' ); // Skip private and internal meta

	$count = 0;
	foreach ( $all_meta as $key => $values ) {
		if ( $count >= 5 ) {
			break;
		}

		// Skip private meta keys (starting with _)
		$skip = false;
		foreach ( $skip_prefix as $prefix ) {
			if ( 0 === strpos( $key, $prefix ) ) {
				$skip = true;
				break;
			}
		}
		if ( $skip ) {
			continue;
		}

		$value = is_array( $values ) ? implode( ', ', $values ) : $values;
		$value = wp_strip_all_tags( $value );

		if ( empty( $value ) ) {
			continue;
		}

		$metadata[] = array(
			'label' => ucwords( str_replace( array( '-', '_' ), ' ', $key ) ),
			'value' => $value,
		);

		$count++;
	}

	return $metadata;
}

/**
 * Enqueue quick view JS on archive pages where Tainacan items are listed.
 */
function tainacan_enqueue_quick_view() {
	// Only enqueue on pages that may display Tainacan items
	$should_enqueue = false;

	if ( is_post_type_archive() ) {
		$post_type = get_query_var( 'post_type' );
		if ( is_string( $post_type ) && 0 === strpos( $post_type, 'tnc_col_' ) ) {
			$should_enqueue = true;
		}
	}

	// Also enqueue on Tainacan-related pages
	if ( function_exists( 'tainacan_get_api_posttype' ) ) {
		if ( is_post_type_archive( 'tainacan-collection' ) || is_tax() ) {
			$should_enqueue = true;
		}
	}

	// Check for Tainacan archive templates
	if ( ! $should_enqueue ) {
		$template = get_page_template_slug();
		if ( $template && false !== strpos( $template, 'tainacan' ) ) {
			$should_enqueue = true;
		}
	}

	// Also check if the page has Tainacan blocks
	if ( ! $should_enqueue && is_singular() ) {
		global $post;
		if ( $post && has_block( 'tainacan', $post ) ) {
			$should_enqueue = true;
		}
	}

	if ( ! $should_enqueue ) {
		return;
	}

	wp_enqueue_script(
		'tainacan-quick-view',
		get_template_directory_uri() . '/assets/js/quick-view.js',
		array(),
		wp_get_theme()->get( 'Version' ),
		true
	);

	wp_localize_script( 'tainacan-quick-view', 'tainacanQuickView', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => wp_create_nonce( 'tainacan_quick_view_nonce' ),
		'i18n'    => array(
			'close'    => __( 'Close', 'flavor' ),
			'loading'  => __( 'Loading...', 'flavor' ),
			'error'    => __( 'Failed to load item details.', 'flavor' ),
			'viewFull' => __( 'View full item', 'flavor' ),
		),
	) );

	// Add inline styles for the modal
	wp_add_inline_style( 'tainacan-interface-style', tainacan_quick_view_inline_css() );
}
add_action( 'wp_enqueue_scripts', 'tainacan_enqueue_quick_view' );

/**
 * Return inline CSS for the quick view modal.
 *
 * @return string
 */
function tainacan_quick_view_inline_css() {
	return '
.tainacan-quick-view-overlay {
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	background: rgba(0, 0, 0, 0.6);
	z-index: 99999;
	display: flex;
	align-items: center;
	justify-content: center;
	opacity: 0;
	transition: opacity 0.25s ease;
	padding: 20px;
	box-sizing: border-box;
}
.tainacan-quick-view-overlay.is-active {
	opacity: 1;
}
.tainacan-quick-view-modal {
	background: #fff;
	border-radius: 8px;
	max-width: 680px;
	width: 100%;
	max-height: 85vh;
	overflow-y: auto;
	position: relative;
	transform: translateY(20px);
	transition: transform 0.25s ease;
	box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
}
.tainacan-quick-view-overlay.is-active .tainacan-quick-view-modal {
	transform: translateY(0);
}
.tainacan-quick-view-close {
	position: absolute;
	top: 12px;
	right: 12px;
	background: none;
	border: none;
	font-size: 24px;
	line-height: 1;
	cursor: pointer;
	padding: 8px;
	z-index: 2;
	color: #333;
	border-radius: 4px;
}
.tainacan-quick-view-close:hover,
.tainacan-quick-view-close:focus {
	background: #f0f0f0;
}
.tainacan-quick-view-thumbnail {
	width: 100%;
	max-height: 300px;
	object-fit: cover;
	border-radius: 8px 8px 0 0;
	display: block;
}
.tainacan-quick-view-content {
	padding: 24px;
}
.tainacan-quick-view-title {
	margin: 0 0 12px;
	font-size: 1.5rem;
	line-height: 1.3;
}
.tainacan-quick-view-description {
	color: #555;
	margin: 0 0 20px;
	line-height: 1.6;
}
.tainacan-quick-view-metadata {
	list-style: none;
	padding: 0;
	margin: 0 0 20px;
}
.tainacan-quick-view-metadata li {
	padding: 8px 0;
	border-bottom: 1px solid #eee;
	display: flex;
	gap: 10px;
}
.tainacan-quick-view-metadata li:last-child {
	border-bottom: none;
}
.tainacan-quick-view-meta-label {
	font-weight: 600;
	min-width: 120px;
	color: #333;
}
.tainacan-quick-view-meta-value {
	color: #555;
}
.tainacan-quick-view-link {
	display: inline-block;
	padding: 10px 24px;
	background: #1d8dab;
	color: #fff;
	text-decoration: none;
	border-radius: 4px;
	font-size: 0.95rem;
	transition: background 0.2s;
}
.tainacan-quick-view-link:hover,
.tainacan-quick-view-link:focus {
	background: #166f89;
	color: #fff;
}
.tainacan-quick-view-spinner {
	text-align: center;
	padding: 60px 24px;
}
.tainacan-quick-view-spinner::after {
	content: "";
	display: inline-block;
	width: 36px;
	height: 36px;
	border: 3px solid #ddd;
	border-top-color: #1d8dab;
	border-radius: 50%;
	animation: tainacan-qv-spin 0.7s linear infinite;
}
@keyframes tainacan-qv-spin {
	to { transform: rotate(360deg); }
}
.tainacan-quick-view-error {
	text-align: center;
	padding: 40px 24px;
	color: #a00;
}
body.tainacan-scroll-locked {
	overflow: hidden;
}
';
}
