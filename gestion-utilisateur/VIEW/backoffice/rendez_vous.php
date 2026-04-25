<?php
session_start();
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}
$isAdmin = ($_SESSION['user_role'] === 'admin');
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>innoGov | Rendez-vous</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }

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

        .main-content { margin-left: 280px; padding: 20px; transition: all 0.3s; }
        .top-bar { background: white; border-radius: 20px; padding: 15px 25px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; }
        .card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 25px; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; }
        .btn-add { background: #2E7D32; color: white; padding: 8px 20px; border-radius: 10px; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        .badge-confirme { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; background: #E8F5E9; color: #2E7D32; }
        .badge-attente { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; background: #FFF3E0; color: #E65100; }
        .btn-edit { background: #2196F3; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; margin-right: 5px; display: inline-block; }
        .btn-delete { background: #dc2626; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; display: inline-block; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } }
    </style>
</head>
<body>

<?php include 'includes/sidebar.php'; ?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-calendar-check"></i> Gestion des rendez-vous</h1>
        <div><?= htmlspecialchars($_SESSION['user_nom']) ?></div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>📅 Liste des rendez-vous</h3>
            <?php if($isAdmin): ?><a href="#" class="btn-add"><i class="fas fa-plus"></i> Ajouter un rendez-vous</a><?php endif; ?>
        </div>
        <div style="overflow-x: auto;">
            <table><thead><tr><th>Citoyen</th><th>Service</th><th>Date</th><th>Heure</th><th>Statut</th><?php if($isAdmin): ?><th>Actions</th><?php endif; ?></tr></thead>
            <tbody>
                <tr><td>Ahmed Ben Ali</td><td>Passeport</td><td>20/04/2026</td><td>10:00</td><td><span class="badge-confirme">Confirmé</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Annuler</a></td><?php endif; ?></tr>
                <tr><td>Sarra Mansouri</td><td>CIN</td><td>22/04/2026</td><td>14:30</td><td><span class="badge-attente">En attente</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Annuler</a></td><?php endif; ?></tr>
                <tr><td>Mohamed Karray</td><td>Permis conduire</td><td>25/04/2026</td><td>09:00</td><td><span class="badge-attente">En attente</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Annuler</a></td><?php endif; ?></tr>
            </tbody></table>
        </div>
    </div>
</div>
</body>
</html>