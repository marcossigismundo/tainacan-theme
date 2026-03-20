/**
 * Tainacan Scroll Animations
 *
 * Uses IntersectionObserver to animate elements as they enter the viewport.
 * Reads configuration from the global tainacanAnimations object.
 *
 * @since 2.9.0
 */
(function () {
	'use strict';

	var config = window.tainacanAnimations || {
		type: 'fade-up',
		duration: 400,
		delay: 50,
		targets: 'cards-only',
	};

	// Selector maps for different target configurations
	var selectorMap = {
		'cards-only':
			'.tainacan-collection-item, .list-post, .card, .tainacan-term-item',
		'all-sections':
			'.tainacan-collection-item, .list-post, .card, .tainacan-term-item, section, .wp-block-group, .entry-content > *',
		'headings-cards':
			'.tainacan-collection-item, .list-post, .card, .tainacan-term-item, h1, h2, h3, h4, h5, h6',
	};

	/**
	 * Check if user prefers reduced motion.
	 *
	 * @return {boolean}
	 */
	function prefersReducedMotion() {
		return (
			window.matchMedia &&
			window.matchMedia('(prefers-reduced-motion: reduce)').matches
		);
	}

	/**
	 * Get all elements that should be animated.
	 *
	 * @return {NodeList}
	 */
	function getTargetElements() {
		// Always include elements with explicit data-animate attribute
		var selector = '[data-animate]';
		var targetSelector = selectorMap[config.targets] || selectorMap['cards-only'];

		if (targetSelector) {
			selector += ', ' + targetSelector;
		}

		return document.querySelectorAll(selector);
	}

	/**
	 * Group elements by their parent container for stagger delay calculation.
	 *
	 * @param {NodeList} elements
	 * @return {Map}
	 */
	function groupByParent(elements) {
		var groups = new Map();

		for (var i = 0; i < elements.length; i++) {
			var el = elements[i];
			var parent = el.parentElement;

			if (!groups.has(parent)) {
				groups.set(parent, []);
			}
			groups.get(parent).push(el);
		}

		return groups;
	}

	/**
	 * Initialize scroll animations.
	 */
	function init() {
		var elements = getTargetElements();

		if (!elements.length) {
			return;
		}

		// If reduced motion is preferred, show everything immediately
		if (prefersReducedMotion()) {
			for (var i = 0; i < elements.length; i++) {
				elements[i].classList.add('is-visible');
			}
			return;
		}

		// Add the animation class to all target elements
		for (var j = 0; j < elements.length; j++) {
			elements[j].classList.add('tainacan-animate');
		}

		// Group by parent for stagger delay calculation
		var groups = groupByParent(elements);

		// Create the IntersectionObserver
		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (!entry.isIntersecting) {
						return;
					}

					var el = entry.target;
					var parent = el.parentElement;
					var siblings = groups.get(parent) || [el];
					var index = siblings.indexOf(el);
					var staggerDelay = index >= 0 ? index * config.delay : 0;

					if (staggerDelay > 0) {
						el.style.transitionDelay = staggerDelay + 'ms';
					}

					// Trigger the animation
					el.classList.add('is-visible');

					// Unobserve - animate only once
					observer.unobserve(el);

					// Clean up transition-delay after animation completes
					if (staggerDelay > 0) {
						setTimeout(function () {
							el.style.transitionDelay = '';
						}, config.duration + staggerDelay + 50);
					}
				});
			},
			{
				threshold: 0.1,
			}
		);

		// Observe all elements
		for (var k = 0; k < elements.length; k++) {
			observer.observe(elements[k]);
		}
	}

	// Run on DOM ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
