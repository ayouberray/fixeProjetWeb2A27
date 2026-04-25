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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Gestion des réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #f0f2f5; }
        .admin-container { display: flex; min-height: 100vh; }
        .main-content { margin-left: 280px; flex: 1; padding: 20px; transition: all 0.3s; }
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
        .page-title h1 { font-size: 1.5rem; font-weight: 700; background: linear-gradient(135deg, #2E7D32, #4CAF50); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .admin-info { display: flex; align-items: center; gap: 15px; }
        .admin-badge { background: linear-gradient(135deg, #2E7D32, #4CAF50); padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; color: white; }
        .admin-avatar { width: 45px; height: 45px; background: linear-gradient(135deg, #2E7D32, #4CAF50); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem; color: white; box-shadow: 0 4px 15px rgba(46,125,50,0.3); }
        .card { background: white; border-radius: 20px; padding: 20px; margin-bottom: 25px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid rgba(46,125,50,0.1); }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #e2e8f0; }
        .filter-bar { display: flex; gap: 15px; margin-bottom: 20px; flex-wrap: wrap; }
        .filter-select { padding: 8px 15px; border: 1px solid #e2e8f0; border-radius: 10px; font-family: 'Inter', sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { color: #64748b; font-weight: 600; font-size: 0.85rem; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .badge-nouveau { background: #E3F2FD; color: #1976D2; }
        .badge-cours { background: #FFF3E0; color: #E65100; }
        .badge-resolu { background: #E8F5E9; color: #2E7D32; }
        .btn-edit { background: #2196F3; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; margin-right: 5px; display: inline-block; }
        .btn-view { background: #6c757d; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; display: inline-block; }
        .priority-high { color: #dc2626; font-weight: 600; }
        .priority-medium { color: #e65100; font-weight: 600; }
        .priority-low { color: #16a34a; font-weight: 600; }
        @media (max-width: 768px) { .sidebar { transform: translateX(-100%); position: fixed; } .sidebar.open { transform: translateX(0); } .main-content { margin-left: 0; } .menu-toggle { display: block; } }
        .menu-toggle { display: none; background: #2E7D32; color: white; border: none; padding: 10px 15px; border-radius: 10px; cursor: pointer; }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="main-content">
        <div class="top-bar">
            <button class="menu-toggle" id="menuToggle"><i class="fas fa-bars"></i> Menu</button>
            <div class="page-title">
                <h1><i class="fas fa-comment-dots"></i> Gestion des réclamations</h1>
            </div>
            <div class="admin-info">
                <span class="admin-badge"><?= ucfirst($_SESSION['user_role']) ?></span>
                <span><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['user_nom'], 0, 1)) ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>⚠️ Liste des réclamations</h3>
            </div>
            <div class="filter-bar">
                <select class="filter-select">
                    <option>Tous les statuts</option>
                    <option>Nouveau</option>
                    <option>En cours</option>
                    <option>Résolu</option>
                </select>
                <select class="filter-select">
                    <option>Toutes les priorités</option>
                    <option>Haute</option>
                    <option>Moyenne</option>
                    <option>Basse</option>
                </select>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr><th>N°</th><th>Citoyen</th><th>Sujet</th><th>Priorité</th><th>Date</th><th>Statut</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>#REC-001</td>
                            <td>Leila Hamdi</td>
                            <td>Délai trop long</td>
                            <td><span class="priority-high">⚠️ Haute</span></td>
                            <td>05/04/2026</td>
                            <td><span class="badge badge-cours">En cours</span></td>
                            <td><a href="#" class="btn-edit">Traiter</a><a href="#" class="btn-view">Voir</a></td>
                         </tr>
                        <tr>
                            <td>#REC-002</td>
                            <td>Karim Sassi</td>
                            <td>Erreur dans document</td>
                            <td><span class="priority-medium">🟠 Moyenne</span></td>
                            <td>08/04/2026</td>
                            <td><span class="badge badge-resolu">Résolu</span></td>
                            <td><a href="#" class="btn-view">Voir</a></td>
                         </tr>
                        <tr>
                            <td>#REC-003</td>
                            <td>Nadia Riahi</td>
                            <td>Problème technique</td>
                            <td><span class="priority-high">⚠️ Haute</span></td>
                            <td>10/04/2026</td>
                            <td><span class="badge badge-nouveau">Nouveau</span></td>
                            <td><a href="#" class="btn-edit">Traiter</a><a href="#" class="btn-view">Voir</a></td>
                         </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('menuToggle')?.addEventListener('click', function() {
        document.getElementById('sidebar').classList.toggle('open');
    });
</script>

</body>
</html>