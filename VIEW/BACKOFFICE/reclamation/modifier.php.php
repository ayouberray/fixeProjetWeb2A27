<?php
session_start();
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
$ctrl = new ReclamationController();

$id = $_GET['id'] ?? 0;
$reclamation = $ctrl->getReclamationById($id);

if(!$reclamation) {
    header("Location: lister.php");
    exit();
}

$error = "";
$success = "";

$db = Config::getConnexion();
$services = $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif'")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    if($ctrl->modifierReclamation(
        $id,
        $_POST['objet'],
        $_POST['description'],
        $_POST['priorite'],
        $_POST['categorie'],
        $_POST['lieu'] ?? null,
        $_POST['id_service'] ?? null
    )) {
        $success = "Réclamation modifiée avec succès";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-edit"></i>
                </div>
                <h1>Modifier la réclamation</h1>
                <p>Réf: <?= htmlspecialchars($reclamation['reference']) ?></p>
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
                        <label><i class="fas fa-tag"></i> Catégorie</label>
                        <select name="categorie" required>
                            <option value="administrative" <?= $reclamation['categorie'] == 'administrative' ? 'selected' : '' ?>>Administrative</option>
                            <option value="sociale" <?= $reclamation['categorie'] == 'sociale' ? 'selected' : '' ?>>Sociale</option>
                            <option value="infrastructure" <?= $reclamation['categorie'] == 'infrastructure' ? 'selected' : '' ?>>Infrastructure</option>
                            <option value="sante" <?= $reclamation['categorie'] == 'sante' ? 'selected' : '' ?>>Santé</option>
                            <option value="education" <?= $reclamation['categorie'] == 'education' ? 'selected' : '' ?>>Éducation</option>
                            <option value="transport" <?= $reclamation['categorie'] == 'transport' ? 'selected' : '' ?>>Transport</option>
                            <option value="environnement" <?= $reclamation['categorie'] == 'environnement' ? 'selected' : '' ?>>Environnement</option>
                            <option value="autre" <?= $reclamation['categorie'] == 'autre' ? 'selected' : '' ?>>Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Service</label>
                        <select name="id_service">
                            <option value="">Non spécifié</option>
                            <?php foreach($services as $s): ?>
                                <option value="<?= $s['id_service'] ?>" <?= ($reclamation['id_service'] == $s['id_service']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($s['nom_service']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Priorité</label>
                        <select name="priorite" required>
                            <option value="faible" <?= $reclamation['priorite'] == 'faible' ? 'selected' : '' ?>>Faible</option>
                            <option value="normale" <?= $reclamation['priorite'] == 'normale' ? 'selected' : '' ?>>Normale</option>
                            <option value="haute" <?= $reclamation['priorite'] == 'haute' ? 'selected' : '' ?>>Haute</option>
                            <option value="urgente" <?= $reclamation['priorite'] == 'urgente' ? 'selected' : '' ?>>Urgente</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Lieu</label>
                        <input type="text" name="lieu" value="<?= htmlspecialchars($reclamation['lieu'] ?? '') ?>">
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Objet</label>
                    <input type="text" name="objet" value="<?= htmlspecialchars($reclamation['objet']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description</label>
                    <textarea name="description" rows="5" required><?= htmlspecialchars($reclamation['description']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-tag"></i> Statut</label>
                    <select name="statut" id="statut">
                        <option value="soumise" <?= $reclamation['statut'] == 'soumise' ? 'selected' : '' ?>>Soumise</option>
                        <option value="en_cours" <?= $reclamation['statut'] == 'en_cours' ? 'selected' : '' ?>>En cours</option>
                        <option value="traitee" <?= $reclamation['statut'] == 'traitee' ? 'selected' : '' ?>>Traitée</option>
                        <option value="rejetee" <?= $reclamation['statut'] == 'rejetee' ? 'selected' : '' ?>>Rejetée</option>
                        <option value="cloturee" <?= $reclamation['statut'] == 'cloturee' ? 'selected' : '' ?>>Clôturée</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 15px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                    <a href="details.php?id=<?= $id ?>" class="btn btn-secondary"><i class="fas fa-eye"></i> Voir détails</a>
                    <a href="lister.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Retour</a>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        document.getElementById('statut')?.addEventListener('change', function() {
            if(confirm('Modifier le statut de cette réclamation ?')) {
                window.location.href = 'modifier-statut.php?id=<?= $id ?>&statut=' + this.value;
            }
        });
    </script>
</body>
</html>