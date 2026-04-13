<?php
// VIEW/backoffice/modifier_demande.php

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: ../frontoffice/index.php?error=ID invalide');
    exit();
}

$controller = new DemandeController();
$data = $controller->modifier($id_demande);

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
    <title>InnoGov • Modifier Demande #<?= $id_demande ?></title>
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
            display: flex; 
            align-items: center; 
            gap: 1rem; 
            flex-wrap: wrap;
        }

        .demande-id { 
            background: rgba(255,255,255,0.2); 
            padding: 0.25rem 1rem; 
            border-radius: 100px; 
            font-size: 1rem; 
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

        .form-textarea { resize: vertical; min-height: 120px; }

        .current-info {
            background: var(--bg-secondary);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border: 1px solid var(--border-subtle);
        }

        .error-message {
            background: #fee2e2;
            color: #dc2626;
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            font-size: 0.875rem;
            border-left: 4px solid #dc2626;
        }

        .char-counter {
            text-align: right;
            font-size: 0.7rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
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
            .form-title { flex-direction: column; align-items: flex-start; }
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
                
                <div class="current-info">
                    <strong><i class="fas fa-info-circle"></i> Statut actuel :</strong> 
                    <?php
                    $statuts = [
                        'en_attente' => '⏳ En attente', 
                        'en_cours' => '🔄 En cours', 
                        'traite' => '✅ Traité', 
                        'refuse' => '❌ Refusé'
                    ];
                    echo $statuts[$demande['statut']] ?? $demande['statut'];
                    ?>
                    <br>
                    <strong><i class="fas fa-calendar"></i> Date de création :</strong> <?= date('d/m/Y H:i', strtotime($demande['date_creation'])) ?>
                </div>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label">Titre</label>
                        <input type="text" name="titre" class="form-input" 
                               value="<?= htmlspecialchars($demande['titre']) ?>" maxlength="255" required>
                        <div class="char-counter"><i class="fas fa-pencil-alt"></i> <span id="titreCounter">0</span>/255</div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Service</label>
                        <select name="id_service" class="form-select" required>
                            <option value="">-- Sélectionnez un service --</option>
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
                        <select name="type_demande" class="form-select" required>
                            <option value="">-- Sélectionnez un type --</option>
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
                        <textarea name="description" class="form-textarea" required><?= htmlspecialchars($demande['description']) ?></textarea>
                        <div class="char-counter"><i class="fas fa-align-left"></i> <span id="descriptionCounter">0</span> caractères</div>
                    </div>
                    
                    <div class="form-actions">
                        <a href="../frontoffice/index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer
                        </button>
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
