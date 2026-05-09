<?php


require_once __DIR__ . '/../../CONTROLLER/SuiviDemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id_demande) {
    header('Location: index.php?error=ID de demande invalide');
    exit();
}

$controller = new SuiviDemandeController();
$data = $controller->show($id_demande);

$demande = $data['demande'] ?? null;
$historique = $data['historique'] ?? [];
$delai = $data['delai'] ?? null;

if (!$demande) {
    header('Location: index.php?error=Demande introuvable');
    exit();
}

$user_nom = $_SESSION['user_nom'] ?? 'Ben Ali';
$user_prenom = $_SESSION['user_prenom'] ?? 'Mohamed';

$statut_config = [
    'en_attente' => ['class' => 'warning', 'icon' => '⏳', 'label' => 'En attente'],
    'en_cours' => ['class' => 'info', 'icon' => '🔄', 'label' => 'En cours'],
    'traite' => ['class' => 'success', 'icon' => '✅', 'label' => 'Traité'],
    'refuse' => ['class' => 'danger', 'icon' => '❌', 'label' => 'Refusé']
];

$statut_actuel = $statut_config[$demande['statut']] ?? $statut_config['en_attente'];
$reference = 'DM-' . date('Y') . '-' . str_pad($id_demande, 6, '0', STR_PAD_LEFT);

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
    <title>InnoGov • Suivi Demande #<?= $id_demande ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
       

        :root {
            
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            
            --bg-main: #F5FCF9;
            --bg-secondary: #EBF7F3;
            --bg-card: rgba(255, 255, 255, 0.92);
            --bg-card-dashboard: rgba(255, 255, 255, 0.95);
            
            --text-title: #1A2E2A;
            --text-body: #2C5A4F;
            --text-secondary: #5C8B7E;
            --text-muted: #8FB3A8;
            
            --border-subtle: rgba(0, 109, 91, 0.12);
            --border-normal: rgba(0, 109, 91, 0.2);
            --border-strong: rgba(0, 109, 91, 0.35);
            
            --shadow-card: 0 24px 48px rgba(0, 77, 61, 0.12);
            --shadow-card-hover: 0 32px 64px rgba(0, 77, 61, 0.16);
            --shadow-btn: 0 4px 16px rgba(0, 109, 91, 0.25);
            --shadow-btn-hover: 0 8px 24px rgba(0, 109, 91, 0.35);
            
            --radius-card: 20px;
            --radius-btn: 10px;
            --radius-badge: 100px;
            
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-main);
            color: var(--text-body);
            line-height: 1.6;
            min-height: 100vh;
            padding: 2rem;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            color: var(--text-title);
        }

        
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-main);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            transition: opacity 0.5s;
        }

        .loader.hide {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--border-subtle);
            border-top-color: var(--primary);
            border-right-color: var(--primary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #006D5B 0%, #004D3D 100%);
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            padding: 2rem 1.5rem;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.08);
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
            cursor: pointer;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            font-weight: 700;
            color: white;
            box-shadow: 0 0 20px rgba(0, 109, 91, 0.3);
        }

        .logo-text {
            font-size: 1.4rem;
            font-weight: 800;
            color: white;
        }

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
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
        }

        .user-info h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: white;
            font-family: 'DM Sans', sans-serif;
        }

        .user-info p {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: var(--transition-base);
            font-weight: 500;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .nav-link i {
            width: 24px;
        }

        
        .main {
            margin-left: 280px;
            max-width: calc(100% - 280px);
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        
        .page-header {
            background: var(--bg-card-dashboard);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-card);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 1rem;
            transition: var(--transition-base);
        }

        .back-link:hover {
            color: var(--primary-dark);
            transform: translateX(-3px);
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--text-title);
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .demande-id {
            background: var(--bg-secondary);
            padding: 0.25rem 1rem;
            border-radius: var(--radius-badge);
            font-size: 0.9rem;
            color: var(--primary);
            font-weight: 600;
        }

        .reference {
            background: var(--primary);
            color: white;
            padding: 0.25rem 1rem;
            border-radius: var(--radius-badge);
            font-size: 0.8rem;
            font-weight: 500;
        }

        
        .status-banner {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-card);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
        }

        .status-header {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .status-icon {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .status-icon.warning { background: #FEF3C7; }
        .status-icon.info { background: #DBEAFE; }
        .status-icon.success { background: #D1FAE5; }
        .status-icon.danger { background: #FEE2E2; }

        .status-info h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-title);
            margin-bottom: 0.5rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 1rem;
            border-radius: var(--radius-badge);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .status-badge.warning { background: #FEF3C7; color: #D97706; }
        .status-badge.info { background: #DBEAFE; color: #2563EB; }
        .status-badge.success { background: #D1FAE5; color: var(--primary); }
        .status-badge.danger { background: #FEE2E2; color: #DC2626; }

    
        .progress-container {
            margin-top: 1.5rem;
        }

        .progress-steps {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .progress-step {
            text-align: center;
            flex: 1;
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        .progress-step.completed {
            color: var(--primary);
            font-weight: 600;
        }

        .progress-bar {
            height: 8px;
            background: var(--border-subtle);
            border-radius: var(--radius-badge);
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--primary);
            border-radius: var(--radius-badge);
            transition: width 0.5s ease;
        }

        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border-subtle);
            transition: var(--transition-base);
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-card-hover);
            border-color: var(--border-strong);
        }

        .info-card-title {
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            color: var(--text-secondary);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-item {
            margin-bottom: 1rem;
        }
        .info-item:last-child {
            margin-bottom: 0;
        }
        .info-label {
            font-size: 0.7rem;
            color: var(--text-secondary);
            margin-bottom: 0.25rem;
        }
        .info-value {
            font-weight: 600;
            color: var(--text-title);
        }

        
        .description-text {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-subtle);
        }

        .description-text p {
            color: var(--text-body);
            line-height: 1.6;
            white-space: pre-wrap;
        }

        
        .timeline {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border-subtle);
        }

        .timeline-title {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-title);
        }

        .timeline-items {
            position: relative;
        }

        .timeline-items::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: var(--border-subtle);
        }

        .timeline-item {
            display: flex;
            gap: 1.5rem;
            padding-bottom: 1.5rem;
            position: relative;
        }

        .timeline-item:last-child {
            padding-bottom: 0;
        }

        .timeline-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: white;
            border: 2px solid var(--border-subtle);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            z-index: 1;
        }

        .timeline-icon.success {
            background: #D1FAE5;
            border-color: #10b981;
        }
        .timeline-icon.warning {
            background: #FEF3C7;
            border-color: #f59e0b;
        }
        .timeline-icon.info {
            background: #DBEAFE;
            border-color: #3b82f6;
        }
        .timeline-icon.danger {
            background: #FEE2E2;
            border-color: #ef4444;
        }

        .timeline-content {
            flex: 1;
        }

        .timeline-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .timeline-action {
            font-weight: 600;
            color: var(--text-title);
            font-size: 0.85rem;
        }

        .timeline-date {
            font-size: 0.7rem;
            color: var(--text-secondary);
        }

        .timeline-comment {
            color: var(--text-body);
            font-size: 0.8rem;
            margin-top: 0.5rem;
        }

        .timeline-agent {
            font-size: 0.7rem;
            color: var(--primary);
            margin-top: 0.5rem;
        }

    
        .actions-bar {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.7rem 1.5rem;
            border-radius: var(--radius-btn);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
            font-size: 0.85rem;
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

        .btn-success {
            background: #10b981;
            color: white;
        }
        .btn-success:hover {
            background: #059669;
            transform: translateY(-2px);
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

        .btn-danger {
            background: white;
            color: #ef4444;
            border: 1.5px solid #fee2e2;
        }
        .btn-danger:hover {
            background: #ef4444;
            color: white;
            transform: translateY(-2px);
        }

        .btn-pdf {
            background: #dc2626;
            color: white;
        }
        .btn-pdf:hover {
            background: #b91c1c;
            transform: translateY(-2px);
        }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        .alert-info {
            background: #D1FAE5;
            color: var(--primary);
            padding: 1rem;
            border-radius: var(--radius-btn);
            text-align: center;
            margin-top: 2rem;
        }

        
        .toast-notify {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 40px;
            box-shadow: var(--shadow-card);
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            font-size: 0.85rem;
        }

        .toast-notify.show {
            opacity: 1;
        }

    
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            .sidebar {
                display: none;
            }
            .main {
                margin-left: 0;
                max-width: 100%;
            }
            .info-grid {
                grid-template-columns: 1fr;
            }
            .status-header {
                flex-direction: column;
                text-align: center;
            }
            .actions-bar {
                flex-direction: column;
            }
            .btn {
                justify-content: center;
            }
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .sidebar, .actions-bar, .back-link, .no-print {
                display: none !important;
            }
            .main {
                margin-left: 0;
                max-width: 100%;
            }
            .status-banner, .info-card, .description-text, .timeline {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>

<div class="loader" id="loader">
    <div class="spinner"></div>
</div>


<div id="toastMsg" class="toast-notify">✓ Action réussie</div>


<aside class="sidebar">
    <div class="logo" onclick="window.location.href='index.php'">
        <div class="logo-icon">IG</div>
        <span class="logo-text">InnoGov</span>
    </div>

    <div class="user-profile">
        <div class="avatar"><?= strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) ?></div>
        <div class="user-info">
            <h4><?= htmlspecialchars($user_prenom . ' ' . $user_nom) ?></h4>
            <p>Citoyen</p>
        </div>
    </div>

    <ul class="nav-menu">
        <li class="nav-item">
            <a href="index.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Tableau de bord
            </a>
        </li>
        <li class="nav-item">
            <a href="../../VIEW/backoffice/ajouter_demande.php" class="nav-link">
                <i class="fas fa-plus-circle"></i> Nouvelle Demande
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-calendar-alt"></i> Rendez-vous
            </a>
        </li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="fas fa-user"></i> Mon Profil
            </a>
        </li>
    </ul>
</aside>


<main class="main">
    <div class="container">
        <div class="page-header no-print reveal">
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Retour au tableau de bord
            </a>
            <div class="page-title">
                Suivi de la demande
                <span class="demande-id">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span>
                <span class="reference">Réf: <?= $reference ?></span>
            </div>
        </div>

    
        <div class="status-banner reveal">
            <div class="status-header">
                <div class="status-icon <?= $statut_actuel['class'] ?>">
                    <?= $statut_actuel['icon'] ?>
                </div>
                <div class="status-info">
                    <h2><?= htmlspecialchars($demande['titre']) ?></h2>
                    <span class="status-badge <?= $statut_actuel['class'] ?>">
                        <?= $statut_actuel['icon'] ?> <?= $statut_actuel['label'] ?>
                    </span>
                </div>
            </div>

            <div class="progress-container">
                <div class="progress-steps">
                    <span class="progress-step completed"><i class="fas fa-file-alt"></i> Création</span>
                    <span class="progress-step <?= in_array($demande['statut'], ['en_cours', 'traite']) ? 'completed' : '' ?>">
                        <i class="fas fa-spinner"></i> En cours
                    </span>
                    <span class="progress-step <?= $demande['statut'] == 'traite' ? 'completed' : '' ?>">
                        <i class="fas fa-check-circle"></i> Traité
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: 
                        <?php
                        if ($demande['statut'] == 'en_attente') echo '10%';
                        elseif ($demande['statut'] == 'en_cours') echo '50%';
                        elseif ($demande['statut'] == 'traite') echo '100%';
                        elseif ($demande['statut'] == 'refuse') echo '100%';
                        else echo '10%';
                        ?>
                    "></div>
                </div>
            </div>
        </div>

        
        <div class="info-grid">
            <div class="info-card reveal">
                <div class="info-card-title"><i class="fas fa-info-circle"></i> Informations générales</div>
                <div class="info-item">
                    <div class="info-label">Type de demande</div>
                    <div class="info-value"><?= $types_demandes[$demande['type_demande']] ?? $demande['type_demande'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Service concerné</div>
                    <div class="info-value"><?= htmlspecialchars($demande['nom_service'] ?? 'Non assigné') ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Date de création</div>
                    <div class="info-value"><?= $demande['date_creation_format'] ?> à <?= $demande['heure_creation'] ?></div>
                </div>
            </div>

            <div class="info-card reveal">
                <div class="info-card-title"><i class="fas fa-user-check"></i> Contact et délais</div>
                <div class="info-item">
                    <div class="info-label">Demandeur</div>
                    <div class="info-value"><?= htmlspecialchars($user_prenom . ' ' . $user_nom) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Délai de traitement</div>
                    <div class="info-value">
                        <?php if ($demande['statut'] == 'traite' && $delai): ?>
                            <span style="color: #10b981;">✅ Traitée en <strong><?= $delai ?> jour(s)</strong></span>
                        <?php elseif ($demande['statut'] == 'traite'): ?>
                            <span style="color: #10b981;">✅ Demande traitée</span>
                        <?php else: ?>
                            <span style="color: #f59e0b;">⏳ En attente depuis <strong><?= $demande['jours_ecoules'] ?> jour(s)</strong></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="description-text reveal">
            <div class="info-card-title" style="margin-bottom: 1rem;"><i class="fas fa-file-alt"></i> Description</div>
            <p><?= nl2br(htmlspecialchars($demande['description'])) ?></p>
        </div>

        
        <div class="timeline reveal">
            <div class="timeline-title"><i class="fas fa-history"></i> Historique de suivi</div>

            <?php if (empty($historique)): ?>
                <div class="empty-state">
                    <i class="fas fa-clock" style="font-size: 2rem; color: #cbd5e1; margin-bottom: 0.5rem; display: block;"></i>
                    <p>Aucun suivi disponible pour le moment.</p>
                </div>
            <?php else: ?>
                <div class="timeline-items">
                    <?php foreach ($historique as $suivi): 
                        $icon_class = 'info'; $icon = 'fa-clipboard-list';
                        if ($suivi['nouveau_statut'] == 'traite') { $icon_class = 'success'; $icon = 'fa-check-circle'; }
                        elseif ($suivi['nouveau_statut'] == 'en_cours') { $icon_class = 'info'; $icon = 'fa-spinner'; }
                        elseif ($suivi['nouveau_statut'] == 'refuse') { $icon_class = 'danger'; $icon = 'fa-times-circle'; }
                        elseif ($suivi['nouveau_statut'] == 'en_attente') { $icon_class = 'warning'; $icon = 'fa-clock'; }
                    ?>
                    <div class="timeline-item">
                        <div class="timeline-icon <?= $icon_class ?>">
                            <i class="fas <?= $icon ?>"></i>
                        </div>
                        <div class="timeline-content">
                            <div class="timeline-header">
                                <span class="timeline-action">
                                    <?php if ($suivi['ancien_statut']): ?>
                                        Statut changé : <?= $statut_config[$suivi['ancien_statut']]['label'] ?? $suivi['ancien_statut'] ?> 
                                        → <?= $statut_config[$suivi['nouveau_statut']]['label'] ?? $suivi['nouveau_statut'] ?>
                                    <?php else: ?>
                                        <i class="fas fa-plus-circle"></i> Demande créée
                                    <?php endif; ?>
                                </span>
                                <span class="timeline-date">
                                    <i class="far fa-calendar-alt"></i> <?= $suivi['date_formatee'] ?> à <?= $suivi['heure_formatee'] ?>
                                </span>
                            </div>
                            <?php if ($suivi['commentaire']): ?>
                                <div class="timeline-comment">
                                    <i class="fas fa-comment"></i> <?= htmlspecialchars($suivi['commentaire']) ?>
                                </div>
                            <?php endif; ?>
                            <?php if ($suivi['agent_nom']): ?>
                                <div class="timeline-agent">
                                    <i class="fas fa-user-check"></i> Par <?= htmlspecialchars($suivi['agent_prenom'] . ' ' . $suivi['agent_nom']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    
        <div class="actions-bar no-print reveal">
            <a href="index.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour aux demandes
            </a>

            <?php if (in_array($demande['statut'], ['en_attente', 'en_cours'])): ?>
                <a href="../backoffice/modifier_demande.php?id=<?= $id_demande ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
            <?php endif; ?>

            <?php if ($demande['statut'] == 'traite'): ?>
                <button onclick="window.print()" class="btn btn-success">
                    <i class="fas fa-print"></i> Imprimer
                </button>
                <button onclick="exporterPDF()" class="btn btn-pdf">
                    <i class="fas fa-file-pdf"></i> Exporter PDF
                </button>
            <?php endif; ?>

            <a href="../backoffice/supprimer_demande.php?id=<?= $id_demande ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette demande ?')">
                <i class="fas fa-trash-alt"></i> Supprimer
            </a>
        </div>

        <?php if ($demande['statut'] == 'traite'): ?>
            <div class="alert-info no-print reveal">
                <i class="fas fa-check-circle"></i> Cette demande a été traitée. Vous pouvez exporter ou imprimer le récapitulatif.
            </div>
        <?php endif; ?>
    </div>
</main>

<script>

    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        setTimeout(function() {
            loader.classList.add('hide');
            setTimeout(function() {
                loader.style.display = 'none';
            }, 500);
        }, 800);
        
        initScrollReveal();
    });

    
    function initScrollReveal() {
        const reveals = document.querySelectorAll('.reveal');
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                }
            });
        }, { threshold: 0.1 });
        reveals.forEach(el => observer.observe(el));
    }

    
    function showToast(message, isError = false) {
        const toast = document.getElementById('toastMsg');
        if (toast) {
            toast.style.background = isError ? '#DC2626' : 'var(--primary)';
            toast.innerHTML = isError ? '⚠️ ' + message : '✓ ' + message;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    }

    function exporterPDF() {
        const contenu = document.querySelector('.container').cloneNode(true);
        contenu.querySelectorAll('.no-print').forEach(el => el.remove());
        
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>InnoGov - Suivi Demande #<?= $id_demande ?></title>
                <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
                <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; }
                    body { font-family: 'DM Sans', sans-serif; padding: 1.5cm; background: white; }
                    h1, h2, h3 { font-family: 'Syne', sans-serif; }
                    .status-banner, .info-card, .description-text, .timeline {
                        background: white; border: 1px solid #e2e8f0; border-radius: 16px;
                        padding: 1.5rem; margin-bottom: 1.5rem;
                    }
                    .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem; }
                    .status-badge { display: inline-block; padding: 0.25rem 1rem; border-radius: 100px; font-weight: 600; }
                    .status-badge.success { background: #D1FAE5; color: #006D5B; }
                    .status-badge.warning { background: #FEF3C7; color: #D97706; }
                    .status-badge.info { background: #DBEAFE; color: #2563EB; }
                    .status-badge.danger { background: #FEE2E2; color: #DC2626; }
                    .timeline-item { display: flex; gap: 1rem; margin-bottom: 1rem; }
                    .timeline-icon {
                        width: 40px; height: 40px; border-radius: 12px;
                        display: flex; align-items: center; justify-content: center;
                    }
                    .timeline-icon.success { background: #D1FAE5; }
                    .timeline-icon.warning { background: #FEF3C7; }
                    .timeline-icon.info { background: #DBEAFE; }
                    .page-header { display: flex; justify-content: space-between; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 2px solid #e2e8f0; }
                    .footer { margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e2e8f0; text-align: center; font-size: 0.8rem; color: #64748b; }
                    .reference { background: #006D5B; color: white; padding: 0.25rem 1rem; border-radius: 100px; font-size: 0.8rem; }
                </style>
            </head>
            <body>
                <div class="page-header">
                    <h1 style="color: #006D5B;">InnoGov</h1>
                    <span class="reference">Réf: <?= $reference ?></span>
                </div>
                <div style="color: #64748b; margin-bottom: 1.5rem;">Document généré le <?= date('d/m/Y à H:i') ?></div>
                ${contenu.innerHTML}
                <div class="footer">
                    <i class="fas fa-building"></i> Mairie - Service Municipal<br>
                    Ce document est un récapitulatif officiel.
                </div>
                <script>window.onload = function() { window.print(); setTimeout(() => window.close(), 500); };<\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }
</script>
</body>
</html>