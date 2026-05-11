<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/ServiceController.php";
require_once __DIR__."/../../MODEL/config.php";

$serviceController = new ServiceController();
$db = Config::getConnexion();

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $nom = $_POST['nom_service'];
    $desc = $_POST['description'];
    $duree = $_POST['duree_moyenne'];
    
    if(!empty($nom) && !empty($duree)){
        // Note: You might need to check if your Service model/controller has an add method
        // Using direct SQL if controller doesn't have it or is complex
        $stmt = $db->prepare("INSERT INTO services (nom_service, description, duree_moyenne, statut) VALUES (?, ?, ?, 'actif')");
        if($stmt->execute([$nom, $desc, $duree])){
            $success = "Service ajouté avec succès !";
            header("refresh:2;url=/Gestion_RDV/projet/VIEW/backoffice/admin-services.php");
        } else {
            $error = "Erreur lors de l'ajout.";
        }
    } else {
        $error = "Veuillez remplir les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Ajouter Service</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gestion_RDV/projet/assets/css/style.css?v=20260509_v9">
    <style>
        body { font-family: 'Roboto', sans-serif; background: var(--bg-page); }
        .futuristic-container { max-width: 700px; margin: 40px auto; }
        .cyber-card { background: white; padding: 40px; border-radius: 20px; border: 1px solid #e2e8f0; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-label { display: block; margin-bottom: 8px; font-weight: 700; color: var(--primary); }
        .futuristic-input { width: 100%; padding: 12px; border-radius: 10px; border: 1px solid #cbd5e1; margin-bottom: 20px; box-sizing: border-box; }
        .btn-cyber { background: var(--primary); color: white; border: none; padding: 15px 30px; border-radius: 12px; font-weight: 700; cursor: pointer; width: 100%; transition: 0.2s; }
        .btn-cyber:hover { background: var(--primary-dark); transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="navbar-wrapper">
        <nav class="navbar floating-pill">
            <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
                <div class="logo-hybrid"><div class="logo-circle"><i class="fas fa-leaf"></i></div><span class="logo-text-serif">InnoGov</span></div>
            </a>
            <div class="nav-menu"><a href="/Gestion_RDV/projet/VIEW/backoffice/admin-services.php" class="nav-link">← Retour</a></div>
        </nav>
    </div>

    <div class="futuristic-container" style="margin-top: 120px;">
        <div class="cyber-card">
            <h2 style="margin-top: 0; color: var(--primary); text-align: center;"><i class="fas fa-plus-circle"></i> Ajouter un Service</h2>
            <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            
            <form method="POST">
                <label class="form-label">Nom du Service *</label>
                <input type="text" name="nom_service" class="futuristic-input" required>
                
                <label class="form-label">Description</label>
                <textarea name="description" class="futuristic-input" rows="4"></textarea>
                
                <label class="form-label">Durée moyenne (min) *</label>
                <input type="number" name="duree_moyenne" class="futuristic-input" required>
                
                <button type="submit" class="btn-cyber">Enregistrer le Service</button>
            </form>
        </div>
    </div>
</body>
</html>
