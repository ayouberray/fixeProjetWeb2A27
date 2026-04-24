<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;
$rdv = $rdvController->getRendezVousById($id_rdv);
if(!$rdv){ header("Location: /projet/VIEW/backoffice/admin-lister-rdv.php"); exit(); }

$db = Config::getConnexion();
$citoyens = $db->query("SELECT CONCAT(prenom, ' ', nom) as nom_complet FROM users WHERE role = 'citoyen' ORDER BY nom")->fetchAll();
$services = $db->query("SELECT nom_service FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $citoyen_nom = $_POST['citoyen_nom'];
    $service_nom = $_POST['service_nom'];
    $date_heure = $_POST['date_heure'];
    $statut = $_POST['statut'];
    $motif = $_POST['motif'];
    
    if(!empty($citoyen_nom) && !empty($service_nom) && !empty($date_heure)){
        $result = $rdvController->adminModifierRendezVous($id_rdv, $citoyen_nom, $service_nom, $date_heure, $statut, $motif);
        if($result){ 
            $success = "Rendez-vous modifié !"; 
            header("refresh:2;url=/projet/VIEW/backoffice/admin-lister-rdv.php");
        } else { 
            $error = "Erreur lors de la modification"; 
        }
    } else { 
        $error = "Veuillez remplir tous les champs"; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Modifier rendez-vous</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-building"></i></div>
                <div class="logo-text"><h1>InnoGov</h1><p>Administration</p></div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">← Retour</a>
        </div>
    </div>
</nav>

<section class="hero" style="min-height: 40vh;">
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Modifier un rendez-vous</h1>
        <p>RDV #<?= $id_rdv ?></p>
    </div>
</section>

<div class="container">
    <div class="card">
        <div class="card-header"><h2 class="card-title">Modifier rendez-vous #<?= $id_rdv ?></h2></div>
        
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Citoyen</label>
                <select name="citoyen_nom" class="form-control" required>
                    <?php foreach($citoyens as $c): $selected = ($c['nom_complet'] == $rdv['citoyen_nom']) ? 'selected' : ''; ?>
                        <option value="<?= htmlspecialchars($c['nom_complet']) ?>" <?= $selected ?>><?= htmlspecialchars($c['nom_complet']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Service</label>
                <select name="service_nom" class="form-control" required>
                    <?php foreach($services as $s): $selected = ($s['nom_service'] == $rdv['service_nom']) ? 'selected' : ''; ?>
                        <option value="<?= htmlspecialchars($s['nom_service']) ?>" <?= $selected ?>><?= htmlspecialchars($s['nom_service']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="statut" class="form-control" required>
                    <option value="en_attente" <?= $rdv['statut']=='en_attente' ? 'selected' : '' ?>>En attente</option>
                    <option value="confirme" <?= $rdv['statut']=='confirme' ? 'selected' : '' ?>>Confirmé</option>
                    <option value="annule" <?= $rdv['statut']=='annule' ? 'selected' : '' ?>>Annulé</option>
                    <option value="termine" <?= $rdv['statut']=='termine' ? 'selected' : '' ?>>Terminé</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Date et heure</label>
                <input type="datetime-local" name="date_heure" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime($rdv['date_heure'])) ?>" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Motif</label>
                <textarea name="motif" class="form-control" rows="3"><?= htmlspecialchars($rdv['motif'] ?? '') ?></textarea>
            </div>
            
            <button type="submit" class="btn btn-warning">Enregistrer</button>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>

</body>
</html>