<?php
session_start();
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../CONTROLLER/ReponseController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$id_reclamation = $_GET['id_reclamation'] ?? 0;
$recCtrl = new ReclamationController();
$repCtrl = new ReponseController();

$reclamation = $recCtrl->getReclamationById($id_reclamation);
if(!$reclamation || $reclamation['id_citoyen'] != $_SESSION['user_id']){
    header("Location: ../RECLAMATION/mes-reclamations.php");
    exit();
}

$reponses = $repCtrl->getReponsesByReclamation($id_reclamation);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Conversation #<?= htmlspecialchars($reclamation['reference']) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5FCF9; color: #1A2E2A; }
        .navbar { background: white; padding: 1rem 2rem; box-shadow: 0 2px 10px rgba(0,0,0,0.05); position: sticky; top: 0; z-index: 100; }
        .container { max-width: 900px; margin: 40px auto; padding: 0 20px; }
        .chat-header { background: linear-gradient(135deg, #006D5B, #004D3D); color: white; padding: 25px; border-radius: 20px 20px 0 0; }
        .chat-body { background: white; padding: 30px; border-radius: 0 0 20px 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); min-height: 400px; }
        
        .message { margin-bottom: 25px; display: flex; flex-direction: column; max-width: 80%; }
        .message.user { align-self: flex-end; margin-left: auto; }
        .message.admin { align-self: flex-start; }
        
        .message-bubble { padding: 15px 20px; border-radius: 15px; font-size: 14px; line-height: 1.5; position: relative; }
        .user .message-bubble { background: #E6F4F0; color: #006D5B; border-bottom-right-radius: 2px; }
        .admin .message-bubble { background: #F3F4F6; color: #374151; border-bottom-left-radius: 2px; }
        
        .message-info { font-size: 11px; color: #9CA3AF; margin-top: 5px; display: flex; gap: 10px; }
        .user .message-info { justify-content: flex-end; }
        
        .reclamation-summary { background: #F9FAFB; border: 1px solid #E5E7EB; padding: 15px; border-radius: 12px; margin-bottom: 30px; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .status-traitee { background: #D1FAE5; color: #065F46; }
        
        .btn-back { display: inline-flex; align-items: center; gap: 8px; color: white; text-decoration: none; font-size: 14px; margin-bottom: 15px; opacity: 0.8; }
        .btn-back:hover { opacity: 1; }
    </style>
</head>
<body>

<nav class="navbar">
    <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; width: 100%;">
        <a href="../../../index.php" style="text-decoration: none; color: #006D5B; font-weight: 800; font-size: 22px;">InnoGov</a>
        <a href="../RECLAMATION/mes-reclamations.php" style="text-decoration: none; color: #2C5A4F; font-weight: 500;">Mes réclamations</a>
    </div>
</nav>

<div class="container">
    <a href="../RECLAMATION/mes-reclamations.php" class="btn-back" style="color: #006D5B;"><i class="fas fa-arrow-left"></i> Retour à la liste</a>
    
    <div class="chat-header">
        <h2>Conversation #<?= htmlspecialchars($reclamation['reference']) ?></h2>
        <p style="opacity: 0.8; font-size: 14px;"><?= htmlspecialchars($reclamation['objet']) ?></p>
    </div>
    
    <div class="chat-body">
        <div class="reclamation-summary">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                <span style="font-weight: 700; font-size: 13px; color: #006D5B;">MA RÉCLAMATION INITIALE</span>
                <span class="status-badge status-<?= $reclamation['statut'] ?>"><?= ucfirst($reclamation['statut']) ?></span>
            </div>
            <p style="font-size: 14px; color: #4B5563;"><?= nl2br(htmlspecialchars($reclamation['description'])) ?></p>
            <div class="message-info" style="margin-top: 10px;">
                <span><i class="far fa-calendar-alt"></i> <?= date('d/m/Y H:i', strtotime($reclamation['date_soumission'])) ?></span>
            </div>
        </div>

        <div style="display: flex; flex-direction: column;">
            <?php if(empty($reponses)): ?>
                <div style="text-align: center; padding: 40px; color: #9CA3AF;">
                    <i class="fas fa-clock fa-3x" style="margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>Aucune réponse pour le moment. Nos agents traitent votre demande.</p>
                </div>
            <?php else: ?>
                <?php foreach($reponses as $rep): ?>
                    <div class="message admin">
                        <div class="message-bubble">
                            <strong style="display: block; margin-bottom: 5px; color: #006D5B; font-size: 12px;">
                                <i class="fas fa-user-tie"></i> <?= htmlspecialchars($rep['nom_agent']) ?> (<?= htmlspecialchars($rep['service_agent'] ?? 'Agent InnoGov') ?>)
                            </strong>
                            <?= nl2br(htmlspecialchars($rep['contenu'])) ?>
                            <?php if($rep['decision']): ?>
                                <div style="margin-top: 10px; padding-top: 10px; border-top: 1px dashed #D1D5DB; font-weight: 600; font-size: 13px;">
                                    Décision : <?= htmlspecialchars($rep['decision']) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="message-info">
                            <span><?= date('d/m/Y H:i', strtotime($rep['date_reponse'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
