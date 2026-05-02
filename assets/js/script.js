// ========== SCRIPT INNOC@V - TOUTES INTERACTIONS ==========

// Loader
window.addEventListener('load', () => {
    const loader = document.querySelector('.loader');
    if(loader) {
        setTimeout(() => {
            loader.classList.add('hide');
            setTimeout(() => loader.remove(), 500);
        }, 500);
    }
});


const revealElements = document.querySelectorAll('.reveal');
function checkReveal() {
    revealElements.forEach(el => {
        const rect = el.getBoundingClientRect();
        if(rect.top < window.innerHeight - 100) el.classList.add('active');
    });
}
window.addEventListener('scroll', checkReveal);
window.addEventListener('load', checkReveal);

// Compteurs animés
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const update = () => {
        start += increment;
        if(start < target) {
            element.textContent = Math.floor(start);
            requestAnimationFrame(update);
        } else element.textContent = target;
    };
    update();
}
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if(entry.isIntersecting) {
            const numbers = entry.target.querySelectorAll('.number');
            numbers.forEach(num => {
                const target = parseInt(num.getAttribute('data-target'));
                if(target && !num.classList.contains('animated')) {
                    num.classList.add('animated');
                    animateCounter(num, target);
                }
            });
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });
const statsGrid = document.querySelector('.stats-grid');
if(statsGrid) statsObserver.observe(statsGrid);

// Slideshow automatique
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slideshow .slide');
if(slides.length > 0) {
    setInterval(() => {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }, 5000);
}

// Traductions FR/AR + RTL et Theme
let currentLang = localStorage.getItem('lang') || 'fr';
let currentTheme = localStorage.getItem('theme') || 'light';
document.documentElement.setAttribute('data-theme', currentTheme);

const translations = {
    fr: {
        home: 'Accueil', offers: 'Offres', admin: 'Admin Offres', candidatures: 'Candidatures',
        seeOffers: 'Voir les offres',
        heroBadge: '✨ Recrutement public simplifié', heroTitle: 'Offres d\'emploi<br>municipales digitalisées',
        heroDesc: 'Postulez en ligne aux offres de la fonction publique territoriale',
        heroBtn1: 'Découvrir les offres', heroBtn2: 'En savoir plus',
        statsTitle: 'Chiffres Clés', totalOffers: 'Total offres', openOffers: 'Offres ouvertes',
        totalApplications: 'Candidatures reçues', satisfaction: 'Taux de satisfaction',
        latestOffers: '📢 Dernières offres',
        newsTitle: 'Actualités recrutement', readMore: 'Lire la suite',
        footerDesc: 'Plateforme de gestion des offres et candidatures pour les municipalités',
        monFri: 'Lundi - Vendredi: 8h30 - 15h30', online: 'Formulaire de contact 24h/24', allRights: 'Tous droits réservés'
    },
    ar: {
        home: 'الرئيسية', offers: 'العروض', admin: 'إدارة العروض', candidatures: 'الترشحات',
        seeOffers: 'تصفح العروض',
        heroBadge: '✨ توظيف عمومي مبسط', heroTitle: 'عروض عمل<br>بلدية رقمية',
        heroDesc: 'قدم عبر الإنترنت لعروض الوظيفة العمومية المحلية',
        heroBtn1: 'استكشاف العروض', heroBtn2: 'اقرأ المزيد',
        statsTitle: 'إحصائيات رئيسية', totalOffers: 'إجمالي العروض', openOffers: 'العروض المفتوحة',
        totalApplications: 'الترشحات المستلمة', satisfaction: 'نسبة الرضا',
        latestOffers: '📢 أحدث العروض',
        newsTitle: 'أخبار التوظيف', readMore: 'اقرأ المزيد',
        footerDesc: 'منصة إدارة عروض العمل والترشحات للبلديات',
        monFri: 'الإثنين - الجمعة: 8:30 - 15:30', online: 'نموذج اتصال 24/24', allRights: 'جميع الحقوق محفوظة'
    }
};

function switchLanguage(lang) {
    currentLang = lang;
    localStorage.setItem('lang', lang);
    document.querySelectorAll('[data-i18n]').forEach(el => {
        const key = el.getAttribute('data-i18n');
        if(translations[lang][key]) {
            if(el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') el.placeholder = translations[lang][key];
            else el.innerHTML = translations[lang][key];
        }
    });
    document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = lang;
    document.querySelectorAll('.lang-btn').forEach(btn => {
        if(btn.getAttribute('data-lang') === lang) btn.classList.add('active');
        else btn.classList.remove('active');
    });
}
document.addEventListener('DOMContentLoaded', () => {
    switchLanguage(currentLang);
    
    // Theme Toggle
    const themeBtns = document.querySelectorAll('#theme-toggle');
    themeBtns.forEach(btn => {
        if (currentTheme === 'dark') {
            btn.innerHTML = '<i class="fas fa-sun"></i>';
        }
        btn.addEventListener('click', () => {
            currentTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', currentTheme);
            localStorage.setItem('theme', currentTheme);
            document.querySelectorAll('#theme-toggle').forEach(b => {
                b.innerHTML = currentTheme === 'dark' ? '<i class="fas fa-sun"></i>' : '<i class="fas fa-moon"></i>';
            });
        });
    });
});
document.querySelectorAll('.lang-btn:not(#theme-toggle)').forEach(btn => {
    btn.addEventListener('click', () => switchLanguage(btn.getAttribute('data-lang')));
});

// Toast helper (si besoin)
window.showToast = function(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast-notif ${type === 'error' ? 'error' : ''}`;
    toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
};