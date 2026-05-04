<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['user_nom'] = 'Administrateur';
$_SESSION['user_prenom'] = 'Admin';
$_SESSION['user_role'] = 'admin';

// ========== CONFIGURATION TWILIO ==========
define('TWILIO_SID', 'AC8076693893118ab692d90b6b60aa2456');
define('TWILIO_TOKEN', '5c07c38bb732fc025429e90f9fd63806');
define('TWILIO_PHONE', '+19129133693');

require_once __DIR__ . '/../../CONTROLLER/DemandeController.php';

$id_demande = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id_demande) { header('Location: index.php?error=ID invalide'); exit(); }

$controller = new DemandeController();
$data = $controller->modifier($id_demande);

$demande = $data['demande'] ?? null;
$services = $data['services'] ?? [];
if (!$demande) { header('Location: index.php?error=Demande introuvable'); exit(); }

$ancienStatut = $demande['statut'];

// ========== SI POST ==========
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $nouveauStatut = $_POST['statut'] ?? $ancienStatut;
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $id_service = (int)($_POST['id_service'] ?? 0);
    $type_demande = $_POST['type_demande'] ?? '';
    
    // Connexion BDD
    $bdd = new PDO('mysql:host=localhost;dbname=pro', 'root', '');
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 1. Mise à jour demande
    $update = $bdd->prepare("UPDATE demandes SET titre=?, description=?, type_demande=?, id_service=?, statut=?, date_modification=NOW() WHERE id_demande=?");
    $update->execute([$titre, $description, $type_demande, $id_service, $nouveauStatut, $id_demande]);
    
    // 2. Ajouter suivi
    $suivi = $bdd->prepare("INSERT INTO suivi_demandes (id_demande, ancien_statut, nouveau_statut, commentaire) VALUES (?, ?, ?, 'Statut modifié')");
    $suivi->execute([$id_demande, $ancienStatut, $nouveauStatut]);
    
    // 3. Si passage à traité → SMS
    if ($ancienStatut !== 'traite' && $nouveauStatut === 'traite') {
        
        // Récupérer tel du citoyen
        $stmt = $bdd->prepare("SELECT c.telephone FROM demandes d JOIN citoyens c ON d.id_citoyen = c.id_citoyen WHERE d.id_demande=?");
        $stmt->execute([$id_demande]);
        $tel = $stmt->fetchColumn();
        
        $smsEnvoye = false;
        
        if ($tel) {
            // Nettoyer numéro
            $tel = preg_replace('/[^0-9]/', '', $tel);
            if (!empty($tel) && $tel[0] != '+') $tel = '+' . $tel;
            
            // Envoyer SMS via Twilio
            $ch = curl_init('https://api.twilio.com/2010-04-01/Accounts/' . TWILIO_SID . '/Messages.json');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                'From' => TWILIO_PHONE,
                'To'   => $tel,
                'Body' => "InnoGov: Demande #" . str_pad($id_demande, 5, '0', STR_PAD_LEFT) . " traitee."
            ]));
            curl_setopt($ch, CURLOPT_USERPWD, TWILIO_SID . ':' . TWILIO_TOKEN);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $resp = curl_exec($ch);
            $http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $smsEnvoye = ($http == 201);
            
            // Log
            $logDir = __DIR__ . '/../../logs';
            if (!is_dir($logDir)) mkdir($logDir, 0777, true);
            file_put_contents($logDir . '/sms.log', date('Y-m-d H:i:s') . " | #$id_demande | Tel:$tel | HTTP:$http | $resp\n", FILE_APPEND);
        }
        
        $msg = "Demande #" . str_pad($id_demande, 5, '0', STR_PAD_LEFT) . " traitée";
        $msg .= $smsEnvoye ? " | 📱 SMS envoyé" : " | ⚠️ SMS échoué";
        header('Location: index.php?success=' . urlencode($msg));
        exit();
    }
    
    header('Location: index.php?success=Demande modifiée avec succès');
    exit();
}

$types_demandes = [
    'urbanisme' => '🏗️ Urbanisme', 'voirie' => '🛣️ Voirie', 'etat_civil' => '📜 État Civil',
    'culture' => '🎭 Culture', 'social' => '🤝 Social', 'autre' => '📌 Autre'
];

