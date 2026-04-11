<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReponseController.php";
require_once "../../../CONTROLLER/ReclamationController.php";

$reponseCtrl = new ReponseController();
$recCtrl = new ReclamationController();

$id_rec = $_GET['id_rec'] ?? 0;
$reclamation = $recCtrl->getReclamationById($id_rec);

if(!$reclamation || $reclamation['id_citoyen'] != $_SESSION['user_id']) {
    header("Location: ../reclamation/mes-reclamations.php");
    exit();
}

$reponses = $reponseCtrl->getReponsesByReclamation($id_rec);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réponses à ma réclamation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-reply-all"></i>
            </div>
            <h1>Réponses à ma réclamation</h1>
            <p>Réf: <?= htmlspecialchars($reclamation['reference']) ?></p>
        </div>
        
        <div class="info-section" style="background: rgba(255,255,255,0.03); border-radius: 20px; padding: 20px; margin-bottom: 20px;">
            <h3>Objet: <?= htmlspecialchars($reclamation['objet']) ?></h3>
            <p>Statut: <span class="badge badge-<?= $reclamation['statut'] ?>"><?= ucfirst(str_replace('_', ' ', $reclamation['statut'])) ?></span></p>
        </div>
        
        <div class="info-section">
            <h3><i class="fas fa-comments"></i> Réponses (<?= count($reponses) ?>)</h3>
            <?php if(empty($reponses)): ?>
                <p>Aucune réponse pour l'instant.</p>
            <?php else: ?>
                <?php foreach($reponses as $rep): ?>
                <div style="background: rgba(255,255,255,0.02); border-radius: 15px; padding: 20px; margin-bottom: 15px; border-left: 3px solid #10b981;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                        <strong><i class="fas fa-user-shield"></i> Service: <?= htmlspecialchars($rep['service_agent'] ?? 'Administration') ?></strong>
                        <small><?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?></small>
                    </div>
                    <div style="margin: 15px 0;"><?= nl2br(htmlspecialchars($rep['contenu'])) ?></div>
                    <?php if($rep['decision']): ?>
                        <div style="padding: 10px; background: rgba(16,185,129,0.1); border-radius: 10px;">
                            <strong>Décision:</strong> <?= htmlspecialchars($rep['decision']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <a href="../reclamation/details.php?id=<?= $id_rec ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
    </div>
</body>
</html>