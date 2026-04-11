<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReponseController.php";
$ctrl = new ReponseController();

$id = $_GET['id'] ?? 0;
$reponse = $ctrl->getReponseById($id);

if(!$reponse) {
    header("Location: lister.php");
    exit();
}

$error = "";
$success = "";

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($ctrl->modifierReponse($id, $_POST['contenu'], $_POST['type_reponse'], $_POST['decision'] ?? null)) {
        $success = "Réponse modifiée avec succès";
        $reponse = $ctrl->getReponseById($id);
    } else {
        $error = "Erreur lors de la modification";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier réponse</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 600px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h1>Modifier la réponse</h1>
                <p>Réclamation: <?= htmlspecialchars($reponse['reference']) ?></p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Type de réponse</label>
                    <select name="type_reponse" required>
                        <option value="information" <?= $reponse['type_reponse'] == 'information' ? 'selected' : '' ?>>Information</option>
                        <option value="resolution" <?= $reponse['type_reponse'] == 'resolution' ? 'selected' : '' ?>>Résolution</option>
                        <option value="rejet" <?= $reponse['type_reponse'] == 'rejet' ? 'selected' : '' ?>>Rejet</option>
                        <option value="renvoi" <?= $reponse['type_reponse'] == 'renvoi' ? 'selected' : '' ?>>Renvoi</option>
                        <option value="cloture" <?= $reponse['type_reponse'] == 'cloture' ? 'selected' : '' ?>>Clôture</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Contenu</label>
                    <textarea name="contenu" rows="6" required><?= htmlspecialchars($reponse['contenu']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label>Décision</label>
                    <textarea name="decision" rows="3"><?= htmlspecialchars($reponse['decision'] ?? '') ?></textarea>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="lister.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>