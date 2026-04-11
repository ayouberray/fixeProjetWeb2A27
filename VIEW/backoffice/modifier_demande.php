<?php
// VIEW/backoffice/modifier_demande.php

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: ../frontoffice/index.php?error=ID invalide');
    exit();
}

$controller = new DemandeController();
$data = $controller->updateDemande($id_demande);

$demande = $data['demande'] ?? null;
$services = $data['services'] ?? [];

if (!$demande) {
    header('Location: ../frontoffice/index.php?error=Demande introuvable');
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Demande #<?= $id_demande ?> • Backoffice</title>
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
        }
        
        .form-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, #f59e0b, #d97706);
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
        
        .form-title { font-size: 1.75rem; font-weight: 700; display: flex; align-items: center; gap: 1rem; }
        .demande-id { background: rgba(255,255,255,0.2); padding: 0.25rem 1rem; border-radius: 100px; font-size: 1rem; }
        
        .form-body { padding: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }
        
        .form-input, .form-select, .form-textarea {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--gray-200);
            border-radius: 12px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
        }
        
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #f59e0b;
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.1);
        }
        
        .form-textarea { resize: vertical; min-height: 120px; }
        
        .current-info {
            background: var(--gray-50);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--gray-200);
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
        }
        
        .btn-primary {
            background: #f59e0b;
            color: white;
        }
        
        .btn-primary:hover {
            background: #d97706;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1.5px solid var(--gray-200);
        }
        
        .char-counter {
            text-align: right;
            font-size: 0.75rem;
            color: var(--gray-600);
            margin-top: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-card">
            <div class="form-header">
                <a href="../frontoffice/index.php" class="back-link">← Retour au tableau de bord</a>
                <h1 class="form-title">
                    Modifier la Demande
                    <span class="demande-id">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span>
                </h1>
            </div>
            
            <div class="form-body">
                <div class="current-info">
                    <strong>Statut actuel :</strong> 
                    <?php
                    $statuts = ['en_attente' => '⏳ En attente', 'en_cours' => '🔄 En cours', 'traite' => '✅ Traité', 'refuse' => '❌ Refusé'];
                    echo $statuts[$demande['statut']] ?? $demande['statut'];
                    ?>
                    <br>
                    <strong>Date de création :</strong> <?= date('d/m/Y H:i', strtotime($demande['date_creation'])) ?>
                </div>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titre" class="form-input" 
                               value="<?= htmlspecialchars($demande['titre']) ?>" maxlength="255">
                        <div class="char-counter"><span id="titreCounter">0</span>/255</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Service</label>
                        <select name="id_service" class="form-select">
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['id_service'] ?>"
                                    <?= $demande['id_service'] == $service['id_service'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nom_service']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Type</label>
                        <select name="type_demande" class="form-select">
                            <?php foreach ($types_demandes as $value => $label): ?>
                                <option value="<?= $value ?>"
                                    <?= $demande['type_demande'] == $value ? 'selected' : '' ?>>
                                    <?= $label ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-textarea"><?= htmlspecialchars($demande['description']) ?></textarea>
                        <div class="char-counter"><span id="descriptionCounter">0</span> caractères</div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="../frontoffice/index.php" class="btn btn-secondary">← Annuler</a>
                        <button type="submit" class="btn btn-primary">💾 Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        const titreInput = document.querySelector('input[name="titre"]');
        document.getElementById('titreCounter').textContent = titreInput.value.length;
        titreInput.addEventListener('input', function() {
            document.getElementById('titreCounter').textContent = this.value.length;
        });
        
        const descInput = document.querySelector('textarea[name="description"]');
        document.getElementById('descriptionCounter').textContent = descInput.value.length;
        descInput.addEventListener('input', function() {
            document.getElementById('descriptionCounter').textContent = this.value.length;
        });
        
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') window.location.href = '../frontoffice/index.php'; 
        });
    </script>
</body>
</html>