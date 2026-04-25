// ============================================
// INNOGOV - MAIN JAVASCRIPT
// Animations, compteurs, slideshow, loader, etc.
// ============================================

// ===== LOADER =====
window.addEventListener('load', function() {
    const loader = document.getElementById('loader');
    if (loader) {
        setTimeout(() => {
            loader.classList.add('hide');
        }, 500);
    }
});

// ===== NAVBAR SCROLL EFFECT =====
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }
});

// ===== SCROLL REVEAL =====
const revealElements = document.querySelectorAll('.reveal');

function checkReveal() {
    revealElements.forEach(element => {
        const rect = element.getBoundingClientRect();
        const windowHeight = window.innerHeight;
        if (rect.top < windowHeight - 100) {
            element.classList.add('active');
        }
    });
}

window.addEventListener('scroll', checkReveal);
window.addEventListener('load', checkReveal);

// ===== COMPTEURS ANIMÉS =====
const counters = document.querySelectorAll('.counter');
let animated = false;

function animateCounters() {
    if (animated) return;
    
    const triggerPoint = window.innerHeight * 0.8;
    const countersSection = document.querySelector('.stats-grid');
    
    if (countersSection && countersSection.getBoundingClientRect().top < triggerPoint) {
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-target'));
            let current = 0;
            const increment = target / 50;
            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    counter.innerText = Math.ceil(current);
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.innerText = target;
                }
            };
            updateCounter();
        });
        animated = true;
    }
}

window.addEventListener('scroll', animateCounters);
window.addEventListener('load', animateCounters);

// ===== SLIDESHOW (pour les pages autres que index) =====
let currentSlide = 0;
let slideInterval;

function initSlideshow() {
    const slides = document.querySelectorAll('.slide');
    if (slides.length === 0) return;
    
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
    
    showSlide(0);
    slideInterval = setInterval(nextSlide, 5000);
}

// Démarrer le slideshow si on n'est pas sur la page d'accueil (sans vidéo)
if (!document.querySelector('.hero-video')) {
    initSlideshow();
}

// ===== TOAST NOTIFICATIONS =====
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'error' ? 'exclamation-circle' : 'info-circle')}"></i>
        <span>${message}</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// ===== TOGGLE LANGUE (Français / Arabe) =====
let currentLang = 'fr';

function toggleLanguage() {
    const body = document.body;
    const langBtns = document.querySelectorAll('.lang-btn');
    
    langBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.getAttribute('data-lang');
            currentLang = lang;
            
            // Mettre à jour les classes actives
            langBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Changer la direction pour l'arabe
            if (lang === 'ar') {
                body.classList.add('rtl');
                document.documentElement.setAttribute('dir', 'rtl');
                document.documentElement.setAttribute('lang', 'ar');
            } else {
                body.classList.remove('rtl');
                document.documentElement.setAttribute('dir', 'ltr');
                document.documentElement.setAttribute('lang', 'fr');
            }
            
            // Mettre à jour les textes (à implémenter avec un fichier de traduction)
            updateTranslations(lang);
        });
    });
}

function updateTranslations(lang) {
    // Les traductions seront chargées dynamiquement
    // Pour l'instant, on peut stocker les traductions dans un objet
    const translations = {
        fr: {
            home: 'Accueil',
            services: 'Services',
            contact: 'Contact',
            login: 'Connexion',
            register: 'Inscription',
            welcome: 'Bienvenue sur innoGov',
            subtitle: 'Digitaliser aujourd\'hui, servir mieux demain'
        },
        ar: {
            home: 'الرئيسية',
            services: 'الخدمات',
            contact: 'اتصل بنا',
            login: 'تسجيل الدخول',
            register: 'إنشاء حساب',
            welcome: 'مرحباً بكم في innoGov',
            subtitle: 'نرقمن اليوم لنخدم بشكل أفضل غداً'
        }
    };
    
    const t = translations[lang];
    if (t) {
        document.querySelectorAll('[data-translate]').forEach(el => {
            const key = el.getAttribute('data-translate');
            if (t[key]) el.textContent = t[key];
        });
    }
}

// Initialiser le toggle langue
toggleLanguage();

// ===== LIENS DOUX (smooth scroll) =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// ===== EFFET HOVER SUR LES CARTES =====
const cards = document.querySelectorAll('.service-card, .stat-card, .news-card');
cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transition = 'all 0.3s ease';
    });
});