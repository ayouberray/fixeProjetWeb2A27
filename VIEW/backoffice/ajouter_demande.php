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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nouvelle Demande • Backoffice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --danger: #ef4444;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
        }
        
        .container { max-width: 700px; width: 100%; }
        
        .form-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.1);
            overflow: hidden;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .form-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
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
        }
        
        .form-header .back-link:hover { color: white; }
        
        .form-title { font-size: 1.75rem; font-weight: 700; }
        .form-subtitle { opacity: 0.9; font-size: 0.95rem; margin-top: 0.5rem; }
        
        .form-body { padding: 2rem; }
        
        .form-group { margin-bottom: 1.5rem; }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        .form-label .required { color: var(--danger); }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
        }
        
        .form-input.error, .form-select.error, .form-textarea.error {
            border-color: var(--danger);
        }
        
        .form-textarea { resize: vertical; min-height: 120px; }
        
        .error-message {
            color: var(--danger);
            font-size: 0.8rem;
            margin-top: 0.375rem;
        }
        
        .char-counter {
            text-align: right;
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
        }
        
        .general-error {
            background: #fee2e2;
            color: #991b1b;
            padding: 1rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            padding-top: 1rem;
        }
        
        .btn {
            flex: 1;
            padding: 0.875rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }
        
        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1.5px solid var(--gray-200);
        }
        
        .btn-secondary:hover {
            background: var(--gray-50);
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
                <a href="../frontoffice/index.php" class="back-link">← Retour au tableau de bord</a>
                <h1 class="form-title">Nouvelle Demande</h1>
                <p class="form-subtitle">Remplissez le formulaire pour soumettre votre demande</p>
            </div>
            
            <div class="form-body">
                <?php if (!empty($errors['general'])): ?>
                    <div class="general-error">❌ <?= htmlspecialchars($errors['general']) ?></div>
                <?php endif; ?>
                
                <form method="POST" id="demandeForm">
                    <div class="form-group">
                        <label class="form-label" for="titre">Titre <span class="required">*</span></label>
                        <input type="text" id="titre" name="titre" 
                               class="form-input <?= isset($errors['titre']) ? 'error' : '' ?>"
                               value="<?= htmlspecialchars($form_data['titre']) ?>"
                               placeholder="Ex: Réparation trottoir rue des Lilas" maxlength="255">
                        <div class="char-counter"><span id="titreCounter">0</span>/255</div>
                        <?php if (isset($errors['titre'])): ?>
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['titre']) ?></div>
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
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['id_service']) ?></div>
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
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['type_demande']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="description">Description <span class="required">*</span></label>
                        <textarea id="description" name="description" 
                                  class="form-textarea <?= isset($errors['description']) ? 'error' : '' ?>"
                                  placeholder="Décrivez votre demande en détail..."><?= htmlspecialchars($form_data['description']) ?></textarea>
                        <div class="char-counter"><span id="descriptionCounter">0</span> caractères (min 20)</div>
                        <?php if (isset($errors['description'])): ?>
                            <div class="error-message">⚠️ <?= htmlspecialchars($errors['description']) ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-actions">
                        <a href="../frontoffice/index.php" class="btn btn-secondary">← Annuler</a>
                        <button type="submit" class="btn btn-primary">➕ Créer la demande</button>
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