<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../../CONTROLLER/EmploiController.php";
$ctrl = new EmploiController();
$emplois = $ctrl->getAllEmplois();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Emplois</title>
    <style>
        body{font-family:Arial;padding:20px;background:#f5f5f5;}
        .container{max-width:1200px;margin:0 auto;background:white;padding:20px;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
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
        .small{font-size:12px;color:#666;}
    </style>
</head>
<body>
<div class="container">
    <h1>📋 Gestion des Emplois (Plannings)</h1>
    <a href="admin-emplois-ajouter.php" class="btn btn-add">+ Ajouter un Emploi</a>
    <br><br>
    
    <?php if(!empty($emplois)): ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Agent</th>
            <th>Service</th>
            <th>Shift</th>
            <th>Date Travail</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php foreach($emplois as $e): ?>
        <tr>
            <td><?= $e['id_emploi'] ?></td>
            <td><?= htmlspecialchars($e['agent_nom'] ?? 'N/A').' '.htmlspecialchars($e['agent_prenom'] ?? '') ?></td>
            <td><?= htmlspecialchars($e['nom_service'] ?? 'N/A') ?></td>
            <td>
                <span class="small"><?= htmlspecialchars($e['nom_shift'] ?? 'N/A') ?></span><br>
                <span class="small"><?= substr($e['heure_debut'] ?? '00:00', 0, 5) ?> - <?= substr($e['heure_fin'] ?? '00:00', 0, 5) ?></span>
            </td>
            <td><?= date('d/m/Y', strtotime($e['date_travail'])) ?></td>
            <td>
                <?php 
                    $statuts = ['planifie' => '🔵 Planifié', 'termine' => '✅ Terminé', 'annule' => '❌ Annulé'];
                    echo $statuts[$e['statut']] ?? $e['statut'];
                ?>
            </td>
            <td>
                <a href="admin-emplois-modifier.php?id=<?= $e['id_emploi'] ?>" class="btn btn-edit">Modifier</a>
                <a href="admin-emplois-supprimer.php?id=<?= $e['id_emploi'] ?>" class="btn btn-delete" onclick="return confirm('Supprimer ?')">Supprimer</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php else: ?>
    <p style="color:#666;">Aucun emploi trouvé</p>
    <?php endif; ?>
</div>
</body>
</html>

