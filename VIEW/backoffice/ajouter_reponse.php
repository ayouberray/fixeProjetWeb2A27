<?php
require_once __DIR__ . '/../../CONTROLLER/ReponseController.php';

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier l'ID de la demande
$id_demande = isset($_GET['id_demande']) ? (int)$_GET['id_demande'] : 0;

if (!$id_demande) {
    header('Location: index.php?error=ID demande invalide');
    exit();
}

// Récupérer les infos de la demande
require_once __DIR__ . '/../../MODEL/config.php';
$db = Config::getConnexion();
$stmt = $db->prepare("SELECT d.*, c.nom as nom_citoyen, c.prenom as prenom_citoyen, s.nom_service 
                      FROM demandes d 
                      LEFT JOIN citoyens c ON d.id_citoyen = c.id_citoyen 
                      LEFT JOIN services s ON d.id_service = s.id_service 
                      WHERE d.id_demande = ?");
$stmt->execute([$id_demande]);
$demande = $stmt->fetch();

if (!$demande) {
    header('Location: index.php?error=Demande introuvable');
    exit();
}

$error = '';
$reponseController = new ReponseController();

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contenu = trim($_POST['contenu'] ?? '');
    $type_reponse = $_POST['type_reponse'] ?? 'reponse';
    
    if (strlen($contenu) < 10) {
        $error = 'La réponse doit contenir au moins 10 caractères';
    } else {
        $id_agent = $_SESSION['user_id'] ?? 1;
        
        if ($reponseController->ajouter($id_demande, $demande['id_citoyen'], $id_agent, $contenu, $type_reponse)) {
            header('Location: index.php?success=Réponse ajoutée avec succès');
            exit();
        } else {
            $error = 'Erreur lors de l\'ajout de la réponse';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Ajouter une réponse</title>
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
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
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
            padding: 2rem;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .back-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            transition: var(--transition-base);
        }

        .back-link:hover { color: white; transform: translateX(-3px); }

        .form-title { font-size: 1.75rem; font-weight: 800; }

        .form-body { padding: 2rem; }

        .info-box {
            background: var(--primary-light);
            border-radius: var(--radius-md);
            padding: 1.25rem;
            margin-bottom: 1.5rem;
            border-left: 4px solid var(--primary);
        }

        .info-row {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .info-label { font-weight: 600; color: var(--gray-700); min-width: 130px; }

        .form-group { margin-bottom: 1.5rem; }

        .form-label {
            display: block;
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        .form-select, .form-textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: var(--transition-base);
        }

        .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,109,91,0.15);
        }

        .form-textarea { resize: vertical; min-height: 150px; }

        .error-message {
            background: #FEE2E2;
            color: #DC2626;
            padding: 1rem;
            border-radius: var(--radius-md);
            margin-bottom: 1.5rem;
            border-left: 4px solid #DC2626;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
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
                <a href="index.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                </a>
                <h1 class="form-title">Ajouter une réponse</h1>
            </div>

            <div class="form-body">
                <?php if ($error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>

                <div class="info-box">
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-hashtag"></i> Demande :</span>
                        <span>#<?= str_pad($demande['id_demande'], 5, '0', STR_PAD_LEFT) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-file-alt"></i> Titre :</span>
                        <span><?= htmlspecialchars($demande['titre']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-user"></i> Citoyen :</span>
                        <span><?= htmlspecialchars($demande['prenom_citoyen'] . ' ' . $demande['nom_citoyen']) ?></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label"><i class="fas fa-building"></i> Service :</span>
                        <span><?= htmlspecialchars($demande['nom_service'] ?? 'Non assigné') ?></span>
                    </div>
                </div>

                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="type_reponse">Type de réponse</label>
                        <select id="type_reponse" name="type_reponse" class="form-select">
                            <option value="reponse">💬 Réponse</option>
                            <option value="commentaire">📝 Commentaire</option>
                            <option value="cloture">✅ Clôture</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="contenu">Contenu de la réponse</label>
                        <textarea id="contenu" name="contenu" class="form-textarea" 
                                  placeholder="Rédigez votre réponse (minimum 10 caractères)..."></textarea>
                    </div>

                    <div class="form-actions">
                        <a href="index.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Envoyer la réponse
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>