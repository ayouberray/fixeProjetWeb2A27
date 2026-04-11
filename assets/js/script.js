// ========== SCRIPT DYNAMIQUE INNOGOV ==========

// Loader
window.addEventListener('load', function() {
    const loader = document.querySelector('.loader');
    if(loader) {
        setTimeout(() => {
            loader.classList.add('hide');
            setTimeout(() => loader.remove(), 500);
        }, 500);
    }
});

// Scroll Reveal
const revealElements = document.querySelectorAll('.reveal');

function checkReveal() {
    revealElements.forEach(element => {
        const elementTop = element.getBoundingClientRect().top;
        const windowHeight = window.innerHeight;
        if(elementTop < windowHeight - 100) {
            element.classList.add('active');
        }
    });
}

window.addEventListener('scroll', checkReveal);
window.addEventListener('load', checkReveal);

// ========== COMPTEURS ANIMÉS ==========
function animateCounter(element, target, duration = 2000) {
    let start = 0;
    const increment = target / (duration / 16);
    const updateCounter = () => {
        start += increment;
        if(start < target) {
            element.textContent = Math.floor(start);
            requestAnimationFrame(updateCounter);
        } else {
            element.textContent = target;
        }
    };
    updateCounter();
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

// ========== SLIDESHOW AUTOMATIQUE (pour frontoffice et backoffice) ==========
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slideshow .slide');

if(slides.length > 0){
    setInterval(() => {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }, 5000);
}

// ========== LANGUE TOGGLE ==========
let currentLang = 'fr';

const translations = {
    fr: {
        home: 'Accueil',
        myAppointments: 'Mes RDV',
        admin: 'Admin',
        bookAppointment: 'Prendre RDV',
        heroTitle: 'Services Municipaux Digitalisés',
        heroSubtitle: 'Simplifiez vos démarches administratives en ligne',
        heroBtn1: 'Prendre rendez-vous',
        heroBtn2: 'En savoir plus',
        statsTitle: 'Chiffres Clés',
        citizens: 'Citoyens',
        services: 'Services',
        appointments: 'RDV traités',
        experience: 'Années d\'expérience',
        servicesTitle: 'Nos Services',
        newsTitle: 'Actualités',
        readMore: 'Lire la suite',
        contact: 'Contact',
        hours: 'Horaires',
        monFri: 'Lundi - Vendredi: 8h30 - 15h30',
        allRights: 'Tous droits réservés'
    },
    ar: {
        home: 'الرئيسية',
        myAppointments: 'مواعيدي',
        admin: 'المشرف',
        bookAppointment: 'حجز موعد',
        heroTitle: 'الخدمات البلدية الرقمية',
        heroSubtitle: 'تبسيط الإجراءات الإدارية عبر الإنترنت',
        heroBtn1: 'حجز موعد',
        heroBtn2: 'اقرأ المزيد',
        statsTitle: 'إحصائيات رئيسية',
        citizens: 'مواطن',
        services: 'خدمة',
        appointments: 'موعد تمت معالجته',
        experience: 'سنة خبرة',
        servicesTitle: 'خدماتنا',
        newsTitle: 'آخر الأخبار',
        readMore: 'اقرأ المزيد',
        contact: 'اتصل بنا',
        hours: 'ساعات العمل',
        monFri: 'الإثنين - الجمعة: 8:30 - 15:30',
        allRights: 'جميع الحقوق محفوظة'
    }
};

function switchLanguage(lang) {
    currentLang = lang;
    document.querySelectorAll('[data-i18n]').forEach(element => {
        const key = element.getAttribute('data-i18n');
        if(translations[lang][key]) {
            if(element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
                element.placeholder = translations[lang][key];
            } else {
                element.textContent = translations[lang][key];
            }
        }
    });
    document.documentElement.dir = lang === 'ar' ? 'rtl' : 'ltr';
    document.documentElement.lang = lang;
    
    document.querySelectorAll('.lang-btn').forEach(btn => {
        if(btn.getAttribute('data-lang') === lang) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const langButtons = document.querySelectorAll('.lang-btn');
    langButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            switchLanguage(btn.getAttribute('data-lang'));
        });
    });
});

// ========== NOTIFICATION TOAST ==========
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
    toast.style.cssText = `
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: white;
        padding: 14px 24px;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9999;
        animation: slideInRight 0.3s ease;
        border-left: 4px solid ${type === 'success' ? '#00A86B' : '#E31E24'};
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);