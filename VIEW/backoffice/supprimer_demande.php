<?php


require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: ../frontoffice/index.php?error=ID invalide');
    exit();
}

$controller = new DemandeController();


if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    
    $result = $controller->deleteDemandeConfirm($id_demande);
    
    if ($result['success']) {
        
        header('Location: supprimer_demande.php?deleted=success&id=' . $id_demande);
        exit();
    } else {
        $error = $result['error'] ?? 'Erreur lors de la suppression';
        header('Location: ../frontoffice/index.php?error=' . urlencode($error));
        exit();
    }
}


$data = $controller->supprimer($id_demande);
$demande = $data['demande'] ?? null;

if (!$demande) {
    header('Location: ../frontoffice/index.php?error=Demande introuvable');
    exit();
}


if (isset($_GET['deleted']) && $_GET['deleted'] === 'success') {
    $deleted_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>InnoGov • Suppression réussie</title>
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

            .demande-info {
                background: var(--bg-main);
                border-radius: 16px;
                padding: 1rem;
                margin: 1.5rem;
                border: 1px solid var(--border-subtle);
            }

            .info-row { 
                display: flex; 
                justify-content: center;
                gap: 0.5rem;
                font-size: 0.9rem;
            }

            .info-value { 
                font-weight: 600; 
                color: var(--primary);
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

            .btn-secondary {
                background: white;
                color: var(--text-secondary);
                border: 1.5px solid var(--border-subtle);
            }

            .btn-secondary:hover {
                border-color: var(--primary);
                color: var(--primary);
                transform: translateY(-2px);
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
                    <h1 class="success-title">Demande supprimée !</h1>
                    <p class="success-message">La demande a été supprimée avec succès.</p>
                </div>
                
                <div class="demande-info">
                    <div class="info-row">
                        <span><i class="fas fa-hashtag"></i> N° Demande :</span>
                        <span class="info-value">#<?= str_pad($deleted_id, 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                </div>
                
                <div class="action-buttons">
                    <a href="../frontoffice/index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Retour à l'accueil
                    </a>
                </div>
                
                <div class="countdown">
                    <i class="fas fa-hourglass-half"></i> Redirection automatique dans <span id="countdown">5</span> secondes...
                </div>
            </div>
        </div>
        
        <script>
            let seconds = 5;
            const countdownElement = document.getElementById('countdown');
            const interval = setInterval(function() {
                seconds--;
                countdownElement.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = '../frontoffice/index.php';
                }
            }, 1000);
        </script>
    </body>
    </html>
    <?php
    exit();
}

// Page de confirmation avant suppression
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Supprimer Demande #<?= $id_demande ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
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
            --danger: #ef4444;
            --danger-dark: #dc2626;
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

        .modal-container { max-width: 500px; width: 100%; }

        .modal-card {
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
            color: var(--danger);
        }

        .modal-title { 
            font-size: 1.75rem; 
            font-weight: 800; 
            color: var(--text-title); 
            margin-bottom: 0.5rem; 
        }

        .modal-subtitle { 
            color: var(--text-secondary); 
            font-size: 0.9rem; 
        }

        .modal-body { padding: 0 2rem 2rem; }

        .demande-info {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            border: 1px solid var(--border-subtle);
        }

        .info-row { 
            display: flex; 
            margin-bottom: 0.75rem; 
        }

        .info-label { 
            width: 100px; 
            font-weight: 500; 
            color: var(--text-secondary); 
        }

        .info-value { 
            flex: 1; 
            font-weight: 600; 
            color: var(--text-title);
        }

        .warning-message {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            color: #92400e;
            font-size: 0.85rem;
        }

        .action-buttons { 
            display: flex; 
            gap: 1rem; 
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

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), var(--danger-dark));
            color: white;
            box-shadow: 0 4px 16px rgba(239, 68, 68, 0.25);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(239, 68, 68, 0.35);
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
            .action-buttons { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="modal-container">
        <div class="modal-card">
            <div class="modal-header">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="modal-title">Confirmation de suppression</h1>
                <p class="modal-subtitle">Cette action est irréversible</p>
            </div>
            
            <div class="modal-body">
                <div class="demande-info">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-hashtag"></i> N° Demande</span>
                        <span class="info-value">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-heading"></i> Titre</span>
                        <span class="info-value"><?= htmlspecialchars($demande['titre']) ?></span>
                    </div>
                </div>
                
                <div class="warning-message">
                    <i class="fas fa-trash-alt"></i> La suppression entraînera la perte de tout l'historique de suivi.
                </div>
                
                <div class="action-buttons">
                    <a href="../frontoffice/index.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <a href="?id=<?= $id_demande ?>&confirm=yes" class="btn btn-danger">
                        <i class="fas fa-trash-alt"></i> Supprimer
                    </a>
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