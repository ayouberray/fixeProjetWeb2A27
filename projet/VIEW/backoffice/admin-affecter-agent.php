<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";
require_once __DIR__."/../../api/mail_sender.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;

$db = Config::getConnexion();

$agents = $db->prepare("SELECT id, CONCAT(prenom, ' ', nom) as nom_complet FROM users WHERE role = 'agent'");
$agents->execute();
$agentsList = $agents->fetchAll();

$rdv = $rdvController->getRendezVousById($id_rdv);

$error = ""; $success = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $agent_nom = $_POST['agent_nom'];
    if(!empty($agent_nom)){
        $rdvController->affecterAgent($id_rdv, $agent_nom);
        
        // ENVOI DE L'EMAIL D'AFFECTATION D'AGENT
        if (!empty($rdv['citoyen_email'])) {
            $agent_html = generateAgentAssignedHTML([
                'id_rdv' => $id_rdv,
                'citoyen_nom' => $rdv['citoyen_nom'],
                'service_nom' => $rdv['service_nom'],
                'date_heure' => $rdv['date_heure'],
                'agent_nom' => $agent_nom
            ]);
            sendMunicipalEmail($rdv['citoyen_email'], $rdv['citoyen_nom'], "👤 Un agent a été affecté à votre RDV #$id_rdv - InnoGov", $agent_html);
        }

        $success = "Agent affecté avec succès ! Un email de notification a été envoyé.";
        header("refresh:2;url=/projet/VIEW/backoffice/admin-lister-rdv.php");
    } else { 
        $error = "Veuillez sélectionner un agent"; 
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Affecter un agent</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hero { display: none; }
        
        .futuristic-container { max-width: 700px; margin: 40px auto; position: relative; z-index: 1; }
        
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
        .futuristic-input:focus { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
        .futuristic-label {
            position: absolute; top: -10px; left: 15px; background: white; padding: 0 8px; font-size: 12px;
            font-weight: 600; color: #4f46e5; border-radius: 4px; letter-spacing: 0.5px;
        }

        .btn-cyber-primary {
            background: #4f46e5; color: white; border: none; padding: 14px 30px;
            border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; width: 100%; transition: all 0.2s ease;
        }
        .btn-cyber-primary:hover { background: #4338ca; }

        .page-header { text-align: center; margin-bottom: 30px; }
        .page-header h2 { font-size: 2rem; color: #1e293b; font-weight: 700; margin-bottom: 10px; }
        .page-header p { color: #64748b; font-size: 1.1rem; }
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
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">← Retour</a>
        </div>
    </div>
</nav>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-user-plus" style="color: #4f46e5;"></i> Affecter Agent</h2>
        <p>RDV #<?= $id_rdv ?> - <strong style="color: #4f46e5;"><?= htmlspecialchars($rdv['citoyen_nom'] ?? '') ?></strong> - <?= htmlspecialchars($rdv['service_nom'] ?? '') ?></p>
    </div>

    <div class="cyber-card">
        <?php if($error): ?><div class="alert alert-danger" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-exclamation-triangle"></i> <?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="alert alert-success" style="border-radius: 12px; margin-bottom: 25px;"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>
        
        <form method="POST">
            <div class="form-floating">
                <label class="futuristic-label"><i class="fas fa-user-tie"></i> Agent assigné</label>
                <select name="agent_nom" class="futuristic-input" required>
                    <option value="" disabled selected>-- Sélectionnez un agent dans la liste --</option>
                    <?php foreach($agentsList as $agent): ?>
                        <option value="<?= htmlspecialchars($agent['nom_complet']) ?>"><?= htmlspecialchars($agent['nom_complet']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn-cyber-primary"><i class="fas fa-check-circle"></i> Confirmer l'affectation</button>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" style="color: #64748b; text-decoration: none; font-weight: 600;"><i class="fas fa-times"></i> Annuler et retourner</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>