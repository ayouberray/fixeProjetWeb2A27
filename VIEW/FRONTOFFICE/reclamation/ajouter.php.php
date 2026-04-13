<?php
session_start();

// Forcer l'ID citoyen pour le test
$_SESSION['user_id'] = 2;
$_SESSION['user_nom'] = "Ben Ali";
$_SESSION['user_prenom'] = "Mohamed";

require_once "C:/xampp/htdocs/PROJETFIXE/CONTROLLER/ReclamationController.php";
require_once "C:/xampp/htdocs/PROJETFIXE/MODEL/config.php";

$error = "";
$success = "";

$db = Config::getConnexion();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $ctrl = new ReclamationController();
    
    $reference = $ctrl->genererReference();
    $id_citoyen = $_SESSION['user_id'];
    $id_service = !empty($_POST['id_service']) ? $_POST['id_service'] : null;
    $categorie = $_POST['categorie'];
    $objet = $_POST['objet'];
    $description = $_POST['description'];
    $priorite = $_POST['priorite'];
    $lieu = $_POST['lieu'] ?? null;
    
    // Insertion directe sans passer par l'objet Reclamation
    $sql = "INSERT INTO reclamation (reference, id_citoyen, id_service, categorie, objet, description, lieu, priorite, statut, date_soumission) 
            VALUES (:reference, :id_citoyen, :id_service, :categorie, :objet, :description, :lieu, :priorite, 'soumise', NOW())";
    
    try {
        $req = $db->prepare($sql);
        $result = $req->execute([
            ':reference' => $reference,
            ':id_citoyen' => $id_citoyen,
            ':id_service' => $id_service,
            ':categorie' => $categorie,
            ':objet' => $objet,
            ':description' => $description,
            ':lieu' => $lieu,
            ':priorite' => $priorite
        ]);
        
        if($result){
            $success = "✅ Réclamation envoyée avec succès !<br>Référence : " . $reference;
        } else {
            $error = "❌ Erreur lors de l'envoi de votre réclamation.";
        }
    } catch(Exception $e) {
        $error = "❌ Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déposer une réclamation</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #F5FCF9 0%, #EBF7F3 100%);
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(245, 252, 249, 0.85);
            backdrop-filter: blur(16px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 2rem;
            transition: all 0.3s;
        }
        .navbar.scrolled { background: rgba(245, 252, 249, 0.98); box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
        .navbar-container { max-width: 1400px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .logo { display: flex; align-items: center; gap: 12px; text-decoration: none; }
        .logo-icon { width: 45px; height: 45px; background: linear-gradient(135deg, #006D5B, #004D3D); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 22px; }
        .logo-text h1 { font-size: 22px; font-weight: 800; background: linear-gradient(135deg, #006D5B, #004D3D); -webkit-background-clip: text; background-clip: text; color: transparent; }
        .logo-text p { font-size: 11px; color: #5C8B7E; }
        .nav-menu { display: flex; gap: 2rem; align-items: center; flex-wrap: wrap; }
        .nav-link { text-decoration: none; color: #2C5A4F; font-weight: 500; transition: all 0.3s; }
        .nav-link:hover { color: #006D5B; }
        .btn-primary { background: linear-gradient(135deg, #006D5B, #004D3D); color: white; padding: 8px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,109,91,0.3); }
        
        .hero {
            min-height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 120px 2rem 60px;
            background: linear-gradient(135deg, #006D5B 0%, #004D3D 100%);
            color: white;
        }
        .hero h1 { font-size: 42px; margin-bottom: 15px; }
        .hero p { font-size: 18px; opacity: 0.9; }
        
        .container { max-width: 700px; margin: 0 auto; padding: 40px 2rem; }
        
        .card { background: rgba(255,255,255,0.92); backdrop-filter: blur(10px); border-radius: 20px; box-shadow: 0 24px 48px rgba(0,77,61,0.12); padding: 32px; }
        .card-header { margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid rgba(0,109,91,0.12); }
        .card-title { font-size: 24px; font-weight: 700; display: flex; align-items: center; gap: 8px; color: #1A2E2A; }
        
        .form-group { margin-bottom: 20px; }
        .form-label { display: block; margin-bottom: 8px; font-weight: 500; color: #374151; font-size: 14px; }
        .form-control { width: 100%; padding: 12px 16px; border: 1px solid #D1D5DB; border-radius: 10px; font-size: 14px; transition: all 0.2s; background: white; }
        .form-control:focus { outline: none; border-color: #006D5B; box-shadow: 0 0 0 3px rgba(0,109,91,0.1); }
        textarea.form-control { resize: vertical; min-height: 120px; }
        
        .btn-submit { width: 100%; background: linear-gradient(135deg, #006D5B, #004D3D); color: white; padding: 14px; border: none; border-radius: 10px; font-size: 16px; font-weight: 600; cursor: pointer; transition: all 0.3s; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,109,91,0.3); }
        
        .alert { padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; }
        .alert-success { background: #D1FAE5; color: #006D5B; border-left: 3px solid #006D5B; }
        .alert-danger { background: #FEE2E2; color: #DC2626; border-left: 3px solid #DC2626; }
        
        .info-user { background: #E6F4F0; padding: 12px 16px; border-radius: 10px; margin-bottom: 20px; display: flex; align-items: center; gap: 10px; color: #006D5B; }
        
        .footer { background: linear-gradient(180deg, #0D3328, #0A281E); color: white; padding: 40px 2rem 20px; margin-top: 60px; text-align: center; }
        
        @media (max-width: 768px) { .navbar-container { flex-direction: column; text-align: center; } .nav-menu { justify-content: center; } .hero h1 { font-size: 32px; } }
    </style>
</head>
<body>

<nav class="navbar" id="navbar">
    <div class="navbar-container">
        <a href="/PROJETFIXE/index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text"><h1>InnoGov</h1><p>Espace Citoyen</p></div>
        </a>
        <div class="nav-menu">
            <a href="/PROJETFIXE/index.php" class="nav-link">Accueil</a>
            <a href="/PROJETFIXE/VIEW/FRONTOFFICE/RECLAMATION/mes-reclamations.php" class="nav-link">Mes réclamations</a>
            <a href="/PROJETFIXE/VIEW/BACKOFFICE/RECLAMATION/lister.php" class="nav-link">Admin</a>
            <a href="/PROJETFIXE/VIEW/FRONTOFFICE/RECLAMATION/ajouter.php" class="btn-primary">Déposer</a>
        </div>
    </div>
</nav>

<section class="hero">
    <div class="hero-content">
        <h1>Déposer une réclamation</h1>
        <p>Exprimez votre insatisfaction ou signalez un problème</p>
    </div>
</section>

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2 class="card-title"><i class="fas fa-pen-alt"></i> Nouvelle réclamation</h2>
        </div>
        
        <div class="info-user">
            <i class="fas fa-user-circle"></i>
            <span>Bienvenue, <strong><?= $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'] ?></strong></span>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label class="form-label">Service concerné</label>
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
                <label class="form-label">Objet *</label>
                <input type="text" name="objet" class="form-control" placeholder="Titre clair et concis" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" class="form-control" placeholder="Décrivez votre problème en détail (minimum 20 caractères)" required></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Priorité *</label>
                <select name="priorite" class="form-control" required>
                    <option value="faible">🟢 Faible</option>
                    <option value="normale" selected>🟡 Normale</option>
                    <option value="haute">🟠 Haute</option>
                    <option value="urgente">🔴 Urgente</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Lieu</label>
                <input type="text" name="lieu" class="form-control" placeholder="Adresse, quartier...">
            </div>
            
            <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Envoyer ma réclamation</button>
        </form>
    </div>
</div>

<footer class="footer">
    <div class="footer-container"><p>&copy; 2024 InnoGov - Tous droits réservés</p></div>
</footer>

<script>
    window.addEventListener('scroll', function() {
        document.getElementById('navbar').classList.toggle('scrolled', window.scrollY > 50);
    });
</script>

</body>
</html>
