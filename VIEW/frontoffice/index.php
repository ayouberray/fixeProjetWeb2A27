<?php
// VIEW/frontoffice/index.php
// Page d'accueil - Liste des demandes du citoyen

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';
require_once __DIR__ . '/../../MODEL/Demande.php';
require_once __DIR__ . '/../../MODEL/SuiviDemande.php';

$controller = new DemandeController();
$data = $controller->index();

$demandes = $data['demandes'] ?? [];
$stats = $data['stats'] ?? ['total' => 0, 'en_attente' => 0, 'en_cours' => 0, 'traite' => 0, 'refuse' => 0];

$user_id = $_SESSION['user_id'] ?? 2;
$user_nom = $_SESSION['user_nom'] ?? 'Ben Ali';
$user_prenom = $_SESSION['user_prenom'] ?? 'Mohamed';
$user_initials = strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1));

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov • Mes Demandes • Mairie</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ============================================
           INNOGOV - DESIGN FINAL (VERT #006D5B)
           ============================================ */

        :root {
            /* Couleurs principales */
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            /* Backgrounds */
            --bg-main: #F5FCF9;
            --bg-secondary: #EBF7F3;
            --bg-card: rgba(255, 255, 255, 0.92);
            --bg-card-dashboard: rgba(255, 255, 255, 0.95);
            /* Textes */
            --text-title: #1A2E2A;
            --text-body: #2C5A4F;
            --text-secondary: #5C8B7E;
            --text-muted: #8FB3A8;
            /* Bordures */
            --border-subtle: rgba(0, 109, 91, 0.12);
            --border-normal: rgba(0, 109, 91, 0.2);
            --border-strong: rgba(0, 109, 91, 0.35);
            /* Ombres (avec teinte verte) */
            --shadow-card: 0 24px 48px rgba(0, 77, 61, 0.12);
            --shadow-card-hover: 0 32px 64px rgba(0, 77, 61, 0.16);
            --shadow-btn: 0 4px 16px rgba(0, 109, 91, 0.25);
            --shadow-btn-hover: 0 8px 24px rgba(0, 109, 91, 0.35);
            /* Radius */
            --radius-card: 20px;
            --radius-btn: 10px;
            --radius-badge: 100px;
            /* Transitions */
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
            overflow-x: hidden;
        }

        h1, h2, h3, h4 {
            font-family: 'Syne', sans-serif;
            font-weight: 700;
            color: var(--text-title);
        }

        /* ========== LOADER ========== */
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

        /* ========== SIDEBAR VERTE ========== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #006D5B 0%, #004D3D 100%);
            position: fixed;
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

        /* ========== MAIN CONTENT ========== */
        .main {
            margin-left: 280px;
            padding: 2rem;
            min-height: 100vh;
            background: var(--bg-main);
        }

        /* ========== TOP BAR ========== */
        .top-bar {
            background: var(--bg-card-dashboard);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-card);
            padding: 1.5rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
        }

        .page-title {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--text-title);
        }

        .page-subtitle {
            color: var(--text-secondary);
            margin-top: 0.25rem;
            font-size: 0.9rem;
        }

        /* ========== BOUTONS ========== */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: var(--radius-btn);
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
            box-shadow: var(--shadow-btn);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-btn-hover);
        }

        /* ========== STATS CARDS ========== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--bg-card);
            backdrop-filter: blur(10px);
            padding: 1.5rem;
            border-radius: var(--radius-card);
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
            transition: var(--transition-base);
            cursor: pointer;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-card-hover);
            border-color: var(--border-strong);
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
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: var(--primary);
            font-family: 'Syne', sans-serif;
            line-height: 1;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        /* ========== SECTION DEMANDES ========== */
        .demandes-section {
            background: var(--bg-card-dashboard);
            backdrop-filter: blur(10px);
            border-radius: var(--radius-card);
            overflow: hidden;
            border: 1px solid var(--border-subtle);
            box-shadow: var(--shadow-card);
        }

        .section-header {
            padding: 1.5rem 2rem;
            border-bottom: 1px solid var(--border-subtle);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .section-header h2 {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--text-title);
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
            border: 1px solid var(--border-normal);
            border-radius: var(--radius-btn);
            font-size: 0.85rem;
            background: white;
            transition: var(--transition-base);
        }

        .filter-select:focus, .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.1);
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

        .table th {
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border-subtle);
        }

        .table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid var(--border-subtle);
            font-size: 0.9rem;
        }

        .table tr:hover td {
            background: var(--primary-light);
        }

        /* ========== BADGES ========== */
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-badge);
            font-size: 0.7rem;
            font-weight: 600;
        }

        .status-en_attente {
            background: #FEF3C7;
            color: #D97706;
        }

        .status-en_cours {
            background: #DBEAFE;
            color: #2563EB;
        }

        .status-traite {
            background: #D1FAE5;
            color: var(--primary);
        }

        .status-refuse {
            background: #FEE2E2;
            color: #DC2626;
        }

        /* ========== ACTION BUTTONS ========== */
        .action-group {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--text-secondary);
            background: var(--bg-secondary);
            transition: var(--transition-base);
            border: none;
            cursor: pointer;
        }

        .action-btn:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-3px);
        }

        .action-btn.delete:hover {
            background: #DC2626;
        }

        /* ========== INFO FOOTER ========== */
        .info-footer {
            padding: 1rem 2rem;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-subtle);
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
            color: var(--text-secondary);
        }

        .info-item .value {
            font-weight: 700;
            color: var(--primary);
        }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .empty-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .empty-text {
            color: var(--text-secondary);
            font-size: 0.85rem;
            margin-bottom: 1.5rem;
        }

        /* ========== ALERTS ========== */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: var(--radius-btn);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: #D1FAE5;
            color: var(--primary);
            border-left: 4px solid var(--primary);
        }

        .alert-error {
            background: #FEE2E2;
            color: #DC2626;
            border-left: 4px solid #DC2626;
        }

        /* ========== TOAST ========== */
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

        /* ========== SCROLLBAR ========== */
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

        /* ========== ANIMATIONS ========== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        }
    </style>
</head>
<body>

<!-- LOADER -->
<div class="loader" id="loader">
    <div class="spinner"></div>
</div>

<!-- TOAST -->
<div id="toastMsg" class="toast-notify">✓ Action réussie</div>

<div class="app">
    <!-- SIDEBAR VERTE -->
    <aside class="sidebar">
        <div class="logo" onclick="window.location.href='index.php'">
            <div class="logo-icon">IG</div>
            <span class="logo-text">InnoGov</span>
        </div>

        <div class="user-profile">
            <div class="avatar"><?= $user_initials ?></div>
            <div class="user-info">
                <h4><?= htmlspecialchars($user_prenom . ' ' . $user_nom) ?></h4>
                <p>Citoyen</p>
            </div>
        </div>

        <ul class="nav-menu">
            <li class="nav-item">
                <a href="index.php" class="nav-link active">
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

    <!-- MAIN CONTENT -->
    <main class="main">
        <div class="top-bar reveal">
            <div>
                <h1 class="page-title">Mes Demandes</h1>
                <p class="page-subtitle">Gérez et suivez toutes vos demandes municipales</p>
            </div>
            <a href="../../VIEW/backoffice/ajouter_demande.php" class="btn-primary">
                <i class="fas fa-plus"></i> Nouvelle Demande
            </a>
        </div>

        <!-- STATS CARDS -->
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

        <!-- MESSAGES -->
        <?php if ($message): ?>
            <div class="alert alert-success reveal">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error reveal">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- LISTE DES DEMANDES -->
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
                        <a href="../../VIEW/backoffice/ajouter_demande.php" class="btn-primary">Créer une demande</a>
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
                                <tr data-status="<?= $d['statut'] ?>" data-search="<?= strtolower($d['titre'] . ' ' . ($d['nom_service'] ?? '')) ?>">
                                    <td><strong>#<?= str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                    <td>
                                        <div style="font-weight: 500;"><?= htmlspecialchars($d['titre']) ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary); margin-top: 4px;">
                                            <?= substr(htmlspecialchars($d['description']), 0, 50) ?>...
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($d['nom_service'] ?? 'Non assigné') ?></td>
                                    <td>
                                        <?php
                                        $badgeClass = match($d['statut']) {
                                            'traite' => 'status-traite',
                                            'en_cours' => 'status-en_cours',
                                            'refuse' => 'status-refuse',
                                            default => 'status-en_attente'
                                        };
                                        $statutText = match($d['statut']) {
                                            'traite' => '✅ Traité',
                                            'en_cours' => '🔄 En cours',
                                            'refuse' => '❌ Refusé',
                                            default => '⏳ En attente'
                                        };
                                        ?>
                                        <span class="status-badge <?= $badgeClass ?>"><?= $statutText ?></span>
                                    </td>
                                    <td>
                                        <div><?= $d['date_formatee'] ?></div>
                                        <div style="font-size: 0.7rem; color: var(--text-secondary);"><?= $d['heure_formatee'] ?></div>
                                    </td>
                                    <td>
                                        <div class="action-group">
                                            <a href="suivi_demande.php?id=<?= $d['id_demande'] ?>" class="action-btn" title="Voir"><i class="fas fa-eye"></i></a>
                                            <a href="../backoffice/modifier_demande.php?id=<?= $d['id_demande'] ?>" class="action-btn" title="Modifier"><i class="fas fa-edit"></i></a>
                                            <a href="../backoffice/supprimer_demande.php?id=<?= $d['id_demande'] ?>"
                                               class="action-btn delete"
                                               title="Supprimer"
                                               onclick="return confirm('Supprimer cette demande ?')"><i class="fas fa-trash-alt"></i></a>
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
                        <span class="label"><i class="fas fa-chart-line"></i> Total demandes :</span>
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
    // ===== CACHER LE LOADER =====
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

    // ===== SCROLL REVEAL =====
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

    // ===== COMPTEURS ANIMÉS =====
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

    // ===== FILTRES =====
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

    // ===== TOAST =====
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

    // ===== CARTES STATS CLICK =====
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
