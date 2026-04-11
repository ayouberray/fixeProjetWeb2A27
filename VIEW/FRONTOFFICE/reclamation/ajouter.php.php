<?php
session_start();
if(!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

require_once "../../../CONTROLLER/ReclamationController.php";
require_once "../../../MODEL/Reclamation.php";

$ctrl = new ReclamationController();
$error = "";
$success = "";

$db = Config::getConnexion();
$services = $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif'")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $reference = $ctrl->genererReference();
    
    // Gestion fichier joint
    $piece_jointe = null;
    if(isset($_FILES['piece_jointe']) && $_FILES['piece_jointe']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $filename = $_FILES['piece_jointe']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if(in_array($ext, $allowed)) {
            $piece_jointe = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $filename);
            move_uploaded_file($_FILES['piece_jointe']['tmp_name'], "../../../uploads/" . $piece_jointe);
        }
    }
    
    $reclamation = new Reclamation(
        $reference,
        $_SESSION['user_id'],
        $_POST['categorie'],
        $_POST['objet'],
        $_POST['description'],
        $_POST['priorite'],
        !empty($_POST['id_service']) ? $_POST['id_service'] : null,
        !empty($_POST['lieu']) ? $_POST['lieu'] : null,
        $piece_jointe
    );
    
    if($ctrl->ajouterReclamation($reclamation)) {
        $success = "Votre réclamation a été envoyée. Référence : " . $reference;
    } else {
        $error = "Erreur lors de l'envoi.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Déposer une réclamation</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="card" style="max-width: 800px; margin: 0 auto;">
            <div class="header">
                <div class="header-icon">
                    <i class="fas fa-pen-alt"></i>
                </div>
                <h1>Déposer une réclamation</h1>
                <p>Exprimez votre insatisfaction ou signalez un problème</p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success"><i class="fas fa-check-circle"></i> <?= $success ?></div>
            <?php endif; ?>
            <?php if($error): ?>
                <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data" id="reclamationForm">
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Catégorie *</label>
                        <select name="categorie" id="categorie" required>
                            <option value="">Sélectionnez</option>
                            <option value="administrative">📋 Administrative</option>
                            <option value="sociale">🤝 Sociale</option>
                            <option value="infrastructure">🏗️ Infrastructure</option>
                            <option value="sante">🏥 Santé</option>
                            <option value="education">📚 Éducation</option>
                            <option value="transport">🚌 Transport</option>
                            <option value="environnement">🌳 Environnement</option>
                            <option value="autre">📌 Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-building"></i> Service concerné</label>
                        <select name="id_service">
                            <option value="">Non spécifié</option>
                            <?php foreach($services as $s): ?>
                                <option value="<?= $s['id_service'] ?>"><?= htmlspecialchars($s['nom_service']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-heading"></i> Objet *</label>
                    <input type="text" name="objet" id="objet" placeholder="Titre clair et concis" required>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-align-left"></i> Description *</label>
                    <textarea name="description" id="description" placeholder="Décrivez votre problème en détail (minimum 20 caractères)" required></textarea>
                </div>
                
                <div class="grid-2">
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Lieu</label>
                        <input type="text" name="lieu" placeholder="Adresse, quartier...">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-chart-line"></i> Priorité *</label>
                        <select name="priorite" id="priorite" required>
                            <option value="faible">🟢 Faible</option>
                            <option value="normale" selected>🟡 Normale</option>
                            <option value="haute">🟠 Haute</option>
                            <option value="urgente">🔴 Urgente</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-paperclip"></i> Pièce jointe</label>
                    <input type="file" name="piece_jointe" accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                    <small style="color: rgba(255,255,255,0.4);">Formats: JPG, PNG, PDF, DOC (max 5MB)</small>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-paper-plane"></i> Envoyer ma réclamation</button>
            </form>
        </div>
    </div>
    
    <script src="../../../assets/js/validationReclamation.js"></script>
    <script>
        function createParticles() {
            const container = document.createElement('div');
            container.className = 'particles';
            document.body.appendChild(container);
            for (let i = 0; i < 30; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 20 + 's';
                container.appendChild(particle);
            }
        }
        createParticles();
    </script>
</body>
</html>