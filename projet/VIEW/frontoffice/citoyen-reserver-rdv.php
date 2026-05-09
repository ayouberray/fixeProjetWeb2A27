<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";
require_once __DIR__."/../../api/mail_sender.php";

$rdvController = new RendezVousController();
$error = "";
$success = "";

$db = Config::getConnexion();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $id_service = $_POST['id_service'];
    $date_heure = $_POST['date_heure'];
    $motif = $_POST['motif'];
    $citoyen_email = trim($_POST['citoyen_email'] ?? '');
    
    if(!empty($id_service) && !empty($date_heure)){
        $citoyen_nom = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
        
        $rdv = new RendezVous($citoyen_nom, $id_service, $date_heure, $motif);
        $result = $rdvController->ajouterRendezVous($rdv);
        if($result){
            // Sauvegarder l'email si fourni
            if (!empty($citoyen_email)) {
                $db->prepare("UPDATE rendez_vous SET citoyen_email = ? WHERE id_rdv = ?")
                   ->execute([$citoyen_email, $result]);
                   
                // ENVOI DE L'EMAIL DE CONFIRMATION IMMÉDIAT
                $service_name = $db->query("SELECT nom_service FROM services WHERE id_service = $id_service")->fetchColumn();
                $confirm_html = generateConfirmationHTML([
                    'id_rdv' => $result,
                    'citoyen_nom' => $citoyen_nom,
                    'date_heure' => $date_heure,
                    'nom_service' => $service_name
                ]);
                sendMunicipalEmail($citoyen_email, $citoyen_nom, "✅ Confirmation de votre RDV #$result - InnoGov", $confirm_html);
            }
            $success = "Rendez-vous réservé avec succès ! Un email de confirmation a été envoyé à " . htmlspecialchars($citoyen_email); 
        } else { 
            $error = "Erreur lors de la réservation"; 
        }
    } else { 
        $error = "Veuillez sélectionner un service et une date"; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver un rendez-vous - InnoGov</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gestion_RDV/projet/assets/css/style.css?v=20260509_v9">
    <script src="/Gestion_RDV/projet/assets/js/script.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        
        
        .futuristic-container { max-width: 800px; margin: 60px auto; position: relative; z-index: 1; }
        
        .cyber-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 40px; position: relative; overflow: hidden;
        }
        .cyber-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: var(--primary);
        }

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

        .btn-cyber {
            background: var(--primary); color: white; border: none; padding: 14px 30px;
            border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; width: 100%; transition: all 0.2s ease;
        }
        .btn-cyber:hover { background: var(--primary-dark); }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 2rem; color: #1e293b; font-weight: 700; margin-bottom: 10px; }
        .page-header p { color: #64748b; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/Gestion_RDV/projet/index.php" class="nav-link">Accueil</a>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link">Mes RDV</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle" title="Mode Sombre/Clair"><i class="fas fa-sun" id="theme-icon"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta">
                <i class="fas fa-calendar-plus"></i> Prendre RDV
            </a>
        </div>
    </nav>
</div>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-slideshow">
        <img src="/Gestion_RDV/projet/assets/images/tunisia1.jpg" class="slide active" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia2.jpg" class="slide" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia3.jpg" class="slide" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia4.jpg" class="slide" alt="Tunisie">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne</p>
        <div class="hero-buttons">
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
        </div>
    </div>
</section>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-calendar-plus" style="color: var(--primary);"></i> Réserver un créneau</h2>
        <p>Bienvenue, <strong style="color: var(--primary);"><?= $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'] ?></strong></p>
    </div>

    <div class="cyber-card reveal">
        <?php if($error): ?>
            <div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div>
        <?php endif; ?>
        <?php if($success): ?>
            <div class="alert alert-success" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-check-circle"></i> <?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-floating">
                <label class="futuristic-label"><i class="fas fa-concierge-bell"></i> Service Requis *</label>
                <select name="id_service" class="futuristic-input" required>
                    <option value="" disabled selected>-- Sélectionnez le service municipal --</option>
                    <?php foreach($services as $s): ?>
                        <option value="<?= $s['id_service'] ?>"><?= htmlspecialchars($s['nom_service']) ?> (Durée estimée: <?= $s['duree_moyenne'] ?> min)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-floating" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="position: relative;">
                    <label class="futuristic-label"><i class="fas fa-calendar-alt"></i> Date & Heure *</label>
                    <input type="datetime-local" name="date_heure" class="futuristic-input" required>
                </div>
                <div style="position: relative;">
                    <label class="futuristic-label"><i class="fas fa-envelope"></i> Email (Rappels) *</label>
                    <input type="email" name="citoyen_email" class="futuristic-input" placeholder="votre.email@exemple.com" required>
                </div>
            </div>
            
            <div class="form-floating" style="margin-top: 25px;">
                <label class="futuristic-label"><i class="fas fa-comment-dots"></i> Motif / Informations complémentaires</label>
                <textarea name="motif" class="futuristic-input" rows="4" placeholder="Précisez votre demande pour faciliter le traitement par nos agents..."></textarea>
            </div>
            
            <div style="margin-top: 30px; display: flex; gap: 15px;">
                <button type="submit" class="btn-cyber"><i class="fas fa-paper-plane"></i> Confirmer la réservation</button>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php" style="color: #64748b; text-decoration: none; font-weight: 600; font-size: 14px;"><i class="fas fa-history"></i> Consulter mon historique</a>
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
