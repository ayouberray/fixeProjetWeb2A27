<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../MODEL/config.php";

$db = Config::getConnexion();

$total = $db->query("SELECT COUNT(*) as total FROM rendez_vous")->fetch();
$en_attente = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='en_attente'")->fetch();
$confirme = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='confirme'")->fetch();
$annule = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='annule'")->fetch();
$termine = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='termine'")->fetch();

$parService = $db->query("SELECT service_nom, COUNT(*) as total FROM rendez_vous GROUP BY service_nom ORDER BY total DESC")->fetchAll();
$parAgent = $db->query("SELECT agent_nom, COUNT(*) as total FROM rendez_vous WHERE agent_nom IS NOT NULL GROUP BY agent_nom ORDER BY total DESC")->fetchAll();
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
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Rendez-vous</a>
            <a href="/projet/VIEW/backoffice/admin-services.php" class="nav-link">Services</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link active">Statistiques</a>
            <a href="/projet/index.php" class="nav-link">Espace citoyen</a>
        </div>
    </div>
</nav>

<section class="hero" style="min-height: 40vh;">
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
        <div class="card-header"><h2 class="card-title"><i class="fas fa-chart-line"></i> Statistiques des rendez-vous</h2></div>
        
        <div class="stats-grid">
            <div class="stat-card"><i class="fas fa-calendar-alt"></i><div class="number"><?= $total['total'] ?></div><div class="label">Total RDV</div></div>
            <div class="stat-card"><i class="fas fa-clock"></i><div class="number"><?= $en_attente['total'] ?></div><div class="label">En attente</div></div>
            <div class="stat-card"><i class="fas fa-check-circle"></i><div class="number"><?= $confirme['total'] ?></div><div class="label">Confirmés</div></div>
            <div class="stat-card"><i class="fas fa-times-circle"></i><div class="number"><?= $annule['total'] ?></div><div class="label">Annulés</div></div>
            <div class="stat-card"><i class="fas fa-check-double"></i><div class="number"><?= $termine['total'] ?></div><div class="label">Traités</div></div>
        </div>
        
        <h3 style="margin-top: 40px;"><i class="fas fa-concierge-bell"></i> Par service</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Service</th><th>Nombre de RDV</th></tr></thead>
                <tbody>
                    <?php foreach($parService as $s): ?>
                    <tr><td><i class="fas fa-folder-open"></i> <?= htmlspecialchars($s['service_nom']) ?></td><td><span class="badge badge-confirme"><?= $s['total'] ?></span></td></tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <h3 style="margin-top: 40px;"><i class="fas fa-users"></i> Par agent</h3>
        <div class="table-wrapper">
            <table class="table">
                <thead><tr><th>Agent</th><th>Nombre de RDV</th></tr></thead>
                <tbody>
                    <?php foreach($parAgent as $a): ?>
                    <tr><td><i class="fas fa-user-check"></i> <?= htmlspecialchars($a['agent_nom']) ?></td><td><span class="badge badge-primary"><?= $a['total'] ?></span></td></tr>
                    <?php endforeach; ?>
                    <?php if(empty($parAgent)): ?>
                    <tr><td colspan="2" style="text-align:center">Aucun agent affecté</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>