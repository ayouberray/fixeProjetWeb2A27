<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;

$db = Config::getConnexion();

// Récupérer les agents (nom complet)
$agents = $db->prepare("SELECT id, CONCAT(prenom, ' ', nom) as nom_complet FROM users WHERE role = 'agent'");
$agents->execute();
$agentsList = $agents->fetchAll();

// Récupérer les infos du RDV
$rdv = $rdvController->getRendezVousById($id_rdv);

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $agent_nom = $_POST['agent_nom'];
    if(!empty($agent_nom)){
        $rdvController->affecterAgent($id_rdv, $agent_nom);
        $success = "Agent affecté avec succès !";
        header("refresh:2;url=/projet/VIEW/backoffice/admin-lister-rdv.php");
    } else { 
        $error = "Veuillez sélectionner un agent"; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Affecter un agent</title>
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
        <h1>Affecter un agent</h1>
        <p>RDV #<?= $id_rdv ?> - <?= htmlspecialchars($rdv['citoyen_nom'] ?? '') ?> - <?= htmlspecialchars($rdv['service_nom'] ?? '') ?></p>
    </div>
</section>

<div class="container">
    <div class="card">
        <div class="card-header"><h2 class="card-title">Affecter un agent au rendez-vous</h2></div>
        
        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Choisir un agent</label>
                <select name="agent_nom" class="form-control" required>
                    <option value="">-- Sélectionner --</option>
                    <?php foreach($agentsList as $agent): ?>
                        <option value="<?= htmlspecialchars($agent['nom_complet']) ?>"><?= htmlspecialchars($agent['nom_complet']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Affecter</button>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="btn btn-outline">Annuler</a>
        </form>
    </div>
</div>

</body>
</html>