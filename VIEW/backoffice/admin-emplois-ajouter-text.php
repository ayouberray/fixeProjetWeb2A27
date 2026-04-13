<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__."/../../CONTROLLER/EmploiController.php";
$ctrl = new EmploiController();
$erreur = '';

// Récupérer les listes, y compris les agents pour les propositions
$agents = $ctrl->getAgents();
$services = $ctrl->getServices();
$shifts = $ctrl->getShifts();

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom_agent = trim($_POST['nom_agent'] ?? '');
    $id_service = $_POST['id_service'] ?? '';
    $id_shift = $_POST['id_shift'] ?? '';
    $date_travail = $_POST['date_travail'] ?? '';
    
    if($nom_agent && $id_service && $id_shift && $date_travail) {
        if($ctrl->ajouterEmploiByName($nom_agent, $id_service, $id_shift, $date_travail)) {
            header("Location: admin-emplois-lister.php");
            exit();
        } else {
            $erreur = "Erreur lors de l'ajout - Vérifiez que l'agent existe (nom et prénom)";
        }
    } else {
        $erreur = "Tous les champs sont obligatoires";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Emploi (Version Texte)</title>
    <style>
        body{font-family:Arial;padding:20px;background:#f5f5f5;}
        .container{max-width:600px;background:white;padding:30px;border-radius:5px;box-shadow:0 0 10px rgba(0,0,0,0.1);}
        h1{color:#005C9E;}
        .form-group{margin:20px 0;}
        label{display:block;margin-bottom:5px;font-weight:bold;}
        input, select{width:100%;padding:10px;border:1px solid #ddd;border-radius:3px;box-sizing:border-box;font-size:14px;}
        select{cursor:pointer;}
        .btn{padding:10px 20px;background:#005C9E;color:white;border:none;border-radius:3px;cursor:pointer;margin-right:10px;font-size:14px;}
        .btn:hover{background:#003f75;}
        .btn-cancel{background:#666;}
        .btn-cancel:hover{background:#444;}
        .erreur{color:red;background:#ffe6e6;padding:10px;border-radius:3px;margin-bottom:20px;}
        .info{color:#666;font-size:12px;margin-top:5px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Ajouter un Emploi (Version avec saisie du nom)</h1>
        
        <?php if($erreur): ?>
            <div class="erreur"><?php echo $erreur; ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="nom_agent">Nom de l'Agent *</label>
                <input type="text" id="nom_agent" name="nom_agent" list="agents" placeholder="Ex: Dupont Jean" value="<?php echo htmlspecialchars($nom_agent ?? ''); ?>" required>
                <datalist id="agents">
                    <?php foreach($agents as $agent): ?>
                        <option value="<?php echo htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']); ?>"></option>
                    <?php endforeach; ?>
                </datalist>
                <div class="info">Choisissez un agent parmi les propositions ou saisissez le nom complet (nom prénom).</div>
            </div>
            
            <div class="form-group">
                <label for="id_service">Service *</label>
                <select id="id_service" name="id_service" required>
                    <option value="">Choisir un service</option>
                    <?php foreach($services as $service): ?>
                        <option value="<?php echo $service['id_service']; ?>" <?php echo (isset($id_service) && $id_service == $service['id_service']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($service['nom_service']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="id_shift">Shift *</label>
                <select id="id_shift" name="id_shift" required>
                    <option value="">Choisir un shift</option>
                    <?php foreach($shifts as $shift): ?>
                        <option value="<?php echo $shift['id_shift']; ?>" <?php echo (isset($id_shift) && $id_shift == $shift['id_shift']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($shift['nom_shift'] . ' (' . $shift['heure_debut'] . ' - ' . $shift['heure_fin'] . ')'); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="date_travail">Date de travail *</label>
                <input type="date" id="date_travail" name="date_travail" value="<?php echo htmlspecialchars($date_travail ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Ajouter l'Emploi</button>
                <a href="admin-emplois-lister.php" class="btn btn-cancel">Annuler</a>
            </div>
        </form>
    </div>
</body>
</html>
