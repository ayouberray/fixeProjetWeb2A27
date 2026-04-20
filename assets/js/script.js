(() => {
    const body = document.body;

    const hideLoader = () => {
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

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.12 });

        items.forEach((el) => revealObserver.observe(el));
    };

    const animateValue = (node, target, duration = 1500) => {
        const start = 0;
        const startAt = performance.now();

        const step = (time) => {
            const progress = Math.min((time - startAt) / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            const value = Math.round(start + (target - start) * ease);
            node.textContent = value.toLocaleString('fr-FR');

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    };

    const initCounters = () => {
        const counters = document.querySelectorAll('[data-countup]');
        if (!counters.length) return;

        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                const target = Number.parseInt(entry.target.getAttribute('data-countup'), 10);
                if (Number.isFinite(target)) {
                    animateValue(entry.target, target);
                }
                observer.unobserve(entry.target);
            });
        }, { threshold: 0.4 });

        counters.forEach((counter) => counterObserver.observe(counter));
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

    const initBackground = () => {
        const mode = body.getAttribute('data-background-mode');
        const slides = document.querySelectorAll('.background-stage__slide');
        if (!slides.length || mode !== 'slideshow') return;

        const images = parseImages();
        if (!images.length) return;

        let active = 0;
        slides[0].style.backgroundImage = `url('${images[0]}')`;
        slides[0].classList.add('is-active');

        if (images.length === 1) return;

        setInterval(() => {
            const next = (active + 1) % images.length;
            const nextLayer = slides[(active + 1) % slides.length];
            const oldLayer = slides[active % slides.length];

            nextLayer.style.backgroundImage = `url('${images[next]}')`;
            nextLayer.classList.add('is-active');
            oldLayer.classList.remove('is-active');

            active = next;
        }, 5000);
    };

    const initToasts = () => {
        const stack = document.getElementById('toastStack');
        if (!stack) return;

        const closeToast = (toast) => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(18px)';
            setTimeout(() => toast.remove(), 260);
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

    window.addEventListener('load', hideLoader);
    initReveal();
    initCounters();
    initBackground();
    initToasts();
})();
