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
    <title>Tableau de Bord • Mes Demandes • Mairie</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --gray-50: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --gray-900: #111827;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--gray-100);
            min-height: 100vh;
            color: var(--gray-900);
        }
        
        .app { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar {
            width: 280px;
            background: white;
            border-right: 1px solid var(--gray-200);
            padding: 2rem 1.5rem;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
        }
        
        .logo-icon {
            width: 40px;
            height: 40px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }
        
        .logo-text { font-size: 1.25rem; font-weight: 700; }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 0;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 1.5rem;
        }
        
        .avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .user-info h4 { font-weight: 600; margin-bottom: 0.25rem; }
        .user-info p { font-size: 0.875rem; color: var(--gray-600); }
        
        .nav-menu { list-style: none; }
        .nav-item { margin-bottom: 0.5rem; }
        
        .nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1rem;
            color: var(--gray-600);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.2s;
            font-weight: 500;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--primary);
            color: white;
        }
        
        /* Main Content */
        .main {
            flex: 1;
            margin-left: 280px;
            padding: 2rem;
        }
        
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        
        .page-title { font-size: 1.875rem; font-weight: 700; }
        .page-subtitle { color: var(--gray-600); margin-top: 0.25rem; }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: white;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.75rem;
        }
        
        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
        }
        
        .stat-value { font-size: 2rem; font-weight: 700; margin-bottom: 0.25rem; }
        .stat-label { color: var(--gray-600); font-size: 0.875rem; }
        
        /* Alerts */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error { background: #fee2e2; color: #991b1b; }
        
        /* Filters */
        .filters-bar {
            background: white;
            padding: 1rem 1.5rem;
            border-radius: 16px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .filter-select {
            padding: 0.625rem 2rem 0.625rem 1rem;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.875rem;
            background: white;
        }
        
        .search-input {
            padding: 0.625rem 1rem;
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            font-size: 0.875rem;
            width: 250px;
            margin-left: auto;
        }
        
        /* Table */
        .table-container {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th {
            background: var(--gray-50);
            padding: 1rem 1.5rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: var(--gray-600);
            border-bottom: 1px solid var(--gray-200);
        }
        
        .table td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .table tr:hover { background: var(--gray-50); }
        
        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
        }
        
        .badge-success { background: #d1fae5; color: #065f46; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-info { background: #dbeafe; color: #1e40af; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        
        /* Action Buttons */
        .action-group { display: flex; gap: 0.5rem; }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-600);
            background: var(--gray-100);
            transition: all 0.2s;
        }
        
        .btn-icon:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .btn-icon.delete:hover { background: var(--danger); }
        
        .btn-primary {
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }
        
        .empty-icon { font-size: 4rem; margin-bottom: 1rem; }
        .empty-title { font-size: 1.25rem; font-weight: 600; margin-bottom: 0.5rem; }
        .empty-text { color: var(--gray-600); margin-bottom: 1.5rem; }
        
        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
    <div class="app">
        <aside class="sidebar">
            <div class="logo">
                <div class="logo-icon">🏛️</div>
                <span class="logo-text">MairieConnect</span>
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
                        <span>📋</span> Mes Demandes
                    </a>
                </li>
                <li class="nav-item">
                    <a href="../../VIEW/backoffice/ajouter_demande.php" class="nav-link">
                        <span>➕</span> Nouvelle Demande
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span>📅</span> Rendez-vous
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <span>👤</span> Mon Profil
                    </a>
                </li>
            </ul>
        </aside>
        
        <main class="main">
            <div class="top-bar">
                <div>
                    <h1 class="page-title">Mes Demandes</h1>
                    <p class="page-subtitle">Gérez et suivez toutes vos demandes municipales</p>
                </div>
                <a href="../../VIEW/backoffice/ajouter_demande.php" class="btn-primary">
                    <span>➕</span> Nouvelle Demande
                </a>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">📋</div>
                    </div>
                    <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                    <div class="stat-label">Total Demandes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #fef3c7; color: #92400e;">⏳</div>
                    </div>
                    <div class="stat-value"><?= $stats['en_attente'] ?? 0 ?></div>
                    <div class="stat-label">En Attente</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #dbeafe; color: #1e40af;">🔄</div>
                    </div>
                    <div class="stat-value"><?= $stats['en_cours'] ?? 0 ?></div>
                    <div class="stat-label">En Cours</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="background: #d1fae5; color: #065f46;">✅</div>
                    </div>
                    <div class="stat-value"><?= $stats['traite'] ?? 0 ?></div>
                    <div class="stat-label">Traitées</div>
                </div>
            </div>
            
            <!-- Messages -->
            <?php if ($message): ?>
            <div class="alert alert-success">
                <span>✅</span> <?= htmlspecialchars($message) ?>
            </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-error">
                <span>❌</span> <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>
            
            <!-- Filters -->
            <div class="filters-bar">
                <select id="filterStatus" class="filter-select">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente">⏳ En attente</option>
                    <option value="en_cours">🔄 En cours</option>
                    <option value="traite">✅ Traité</option>
                    <option value="refuse">❌ Refusé</option>
                </select>
                <input type="text" id="searchInput" class="search-input" placeholder="🔍 Rechercher...">
            </div>
            
            <!-- Table -->
            <div class="table-container">
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
                        <tbody>
                            <?php foreach ($demandes as $d): ?>
                            <tr data-status="<?= $d['statut'] ?>">
                                <td><strong>#<?= str_pad($d['id_demande'], 5, '0', STR_PAD_LEFT) ?></strong></td>
                                <td>
                                    <div style="font-weight: 500;"><?= htmlspecialchars($d['titre']) ?></div>
                                    <div style="font-size: 0.75rem; color: var(--gray-600); margin-top: 4px;">
                                        <?= substr(htmlspecialchars($d['description']), 0, 50) ?>...
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($d['nom_service'] ?? 'Non assigné') ?></td>
                                <td>
                                    <?php
                                    $badgeClass = match($d['statut']) {
                                        'traite' => 'badge-success',
                                        'en_cours' => 'badge-info',
                                        'refuse' => 'badge-danger',
                                        default => 'badge-warning'
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
                                    <div style="font-size: 0.75rem; color: var(--gray-600);"><?= $d['heure_formatee'] ?></div>
                                </td>
                                <td>
                                    <div class="action-group" style="justify-content: center;">
                                        <a href="suivi_demande.php?id=<?= $d['id_demande'] ?>" class="btn-icon" title="Voir le suivi">👁️</a>
                                        <a href="../backoffice/modifier_demande.php?id=<?= $d['id_demande'] ?>" class="btn-icon" title="Modifier">✏️</a>
                                        <a href="../backoffice/supprimer_demande.php?id=<?= $d['id_demande'] ?>" 
                                           class="btn-icon delete" 
                                           title="Supprimer"
                                           onclick="return confirm('Supprimer cette demande ?')">🗑️</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </main>
    </div>
    
    <script>
        const filterSelect = document.getElementById('filterStatus');
        const searchInput = document.getElementById('searchInput');
        const rows = document.querySelectorAll('tbody tr');
        
        function filterTable() {
            const statusFilter = filterSelect?.value || '';
            const searchTerm = searchInput?.value.toLowerCase() || '';
            
            rows.forEach(row => {
                const status = row.dataset.status;
                const title = row.querySelector('td:nth-child(2)')?.textContent.toLowerCase() || '';
                const service = row.querySelector('td:nth-child(3)')?.textContent.toLowerCase() || '';
                
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesSearch = !searchTerm || title.includes(searchTerm) || service.includes(searchTerm);
                
                row.style.display = (matchesStatus && matchesSearch) ? '' : 'none';
            });
        }
        
        if (filterSelect) filterSelect.addEventListener('change', filterTable);
        if (searchInput) searchInput.addEventListener('input', filterTable);
    </script>
</body>
</html>