<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$error = "";
$success = "";
$id_rdv = $_GET['id'] ?? 0;

$rdv = $rdvController->getRendezVousById($id_rdv);

$citoyen_nom = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
if(!$rdv || $rdv['citoyen_nom'] != $citoyen_nom){
    header("Location: /projet/VIEW/frontoffice/citoyen-mes-rdv.php"); 
    exit();
}

$db = Config::getConnexion();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_service = $_POST['id_service'];
    $date_heure = $_POST['date_heure'];
    $motif = $_POST['motif'];
    
    if(!empty($id_service) && !empty($date_heure)){
        $result = $rdvController->modifierRendezVousComplet($id_rdv, $id_service, $date_heure, $motif);
        if($result){
            $success = "Rendez-vous modifié avec succès !";
            header("refresh:2;url=/projet/VIEW/frontoffice/citoyen-mes-rdv.php");
        } else {
            $error = "Erreur lors de la modification";
        }
    } else { 
        $error = "Veuillez choisir un service et une date"; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier rendez-vous - InnoGov</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hero { display: none; }
        
        .futuristic-container { max-width: 800px; margin: 60px auto; position: relative; z-index: 1; }
        
        .cyber-card {
            background: #ffffff; border-radius: 12px;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 40px; position: relative; overflow: hidden;
        }

        .form-floating { position: relative; margin-bottom: 25px; }
        .futuristic-input {
            width: 100%; padding: 14px 15px; font-size: 15px; background: #ffffff; border: 1px solid #cbd5e1;
            border-radius: 8px; color: #1e293b; transition: all 0.2s ease; box-sizing: border-box; outline: none;
        }
        .futuristic-input:focus { border-color: #f59e0b; box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1); }
        .futuristic-label {
            position: absolute; top: -10px; left: 15px; background: white; padding: 0 8px; font-size: 12px;
            font-weight: 600; color: #d97706; border-radius: 4px; letter-spacing: 0.5px;
        }

        .btn-cyber-warning {
            background: #f59e0b; color: white; border: none; padding: 14px 30px;
            border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s ease;
        }
        .btn-cyber-warning:hover { background: #d97706; }

        .btn-outline-cancel {
            background: #ffffff; color: #64748b; border: 1px solid #cbd5e1; padding: 14px 30px;
            border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; transition: all 0.2s ease; text-decoration: none;
        }
        .btn-outline-cancel:hover { background: #f1f5f9; color: #475569; }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 2rem; color: #1e293b; font-weight: 700; margin-bottom: 10px; }
        .page-header p { color: #64748b; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <img src="/projet/assets/images/innogov-logo.png" alt="InnoGov" class="logo-img">
                <div class="logo-text">
                    <p class="logo-subtitle">Municipalité Tunisienne</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/index.php" class="nav-link">Accueil</a>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link active">Mes RDV</a>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary btn-sm">Prendre RDV</a>
        </div>
    </div>
</nav>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-edit" style="color: #f59e0b;"></i> Modifier rendez-vous #<?= $id_rdv ?></h2>
        <p>Modifiez le service, la date ou le motif de votre rendez-vous</p>
    </div>
    
    <div class="cyber-card reveal">
        <?php if($error): ?>
            <div class="alert alert-danger" style="border-radius: 8px; margin-bottom: 25px;"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success" style="border-radius: 8px; margin-bottom: 25px;"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-floating">
                <label class="futuristic-label"><i class="fas fa-concierge-bell"></i> Service *</label>
                <select name="id_service" class="futuristic-input" required>
                    <option value="">-- Choisir un service --</option>
                    <?php foreach($services as $s): 
                        $selected = ($s['id_service'] == $rdv['id_service']) ? 'selected' : '';
                    ?>
                        <option value="<?= $s['id_service'] ?>" <?= $selected ?>>
                            <?= htmlspecialchars($s['nom_service']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-floating">
                <label class="futuristic-label"><i class="fas fa-calendar-alt"></i> Date et heure *</label>
                <input type="datetime-local" name="date_heure" class="futuristic-input" 
                       value="<?= date('Y-m-d\TH:i', strtotime($rdv['date_heure'])) ?>" required>
            </div>
            
            <div class="form-floating">
                <label class="futuristic-label"><i class="fas fa-pencil-alt"></i> Motif</label>
                <textarea name="motif" class="futuristic-input" rows="3"><?= htmlspecialchars($rdv['motif'] ?? '') ?></textarea>
            </div>
            
            <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-top: 30px;">
                <button type="submit" class="btn-cyber-warning">
                    <i class="fas fa-save"></i> Enregistrer les modifications
                </button>
                <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="btn-outline-cancel">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>InnoGov</h4><p>Plateforme de services municipaux</p></div>
        <div class="footer-section"><h4>Contact</h4><p>Tel: +216 70 000 000</p></div>
        <div class="footer-section"><h4>Horaires</h4><p>Lun-Ven: 8h30 - 15h30</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 InnoGov - Tous droits réservés</p></div>
</footer>

</body>
</html>