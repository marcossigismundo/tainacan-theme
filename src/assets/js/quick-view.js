/**
 * Tainacan Quick View Modal
 *
 * Provides an AJAX-powered modal to preview Tainacan items inline.
 * Reads configuration from the global tainacanQuickView object.
 *
 * @since 2.9.0
 */
(function () {
	'use strict';

	var config = window.tainacanQuickView || {};
	var overlay = null;
	var focusableElements = [];
	var lastFocusedElement = null;

	/**
	 * Escape HTML entities to prevent XSS.
	 *
	 * @param {string} str
	 * @return {string}
	 */
	function escapeHtml(str) {
		if (!str) return '';
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		return div.innerHTML;
	}

	/**
	 * Create the modal overlay element.
	 *
	 * @return {Element}
	 */
	function createOverlay() {
		var el = document.createElement('div');
		el.className = 'tainacan-quick-view-overlay';
		el.setAttribute('role', 'dialog');
		el.setAttribute('aria-modal', 'true');
		el.setAttribute('aria-label', 'Quick View');
		el.innerHTML = '<div class="tainacan-quick-view-modal"></div>';
		document.body.appendChild(el);
		return el;
	}

	/**
	 * Get the modal content container.
	 *
	 * @return {Element}
	 */
	function getModal() {
		if (!overlay) {
			overlay = createOverlay();
		}
		return overlay.querySelector('.tainacan-quick-view-modal');
	}

	/**
	 * Show the modal with loading state.
	 */
	function showModal() {
		lastFocusedElement = document.activeElement;

		if (!overlay) {
			overlay = createOverlay();
		}

		var modal = getModal();
		var i18n = config.i18n || {};

		modal.innerHTML =
			'<button class="tainacan-quick-view-close" type="button" aria-label="' +
			escapeHtml(i18n.close || 'Close') +
			'">&times;</button>' +
			'<div class="tainacan-quick-view-spinner" role="status">' +
			'<span class="sr-only">' +
			escapeHtml(i18n.loading || 'Loading...') +
			'</span></div>';

		overlay.style.display = 'flex';

		// Lock body scroll
		document.body.classList.add('tainacan-scroll-locked');

		// Trigger reflow for transition
		void overlay.offsetWidth;
		overlay.classList.add('is-active');

		// Bind events
		bindCloseEvents();
		setupFocusTrap();
	}

	/**
	 * Hide and clean up the modal.
	 */
	function hideModal() {
		if (!overlay) return;

		overlay.classList.remove('is-active');

		// Wait for transition to finish
		setTimeout(function () {
			overlay.style.display = 'none';
			document.body.classList.remove('tainacan-scroll-locked');
			unbindCloseEvents();

			// Restore focus
			if (lastFocusedElement) {
				lastFocusedElement.focus();
				lastFocusedElement = null;
			}
		}, 250);
	}

	/**
	 * Render item data into the modal.
	 *
	 * @param {Object} data Item data from AJAX response.
	 */
	function renderItem(data) {
		var modal = getModal();
		var i18n = config.i18n || {};
		var html = '';

		// Close button
		html +=
			'<button class="tainacan-quick-view-close" type="button" aria-label="' +
			escapeHtml(i18n.close || 'Close') +
			'">&times;</button>';

		// Thumbnail
		if (data.thumbnail_url) {
			html +=
				'<img class="tainacan-quick-view-thumbnail" src="' +
				escapeHtml(data.thumbnail_url) +
				'" alt="' +
				escapeHtml(data.title) +
				'">';
		}

		html += '<div class="tainacan-quick-view-content">';

		// Title
		html +=
			'<h2 class="tainacan-quick-view-title">' +
			escapeHtml(data.title) +
			'</h2>';

		// Description
		if (data.description) {
			html +=
				'<p class="tainacan-quick-view-description">' +
				escapeHtml(data.description) +
				'</p>';
		}

		// Metadata list
		if (data.metadata && data.metadata.length > 0) {
			html += '<ul class="tainacan-quick-view-metadata">';
			for (var i = 0; i < data.metadata.length; i++) {
				var meta = data.metadata[i];
				html += '<li>';
				html +=
					'<span class="tainacan-quick-view-meta-label">' +
					escapeHtml(meta.label) +
					'</span>';
				html +=
					'<span class="tainacan-quick-view-meta-value">' +
					escapeHtml(meta.value) +
					'</span>';
				html += '</li>';
			}
			html += '</ul>';
		}

		// View full item link
		if (data.permalink) {
			html +=
				'<a class="tainacan-quick-view-link" href="' +
				escapeHtml(data.permalink) +
				'">' +
				escapeHtml(i18n.viewFull || 'View full item') +
				'</a>';
		}

		html += '</div>';

		modal.innerHTML = html;

		// Re-bind close button and focus trap
		bindCloseEvents();
		setupFocusTrap();

		// Focus the close button
		var closeBtn = modal.querySelector('.tainacan-quick-view-close');
		if (closeBtn) {
			closeBtn.focus();
		}
	}

	/**
	 * Show an error message inside the modal.
	 *
	 * @param {string} message
	 */
	function renderError(message) {
		var modal = getModal();
		var i18n = config.i18n || {};

		modal.innerHTML =
			'<button class="tainacan-quick-view-close" type="button" aria-label="' +
			escapeHtml(i18n.close || 'Close') +
			'">&times;</button>' +
			'<div class="tainacan-quick-view-error">' +
			'<p>' +
			escapeHtml(message || i18n.error || 'Failed to load item details.') +
			'</p></div>';

		bindCloseEvents();
	}

	/**
	 * Fetch item data via AJAX.
	 *
	 * @param {number} itemId
	 */
	function fetchItem(itemId) {
		var formData = new FormData();
		formData.append('action', 'tainacan_quick_view');
		formData.append('item_id', itemId);
		formData.append('nonce', config.nonce || '');

		fetch(config.ajaxUrl || '', {
			method: 'POST',
			credentials: 'same-origin',
			body: formData,
		})
			.then(function (response) {
				return response.json();
			})
			.then(function (result) {
				if (result.success && result.data) {
					renderItem(result.data);
				} else {
					var msg =
						result.data && result.data.message
							? result.data.message
							: '';
					renderError(msg);
				}
			})
			.catch(function () {
				renderError('');
			});
	}

	/**
	 * Handle keyboard events for closing and focus trap.
	 *
	 * @param {KeyboardEvent} e
	 */
	function handleKeydown(e) {
		if (e.key === 'Escape' || e.keyCode === 27) {
			hideModal();
			return;
		}

		// Focus trap
		if (e.key === 'Tab' || e.keyCode === 9) {
			if (!focusableElements.length) return;

			var firstEl = focusableElements[0];
			var lastEl = focusableElements[focusableElements.length - 1];

			if (e.shiftKey) {
				if (document.activeElement === firstEl) {
					e.preventDefault();
					lastEl.focus();
				}
			} else {
				if (document.activeElement === lastEl) {
					e.preventDefault();
					firstEl.focus();
				}
			}
		}
	}

	/**
	 * Handle click on overlay backdrop.
	 *
	 * @param {MouseEvent} e
	 */
	function handleOverlayClick(e) {
		if (e.target === overlay) {
			hideModal();
		}
	}

	/**
	 * Handle click on close button.
	 *
	 * @param {MouseEvent} e
	 */
	function handleCloseClick(e) {
		if (e.target.classList.contains('tainacan-quick-view-close')) {
			hideModal();
		}
	}

	/**
	 * Bind all close-related event listeners.
	 */
	function bindCloseEvents() {
		document.addEventListener('keydown', handleKeydown);
		if (overlay) {
			overlay.addEventListener('click', handleOverlayClick);
			overlay.addEventListener('click', handleCloseClick);
		}
	}

	/**
	 * Unbind all close-related event listeners.
	 */
	function unbindCloseEvents() {
		document.removeEventListener('keydown', handleKeydown);
		if (overlay) {
			overlay.removeEventListener('click', handleOverlayClick);
			overlay.removeEventListener('click', handleCloseClick);
		}
	}

	/**
	 * Set up focus trap within the modal.
	 */
	function setupFocusTrap() {
		var modal = getModal();
		focusableElements = Array.prototype.slice.call(
			modal.querySelectorAll(
				'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
			)
		);
	}

	/**
	 * Handle click on quick view trigger buttons (event delegation).
	 *
	 * @param {MouseEvent} e
	 */
	function handleTriggerClick(e) {
		var trigger = e.target.closest('.tainacan-quick-view-trigger');
		if (!trigger) return;

		e.preventDefault();
		e.stopPropagation();

		var itemId = trigger.getAttribute('data-item-id');
		if (!itemId) return;

		showModal();
		fetchItem(itemId);
	}

	/**
	 * Initialize the quick view system.
	 */
	function init() {
		// Use event delegation on document body for trigger clicks
		document.body.addEventListener('click', handleTriggerClick);
	}

	// Run on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
