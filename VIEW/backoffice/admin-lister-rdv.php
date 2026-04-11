<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";

$rdvController = new RendezVousController();
$list = $rdvController->getAllRendezVous();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Liste des rendez-vous</title>
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
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link active">Tous les RDV</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
            <a href="/projet/index.php" class="nav-link">Espace citoyen</a>
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
        <h1>Administration</h1>
        <p>Gérez tous les rendez-vous de la municipalité</p>
        <div class="hero-buttons">
            <a href="/projet/VIEW/backoffice/admin-ajouter-rdv.php" class="btn btn-primary">Ajouter un RDV</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="btn btn-outline">Voir stats</a>
        </div>
    </div>
</section>

<div class="container">
    <div class="card reveal">
        <div class="card-header">
            <h2 class="card-title">Tous les rendez-vous</h2>
            <a href="/projet/VIEW/backoffice/admin-ajouter-rdv.php" class="btn btn-primary btn-sm">Ajouter un RDV</a>
        </div>
        
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Citoyen</th><th>Service</th><th>Date/Heure</th><th>Statut</th><th>Agent</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if(!empty($list)): ?>
                        <?php foreach($list as $rdv): ?>
                        <tr>
                            <td>#<?= $rdv['id_rdv'] ?></td>
                            <td><?= htmlspecialchars($rdv['citoyen']) ?></td>
                            <td><?= htmlspecialchars($rdv['nom_service']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rdv['date_heure'])) ?></td>
                            <td><span class="badge badge-<?= $rdv['statut'] ?>"><?= $rdv['statut'] ?></span></td>
                            <td><?= $rdv['id_agent'] ? 'Agent #'.$rdv['id_agent'] : 'Non affecté' ?></td>
                            <td>
                                <a href="/projet/VIEW/backoffice/admin-modifier-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm">Modifier</a>
                                <a href="/projet/VIEW/backoffice/admin-affecter-agent.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-secondary btn-sm">Affecter</a>
                                <a href="/projet/VIEW/backoffice/admin-supprimer-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center">Aucun rendez-vous</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer"><div class="footer-container"><div class="footer-section"><h4>InnoGov Admin</h4><p>© 2024</p></div></div></footer>

</body>
</html>