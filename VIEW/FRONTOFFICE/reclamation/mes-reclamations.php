<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
$ctrl = new ReclamationController();

$reclamations = $ctrl->getReclamationsByCitoyen($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes réclamations</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-list-alt"></i>
            </div>
            <h1>Mes réclamations</h1>
            <p>Suivez l'état de vos réclamations</p>
        </div>
        
        <div style="margin-bottom: 20px;">
            <a href="ajouter.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nouvelle réclamation</a>
            <a href="../../dashboard-citoyen.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Objet</th>
                        <th>Catégorie</th>
                        <th>Priorité</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($reclamations)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 60px;">Vous n'avez aucune réclamation</td></tr>
                    <?php else: ?>
                        <?php foreach($reclamations as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['reference']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($r['objet'], 0, 40)) ?>...</td>
                            <td><?= ucfirst($r['categorie']) ?></td>
                            <td class="priority-<?= $r['priorite'] == 'urgente' || $r['priorite'] == 'haute' ? 'high' : ($r['priorite'] == 'normale' ? 'normal' : 'low') ?>">
                                <?= ucfirst($r['priorite']) ?>
                            </td>
                            <td><span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst(str_replace('_', ' ', $r['statut'])) ?></span></td>
                            <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                            <td class="actions">
                                <a href="details.php?id=<?= $r['id_reclamation'] ?>" class="btn-view"><i class="fas fa-eye"></i></a>
                                <?php if($r['statut'] == 'traitee' && !$ctrl->getAvisByReclamation($r['id_reclamation'])): ?>
                                    <a href="avis.php?id=<?= $r['id_reclamation'] ?>" class="btn" style="background: #f59e0b;"><i class="fas fa-star"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>