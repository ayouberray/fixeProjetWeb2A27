// ========== SCRIPT DYNAMIQUE INNOGOV PREMIUM ==========

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

// ========== PREMIUM 3D REVEAL ANIMATIONS ==========
const revealElements = document.querySelectorAll('.reveal');
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
            // Unobserve after showing to keep it efficient
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.15, rootMargin: '0px 0px -50px 0px' });

revealElements.forEach(el => revealObserver.observe(el));

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

// ========== HERO SLIDESHOW & PARALLAX ==========
let currentSlide = 0;
const slides = document.querySelectorAll('.hero-slideshow .slide');

if(slides.length > 0){
    setInterval(() => {
        slides[currentSlide].classList.remove('active');
        currentSlide = (currentSlide + 1) % slides.length;
        slides[currentSlide].classList.add('active');
    }, 6000);

    // Parallax on scroll
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const heroSlideshow = document.querySelector('.hero-slideshow');
        if (heroSlideshow) {
            heroSlideshow.style.transform = `translateY(${scrolled * 0.4}px)`;
        }
    });
}

// ========== GLOBAL PREMIUM MODALS ==========
function openPremiumModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden'; // Stop scroll
    }
}

function closePremiumModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = ''; // Resume scroll
    }
}

// Close modals when clicking outside the box
window.addEventListener('click', (e) => {
    if (e.target.classList.contains('premium-modal')) {
        e.target.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// ========== TRADUCTION SYSTEM ==========
let originalTexts = new Map();
const translations = {
    fr: {
        'Accueil': 'Accueil', 'Mes RDV': 'Mes RDV', 'Admin': 'Admin', 'Prendre RDV': 'Prendre RDV',
        'Espace citoyen': 'Espace citoyen', 'Retour': 'Retour', 'Prendre rendez-vous': 'Prendre rendez-vous',
        'En savoir plus': 'En savoir plus', 'Réserver': 'Réserver', 'Annuler': 'Annuler',
        'Modifier': 'Modifier', 'Supprimer': 'Supprimer', 'Enregistrer': 'Enregistrer',
        'en_attente': 'En attente', 'confirme': 'Confirmé', 'annule': 'Annulé', 'termine': 'Terminé'
    },
    ar: {
        'Accueil': 'الرئيسية', 'Mes RDV': 'مواعيدي', 'Admin': 'المشرف', 'Prendre RDV': 'حجز موعد',
        'Espace citoyen': 'مساحة المواطن', 'Retour': 'رجوع', 'Prendre rendez-vous': 'حجز موعد',
        'En savoir plus': 'اقرأ المزيد', 'Réserver': 'حجز', 'Annuler': 'إلغاء',
        'Modifier': 'تعديل', 'Supprimer': 'حذف', 'Enregistrer': 'حفظ',
        'en_attente': 'قيد الانتظار', 'confirme': 'مؤكد', 'annule': 'ملغي', 'termine': 'منتهي'
    }
};

function saveOriginalTexts() {
    const elements = document.querySelectorAll('h1, h2, h3, h4, h5, p, a, button, span, label, th, td');
    elements.forEach(el => {
        if(el.children.length === 0 || (el.children.length === 1 && el.children[0].tagName === 'I')) {
            const text = el.textContent.trim();
            if(text && !originalTexts.has(el)) originalTexts.set(el, text);
        }
    });
}

function translatePage(lang) {
    originalTexts.forEach((text, el) => {
        if(translations[lang] && translations[lang][text]) {
            const icon = el.querySelector('i');
            if(icon) {
                el.innerHTML = icon.outerHTML + ' ' + translations[lang][text];
            } else {
                el.textContent = translations[lang][text];
            }
        }
    });
    document.documentElement.setAttribute('dir', lang === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.setAttribute('lang', lang);
    localStorage.setItem('preferred_language', lang);
    
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.classList.toggle('active', btn.getAttribute('data-lang') === lang);
    });
}

// ========== AUTO-SCROLL AFTER SLIDESHOW ==========
function autoScrollAfterSlideshow() {
    setTimeout(() => {
        const hero = document.querySelector('.hero');
        if (hero) {
            window.scrollTo({
                top: hero.offsetHeight - 80,
                behavior: 'smooth'
            });
        }
    }, 2500);
}

// ========== DARK MODE ==========
function initDarkMode() {
    const btn = document.querySelector('.theme-toggle');
    if(!btn) return;
    const icon = btn.querySelector('i');
    const saved = localStorage.getItem('theme') || 'light';
    
    const apply = (theme) => {
        if(theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            if(icon) icon.className = 'fas fa-moon';
        } else {
            document.documentElement.removeAttribute('data-theme');
            if(icon) icon.className = 'fas fa-sun';
        }
    };
    apply(saved);
    btn.addEventListener('click', () => {
        const now = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        apply(now);
        localStorage.setItem('theme', now);
    });
}

// ========== ADVANCED 3D TILT ==========
function init3DTilt() {
    const cards = document.querySelectorAll('.stat-card, .service-card, .news-card, .card, .rdv-card');
    cards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -12;
            const rotateY = ((x - centerX) / centerX) * 12;
            
            card.style.transform = `perspective(1000px) translateY(-10px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            card.style.boxShadow = `${-rotateY * 0.5}px ${rotateX * 0.5}px 40px rgba(0,0,0,0.2)`;
        });
        
        card.addEventListener('mouseleave', () => {
            card.style.transform = `perspective(1000px) translateY(0) rotateX(0) rotateY(0)`;
            card.style.boxShadow = '';
        });
    });
}

// ========== INITIALISATION ==========
document.addEventListener('DOMContentLoaded', function() {
    saveOriginalTexts();
    const savedLang = localStorage.getItem('preferred_language') || 'fr';
    translatePage(savedLang);
    
    document.querySelectorAll('.lang-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            translatePage(btn.getAttribute('data-lang'));
        });
    });
    
    autoScrollAfterSlideshow();
    initDarkMode();
    init3DTilt();
});

// ========== NAVBAR SCROLL EFFECT ==========
(function() {
    const navbar = document.querySelector('.navbar.floating-pill');
    if (!navbar) return;
    const handle = () => {
        if (window.scrollY > 60) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    };
    handle();
    window.addEventListener('scroll', handle, { passive: true });
})();

// ========== ULTRA PREMIUM 3D CHATBOT LOGIC ==========
(function() {
    const toggle = document.getElementById('chatbot-toggle');
    const container = document.getElementById('chatbot-container');
    const close = document.getElementById('chatbot-close');
    const input = document.getElementById('chatbot-input');
    const sendBtn = document.getElementById('chatbot-send-btn');
    const messages = document.getElementById('chatbot-messages');
    const uploadBtn = document.getElementById('chatbot-upload-btn');
    const fileInput = document.getElementById('chatbot-file-input');

    if (!toggle || !container) return;

    // Toggle Chatbot
    toggle.addEventListener('click', () => {
        container.classList.toggle('active');
        if (container.classList.contains('active')) {
            if (messages.children.length === 0) {
                addMessage('Bonjour ! Je suis InnoBot, votre assistant municipal intelligent. Comment puis-je vous aider aujourd\'hui ?', 'bot');
            }
            input.focus();
        }
    });

    if (close) close.addEventListener('click', () => container.classList.remove('active'));

    // Handle Messages
    function addMessage(text, side) {
        const msg = document.createElement('div');
        msg.className = `message ${side}`;
        msg.innerHTML = text;
        messages.appendChild(msg);
        messages.scrollTop = messages.scrollHeight;
        return msg;
    }

    let currentBase64Image = null;

    async function handleSend() {
        const text = input.value.trim();
        if (!text && !currentBase64Image) return;

        addMessage(text || "<em>[Analyse d'image...]</em>", 'user');
        input.value = '';

        // Add "Typing" indicator
        const typingMsg = addMessage('<span class="typing-dots"><span></span><span></span><span></span></span>', 'bot');
        
        try {
            const response = await fetch('/Gestion_RDV/projet/api/chatbot_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    q: text,
                    image: currentBase64Image
                })
            });

            const data = await response.json();
            typingMsg.innerHTML = data.text;
            currentBase64Image = null; // Reset image after send
        } catch (error) {
            typingMsg.innerHTML = "Désolé, je rencontre une difficulté technique pour me connecter à mon cerveau IA. Veuillez réessayer plus tard.";
            console.error("Chatbot Error:", error);
        }
        
        messages.scrollTop = messages.scrollHeight;
    }

    if (sendBtn) sendBtn.addEventListener('click', handleSend);
    if (input) input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') handleSend();
    });

    // File Upload Simulation & Base64 conversion
    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', () => {
            const file = fileInput.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    currentBase64Image = e.target.result;
                    addMessage(`<i class="fas fa-image"></i> Image prête pour analyse : <strong>${file.name}</strong>`, 'user');
                    addMessage("Cliquez sur envoyer pour que j'analyse cette image.", 'bot');
                };
                reader.readAsDataURL(file);
            }
        });
    }
})();