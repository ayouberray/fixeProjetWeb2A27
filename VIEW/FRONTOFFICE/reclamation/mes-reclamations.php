<?php
session_start();
if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
    $_SESSION['user_type'] = 'citoyen';
}

require_once __DIR__ . "/../../../MODEL/config.php";
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";

$recController = new ReclamationController();
$list = $recController->getReclamationByCitoyen($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5FCF9; }
        .navbar { background: rgba(245, 252, 249, 0.85); backdrop-filter: blur(16px); position: fixed; top: 0; width: 100%; z-index: 1000; padding: 1rem 2rem; }
        .navbar-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 45px; height: 45px; background: linear-gradient(135deg, #006D5B, #004D3D); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; }
        .logo-text h1 { font-size: 22px; font-weight: 800; color: #006D5B; }
        .nav-menu { display: flex; gap: 2rem; align-items: center; }
        .nav-link { text-decoration: none; color: #2C5A4F; font-weight: 500; }
        .hero { min-height: 30vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 100px 2rem 40px; background: linear-gradient(135deg, #006D5B 0%, #004D3D 100%); color: white; }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 2rem; }
        .card { background: white; border-radius: 20px; box-shadow: 0 24px 48px rgba(0,77,61,0.1); padding: 30px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .table th { text-align: left; padding: 15px; background: #f8f9fa; color: #006D5B; }
        .table td { padding: 15px; border-bottom: 1px solid #eee; }
        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-traitee { background: #d1fae5; color: #065f46; }
        .badge-soumise { background: #fef3c7; color: #92400e; }
        .badge-en_cours { background: #dbeafe; color: #1e40af; }
        .badge-rejetee { background: #fee2e2; color: #991b1b; }
        .btn { padding: 8px 16px; border-radius: 8px; text-decoration: none; font-size: 13px; font-weight: 500; display: inline-flex; align-items: center; gap: 5px; }
        .btn-info { background: #e0f2fe; color: #0369a1; }
        .btn-success { background: #dcfce7; color: #15803d; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="../../../index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text"><h1>InnoGov</h1></div>
        </a>
        <div class="nav-menu">
            <a href="../../../index.php" class="nav-link">Accueil</a>
            <a href="ajouter.php" style="color: white; padding: 8px 20px; background: #006D5B; border-radius: 8px; text-decoration: none;">Déposer</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div>
        <h1>Mes réclamations</h1>
        <p>Suivez l'état de vos demandes en temps réel</p>
    </div>
</section>

<div class="container">
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Objet</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($list as $r): ?>
                <tr>
                    <td><strong><?= htmlspecialchars($r['reference']) ?></strong></td>
                    <td><?= htmlspecialchars($r['objet']) ?></td>
                    <td><span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst($r['statut']) ?></span></td>
                    <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                    <td>
                        <a href="../REPONSE/voir.php?id_reclamation=<?= $r['id_reclamation'] ?>" class="btn btn-info">
                            <i class="fas fa-comment-dots"></i> Voir réponse
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="../../../ASSETS/JS/script.js"></script>
</body>
</html>
