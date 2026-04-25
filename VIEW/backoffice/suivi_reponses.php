<?php
require_once __DIR__ . '/../../MODEL/config.php';
require_once __DIR__ . '/../../MODEL/SuiviReponse.php';

// Initialiser la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id_demande = isset($_GET['id_demande']) ? (int)$_GET['id_demande'] : 0;

if (!$id_demande) {
    header('Location: index.php?error=ID demande invalide');
    exit();
}

// Connexion DB
$db = Config::getConnexion();

// Récupérer les infos de la demande
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

// Récupérer les réponses via le modèle
$suiviReponse = new SuiviReponse();
$reponses = $suiviReponse->getReponsesByDemande($id_demande);

// Messages
$message = $_GET['success'] ?? '';
$error = $_GET['error'] ?? '';

$user_initials = 'AD';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Réponses Demande #<?= $id_demande ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --dark: #1A2C3E;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px -2px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            color: var(--dark);
            line-height: 1.6;
        }

        /* ========== SIDEBAR ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #006D5B 0%, #004D3D 100%);
            position: fixed;
            height: 100vh;
            padding: 2rem 1.5rem;
            box-shadow: var(--shadow-lg);
            overflow-y: auto;
            z-index: 100;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 800;
            color: white;
            box-shadow: var(--shadow-primary);
        }

        .logo-text { font-size: 1.4rem; font-weight: 800; color: white; }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .avatar {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-info h4 { font-weight: 600; color: white; font-size: 0.95rem; }
        .user-info p { font-size: 0.8rem; color: rgba(255, 255, 255, 0.7); }

        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: var(--radius-md);
            transition: var(--transition-base);
            font-weight: 500;
        }

        .nav-link:hover { background: rgba(255, 255, 255, 0.15); color: white; transform: translateX(5px); }
        .nav-link.active { background: rgba(255, 255, 255, 0.2); color: white; }
        .nav-link i { width: 24px; text-align: center; }

        /* ========== MAIN ========== */
        .main {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
        }

        /* ========== TOP BAR ========== */
        .top-bar {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: var(--shadow-md);
            border-bottom: 3px solid var(--primary);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition-base);
        }

        .back-btn:hover { color: var(--primary); transform: translateX(-3px); }

        .page-title { font-size: 1.5rem; font-weight: 800; }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            box-shadow: var(--shadow-primary);
        }

        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 12px 28px -8px rgba(0,109,91,0.5); }

        /* ========== ALERTES ========== */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-left: 4px solid;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .alert-success { background: #D1FAE5; color: #059669; border-left-color: #059669; }
        .alert-danger { background: #FEE2E2; color: #DC2626; border-left-color: #DC2626; }

        /* ========== DEMANDE INFO CARD ========== */
        .info-card {
            background: var(--white);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
            border-left: 4px solid var(--primary);
        }

        .info-card h3 {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--dark);
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .info-label { font-weight: 600; color: var(--gray-700); min-width: 80px; }

        /* ========== RÉPONSES ========== */
        .reponses-container {
            background: var(--white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            overflow: hidden;
        }

        .reponses-header {
            padding: 1.5rem;
            border-bottom: 2px solid var(--gray-300);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .reponses-header h2 {
            font-size: 1.2rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .reponse-card {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-300);
            transition: var(--transition-base);
        }

        .reponse-card:last-child { border-bottom: none; }
        .reponse-card:hover { background: var(--gray-100); }

        .reponse-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .reponse-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .reponse-type {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .type-reponse { background: #DBEAFE; color: #2563EB; }
        .type-commentaire { background: #FEF3C7; color: #D97706; }
        .type-cloture { background: #D1FAE5; color: #059669; }

        .reponse-date {
            font-size: 0.8rem;
            color: var(--gray-500);
        }

        .reponse-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-icon {
            width: 32px;
            height: 32px;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-700);
            background: var(--gray-100);
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
        }

        .action-icon:hover { background: var(--primary); color: white; transform: translateY(-2px); }
        .action-icon.delete:hover { background: var(--danger); }

        .reponse-contenu {
            background: var(--gray-100);
            padding: 1rem;
            border-radius: var(--radius-md);
            font-size: 0.9rem;
            line-height: 1.7;
            color: var(--dark);
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon { font-size: 3rem; color: var(--gray-300); margin-bottom: 1rem; }
        .empty-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; }
        .empty-text { color: var(--gray-500); font-size: 0.85rem; margin-bottom: 1.5rem; }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="app">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <a href="index.php" class="logo">
                <div class="logo-icon">IG</div>
                <span class="logo-text">InnoGov</span>
            </a>
            <div class="user-profile">
                <div class="avatar"><?= $user_initials ?></div>
                <div class="user-info">
                    <h4>Administrateur</h4>
                </div>
            </div>
            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="ajouter_demande.php" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Ajouter une demande
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-calendar-alt"></i> Rendez-vous
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN -->
        <main class="main">
            <!-- TOP BAR -->
            <div class="top-bar">
                <div>
                    <a href="index.php" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                    <h1 class="page-title" style="margin-top: 0.5rem;">
                        Réponses - Demande #<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?>
                    </h1>
                </div>
                <a href="ajouter_reponse.php?id_demande=<?= $id_demande ?>" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter une réponse
                </a>
            </div>

            <!-- MESSAGES -->
            <?php if ($message): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- INFO DEMANDE -->
            <div class="info-card">
                <h3><i class="fas fa-info-circle"></i> Informations de la demande</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="info-label">Titre :</span>
                        <span><?= htmlspecialchars($demande['titre']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Citoyen :</span>
                        <span><?= htmlspecialchars($demande['prenom_citoyen'] . ' ' . $demande['nom_citoyen']) ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Service :</span>
                        <span><?= htmlspecialchars($demande['nom_service'] ?? 'Non assigné') ?></span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut :</span>
                        <span>
                            <?php
                            $statuts = [
                                'en_attente' => '⏳ En attente',
                                'en_cours' => '🔄 En cours',
                                'traite' => '✅ Traité',
                                'refuse' => '❌ Refusé'
                            ];
                            echo $statuts[$demande['statut']] ?? $demande['statut'];
                            ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- LISTE DES RÉPONSES -->
            <div class="reponses-container">
                <div class="reponses-header">
                    <h2>
                        <i class="fas fa-comments" style="color: var(--primary);"></i>
                        Réponses (<?= count($reponses) ?>)
                    </h2>
                </div>

                <?php if (empty($reponses)): ?>
                    <div class="empty-state">
                        <div class="empty-icon">📭</div>
                        <h3 class="empty-title">Aucune réponse</h3>
                        <p class="empty-text">Aucune réponse n'a encore été ajoutée à cette demande.</p>
                        <a href="ajouter_reponse.php?id_demande=<?= $id_demande ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Ajouter une réponse
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($reponses as $rep): ?>
                        <?php
                        $typeClass = match($rep['type_reponse']) {
                            'commentaire' => 'type-commentaire',
                            'cloture' => 'type-cloture',
                            default => 'type-reponse'
                        };
                        $typeLabel = match($rep['type_reponse']) {
                            'commentaire' => '📝 Commentaire',
                            'cloture' => '✅ Clôture',
                            default => '💬 Réponse'
                        };
                        ?>
                        <div class="reponse-card">
                            <div class="reponse-header">
                                <div class="reponse-meta">
                                    <span class="reponse-type <?= $typeClass ?>"><?= $typeLabel ?></span>
                                    <span class="reponse-date">
                                        <i class="fas fa-clock"></i>
                                        <?= date('d/m/Y H:i', strtotime($rep['date_creation'])) ?>
                                    </span>
                                    <?php if (!empty($rep['nom_agent'])): ?>
                                        <span style="font-size: 0.8rem; color: var(--gray-500);">
                                            <i class="fas fa-user"></i>
                                            <?= htmlspecialchars($rep['prenom_agent'] . ' ' . $rep['nom_agent']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="reponse-actions">
                                    <!-- BOUTON MODIFIER -->
                                    <a href="modifier_reponse.php?id=<?= $rep['id_reponse'] ?>" 
                                       class="action-icon" 
                                       title="Modifier cette réponse">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <!-- BOUTON SUPPRIMER -->
                                    <a href="supprimer_reponse.php?id=<?= $rep['id_reponse'] ?>" 
                                       class="action-icon delete" 
                                       title="Supprimer cette réponse"
                                       onclick="return confirm('Supprimer cette réponse ?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="reponse-contenu">
                                <?= nl2br(htmlspecialchars($rep['contenu'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </main>
    </div>
</body>
</html>