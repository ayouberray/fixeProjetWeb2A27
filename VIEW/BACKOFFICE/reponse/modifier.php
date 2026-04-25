<?php
require_once __DIR__ . "/../../../CONTROLLER/ReponseController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReponseController();
$id = $_GET['id'] ?? 0;
$reponse = $ctrl->getReponseById($id);

if(!$reponse){
    header("Location: lister.php");
    exit();
}

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $contenu = $_POST['contenu'];
    $type_reponse = $_POST['type_reponse'];
    $decision = $_POST['decision'] ?? null;
    
    if(!empty($contenu)){
        $result = $ctrl->modifierReponse($id, $contenu, $type_reponse, $decision);
        if($result){ 
            $success = "Réponse mise à jour avec succès !";
            $reponse = $ctrl->getReponseById($id);
        } else { 
            $error = "Erreur lors de la mise à jour"; 
        }
    } else {
        $error = "Le contenu ne peut pas être vide";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier la réponse</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; }
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #0D3328 0%, #0A281E 100%); color: white; z-index: 100; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; }
        .sidebar-nav { padding: 20px 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; }
        .sidebar-link.active { background: #006D5B; color: white; }
        .admin-main { margin-left: 280px; padding: 24px; }
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); padding: 24px; }
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; }
        .btn { padding: 10px 20px; border-radius: 8px; border: none; cursor: pointer; font-weight: 500; }
        .btn-primary { background: #006D5B; color: white; }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #D1FAE5; color: #065F46; }
        .alert-danger { background: #FEE2E2; color: #991B1B; }
    </style>
</head>
<body>

<div style="display: flex;">
    <aside class="admin-sidebar">
        <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div></div>
        <nav class="sidebar-nav">
            <a href="../RECLAMATION/lister.php" class="sidebar-link"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-reply"></i><span>Réponses</span></a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <div class="card">
            <h2 style="margin-bottom: 20px;">Modifier la réponse pour #<?= htmlspecialchars($reponse['ref_reclamation']) ?></h2>
            
            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            
            <form method="POST" id="reponseForm">
                <div class="form-group">
                    <label class="form-label">Type de réponse</label>
                    <select name="type_reponse" class="form-control">
                        <option value="information" <?= $reponse['type_reponse'] == 'information' ? 'selected' : '' ?>>Information</option>
                        <option value="rejet" <?= $reponse['type_reponse'] == 'rejet' ? 'selected' : '' ?>>Rejet</option>
                        <option value="cloture" <?= $reponse['type_reponse'] == 'cloture' ? 'selected' : '' ?>>Clôture</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Contenu de la réponse</label>
                    <textarea name="contenu" class="form-control" rows="6" id="contenu"><?= htmlspecialchars($reponse['contenu']) ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Décision (optionnel)</label>
                    <input type="text" name="decision" class="form-control" value="<?= htmlspecialchars($reponse['decision']) ?>">
                </div>
                <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                <a href="lister.php" style="margin-left: 10px; text-decoration: none; color: #6B7280;">Annuler</a>
            </form>
        </div>
    </main>
</div>

<script src="../../../ASSETS/JS/script.js"></script>
</body>
</html>
