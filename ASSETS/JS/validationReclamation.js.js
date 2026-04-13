// ============================================
// INNOGOV - SCRIPT COMPLET
// ============================================

document.addEventListener('DOMContentLoaded', function() {

    // ========== 1. LOADER ==========
    const loader = document.querySelector('.loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('hide');
        }, 500);
    }

    // ========== 2. NAVBAR SCROLL EFFECT ==========
    const navbar = document.querySelector('.navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }

    // ========== 3. SCROLL REVEAL ==========
    const revealElements = document.querySelectorAll('.reveal');

    function checkReveal() {
        revealElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const windowHeight = window.innerHeight;
            if (elementTop < windowHeight - 100) {
                element.classList.add('active');
            }
        });
    }

    checkReveal();
    window.addEventListener('scroll', checkReveal);

    // ========== 4. SLIDESHOW (pour backoffice et frontoffice) ==========
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if (slides.length > 0) {
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }

        // Démarrage du slideshow
        setInterval(nextSlide, 5000);
    }

    // ========== 5. COMPTEURS ANIMÉS ==========
    const statNumbers = document.querySelectorAll('.stat-card .number, .hero-stats .stat-number');

    function animateNumber(element) {
        const target = parseInt(element.getAttribute('data-target'));
        if (isNaN(target)) return;

        let current = 0;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 30);
    }

    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const numberElement = entry.target.querySelector('.number') || entry.target;
                if (numberElement && !numberElement.classList.contains('animated')) {
                    numberElement.classList.add('animated');
                    animateNumber(numberElement);
                }
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('.stat-card, .hero-stats .stat').forEach(stat => {
        statsObserver.observe(stat);
    });

    // ========== 6. TOAST NOTIFICATION ==========
    window.showNotification = function(message, type = 'success') {
        // Supprimer les anciennes notifications
        const oldNotifications = document.querySelectorAll('.toast-notification');
        oldNotifications.forEach(notif => notif.remove());

        const notification = document.createElement('div');
        notification.className = `toast-notification toast-${type}`;
        notification.innerHTML = `
            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i>
            <span>${message}</span>
        `;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: ${type === 'success' ? '#006D5B' : '#EF4444'};
            color: white;
            padding: 14px 24px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
            font-family: 'DM Sans', sans-serif;
            font-weight: 500;
            cursor: pointer;
        `;

        document.body.appendChild(notification);

        // Auto-fermeture après 4 secondes
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification && notification.remove) notification.remove();
            }, 300);
        }, 4000);

        // Fermeture au clic
        notification.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });
    };

    // ========== 7. VALIDATION DES FORMULAIRES ==========
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#EF4444';
                    field.style.backgroundColor = 'rgba(239,68,68,0.05)';
                } else {
                    field.style.borderColor = '';
                    field.style.backgroundColor = '';
                }
            });

            if (!isValid) {
                e.preventDefault();
                showNotification('Veuillez remplir tous les champs obligatoires', 'error');
            }
        });

        // Nettoyer les styles au focus
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '';
                this.style.backgroundColor = '';
            });
        });
    });

    // ========== 8. CONFIRMATION DE SUPPRESSION ==========
    const deleteButtons = document.querySelectorAll('.btn-danger, .delete-btn, a[onclick*="confirm"]');
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('⚠️ Êtes-vous sûr de vouloir effectuer cette action ?\n\nCette opération est irréversible.')) {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        });
    });

    // ========== 9. TOGGLE LANGUE (RTL) ==========
    const langFr = document.querySelector('.lang-btn[data-lang="fr"]');
    const langAr = document.querySelector('.lang-btn[data-lang="ar"]');

    if (langFr && langAr) {
        // Vérifier la langue sauvegardée
        const savedLang = localStorage.getItem('innogov_lang');
        if (savedLang === 'ar') {
            document.documentElement.dir = 'rtl';
            langAr.classList.add('active');
            langFr.classList.remove('active');
        } else {
            document.documentElement.dir = 'ltr';
            langFr.classList.add('active');
            langAr.classList.remove('active');
        }

        langFr.addEventListener('click', () => {
            document.documentElement.dir = 'ltr';
            langFr.classList.add('active');
            langAr.classList.remove('active');
            localStorage.setItem('innogov_lang', 'fr');
            showNotification('Langue changée en Français', 'success');
        });

        langAr.addEventListener('click', () => {
            document.documentElement.dir = 'rtl';
            langAr.classList.add('active');
            langFr.classList.remove('active');
            localStorage.setItem('innogov_lang', 'ar');
            showNotification('تم تغيير اللغة إلى العربية', 'success');
        });
    }

    // ========== 10. MENU MOBILE RESPONSIVE ==========
    const createMobileMenu = () => {
        const navbar = document.querySelector('.navbar-container');
        const navMenu = document.querySelector('.nav-menu');

        if (window.innerWidth <= 768 && !document.querySelector('.menu-toggle')) {
            const toggleBtn = document.createElement('button');
            toggleBtn.className = 'menu-toggle';
            toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
            toggleBtn.style.cssText = `
                background: none;
                border: none;
                font-size: 24px;
                cursor: pointer;
                color: #006D5B;
                padding: 8px;
            `;

            toggleBtn.addEventListener('click', () => {
                const isVisible = navMenu.style.display === 'flex';
                navMenu.style.display = isVisible ? 'none' : 'flex';
                navMenu.style.flexDirection = 'column';
                navMenu.style.width = '100%';
                navMenu.style.marginTop = '1rem';
                navMenu.style.gap = '1rem';
            });

            navbar.insertBefore(toggleBtn, navMenu);
            navMenu.style.display = 'none';
        } else if (window.innerWidth > 768) {
            const toggle = document.querySelector('.menu-toggle');
            if (toggle) toggle.remove();
            if (navMenu) {
                navMenu.style.display = 'flex';
                navMenu.style.flexDirection = 'row';
            }
        }
    };

    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);

    // ========== 11. ANIMATION DES CARTES AU SURVOL ==========
    const cards = document.querySelectorAll('.service-card, .stat-card, .news-card, .card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
    });

    // ========== 12. GESTION DES ERREURS AJAX ==========
    window.handleAjaxError = function(xhr, status, error) {
        console.error('Erreur AJAX:', error);
        showNotification('Une erreur est survenue. Veuillez réessayer.', 'error');
    };

    // ========== 13. PRÉVENTION DOUBON CLIC SUR FORMULAIRES ==========
    const submitButtons = document.querySelectorAll('button[type="submit"]');
    submitButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            const form = this.closest('form');
            if (form && form.checkValidity()) {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Chargement...';
                setTimeout(() => {
                    this.disabled = false;
                    this.innerHTML = this.getAttribute('data-original-text') || this.innerHTML;
                }, 3000);
            }
        });
    });

    // Sauvegarder le texte original des boutons
    submitButtons.forEach(btn => {
        btn.setAttribute('data-original-text', btn.innerHTML);
    });
});

// ========== STYLES ADDITIONNELS POUR ANIMATIONS ==========
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
        }
        to {
            opacity: 1;
        }
    }
    
    .menu-toggle {
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #006D5B;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .menu-toggle:hover {
        background: rgba(0, 109, 91, 0.1);
        border-radius: 8px;
    }
    
    @media (max-width: 768px) {
        .nav-menu {
            display: none;
            flex-direction: column;
            width: 100%;
            gap: 1rem;
            padding: 1rem 0;
        }
        
        .nav-menu.show {
            display: flex;
        }
        
        .navbar-container {
            flex-wrap: wrap;
        }
    }
    
    /* Animation de pulsation pour les boutons */
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .btn-primary:active {
        animation: pulse 0.3s ease;
    }
    
    /* Transition pour les cartes */
    .service-card, .stat-card, .news-card, .card {
        will-change: transform;
    }
    
    /* Amélioration du scroll */
    html {
        scroll-behavior: smooth;
    }
`;
document.head.appendChild(style);

// ========== INITIALISATION AOS SI PRÉSENT ==========
if (typeof AOS !== 'undefined') {
    AOS.init({
        duration: 700,
        once: true,
        offset: 100,
        easing: 'ease-out-cubic'
    });
}
