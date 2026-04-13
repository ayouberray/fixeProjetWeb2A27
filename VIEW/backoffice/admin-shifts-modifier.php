<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../../CONTROLLER/ShiftController.php";
$ctrl = new ShiftController();
$erreur = '';
$id = $_GET['id'] ?? 0;
$shift = null;

if($id) {
    $shift = $ctrl->getShiftById($id);
}

if(!$shift) {
    $erreur = "Shift non trouvé";
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom_shift'] ?? '';
    $debut = $_POST['heure_debut'] ?? '';
    $fin = $_POST['heure_fin'] ?? '';
    
    if($nom && $debut && $fin) {
        if($ctrl->modifierShift($id, $nom, $debut, $fin)) {
            header("Location: admin-shifts-lister.php");
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
    <title>Modifier un Shift</title>
    <style>
        body{font-family:Arial;padding:20px;background:#f5f5f5;}
        .container{max-width:600px;background:white;padding:30px;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
        h1{color:#005C9E;}
        .form-group{margin:20px 0;}
        label{display:block;margin-bottom:5px;font-weight:bold;}
        input{width:100%;padding:10px;border:1px solid #ddd;border-radius:3px;box-sizing:border-box;}
        .btn{padding:10px 20px;background:#005C9E;color:white;border:none;border-radius:3px;cursor:pointer;margin-right:10px;font-size:14px;}
        .btn:hover{background:#003f75;}
        .btn-cancel{background:#666;}
        .btn-cancel:hover{background:#444;}
        .erreur{color:red;background:#ffe6e6;padding:10px;border-radius:3px;margin-bottom:20px;}
    </style>
</head>
<body>
<div class="container">
    <h1>✏️ Modifier un Shift</h1>
    <button type="button" class="btn btn-cancel" onclick="if(history.length>1){history.back();}else{window.location.href='admin-shifts-lister.php';}" style="margin-bottom:15px;display:inline-block;">← Retour</button>
    <?php if($erreur): ?>
    <div class="erreur"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
    
    <?php if($shift): ?>
    <form method="POST">
        <div class="form-group">
            <label>Nom du Shift:</label>
            <input type="text" name="nom_shift" value="<?= htmlspecialchars($shift['nom_shift']) ?>" required>
        </div>
        <div class="form-group">
            <label>Heure début:</label>
            <input type="time" name="heure_debut" value="<?= substr($shift['heure_debut'],0,5) ?>" required>
        </div>
        <div class="form-group">
            <label>Heure fin:</label>
            <input type="time" name="heure_fin" value="<?= substr($shift['heure_fin'],0,5) ?>" required>
        </div>
        <div class="form-group">
            <button type="submit" class="btn">Modifier</button>
            <a href="admin-shifts-lister.php" class="btn btn-cancel" style="text-decoration:none;">Annuler</a>
        </div>
    </form>
    <?php endif; ?>
</div>
</body>
</html>

