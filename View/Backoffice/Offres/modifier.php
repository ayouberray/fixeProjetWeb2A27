<?php
// Ce fichier reçoit $offre depuis le contrôleur
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une offre - Admin</title>
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
    <style>
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            z-index: 1000;
            animation: slideInRight 0.3s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            font-weight: 500;
        }
        .toast-error { background: #dc3545; }
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        .error-message { color: #dc3545; font-size: 0.75rem; margin-top: 0.25rem; display: block; }
        .is-invalid { border-color: #dc3545 !important; }
    </style>
</head>
<body>
    <header class="main-header" style="background: var(--white); padding: 1rem 0; box-shadow: var(--shadow-sm); margin-bottom: 2rem;">
        <div class="container header-inner" style="display: flex; justify-content: space-between; align-items: center;">
            <a href="index.php?controller=offre&action=admin-lister" class="logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none; font-weight: bold; font-size: 1.5rem; color: var(--primary);">
                <i class="fas fa-briefcase"></i> INNOC@V
            </a>
            <div class="lang-toggle">
                <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
            </div>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <div class="card grid-1">
            <h1>Modifier l'offre</h1>
            <form id="offreForm" novalidate>
                <input type="hidden" name="id" value="<?= htmlspecialchars($offre['id_offre']) ?>">
                <div class="form-group">
                    <label class="form-label">Titre</label>
                    <input type="text" name="titre" value="<?= htmlspecialchars($offre['titre']) ?>" class="form-control">
                    <div class="error-message" id="error-titre"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control"><?= htmlspecialchars($offre['description']) ?></textarea>
                    <div class="error-message" id="error-description"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Entité</label>
                    <input type="text" name="entite" value="<?= htmlspecialchars($offre['entite']) ?>" class="form-control">
                    <div class="error-message" id="error-entite"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Date limite</label>
                    <input type="date" name="date_limite" value="<?= $offre['date_limite'] ?>" class="form-control">
                    <div class="error-message" id="error-date"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Nombre de postes</label>
                    <input type="number" name="nombre_postes" value="<?= $offre['nombre_postes'] ?>" class="form-control">
                    <div class="error-message" id="error-postes"></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Statut</label>
                    <select name="statut" class="form-control">
                        <option value="Ouvert" <?= $offre['statut'] == 'Ouvert' ? 'selected' : '' ?>>Ouvert</option>
                        <option value="Fermer" <?= $offre['statut'] == 'Fermer' ? 'selected' : '' ?>>Fermé</option>
                    </select>
                </div>
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Mettre à jour</button>
                    <a href="index.php?controller=offre&action=admin-lister" class="btn btn-outline">Retour</a>
                </div>
            </form>
        </div>
    </main>

    <script>
        function showToast(message, type = 'success') {
            const oldToast = document.querySelector('.toast');
            if (oldToast) oldToast.remove();
            const toast = document.createElement('div');
            toast.className = `toast ${type === 'error' ? 'toast-error' : ''}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i><span>${message}</span>`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function setError(id, msg) { document.getElementById(id).innerHTML = msg; }
        function clearError(id) { document.getElementById(id).innerHTML = ''; }

        const form = document.getElementById('offreForm');
        const titre = document.querySelector('[name="titre"]');
        const description = document.querySelector('[name="description"]');
        const entite = document.querySelector('[name="entite"]');
        const dateLimite = document.querySelector('[name="date_limite"]');
        const nbPostes = document.querySelector('[name="nombre_postes"]');

        function validateForm() {
            let valid = true;
            if (!titre.value.trim()) {
                setError('error-titre', 'Le titre est requis');
                titre.classList.add('is-invalid');
                valid = false;
            } else {
                clearError('error-titre');
                titre.classList.remove('is-invalid');
            }
            if (!description.value.trim()) {
                setError('error-description', 'La description est requise');
                description.classList.add('is-invalid');
                valid = false;
            } else {
                clearError('error-description');
                description.classList.remove('is-invalid');
            }
            if (!entite.value.trim()) {
                setError('error-entite', 'L\'entité est requise');
                entite.classList.add('is-invalid');
                valid = false;
            } else {
                clearError('error-entite');
                entite.classList.remove('is-invalid');
            }
            if (!dateLimite.value) {
                setError('error-date', 'Date limite requise');
                dateLimite.classList.add('is-invalid');
                valid = false;
            } else if (new Date(dateLimite.value) < new Date().setHours(0,0,0,0)) {
                setError('error-date', 'La date doit être aujourd\'hui ou dans le futur');
                dateLimite.classList.add('is-invalid');
                valid = false;
            } else {
                clearError('error-date');
                dateLimite.classList.remove('is-invalid');
            }
            const postes = parseInt(nbPostes.value);
            if (isNaN(postes) || postes < 1) {
                setError('error-postes', 'Minimum 1 poste');
                nbPostes.classList.add('is-invalid');
                valid = false;
            } else {
                clearError('error-postes');
                nbPostes.classList.remove('is-invalid');
            }
            return valid;
        }

        form.onsubmit = async (e) => {
            e.preventDefault();
            if (!validateForm()) {
                showToast('Veuillez corriger les erreurs', 'error');
                return;
            }
            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mise à jour...';
            const formData = new FormData(form);
            const id = formData.get('id');
            try {
                const res = await fetch(`index.php?controller=offre&action=modifier&id=${id}`, {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    showToast('Offre modifiée avec succès', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php?controller=offre&action=admin-lister';
                    }, 1500);
                } else {
                    showToast(data.message || 'Erreur lors de la modification', 'error');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            } catch (err) {
                showToast('Erreur réseau', 'error');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        };
    </script>
</body>
</html>