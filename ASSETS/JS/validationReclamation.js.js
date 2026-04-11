// Validation du formulaire de réclamation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('reclamationForm');
    if (!form) return;

    form.addEventListener('submit', function(e) {
        let errors = [];
        
        // Récupérer les champs
        const objet = document.getElementById('objet')?.value.trim();
        const description = document.getElementById('description')?.value.trim();
        const categorie = document.getElementById('categorie')?.value;
        const priorite = document.getElementById('priorite')?.value;
        
        // Validation objet
        if (!objet || objet.length < 5) {
            errors.push("L'objet doit contenir au moins 5 caractères");
            highlightError('objet');
        } else {
            removeError('objet');
        }
        
        // Validation description
        if (!description || description.length < 20) {
            errors.push("La description doit contenir au moins 20 caractères");
            highlightError('description');
        } else {
            removeError('description');
        }
        
        // Validation catégorie
        if (!categorie) {
            errors.push("Veuillez sélectionner une catégorie");
            highlightError('categorie');
        } else {
            removeError('categorie');
        }
        
        // Validation priorité
        if (!priorite) {
            errors.push("Veuillez sélectionner une priorité");
            highlightError('priorite');
        } else {
            removeError('priorite');
        }
        
        // Validation fichier joint
        const fileInput = document.querySelector('input[type="file"]');
        if (fileInput && fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) {
                errors.push("Le fichier ne doit pas dépasser 5MB");
                highlightError('piece_jointe');
            }
            
            if (!allowedTypes.includes(file.type)) {
                errors.push("Format de fichier non autorisé (JPG, PNG, PDF, DOC)");
                highlightError('piece_jointe');
            }
        }
        
        // Afficher les erreurs
        if (errors.length > 0) {
            e.preventDefault();
            showErrors(errors);
            return false;
        }
        
        // Afficher un loader
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner loading"></i> Envoi en cours...';
        }
    });
    
    // Nettoyer les erreurs lors de la saisie
    const fields = ['objet', 'description', 'categorie', 'priorite'];
    fields.forEach(field => {
        const input = document.getElementById(field);
        if (input) {
            input.addEventListener('input', function() {
                removeError(field);
            });
            input.addEventListener('change', function() {
                removeError(field);
            });
        }
    });
});

function highlightError(fieldId) {
    const element = document.getElementById(fieldId);
    if (element) {
        element.style.borderColor = '#ef4444';
        element.style.backgroundColor = 'rgba(239, 68, 68, 0.05)';
    }
}

function removeError(fieldId) {
    const element = document.getElementById(fieldId);
    if (element) {
        element.style.borderColor = '';
        element.style.backgroundColor = '';
    }
}

function showErrors(errors) {
    // Supprimer les anciennes alertes
    const oldAlert = document.querySelector('.alert-error');
    if (oldAlert) oldAlert.remove();
    
    // Créer la nouvelle alerte
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error';
    alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i><div>' + errors.join('<br>') + '</div>';
    
    // Insérer en haut du formulaire
    const form = document.getElementById('reclamationForm');
    if (form) {
        form.insertBefore(alertDiv, form.firstChild);
        
        // Scroll vers l'erreur
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-fermeture après 5 secondes
        setTimeout(() => {
            if (alertDiv) alertDiv.remove();
        }, 5000);
    }
}