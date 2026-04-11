<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/AvisController.php";
$ctrl = new AvisController();

$avis = $ctrl->getAllAvis();
$stats = $ctrl->getStatistiques();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des avis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        .stars-display { color: #f59e0b; font-size: 14px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-star"></i>
            </div>
            <h1>Gestion des avis</h1>
            <p>Consultez les avis laissés par les citoyens</p>
        </div>
        
        <div class="stats-mini" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; max-width: 400px; margin-bottom: 30px;">
            <div class="stat-mini"><div class="number"><?= $stats['total'] ?></div><div class="label">Total avis</div></div>
            <div class="stat-mini"><div class="number"><?= number_format($stats['moyenne'], 1) ?></div><div class="label">Note moyenne</div></div>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Réclamation</th>
                        <th>Citoyen</th>
                        <th>Note</th>
                        <th>Satisfaction</th>
                        <th>Commentaire</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($avis)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 60px;">Aucun avis trouvé</td></tr>
                    <?php else: ?>
                        <?php foreach($avis as $a): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($a['reference']) ?></strong><br><small><?= htmlspecialchars(substr($a['objet'], 0, 30)) ?>...</small></td>
                            <td><?= htmlspecialchars($a['prenom'] . ' ' . $a['nom']) ?></td>
                            <td class="stars-display">
                                <?php for($i = 1; $i <= 5; $i++): ?>
                                    <?= $i <= $a['note'] ? '★' : '☆' ?>
                                <?php endfor; ?>
                            </td>
                            <td><?= ucfirst(str_replace('_', ' ', $a['satisfaction'])) ?></td>
                            <td><?= htmlspecialchars(substr($a['commentaire'] ?? '', 0, 50)) ?>...</td>
                            <td><?= date('d/m/Y', strtotime($a['date_avis'])) ?></td>
                            <td class="actions">
                                <a href="modifier.php?id=<?= $a['id_avis'] ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                <a href="supprimer.php?id=<?= $a['id_avis'] ?>" class="btn-delete" onclick="return confirm('Supprimer cet avis ?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <div style="margin-top: 20px;">
            <a href="../../dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
</body>
</html>