<?php
session_start();
require_once __DIR__."/../../CONTROLLER/ServiceController.php";

$serviceController = new ServiceController();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    if(isset($_POST['action']) && $_POST['action'] == 'ajouter'){
        $nom_service = trim($_POST['nom_service']);
        $description = trim($_POST['description']);
        $duree_moyenne = $_POST['duree_moyenne'];
        
        if(!empty($nom_service)){
            $service = new Service($nom_service, $description, $duree_moyenne);
            $result = $serviceController->ajouterService($service);
            if($result){
                $success = "Service ajouté avec succès !";
                header("refresh:1;url=/projet/VIEW/backoffice/admin-services.php");
            } else {
                $error = "Erreur lors de l'ajout";
            }
        } else {
            $error = "Veuillez saisir un nom de service";
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'modifier'){
        $id_service = $_POST['id_service'];
        $nom_service = trim($_POST['nom_service']);
        $description = trim($_POST['description']);
        $duree_moyenne = $_POST['duree_moyenne'];
        $statut = $_POST['statut'];
        
        $result = $serviceController->modifierService($id_service, $nom_service, $description, $duree_moyenne, $statut);
        if($result){
            $success = "Service modifié avec succès !";
            header("refresh:1;url=/projet/VIEW/backoffice/admin-services.php");
        } else {
            $error = "Erreur lors de la modification";
        }
    }
    
    if(isset($_POST['action']) && $_POST['action'] == 'supprimer'){
        $id_service = $_POST['id_service'];
        $result = $serviceController->supprimerService($id_service);
        if($result){
            $success = "Service supprimé avec succès !";
            header("refresh:1;url=/projet/VIEW/backoffice/admin-services.php");
        } else {
            $error = "Impossible de supprimer ce service (des rendez-vous y sont associés)";
        }
    }
}

