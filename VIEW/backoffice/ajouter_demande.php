<?php
// VIEW/backoffice/ajouter_demande.php
// Formulaire d'ajout d'une demande (Backoffice)

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

// Vérifier si la demande a été créée avec succès (redirection depuis le controller)
if (isset($_GET['success']) && $_GET['success'] === 'created') {
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InnoGov • Demande créée</title>
        <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <style>
            :root {
                --primary: #006D5B;
                --primary-dark: #004D3D;
                --bg-main: #F5FCF9;
                --bg-card: rgba(255, 255, 255, 0.95);
                --text-title: #1A2E2A;
                --text-secondary: #5C8B7E;
                --border-subtle: rgba(0, 109, 91, 0.12);
                --shadow-card: 0 24px 48px rgba(0, 77, 61, 0.12);
                --radius-card: 20px;
                --radius-btn: 10px;
                --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                --success: #10b981;
            }

            * { margin: 0; padding: 0; box-sizing: border-box; }

            body {
                font-family: 'DM Sans', sans-serif;
                background: var(--bg-main);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }

            h1, h2, h3 {
                font-family: 'Syne', sans-serif;
                font-weight: 700;
                color: var(--text-title);
            }

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

            .success-header {
                padding: 2rem 2rem 1rem;
            }

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

            .success-title { 
                font-size: 1.75rem; 
                font-weight: 800; 
                color: var(--text-title); 
                margin-bottom: 0.5rem; 
            }

            .success-message { 
                color: var(--text-secondary); 
                font-size: 0.9rem; 
                margin-bottom: 1rem;
            }

            .action-buttons { 
                display: flex; 
                gap: 1rem; 
                padding: 0 2rem 2rem;
            }

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
            }

            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 24px rgba(0, 109, 91, 0.35);
            }

            .countdown {
                margin-top: 1rem;
                margin-bottom: 1.5rem;
                font-size: 0.8rem;
                color: var(--text-secondary);
            }

            @media (max-width: 640px) {
                .action-buttons { flex-direction: column; }
            }
        </style>
    </head>
    <body>
        <div class="success-container">
            <div class="success-card">
                <div class="success-header">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h1 class="success-title">Demande créée !</h1>
                    <p class="success-message">Votre demande a été soumise avec succès.</p>
                </div>
                
                <div class="action-buttons">
                    <a href="../frontoffice/index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
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
                    window.location.href = '../frontoffice/index.php?success=Demande créée avec succès';
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
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ============================================
           INNOGOV - DESIGN FINAL (VERT #006D5B)
           ============================================ */

        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --bg-main: #F5FCF9;
            --bg-secondary: #EBF7F3;
            --bg-card: rgba(255, 255, 255, 0.95);
            --text-title: #1A2E2A;
            --text-body: #2C5A4F;
            --text-secondary: #5C8B7E;
            --border-subtle: rgba(0, 109, 91, 0.12);
            --border-normal: rgba(0, 109, 91, 0.2);
            --shadow-card: 0 24px 48px rgba(0, 77, 61, 0.12);
            --shadow-btn: 0 4px 16px rgba(0, 109, 91, 0.25);
            --shadow-btn-hover: 0 8px 24px rgba(0, 109, 91, 0.35);
            --radius-card: 20px;
            --radius-btn: 10px;
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }

        h1, h2, h3 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            color: var(--text-title);
        }

        .container { max-width: 700px; width: 100%; }

        .form-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-card);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
            overflow: hidden;
            animation: slideIn 0.4s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-header {
            padding: 2rem 2rem 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
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
        }

        .form-header .back-link:hover { 
            color: white; 
            transform: translateX(-3px);
        }

        .form-title { 
            font-size: 1.75rem; 
            font-weight: 800; 
        }

        .form-subtitle { 
            opacity: 0.9; 
            font-size: 0.9rem; 
            margin-top: 0.5rem; 
            font-family: 'DM Sans', sans-serif;
        }

        .form-body { padding: 2rem; }

        .form-group { margin-bottom: 1.5rem; }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--text-title);
            margin-bottom: 0.5rem;
        }

        .form-label .required { color: #ef4444; }

        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border-normal);
            border-radius: var(--radius-btn);
            font-size: 0.95rem;
            font-family: 'DM Sans', sans-serif;
            transition: var(--transition-base);
            background: white;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.15);
        }

        .form-input.error, .form-select.error, .form-textarea.error {
            border-color: #ef4444;
        }

        .form-textarea { resize: vertical; min-height: 120px; }

        .error-message {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.375rem;
        }

        .char-counter {
            text-align: right;
            font-size: 0.7rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
        }

        .general-error {
            background: #fee2e2;
            color: #dc2626;
            padding: 1rem;
            border-radius: var(--radius-btn);
            margin-bottom: 1.5rem;
            border-left: 4px solid #dc2626;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            padding-top: 1rem;
        }

        .btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: var(--radius-btn);
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
            font-size: 0.9rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: var(--shadow-btn);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-btn-hover);
        }

        .btn-secondary {
            background: white;
            color: var(--text-secondary);
            border: 1.5px solid var(--border-normal);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        @media (max-width: 640px) {
            .form-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <a href="../frontoffice/index.php" class="back-link">
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
                        <label class="form-label" for="titre">Titre <span class="required">*</span></label>
                        <input type="text" id="titre" name="titre" 
                               class="form-input <?= isset($errors['titre']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($form_data['titre']) ?>"
                               placeholder="Ex: Réparation trottoir rue des Lilas" maxlength="255">
                        <div class="char-counter"><i class="fas fa-pencil-alt"></i> <span id="titreCounter">0</span>/255</div>
                        <?php if (isset($errors['titre'])): ?>
                            <div class="error-message"><i class="fas fa-warning"></i> <?= htmlspecialchars($errors['titre']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="id_service">Service <span class="required">*</span></label>
                        <select id="id_service" name="id_service" 
                                class="form-select <?= isset($errors['id_service']) ? 'error' : '' ?>">
                            <option value="">-- Sélectionnez un service --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id_service'] ?>"
                                    <?= ($form_data['id_service'] ?? '') == $service['id_service'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nom_service']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['id_service'])): ?>
                            <div class="error-message"><i class="fas fa-warning"></i> <?= htmlspecialchars($errors['id_service']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="type_demande">Type <span class="required">*</span></label>
                        <select id="type_demande" name="type_demande" 
                                class="form-select <?= isset($errors['type_demande']) ? 'error' : '' ?>">
                            <option value="">-- Sélectionnez un type --</option>
                            <?php foreach ($types_demandes as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= ($form_data['type_demande'] ?? '') == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['type_demande'])): ?>
                            <div class="error-message"><i class="fas fa-warning"></i> <?= htmlspecialchars($errors['type_demande']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="description">Description <span class="required">*</span></label>
                        <textarea id="description" name="description" 
                                  class="form-textarea <?= isset($errors['description']) ? 'error' : '' ?>"
                                  placeholder="Décrivez votre demande en détail..."><?= htmlspecialchars($form_data['description']) ?></textarea>
                        <div class="char-counter"><i class="fas fa-align-left"></i> <span id="descriptionCounter">0</span> caractères (min 20)</div>
                        <?php if (isset($errors['description'])): ?>
                            <div class="error-message"><i class="fas fa-warning"></i> <?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <a href="../frontoffice/index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus-circle"></i> Créer la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const titreInput = document.getElementById('titre');
        const titreCounter = document.getElementById('titreCounter');
        titreInput.addEventListener('input', function() { titreCounter.textContent = this.value.length; });
        titreCounter.textContent = titreInput.value.length;
        
        const descriptionInput = document.getElementById('description');
        const descriptionCounter = document.getElementById('descriptionCounter');
        descriptionInput.addEventListener('input', function() { descriptionCounter.textContent = this.value.length; });
        descriptionCounter.textContent = descriptionInput.value.length;
        
        document.getElementById('demandeForm').addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.form-input, .form-select, .form-textarea').forEach(el => el.classList.remove('error'));
            
            if (titreInput.value.trim().length < 5) { titreInput.classList.add('error'); isValid = false; }
            if (descriptionInput.value.trim().length < 20) { descriptionInput.classList.add('error'); isValid = false; }
            if (!document.getElementById('id_service').value) { document.getElementById('id_service').classList.add('error'); isValid = false; }
            if (!document.getElementById('type_demande').value) { document.getElementById('type_demande').classList.add('error'); isValid = false; }
            
            if (!isValid) { e.preventDefault(); alert('Veuillez corriger les erreurs.'); }
        });
        
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') window.location.href = '../frontoffice/index.php'; 
        });
    </script>
</body>
</html>
