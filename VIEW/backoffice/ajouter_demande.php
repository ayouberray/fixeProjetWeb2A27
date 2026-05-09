<?php

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$controller = new DemandeController();
$data = $controller->ajouter();

$services = $data['services'] ?? [];
$errors = $data['errors'] ?? [];
$form_data = $data['form_data'] ?? ['titre' => '', 'description' => '', 'id_service' => '', 'type_demande' => ''];

$types_demandes = [
    'urbanisme' => '🏗️ Urbanisme',
    'voirie' => '🛣️ Voirie',
    'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture',
    'social' => '🤝 Social',
    'autre' => '📌 Autre'
];

if (isset($_GET['success']) && $_GET['success'] === 'created') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InnoGov • Demande créée</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            :root {
                --primary: #006D5B;
                --primary-dark: #004D3D;
                --primary-light: #E6F4F0;
                --bg-main: #F5FCF9;
                --bg-card: rgba(255, 255, 255, 0.95);
                --text-title: #1A2C3E;
                --text-body: #2C5A4F;
                --text-secondary: #8A99B0;
                --border-subtle: rgba(0, 109, 91, 0.12);
                --shadow-card: 0 24px 48px rgba(0, 77, 61, 0.12);
                --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
                --radius-card: 1rem;
                --radius-btn: 0.75rem;
                --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                --success: #00A86B;
            }
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: var(--bg-main);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }
            h1, h2, h3 { font-family: 'Inter', sans-serif; font-weight: 700; color: var(--text-title); }
            .success-container { max-width: 500px; width: 100%; }
            .success-card {
                background: var(--bg-card);
                backdrop-filter: blur(10px);
                border-radius: var(--radius-card);
                border: 1px solid var(--border-subtle);
                box-shadow: var(--shadow-card);
                overflow: hidden;
                text-align: center;
                animation: slideIn 0.4s ease;
            }
            @keyframes slideIn {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }
            .success-header { padding: 2rem 2rem 1rem; }
            .success-icon {
                width: 80px;
                height: 80px;
                background: #D1FAE5;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: 0 auto 1.5rem;
                font-size: 2.5rem;
                color: var(--success);
            }
            .success-title { font-size: 1.75rem; font-weight: 800; margin-bottom: 0.5rem; }
            .success-message { color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem; }
            .action-buttons { display: flex; gap: 1rem; padding: 0 2rem 2rem; }
            .btn {
                flex: 1;
                padding: 0.875rem 1.5rem;
                border-radius: var(--radius-btn);
                font-weight: 600;
                text-decoration: none;
                text-align: center;
                transition: var(--transition-base);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                font-size: 0.9rem;
            }
            .btn-primary {
                background: linear-gradient(135deg, var(--primary), var(--primary-dark));
                color: white;
                box-shadow: var(--shadow-primary);
            }
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 12px 28px -8px rgba(0,109,91,0.5);
            }
            .countdown { margin-top: 1rem; margin-bottom: 1.5rem; font-size: 0.8rem; color: var(--text-secondary); }
            @media (max-width: 640px) { .action-buttons { flex-direction: column; } }
        </style>
    </head>
    <body>
        <div class="success-container">
            <div class="success-card">
                <div class="success-header">
                    <div class="success-icon"><i class="fas fa-check-circle"></i></div>
                    <h1 class="success-title">Demande créée !</h1>
                    <p class="success-message">Votre demande a été soumise avec succès.</p>
                </div>
                <div class="action-buttons">
                    <a href="../frontoffice/client.php" class="btn btn-primary"><i class="fas fa-home"></i> Retour à l'accueil</a>
                </div>
                <div class="countdown">
                    <i class="fas fa-hourglass-half"></i> Redirection automatique dans <span id="countdown">3</span> secondes...
                </div>
            </div>
        </div>
        <script>
            let seconds = 3;
            const countdownElement = document.getElementById('countdown');
            const interval = setInterval(function() {
                seconds--;
                countdownElement.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = '../frontoffice/client.php';
                }
            }, 1000);
        </script>
    </body>
    </html>
    <?php
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Nouvelle Demande</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --dark: #1A2C3E;
            --gray-900: #2D3A4B;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px -2px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --shadow-xl: 0 20px 40px -12px rgba(0,0,0,0.2);
            --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        h1, h2, h3 { font-family: 'Inter', sans-serif; font-weight: 700; color: var(--dark); }

        .container { max-width: 700px; width: 100%; }

        .form-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            padding: 2rem 2rem 1.5rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .form-header .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            transition: var(--transition-base);
            font-weight: 500;
        }

        .form-header .back-link:hover { 
            color: white; 
            transform: translateX(-3px);
        }

        .form-title { font-size: 1.75rem; font-weight: 800; }
        .form-subtitle { opacity: 0.9; font-size: 0.9rem; margin-top: 0.5rem; }
        .form-body { padding: 2rem; }

        .form-group { margin-bottom: 1.5rem; }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-label .required { color: var(--danger); }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition-base);
            background: white;
            color: var(--dark);
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,109,91,0.15);
        }

        .form-input.error, .form-select.error, .form-textarea.error {
            border-color: var(--danger);
            background-color: #FEF2F2;
        }

        .form-textarea { resize: vertical; min-height: 130px; }

        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.375rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .char-counter {
            text-align: right;
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        .general-error {
            background: #FEE2E2;
            color: #DC2626;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            border-left: 4px solid #DC2626;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            padding-top: 1rem;
        }

        .btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.95rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: var(--shadow-primary);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -8px rgba(0,109,91,0.5);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .toast-error {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--danger);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            z-index: 9999;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.9rem;
            font-weight: 500;
            animation: slideInRight 0.3s ease;
            max-width: 400px;
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 640px) {
            .form-actions { flex-direction: column; }
            .toast-error { top: auto; bottom: 20px; right: 20px; left: 20px; max-width: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <a href="../frontoffice/client.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <h1 class="form-title">Nouvelle Demande</h1>
                <p class="form-subtitle">Remplissez le formulaire pour soumettre votre demande</p>
            </div>
            
            <div class="form-body">
                <?php if (!empty($errors['general'])): ?>
                    <div class="general-error">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($errors['general']) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="demandeForm">
                    <div class="form-group">
                        <label class="form-label" for="titre">Titre de la demande <span class="required">*</span></label>
                        <input type="text" id="titre" name="titre" 
                               class="form-input <?= isset($errors['titre']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($form_data['titre']) ?>"
                               placeholder="Ex: Réparation trottoir rue des Lilas">
                        <div class="char-counter"><span id="titreCounter">0</span>/255 caractères</div>
                        <?php if (!empty($errors['titre'])): ?>
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['titre']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="titreError" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="id_service">Service concerné <span class="required">*</span></label>
                        <select id="id_service" name="id_service" class="form-select <?= isset($errors['id_service']) ? 'error' : '' ?>">
                            <option value="">-- Sélectionnez un service --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id_service'] ?>"
                                    <?= ($form_data['id_service'] ?? '') == $service['id_service'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nom_service']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['id_service'])): ?>
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['id_service']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="serviceError" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="type_demande">Type de demande <span class="required">*</span></label>
                        <select id="type_demande" name="type_demande" class="form-select <?= isset($errors['type_demande']) ? 'error' : '' ?>">
                            <option value="">-- Sélectionnez un type --</option>
                            <?php foreach ($types_demandes as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= ($form_data['type_demande'] ?? '') == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['type_demande'])): ?>
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['type_demande']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="typeError" style="display: none;"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="description">Description détaillée <span class="required">*</span></label>
                        <textarea id="description" name="description" 
                                  class="form-textarea <?= isset($errors['description']) ? 'error' : '' ?>"
                                  placeholder="Décrivez votre demande en détail (minimum 20 caractères)..."><?= htmlspecialchars($form_data['description']) ?></textarea>
                        <div class="char-counter"><span id="descriptionCounter">0</span> caractères (minimum 20)</div>
                        <?php if (!empty($errors['description'])): ?>
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                        <div class="error-message" id="descriptionError" style="display: none;"></div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="../frontoffice/client.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="button" id="submitBtn" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Soumettre la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const titreInput = document.getElementById('titre');
        const serviceSelect = document.getElementById('id_service');
        const typeSelect = document.getElementById('type_demande');
        const descriptionInput = document.getElementById('description');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('demandeForm');
        
        const titreError = document.getElementById('titreError');
        const serviceError = document.getElementById('serviceError');
        const typeError = document.getElementById('typeError');
        const descriptionError = document.getElementById('descriptionError');
        
        const titreCounter = document.getElementById('titreCounter');
        const descriptionCounter = document.getElementById('descriptionCounter');
        
        function validateTitre() {
            const valeur = titreInput.value.trim();
            if (valeur.length === 0) {
                titreError.textContent = '⚠️ Le titre est requis';
                titreError.style.display = 'block';
                titreInput.classList.add('error');
                return false;
            } else if (valeur.length < 5) {
                titreError.textContent = '⚠️ Le titre doit contenir au moins 5 caractères';
                titreError.style.display = 'block';
                titreInput.classList.add('error');
                return false;
            } else {
                titreError.textContent = '';
                titreError.style.display = 'none';
                titreInput.classList.remove('error');
                return true;
            }
        }
        
        function validateService() {
            const valeur = serviceSelect.value;
            if (valeur === '') {
                serviceError.textContent = '⚠️ Veuillez sélectionner un service';
                serviceError.style.display = 'block';
                serviceSelect.classList.add('error');
                return false;
            } else {
                serviceError.textContent = '';
                serviceError.style.display = 'none';
                serviceSelect.classList.remove('error');
                return true;
            }
        }
        
        function validateType() {
            const valeur = typeSelect.value;
            if (valeur === '') {
                typeError.textContent = '⚠️ Veuillez sélectionner un type de demande';
                typeError.style.display = 'block';
                typeSelect.classList.add('error');
                return false;
            } else {
                typeError.textContent = '';
                typeError.style.display = 'none';
                typeSelect.classList.remove('error');
                return true;
            }
        }
        
        function validateDescription() {
            const valeur = descriptionInput.value.trim();
            if (valeur.length === 0) {
                descriptionError.textContent = '⚠️ La description est requise';
                descriptionError.style.display = 'block';
                descriptionInput.classList.add('error');
                return false;
            } else if (valeur.length < 20) {
                descriptionError.textContent = '⚠️ La description doit contenir au moins 20 caractères (actuellement: ' + valeur.length + ')';
                descriptionError.style.display = 'block';
                descriptionInput.classList.add('error');
                return false;
            } else {
                descriptionError.textContent = '';
                descriptionError.style.display = 'none';
                descriptionInput.classList.remove('error');
                return true;
            }
        }
        
        function showToast(message) {
            const oldToast = document.querySelector('.toast-error');
            if (oldToast) oldToast.remove();
            
            const toast = document.createElement('div');
            toast.className = 'toast-error';
            toast.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + message;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        }
        
        function validateAll() {
            const isTitreValid = validateTitre();
            const isServiceValid = validateService();
            const isTypeValid = validateType();
            const isDescriptionValid = validateDescription();
            
            return isTitreValid && isServiceValid && isTypeValid && isDescriptionValid;
        }
        
        titreInput.addEventListener('input', function() {
            validateTitre();
            titreCounter.textContent = this.value.length;
        });
        
        descriptionInput.addEventListener('input', function() {
            validateDescription();
            descriptionCounter.textContent = this.value.length;
        });
        
        serviceSelect.addEventListener('change', validateService);
        typeSelect.addEventListener('change', validateType);
        
        titreCounter.textContent = titreInput.value.length;
        descriptionCounter.textContent = descriptionInput.value.length;
        
        submitBtn.addEventListener('click', function(e) {
            e.preventDefault();
            
            if (validateAll()) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi en cours...';
                submitBtn.disabled = true;
                form.submit();
            } else {
                let errorMessages = [];
                if (titreError.style.display !== 'none') errorMessages.push('Titre');
                if (serviceError.style.display !== 'none') errorMessages.push('Service');
                if (typeError.style.display !== 'none') errorMessages.push('Type');
                if (descriptionError.style.display !== 'none') errorMessages.push('Description');
                
                showToast('Veuillez corriger les champs suivants : ' + errorMessages.join(', '));
            }
        });
        
        form.addEventListener('keypress', function(e) {
            if (e.key === 'Enter' && !e.target.matches('textarea')) {
                e.preventDefault();
                if (validateAll()) {
                    form.submit();
                } else {
                    let errorMessages = [];
                    if (titreError.style.display !== 'none') errorMessages.push('Titre');
                    if (serviceError.style.display !== 'none') errorMessages.push('Service');
                    if (typeError.style.display !== 'none') errorMessages.push('Type');
                    if (descriptionError.style.display !== 'none') errorMessages.push('Description');
                    showToast('Veuillez corriger les champs suivants : ' + errorMessages.join(', '));
                }
            }
        });
        
        form.addEventListener('submit', function(e) {
            if (!validateAll()) {
                e.preventDefault();
                showToast('Formulaire invalide. Veuillez corriger les erreurs.');
            }
        });
        
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') window.location.href = '../frontoffice/client.php'; 
        });
    </script>
</body>
</html>