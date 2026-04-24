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
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link active">Rendez-vous</a>
            <a href="/projet/VIEW/backoffice/admin-services.php" class="nav-link">Services</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
            <a href="/projet/index.php" class="nav-link">Espace citoyen</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
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
            <h2 class="card-title"><i class="fas fa-calendar-check"></i> Tous les rendez-vous</h2>
            <a href="/projet/VIEW/backoffice/admin-ajouter-rdv.php" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Ajouter un RDV
            </a>
        </div>
        
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Citoyen</th>
                        <th>Service</th>
                        <th>Date/Heure</th>
                        <th>Statut</th>
                        <th>Agent</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(!empty($list)): ?>
                        <?php foreach($list as $rdv): ?>
                        <tr>
                            <td>#<?= $rdv['id_rdv'] ?></td>
                            <td><strong><?= htmlspecialchars($rdv['citoyen_nom']) ?></strong></td>
                            <td><?= htmlspecialchars($rdv['service_nom']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($rdv['date_heure'])) ?></td>
                            <td><span class="badge badge-<?= $rdv['statut'] ?>"><?= $rdv['statut'] ?></span></td>
                            <td>
                                <?php if($rdv['agent_nom']): ?>
                                    <i class="fas fa-user-check"></i> <?= htmlspecialchars($rdv['agent_nom']) ?>
                                <?php else: ?>
                                    <span class="badge badge-en_attente">Non affecté</span>
                                <?php endif; ?>
                            </td>
                            <td class="actions">
                                <a href="/projet/VIEW/backoffice/admin-modifier-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-warning btn-sm" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="/projet/VIEW/backoffice/admin-affecter-agent.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-secondary btn-sm" title="Affecter un agent">
                                    <i class="fas fa-user-plus"></i>
                                </a>
                                <a href="/projet/VIEW/backoffice/admin-supprimer-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce rendez-vous ?')" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align:center; padding: 40px;">
                                <i class="fas fa-calendar-times" style="font-size: 48px; color: var(--gray-500); margin-bottom: 15px; display: block;"></i>
                                Aucun rendez-vous trouvé
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>InnoGov Admin</h4><p>Plateforme de gestion municipale</p></div>
        <div class="footer-section"><h4>Contact</h4><p>Tel: +216 70 000 000</p></div>
        <div class="footer-section"><h4>Horaires</h4><p>Lun-Ven: 8h30 - 15h30</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 InnoGov - Tous droits réservés</p></div>
</footer>

</body>
</html>