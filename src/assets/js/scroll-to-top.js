/**
 * Scroll-to-Top Button
 *
 * Vanilla JS implementation that dynamically creates a scroll-to-top button.
 * Reads configuration from the `tainacanScrollToTop` global object:
 *   - icon     : string  – HTML/SVG for the button icon (default: arrow SVG)
 *   - shape    : string  – "circle" | "rounded" | "square" (default: "circle")
 *   - size     : string  – "small" | "medium" | "large" (default: "medium")
 *   - position : string  – "right" | "left" (default: "right")
 *
 * @since 2.9.0
 */
(function () {
    'use strict';

    // -------------------------------------------------------------------------
    // Configuration
    // -------------------------------------------------------------------------
    var config = window.tainacanScrollToTop || {};

    var defaultIcon =
        '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" ' +
        'stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<polyline points="18 15 12 9 6 15"></polyline></svg>';

    var icon     = config.icon || defaultIcon;
    var shape    = config.shape || 'circle';
    var size     = config.size || 'medium';
    var position = config.position || 'right';

    var SHOW_THRESHOLD   = 300; // px scrolled before button appears.
    var prefersReduced   = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // -------------------------------------------------------------------------
    // Size map (width/height in px)
    // -------------------------------------------------------------------------
    var sizeMap = {
        small:  36,
        medium: 44,
        large:  56
    };
    var btnSize = sizeMap[size] || sizeMap.medium;

    // -------------------------------------------------------------------------
    // Border-radius map
    // -------------------------------------------------------------------------
    var radiusMap = {
        circle:  '50%',
        rounded: '8px',
        square:  '0'
    };
    var btnRadius = radiusMap[shape] || radiusMap.circle;

    // -------------------------------------------------------------------------
    // Create button
    // -------------------------------------------------------------------------
    var btn = document.createElement('button');
    btn.className = 'tainacan-scroll-to-top';
    btn.setAttribute('aria-label', 'Scroll to top');
    btn.setAttribute('type', 'button');
    btn.innerHTML = icon;

    // Inline styles – keep it self-contained so it works without extra CSS.
    btn.style.cssText = [
        'position: fixed',
        'bottom: 24px',
        position === 'left' ? 'left: 24px' : 'right: 24px',
        'z-index: 9999',
        'width: ' + btnSize + 'px',
        'height: ' + btnSize + 'px',
        'border: none',
        'border-radius: ' + btnRadius,
        'background-color: var(--tainacan-secondary, #298596)',
        'color: #ffffff',
        'cursor: pointer',
        'display: flex',
        'align-items: center',
        'justify-content: center',
        'opacity: 0',
        'visibility: hidden',
        'transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease',
        'transform: translateY(16px)',
        'box-shadow: 0 2px 8px rgba(0,0,0,0.15)',
        'padding: 0',
        'line-height: 1'
    ].join('; ');

    // Hover effect.
    btn.addEventListener('mouseenter', function () {
        btn.style.opacity = '0.85';
        btn.style.transform = 'translateY(-2px)';
    });
    btn.addEventListener('mouseleave', function () {
        btn.style.opacity = '1';
        btn.style.transform = 'translateY(0)';
    });

    // Append to body.
    document.body.appendChild(btn);

    // -------------------------------------------------------------------------
    // Scroll visibility
    // -------------------------------------------------------------------------
    var visible = false;
    var ticking = false;

    function updateVisibility() {
        var scrollY = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollY > SHOW_THRESHOLD && !visible) {
            visible = true;
            btn.style.opacity    = '1';
            btn.style.visibility = 'visible';
            btn.style.transform  = 'translateY(0)';
        } else if (scrollY <= SHOW_THRESHOLD && visible) {
            visible = false;
            btn.style.opacity    = '0';
            btn.style.visibility = 'hidden';
            btn.style.transform  = 'translateY(16px)';
        }
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) {
            window.requestAnimationFrame(updateVisibility);
            ticking = true;
        }
    }, { passive: true });

    // -------------------------------------------------------------------------
    // Click handler
    // -------------------------------------------------------------------------
    btn.addEventListener('click', function () {
        if (prefersReduced) {
            // Jump immediately when reduced motion is preferred.
            window.scrollTo(0, 0);
        } else {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    // -------------------------------------------------------------------------
    // Reduced motion – disable CSS transitions
    // -------------------------------------------------------------------------
    if (prefersReduced) {
        btn.style.transition = 'none';
    }

    // Initial check in case page is already scrolled.
    updateVisibility();
})();
