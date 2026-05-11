<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";
require_once __DIR__."/../../api/mail_sender.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;
$rdv = $rdvController->getRendezVousById($id_rdv);
if(!$rdv){ header("Location: /projet/VIEW/backoffice/admin-lister-rdv.php"); exit(); }

$db = Config::getConnexion();
$citoyens = $db->query("SELECT CONCAT(prenom, ' ', nom) as nom_complet FROM users WHERE role = 'citoyen' ORDER BY nom")->fetchAll();
$services = $db->query("SELECT * FROM services WHERE statut='actif' ORDER BY nom_service")->fetchAll();

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $citoyen_nom = $_POST['citoyen_nom'];
    $id_service = $_POST['id_service'];
    $date_heure = $_POST['date_heure'];
    $statut = $_POST['statut'];
    $motif = $_POST['motif'];
    
    if(!empty($citoyen_nom) && !empty($id_service) && !empty($date_heure)){
        $result = $rdvController->adminModifierRendezVous($id_rdv, $citoyen_nom, $id_service, $date_heure, $statut, $motif);
        if($result){ 
            // ENVOI DE L'EMAIL DE MISE À JOUR SI L'EMAIL EST DISPONIBLE
            if (!empty($rdv['citoyen_email'])) {
                $status_html = generateStatusUpdateHTML([
                    'id_rdv' => $id_rdv,
                    'citoyen_nom' => $citoyen_nom,
                    'statut' => $statut,
                    'date_heure' => $date_heure,
                    'service_nom' => $rdv['service_nom']
                ]);
                sendMunicipalEmail($rdv['citoyen_email'], $citoyen_nom, "🔔 Mise à jour de votre RDV #$id_rdv - InnoGov", $status_html);
            }

            $success = "Rendez-vous modifié avec succès ! Un email a été envoyé au citoyen."; 
            header("refresh:2;url=/projet/VIEW/backoffice/admin-lister-rdv.php");
        } else { 
            $error = "Erreur lors de la modification"; 
        }
    } else { 
        $error = "Veuillez remplir tous les champs"; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Modifier rendez-vous</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gestion_RDV/projet/assets/css/style.css??v=20260509_v9">
    <style>
        body {  font-family: 'Inter', sans-serif; background: var(--bg-page); }
        
        
        .futuristic-container { max-width: 800px; margin: 40px auto; position: relative; z-index: 1; }
        
        .cyber-card {
            background: #ffffff;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
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
            border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; width: 100%; transition: all 0.2s ease;
        }
        .btn-cyber-warning:hover { background: #d97706; }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 2rem; color: #1e293b; font-weight: 700; margin-bottom: 10px; }
        .page-header p { color: #64748b; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">← Retour</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle" title="Mode Sombre/Clair"><i class="fas fa-sun" id="theme-icon"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-cta">
                <i class="fas fa-list"></i> Voir les RDV
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
        <h1>Administration Municipale</h1>
        <p>Gérez les services et les rendez-vous en toute simplicité</p>
    </div>
</section>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-edit" style="color: #f59e0b;"></i> Modifier RDV #<?= $id_rdv ?></h2>
        <p>Mise à jour des informations du rendez-vous</p>
    </div>

    <div class="cyber-card">
        <?php if($error): ?><div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-floating" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="position: relative;">
                    <label class="futuristic-label"><i class="fas fa-user"></i> Citoyen</label>
                    <select name="citoyen_nom" class="futuristic-input" required>
                        <?php foreach($citoyens as $c): $selected = ($c['nom_complet'] == $rdv['citoyen_nom']) ? 'selected' : ''; ?>
                            <option value="<?= htmlspecialchars($c['nom_complet']) ?>" <?= $selected ?>><?= htmlspecialchars($c['nom_complet']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div style="position: relative;">
                    <label class="futuristic-label"><i class="fas fa-concierge-bell"></i> Service</label>
                    <select name="id_service" class="futuristic-input" required>
                        <?php foreach($services as $s): $selected = ($s['id_service'] == $rdv['id_service']) ? 'selected' : ''; ?>
                            <option value="<?= $s['id_service'] ?>" <?= $selected ?>><?= htmlspecialchars($s['nom_service']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-floating" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div style="position: relative;">
                    <label class="futuristic-label"><i class="fas fa-tasks"></i> Statut</label>
                    <select name="statut" class="futuristic-input" required>
                        <option value="en_attente" <?= $rdv['statut']=='en_attente' ? 'selected' : '' ?>>En attente</option>
                        <option value="confirme" <?= $rdv['statut']=='confirme' ? 'selected' : '' ?>>Confirmé</option>
                        <option value="annule" <?= $rdv['statut']=='annule' ? 'selected' : '' ?>>Annulé</option>
                        <option value="termine" <?= $rdv['statut']=='termine' ? 'selected' : '' ?>>Terminé</option>
                    </select>
                </div>
                <div style="position: relative;">
                    <label class="futuristic-label"><i class="fas fa-calendar-alt"></i> Date et heure</label>
                    <input type="datetime-local" name="date_heure" class="futuristic-input" value="<?= date('Y-m-d\TH:i', strtotime($rdv['date_heure'])) ?>" required>
                </div>
            </div>
            
            <div class="form-floating">
                <label class="futuristic-label"><i class="fas fa-comment-dots"></i> Motif / Note</label>
                <textarea name="motif" class="futuristic-input" rows="4"><?= htmlspecialchars($rdv['motif'] ?? '') ?></textarea>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-cyber-warning"><i class="fas fa-save"></i> Mettre à jour le RDV</button>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" style="color: #64748b; text-decoration: none; font-weight: 600;"><i class="fas fa-times"></i> Annuler et retourner</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
