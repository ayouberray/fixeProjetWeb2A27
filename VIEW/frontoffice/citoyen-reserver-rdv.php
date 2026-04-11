<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$error = "";
$success = "";

$db = Config::getConnexion();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_service = $_POST['id_service'];
    $date_heure = $_POST['date_heure'];
    $motif = $_POST['motif'];
    
    if(!empty($id_service) && !empty($date_heure)){
        $rdv = new RendezVous($_SESSION['user_id'], $id_service, $date_heure, $motif);
        $result = $rdvController->ajouterRendezVous($rdv);
        if($result){ $success = "Rendez-vous réservé avec succès ! ID: #".$result; }
        else { $error = "Erreur lors de la réservation"; }
    } else { $error = "Veuillez sélectionner un service et une date"; }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un rendez-vous - InnoGov</title>
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
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link">Mes RDV</a>
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
            <h2 class="card-title"><i class="fas fa-calendar-plus"></i> Réserver un rendez-vous</h2>
            <p>Bienvenue, <strong><?= $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'] ?></strong></p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label"><i class="fas fa-concierge-bell"></i> Service *</label>
                <select name="id_service" class="form-control" required>
                    <option value="">-- Choisir un service --</option>
                    <?php foreach($services as $s): ?>
                        <option value="<?= $s['id_service'] ?>"><?= $s['nom_service'] ?> (<?= $s['duree_moyenne'] ?> min)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-calendar-alt"></i> Date et heure *</label>
                <input type="datetime-local" name="date_heure" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label class="form-label"><i class="fas fa-pencil-alt"></i> Motif (optionnel)</label>
                <textarea name="motif" class="form-control" rows="3" placeholder="Décrivez l'objet de votre rendez-vous..."></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Réserver</button>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="btn btn-outline">Mes rendez-vous</a>
        </form>
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