<?php

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';
require_once __DIR__ . '/../../MODEL/Demande.php';
require_once __DIR__ . '/../../MODEL/SuiviDemande.php';

$controller = new DemandeController();
$data = $controller->index();

$demandes = $data['demandes'] ?? [];
$stats = $data['stats'] ?? ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];

// Forcer le profil Administrateur
$user_id = 1; 
$user_nom = 'Administrateur';
$user_prenom = 'Admin';
$user_initials = 'AD';

$message = $_GET['success'] ?? $_GET['message'] ?? '';
$error = $_GET['error'] ?? '';

$types_demandes = [
    'urbanisme' => '🏗️ Urbanisme',
    'voirie' => '🛣️ Voirie',
    'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture',
    'social' => '🤝 Social',
    'autre' => '📌 Autre'
];

// Récupérer le nombre de réponses pour chaque demande
require_once __DIR__ . '/../../MODEL/config.php';
$db = Config::getConnexion();
$stmtReponses = $db->query("SELECT id_demande, COUNT(*) as nb_reponses FROM reponse_demandes GROUP BY id_demande");
$reponsesCount = [];
while ($row = $stmtReponses->fetch()) {
    $reponsesCount[$row['id_demande']] = $row['nb_reponses'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Espace Administrateur • Mairie</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: var(--gray-100);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Inter', sans-serif;
            font-weight: 700;
            color: var(--dark);
        }

        /* ========== LOADER ========== */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
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
            border: 3px solid var(--gray-300);
            border-top-color: var(--primary);
            border-right-color: var(--secondary);
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
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
            cursor: pointer;
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
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 1.1rem;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .user-info h4 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: white;
            font-size: 0.95rem;
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
            border-radius: var(--radius-md);
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .nav-link i {
            width: 24px;
            text-align: center;
        }

        /* ========== MAIN ========== */
        .main {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            background: var(--gray-100);
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

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--dark);
        }

        .page-subtitle {
            color: var(--gray-500);
            margin-top: 0.25rem;
            font-size: 0.9rem;
        }

        /* ========== BOUTONS ========== */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: var(--radius-md);
            text-decoration: none;
            font-weight: 600;
            font-size: 15px;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
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

        .btn-sm {
            padding: 8px 16px;
            font-size: 12px;
        }

        /* ========== STATS GRID ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--white);
            padding: 1.5rem;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
            transition: var(--transition-base);
            cursor: pointer;
            border-bottom: 3px solid var(--primary);
        }

        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            line-height: 1;
        }

        .stat-label {
            color: var(--gray-700);
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        /* ========== DEMANDES SECTION ========== */
        .demandes-section {
            background: var(--white);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .section-header {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid var(--gray-300);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filters {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .filter-select, .search-input {
            padding: 0.6rem 1rem;
            border: 2px solid var(--gray-300);
            border-radius: var(--radius-md);
            font-size: 0.85rem;
            background: var(--white);
            transition: var(--transition-base);
            font-family: 'Inter', sans-serif;
        }

        .filter-select:focus, .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0,109,91,0.15);
        }

        .search-input {
            width: 250px;
        }

        /* ========== TABLE ========== */
        .table-wrapper {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead tr {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
        }

        .table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--gray-300);
        }

        .table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--gray-300);
            font-size: 0.9rem;
        }

        .table tbody tr:hover td {
            background: var(--primary-light);
        }

        /* ========== BADGES ========== */
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-en_attente { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traite { background: #D1FAE5; color: #059669; }
        .badge-refuse { background: #FEE2E2; color: #DC2626; }

        /* Badge pour le nombre de réponses */
        .badge-reponses {
            background: #E0E7FF;
            color: #3730A3;
            font-size: 10px;
            padding: 2px 8px;
            border-radius: 50px;
            font-weight: 700;
            margin-left: 6px;
        }

        /* ========== ACTIONS ========== */
        .action-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .action-btn {
            width: 34px;
            height: 34px;
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
            position: relative;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        .action-btn.reply-btn {
            color: #059669;
            background: #D1FAE5;
        }

        .action-btn.reply-btn:hover {
            background: #059669;
            color: white;
        }

        .action-btn.delete:hover {
            background: var(--danger);
        }

        /* ========== INFO FOOTER ========== */
        .info-footer {
            padding: 1rem 2rem;
            background: var(--gray-100);
            border-top: 2px solid var(--gray-300);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .info-stats {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.8rem;
        }

        .info-item .label {
            color: var(--gray-500);
        }

        .info-item .value {
            font-weight: 700;
            color: var(--primary);
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: var(--gray-300);
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }

        .empty-text {
            color: var(--gray-500);
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
        }

        /* ========== ALERTES ========== */
        .alert {
            padding: 16px 24px;
            border-radius: var(--radius-md);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 14px;
            border-left: 4px solid;
            font-weight: 500;
        }

        .alert-success {
            background: #D1FAE5;
            color: #059669;
            border-left-color: #059669;
        }

        .alert-danger {
            background: #FEE2E2;
            color: #DC2626;
            border-left-color: #DC2626;
        }

        /* ========== TOAST ========== */
        .toast-notify {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 50px;
            box-shadow: var(--shadow-lg);
            z-index: 2000;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
            font-size: 0.9rem;
            font-weight: 500;
            border: 2px solid var(--primary-dark);
        }

        .toast-notify.show {
            opacity: 1;
        }

        /* ========== ANIMATIONS ========== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: var(--gray-100);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-dark);
        }

        /* ========== RESPONSIVE ========== */
        @media (max-width: 1024px) {
            .sidebar {
                width: 240px;
            }
            .main {
                margin-left: 240px;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
                z-index: 1000;
            }
            .main {
                margin-left: 0;
            }
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            .page-title {
                font-size: 1.4rem;
            }
            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>

    <div class="loader" id="loader">
        <div class="spinner"></div>
    </div>

    <div id="toastMsg" class="toast-notify">✓ Action réussie</div>

    <div class="app">
        <aside class="sidebar">
            <div class="logo" onclick="window.location.href='index.php'">
                <div class="logo-icon">IG</div>
                <span class="logo-text">InnoGov</span>
            </div>

            <div class="user-profile">
                <div class="avatar"><?= $user_initials ?></div>
                <div class="user-info">
                    <h4>Administrateur</h4>
                </div>
            </div>

            <ul class="nav-menu">
                <li class="nav-item">
                    <a href="index.php" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i> Tableau de bord
                    </a>
                </li>
                <li class="nav-item">
                    <a href="liste_clients.php" class="nav-link">
                        <i class="fas fa-users"></i> Liste des clients
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../VIEW/backoffice/ajouter_demande.php" class="nav-link">
                        <i class="fas fa-plus-circle"></i> Ajouter une demande
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
            <div class="top-bar reveal">
                <div>
                    <h1 class="page-title">Tableau de bord administrateur</h1>
                    <p class="page-subtitle">Gérez et suivez l'ensemble des demandes municipales</p>
                </div>
                <a href="../../VIEW/backoffice/ajouter_demande.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Ajouter une demande
                </a>
            </div>

            <div class="stats-grid">
                <div class="stat-card reveal" data-type="total">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #D1FAE5; color: var(--primary);">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                    </div>
                    <div class="stat-value counter" data-target="<?= $stats['total'] ?? 0 ?>">0</div>
                    <div class="stat-label">Total Demandes</div>
                </div>
                <div class="stat-card reveal" data-type="en_attente">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #FEF3C7; color: #D97706;">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <div class="stat-value counter" data-target="<?= $stats['en_attente'] ?? 0 ?>">0</div>
                    <div class="stat-label">En Attente</div>
                </div>
                <div class="stat-card reveal" data-type="en_cours">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #DBEAFE; color: #2563EB;">
                            <i class="fas fa-spinner"></i>
                        </div>
                    </div>
                    <div class="stat-value counter" data-target="<?= $stats['en_cours'] ?? 0 ?>">0</div>
                    <div class="stat-label">En Cours</div>
                </div>
                <div class="stat-card reveal" data-type="traite">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #D1FAE5; color: var(--primary);">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-value counter" data-target="<?= $stats['traite'] ?? 0 ?>">0</div>
                    <div class="stat-label">Traitées</div>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="alert alert-success reveal">
                    <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="alert alert-danger reveal">
                    <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="demandes-section reveal">
                <div class="section-header">
                    <h2><i class="fas fa-list" style="color: var(--primary);"></i> Liste des demandes</h2>
                    <div class="filters">
                        <select id="filterStatus" class="filter-select">
                            <option value="">Tous les statuts</option>
                            <option value="en_attente">⏳ En attente</option>
                            <option value="en_cours">🔄 En cours</option>
                            <option value="traite">✅ Traité</option>
                            <option value="refuse">❌ Refusé</option>
                        </select>
                        <input type="text" id="searchInput" class="search-input" placeholder="🔍 Rechercher...">
                    </div>
                </div>

                <div class="table-wrapper">
                    <?php if (empty($demandes)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📭</div>
                            <h3 class="empty-title">Aucune demande trouvée</h3>
                            <p class="empty-text">Commencez par créer votre première demande municipale.</p>
                            <a href="../../VIEW/backoffice/ajouter_demande.php" class="btn btn-primary">Créer une demande</a>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>N° Demande</th>
                                    <th>Titre</th>
                                    <th>Service</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th style="text-align: center;">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="demandesTableBody">
                                <?php foreach ($demandes as $d): ?>
                                    <?php $nbReponses = $reponsesCount[$d['id_demande']] ?? 0; ?>
                                    <tr data-status="<?= $d['statut'] ?>" data-search="<?= strtolower($d['titre'] . ' ' . ($d['nom_service'] ?? '')) ?>">
                                        <td><strong>#<?= str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                        <td>
                                            <div style="font-weight: 600;">
                                                <?= htmlspecialchars($d['titre']) ?>
                                                <?php if ($nbReponses > 0): ?>
                                                    <span class="badge-reponses"><?= $nbReponses ?> réponse(s)</span>
                                                <?php endif; ?>
                                            </div>
                                            <div style="font-size: 0.75rem; color: var(--gray-500); margin-top: 4px;">
                                                <?= substr(htmlspecialchars($d['description']), 0, 50) ?>...
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($d['nom_service'] ?? 'Non assigné') ?></td>
                                        <td>
                                            <?php
                                            $badgeClass = match($d['statut']) {
                                                'traite' => 'badge-traite',
                                                'en_cours' => 'badge-en_cours',
                                                'refuse' => 'badge-refuse',
                                                default => 'badge-en_attente'
                                            };
                                            $statutText = match($d['statut']) {
                                                'traite' => '✅ Traité',
                                                'en_cours' => '🔄 En cours',
                                                'refuse' => '❌ Refusé',
                                                default => '⏳ En attente'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= $statutText ?></span>
                                        </td>
                                        <td>
                                            <div><?= $d['date_formatee'] ?></div>
                                            <div style="font-size: 0.75rem; color: var(--gray-500);"><?= $d['heure_formatee'] ?></div>
                                        </td>
                                        <td>
                                            <div class="action-group">
                                                <a href="suivi_demande.php?id=<?= $d['id_demande'] ?>" class="action-btn" title="Voir le suivi">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <a href="suivi_reponses.php?id_demande=<?= $d['id_demande'] ?>" class="action-btn reply-btn" title="Voir les réponses" style="position: relative;">
                                                    <i class="fas fa-reply"></i>
                                                    <?php if ($nbReponses > 0): ?>
                                                        <span style="position: absolute; top: -6px; right: -6px; background: #059669; color: white; border-radius: 50%; width: 18px; height: 18px; font-size: 10px; display: flex; align-items: center; justify-content: center; font-weight: 700;">
                                                            <?= $nbReponses ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </a>
                                                
                                                <a href="../backoffice/modifier_demande.php?id=<?= $d['id_demande'] ?>" class="action-btn" title="Modifier la demande">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <a href="../backoffice/supprimer_demande.php?id=<?= $d['id_demande'] ?>"
                                                   class="action-btn delete"
                                                   title="Supprimer la demande"
                                                   onclick="return confirm('Supprimer cette demande ?')">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="info-footer">
                    <div class="info-stats">
                        <div class="info-item">
                            <span class="label"><i class="fas fa-chart-line"></i> Total :</span>
                            <span class="value" id="footerTotal"><?= $stats['total'] ?? 0 ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-clock"></i> En attente :</span>
                            <span class="value" id="footerEnAttente"><?= $stats['en_attente'] ?? 0 ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-spinner"></i> En cours :</span>
                            <span class="value" id="footerEnCours"><?= $stats['en_cours'] ?? 0 ?></span>
                        </div>
                        <div class="info-item">
                            <span class="label"><i class="fas fa-check-circle"></i> Traitées :</span>
                            <span class="value" id="footerTraitees"><?= $stats['traite'] ?? 0 ?></span>
                        </div>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-sync-alt" style="color: var(--primary);"></i>
                        <span class="label">Dernière mise à jour :</span>
                        <span class="value"><?= date('d/m/Y H:i') ?></span>
                    </div>
                </div>
            </div>
        </main>
    </div>

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
            initCounters();
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

        function initCounters() {
            const counters = document.querySelectorAll('.counter');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        const target = parseInt(counter.getAttribute('data-target'));
                        if (target === 0) {
                            counter.innerText = '0';
                            return;
                        }
                        let current = 0;
                        const increment = target / 60;
                        const updateCounter = () => {
                            current += increment;
                            if (current < target) {
                                counter.innerText = Math.floor(current);
                                requestAnimationFrame(updateCounter);
                            } else {
                                counter.innerText = target;
                            }
                        };
                        updateCounter();
                        observer.unobserve(counter);
                    }
                });
            }, { threshold: 0.5 });
            counters.forEach(counter => observer.observe(counter));
        }

        const filterSelect = document.getElementById('filterStatus');
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('#demandesTableBody tr');

        function filterTable() {
            const statusValue = filterSelect?.value || '';
            const searchTerm = searchInput?.value.toLowerCase() || '';

            let visibleCount = 0;
            let enAttenteCount = 0;
            let enCoursCount = 0;
            let traiteesCount = 0;

            rows.forEach(row => {
                const status = row.dataset.status;
                const searchText = row.dataset.search || '';

                const matchesStatus = !statusValue || status === statusValue;
                const matchesSearch = !searchTerm || searchText.includes(searchTerm);

                const isVisible = matchesStatus && matchesSearch;
                row.style.display = isVisible ? '' : 'none';

                if (isVisible) {
                    visibleCount++;
                    if (status === 'en_attente') enAttenteCount++;
                    else if (status === 'en_cours') enCoursCount++;
                    else if (status === 'traite') traiteesCount++;
                }
            });

            document.getElementById('footerTotal').innerText = visibleCount;
            document.getElementById('footerEnAttente').innerText = enAttenteCount;
            document.getElementById('footerEnCours').innerText = enCoursCount;
            document.getElementById('footerTraitees').innerText = traiteesCount;
        }

        if (filterSelect) filterSelect.addEventListener('change', filterTable);
        if (searchInput) searchInput.addEventListener('input', filterTable);

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

        document.querySelectorAll('.stat-card').forEach((card, index) => {
            card.addEventListener('click', () => {
                const types = ['total', 'en_attente', 'en_cours', 'traite'];
                const type = types[index];
                if (filterSelect) {
                    if (type === 'total') filterSelect.value = '';
                    else if (type === 'en_attente') filterSelect.value = 'en_attente';
                    else if (type === 'en_cours') filterSelect.value = 'en_cours';
                    else if (type === 'traite') filterSelect.value = 'traite';
                    filterTable();
                    showToast(`Filtre appliqué : ${card.querySelector('.stat-label')?.innerText}`, false);
                }
            });
        });
    </script>
</body>
</html>