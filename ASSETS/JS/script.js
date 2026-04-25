// ============================================
// INNOGOV - SCRIPT COMPLET AVEC VALIDATION
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
    if (revealElements.length > 0) {
        checkReveal();
        window.addEventListener('scroll', checkReveal);
    }

    // ========== 4. SLIDESHOW ==========
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if (slides.length > 0) {
        let currentSlide = 0;

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) slide.classList.add('active');
            });
        }

        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
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
        const oldNotifications = document.querySelectorAll('.toast-notification');
        oldNotifications.forEach(notif => notif.remove());

        const notification = document.createElement('div');
        notification.className = `toast-notification toast-${type}`;
        notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i><span>${message}</span>`;
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

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification && notification.remove) notification.remove();
            }, 300);
        }, 4000);

        notification.addEventListener('click', () => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });
    };

    // ========== 7. VALIDATION DU FORMULAIRE DE RÉCLAMATION ==========
    window.initReclamationValidation = function() {
        const form = document.getElementById('reclamationForm');
        if (!form) return;

        const categorie = document.getElementById('categorie');
        const objet = document.getElementById('objet');
        const description = document.getElementById('description');
        const submitBtn = document.getElementById('submitBtn');

        function validateField(field, validationFn, errorId) {
            const isValid = validationFn(field.value);
            const errorDiv = document.getElementById(errorId);

            if (!isValid) {
                field.classList.add('error');
                if (errorDiv) errorDiv.classList.add('show');
                return false;
            } else {
                field.classList.remove('error');
                if (errorDiv) errorDiv.classList.remove('show');
                return true;
            }
        }

        const validators = {
            categorie: (value) => value !== '',
            objet: (value) => value.trim().length >= 5,
            description: (value) => value.trim().length >= 20
        };

        function validateAll() {
            const isCategorieValid = validateField(categorie, validators.categorie, 'categorieError');
            const isObjetValid = validateField(objet, validators.objet, 'objetError');
            const isDescriptionValid = validateField(description, validators.description, 'descriptionError');
            return isCategorieValid && isObjetValid && isDescriptionValid;
        }

        if (categorie) categorie.addEventListener('change', () => validateField(categorie, validators.categorie, 'categorieError'));
        if (objet) objet.addEventListener('input', () => validateField(objet, validators.objet, 'objetError'));
        if (description) description.addEventListener('input', () => validateField(description, validators.description, 'descriptionError'));

        form.addEventListener('submit', function(e) {
            if (!validateAll()) {
                e.preventDefault();
                showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
                return false;
            }
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            }
        });
    };

    // ========== 8. VALIDATION DU FORMULAIRE DE RÉPONSE (BACK-OFFICE) ==========
    const reponseForm = document.getElementById('reponseForm');
    if (reponseForm) {
        reponseForm.addEventListener('submit', function(e) {
            const contenu = document.getElementById('contenu');
            const nomAgent = document.getElementById('nom_agent');
            let isValid = true;

            if (contenu && contenu.value.trim().length < 10) {
                isValid = false;
                contenu.style.borderColor = '#EF4444';
                showNotification('Le contenu de la réponse doit faire au moins 10 caractères.', 'error');
            }

            if (nomAgent && nomAgent.value.trim().length < 2) {
                isValid = false;
                nomAgent.style.borderColor = '#EF4444';
                showNotification('Le nom de l\'agent est requis.', 'error');
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }

    // ========== 9. CONFIRMATION DE SUPPRESSION ==========
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

    // ========== 10. FONCTIONS POUR MODALES (BACK-OFFICE) ==========
    window.showDetails = function(reclamation) {
        const modal = document.getElementById('detailsModal');
        const modalBody = document.getElementById('modalBody');
        if (!modal || !modalBody) return;

        let statusBadge = '';
        switch(reclamation.statut) {
            case 'soumise': statusBadge = 'badge-soumise'; break;
            case 'en_cours': statusBadge = 'badge-en_cours'; break;
            case 'traitee': statusBadge = 'badge-traitee'; break;
            case 'rejetee': statusBadge = 'badge-rejetee'; break;
            default: statusBadge = 'badge-soumise';
        }
        
        const dateSoumission = new Date(reclamation.date_soumission).toLocaleDateString('fr-FR');
        
        modalBody.innerHTML = `
            <div class="detail-row"><div class="detail-label">Référence</div><div class="detail-value">${reclamation.reference}</div></div>
            <div class="detail-row"><div class="detail-label">Citoyen</div><div class="detail-value">${reclamation.citoyen}</div></div>
            <div class="detail-row"><div class="detail-label">Service</div><div class="detail-value">${reclamation.nom_service || 'Non spécifié'}</div></div>
            <div class="detail-row"><div class="detail-label">Catégorie</div><div class="detail-value">${reclamation.categorie}</div></div>
            <div class="detail-row"><div class="detail-label">Objet</div><div class="detail-value">${reclamation.objet}</div></div>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${reclamation.description}</div></div>
            <div class="detail-row"><div class="detail-label">Lieu</div><div class="detail-value">${reclamation.lieu || 'Non spécifié'}</div></div>
            <div class="detail-row"><div class="detail-label">Priorité</div><div class="detail-value">${reclamation.priorite}</div></div>
            <div class="detail-row"><div class="detail-label">Statut</div><div class="detail-value"><span class="badge ${statusBadge}">${reclamation.statut}</span></div></div>
            <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value">${dateSoumission}</div></div>
        `;
        modal.style.display = 'flex';
    };

    window.closeModal = function() {
        const modal = document.getElementById('detailsModal');
        if (modal) modal.style.display = 'none';
    };
});
