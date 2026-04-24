<?php
session_start();
require_once __DIR__."/../../CONTROLLER/ServiceController.php";

$serviceController = new ServiceController();

// Traitement des actions
if($_SERVER["REQUEST_METHOD"] == "POST"){
    
    // AJOUTER
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
    
    // MODIFIER
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
    
    // SUPPRIMER
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
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <style>
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; align-items: center; justify-content: center; }
    </style>
</head>
<body>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-building"></i></div>
                <div class="logo-text"><h1>InnoGov</h1><p>Administration</p></div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Rendez-vous</a>
            <a href="/projet/VIEW/backoffice/admin-services.php" class="nav-link active">Services</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-concierge-bell"></i> Gestion des services municipaux</h2>
            <button class="btn btn-primary" onclick="openModal('addModal')">
                <i class="fas fa-plus"></i> Nouveau service
            </button>
        </div>
        
        <?php if(isset($error) && $error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if(isset($success) && $success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr><th>ID</th><th>Nom du service</th><th>Description</th><th>Durée</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if(!empty($services)): ?>
                        <?php foreach($services as $s): ?>
                        <tr>
                            <td>#<?= $s['id_service'] ?></td>
                            <td><strong><?= htmlspecialchars($s['nom_service']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($s['description'] ?? '', 0, 60)) ?><?= strlen($s['description'] ?? '') > 60 ? '...' : '' ?></td>
                            <td><?= $s['duree_moyenne'] ?> min</td>
                            <td><span class="badge badge-<?= $s['statut'] ?>"><?= $s['statut'] == 'actif' ? 'Actif' : 'Inactif' ?></span></td>
                            <td>
                                <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= $s['id_service'] ?>, '<?= htmlspecialchars($s['nom_service']) ?>', '<?= htmlspecialchars($s['description'] ?? '') ?>', <?= $s['duree_moyenne'] ?>, '<?= $s['statut'] ?>')">Modifier</button>
                                <button class="btn btn-danger btn-sm" onclick="openDeleteModal(<?= $s['id_service'] ?>, '<?= htmlspecialchars($s['nom_service']) ?>')">Supprimer</button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" style="text-align:center">Aucun service trouvé</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL AJOUT -->
<div id="addModal" class="modal">
    <div class="card" style="max-width:500px; margin:20px; width:90%;">
        <h3><i class="fas fa-plus"></i> Ajouter un service</h3>
        <form method="POST">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-group">
                <label class="form-label">Nom du service *</label>
                <input type="text" name="nom_service" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Durée moyenne (minutes)</label>
                <input type="number" name="duree_moyenne" class="form-control" value="30">
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('addModal')">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL MODIFICATION -->
<div id="editModal" class="modal">
    <div class="card" style="max-width:500px; margin:20px; width:90%;">
        <h3><i class="fas fa-edit"></i> Modifier un service</h3>
        <form method="POST">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id_service" id="edit_id">
            <div class="form-group">
                <label class="form-label">Nom du service *</label>
                <input type="text" name="nom_service" id="edit_nom" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Durée moyenne (minutes)</label>
                <input type="number" name="duree_moyenne" id="edit_duree" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="statut" id="edit_statut" class="form-control">
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>
            </div>
            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                <button type="submit" class="btn btn-warning">Modifier</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('editModal')">Annuler</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL SUPPRESSION -->
<div id="deleteModal" class="modal">
    <div class="card" style="max-width:400px; margin:20px;">
        <h3><i class="fas fa-trash"></i> Confirmer la suppression</h3>
        <p>Voulez-vous vraiment supprimer le service : <strong id="delete_nom"></strong> ?</p>
        <p style="color: var(--danger); font-size: 14px;">⚠️ Attention : Un service avec des rendez-vous ne peut pas être supprimé.</p>
        <form method="POST">
            <input type="hidden" name="action" value="supprimer">
            <input type="hidden" name="id_service" id="delete_id">
            <div style="display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px;">
                <button type="submit" class="btn btn-danger">Supprimer</button>
                <button type="button" class="btn btn-outline" onclick="closeModal('deleteModal')">Annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(id) {
        document.getElementById(id).style.display = 'flex';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }
    function openEditModal(id, nom, description, duree, statut) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_nom').value = nom;
        document.getElementById('edit_description').value = description;
        document.getElementById('edit_duree').value = duree;
        document.getElementById('edit_statut').value = statut;
        openModal('editModal');
    }
    function openDeleteModal(id, nom) {
        document.getElementById('delete_id').value = id;
        document.getElementById('delete_nom').innerHTML = nom;
        openModal('deleteModal');
    }
    // Fermer en cliquant en dehors
    window.onclick = function(event) {
        if(event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    }
</script>

</body>
</html>