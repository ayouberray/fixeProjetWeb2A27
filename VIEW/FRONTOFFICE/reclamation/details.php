<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
require_once "../../../CONTROLLER/ReponseController.php";
require_once "../../../CONTROLLER/AvisController.php";

$ctrl = new ReclamationController();
$reponseCtrl = new ReponseController();
$avisCtrl = new AvisController();

$id = $_GET['id'] ?? 0;
$reclamation = $ctrl->getReclamationById($id);

if(!$reclamation || $reclamation['id_citoyen'] != $_SESSION['user_id']) {
    header("Location: mes-reclamations.php");
    exit();
}

$reponses = $reponseCtrl->getReponsesByReclamation($id);
$avis = $avisCtrl->getAvisByReclamation($id);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Détails réclamation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
    <style>
        .info-section {
            background: rgba(255,255,255,0.03);
            border-radius: 20px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            padding: 10px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .info-label {
            width: 130px;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
        }
        .response-item {
            background: rgba(255,255,255,0.02);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid #10b981;
        }
        .stars { color: #f59e0b; font-size: 24px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h1>Détails de ma réclamation</h1>
            <p>Réf: <?= htmlspecialchars($reclamation['reference']) ?></p>
        </div>
        
        <div class="info-section">
            <h3><i class="fas fa-info-circle"></i> Informations</h3>
            <div class="info-row">
                <div class="info-label">Objet:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['objet']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Catégorie:</div>
                <div class="info-value"><?= ucfirst($reclamation['categorie']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Priorité:</div>
                <div class="info-value priority-<?= $reclamation['priorite'] == 'urgente' || $reclamation['priorite'] == 'haute' ? 'high' : ($reclamation['priorite'] == 'normale' ? 'normal' : 'low') ?>">
                    <?= ucfirst($reclamation['priorite']) ?>
                </div>
            </div>
            <div class="info-row">
                <div class="info-label">Statut:</div>
                <div class="info-value"><span class="badge badge-<?= $reclamation['statut'] ?>"><?= ucfirst(str_replace('_', ' ', $reclamation['statut'])) ?></span></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date:</div>
                <div class="info-value"><?= date('d/m/Y H:i', strtotime($reclamation['date_soumission'])) ?></div>
            </div>
            <?php if($reclamation['lieu']): ?>
            <div class="info-row">
                <div class="info-label">Lieu:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['lieu']) ?></div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="info-section">
            <h3><i class="fas fa-align-left"></i> Description</h3>
            <p><?= nl2br(htmlspecialchars($reclamation['description'])) ?></p>
        </div>
        
        <!-- Réponses -->
        <div class="info-section">
            <h3><i class="fas fa-reply-all"></i> Réponses (<?= count($reponses) ?>)</h3>
            <?php if(empty($reponses)): ?>
                <p style="color: rgba(255,255,255,0.5);">Aucune réponse pour l'instant.</p>
            <?php else: ?>
                <?php foreach($reponses as $rep): ?>
                <div class="response-item">
                    <div><strong><i class="fas fa-user"></i> Service: <?= htmlspecialchars($rep['service_agent'] ?? 'Administration') ?></strong> 
                    <small>le <?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?></small></div>
                    <div style="margin-top: 10px;"><?= nl2br(htmlspecialchars($rep['contenu'])) ?></div>
                    <?php if($rep['decision']): ?>
                        <div style="margin-top: 10px; padding: 10px; background: rgba(16,185,129,0.1); border-radius: 10px;">
                            <strong>Décision:</strong> <?= htmlspecialchars($rep['decision']) ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <!-- Avis -->
        <?php if($avis): ?>
        <div class="info-section">
            <h3><i class="fas fa-star"></i> Mon avis</h3>
            <div class="stars">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?= $i <= $avis['note'] ? '★' : '☆' ?>
                <?php endfor; ?>
            </div>
            <p><strong>Satisfaction:</strong> <?= ucfirst(str_replace('_', ' ', $avis['satisfaction'])) ?></p>
            <p><strong>Commentaire:</strong> <?= nl2br(htmlspecialchars($avis['commentaire'] ?? 'Aucun commentaire')) ?></p>
        </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 15px;">
            <a href="mes-reclamations.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
            <?php if($reclamation['statut'] == 'traitee' && !$avis): ?>
                <a href="avis.php?id=<?= $id ?>" class="btn" style="background: #f59e0b;"><i class="fas fa-star"></i> Donner mon avis</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>