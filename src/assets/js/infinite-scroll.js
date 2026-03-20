/**
 * Tainacan Advanced Pagination - Load More & Infinite Scroll
 *
 * Handles load-more button and infinite scroll pagination for blog/archive pages.
 * Reads configuration from the global tainacanPagination object.
 *
 * @since 2.9.0
 */
(function () {
	'use strict';

	var config = window.tainacanPagination || {};
	var nextPageUrl = config.nextPageUrl || '';
	var isLoading = false;
	var noMoreItems = false;

	/**
	 * Find the posts container element.
	 *
	 * @return {Element|null}
	 */
	function getPostsContainer() {
		var selectors = ['.posts-list', '.archive-items', 'main .row'];
		for (var i = 0; i < selectors.length; i++) {
			var el = document.querySelector(selectors[i]);
			if (el) {
				return el;
			}
		}
		return null;
	}

	/**
	 * Find the standard pagination element.
	 *
	 * @return {Element|null}
	 */
	function getPaginationElement() {
		return (
			document.querySelector('.nav-links') ||
			document.querySelector('.pagination') ||
			document.querySelector('nav.navigation')
		);
	}

	/**
	 * Create the loading spinner element.
	 *
	 * @return {Element}
	 */
	function createSpinner() {
		var spinner = document.createElement('div');
		spinner.className = 'tainacan-pagination-spinner';
		spinner.setAttribute('role', 'status');
		spinner.setAttribute('aria-live', 'polite');
		spinner.innerHTML =
			'<span class="tainacan-spinner-icon" aria-hidden="true"></span>' +
			'<span class="tainacan-spinner-text">' +
			escapeHtml(config.loadingText || 'Loading...') +
			'</span>';
		spinner.style.cssText =
			'display:none;text-align:center;padding:20px;width:100%;';
		return spinner;
	}

	/**
	 * Create the "Load More" button.
	 *
	 * @return {Element}
	 */
	function createLoadMoreButton() {
		var wrapper = document.createElement('div');
		wrapper.className = 'tainacan-load-more-wrapper';
		wrapper.style.cssText = 'text-align:center;padding:20px 0;width:100%;';

		var button = document.createElement('button');
		button.className = 'tainacan-load-more-btn';
		button.type = 'button';
		button.textContent = config.loadMoreText || 'Load More';
		button.style.cssText =
			'padding:12px 30px;font-size:1rem;cursor:pointer;border:2px solid currentColor;' +
			'background:transparent;border-radius:4px;transition:opacity 0.3s;';

		wrapper.appendChild(button);
		return wrapper;
	}

	/**
	 * Create a sentinel element for IntersectionObserver (infinite scroll).
	 *
	 * @return {Element}
	 */
	function createSentinel() {
		var sentinel = document.createElement('div');
		sentinel.className = 'tainacan-scroll-sentinel';
		sentinel.style.cssText = 'height:1px;width:100%;';
		sentinel.setAttribute('aria-hidden', 'true');
		return sentinel;
	}

	/**
	 * Show a "no more items" message.
	 *
	 * @param {Element} container
	 */
	function showNoMoreMessage(container) {
		var msg = document.createElement('p');
		msg.className = 'tainacan-no-more-items';
		msg.textContent = config.noMoreText || 'No more items';
		msg.style.cssText =
			'text-align:center;padding:20px;opacity:0.6;width:100%;';
		container.parentNode.insertBefore(msg, container.nextSibling);
	}

	/**
	 * Escape HTML entities.
	 *
	 * @param {string} str
	 * @return {string}
	 */
	function escapeHtml(str) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		return div.innerHTML;
	}

	/**
	 * Fetch the next page and extract post elements.
	 *
	 * @param {function} callback Called with (newElements, nextUrl) or (null) on error.
	 */
	function fetchNextPage(callback) {
		if (!nextPageUrl || isLoading || noMoreItems) {
			return;
		}

		isLoading = true;

		fetch(nextPageUrl, {
			credentials: 'same-origin',
			headers: {
				'X-Requested-With': 'XMLHttpRequest',
			},
		})
			.then(function (response) {
				if (!response.ok) {
					throw new Error('Network response was not ok: ' + response.status);
				}
				return response.text();
			})
			.then(function (html) {
				var parser = new DOMParser();
				var doc = parser.parseFromString(html, 'text/html');

				// Find posts in the fetched page
				var container = getPostsContainerFromDoc(doc);
				var newElements = container ? container.children : [];

				// Find next page link in the fetched page
				var nextLink = doc.querySelector('.nav-links .next, a.next.page-numbers');
				var newNextUrl = nextLink ? nextLink.getAttribute('href') : '';

				nextPageUrl = newNextUrl;

				if (!newNextUrl) {
					noMoreItems = true;
				}

				isLoading = false;
				callback(Array.prototype.slice.call(newElements), newNextUrl);
			})
			.catch(function (error) {
				console.error('Tainacan pagination error:', error);
				isLoading = false;
				callback(null);
			});
	}

	/**
	 * Find posts container in a parsed document.
	 *
	 * @param {Document} doc
	 * @return {Element|null}
	 */
	function getPostsContainerFromDoc(doc) {
		var selectors = ['.posts-list', '.archive-items', 'main .row'];
		for (var i = 0; i < selectors.length; i++) {
			var el = doc.querySelector(selectors[i]);
			if (el) {
				return el;
			}
		}
		return null;
	}

	/**
	 * Append new elements to the posts container.
	 *
	 * @param {Element} container
	 * @param {Array} elements
	 */
	function appendElements(container, elements) {
		for (var i = 0; i < elements.length; i++) {
			var el = elements[i];
			// Skip script and style elements
			if (
				el.tagName === 'SCRIPT' ||
				el.tagName === 'STYLE' ||
				el.tagName === 'LINK'
			) {
				continue;
			}

			var imported = document.importNode(el, true);

			// If animation is enabled, mark for scroll-animations.js
			if (config.animate) {
				imported.setAttribute('data-animate', '');
				imported.classList.add('tainacan-animate');
			}

			container.appendChild(imported);
		}
	}

	/**
	 * Initialize Load More button mode.
	 *
	 * @param {Element} container
	 */
	function initLoadMore(container) {
		var pagination = getPaginationElement();
		var loadMoreWrapper = createLoadMoreButton();
		var spinner = createSpinner();
		var button = loadMoreWrapper.querySelector('.tainacan-load-more-btn');

		// Replace standard pagination with Load More button
		if (pagination) {
			pagination.parentNode.insertBefore(loadMoreWrapper, pagination);
			pagination.parentNode.insertBefore(spinner, loadMoreWrapper.nextSibling);
			pagination.style.display = 'none';
		} else {
			container.parentNode.insertBefore(loadMoreWrapper, container.nextSibling);
			container.parentNode.insertBefore(
				spinner,
				loadMoreWrapper.nextSibling
			);
		}

		if (!nextPageUrl) {
			loadMoreWrapper.style.display = 'none';
			return;
		}

		button.addEventListener('click', function () {
			if (isLoading || noMoreItems) {
				return;
			}

			button.disabled = true;
			button.textContent = config.loadingText || 'Loading...';
			spinner.style.display = 'block';

			fetchNextPage(function (elements) {
				spinner.style.display = 'none';

				if (elements && elements.length) {
					appendElements(container, elements);
					button.disabled = false;
					button.textContent = config.loadMoreText || 'Load More';
				}

				if (noMoreItems || !elements) {
					loadMoreWrapper.style.display = 'none';
					showNoMoreMessage(container);
				}
			});
		});
	}

	/**
	 * Initialize Infinite Scroll mode.
	 *
	 * @param {Element} container
	 */
	function initInfiniteScroll(container) {
		var pagination = getPaginationElement();
		var sentinel = createSentinel();
		var spinner = createSpinner();

		// Hide standard pagination
		if (pagination) {
			pagination.style.display = 'none';
		}

		// Add sentinel and spinner after the container
		container.parentNode.insertBefore(sentinel, container.nextSibling);
		container.parentNode.insertBefore(spinner, sentinel.nextSibling);

		if (!nextPageUrl) {
			return;
		}

		var rootMargin = (config.threshold || 300) + 'px';

		var observer = new IntersectionObserver(
			function (entries) {
				if (entries[0].isIntersecting && !isLoading && !noMoreItems) {
					spinner.style.display = 'block';

					fetchNextPage(function (elements) {
						spinner.style.display = 'none';

						if (elements && elements.length) {
							appendElements(container, elements);
						}

						if (noMoreItems) {
							observer.unobserve(sentinel);
							sentinel.remove();
							showNoMoreMessage(container);
						}
					});
				}
			},
			{
				rootMargin: '0px 0px ' + rootMargin + ' 0px',
				threshold: 0,
			}
		);

		observer.observe(sentinel);
	}

	/**
	 * Initialize advanced pagination.
	 */
	function init() {
		if (!config.type || config.type === 'standard') {
			return;
		}

		var container = getPostsContainer();
		if (!container) {
			return;
		}

		if (config.type === 'load-more') {
			initLoadMore(container);
		} else if (config.type === 'infinite-scroll') {
			initInfiniteScroll(container);
		}
	}

	// Run on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
