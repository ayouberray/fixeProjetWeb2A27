<?php
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReclamationController();
$id = $_GET['id'] ?? 0;

if($id){
    $reclamation = $ctrl->getReclamationById($id);
    if(!$reclamation){
        header("Location: lister.php");
        exit();
    }
    
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm'])){
        $ctrl->adminSupprimerReclamation($id);
        header("Location: lister.php?deleted=1");
        exit();
    }
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Confirmer suppression</title>
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
            .admin-content { padding: 24px; display: flex; justify-content: center; align-items: center; min-height: calc(100vh - 80px); }
            .delete-card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); max-width: 500px; width: 100%; text-align: center; }
            .delete-header { padding: 24px; border-bottom: 1px solid #E5E7EB; }
            .delete-icon { width: 70px; height: 70px; background: #FEE2E2; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: #DC2626; font-size: 32px; }
            .delete-title { font-size: 20px; font-weight: 700; color: #1A2E2A; margin-bottom: 8px; }
            .delete-body { padding: 24px; border-bottom: 1px solid #E5E7EB; text-align: left; background: #F9FAFB; }
            .info-row { display: flex; padding: 8px 0; }
            .info-label { width: 100px; font-weight: 600; color: #374151; }
            .info-value { flex: 1; color: #1A2E2A; }
            .delete-footer { padding: 20px 24px; display: flex; gap: 12px; justify-content: center; }
            .btn { display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px; border-radius: 8px; font-size: 14px; font-weight: 500; text-decoration: none; border: none; cursor: pointer; }
            .btn-danger { background: #DC2626; color: white; }
            .btn-secondary { background: #F3F4F6; color: #374151; }
        </style>
    </head>
    <body>
    <div style="display: flex; min-height: 100vh;">
        <aside class="admin-sidebar">
            <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div></div>
            <nav class="sidebar-nav">
                <a href="lister.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
                <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
                <a href="../../../index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-topbar">
                <div class="user-info"><i class="fas fa-user-circle"></i><span>Admin Système</span></div>
            </div>
            
            <div class="admin-content">
                <div class="delete-card">
                    <div class="delete-header">
                        <div class="delete-icon"><i class="fas fa-trash-alt"></i></div>
                        <h2 class="delete-title">Confirmer la suppression</h2>
                        <p class="delete-subtitle">Cette action est irréversible</p>
                    </div>
                    <div class="delete-body">
                        <div class="info-row"><div class="info-label">Référence</div><div class="info-value"><?= htmlspecialchars($reclamation['reference']) ?></div></div>
                        <div class="info-row"><div class="info-label">Citoyen</div><div class="info-value"><?= htmlspecialchars($reclamation['citoyen']) ?></div></div>
                        <div class="info-row"><div class="info-label">Objet</div><div class="info-value"><?= htmlspecialchars(substr($reclamation['objet'], 0, 50)) ?>...</div></div>
                    </div>
                    <div class="delete-footer">
                        <form method="POST"><input type="hidden" name="confirm" value="yes"><button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Oui, supprimer</button></form>
                        <a href="lister.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    </div>
                </div>
            </div>
        </main>
    </div>
    </body>
    </html>
    <?php
    exit();
} else {
    header("Location: lister.php");
}
?>
