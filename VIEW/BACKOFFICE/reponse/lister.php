<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReponseController.php";
$ctrl = new ReponseController();

$reponses = $ctrl->getAllReponses();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des réponses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-reply-all"></i>
            </div>
            <h1>Gestion des réponses</h1>
            <p>Historique des réponses aux réclamations</p>
        </div>
        
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Réclamation</th>
                        <th>Citoyen</th>
                        <th>Agent</th>
                        <th>Type</th>
                        <th>Contenu</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($reponses)): ?>
                        <tr><td colspan="7" style="text-align: center; padding: 60px;">Aucune réponse trouvée</td></tr>
                    <?php else: ?>
                        <?php foreach($reponses as $r): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($r['reference']) ?></strong><br><small><?= htmlspecialchars(substr($r['objet'], 0, 30)) ?>...</small></td>
                            <td><?= htmlspecialchars($r['prenom'] . ' ' . $r['nom']) ?></td>
                            <td><?= htmlspecialchars($r['nom_agent']) ?></td>
                            <td><span class="badge"><?= ucfirst($r['type_reponse']) ?></span></td>
                            <td><?= htmlspecialchars(substr($r['contenu'], 0, 50)) ?>...</td>
                            <td><?= date('d/m/Y H:i', strtotime($r['date_reponse'])) ?></td>
                            <td class="actions">
                                <a href="modifier.php?id=<?= $r['id_reponse'] ?>" class="btn-edit"><i class="fas fa-edit"></i></a>
                                <a href="supprimer.php?id=<?= $r['id_reponse'] ?>" class="btn-delete" onclick="return confirm('Supprimer cette réponse ?')"><i class="fas fa-trash"></i></a>
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