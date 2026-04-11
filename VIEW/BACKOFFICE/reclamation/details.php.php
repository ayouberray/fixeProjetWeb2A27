<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
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

if(!$reclamation) {
    header("Location: lister.php");
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
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .info-label {
            width: 150px;
            font-weight: 600;
            color: rgba(255,255,255,0.6);
        }
        .info-value {
            flex: 1;
        }
        .response-item {
            background: rgba(255,255,255,0.02);
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 3px solid #10b981;
        }
        .stars {
            color: #f59e0b;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <h1>Détails de la réclamation</h1>
            <p>Réf: <?= htmlspecialchars($reclamation['reference']) ?></p>
        </div>
        
        <div class="info-section">
            <h3><i class="fas fa-info-circle"></i> Informations générales</h3>
            <div class="info-row">
                <div class="info-label">Citoyen:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['prenom'] . ' ' . $reclamation['nom']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['email'] ?? 'Non renseigné') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Téléphone:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['telephone'] ?? 'Non renseigné') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Catégorie:</div>
                <div class="info-value"><?= ucfirst($reclamation['categorie']) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Service:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['nom_service'] ?? 'Non spécifié') ?></div>
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
                <div class="info-label">Lieu:</div>
                <div class="info-value"><?= htmlspecialchars($reclamation['lieu'] ?? 'Non précisé') ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Date soumission:</div>
                <div class="info-value"><?= date('d/m/Y H:i', strtotime($reclamation['date_soumission'])) ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Dernière modification:</div>
                <div class="info-value"><?= $reclamation['date_modification'] ? date('d/m/Y H:i', strtotime($reclamation['date_modification'])) : 'Non modifiée' ?></div>
            </div>
        </div>
        
        <div class="info-section">
            <h3><i class="fas fa-heading"></i> Objet</h3>
            <p><?= htmlspecialchars($reclamation['objet']) ?></p>
        </div>
        
        <div class="info-section">
            <h3><i class="fas fa-align-left"></i> Description</h3>
            <p><?= nl2br(htmlspecialchars($reclamation['description'])) ?></p>
        </div>
        
        <?php if($reclamation['piece_jointe']): ?>
        <div class="info-section">
            <h3><i class="fas fa-paperclip"></i> Pièce jointe</h3>
            <a href="../../../uploads/<?= $reclamation['piece_jointe'] ?>" target="_blank" class="btn btn-secondary">
                <i class="fas fa-download"></i> Télécharger le fichier
            </a>
        </div>
        <?php endif; ?>
        
        <!-- Réponses -->
        <div class="info-section">
            <h3><i class="fas fa-reply-all"></i> Réponses (<?= count($reponses) ?>)</h3>
            <?php if(empty($reponses)): ?>
                <p style="color: rgba(255,255,255,0.5);">Aucune réponse pour l'instant.</p>
                <a href="../../reponse/ajouter.php?id_rec=<?= $id ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Répondre</a>
            <?php else: ?>
                <?php foreach($reponses as $rep): ?>
                <div class="response-item">
                    <div><strong><i class="fas fa-user"></i> <?= htmlspecialchars($rep['nom_agent']) ?></strong> 
                    <small style="color: rgba(255,255,255,0.4);">le <?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?></small></div>
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
            <h3><i class="fas fa-star"></i> Avis du citoyen</h3>
            <div class="stars">
                <?php for($i = 1; $i <= 5; $i++): ?>
                    <?= $i <= $avis['note'] ? '★' : '☆' ?>
                <?php endfor; ?>
            </div>
            <p><strong>Satisfaction:</strong> <?= ucfirst(str_replace('_', ' ', $avis['satisfaction'])) ?></p>
            <p><strong>Commentaire:</strong> <?= nl2br(htmlspecialchars($avis['commentaire'] ?? 'Aucun commentaire')) ?></p>
            <p><small>Date: <?= date('d/m/Y H:i', strtotime($avis['date_avis'])) ?></small></p>
        </div>
        <?php endif; ?>
        
        <div style="display: flex; gap: 15px; margin-top: 20px;">
            <a href="modifier.php?id=<?= $id ?>" class="btn btn-primary"><i class="fas fa-edit"></i> Modifier</a>
            <a href="../../reponse/ajouter.php?id_rec=<?= $id ?>" class="btn" style="background: #10b981;"><i class="fas fa-reply"></i> Répondre</a>
            <a href="lister.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
        </div>
    </div>
</body>
</html>