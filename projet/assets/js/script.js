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

// ========== SLIDESHOW AUTOMATIQUE ==========
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slideshow .slide');

if(slides.length > 0){
    setInterval(() => {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }, 5000);
}

// ========== TRADUCTION BIDIRECTIONNELLE (qui marche) ==========

// Stockage des textes originaux en français
let originalTexts = new Map();

// Dictionnaire complet des traductions
const translations = {
    fr: {
        // Navigation
        'Accueil': 'Accueil',
        'Mes RDV': 'Mes RDV',
        'Admin': 'Admin',
        'Prendre RDV': 'Prendre RDV',
        'Espace citoyen': 'Espace citoyen',
        'Retour': 'Retour',
        
        // Hero
        'Services Municipaux Digitalisés': 'Services Municipaux Digitalisés',
        'Simplifiez vos démarches administratives en ligne': 'Simplifiez vos démarches administratives en ligne',
        'Prendre rendez-vous': 'Prendre rendez-vous',
        'En savoir plus': 'En savoir plus',
        
        // Titres
        'Mes rendez-vous': 'Mes rendez-vous',
        'Nouveau RDV': 'Nouveau RDV',
        'Réserver un rendez-vous': 'Réserver un rendez-vous',
        'Modifier rendez-vous': 'Modifier rendez-vous',
        'Administration': 'Administration',
        'Tous les rendez-vous': 'Tous les rendez-vous',
        'Statistiques': 'Statistiques',
        'Gestion des services municipaux': 'Gestion des services municipaux',
        'Services municipaux': 'Services municipaux',
        'Ajouter un service': 'Ajouter un service',
        'Modifier un service': 'Modifier un service',
        
        // Formulaires
        'Bienvenue': 'Bienvenue',
        'Service': 'Service',
        'Date et heure': 'Date et heure',
        'Motif': 'Motif',
        'Choisir un service': 'Choisir un service',
        'Description': 'Description',
        'Durée moyenne (minutes)': 'Durée moyenne (minutes)',
        'Statut': 'Statut',
        'Actif': 'Actif',
        'Inactif': 'Inactif',
        'Décrivez l\'objet de votre rendez-vous...': 'Décrivez l\'objet de votre rendez-vous...',
        
        // Boutons
        'Réserver': 'Réserver',
        'Annuler': 'Annuler',
        'Modifier': 'Modifier',
        'Supprimer': 'Supprimer',
        'Enregistrer': 'Enregistrer',
        'Ajouter un RDV': 'Ajouter un RDV',
        'Voir stats': 'Voir stats',
        'Nouveau service': 'Nouveau service',
        'Créer': 'Créer',
        
        // Tableau
        'ID': 'ID',
        'Citoyen': 'Citoyen',
        'Agent': 'Agent',
        'Actions': 'Actions',
        'Non affecté': 'Non affecté',
        'Aucun rendez-vous trouvé': 'Aucun rendez-vous trouvé',
        'Aucun service trouvé': 'Aucun service trouvé',
        'Nom du service': 'Nom du service',
        'Durée': 'Durée',
        
        // Statuts
        'en_attente': 'En attente',
        'confirme': 'Confirmé',
        'annule': 'Annulé',
        'termine': 'Terminé',
        
        // Admin stats
        'Gérez tous les rendez-vous de la municipalité': 'Gérez tous les rendez-vous de la municipalité',
        'Total RDV': 'Total RDV',
        'En attente': 'En attente',
        'Confirmés': 'Confirmés',
        'Annulés': 'Annulés',
        'Traités': 'Traités',
        'Par service': 'Par service',
        'Par agent': 'Par agent',
        'Chiffres Clés': 'Chiffres Clés',
        'Citoyens': 'Citoyens',
        'Services': 'Services',
        'RDV traités': 'RDV traités',
        'Années d\'expérience': 'Années d\'expérience',
        'Nos Services': 'Nos Services',
        'Actualités': 'Actualités',
        'Lire la suite': 'Lire la suite',
        
        // Footer
        'Contact': 'Contact',
        'Horaires': 'Horaires',
        'Lundi - Vendredi: 8h30 - 15h30': 'Lundi - Vendredi: 8h30 - 15h30',
        'Tous droits réservés': 'Tous droits réservés',
        'InnoGov': 'InnoGov',
        'Municipalité Tunisienne': 'Municipalité Tunisienne',
        'Plateforme de services municipaux': 'Plateforme de services municipaux',
        
        // Messages
        'Supprimer ce rendez-vous ?': 'Supprimer ce rendez-vous ?',
        'Annuler ce rendez-vous ?': 'Annuler ce rendez-vous ?',
        'Supprimer ce service ?': 'Supprimer ce service ?'
    },
    ar: {
        // Navigation
        'Accueil': 'الرئيسية',
        'Mes RDV': 'مواعيدي',
        'Admin': 'المشرف',
        'Prendre RDV': 'حجز موعد',
        'Espace citoyen': 'مساحة المواطن',
        'Retour': 'رجوع',
        
        // Hero
        'Services Municipaux Digitalisés': 'الخدمات البلدية الرقمية',
        'Simplifiez vos démarches administratives en ligne': 'تبسيط الإجراءات الإدارية عبر الإنترنت',
        'Prendre rendez-vous': 'حجز موعد',
        'En savoir plus': 'اقرأ المزيد',
        
        // Titres
        'Mes rendez-vous': 'مواعيدي',
        'Nouveau RDV': 'موعد جديد',
        'Réserver un rendez-vous': 'حجز موعد',
        'Modifier rendez-vous': 'تعديل موعد',
        'Administration': 'الإدارة',
        'Tous les rendez-vous': 'جميع المواعيد',
        'Statistiques': 'إحصائيات',
        'Gestion des services municipaux': 'إدارة الخدمات البلدية',
        'Services municipaux': 'الخدمات البلدية',
        'Ajouter un service': 'إضافة خدمة',
        'Modifier un service': 'تعديل خدمة',
        
        // Formulaires
        'Bienvenue': 'مرحباً',
        'Service': 'الخدمة',
        'Date et heure': 'التاريخ والوقت',
        'Motif': 'السبب',
        'Choisir un service': 'اختر خدمة',
        'Description': 'الوصف',
        'Durée moyenne (minutes)': 'المدة المتوسطة (دقائق)',
        'Statut': 'الحالة',
        'Actif': 'نشط',
        'Inactif': 'غير نشط',
        'Décrivez l\'objet de votre rendez-vous...': 'صف سبب زيارتك...',
        
        // Boutons
        'Réserver': 'حجز',
        'Annuler': 'إلغاء',
        'Modifier': 'تعديل',
        'Supprimer': 'حذف',
        'Enregistrer': 'حفظ',
        'Ajouter un RDV': 'إضافة موعد',
        'Voir stats': 'عرض الإحصائيات',
        'Nouveau service': 'خدمة جديدة',
        'Créer': 'إنشاء',
        
        // Tableau
        'ID': 'الرقم',
        'Citoyen': 'المواطن',
        'Agent': 'الموظف',
        'Actions': 'إجراءات',
        'Non affecté': 'غير معين',
        'Aucun rendez-vous trouvé': 'لا توجد مواعيد',
        'Aucun service trouvé': 'لا توجد خدمات',
        'Nom du service': 'اسم الخدمة',
        'Durée': 'المدة',
        
        // Statuts
        'en_attente': 'قيد الانتظار',
        'confirme': 'مؤكد',
        'annule': 'ملغي',
        'termine': 'منتهي',
        
        // Admin stats
        'Gérez tous les rendez-vous de la municipalité': 'إدارة جميع مواعيد البلدية',
        'Total RDV': 'إجمالي المواعيد',
        'En attente': 'قيد الانتظار',
        'Confirmés': 'مؤكدة',
        'Annulés': 'ملغاة',
        'Traités': 'منتهية',
        'Par service': 'حسب الخدمة',
        'Par agent': 'حسب الموظف',
        'Chiffres Clés': 'إحصائيات رئيسية',
        'Citoyens': 'مواطن',
        'Services': 'خدمة',
        'RDV traités': 'موعد تمت معالجته',
        'Années d\'expérience': 'سنة خبرة',
        'Nos Services': 'خدماتنا',
        'Actualités': 'آخر الأخبار',
        'Lire la suite': 'اقرأ المزيد',
        
        // Footer
        'Contact': 'اتصل بنا',
        'Horaires': 'ساعات العمل',
        'Lundi - Vendredi: 8h30 - 15h30': 'الإثنين - الجمعة: 8:30 - 15:30',
        'Tous droits réservés': 'جميع الحقوق محفوظة',
        'InnoGov': 'إنوغوف',
        'Municipalité Tunisienne': 'البلدية التونسية',
        'Plateforme de services municipaux': 'منصة الخدمات البلدية',
        
        // Messages
        'Supprimer ce rendez-vous ?': 'هل تريد حذف هذا الموعد؟',
        'Annuler ce rendez-vous ?': 'هل تريد إلغاء هذا الموعد؟',
        'Supprimer ce service ?': 'هل تريد حذف هذه الخدمة؟'
    }
};

