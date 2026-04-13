<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../../CONTROLLER/EmploiController.php";
$ctrl = new EmploiController();
$erreur = '';
$id = $_GET['id'] ?? 0;
$emploi = null;

// Récupérer les listes
$agents = $ctrl->getAgents();
$services = $ctrl->getServices();
$shifts = $ctrl->getShifts();

if($id) {
    $emploi = $ctrl->getEmploiById($id);
}

if(!$emploi) {
    $erreur = "Emploi non trouvé";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_agent = $_POST['id_agent'] ?? '';
    $id_service = $_POST['id_service'] ?? '';
    $id_shift = $_POST['id_shift'] ?? '';
    $date_travail = $_POST['date_travail'] ?? '';
    $statut = $_POST['statut'] ?? 'planifie';
    
    if($id_agent && $id_service && $id_shift && $date_travail) {
        if($ctrl->modifierEmploi($id, $id_agent, $id_service, $id_shift, $date_travail, $statut)) {
            header("Location: admin-emplois-lister.php");
            exit();
        } else {
            $erreur = "Erreur lors de la modification";
        }
    } else {
        $erreur = "Tous les champs sont obligatoires";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modifier un Emploi</title>
    <style>
        body{font-family:Arial;padding:20px;background:#f5f5f5;}
        .container{max-width:600px;background:white;padding:30px;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
        h1{color:#005C9E;}
        .form-group{margin:20px 0;}
        label{display:block;margin-bottom:5px;font-weight:bold;}
        input, select{width:100%;padding:10px;border:1px solid #ddd;border-radius:3px;box-sizing:border-box;font-size:14px;}
        select{cursor:pointer;}
        .btn{padding:10px 20px;background:#005C9E;color:white;border:none;border-radius:3px;cursor:pointer;margin-right:10px;font-size:14px;}
        .btn:hover{background:#003f75;}
        .btn-cancel{background:#666;}
        .btn-cancel:hover{background:#444;}
        .erreur{color:red;background:#ffe6e6;padding:10px;border-radius:3px;margin-bottom:20px;}
    </style>
</head>
<body>
<div class="container">
    <h1>✏️ Modifier un Emploi (Planning)</h1>
    <button type="button" class="btn btn-cancel" onclick="if(history.length>1){history.back();}else{window.location.href='admin-emplois-lister.php';}" style="margin-bottom:15px;display:inline-block;">← Retour</button>
    <?php if($erreur): ?>
    <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
    
    <?php if($emploi): ?>
    <form method="POST">
        <div class="form-group">
            <label>Agent:</label>
            <select name="id_agent" required>
                <option value="">-- Sélectionner un agent --</option>
                <?php foreach($agents as $a): ?>
                <option value="<?= $a['id'] ?>" <?= $a['id'] == $emploi['id_agent'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($a['nom'].' '.$a['prenom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Service:</label>
            <select name="id_service" required>
                <option value="">-- Sélectionner un service --</option>
                <?php foreach($services as $s): ?>
                <option value="<?= $s['id_service'] ?>" <?= $s['id_service'] == $emploi['id_service'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($s['nom_service']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Shift (Horaire):</label>
            <select name="id_shift" required>
                <option value="">-- Sélectionner un shift --</option>
                <?php foreach($shifts as $sh): ?>
                <option value="<?= $sh['id_shift'] ?>" <?= $sh['id_shift'] == $emploi['id_shift'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($sh['nom_shift']).' - '.substr($sh['heure_debut'],0,5).' à '.substr($sh['heure_fin'],0,5) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Date du Travail:</label>
            <input type="date" name="date_travail" value="<?= $emploi['date_travail'] ?>" required>
        </div>
        
        <div class="form-group">
            <label>Statut:</label>
            <select name="statut" required>
                <option value="planifie" <?= $emploi['statut'] == 'planifie' ? 'selected' : '' ?>>Planifié</option>
                <option value="termine" <?= $emploi['statut'] == 'termine' ? 'selected' : '' ?>>Terminé</option>
                <option value="annule" <?= $emploi['statut'] == 'annule' ? 'selected' : '' ?>>Annulé</option>
            </select>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Modifier</button>
            <a href="admin-emplois-lister.php" class="btn btn-cancel" style="text-decoration:none;">Annuler</a>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>