$statuts = [
    'en_attente' => '⏳ En attente', 'en_cours' => '🔄 En cours', 
    'traite' => '✅ Traité', 'refuse' => '❌ Refusé'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Demande #<?= $id_demande ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:#f5fcf9;display:flex;align-items:center;justify-content:center;min-height:100vh;padding:1.5rem}
        .card{background:white;border-radius:1rem;box-shadow:0 12px 24px rgba(0,0,0,0.1);max-width:700px;width:100%;overflow:hidden}
        .header{background:linear-gradient(135deg,#006D5B,#004D3D);color:white;padding:2rem}
        .header a{color:rgba(255,255,255,0.8);text-decoration:none;display:inline-flex;align-items:center;gap:0.5rem;margin-bottom:1rem}
        .header a:hover{color:white}
        .header h1{font-size:1.5rem}
        .badge{background:rgba(255,255,255,0.2);padding:0.3rem 1rem;border-radius:50px;font-size:0.9rem;margin-left:0.5rem}
        .body{padding:2rem}
        .form-group{margin-bottom:1.2rem}
        label{display:block;font-weight:600;color:#4a5a6e;margin-bottom:0.4rem;font-size:0.9rem}
        input,select,textarea{width:100%;padding:0.8rem;border:2px solid #d1d9e6;border-radius:0.75rem;font-size:0.95rem;font-family:'Inter',sans-serif}
        input:focus,select:focus,textarea:focus{outline:none;border-color:#006D5B;box-shadow:0 0 0 4px rgba(0,109,91,0.1)}
        textarea{min-height:100px;resize:vertical}
        .statut-options{display:grid;grid-template-columns:repeat(4,1fr);gap:0.5rem}
        .statut-options input{display:none}
        .statut-options label{display:flex;align-items:center;justify-content:center;gap:0.3rem;padding:0.7rem 0.5rem;border:2px solid #d1d9e6;border-radius:0.5rem;cursor:pointer;font-weight:600;font-size:0.85rem;text-align:center}
        .statut-options label:hover{border-color:#006D5B;background:#e6f4f0}
        .statut-options input:checked+label{border-color:#006D5B;background:#e6f4f0;color:#004D3D}
        .info{background:#e6f4f0;border-radius:0.75rem;padding:1rem;margin-bottom:1.5rem}
        .info-row{display:flex;align-items:center;gap:0.5rem;margin-bottom:0.5rem;font-size:0.9rem}
        .info-row i{color:#006D5B;width:20px}
        .actions{display:flex;gap:1rem;margin-top:1rem}
        .btn{flex:1;padding:0.9rem;border-radius:0.75rem;font-weight:600;font-size:0.95rem;cursor:pointer;text-decoration:none;text-align:center;border:none;display:flex;align-items:center;justify-content:center;gap:0.5rem}
        .btn-primary{background:linear-gradient(135deg,#006D5B,#004D3D);color:white}
        .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,109,91,0.4)}
        .btn-secondary{background:white;color:#4a5a6e;border:2px solid #d1d9e6}
        .btn-success{background:linear-gradient(135deg,#00A86B,#008f5a);color:white;width:100%}
        .btn-success:hover{transform:translateY(-2px);box-shadow:0 8px 20px rgba(0,168,107,0.4)}
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <a href="index.php"><i class="fas fa-arrow-left"></i> Retour au tableau de bord</a>
            <h1>Modifier la Demande <span class="badge">#<?= str_pad($id_demande, 5, '0', STR_PAD_LEFT) ?></span></h1>
        </div>
        <div class="body">
            <div class="info">
                <div class="info-row"><i class="fas fa-calendar-alt"></i><strong>Création :</strong> <?= date('d/m/Y H:i', strtotime($demande['date_creation'])) ?></div>
                <div class="info-row"><i class="fas fa-clock"></i><strong>Modification :</strong> <?= $demande['date_modification'] ? date('d/m/Y H:i', strtotime($demande['date_modification'])) : 'Jamais' ?></div>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Statut</label>
                    <div class="statut-options">
                        <?php foreach ($statuts as $value => $label): ?>
                            <div>
                                <input type="radio" id="st_<?= $value ?>" name="statut" value="<?= $value ?>" <?= $demande['statut'] == $value ? 'checked' : '' ?>>
                                <label for="st_<?= $value ?>"><?= $label ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="form-group"><label>Titre</label><input type="text" name="titre" value="<?= htmlspecialchars($demande['titre']) ?>" maxlength="255" required></div>
                <div class="form-group"><label>Service</label>
                    <select name="id_service" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= $s['id_service'] ?>" <?= $demande['id_service'] == $s['id_service'] ? 'selected' : '' ?>><?= htmlspecialchars($s['nom_service']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Type</label>
                    <select name="type_demande" required>
                        <option value="">-- Sélectionnez --</option>
                        <?php foreach ($types_demandes as $v => $l): ?>
                            <option value="<?= $v ?>" <?= $demande['type_demande'] == $v ? 'selected' : '' ?>><?= $l ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group"><label>Description</label><textarea name="description" required><?= htmlspecialchars($demande['description']) ?></textarea></div>
                
                <div class="actions">
                    <a href="index.php" class="btn btn-secondary"><i class="fas fa-times"></i> Annuler</a>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Enregistrer</button>
                </div>
                
                <?php if ($demande['statut'] !== 'traite'): ?>
                <div style="margin-top:0.5rem;">
                    <button type="button" id="btnTraiter" class="btn btn-success" onclick="traiter()">
                        <i class="fas fa-check-double"></i> Marquer comme traité et envoyer SMS
                    </button>
                </div>
                <?php endif; ?>
            </form>
        </div>
    </div>
    <script>
    function traiter(){
        document.getElementById('st_traite').checked = true;
        if(confirm('📱 SMS envoyé au client. Continuer ?')) document.querySelector('form').submit();
    }
    document.querySelector('form').addEventListener('submit', function(e){
        var ns = document.querySelector('input[name="statut"]:checked').value;
        if(ns === 'traite' && '<?= $demande['statut'] ?>' !== 'traite'){
            if(!confirm('📱 SMS envoyé. Continuer ?')){ e.preventDefault(); return false; }
        }
        this.querySelector('.btn-primary').innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
    });
    </script>
</body>
</html>