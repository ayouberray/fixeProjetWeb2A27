<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])){ $_SESSION['user_id'] = 2; }

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$error = ""; $success = "";
$id_rdv = $_GET['id'] ?? 0;

$db = Config::getConnexion();
$sql = "SELECT * FROM rendez_vous WHERE id_rdv = :id AND id_citoyen = :citoyen";
$req = $db->prepare($sql);
$req->execute(['id' => $id_rdv, 'citoyen' => $_SESSION['user_id']]);
$rdv = $req->fetch();

if(!$rdv || $rdv['statut'] == 'annule' || $rdv['statut'] == 'termine'){
    header("Location: /projet/VIEW/frontoffice/citoyen-mes-rdv.php"); exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $date_heure = $_POST['date_heure'];
    $motif = $_POST['motif'];
    if(!empty($date_heure)){
        $rdvController->modifierRendezVous($id_rdv, $date_heure, $motif);
        $success = "Rendez-vous modifié avec succès !";
        header("refresh:2;url=/projet/VIEW/frontoffice/citoyen-mes-rdv.php");
    } else { $error = "Veuillez choisir une date"; }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier rendez-vous - InnoGov</title>
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
        <div class="card-header"><h2 class="card-title">Modifier rendez-vous #<?= $id_rdv ?></h2></div>
        
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Date et heure *</label>
                <input type="datetime-local" name="date_heure" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($rdv['date_heure'])) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Motif</label>
                <textarea name="motif" class="form-control" rows="3"><?= htmlspecialchars($rdv['motif'] ?? '') ?></textarea>
            </div>
            <button type="submit" class="btn btn-warning">Enregistrer</button>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>

<footer class="footer"><div class="footer-container"><div class="footer-section"><h4>InnoGov</h4><p>© 2024</p></div></div></footer>

</body>
</html>