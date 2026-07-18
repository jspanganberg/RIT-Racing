/* ============================================================
   RIT RACING — main.js
   
   Handles all client-side behavior for the public site:
     - Navigation scroll state (adds .scrolled class to header)
     - Mobile hamburger menu (open/close, body scroll lock)
     - Mobile dropdown submenus (tap to expand)
     - Theme toggle (dark/light, saved to localStorage)
     - Scroll reveal animations (IntersectionObserver)
     - Counter animation (animates numbers on scroll)
     - Smooth scroll for anchor links
   
   No build step required. Vanilla JS, no dependencies.
   ============================================================ */

document.addEventListener('DOMContentLoaded', function() {

    // ── Scroll dispatch: one rAF-throttled listener feeds every ──
    // ── scroll-driven effect below instead of each registering ──
    // ── its own 'scroll' handler (avoids redundant layout work). ─
    var header   = document.getElementById('site-header');
    var heroBg   = document.querySelector('.hero-bg');
    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var ticking = false;

    function onScrollFrame() {
        var y = window.scrollY;
        if (header) header.classList.toggle('scrolled', y > 20);
        // Subtle hero parallax: background drifts slower than scroll, fades out
        if (heroBg && !reduceMotion && y < window.innerHeight) {
            heroBg.style.transform = 'translate3d(0,' + (y * 0.25) + 'px,0)';
        }
        ticking = false;
    }
    function onScroll() {
        if (!ticking) { ticking = true; requestAnimationFrame(onScrollFrame); }
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    onScrollFrame();

    // ── Mobile Hamburger ─────────────────────────────────────
    var hamburger = document.getElementById('hamburger');
    var navLinks  = document.getElementById('nav-links');

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function() {
            var isOpen = navLinks.classList.toggle('mobile-open');
            hamburger.classList.toggle('open', isOpen);
            hamburger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            document.body.style.overflow = isOpen ? 'hidden' : '';
            // Disable backdrop-filter on header when menu is open (fixes fixed-position containment)
            if (header) header.classList.toggle('menu-open', isOpen);
        });

        navLinks.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function(e) {
                // Dropdown toggle: open/close the sub-menu on tap
                if (link.classList.contains('dropdown-toggle')) {
                    e.preventDefault();
                    var parent = link.closest('.has-dropdown');
                    if (parent) {
                        var wasOpen = parent.classList.contains('open');
                        // Close all other dropdowns first
                        navLinks.querySelectorAll('.has-dropdown.open').forEach(function(d) {
                            d.classList.remove('open');
                            d.querySelector('.dropdown-toggle').setAttribute('aria-expanded', 'false');
                        });
                        // Toggle this one
                        if (!wasOpen) {
                            parent.classList.add('open');
                            link.setAttribute('aria-expanded', 'true');
                        }
                    }
                    return;
                }
                // Normal link: close the mobile menu
                hamburger.classList.remove('open');
                navLinks.classList.remove('mobile-open');
                hamburger.setAttribute('aria-expanded', 'false');
                document.body.style.overflow = '';
                if (header) header.classList.remove('menu-open');
            });
        });
    }

    // ── Dropdown keyboard accessibility ──────────────────────
    document.querySelectorAll('.has-dropdown').forEach(function(item) {
        var btn = item.querySelector('.dropdown-toggle');
        if (btn) {
            btn.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    var isOpen = item.classList.toggle('open');
                    btn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                }
            });
            // Close on Escape
            item.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    item.classList.remove('open');
                    btn.setAttribute('aria-expanded', 'false');
                    btn.focus();
                }
            });
        }
    });

    // ── Scroll Reveal (single subtle fade) ───────────────────
    var revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length && 'IntersectionObserver' in window) {
        var revealObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });
        revealEls.forEach(function(el) { revealObserver.observe(el); });
    } else {
        // Fallback: show everything immediately
        revealEls.forEach(function(el) { el.classList.add('visible'); });
    }

    // ── Counter Animation ────────────────────────────────────
    var counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window) {
        var countObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var el    = entry.target;
                    var end   = parseInt(el.dataset.count, 10);
                    var suffix = el.dataset.suffix || '';

                    // Very small numbers (1–3): display instantly
                    if (end <= 3) {
                        el.textContent = end + suffix;
                        countObserver.unobserve(el);
                        return;
                    }

                    // For larger numbers, start from ~80% to avoid long awkward counts
                    var start = end > 100 ? Math.floor(end * 0.8) : 0;
                    var cur   = start;
                    var step  = Math.max(1, Math.ceil((end - start) / 40));
                    var timer = setInterval(function() {
                        cur += step;
                        if (cur >= end) { cur = end; clearInterval(timer); }
                        el.textContent = cur + suffix;
                    }, 30);
                    countObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(function(el) { countObserver.observe(el); });
    }

    // ── Lazy load images with data-src ───────────────────────
    var lazyImgs = document.querySelectorAll('img[data-src]');
    if (lazyImgs.length && 'IntersectionObserver' in window) {
        var imgObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    var img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    imgObserver.unobserve(img);
                }
            });
        }, { rootMargin: '200px' });
        lazyImgs.forEach(function(img) { imgObserver.observe(img); });
    }

});
