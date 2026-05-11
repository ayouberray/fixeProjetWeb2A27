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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Modifier la réponse</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; }
        
        .hero { position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden; }
        .hero-slideshow { position: absolute; top: 0; left: 0; width: 100%; height: 100%; }
        .hero-slideshow .slide { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease; }
        .hero-slideshow .slide.active { opacity: 1; }
        .hero-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,109,91,0.85) 0%, rgba(0,77,61,0.95) 100%); z-index: 1; }
        
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: rgba(13, 51, 40, 0.95); backdrop-filter: blur(10px); color: white; z-index: 100; overflow-y: auto; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; }
        .logo-mini i { font-size: 28px; color: #006D5B; }
        .sidebar-nav { padding: 20px 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar-link.active { background: #006D5B; color: white; }
        
        .admin-main { margin-left: 280px; padding: 30px; position: relative; z-index: 2; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 24px 48px rgba(0,0,0,0.2); padding: 30px; max-width: 700px; width: 100%; }
        .card-header { margin-bottom: 25px; }
        .card-header h2 { color: #006D5B; display: flex; align-items: center; gap: 10px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 600; color: #374151; }
        .form-control { width: 100%; padding: 12px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: #006D5B; box-shadow: 0 0 0 3px rgba(0,109,91,0.1); }
        textarea.form-control { resize: vertical; min-height: 120px; }
        
        .btn { padding: 12px 24px; border-radius: 8px; border: none; cursor: pointer; font-weight: 600; transition: all 0.2s; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-primary:hover { background: #004D3D; transform: translateY(-2px); }
        .btn-secondary { background: #F3F4F6; color: #374151; text-decoration: none; display: inline-block; text-align: center; margin-left: 10px; }
        
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; }
        .alert-success { background: #D1FAE5; color: #065F46; border-left: 4px solid #10B981; }
        .alert-danger { background: #FEE2E2; color: #991B1B; border-left: 4px solid #EF4444; }
        
        @media (max-width: 768px) { .admin-sidebar { transform: translateX(-100%); } .admin-main { margin-left: 0; } }
    </style>
</head>
<body>

<!-- SLIDESHOW BACKGROUND -->
<div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1; overflow: hidden;">
    <div class="hero-slideshow" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;">
        <div class="slide active" style="background-image: url('../../../ASSETS/images/tunisia1.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 1; transition: opacity 1.5s ease;"></div>
        <div class="slide" style="background-image: url('../../../ASSETS/images/tunisia2.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease;"></div>
        <div class="slide" style="background-image: url('../../../ASSETS/images/tunisia3.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease;"></div>
        <div class="slide" style="background-image: url('../../../ASSETS/images/tunisia4.jpg'); position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease;"></div>
    </div>
    <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg, rgba(0,109,91,0.85) 0%, rgba(0,77,61,0.95) 100%); z-index: 1;"></div>
</div>

<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div></div>
        <nav class="sidebar-nav">
            <a href="../RECLAMATION/lister.php" class="sidebar-link"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-reply"></i><span>Réponses</span></a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <div class="card">
            <div class="card-header">
                <h2><i class="fas fa-edit"></i> Modifier la réponse</h2>
            </div>
            
            <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
            
            <form method="POST" id="reponseForm" novalidate>
                <div class="form-group">
                    <label class="form-label">Type de réponse</label>
                    <select name="type_reponse" class="form-control">
                        <option value="information" <?= $reponse['type_reponse'] == 'information' ? 'selected' : '' ?>>📢 Information</option>
                        <option value="resolution" <?= $reponse['type_reponse'] == 'resolution' ? 'selected' : '' ?>>✅ Résolution</option>
                        <option value="rejet" <?= $reponse['type_reponse'] == 'rejet' ? 'selected' : '' ?>>❌ Rejet</option>
                        <option value="renvoi" <?= $reponse['type_reponse'] == 'renvoi' ? 'selected' : '' ?>>🔄 Renvoi</option>
                        <option value="cloture" <?= $reponse['type_reponse'] == 'cloture' ? 'selected' : '' ?>>🔒 Clôture</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contenu de la réponse</label>
                    <textarea name="contenu" id="contenu" class="form-control" rows="6"><?= htmlspecialchars($reponse['contenu']) ?></textarea>
                    <div class="error-message" id="contenuError">Le contenu doit contenir au moins 10 caractères</div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Décision (optionnel)</label>
                    <input type="text" name="decision" class="form-control" value="<?= htmlspecialchars($reponse['decision']) ?>">
                </div>
                
                <div>
                    <button type="submit" class="btn btn-primary" id="submitBtn"><i class="fas fa-save"></i> Enregistrer les modifications</button>
                    <a href="lister.php" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </main>
</div>

<script src="../../../ASSETS/JS/script.js"></script>
<script>
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.style.opacity = i === index ? '1' : '0';
            });
        }
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }, 3000);
    }
</script>
</body>
</html>