<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
require_once "../../../MODEL/Reclamation.php";

$ctrl = new ReclamationController();
$error = "";
$success = "";

$db = Config::getConnexion();
$citoyens = $db->query("SELECT id_citoyen, nom, prenom FROM citoyens ORDER BY nom")->fetchAll();
$services = $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif'")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference = $ctrl->genererReference();
    
    $reclamation = new Reclamation(
        $reference,
        $_POST['id_citoyen'],
        $_POST['categorie'],
        $_POST['objet'],
        $_POST['description'],
        $_POST['priorite'],
        !empty($_POST['id_service']) ? $_POST['id_service'] : null,
        !empty($_POST['lieu']) ? $_POST['lieu'] : null,
        null
    );
    
    if($ctrl->ajouterReclamation($reclamation)) {
        $success = "Réclamation ajoutée avec succès. Référence : " . $reference;
    } else {
        $error = "Erreur lors de l'ajout.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une réclamation - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h1>Ajouter une réclamation</h1>
                <p>Créer une nouvelle réclamation pour un citoyen</p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" id="reclamationForm">
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Citoyen *</label>
                        <select name="id_citoyen" required>
                            <option value="">Sélectionnez un citoyen</option>
                            <?php foreach($citoyens as $c): ?>
                                <option value="<?= $c['id_citoyen'] ?>"><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Service</label>
                        <select name="id_service">
                            <option value="">Non spécifié</option>
                            <?php foreach($services as $s): ?>
                                <option value="<?= $s['id_service'] ?>"><?= htmlspecialchars($s['nom_service']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Catégorie *</label>
                        <select name="categorie" required>
                            <option value="">Sélectionnez</option>
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
                        <label><i class="fas fa-chart-line"></i> Priorité *</label>
                        <select name="priorite" required>
                            <option value="faible">Faible</option>
                            <option value="normale" selected>Normale</option>
                            <option value="haute">Haute</option>
                            <option value="urgente">Urgente</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Objet *</label>
                    <input type="text" name="objet" placeholder="Titre de la réclamation" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description *</label>
                    <textarea name="description" rows="5" placeholder="Description détaillée..." required></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Lieu</label>
                    <input type="text" name="lieu" placeholder="Adresse, quartier...">
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="lister.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>
            </form>
        </div>
    </div>
    
    <script src="../../../assets/js/validationReclamation.js"></script>
</body>
</html>