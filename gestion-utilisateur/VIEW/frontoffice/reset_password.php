<?php
session_start();
require_once '../../CONFIG/config.php';
require_once '../../MODEL/Utilisateur.php';

$error = '';
$success = '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    header('Location: forgot_password.php');
    exit();
}

$utilisateurModel = new Utilisateur();
$user = $utilisateurModel->getUserByToken($token);

if (!$user) {
    $error = "❌ Lien invalide ou expiré. Veuillez refaire une demande.";
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } elseif ($password !== $confirm_password) {
        $error = "Les mots de passe ne correspondent pas.";
    } else {
        if ($utilisateurModel->updatePassword($user['email'], $password)) {
            $success = "✅ Votre mot de passe a été réinitialisé avec succès !";
            $utilisateurModel->clearResetToken($user['email']);
        } else {
            $error = "❌ Erreur lors de la réinitialisation.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation - InnoGov</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        :root { --primary: #006D5B; --primary-dark: #004D3D; }
        .slideshow-bg { position: fixed; inset: 0; z-index: -1; overflow: hidden; }
        .slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; animation: slideFade 20s infinite; }
        .slide:nth-child(1) { animation-delay: 0s; }
        .slide:nth-child(2) { animation-delay: 5s; }
        .slide:nth-child(3) { animation-delay: 10s; }
        .slide:nth-child(4) { animation-delay: 15s; }
        @keyframes slideFade { 0%,20%{opacity:1} 25%,100%{opacity:0} }
        .slide-overlay { position: fixed; inset: 0; background: linear-gradient(180deg, rgba(13,46,33,0.6), rgba(13,46,33,0.82)); z-index: -1; }
        .navbar { background: rgba(255,255,255,0.98); backdrop-filter: blur(12px); position: fixed; top: 0; width: 100%; z-index: 1000; padding: 0.5rem 2rem; border-bottom: 1px solid rgba(0,0,0,0.05); transition: all 0.3s ease; }
        .navbar.scrolled { background: white; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .nav-container { max-width: 1280px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .logo { display: flex; align-items: center; text-decoration: none; }
        .logo-img { height: 40px; width: auto; max-width: 130px; object-fit: contain; }
        .nav-links { display: flex; align-items: center; gap: 1.2rem; flex-wrap: wrap; }
        .nav-links a { text-decoration: none; color: #475569; font-weight: 500; font-size: 0.9rem; transition: color 0.3s ease; }
        .nav-links a:hover { color: var(--primary); }
        .btn-login { background: transparent; color: var(--primary); border: 1.5px solid var(--primary); padding: 0.4rem 1.2rem; border-radius: 30px; font-weight: 500; font-size: 0.85rem; text-decoration: none; transition: all 0.3s ease; }
        .btn-login:hover { background: var(--primary); color: white; }
        .btn-register { background: var(--primary); color: white; padding: 0.4rem 1.2rem; border-radius: 30px; font-weight: 500; font-size: 0.85rem; text-decoration: none; transition: all 0.3s ease; }
        .btn-register:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .reset-wrapper { position: relative; z-index: 2; padding: 120px 1.5rem 3rem; max-width: 1080px; margin: 0 auto; }
        .reset-card { background: rgba(255,255,255,0.92); border-radius: 30px; padding: 2.5rem; box-shadow: 0 10px 30px rgba(0,109,91,0.15); backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,0.5); max-width: 500px; margin: 0 auto; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; margin-bottom: 0.55rem; font-weight: 600; color: #1E293B; font-size: 0.95rem; }
        input { width: 100%; padding: 1rem; border: 1px solid #E2E8F0; border-radius: 14px; font-size: 0.95rem; background: white; transition: all 0.25s ease; }
        input:focus { border-color: #006D5B; box-shadow: 0 0 0 3px rgba(0,109,91,0.12); outline: none; }
        .btn-submit { background: #006D5B; color: white; border-radius: 999px; padding: 0.85rem 1.8rem; border: none; font-weight: 700; cursor: pointer; width: 100%; font-size: 1rem; transition: all 0.25s ease; }
        .btn-submit:hover { background: #004D3D; transform: translateY(-2px); }
        .back-link { text-align: center; margin-top: 1.5rem; }
        .back-link a { color: #006D5B; text-decoration: none; font-weight: 600; }
        .alert { padding: 1rem 1.25rem; border-radius: 16px; margin-bottom: 1.5rem; }
        .alert-error { background: #FEF2F2; border: 1px solid #FECACA; color: #DC2626; }
        .alert-success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A; }
        .info-text { text-align: center; color: #475569; margin-bottom: 1.5rem; }
        .footer { background: #1a1a1a; color: #94a3b8; text-align: center; padding: 2rem 1.5rem; margin-top: 3rem; }
        .footer-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 2rem; max-width: 1200px; margin: 0 auto; }
        .footer a { color: #94a3b8; text-decoration: none; }
        .footer a:hover { color: white; }
        .footer-bottom { margin-top: 2rem; border-top: 1px solid rgba(255,255,255,0.12); padding-top: 1.5rem; }
        @media (max-width: 768px) { .reset-card { padding: 1.5rem; } }
    </style>
</head>
<body>

<div class="slideshow-bg">
    <div class="slide" style="background-image: url('../../assets/images/tunisia1.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia2.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia3.jpg');"></div>
    <div class="slide" style="background-image: url('../../assets/images/tunisia4.jpg');"></div>
    <div class="slide-overlay"></div>
</div>

<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo"><img src="../../assets/images/logo.png" alt="Logo" class="logo-img"></a>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="#">Services</a>
            <a href="login.php" class="btn-login">Connexion</a>
            <a href="register.php" class="btn-register">Inscription</a>
        </div>
    </div>
</nav>

<div class="reset-wrapper">
    <div class="reset-card">
        <h2 style="text-align: center; margin-bottom: 1.5rem;">🔐 Nouveau mot de passe</h2>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if(!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="back-link"><a href="login.php">← Se connecter</a></div>
        <?php else: ?>
            <div class="info-text">Choisissez un nouveau mot de passe sécurisé.</div>
            <form method="POST">
                <div class="form-group">
                    <label>🔑 Nouveau mot de passe</label>
                    <input type="password" name="password" required minlength="6" placeholder="Minimum 6 caractères">
                </div>
                <div class="form-group">
                    <label>✓ Confirmer le mot de passe</label>
                    <input type="password" name="confirm_password" required placeholder="Retapez votre mot de passe">
                </div>
                <button type="submit" class="btn-submit">Réinitialiser</button>
            </form>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div><h4>innoGov</h4><p>Digitaliser aujourd'hui, servir mieux demain</p></div>
        <div><h4>Liens rapides</h4><p><a href="index.php">Accueil</a></p><p><a href="register.php">Inscription</a></p></div>
        <div><h4>Horaires</h4><p>Lun - Ven: 8h00 - 17h00</p><p>Sam: 9h00 - 13h00</p></div>
        <div><h4>Contact</h4><p><i class="fas fa-phone"></i> +216 70 000 000</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 innoGov - Tous droits réservés</p></div>
</footer>

<script>
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });
</script>

</body>
</html>