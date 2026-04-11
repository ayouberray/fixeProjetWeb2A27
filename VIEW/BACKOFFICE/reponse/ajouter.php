<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReponseController.php";
require_once "../../../CONTROLLER/ReclamationController.php";
require_once "../../../MODEL/Reponse.php";

$ctrl = new ReponseController();
$recCtrl = new ReclamationController();

$id_rec = $_GET['id_rec'] ?? 0;
$reclamation = $recCtrl->getReclamationById($id_rec);

if(!$reclamation) {
    header("Location: ../reclamation/lister.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $reponse = new Reponse(
        $id_rec,
        $_SESSION['user_nom'] ?? 'Admin',
        $_POST['contenu'],
        $_POST['type_reponse'],
        $_POST['service_agent'] ?? null,
        $_POST['decision'] ?? null
    );
    
    if($ctrl->ajouterReponse($reponse)) {
        // Mettre à jour le statut de la réclamation
        $recCtrl->modifierStatut($id_rec, 'traitee');
        $success = "Réponse ajoutée avec succès";
    } else {
        $error = "Erreur lors de l'ajout de la réponse";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Répondre à la réclamation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 700px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-reply"></i>
                </div>
                <h1>Répondre à la réclamation</h1>
                <p>Réf: <?= htmlspecialchars($reclamation['reference']) ?></p>
            </div>
            
            <div style="background: rgba(255,255,255,0.05); border-radius: 15px; padding: 15px; margin-bottom: 20px;">
                <p><strong>Objet:</strong> <?= htmlspecialchars($reclamation['objet']) ?></p>
                <p><strong>Citoyen:</strong> <?= htmlspecialchars($reclamation['prenom'] . ' ' . $reclamation['nom']) ?></p>
                <p><strong>Description:</strong> <?= nl2br(htmlspecialchars(substr($reclamation['description'], 0, 200))) ?>...</p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Type de réponse</label>
                        <select name="type_reponse" required>
                            <option value="information">Information</option>
                            <option value="resolution">Résolution</option>
                            <option value="rejet">Rejet</option>
                            <option value="renvoi">Renvoi</option>
                            <option value="cloture">Clôture</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Service agent</label>
                        <input type="text" name="service_agent" placeholder="Votre service">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Contenu de la réponse *</label>
                    <textarea name="contenu" rows="6" placeholder="Votre réponse détaillée..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-gavel"></i> Décision (optionnel)</label>
                    <textarea name="decision" rows="3" placeholder="Décision prise concernant cette réclamation..."></textarea>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer la réponse</button>
                    <a href="../reclamation/details.php?id=<?= $id_rec ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>