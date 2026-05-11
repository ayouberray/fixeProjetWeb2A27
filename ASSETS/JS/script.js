(() => {
    const body = document.body;

    const initLoader = () => {
        const loader = document.getElementById('pageLoader');
        if (!loader) return;

        setTimeout(() => {
            loader.classList.add('is-hidden');
            setTimeout(() => loader.remove(), 360);
        }, 220);
    };

    const initReveal = () => {
        const items = document.querySelectorAll('.reveal');
        if (!items.length) return;

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.14 });

        items.forEach((el) => observer.observe(el));
    };

    const animateCounter = (el, target, duration = 1600) => {
        const start = 0;
        const startAt = performance.now();

        const step = (time) => {
            const progress = Math.min((time - startAt) / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const value = Math.round(start + (target - start) * eased);
            el.textContent = value.toLocaleString('fr-FR');

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    };

    const initCounters = () => {
        const counters = document.querySelectorAll('[data-countup]');
        if (!counters.length) return;

        const observer = new IntersectionObserver((entries, obs) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                const target = Number.parseInt(entry.target.getAttribute('data-countup') || '0', 10);
                if (Number.isFinite(target)) {
                    animateCounter(entry.target, target);
                }
                obs.unobserve(entry.target);
            });
        }, { threshold: 0.45 });

        counters.forEach((counter) => observer.observe(counter));
    };

    const parseImages = () => {
        const raw = body.getAttribute('data-bg-images') || '[]';
        try {
            const parsed = JSON.parse(raw);
            return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
        } catch (error) {
            return [];
        }
    };

    const initSlideshow = () => {
        const mode = body.getAttribute('data-background-mode');
        if (mode !== 'slideshow') return;

        const slides = document.querySelectorAll('.background-stage__slide');
        if (!slides.length) return;

        const images = parseImages();
        if (!images.length) return;

        slides[0].style.backgroundImage = `url('${images[0]}')`;
        slides[0].classList.add('is-active');

        if (images.length === 1) return;

        let activeImage = 0;

        setInterval(() => {
            const nextImage = (activeImage + 1) % images.length;
            const nextLayer = slides[(activeImage + 1) % slides.length];
            const oldLayer = slides[activeImage % slides.length];

            nextLayer.style.backgroundImage = `url('${images[nextImage]}')`;
            nextLayer.classList.add('is-active');
            oldLayer.classList.remove('is-active');

            activeImage = nextImage;
        }, 5000);
    };

    const initToasts = () => {
        const stack = document.getElementById('toastStack');
        if (!stack) return;

        const closeToast = (toast) => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(20px)';
            setTimeout(() => toast.remove(), 240);
        };

        stack.querySelectorAll('[data-toast]').forEach((toast) => {
            setTimeout(() => closeToast(toast), 3200);
        });

        window.showToast = (message, type = 'info') => {
            const toast = document.createElement('div');
            toast.className = `toast toast--${type}`;
            toast.textContent = message;
            stack.appendChild(toast);
            setTimeout(() => closeToast(toast), 3200);
        };
    };

    const initAdvancedStats = () => {
        const toggle = document.querySelector('[data-stats-toggle]');
        const panel = document.querySelector('[data-advanced-stats]');
        if (!toggle || !panel) return;
        if (toggle.dataset.ready === '1') return;
        toggle.dataset.ready = '1';

        const animateRings = () => {
            panel.querySelectorAll('.stat-ring').forEach((ring) => {
                const target = Number.parseInt(ring.style.getPropertyValue('--value') || '0', 10);
                const startAt = performance.now();
                const duration = 900;

                const step = (time) => {
                    const progress = Math.min((time - startAt) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    ring.style.setProperty('--progress', Math.round(target * eased));

                    if (progress < 1) {
                        requestAnimationFrame(step);
                    }
                };

                ring.style.setProperty('--progress', '0');
                requestAnimationFrame(step);
            });
        };

        toggle.addEventListener('click', () => {
            const isOpen = !panel.hasAttribute('hidden');
            if (isOpen) {
                panel.setAttribute('hidden', '');
                toggle.setAttribute('aria-expanded', 'false');
                return;
            }

            panel.removeAttribute('hidden');
            toggle.setAttribute('aria-expanded', 'true');
            animateRings();
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    };

    window.addEventListener('load', initLoader);
    initReveal();
    initCounters();
    initSlideshow();
    initToasts();
    initAdvancedStats();
})();
