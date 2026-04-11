<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";

$rdvController = new RendezVousController();
$list = $rdvController->getRendezVousByCitoyen($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes rendez-vous - InnoGov</title>
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
                    <p>Municipalité Tunisienne</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/index.php" class="nav-link">Accueil</a>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link active">Mes RDV</a>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary btn-sm">Prendre RDV</a>
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
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne</p>
        <div class="hero-buttons">
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
            <a href="#services" class="btn btn-outline">En savoir plus</a>
        </div>
    </div>
</section>

<div class="container">
    <div class="card reveal">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-calendar-alt"></i> Mes rendez-vous</h2>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> Nouveau RDV</a>
        </div>
        
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Service</th><th>Date/Heure</th><th>Statut</th><th>Motif</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if(!empty($list)): ?>
                        <?php foreach($list as $rdv): 
                            $disabled = ($rdv['statut'] == 'annule' || $rdv['statut'] == 'termine') ? 'disabled' : '';
                        ?>
                        <tr>
                            <td>#<?= $rdv['id_rdv'] ?></td>
                            <td><?= htmlspecialchars($rdv['nom_service']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rdv['date_heure'])) ?></td>
                            <td><span class="badge badge-<?= $rdv['statut'] ?>"><?= $rdv['statut'] ?></span></td>
                            <td><?= htmlspecialchars($rdv['motif'] ?? '-') ?></td>
                            <td>
                                <a href="/projet/VIEW/frontoffice/citoyen-modifier-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm <?= $disabled ?>" <?= $disabled ?>>Modifier</a>
                                <a href="/projet/VIEW/frontoffice/citoyen-annuler-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Annuler ce rendez-vous ?')">Annuler</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center">Aucun rendez-vous trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>InnoGov</h4><p>Plateforme de services municipaux</p></div>
        <div class="footer-section"><h4>Contact</h4><p>Tel: +216 70 000 000</p></div>
        <div class="footer-section"><h4>Horaires</h4><p>Lun-Ven: 8h30 - 15h30</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 InnoGov - Tous droits réservés</p></div>
</footer>

</body>
</html>