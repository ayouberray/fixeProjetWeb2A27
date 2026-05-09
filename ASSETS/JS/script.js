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

    // ========== 3. SLIDESHOW AUTOMATIQUE (pour frontoffice) ==========
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
        
        // Changer toutes les 3 secondes (3000ms)
        setInterval(nextSlide, 3000);
    }

    // ========== 4. TOAST NOTIFICATION ==========
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

    // ========== 5. VALIDATION DU FORMULAIRE DE RÉCLAMATION (FRONTOFFICE) ==========
    const reclamationForm = document.getElementById('reclamationForm');
    if (reclamationForm) {
        const categorie = document.getElementById('categorie');
        const objet = document.getElementById('objet');
        const description = document.getElementById('description');
        const submitBtn = document.getElementById('submitBtn');

        // Fonction pour afficher/masquer les erreurs
        function showFieldError(field, errorId, isValid) {
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

        // Validation en temps réel
        if (categorie) {
            categorie.addEventListener('change', function() {
                const isValid = this.value !== '';
                showFieldError(this, 'categorieError', isValid);
            });
        }

        if (objet) {
            objet.addEventListener('input', function() {
                const isValid = this.value.trim().length >= 5;
                showFieldError(this, 'objetError', isValid);
            });
        }

        if (description) {
            description.addEventListener('input', function() {
                const isValid = this.value.trim().length >= 20;
                showFieldError(this, 'descriptionError', isValid);
            });
        }

        // Validation avant soumission
        reclamationForm.addEventListener('submit', function(e) {
            let isValid = true;

            if (categorie && !categorie.value) {
                showFieldError(categorie, 'categorieError', false);
                isValid = false;
            }

            if (objet && objet.value.trim().length < 5) {
                showFieldError(objet, 'objetError', false);
                isValid = false;
            }

            if (description && description.value.trim().length < 20) {
                showFieldError(description, 'descriptionError', false);
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                window.showNotification('Veuillez corriger les erreurs dans le formulaire', 'error');
                return false;
            }

            // Désactiver le bouton pour éviter double soumission
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
            }
        });
    }

    // ========== 6. VALIDATION DU FORMULAIRE DE RÉPONSE (BACKOFFICE) ==========
    const reponseForm = document.getElementById('reponseForm');
    if (reponseForm) {
        const nomAgent = document.getElementById('nom_agent');
        const contenu = document.getElementById('contenu');

        function showError(field, errorId, show) {
            const errorDiv = document.getElementById(errorId);
            if (show) {
                field.classList.add('error');
                if (errorDiv) errorDiv.classList.add('show');
            } else {
                field.classList.remove('error');
                if (errorDiv) errorDiv.classList.remove('show');
            }
        }

        if (nomAgent) {
            nomAgent.addEventListener('input', function() {
                showError(this, 'nomAgentError', this.value.trim().length < 2);
            });
        }

        if (contenu) {
            contenu.addEventListener('input', function() {
                showError(this, 'contenuError', this.value.trim().length < 10);
            });
        }

        reponseForm.addEventListener('submit', function(e) {
            let isValid = true;
            if (nomAgent && nomAgent.value.trim().length < 2) {
                showError(nomAgent, 'nomAgentError', true);
                isValid = false;
            }
            if (contenu && contenu.value.trim().length < 10) {
                showError(contenu, 'contenuError', true);
                isValid = false;
            }
            if (!isValid) {
                e.preventDefault();
                if (window.showNotification) window.showNotification('Veuillez corriger les erreurs', 'error');
            }
        });
    }

    // ========== 7. CONFIRMATION DE SUPPRESSION ==========
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

    // ========== 8. FONCTIONS POUR MODALES (BACKOFFICE) ==========
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
            <div class="detail-row"><div class="detail-label">Référence</div><div class="detail-value">${escapeHtml(reclamation.reference)}</div></div>
            <div class="detail-row"><div class="detail-label">Citoyen</div><div class="detail-value">${escapeHtml(reclamation.citoyen)}</div></div>
            <div class="detail-row"><div class="detail-label">Service</div><div class="detail-value">${escapeHtml(reclamation.nom_service || 'Non spécifié')}</div></div>
            <div class="detail-row"><div class="detail-label">Catégorie</div><div class="detail-value">${escapeHtml(reclamation.categorie)}</div></div>
            <div class="detail-row"><div class="detail-label">Objet</div><div class="detail-value">${escapeHtml(reclamation.objet)}</div></div>
            <div class="detail-row"><div class="detail-label">Description</div><div class="detail-value">${escapeHtml(reclamation.description)}</div></div>
            <div class="detail-row"><div class="detail-label">Lieu</div><div class="detail-value">${escapeHtml(reclamation.lieu || 'Non spécifié')}</div></div>
            <div class="detail-row"><div class="detail-label">Priorité</div><div class="detail-value">${escapeHtml(reclamation.priorite)}</div></div>
            <div class="detail-row"><div class="detail-label">Statut</div><div class="detail-value"><span class="badge ${statusBadge}">${escapeHtml(reclamation.statut)}</span></div></div>
            <div class="detail-row"><div class="detail-label">Date</div><div class="detail-value">${dateSoumission}</div></div>
        `;
        modal.style.display = 'flex';
    };

    window.closeModal = function() {
        const modal = document.getElementById('detailsModal');
        if (modal) modal.style.display = 'none';
    };

    // Fonction utilitaire pour échapper le HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});

// Styles pour animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes slideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    .form-control.error { border-color: #EF4444 !important; background-color: #FEE2E2 !important; }
    .error-message { display: none; color: #DC2626; font-size: 12px; margin-top: 5px; }
    .error-message.show { display: block; }
`;
document.head.appendChild(style);