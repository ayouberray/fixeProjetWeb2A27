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
    <title>innoGov | Gestion des rendez-vous</title>
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
        .btn-add { background: #2E7D32; color: white; padding: 8px 20px; border-radius: 10px; text-decoration: none; font-weight: 600; transition: all 0.3s; }
        .btn-add:hover { background: #1b5e20; transform: translateY(-2px); }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e2e8f0; }
        th { color: #64748b; font-weight: 600; font-size: 0.85rem; }
        .badge { padding: 4px 10px; border-radius: 20px; font-size: 0.7rem; font-weight: 600; }
        .badge-confirme { background: #E8F5E9; color: #2E7D32; }
        .badge-attente { background: #FFF3E0; color: #E65100; }
        .badge-annule { background: #FFEBEE; color: #C62828; }
        .btn-edit { background: #2196F3; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; margin-right: 5px; display: inline-block; }
        .btn-delete { background: #dc2626; color: white; padding: 5px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; display: inline-block; }
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
                <h1><i class="fas fa-calendar-check"></i> Gestion des rendez-vous</h1>
            </div>
            <div class="admin-info">
                <span class="admin-badge"><?= ucfirst($_SESSION['user_role']) ?></span>
                <span><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
                <div class="admin-avatar"><?= strtoupper(substr($_SESSION['user_nom'], 0, 1)) ?></div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3>📅 Liste des rendez-vous</h3>
                <?php if($isAdmin): ?>
                    <a href="#" class="btn-add"><i class="fas fa-plus"></i> Ajouter un rendez-vous</a>
                <?php endif; ?>
            </div>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr><th>Citoyen</th><th>Service</th><th>Date</th><th>Heure</th><th>Statut</th><?php if($isAdmin): ?><th>Actions</th><?php endif; ?></tr>
                    </thead>
                    <tbody>
                        <tr><td>Ahmed Ben Ali</td><td>Passeport</td><td>20/04/2026</td><td>10:00</td><td><span class="badge badge-confirme">Confirmé</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete" onclick="return confirm('Annuler ce rendez-vous ?')">Annuler</a></td><?php endif; ?></tr>
                        <tr><td>Sarra Mansouri</td><td>CIN</td><td>22/04/2026</td><td>14:30</td><td><span class="badge badge-attente">En attente</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete" onclick="return confirm('Annuler ce rendez-vous ?')">Annuler</a></td><?php endif; ?></tr>
                        <tr><td>Mohamed Karray</td><td>Permis de conduire</td><td>25/04/2026</td><td>09:00</td><td><span class="badge badge-attente">En attente</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete" onclick="return confirm('Annuler ce rendez-vous ?')">Annuler</a></td><?php endif; ?></tr>
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