<?php
// Fichier: VIEW/frontoffice/verify_email.php
session_start();
require_once '../../CONFIG/config.php';
require_once '../../MODEL/Utilisateur.php';

$error = '';
$success = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    $error = "Lien de vérification invalide.";
} else {
    $utilisateurModel = new Utilisateur();
    
    if ($utilisateurModel->verifyEmailByToken($token)) {
        $success = "✅ Votre email a été confirmé avec succès !<br>
                    Votre compte est maintenant activé.<br>
                    Vous pouvez vous connecter.";
    } else {
        $error = "❌ Lien de vérification invalide ou expiré.<br>
                  Veuillez refaire une inscription.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation email - InnoGov</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;background:linear-gradient(135deg,#667eea,#764ba2);display:flex;justify-content:center;align-items:center;min-height:100vh}
        .container{background:white;border-radius:20px;padding:40px;width:90%;max-width:500px;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.2)}
        .alert{padding:20px;border-radius:10px;margin-bottom:20px}
        .alert-success{background:#d1fae5;color:#059669}
        .alert-error{background:#fee2e2;color:#dc2626}
        button{padding:12px 30px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;border:none;border-radius:10px;cursor:pointer;font-size:16px;font-weight:600;transition:transform 0.2s}
        button:hover{transform:translateY(-2px)}
        h1{margin-bottom:20px;color:#333}
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Confirmation d'email</h1>
        <?php if($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <button onclick="window.location.href='login.php'">Se connecter</button>
        <?php else: ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
            <button onclick="window.location.href='register.php'">S'inscrire</button>
        <?php endif; ?>
    </div>
</body>
</html>