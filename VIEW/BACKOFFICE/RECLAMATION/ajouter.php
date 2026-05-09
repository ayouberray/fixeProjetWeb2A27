<?php
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReclamationController();
$citoyens = $ctrl->getAllCitoyens();
$db = Config::getConnexion();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_citoyen = $_POST['id_citoyen'];
    $id_service = !empty($_POST['id_service']) ? $_POST['id_service'] : null;
    $categorie = $_POST['categorie'];
    $objet = $_POST['objet'];
    $description = $_POST['description'];
    $priorite = $_POST['priorite'];
    $lieu = $_POST['lieu'] ?? null;
    
    if(!empty($id_citoyen) && !empty($categorie) && !empty($objet) && !empty($description)){
        $result = $ctrl->adminAjouterReclamation($id_citoyen, $id_service, $categorie, $objet, $description, $priorite, $lieu);
        if($result){ $success = "Réclamation ajoutée ! ID: #".$result; }
        else { $error = "Erreur lors de l'ajout"; }
    } else { $error = "Veuillez remplir tous les champs obligatoires"; }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Ajouter réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; }
        
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #0D3328 0%, #0A281E 100%); color: white; z-index: 100; overflow-y: auto; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; font-family: 'Syne', sans-serif; }
        .logo-mini i { font-size: 28px; color: #006D5B; }
        .sidebar-subtitle { font-size: 12px; color: rgba(255,255,255,0.4); margin-top: 8px; }
        .sidebar-nav { padding: 0 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; font-size: 14px; font-weight: 500; }
        .sidebar-link i { width: 20px; font-size: 16px; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar-link.active { background: #006D5B; color: white; }
        .sidebar-divider { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3); padding: 16px 16px 8px; margin-top: 8px; }
        
        .admin-main { flex: 1; margin-left: 280px; }
        .admin-topbar { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; position: sticky; top: 0; z-index: 99; }
        .menu-toggle-btn { display: none; background: none; border: none; font-size: 20px; cursor: pointer; color: #006D5B; }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 8px 16px; background: #F3F4F6; border-radius: 30px; font-size: 14px; font-weight: 500; }
        .user-info i { font-size: 20px; color: #006D5B; }
        .admin-content { padding: 24px; }
        
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .card-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 20px 24px; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; font-size: 14px; }
        .form-control { width: 100%; padding: 10px 14px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; transition: all 0.2s; }
        .form-control:focus { outline: none; border-color: #006D5B; box-shadow: 0 0 0 3px rgba(0,109,91,0.1); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-primary:hover { background: #004D3D; }
        .btn-warning { background: #F59E0B; color: white; }
        
        .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #D1FAE5; color: #006D5B; border-left: 3px solid #006D5B; }
        .alert-danger { background: #FEE2E2; color: #DC2626; border-left: 3px solid #DC2626; }
        
        @media (max-width: 768px) { .admin-sidebar { transform: translateX(-100%); } .admin-sidebar.open { transform: translateX(0); } .admin-main { margin-left: 0; } .menu-toggle-btn { display: block; } }
    </style>
</head>
<body>

<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div>
            <p class="sidebar-subtitle">Administration</p>
        </div>
        <nav class="sidebar-nav">
            <a href="lister.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            <div class="sidebar-divider">GESTION</div>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <div class="sidebar-divider">SYSTÈME</div>
            <a href="../../../index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle-btn" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div class="user-info"><i class="fas fa-user-circle"></i><span>Admin Système</span></div>
        </div>
        
        <div class="admin-content">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-plus-circle"></i> Ajouter une réclamation</h2>
                    <a href="lister.php" class="btn btn-warning btn-sm">← Retour</a>
                </div>
                <div class="card-body">
                    <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                    <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label class="form-label">Citoyen *</label>
                            <select name="id_citoyen" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <?php foreach($citoyens as $c): ?>
                                    <option value="<?= $c['id_citoyen'] ?>"><?= $c['nom'].' '.$c['prenom'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Service</label>
                            <select name="id_service" class="form-control">
                                <option value="">-- Non spécifié --</option>
                                <?php foreach($services as $s): ?>
                                    <option value="<?= $s['id_service'] ?>"><?= $s['nom_service'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Catégorie *</label>
                            <select name="categorie" class="form-control" required>
                                <option value="">-- Choisir --</option>
                                <option value="administrative">Administrative</option>
                                <option value="sociale">Sociale</option>
                                <option value="infrastructure">Infrastructure</option>
                                <option value="sante">Santé</option>
                                <option value="education">Éducation</option>
                                <option value="transport">Transport</option>
                                <option value="environnement">Environnement</option>
                                <option value="autre">Autre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Objet *</label>
                            <input type="text" name="objet" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Description *</label>
                            <textarea name="description" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Priorité *</label>
                            <select name="priorite" class="form-control" required>
                                <option value="faible">Faible</option>
                                <option value="normale" selected>Normale</option>
                                <option value="haute">Haute</option>
                                <option value="urgente">Urgente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Lieu</label>
                            <input type="text" name="lieu" class="form-control" placeholder="Adresse, quartier...">
                        </div>
                        <button type="submit" class="btn btn-primary">Enregistrer</button>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => { sidebar.classList.toggle('open'); });
    }
</script>

</body>
</html>
