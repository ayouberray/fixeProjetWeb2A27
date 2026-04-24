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
$id_rdv = $_GET['id'] ?? 0;

// Récupérer le rendez-vous
$rdv = $rdvController->getRendezVousById($id_rdv);

// Vérifier que le citoyen est bien le propriétaire
$citoyen_nom = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
if(!$rdv || $rdv['citoyen_nom'] != $citoyen_nom){
    header("Location: /projet/VIEW/frontoffice/citoyen-mes-rdv.php"); 
    exit();
}

// Récupérer la liste des services
$db = Config::getConnexion();
$services = $db->query("SELECT nom_service FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $service_nom = $_POST['service_nom'];
    $date_heure = $_POST['date_heure'];
    $motif = $_POST['motif'];
    
    if(!empty($service_nom) && !empty($date_heure)){
        // Modifier tous les champs (service, date, motif)
        $result = $rdvController->modifierRendezVousComplet($id_rdv, $service_nom, $date_heure, $motif);
        if($result){
            $success = "Rendez-vous modifié avec succès !";
            header("refresh:2;url=/projet/VIEW/frontoffice/citoyen-mes-rdv.php");
        } else {
            $error = "Erreur lors de la modification";
        }
    } else { 
        $error = "Veuillez choisir un service et une date"; 
    }
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

<section class="hero" style="min-height: 40vh;">
    <div class="hero-slideshow">
        <img src="/projet/assets/images/tunisia1.jpg" class="slide active" alt="Tunisie">
        <img src="/projet/assets/images/tunisia2.jpg" class="slide" alt="Tunisie">
        <img src="/projet/assets/images/tunisia3.jpg" class="slide" alt="Tunisie">
        <img src="/projet/assets/images/tunisia4.jpg" class="slide" alt="Tunisie">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Modifier mon rendez-vous</h1>
        <p>Modifiez le service, la date ou le motif de votre rendez-vous</p>
    </div>
</section>

<div class="container">
    <div class="card reveal">
        <div class="card-header">
            <h2 class="card-title">
                <i class="fas fa-edit"></i> Modifier rendez-vous #<?= $id_rdv ?>
            </h2>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <!-- CHOIX DU SERVICE -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-concierge-bell"></i> Service *</label>
                <select name="service_nom" class="form-control" required>
                    <option value="">-- Choisir un service --</option>
                    <?php foreach($services as $s): 
                        $selected = ($s['nom_service'] == $rdv['service_nom']) ? 'selected' : '';
                    ?>
                        <option value="<?= htmlspecialchars($s['nom_service']) ?>" <?= $selected ?>>
                            <?= htmlspecialchars($s['nom_service']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- DATE ET HEURE -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-calendar-alt"></i> Date et heure *</label>
                <input type="datetime-local" name="date_heure" class="form-control" 
                       value="<?= date('Y-m-d\TH:i', strtotime($rdv['date_heure'])) ?>" required>
            </div>
            
            <!-- MOTIF -->
            <div class="form-group">
                <label class="form-label"><i class="fas fa-pencil-alt"></i> Motif</label>
                <textarea name="motif" class="form-control" rows="3"><?= htmlspecialchars($rdv['motif'] ?? '') ?></textarea>
            </div>
            
            <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                <button type="submit" class="btn btn-warning">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="btn btn-outline">
                    Annuler
                </a>
            </div>
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