let currentLang = 'fr';

// Sauvegarder les textes originaux (une seule fois au chargement)
function saveOriginalTexts() {
    // Éléments à sauvegarder
    const elementsToSave = document.querySelectorAll('h1, h2, h3, h4, h5, h6, p, a, button, span, div, label, th, td, li, option, .card-title, .nav-link, .btn, .hero-content h1, .hero-content p, .form-label, .card-header h2, .footer-section h4, .footer-section p');
    
    elementsToSave.forEach(element => {
        // Sauvegarder uniquement si l'élément a du texte et pas déjà sauvegardé
        if(element.children.length === 0 || (element.children.length === 1 && element.children[0].tagName === 'I')) {
            const text = element.textContent.trim();
            if(text && !originalTexts.has(element)) {
                originalTexts.set(element, text);
            }
        }
    });
    
    // Sauvegarder les placeholders
    document.querySelectorAll('input, textarea, select').forEach(element => {
        const placeholder = element.getAttribute('placeholder');
        if(placeholder && !originalTexts.has(element)) {
            originalTexts.set(element, placeholder);
        }
    });
}

// Fonction pour traduire la page
function translatePage(lang) {
    currentLang = lang;
    
    // Traduire les textes des éléments
    originalTexts.forEach((originalText, element) => {
        // Vérifier si c'est un placeholder
        if(element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
            if(translations[lang][originalText]) {
                element.setAttribute('placeholder', translations[lang][originalText]);
            } else if(lang === 'fr') {
                element.setAttribute('placeholder', originalText);
            }
        } else {
            // Traduire le texte normal
            if(translations[lang][originalText]) {
                // Sauvegarder l'icône si elle existe
                const icon = element.querySelector('i');
                if(icon) {
                    const iconHTML = icon.outerHTML;
                    element.innerHTML = iconHTML + ' ' + translations[lang][originalText];
                } else {
                    element.textContent = translations[lang][originalText];
                }
            } else if(lang === 'fr') {
                // Revenir au texte original
                const icon = element.querySelector('i');
                if(icon) {
                    const iconHTML = icon.outerHTML;
                    element.innerHTML = iconHTML + ' ' + originalText;
                } else {
                    element.textContent = originalText;
                }
            }
        }
    });
    
    // Traduire les options des selects
    document.querySelectorAll('option').forEach(option => {
        const originalOptionText = originalTexts.get(option);
        if(originalOptionText && translations[lang][originalOptionText]) {
            option.textContent = translations[lang][originalOptionText];
        } else if(lang === 'fr' && originalOptionText) {
            option.textContent = originalOptionText;
        }
    });
    
    // Changer la direction RTL pour l'arabe
    if(lang === 'ar') {
        document.documentElement.setAttribute('dir', 'rtl');
        document.documentElement.setAttribute('lang', 'ar');
        document.body.style.direction = 'rtl';
        document.body.style.textAlign = 'right';
    } else {
        document.documentElement.setAttribute('dir', 'ltr');
        document.documentElement.setAttribute('lang', 'fr');
        document.body.style.direction = 'ltr';
        document.body.style.textAlign = 'left';
    }
    
    // Mettre à jour l'état actif des boutons
    document.querySelectorAll('.lang-btn').forEach(btn => {
        if(btn.getAttribute('data-lang') === lang) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    
    // Sauvegarder la langue
    localStorage.setItem('preferred_language', lang);
}

// Charger la langue sauvegardée
function loadSavedLanguage() {
    const savedLang = localStorage.getItem('preferred_language');
    if(savedLang && (savedLang === 'fr' || savedLang === 'ar')) {
        translatePage(savedLang);
    } else {
        translatePage('fr');
    }
}

// ========== NOTIFICATION TOAST ==========
function showToast(message, type = 'success') {
    let translatedMessage = message;
    if(currentLang === 'ar' && translations.ar[message]) {
        translatedMessage = translations.ar[message];
    }
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    const icon = type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle');
    toast.innerHTML = `<i class="fas ${icon}"></i><span>${translatedMessage}</span>`;
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
        border-left: 4px solid ${type === 'success' ? '#00A86B' : (type === 'error' ? '#E31E24' : '#FFB800')};
        font-family: 'Inter', sans-serif;
    `;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// Ajouter les animations CSS
const toastStyle = document.createElement('style');
toastStyle.textContent = `
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(toastStyle);

// ========== AUTO-SCROLL APRÈS SLIDESHOW ==========
function autoScrollAfterSlideshow() {
    const pathname = window.location.pathname;
    const fileName = pathname.split('/').pop();
    const noScrollPages = ['index.php', 'index', ''];
    const isHomePage = noScrollPages.includes(fileName) || 
                       pathname === '/projet/' || 
                       pathname === '/projet/index.php' ||
                       pathname === '/';
    
    if(isHomePage) {
        return;
    }
    
    setTimeout(() => {
        const heroSection = document.querySelector('.hero');
        if(heroSection) {
            const heroHeight = heroSection.offsetHeight;
            window.scrollTo({
                top: heroHeight,
                behavior: 'smooth'
            });
        } else {
            const mainContent = document.querySelector('.container, .card, main');
            if(mainContent) {
                mainContent.scrollIntoView({ behavior: 'smooth' });
            }
        }
    }, 1300);
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    // Sauvegarder tous les textes originaux
    saveOriginalTexts();
    
    // Charger la langue sauvegardée
    loadSavedLanguage();
    
    // Initialiser les boutons de langue
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const lang = this.getAttribute('data-lang');
            translatePage(lang);
        });
    });
    
    // Auto-scroll
    autoScrollAfterSlideshow();
});