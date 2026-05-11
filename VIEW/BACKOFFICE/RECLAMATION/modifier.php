<?php
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReclamationController();
$id = $_GET['id'] ?? 0;

if($id){
    $reclamation = $ctrl->getReclamationById($id);
    if(!$reclamation){
        header("Location: lister.php");
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
            .sidebar-nav { padding: 0 16px; }
            .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; font-size: 14px; font-weight: 500; }
            .sidebar-link:hover { background: rgba(255,255,255,0.08); color: white; }
            .sidebar-link.active { background: #006D5B; color: white; }
            .admin-main { flex: 1; margin-left: 280px; }
            .admin-topbar { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; position: sticky; top: 0; z-index: 99; }
            .admin-content { padding: 24px; }
            .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
            .card-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; }
            .card-body { padding: 20px 24px; }
            .form-group { margin-bottom: 20px; }
            .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; font-size: 14px; }
            .form-control { width: 100%; padding: 10px 14px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 14px; }
            .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; cursor: pointer; border: none; }
            .btn-primary { background: #006D5B; color: white; }
            .btn-warning { background: #F59E0B; color: white; }
            .alert { padding: 12px 16px; border-radius: 8px; margin-bottom: 20px; }
            .alert-success { background: #D1FAE5; color: #006D5B; }
            .alert-danger { background: #FEE2E2; color: #DC2626; }
        </style>
    </head>
    <body>
    <div style="display: flex; min-height: 100vh;">
        <aside class="admin-sidebar">
            <div class="sidebar-header"><div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div></div>
            <nav class="sidebar-nav">
                <a href="lister.php" class="sidebar-link"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
                <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
                <a href="../../../index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
            </nav>
        </aside>
        
        <main class="admin-main">
            <div class="admin-topbar">
                <div class="user-info"><i class="fas fa-user-circle"></i><span>Admin Système</span></div>
            </div>
            
            <div class="admin-content">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title"><i class="fas fa-edit"></i> Modifier la réclamation</h2>
                        <a href="lister.php" class="btn btn-warning btn-sm">← Retour</a>
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
                                <label class="form-label">Objet *</label>
                                <input type="text" name="objet" class="form-control" value="<?= htmlspecialchars($reclamation['objet']) ?>" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Description *</label>
                                <textarea name="description" class="form-control" rows="5" required><?= htmlspecialchars($reclamation['description']) ?></textarea>
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
                                <label class="form-label">Lieu</label>
                                <input type="text" name="lieu" class="form-control" value="<?= htmlspecialchars($reclamation['lieu']) ?>">
                            </div>
                            
                            <button type="submit" name="modifier" class="btn btn-primary">Enregistrer les modifications</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    </body>
    </html>
    <?php
} else {
    header("Location: lister.php");
}
?>
