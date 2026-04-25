<?php
session_start();

// Vérifier si l'utilisateur est connecté (admin ou agent)
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$user_role = $_SESSION['user_role'];
$isAdmin = ($user_role === 'admin');
$isAgent = ($user_role === 'agent');

require_once '../../MODEL/Utilisateur.php';
$utilisateur = new Utilisateur();
$currentUser = $utilisateur->getById($_SESSION['user_id']);

// Récupérer les données
$users = $utilisateur->getAll();
$totalUsers = $utilisateur->countAll();
$totalCitoyens = $utilisateur->countByRole('user');
$totalAdmins = $utilisateur->countByRole('admin');
$totalAgents = $utilisateur->countByRole('agent');

// Données pour les graphiques (exemple)
$months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'];
$userData = [45, 52, 68, 85, 102, 128, 156, 189, 210, 245, 278, 312];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Dashboard Professionnel</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #e8f5e9 100%);
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0d3320 0%, #1a1a1a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }

        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-logo {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .sidebar-logo span {
            color: white;
            background: none;
            -webkit-background-clip: unset;
        }

        .sidebar-subtitle {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: 5px;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            margin: 4px 12px;
            border-radius: 12px;
        }

        .sidebar-nav-item:hover {
            background: rgba(46, 125, 50, 0.3);
            color: white;
        }

        .sidebar-nav-item.active {
            background: linear-gradient(135deg, #2E7D32, #1b5e20);
            color: white;
            box-shadow: 0 4px 15px rgba(46,125,50,0.3);
        }

        .sidebar-nav-item i {
            width: 24px;
            font-size: 1.1rem;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: 280px;
            padding: 20px;
            transition: all 0.3s;
        }

        /* ===== TOP BAR ===== */
        .top-bar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 15px 25px;
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(46,125,50,0.1);
        }

        .page-title h1 {
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .admin-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-badge {
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            color: white;
        }

        .admin-avatar {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            color: white;
            box-shadow: 0 4px 15px rgba(46,125,50,0.3);
        }

        /* ===== STATS CARDS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            transition: all 0.3s;
            border: 1px solid rgba(46,125,50,0.1);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, #2E7D32, #4CAF50);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(46,125,50,0.15);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-title {
            color: #64748b;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            background: rgba(46,125,50,0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: #2E7D32;
        }

        .stat-value {
            font-size: 2.2rem;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 5px;
        }

        .stat-change {
            font-size: 0.75rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .stat-change.positive { color: #22c55e; }
        .stat-change.negative { color: #ef4444; }

        /* ===== CHARTS SECTION ===== */
        .charts-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .chart-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(46,125,50,0.1);
            transition: all 0.3s;
        }

        .chart-card:hover {
            box-shadow: 0 10px 30px rgba(46,125,50,0.1);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .chart-header h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .chart-header select {
            padding: 5px 10px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 0.8rem;
            background: white;
        }

        canvas {
            max-height: 300px;
            width: 100%;
        }

        /* ===== RECENT USERS TABLE ===== */
        .recent-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(46,125,50,0.1);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .card-header h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
        }

        .table-container {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            color: #64748b;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #2E7D32, #4CAF50);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            color: white;
        }

        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .badge-admin { background: #E3F2FD; color: #1976D2; }
        .badge-agent { background: #FFF3E0; color: #E65100; }
        .badge-user { background: #E8F5E9; color: #2E7D32; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1024px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .charts-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                position: fixed;
            }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: 1fr; }
            .menu-toggle { display: block; }
        }

        .menu-toggle {
            display: none;
            background: #2E7D32;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 10px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i> Menu</button>
        <div class="page-title">
            <h1>Tableau de bord</h1>
        </div>
        <div class="admin-info">
            <span class="admin-badge"><?= ucfirst($user_role) ?></span>
            <span><?= htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></span>
            <div class="admin-avatar"><?= strtoupper(substr($currentUser['prenom'], 0, 1)) ?></div>
        </div>
    </div>

    <!-- STATISTIQUES -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Utilisateurs</span>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <span>+12% ce mois</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Citoyens</span>
                <div class="stat-icon"><i class="fas fa-user"></i></div>
            </div>
            <div class="stat-value"><?= $totalCitoyens ?></div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <span>+8% ce mois</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Agents</span>
                <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
            </div>
            <div class="stat-value"><?= $totalAgents ?></div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <span>+5% ce mois</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Administrateurs</span>
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            </div>
            <div class="stat-value"><?= $totalAdmins ?></div>
            <div class="stat-change neutral">
                <i class="fas fa-minus"></i> <span>Stable</span>
            </div>
        </div>
    </div>

    <!-- GRAPHIQUES -->
    <div class="charts-grid">
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-line" style="color: #2E7D32;"></i> Évolution des utilisateurs</h3>
                <select>
                    <option>Cette année</option>
                    <option>Ce mois</option>
                    <option>Cette semaine</option>
                </select>
            </div>
            <canvas id="userChart"></canvas>
        </div>
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie" style="color: #2E7D32;"></i> Répartition par rôle</h3>
            </div>
            <canvas id="roleChart"></canvas>
        </div>
    </div>

    <!-- DERNIERS UTILISATEURS -->
    <div class="recent-card">
        <div class="card-header">
            <h3><i class="fas fa-clock"></i> Derniers utilisateurs inscrits</h3>
            <?php if($isAdmin): ?>
                <a href="liste_utilisateurs.php" style="color: #2E7D32; text-decoration: none;">Voir tout →</a>
            <?php endif; ?>
        </div>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>CIN</th>
                        <th>Rôle</th>
                        <th>Date</th>
                        <?php if($isAdmin): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach(array_slice($users, 0, 5) as $user): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <div class="user-avatar"><?= strtoupper(substr($user['prenom'], 0, 1)) ?></div>
                                <span><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><?= $user['cin'] ?></td>
                        <td>
                            <?php
                            if($user['role'] == 'admin') echo '<span class="badge badge-admin">Admin</span>';
                            elseif($user['role'] == 'agent') echo '<span class="badge badge-agent">Agent</span>';
                            else echo '<span class="badge badge-user">Citoyen</span>';
                            ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                        <?php if($isAdmin): ?>
                        <td>
                            <a href="modifier_utilisateur.php?id=<?= $user['id'] ?>" style="color: #2196F3; margin-right: 10px;"><i class="fas fa-edit"></i></a>
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <a href="../../CONTROLLER/UtilisateurController.php?action=delete&id=<?= $user['id'] ?>" style="color: #dc2626;" onclick="return confirm('Supprimer ?')"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // MENU TOGGLE
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
    });

    // GRAPHIQUE LIGNE
    const ctx = document.getElementById('userChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($months) ?>,
            datasets: [{
                label: 'Nouveaux utilisateurs',
                data: <?= json_encode($userData) ?>,
                borderColor: '#2E7D32',
                backgroundColor: 'rgba(46, 125, 50, 0.05)',
                borderWidth: 3,
                pointBackgroundColor: '#4CAF50',
                pointBorderColor: '#fff',
                pointRadius: 5,
                pointHoverRadius: 7,
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e2e8f0'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // GRAPHIQUE CAMEMBERT
    const ctxPie = document.getElementById('roleChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: ['Citoyens', 'Agents', 'Administrateurs'],
            datasets: [{
                data: [<?= $totalCitoyens ?>, <?= $totalAgents ?>, <?= $totalAdmins ?>],
                backgroundColor: ['#2E7D32', '#4CAF50', '#81C784'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            },
            cutout: '60%'
        }
    });
</script>

</body>
</html>