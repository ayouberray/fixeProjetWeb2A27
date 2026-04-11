<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../MODEL/config.php";

$db = Config::getConnexion();

$total = $db->query("SELECT COUNT(*) as total FROM rendez_vous")->fetch();
$en_attente = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='en_attente'")->fetch();
$confirme = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='confirme'")->fetch();
$annule = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='annule'")->fetch();
$termine = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='termine'")->fetch();

$parService = $db->query("SELECT s.nom_service, COUNT(r.id_rdv) as total FROM services s LEFT JOIN rendez_vous r ON s.id_service = r.id_service GROUP BY s.id_service")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Statistiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-building"></i></div>
                <div class="logo-text">
                    <h1>InnoGov</h1>
                    <p>Administration</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">← Retour</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION AVEC SLIDESHOW -->
<section class="hero">
    <div class="hero-slideshow">
        <img src="/projet/assets/images/tunisia1.jpg" class="slide active" alt="Tunisie">
        <img src="/projet/assets/images/tunisia2.jpg" class="slide" alt="Tunisie">
        <img src="/projet/assets/images/tunisia3.jpg" class="slide" alt="Tunisie">
        <img src="/projet/assets/images/tunisia4.jpg" class="slide" alt="Tunisie">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Statistiques</h1>
        <p>Analyse des rendez-vous</p>
    </div>
</section>

<div class="container">
    <div class="card reveal">
        <div class="card-header"><h2 class="card-title">Statistiques des rendez-vous</h2></div>
        
        <div class="stats-grid">
            <div class="stat-card"><h3>Total RDV</h3><div class="number"><?= $total['total'] ?></div></div>
            <div class="stat-card"><h3>En attente</h3><div class="number"><?= $en_attente['total'] ?></div></div>
            <div class="stat-card"><h3>Confirmés</h3><div class="number"><?= $confirme['total'] ?></div></div>
            <div class="stat-card"><h3>Annulés</h3><div class="number"><?= $annule['total'] ?></div></div>
            <div class="stat-card"><h3>Traités</h3><div class="number"><?= $termine['total'] ?></div></div>
        </div>
        
        <h3>Par service</h3>
        <div class="table-wrapper">
            <table class="table"><thead><tr><th>Service</th><th>Nombre de RDV</th></tr></thead><tbody>
            <?php foreach($parService as $s): ?><tr><td><i class="fas fa-folder-open"></i> <?= htmlspecialchars($s['nom_service']) ?></td><td><span class="badge badge-confirme"><?= $s['total'] ?></span></td></tr><?php endforeach; ?>
            </tbody></table>
        </div>
    </div>
</div>

</body>
</html>