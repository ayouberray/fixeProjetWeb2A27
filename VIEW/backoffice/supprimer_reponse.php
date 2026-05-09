<?php
require_once __DIR__ . '/../../CONTROLLER/ReponseController.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_reponse = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_reponse) {
    header('Location: index.php?error=ID réponse invalide');
    exit();
}

$reponseController = new ReponseController();
$reponse = $reponseController->getReponseById($id_reponse);

if (!$reponse) {
    header('Location: index.php?error=Réponse introuvable');
    exit();
}


if (isset($_POST['confirm_delete'])) {
    if ($reponseController->supprimer($id_reponse)) {
        header('Location: index.php?success=Réponse supprimée avec succès');
        exit();
    } else {
        $error = 'Erreur lors de la suppression';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Supprimer une réponse</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --danger: #E31E24;
            --dark: #1A2C3E;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .container { max-width: 550px; width: 100%; }

        .card {
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

        .card-header {
            padding: 2rem;
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
            color: white;
            text-align: center;
        }

        .warning-icon {
            width: 70px;
            height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
        }

        .card-title { font-size: 1.5rem; font-weight: 800; }

        .card-body { padding: 2rem; }

        .info-box {
            background: #FEF2F2;
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid #DC2626;
        }

        .info-row {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-label { font-weight: 600; color: var(--gray-700); min-width: 100px; }

        .warning-text {
            color: #DC2626;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: #FEF2F2;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
        }

        .form-actions { display: flex; gap: 1rem; }

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

        .btn-danger {
            background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%);
            color: white;
            box-shadow: 0 8px 20px -6px rgba(220,38,38,0.4);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 28px -8px rgba(220,38,38,0.5);
        }

        .btn-secondary {
            background: white;
            color: var(--gray-700);
            border: 2px solid var(--gray-300);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
        }

        @media (max-width: 640px) {
            .form-actions { flex-direction: column; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="warning-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1 class="card-title">Supprimer la réponse</h1>
            </div>

            <div class="card-body">
                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-hashtag"></i> Réponse :</span>
                        <span>#<?= $id_reponse ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-file-alt"></i> Demande :</span>
                        <span><?= htmlspecialchars($reponse['titre_demande']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user"></i> Citoyen :</span>
                        <span><?= htmlspecialchars($reponse['prenom_citoyen'] . ' ' . $reponse['nom_citoyen']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-calendar"></i> Date :</span>
                        <span><?= date('d/m/Y H:i', strtotime($reponse['date_creation'])) ?></span>
                    </div>
                </div>

                <div class="info-box">
                    <strong>Contenu :</strong><br>
                    <?= nl2br(htmlspecialchars(substr($reponse['contenu'], 0, 200))) ?>...
                </div>

                <div class="warning-text">
                    <i class="fas fa-exclamation-circle"></i> 
                    Cette action est irréversible. Êtes-vous sûr de vouloir supprimer cette réponse ?
                </div>

                <form method="POST">
                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" name="confirm_delete" class="btn btn-danger">
                            <i class="fas fa-trash-alt"></i> Confirmer la suppression
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>