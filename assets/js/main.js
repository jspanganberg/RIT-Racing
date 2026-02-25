/* ============================================================
   RIT RACING — main.js
   Handles: nav scroll state, mobile menu, dropdown, reveal animations
   ============================================================ */

document.addEventListener('DOMContentLoaded', () => {

    // ── Nav: add .scrolled class on scroll ──────────────────
    const header = document.getElementById('site-header');
    const handleScroll = () => {
        header.classList.toggle('scrolled', window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();

    // ── Mobile Hamburger ────────────────────────────────────
    const hamburger = document.getElementById('hamburger');
    const navLinks  = document.getElementById('nav-links');

    hamburger?.addEventListener('click', () => {
        hamburger.classList.toggle('open');
        navLinks.classList.toggle('mobile-open');
        document.body.style.overflow =
            navLinks.classList.contains('mobile-open') ? 'hidden' : '';
    });

    // Close mobile menu on nav link click
    navLinks?.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            hamburger.classList.remove('open');
            navLinks.classList.remove('mobile-open');
            document.body.style.overflow = '';
        });
    });

    // ── Scroll Reveal ────────────────────────────────────────
    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12 });

        revealEls.forEach(el => observer.observe(el));
    }

    // ── Dropdown: keyboard accessibility ────────────────────
    document.querySelectorAll('.has-dropdown').forEach(item => {
        const toggle = item.querySelector('.dropdown-toggle');
        toggle?.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                item.classList.toggle('open');
            }
        });
    });

    // ── Counter Animation (for stats) ───────────────────────
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el  = entry.target;
                    const end = parseInt(el.dataset.count, 10);
                    const duration = 1800;
                    const step = Math.ceil(duration / end);
                    let current = 0;
                    const timer = setInterval(() => {
                        current += Math.ceil(end / 60);
                        if (current >= end) { current = end; clearInterval(timer); }
                        el.textContent = current + (el.dataset.suffix || '');
                    }, step);
                    countObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(el => countObserver.observe(el));
    }

});
