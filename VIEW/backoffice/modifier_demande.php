<?php
// ==========================================
// FORCER LA SESSION ADMIN AVANT TOUT
// ==========================================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// S'assurer que l'admin est connecté avec les bons droits
$_SESSION['user_id'] = 1;
$_SESSION['user_nom'] = 'Administrateur';
$_SESSION['user_prenom'] = 'Admin';
$_SESSION['user_role'] = 'admin';

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: index.php?error=ID invalide');
    exit();
}

$controller = new DemandeController();
$data = $controller->modifier($id_demande);

$demande = $data['demande'] ?? null;
$services = $data['services'] ?? [];

if (!$demande) {
    header('Location: index.php?error=Demande introuvable');
    exit();
}

$types_demandes = [
    'urbanisme' => '🏗️ Urbanisme',
    'voirie' => '🛣️ Voirie',
    'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture',
    'social' => '🤝 Social',
    'autre' => '📌 Autre'
];

$statuts = [
    'en_attente' => '⏳ En attente',
    'en_cours' => '🔄 En cours', 
    'traite' => '✅ Traité',
    'refuse' => '❌ Refusé'
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Modifier Demande #<?= $id_demande ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --secondary-dark: #1B5E20;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --info: #17A2B8;
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

        h1, h2, h3 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            color: var(--dark);
        }

        .container { max-width: 750px; width: 100%; }

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

        .form-title { 
            font-size: 1.75rem; 
            font-weight: 800; 
            display: flex; 
            align-items: center; 
            gap: 1rem; 
            flex-wrap: wrap;
        }

        .demande-id { 
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 0.35rem 1.2rem; 
            border-radius: 50px; 
            font-size: 0.95rem;
            font-weight: 600;
            border: 1px solid rgba(255,255,255,0.3);
        }

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

        .form-textarea { resize: vertical; min-height: 120px; }

        /* ========== INFO BOX ========== */
        .info-box {
            background: var(--primary-light);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 2px solid rgba(0,109,91,0.15);
        }

        .info-box .info-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.75rem;
        }

        .info-box .info-row:last-child {
            margin-bottom: 0;
        }

        .info-box .info-icon {
            width: 24px;
            color: var(--primary);
            text-align: center;
        }

        .info-box .info-label {
            font-weight: 600;
            color: var(--gray-700);
            min-width: 150px;
        }

        .info-box .info-value {
            color: var(--dark);
        }

        /* ========== STATUT SELECTOR ========== */
        .statut-selector {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border: 2px solid var(--gray-300);
            transition: var(--transition-base);
        }

        .statut-selector:focus-within {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,109,91,0.1);
        }

        .statut-selector .statut-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .statut-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 0.75rem;
        }

        .statut-option {
            position: relative;
        }

        .statut-option input[type="radio"] {
            display: none;
        }

        .statut-option label {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: var(--transition-base);
            font-weight: 600;
            font-size: 0.9rem;
            text-align: center;
            background: var(--white);
        }

        .statut-option label:hover {
            border-color: var(--primary);
            background: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .statut-option input[type="radio"]:checked + label {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary-dark);
            box-shadow: var(--shadow-sm);
        }

        /* Couleurs spécifiques par statut */
        .statut-option.en_attente input:checked + label {
            border-color: #D97706;
            background: #FEF3C7;
            color: #92400E;
        }

        .statut-option.en_cours input:checked + label {
            border-color: #2563EB;
            background: #DBEAFE;
            color: #1E40AF;
        }

        .statut-option.traite input:checked + label {
            border-color: #059669;
            background: #D1FAE5;
            color: #065F46;
        }

        .statut-option.refuse input:checked + label {
            border-color: #DC2626;
            background: #FEE2E2;
            color: #991B1B;
        }

        /* ========== ERROR MESSAGE ========== */
        .error-message {
            background: #FEE2E2;
            color: #DC2626;
            padding: 1rem 1.25rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            border-left: 4px solid #DC2626;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        /* ========== CHAR COUNTER ========== */
        .char-counter {
            text-align: right;
            font-size: 0.75rem;
            color: var(--gray-500);
            margin-top: 0.25rem;
        }

        /* ========== FORM ACTIONS ========== */
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
            transform: translateY(-3px);
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

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #008f5a 100%);
            color: white;
            box-shadow: 0 8px 20px -6px rgba(0,168,107,0.4);
        }

        .btn-success:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 28px -8px rgba(0,168,107,0.5);
        }

        /* ========== TOAST NOTIFICATION ========== */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 1rem 1.5rem;
            border-radius: var(--radius-md);
            color: white;
            font-weight: 500;
            z-index: 9999;
            animation: slideInRight 0.3s ease;
            box-shadow: var(--shadow-lg);
            max-width: 400px;
        }

        .toast-success {
            background: var(--success);
        }

        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 640px) {
            .form-actions { 
                flex-direction: column; 
            }
            .form-title { 
                flex-direction: column; 
                align-items: flex-start; 
            }
            .statut-options {
                grid-template-columns: repeat(2, 1fr);
            }
            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <h1 class="form-title">
                    Modifier la Demande
                    <span class="demande-id">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span>
                </h1>
            </div>
            
            <div class="form-body">
                <?php if (!empty($data['errors']['general'])): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($data['errors']['general']) ?>
                    </div>
                <?php endif; ?>
                
                <div class="info-box">
                    <div class="info-row">
                        <i class="fas fa-calendar-alt info-icon"></i>
                        <span class="info-label">Date de création :</span>
                        <span class="info-value"><?= date('d/m/Y H:i', strtotime($demande['date_creation'])) ?></span>
                    </div>
                    <div class="info-row">
                        <i class="fas fa-clock info-icon"></i>
                        <span class="info-label">Dernière modification :</span>
                        <span class="info-value"><?= $demande['date_modification'] ? date('d/m/Y H:i', strtotime($demande['date_modification'])) : 'Jamais' ?></span>
                    </div>
                </div>
                
                <form method="POST" id="modifierForm">
                    <!-- STATUT -->
                    <div class="statut-selector">
                        <div class="statut-label">
                            <i class="fas fa-tasks"></i> Statut de la demande <span class="required">*</span>
                        </div>
                        <div class="statut-options">
                            <?php foreach ($statuts as $value => $label): ?>
                                <div class="statut-option <?= $value ?>">
                                    <input type="radio" 
                                           id="statut_<?= $value ?>" 
                                           name="statut" 
                                           value="<?= $value ?>"
                                           <?= $demande['statut'] == $value ? 'checked' : '' ?>>
                                    <label for="statut_<?= $value ?>">
                                        <?= $label ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- TITRE -->
                    <div class="form-group">
                        <label class="form-label" for="titre">
                            Titre de la demande <span class="required">*</span>
                        </label>
                        <input type="text" 
                               id="titre" 
                               name="titre" 
                               class="form-input" 
                               value="<?= htmlspecialchars($demande['titre']) ?>" 
                               maxlength="255" 
                               required>
                        <div class="char-counter"><span id="titreCounter">0</span>/255 caractères</div>
                    </div>
                    
                    <!-- SERVICE -->
                    <div class="form-group">
                        <label class="form-label" for="id_service">
                            Service concerné <span class="required">*</span>
                        </label>
                        <select id="id_service" name="id_service" class="form-select" required>
                            <option value="">-- Sélectionnez un service --</option>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id_service'] ?>"
                                    <?= $demande['id_service'] == $service['id_service'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nom_service']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- TYPE -->
                    <div class="form-group">
                        <label class="form-label" for="type_demande">
                            Type de demande <span class="required">*</span>
                        </label>
                        <select id="type_demande" name="type_demande" class="form-select" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <?php foreach ($types_demandes as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= $demande['type_demande'] == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- DESCRIPTION -->
                    <div class="form-group">
                        <label class="form-label" for="description">
                            Description détaillée <span class="required">*</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  class="form-textarea" 
                                  required
                                  placeholder="Décrivez votre demande en détail..."><?= htmlspecialchars($demande['description']) ?></textarea>
                        <div class="char-counter"><span id="descriptionCounter">0</span> caractères (minimum 20)</div>
                    </div>
                    
                    <!-- ACTIONS -->
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer les modifications
                        </button>
                    </div>
                    
                    <!-- Bouton rapide pour marquer comme traité -->
                    <?php if ($demande['statut'] !== 'traite'): ?>
                    <div class="form-actions" style="margin-top: 0.75rem;">
                        <button type="button" id="btnMarkTraite" class="btn btn-success">
                            <i class="fas fa-check-double"></i> Marquer comme traité et enregistrer
                        </button>
                    </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        // Compteurs de caractères
        const titreInput = document.getElementById('titre');
        const descInput = document.getElementById('description');
        
        document.getElementById('titreCounter').textContent = titreInput.value.length;
        titreInput.addEventListener('input', function() {
            document.getElementById('titreCounter').textContent = this.value.length;
        });
        
        document.getElementById('descriptionCounter').textContent = descInput.value.length;
        descInput.addEventListener('input', function() {
            document.getElementById('descriptionCounter').textContent = this.value.length;
        });
        
        // Bouton rapide "Marquer comme traité"
        const btnMarkTraite = document.getElementById('btnMarkTraite');
        if (btnMarkTraite) {
            btnMarkTraite.addEventListener('click', function() {
                // Sélectionne le statut "traité"
                document.getElementById('statut_traite').checked = true;
                
                // Feedback visuel
                btnMarkTraite.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement en cours...';
                btnMarkTraite.disabled = true;
                
                // Soumet le formulaire automatiquement
                document.getElementById('modifierForm').submit();
            });
        }
        
        // Confirmation avant soumission manuelle
        document.getElementById('modifierForm').addEventListener('submit', function(e) {
            const nouveauStatut = document.querySelector('input[name="statut"]:checked').value;
            const ancienStatut = '<?= $demande['statut'] ?>';
            
            // Si le statut a changé, on confirme
            if (nouveauStatut !== ancienStatut) {
                const confirme = confirm(
                    `⚠️ Vous allez changer le statut de :\n` +
                    `"${getStatutLabel(ancienStatut)}" → "${getStatutLabel(nouveauStatut)}"\n\n` +
                    `Après enregistrement, vous serez redirigé vers le tableau de bord.\n\n` +
                    `Voulez-vous continuer ?`
                );
                
                if (!confirme) {
                    e.preventDefault();
                    return false;
                }
            }
            
            // Affiche un message de chargement
            const submitBtn = document.querySelector('.btn-primary');
            if (submitBtn) {
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
                submitBtn.disabled = true;
            }
        });
        
        function getStatutLabel(statut) {
            const labels = {
                'en_attente': '⏳ En attente',
                'en_cours': '🔄 En cours',
                'traite': '✅ Traité',
                'refuse': '❌ Refusé'
            };
            return labels[statut] || statut;
        }
        
        // Raccourci Échap pour annuler et retourner au backoffice
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') {
                if (confirm('Voulez-vous annuler les modifications et retourner au tableau de bord ?')) {
                    window.location.href = 'index.php';
                }
            }
        });
    </script>
</body>
</html>