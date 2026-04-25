<?php
require_once __DIR__ . "/../../../CONTROLLER/ReponseController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReponseController();
$reponses = $ctrl->getAllReponses();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Gestion des réponses</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; }
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #0D3328 0%, #0A281E 100%); color: white; z-index: 100; overflow-y: auto; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; font-family: 'Syne', sans-serif; }
        .logo-mini i { font-size: 28px; color: #006D5B; }
        .sidebar-nav { padding: 0 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; font-size: 14px; font-weight: 500; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar-link.active { background: #006D5B; color: white; }
        .admin-main { flex: 1; margin-left: 280px; }
        .admin-topbar { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; position: sticky; top: 0; z-index: 99; }
        .admin-content { padding: 24px; }
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .table-wrapper { overflow-x: auto; padding: 20px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 12px 16px; background: #F9FAFB; color: #6B7280; font-weight: 600; font-size: 13px; }
        .table td { padding: 12px 16px; border-bottom: 1px solid #F0F0F0; font-size: 14px; }
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-danger { background: #DC2626; color: white; }
        .badge { padding: 4px 10px; border-radius: 30px; font-size: 11px; font-weight: 600; }
        .badge-info { background: #DBEAFE; color: #1E40AF; }
        .badge-success { background: #D1FAE5; color: #065F46; }
    </style>
</head>
<body>

<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div></div>
        <nav class="sidebar-nav">
            <a href="../RECLAMATION/lister.php" class="sidebar-link"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-reply"></i><span>Réponses</span></a>
            <a href="../../../index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <div class="admin-topbar">
            <div class="user-info"><i class="fas fa-user-circle"></i><span>Admin Système</span></div>
        </div>
        
        <div class="admin-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-list"></i> Liste des réponses</h2>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Réf. Récl.</th>
                                    <th>Agent</th>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reponses as $r): ?>
                                <tr>
                                    <td>#<?= $r['id_reponse'] ?></td>
                                    <td><strong><?= htmlspecialchars($r['ref_reclamation']) ?></strong></td>
                                    <td><?= htmlspecialchars($r['nom_agent']) ?></td>
                                    <td><span class="badge badge-info"><?= ucfirst($r['type_reponse']) ?></span></td>
                                    <td><?= date('d/m/Y H:i', strtotime($r['date_reponse'])) ?></td>
                                    <td>
                                        <a href="modifier.php?id=<?= $r['id_reponse'] ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Modifier</a>
                                        <a href="supprimer.php?id=<?= $r['id_reponse'] ?>" class="btn btn-danger" onclick="return confirm('Supprimer cette réponse ?')"><i class="fas fa-trash"></i> Supprimer</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

</body>
</html>
