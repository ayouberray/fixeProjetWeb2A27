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
        
        /* SLIDESHOW BACKGROUND */
        .hero { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .hero-slideshow { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .hero-slideshow .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease; }
        .hero-slideshow .slide.active { opacity: 1; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,109,91,0.85) 0%, rgba(0,77,61,0.95) 100%); z-index: 1; }
        
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: rgba(13, 51, 40, 0.95); backdrop-filter: blur(10px); color: white; z-index: 100; overflow-y: auto; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; }
        .logo-mini i { font-size: 28px; color: #006D5B; }
        .sidebar-nav { padding: 20px 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; font-size: 14px; font-weight: 500; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-link.active { background: #006D5B; color: white; }
        
        .admin-main { margin-left: 280px; padding: 30px; position: relative; z-index: 2; min-height: 100vh; }
        .admin-topbar { background: white; padding: 15px 25px; border-radius: 15px; margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 8px 16px; background: #F3F4F6; border-radius: 30px; }
        
        .card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 24px 48px rgba(0,0,0,0.1); }
        .card-header { padding: 20px 25px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
        .card-title { font-size: 20px; font-weight: 700; display: flex; align-items: center; gap: 10px; color: #1A2E2A; }
        .card-body { padding: 20px 25px; }
        
        .table-wrapper { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 12px 15px; background: #F9FAFB; color: #6B7280; font-weight: 600; font-size: 13px; }
        .table td { padding: 12px 15px; border-bottom: 1px solid #F0F0F0; font-size: 14px; }
        .table tr:hover td { background: #F9FAFB; }
        
        .badge { padding: 4px 10px; border-radius: 30px; font-size: 11px; font-weight: 600; display: inline-block; }
        .badge-info { background: #DBEAFE; color: #1E40AF; }
        .badge-success { background: #D1FAE5; color: #065F46; }
        .badge-warning { background: #FEF3C7; color: #92400E; }
        .badge-danger { background: #FEE2E2; color: #991B1B; }
        
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-primary:hover { background: #004D3D; }
        .btn-warning { background: #F59E0B; color: white; }
        .btn-danger { background: #DC2626; color: white; }
        
        @media (max-width: 768px) { .admin-sidebar { transform: translateX(-100%); } .admin-main { margin-left: 0; } .admin-sidebar.open { transform: translateX(0); } }
    </style>
</head>
<body>

<!-- SLIDESHOW BACKGROUND -->
<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden;">
    <div class="hero-slideshow" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
        <div class="slide active" style="background-image: url('../../../ASSETS/images/tunisia1.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 1; transition: opacity 1.5s ease;"></div>
        <div class="slide" style="background-image: url('../../../ASSETS/images/tunisia2.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease;"></div>
        <div class="slide" style="background-image: url('../../../ASSETS/images/tunisia3.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease;"></div>
        <div class="slide" style="background-image: url('../../../ASSETS/images/tunisia4.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease;"></div>
    </div>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,109,91,0.85) 0%, rgba(0,77,61,0.95) 100%); z-index: 1;"></div>
</div>

<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar" id="sidebar">
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
                                <th>Réf. Réclamation</th>
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
                                <td><?= htmlspecialchars($r['nom_agent']) ?><td>
                                <td><span class="badge badge-<?= $r['type_reponse'] == 'information' ? 'info' : ($r['type_reponse'] == 'resolution' ? 'success' : ($r['type_reponse'] == 'rejet' ? 'danger' : 'warning')) ?>">
                                    <?= ucfirst($r['type_reponse']) ?>
                                </span></td>
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
    </main>
</div>

<script src="../../../ASSETS/JS/script.js"></script>
<script>
    const sidebar = document.getElementById('sidebar');
    document.addEventListener('click', function(e) {
        if(e.target.closest('.menu-toggle-btn')) {
            sidebar.classList.toggle('open');
        }
    });
</script>
<script>
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.opacity = i === index ? '1' : '0';
            });
        }
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 3000);
    }
</script>
</body>
</html>