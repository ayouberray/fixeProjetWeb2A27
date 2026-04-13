<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Corriger le path pour accÃ©der au CONTROLLER depuis VIEW/admin-shifts-lister.php
// Vue dans: VIEW/
// Controller dans: CONTROLLER/  
// Solution: __DIR__ = C:\xampp\htdocs\gestion emplois\VIEW
// On veut: C:\xampp\htdocs\gestion emplois\CONTROLLER
// Donc on monte d'1 niveau seulement: ../CONTROLLER

$dir = dirname(__FILE__);
$ctrl_path = $dir . "/../CONTROLLER/ShiftController.php";

if (!file_exists($ctrl_path)) {
    die("ERROR: Fichier non trouvÃ©: $ctrl_path<br>
         __DIR__ = " . __DIR__ . "<br>
         RÃ©pertoire courant: " . getcwd());
}

require_once $ctrl_path;

$ctrl = new ShiftController();
$shifts = $ctrl->getAllShifts();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Shifts</title>
    <style>
        body{font-family:Arial;padding:20px;background:#f5f5f5;}
        .container{max-width:1000px;margin:0 auto;background:white;padding:20px;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
        h1{color:#005C9E;}
        table{border-collapse:collapse;width:100%;margin-top:20px;}
        th,td{border:1px solid #ddd;padding:10px;text-align:left;}
        th{background:#005C9E;color:white;}
        tr:hover{background:#f9f9f9;}
        .btn{display:inline-block;padding:5px 10px;color:white;text-decoration:none;border-radius:3px;margin-right:5px;font-size:12px;}
        .btn-add{background:#005C9E;padding:10px 15px;font-size:14px;}
        .btn-edit{background:#FFA500;}
        .btn-delete{background:#e31e24;}
        .btn-edit:hover{background:#FF8C00;}
        .btn-delete:hover{background:#c41a1f;}
    </style>
</head>
<body>
<div class="container">
    <h1>â° Gestion des Shifts (Horaires)</h1>
    <a href="admin-shifts-ajouter.php" class="btn btn-add">+ Ajouter un Shift</a>
    <br><br>
    
    <?php if(!empty($shifts)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Heure DÃ©but</th>
            <th>Heure Fin</th>
            <th>DurÃ©e</th>
            <th>Actions</th>
        </tr>
        <?php foreach($shifts as $s): 
            $debut = new DateTime($s['heure_debut']);
            $fin = new DateTime($s['heure_fin']);
            $diff = $fin->diff($debut);
            $duree = $diff->h.'h '.$diff->i.'min';
        ?>
        <tr>
            <td><?= $s['id_shift'] ?></td>
            <td><?= htmlspecialchars($s['nom_shift']) ?></td>
            <td><?= substr($s['heure_debut'],0,5) ?></td>
            <td><?= substr($s['heure_fin'],0,5) ?></td>
            <td><?= $duree ?></td>
            <td>
                <a href="admin-shifts-modifier.php?id=<?= $s['id_shift'] ?>" class="btn btn-edit">Modifier</a>
                <a href="backoffice/admin-shifts-supprimer.php?id=<?= $s['id_shift'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p style="color:#666;">Aucun shift trouvÃ©</p>
    <?php endif; ?>
</div>
</body>
</html>
