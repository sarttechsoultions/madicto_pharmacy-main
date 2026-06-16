/* ============================================================
   PAGE LOADER
============================================================ */
window.addEventListener('load', () => {
    const loader = document.getElementById('pageLoader');
    setTimeout(() => {
        loader.classList.add('loaded');
    }, 800);
});

/* ============================================================
   HEADER SCROLL EFFECT
============================================================ */
const header = document.getElementById('siteHeader');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }

    // Scroll to top button
    const scrollTopBtn = document.getElementById('scrollTop');
    if (window.scrollY > 400) {
        scrollTopBtn.classList.add('visible');
    } else {
        scrollTopBtn.classList.remove('visible');
    }
});

/* ============================================================
   SCROLL TO TOP
============================================================ */
document.getElementById('scrollTop').addEventListener('click', () => {
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* ============================================================
   MOBILE MENU
============================================================ */
const menuToggle = document.getElementById("menuToggle");
const mobileMenu = document.getElementById("mobileMenu");

menuToggle.addEventListener("click", () => {
    mobileMenu.classList.toggle("active");
    menuToggle.innerHTML = mobileMenu.classList.contains("active")
        ? `<i class="bi bi-x-lg"></i>`
        : `<i class="bi bi-list"></i>`;
});


const links = document.querySelectorAll('.navbar-link');

links.forEach(link => {
    link.addEventListener('click', function () {
        // Remove active/inactive from all
        links.forEach(l => {
            l.classList.remove('active', 'inactive');
            l.classList.add('inactive');
        });
        // Mark clicked as active
        this.classList.remove('inactive');
        this.classList.add('active');
        // ✅ Page will open normally via href — no preventDefault here
    });
});


/* ============================================================
   SCROLL ANIMATION (Intersection Observer)
============================================================ */
const animatedEls = document.querySelectorAll('.animate-fade-up, .animate-fade-up, .animate-fade-up, .animate-scale');

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const delay = el.style.getPropertyValue('--delay') || '0s';
            el.style.animationDelay = delay;
            el.classList.add('in-view');
            observer.unobserve(el);
        }
    });
}, { threshold: 0.12 });

animatedEls.forEach(el => observer.observe(el));

/* ============================================================
   PHARMACY SCROLL BUTTONS
============================================================ */
document.addEventListener("DOMContentLoaded", () => {
    const gridContainer = document.querySelector(".pharmacy-grid");
    const btnLeft = document.getElementById("scrollLeft");
    const btnRight = document.getElementById("scrollRight");

    if (!gridContainer || !btnLeft || !btnRight) return;

    const getScrollAmount = () => {
        const firstCard = gridContainer.querySelector(".medicine-card");
        return firstCard ? firstCard.clientWidth + 24 : 300;
    };

    btnRight.addEventListener("click", () => {
        gridContainer.scrollBy({ left: getScrollAmount(), behavior: "smooth" });
    });

    btnLeft.addEventListener("click", () => {
        gridContainer.scrollBy({ left: -getScrollAmount(), behavior: "smooth" });
    });

    const toggleButtonState = () => {
        btnLeft.style.opacity = gridContainer.scrollLeft <= 5 ? "0.4" : "1";
        btnLeft.style.pointerEvents = gridContainer.scrollLeft <= 5 ? "none" : "auto";

        const maxScroll = gridContainer.scrollWidth - gridContainer.clientWidth;
        btnRight.style.opacity = gridContainer.scrollLeft >= maxScroll - 5 ? "0.4" : "1";
        btnRight.style.pointerEvents = gridContainer.scrollLeft >= maxScroll - 5 ? "none" : "auto";
    };

    gridContainer.addEventListener("scroll", toggleButtonState);
    window.addEventListener("resize", toggleButtonState);
    toggleButtonState();
});

/* ============================================================
   TAB BUTTONS
============================================================ */
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
    });
});

/* ============================================================
   CONTACT FORM SUBMIT
============================================================ */
document.getElementById('contactForm').addEventListener('submit', (e) => {
    e.preventDefault();
    const btn = e.target.querySelector('.contact-btn');
    btn.innerHTML = '<span>Sent!</span><i class="bi bi-check-circle-fill"></i>';
    btn.style.background = '#15803d';
    setTimeout(() => {
        btn.innerHTML = '<span>Send Message</span><i class="bi bi-send-fill"></i>';
        btn.style.background = '';
        e.target.reset();
    }, 3000);
});

/* ============================================================
   COUNTER ANIMATION
============================================================ */
function animateCounter(el, target, duration = 1500) {
    let start = 0;
    const step = target / (duration / 16);
    const timer = setInterval(() => {
        start += step;
        if (start >= target) {
            el.textContent = target + '+';
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(start) + '+';
        }
    }, 16);
}

const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.count);
            animateCounter(el, target);
            counterObserver.unobserve(el);
        }
    });
}, { threshold: 0.5 });

document.querySelectorAll('[data-count]').forEach(el => counterObserver.observe(el));


/* ============================================================
   Scroller
============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    const track = document.getElementById('partnersTrack');
    if (!track) return;
    const items = Array.from(track.children);
    items.forEach(item => track.appendChild(item.cloneNode(true)));
});










/**
 * legal-page.js
 * Scoped to .privacy-wrapper — no conflicts with site-wide scripts.
 * Handles: Privacy Policy accordion (one open at a time).
 */

(function () {
    'use strict';

    function initPrivacyAccordion() {
        const wrapper = document.querySelector('.privacy-wrapper');
        if (!wrapper) return;

        const items = wrapper.querySelectorAll('.privacy-item');

        items.forEach(function (item) {
            const header = item.querySelector('.privacy-header');
            if (!header) return;

            header.addEventListener('click', function () {
                const isAlreadyActive = item.classList.contains('active');

                // Close all items
                items.forEach(function (el) {
                    el.classList.remove('active');
                });

                // Open clicked item only if it wasn't already open
                if (!isAlreadyActive) {
                    item.classList.add('active');
                }
            });

            // Keyboard accessibility: Enter / Space
            header.setAttribute('role', 'button');
            header.setAttribute('tabindex', '0');

            header.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    header.click();
                }
            });
        });
    }

    // Run after DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPrivacyAccordion);
    } else {
        initPrivacyAccordion();
    }
})();







/* ============================================================
    new 2 section js
  ============================================================ */



(function () {
    'use strict';

    /* ---- FAQ Accordion ---- */
    function initFaqAccordion() {
        const items = document.querySelectorAll('.faq-item');
        if (!items.length) return;

        items.forEach(function (item) {
            const question = item.querySelector('.faq-question');
            if (!question) return;

            question.setAttribute('role', 'button');
            question.setAttribute('tabindex', '0');

            question.addEventListener('click', function () {
                const isOpen = item.classList.contains('active');

                // Close all
                items.forEach(function (el) { el.classList.remove('active'); });

                // Open clicked (toggle)
                if (!isOpen) item.classList.add('active');
            });

            question.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    question.click();
                }
            });
        });
    }

    /* ---- Init on DOM ready ---- */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initFaqAccordion();
        });
    } else {
        initFaqAccordion();
    }

})();