$services = $serviceController->getAllServices();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestion des services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hero { display: none; }
        
        .futuristic-container { max-width: 1200px; margin: 40px auto; position: relative; z-index: 1; padding: 0 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 1.8rem; color: #1e293b; font-weight: 700; margin: 0; }
        
        .cyber-card {
            background: #ffffff; border-radius: 12px;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 40px; position: relative; overflow: hidden; margin-bottom: 30px;
        }

        .btn-cyber-sm {
            background: var(--primary); color: white; border: none;
            padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; text-decoration: none;
        }
        .btn-cyber-sm:hover { background: var(--primary-dark); }

        .cyber-table { width: 100%; border-collapse: collapse; }
        .cyber-table th { background: var(--primary); color: white; font-weight: 600; text-transform: uppercase; font-size: 13px; padding: 12px 15px; text-align: left; }
        .cyber-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .cyber-table tr:last-child td { border-bottom: none; }
        .cyber-table tr:hover td { background: #f8fafc; }

        .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; transition: 0.2s; display: inline-block; margin-right: 5px; border: 1px solid transparent; }
        .btn-edit { background: #ffffff; color: var(--primary); border-color: var(--primary); }
        .btn-edit:hover { background: #f8fafc; }
        .btn-delete { background: #ffffff; color: #dc3545; border-color: #dc3545; }
        .btn-delete:hover { background: #f8fafc; }

        /* Modals */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px); z-index: 1000; align-items: center; justify-content: center; }
        .form-floating { position: relative; margin-bottom: 25px; }
        .futuristic-input {
            width: 100%; padding: 14px 15px; font-size: 15px; background: #ffffff; border: 1px solid #cbd5e1;
            border-radius: 8px; color: #1e293b; transition: all 0.2s ease; box-sizing: border-box; outline: none;
        }
        .futuristic-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(8, 84, 64, 0.1); }
        .futuristic-label {
            position: absolute; top: -10px; left: 15px; background: white; padding: 0 8px; font-size: 12px;
            font-weight: 600; color: var(--primary); border-radius: 4px; letter-spacing: 0.5px;
        }
        .error-message { color: #dc3545; font-size: 13px; margin-top: 5px; display: none; font-weight: 600; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <img src="/projet/assets/images/innogov-logo.png" alt="InnoGov" class="logo-img">
                <div class="logo-text">
                    <p class="logo-subtitle">Administration</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Rendez-vous</a>
            <a href="/projet/VIEW/backoffice/admin-services.php" class="nav-link active">Services</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
        </div>
    </div>
</nav>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-server" style="color: var(--primary);"></i> Liste des Services</h2>
        <button class="btn-cyber-sm" onclick="openModal('addModal')"><i class="fas fa-plus"></i> Nouveau Service</button>
    </div>
        
    <?php if(isset($error) && $error): ?><div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div><?php endif; ?>
    <?php if(isset($success) && $success): ?><div class="alert alert-success" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
        
    <div class="cyber-card reveal">
        <div style="overflow-x: auto;">
            <table class="cyber-table">
                <thead>
                    <tr><th>ID</th><th>Nom du service</th><th>Description</th><th>Durée</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if(!empty($services)): ?>
                        <?php foreach($services as $s): ?>
                        <tr>
                            <td><strong>#<?= $s['id_service'] ?></strong></td>
                            <td><i class="fas fa-tag" style="color: #22c55e; margin-right: 5px;"></i> <?= htmlspecialchars($s['nom_service']) ?></td>
                            <td style="color: #64748b; font-size: 14px;"><?= htmlspecialchars(substr($s['description'] ?? '', 0, 60)) ?><?= strlen($s['description'] ?? '') > 60 ? '...' : '' ?></td>
                            <td><i class="fas fa-clock" style="color: #cbd5e1;"></i> <?= $s['duree_moyenne'] ?> min</td>
                            <td><span class="badge badge-<?= $s['statut'] ?>"><?= $s['statut'] == 'actif' ? 'Actif' : 'Inactif' ?></span></td>
                            <td>
                                <button class="btn-action btn-edit" onclick="openEditModal(<?= $s['id_service'] ?>, '<?= htmlspecialchars($s['nom_service']) ?>', '<?= htmlspecialchars(addslashes($s['description'] ?? '')) ?>', <?= $s['duree_moyenne'] ?>, '<?= $s['statut'] ?>')" title="Modifier"><i class="fas fa-edit"></i></button>
                                <button class="btn-action btn-delete" onclick="openDeleteModal(<?= $s['id_service'] ?>, '<?= htmlspecialchars(addslashes($s['nom_service'])) ?>')" title="Supprimer"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center; padding: 40px; color: #64748b;"><i class="fas fa-folder-open" style="font-size: 40px; margin-bottom: 10px; display: block; color: #cbd5e1;"></i> Aucun service trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL AJOUT -->
<div id="addModal" class="modal">
    <div class="cyber-card" style="max-width:500px; width:90%; padding: 30px;">
        <h3 style="color: #166534; font-weight: 800; margin-top: 0; margin-bottom: 25px;"><i class="fas fa-plus"></i> Ajouter un service</h3>
        <form id="addForm" method="POST" onsubmit="return validerAjout(event)">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-floating">
                <label class="futuristic-label">Nom du service *</label>
                <input type="text" name="nom_service" id="add_nom" class="futuristic-input">
                <div class="error-message" id="add_nom_error"><i class="fas fa-exclamation-circle"></i> Veuillez saisir un nom.</div>
            </div>
            <div class="form-floating">
                <label class="futuristic-label">Description</label>
                <textarea name="description" class="futuristic-input" rows="3"></textarea>
            </div>
            <div class="form-floating">
                <label class="futuristic-label">Durée moyenne (min)</label>
                <input type="number" name="duree_moyenne" id="add_duree" class="futuristic-input" value="30">
                <div class="error-message" id="add_duree_error"><i class="fas fa-exclamation-circle"></i> Durée invalide.</div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" class="btn-action" style="background: #e2e8f0; color: #475569;" onclick="closeModal('addModal')">Annuler</button>
                <button type="submit" class="btn-cyber-sm" style="margin: 0;"><i class="fas fa-save"></i> Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div id="editModal" class="modal">
    <div class="cyber-card" style="max-width:500px; width:90%; padding: 30px;">
        <h3 style="color: #166534; font-weight: 800; margin-top: 0; margin-bottom: 25px;"><i class="fas fa-edit"></i> Modifier le service</h3>
        <form id="editForm" method="POST" onsubmit="return validerModification(event)">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id_service" id="edit_id">
            <div class="form-floating">
                <label class="futuristic-label">Nom du service *</label>
                <input type="text" name="nom_service" id="edit_nom" class="futuristic-input">
                <div class="error-message" id="edit_nom_error"><i class="fas fa-exclamation-circle"></i> Veuillez saisir un nom.</div>
            </div>
            <div class="form-floating">
                <label class="futuristic-label">Description</label>
                <textarea name="description" id="edit_description" class="futuristic-input" rows="3"></textarea>
            </div>
            <div class="form-floating" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <div style="position: relative;">
                    <label class="futuristic-label">Durée (min)</label>
                    <input type="number" name="duree_moyenne" id="edit_duree" class="futuristic-input">
                    <div class="error-message" id="edit_duree_error"><i class="fas fa-exclamation-circle"></i> Durée invalide.</div>
                </div>
                <div style="position: relative;">
                    <label class="futuristic-label">Statut</label>
                    <select name="statut" id="edit_statut" class="futuristic-input">
                        <option value="actif">Actif</option>
                        <option value="inactif">Inactif</option>
                    </select>
                </div>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 25px;">
                <button type="button" class="btn-action" style="background: #e2e8f0; color: #475569;" onclick="closeModal('editModal')">Annuler</button>
                <button type="submit" class="btn-cyber-sm" style="margin: 0; background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);"><i class="fas fa-save"></i> Modifier</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL SUPPRESSION -->
<div id="deleteModal" class="modal">
    <div class="cyber-card" style="max-width:400px; padding: 30px; text-align: center;">
        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ef4444; margin-bottom: 20px;"></i>
        <h3 style="color: #1e293b; margin-top: 0;">Confirmer la suppression</h3>
        <p style="color: #64748b; margin-bottom: 20px;">Voulez-vous vraiment supprimer le service <strong id="delete_nom" style="color: #0f172a;"></strong> ?</p>
        <p style="color: #ef4444; font-size: 13px; font-weight: 600; margin-bottom: 25px; padding: 10px; background: #fee2e2; border-radius: 8px;">⚠️ Attention : Un service avec des rendez-vous ne peut pas être supprimé.</p>
        <form method="POST">
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="id_service" id="delete_id">
            <div style="display: flex; gap: 10px; justify-content: center;">
                <button type="button" class="btn-action" style="background: #e2e8f0; color: #475569; padding: 12px 20px;" onclick="closeModal('deleteModal')">Annuler</button>
                <button type="submit" class="btn-action btn-delete" style="padding: 12px 20px;"><i class="fas fa-trash"></i> Supprimer</button>
            </div>
        </form>
    </div>
</div>

<script>
    // -------- Gestion des modales --------
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
        // Effacer les messages d'erreur à l'ouverture
        document.querySelectorAll('.error-message').forEach(function(el) {
            el.style.display = 'none';
        });
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // -------- Validation Ajout --------
    function validerAjout(event) {
        let valid = true;
        const nom = document.getElementById('add_nom').value.trim();
        const duree = parseInt(document.getElementById('add_duree').value);
        
        // Cacher les anciens messages
        document.getElementById('add_nom_error').style.display = 'none';
        document.getElementById('add_duree_error').style.display = 'none';
        
        if (nom === '') {
            document.getElementById('add_nom_error').style.display = 'block';
            valid = false;
        }
        if (isNaN(duree) || duree <= 0) {
            document.getElementById('add_duree_error').style.display = 'block';
            valid = false;
        }
        
        if (!valid) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // -------- Validation Modification --------
    function validerModification(event) {
        let valid = true;
        const nom = document.getElementById('edit_nom').value.trim();
        const duree = parseInt(document.getElementById('edit_duree').value);
        
        document.getElementById('edit_nom_error').style.display = 'none';
        document.getElementById('edit_duree_error').style.display = 'none';
        
        if (nom === '') {
            document.getElementById('edit_nom_error').style.display = 'block';
            valid = false;
        }
        if (isNaN(duree) || duree <= 0) {
            document.getElementById('edit_duree_error').style.display = 'block';
            valid = false;
        }
        
        if (!valid) {
            event.preventDefault();
            return false;
        }
        return true;
    }

    // -------- Remplissage du formulaire de modification --------
    function openEditModal(id, nom, description, duree, statut) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nom').value = nom;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_duree').value = duree;
        document.getElementById('edit_statut').value = statut;
        openModal('editModal');
    }

    // -------- Remplissage du formulaire de suppression --------
    function openDeleteModal(id, nom) {
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_nom').innerHTML = nom;
        openModal('deleteModal');
    }

    // Fermer la modale en cliquant à l'extérieur
    window.onclick = function(event) {
        if(event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

</body>
</html>