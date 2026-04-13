<?php
require_once "C:/xampp/htdocs/PROJETFIXE/config.php";
require_once CONTROLLER_PATH . "ReclamationController.php";
require_once MODEL_PATH . "config.php";

$ctrl = new ReclamationController();
$id = $_GET['id'] ?? 0;

// ========== ÉTAPE 2 : AFFICHER LE FORMULAIRE DE MODIFICATION ==========
if($id){
    $reclamation = $ctrl->getReclamationById($id);
    if(!$reclamation){
        header("Location: modifier.php");
        exit();
    }
    
    $citoyens = $ctrl->getAllCitoyens();
    $db = Config::getConnexion();
    $services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();
    
    $error = ""; $success = "";
    
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['modifier'])){
        $id_citoyen = $_POST['id_citoyen'];
        $id_service = !empty($_POST['id_service']) ? $_POST['id_service'] : null;
        $categorie = $_POST['categorie'];
        $objet = $_POST['objet'];
        $description = $_POST['description'];
        $priorite = $_POST['priorite'];
        $statut = $_POST['statut'];
        $lieu = $_POST['lieu'] ?? null;
        
        $result = $ctrl->adminModifierReclamation($id, $id_citoyen, $id_service, $categorie, $objet, $description, $priorite, $statut, $lieu);
        if($result){ 
            $success = "Réclamation modifiée avec succès !";
            $reclamation = $ctrl->getReclamationById($id);
        } else { 
            $error = "Erreur lors de la modification"; 
        }
    }
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Modifier réclamation</title>
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
            .form-control { width: 100%; padding: 10px 14px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; }
            .form-control:focus { outline: none; border-color: #006D5B; box-shadow: 0 0 0 3px rgba(0,109,91,0.1); }
            textarea.form-control { resize: vertical; min-height: 100px; }
            
            .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
            .btn-primary { background: #006D5B; color: white; }
            .btn-primary:hover { background: #004D3D; }
            .btn-warning { background: #F59E0B; color: white; }
            
            .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
            .alert-success { background: #D1FAE5; color: #006D5B; border-left: 3px solid #006D5B; }
            .alert-danger { background: #FEE2E2; color: #DC2626; border-left: 3px solid #DC2626; }
            
            .badge { display: inline-flex; padding: 4px 10px; border-radius: 30px; font-size: 12px; font-weight: 600; }
            
            @media (max-width: 768px) { .admin-sidebar { transform: translateX(-100%); } .admin-sidebar.open { transform: translateX(0); } .admin-main { margin-left: 0; } .menu-toggle-btn { display: block; } }
        </style>
    </head>
    <body>
    <div style="display: flex; min-height: 100vh;">
        <aside class="admin-sidebar" id="sidebar">
            <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div><p class="sidebar-subtitle">Administration</p></div>
            <nav class="sidebar-nav">
                <a href="lister.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
                <div class="sidebar-divider">GESTION</div>
                <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
                <div class="sidebar-divider">SYSTÈME</div>
                <a href="/PROJETFIXE/index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
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
                        <h2 class="card-title"><i class="fas fa-edit"></i> Modifier la réclamation</h2>
                        <a href="modifier.php" class="btn btn-warning btn-sm">← Choisir une autre réclamation</a>
                    </div>
                    <div class="card-body">
                        <div style="background: #F0FDF4; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; border-left: 3px solid #006D5B;">
                            <strong>Référence :</strong> <?= htmlspecialchars($reclamation['reference']) ?>
                        </div>
                        
                        <?php if($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
                        <?php if($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>
                        
                        <form method="POST">
                            <div class="form-group">
                                <label class="form-label">Citoyen *</label>
                                <select name="id_citoyen" class="form-control" required>
                                    <?php foreach($citoyens as $c): ?>
                                        <option value="<?= $c['id_citoyen'] ?>" <?= ($c['id_citoyen'] == $reclamation['id_citoyen']) ? 'selected' : '' ?>>
                                            <?= $c['nom'].' '.$c['prenom'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Service</label>
                                <select name="id_service" class="form-control">
                                    <option value="">-- Non spécifié --</option>
                                    <?php foreach($services as $s): ?>
                                        <option value="<?= $s['id_service'] ?>" <?= ($s['id_service'] == $reclamation['id_service']) ? 'selected' : '' ?>>
                                            <?= $s['nom_service'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Catégorie *</label>
                                <select name="categorie" class="form-control" required>
                                    <option value="administrative" <?= $reclamation['categorie']=='administrative' ? 'selected' : '' ?>>Administrative</option>
                                    <option value="sociale" <?= $reclamation['categorie']=='sociale' ? 'selected' : '' ?>>Sociale</option>
                                    <option value="infrastructure" <?= $reclamation['categorie']=='infrastructure' ? 'selected' : '' ?>>Infrastructure</option>
                                    <option value="sante" <?= $reclamation['categorie']=='sante' ? 'selected' : '' ?>>Santé</option>
                                    <option value="education" <?= $reclamation['categorie']=='education' ? 'selected' : '' ?>>Éducation</option>
                                    <option value="transport" <?= $reclamation['categorie']=='transport' ? 'selected' : '' ?>>Transport</option>
                                    <option value="environnement" <?= $reclamation['categorie']=='environnement' ? 'selected' : '' ?>>Environnement</option>
                                    <option value="autre" <?= $reclamation['categorie']=='autre' ? 'selected' : '' ?>>Autre</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Statut *</label>
                                <select name="statut" class="form-control" required>
                                    <option value="soumise" <?= $reclamation['statut']=='soumise' ? 'selected' : '' ?>>Soumise</option>
                                    <option value="en_cours" <?= $reclamation['statut']=='en_cours' ? 'selected' : '' ?>>En cours</option>
                                    <option value="traitee" <?= $reclamation['statut']=='traitee' ? 'selected' : '' ?>>Traitée</option>
                                    <option value="rejetee" <?= $reclamation['statut']=='rejetee' ? 'selected' : '' ?>>Rejetée</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Priorité *</label>
                                <select name="priorite" class="form-control" required>
                                    <option value="faible" <?= $reclamation['priorite']=='faible' ? 'selected' : '' ?>>Faible</option>
                                    <option value="normale" <?= $reclamation['priorite']=='normale' ? 'selected' : '' ?>>Normale</option>
                                    <option value="haute" <?= $reclamation['priorite']=='haute' ? 'selected' : '' ?>>Haute</option>
                                    <option value="urgente" <?= $reclamation['priorite']=='urgente' ? 'selected' : '' ?>>Urgente</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Objet *</label>
                                <input type="text" name="objet" class="form-control" value="<?= htmlspecialchars($reclamation['objet']) ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($reclamation['description']) ?></textarea>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Lieu</label>
                                <input type="text" name="lieu" class="form-control" value="<?= htmlspecialchars($reclamation['lieu'] ?? '') ?>">
                            </div>
                            
                            <button type="submit" name="modifier" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
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
    <?php
    exit();
}

// ========== ÉTAPE 1 : AFFICHER LA LISTE DES RÉCLAMATIONS À MODIFIER ==========
$reclamations = $ctrl->getAllReclamations();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choisir une réclamation à modifier</title>
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
        
        .table-wrapper { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 12px 16px; background: #F9FAFB; color: #6B7280; font-weight: 600; font-size: 13px; }
        .table td { padding: 12px 16px; border-bottom: 1px solid #F0F0F0; font-size: 14px; }
        .table tr:hover td { background: #F9FAFB; }
        
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-primary:hover { background: #004D3D; }
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .badge-soumise { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traitee { background: #D1FAE5; color: #006D5B; }
        
        @media (max-width: 768px) { .admin-sidebar { transform: translateX(-100%); } .admin-sidebar.open { transform: translateX(0); } .admin-main { margin-left: 0; } .menu-toggle-btn { display: block; } }
    </style>
</head>
<body>
<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div><p class="sidebar-subtitle">Administration</p></div>
        <nav class="sidebar-nav">
            <a href="lister.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            <div class="sidebar-divider">GESTION</div>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <div class="sidebar-divider">SYSTÈME</div>
            <a href="/PROJETFIXE/index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
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
                    <h2 class="card-title"><i class="fas fa-edit"></i> Choisir une réclamation à modifier</h2>
                    <a href="lister.php" class="btn btn-primary btn-sm">← Retour</a>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="table">
                            <thead><tr><th>ID</th><th>Réf.</th><th>Citoyen</th><th>Objet</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php foreach($reclamations as $r): ?>
                                <tr>
                                    <td>#<?= $r['id_reclamation'] ?></td>
                                    <td><?= htmlspecialchars($r['reference']) ?></td>
                                    <td><?= htmlspecialchars($r['citoyen']) ?></td>
                                    <td><?= htmlspecialchars(substr($r['objet'], 0, 40)) ?>...</td>
                                    <td><span class="badge badge-<?= $r['statut'] ?>"><?= $r['statut'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                                    <td><a href="modifier.php?id=<?= $r['id_reclamation'] ?>" class="btn btn-primary btn-sm">Modifier</a></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
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
