<?php
// VIEW/backoffice/supprimer_demande.php

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: ../frontoffice/index.php?error=ID invalide');
    exit();
}

$controller = new DemandeController();
$data = $controller->deleteDemande($id_demande);

$demande = $data['demande'] ?? null;

if (!$demande) {
    header('Location: ../frontoffice/index.php?error=Demande introuvable');
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer Demande #<?= $id_demande ?> • Backoffice</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --danger: #ef4444;
            --danger-dark: #dc2626;
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
        
        .modal-container { max-width: 500px; width: 100%; }
        
        .modal-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.15);
            overflow: hidden;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .modal-header {
            padding: 2rem 2rem 1rem;
            text-align: center;
        }
        
        .warning-icon {
            width: 80px;
            height: 80px;
            background: #fee2e2;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            font-size: 2.5rem;
        }
        
        .modal-title { font-size: 1.75rem; font-weight: 700; color: var(--gray-700); margin-bottom: 0.5rem; }
        .modal-subtitle { color: var(--gray-600); font-size: 0.95rem; }
        
        .modal-body { padding: 0 2rem 2rem; }
        
        .demande-info {
            background: var(--gray-50);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border: 1px solid var(--gray-200);
        }
        
        .info-row { display: flex; margin-bottom: 0.75rem; }
        .info-label { width: 100px; font-weight: 500; color: var(--gray-600); }
        .info-value { flex: 1; font-weight: 600; }
        
        .warning-message {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: #92400e;
        }
        
        .action-buttons { display: flex; gap: 1rem; }
        
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
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-danger:hover {
            background: var(--danger-dark);
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 1.5px solid var(--gray-200);
        }
        
        .btn-secondary:hover { background: var(--gray-50); }
    </style>
</head>
<body>
    <div class="modal-container">
        <div class="modal-card">
            <div class="modal-header">
                <div class="warning-icon">⚠️</div>
                <h1 class="modal-title">Confirmation de suppression</h1>
                <p class="modal-subtitle">Cette action est irréversible</p>
            </div>
            
            <div class="modal-body">
                <div class="demande-info">
                    <div class="info-row">
                        <span class="info-label">N° Demande</span>
                        <span class="info-value">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Titre</span>
                        <span class="info-value"><?= htmlspecialchars($demande['titre']) ?></span>
                    </div>
                </div>
                
                <div class="warning-message">
                    ⚠️ La suppression entraînera la perte de tout l'historique de suivi.
                </div>
                
                <div class="action-buttons">
                    <a href="../frontoffice/index.php" class="btn btn-secondary">← Annuler</a>
                    <a href="?id=<?= $id_demande ?>&confirm=yes" class="btn btn-danger">🗑️ Supprimer</a>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('keydown', function(e) { 
            if (e.key === 'Escape') window.location.href = '../frontoffice/index.php'; 
        });
    </script>
</body>
</html>