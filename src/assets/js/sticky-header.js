/**
 * Sticky Header Module
 *
 * Vanilla JS implementation for sticky and transparent header behaviour.
 * Reads configuration from the <body> element data attributes:
 *   - data-sticky-rows   : "all" | "main-only" | "top-main"
 *   - data-shrink-height : number (px)
 *
 * @since 2.9.0
 */
(function () {
    'use strict';

    // Bail early if there is no sticky header.
    var body = document.body;
    if (!body.classList.contains('has-sticky-header') && !body.classList.contains('has-transparent-header')) {
        return;
    }

    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------
    var stickyRows       = body.getAttribute('data-sticky-rows') || 'main-only';
    var shrinkHeight     = parseInt(body.getAttribute('data-shrink-height'), 10) || 60;
    var prefersReduced   = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Thresholds (px).
    var STICKY_THRESHOLD = 0;   // Scroll distance to trigger sticky state.
    var SHRINK_THRESHOLD = 100; // Scroll distance to trigger shrink state.

    // Cache the header element.
    var header = document.querySelector('.tainacan-site-header');
    if (!header) {
        return;
    }

    // Determine the initial header height so we can add body padding later.
    var headerHeight = header.offsetHeight;

    // -------------------------------------------------------------------------
    // State
    // -------------------------------------------------------------------------
    var isSticky    = false;
    var isShrunk    = false;
    var isScrolled  = false; // For transparent header.
    var ticking     = false;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Apply or remove sticky-related classes based on the current scroll
     * position. This function is called inside a requestAnimationFrame
     * callback so it runs at most once per frame.
     */
    function updateHeader() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;

        // --- Sticky ---
        if (body.classList.contains('has-sticky-header')) {
            if (scrollY > STICKY_THRESHOLD && !isSticky) {
                isSticky = true;
                body.classList.add('header-is-sticky');
                // Add padding to body to prevent content jump.
                body.style.paddingTop = headerHeight + 'px';

                // Hide rows that should not be sticky.
                applyRowVisibility(true);
            } else if (scrollY <= STICKY_THRESHOLD && isSticky) {
                isSticky = false;
                body.classList.remove('header-is-sticky');
                body.style.paddingTop = '';
                applyRowVisibility(false);
            }

            // --- Shrink ---
            if (scrollY > SHRINK_THRESHOLD && !isShrunk) {
                isShrunk = true;
                body.classList.add('header-is-shrunk');
            } else if (scrollY <= SHRINK_THRESHOLD && isShrunk) {
                isShrunk = false;
                body.classList.remove('header-is-shrunk');
            }
        }

        // --- Transparent header scroll state ---
        if (body.classList.contains('has-transparent-header')) {
            if (scrollY > STICKY_THRESHOLD && !isScrolled) {
                isScrolled = true;
                body.classList.add('header-scrolled');
            } else if (scrollY <= STICKY_THRESHOLD && isScrolled) {
                isScrolled = false;
                body.classList.remove('header-scrolled');
            }
        }

        ticking = false;
    }

    /**
     * Show or hide header rows depending on the `stickyRows` configuration.
     *
     * @param {boolean} sticky Whether the header is currently in sticky mode.
     */
    function applyRowVisibility(sticky) {
        var topRow    = header.querySelector('.tainacan-header-top-row');
        var bottomRow = header.querySelector('.tainacan-header-bottom-row');

        if (!sticky) {
            // Restore all rows when not sticky.
            if (topRow) topRow.style.display = '';
            if (bottomRow) bottomRow.style.display = '';
            return;
        }

        switch (stickyRows) {
            case 'main-only':
                if (topRow) topRow.style.display = 'none';
                if (bottomRow) bottomRow.style.display = 'none';
                break;
            case 'top-main':
                if (bottomRow) bottomRow.style.display = 'none';
                break;
            // 'all' keeps everything visible — nothing to do.
        }
    }

    // -------------------------------------------------------------------------
    // Scroll listener
    // -------------------------------------------------------------------------
    function onScroll() {
        if (!ticking) {
            if (prefersReduced) {
                // Skip animation frame when reduced motion is preferred — just
                // update synchronously to avoid perceived lag.
                updateHeader();
            } else {
                window.requestAnimationFrame(updateHeader);
            }
            ticking = true;
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });

    // Run once on load in case the page is already scrolled (e.g. anchor link).
    updateHeader();

    // Recalculate header height on resize.
    window.addEventListener('resize', function () {
        headerHeight = header.offsetHeight;
        if (isSticky) {
            body.style.paddingTop = headerHeight + 'px';
        }
    }, { passive: true });
})();
