<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Concours</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <a href="index.php" class="logo">
        <div class="logo-icon"><i class="fas fa-leaf"></i></div>
        <div class="logo-text">inno<span>Gov</span></div>
    </a>
    <div class="nav-links">
        <a href="index.php">Accueil</a>
        <a href="emploi.php">Emploi</a>
        <a href="concours.php">Concours</a>
        <a href="rendez_vous.php">Rendez-vous</a>
        <a href="demandes.php">Demandes</a>
        <a href="reclamations.php">Réclamations</a>
        <?php if(isset($_SESSION['user_id'])): ?>
            <span style="color: #2e7d32;">👋 <?= htmlspecialchars($_SESSION['user_nom']) ?></span>
            <a href="profil.php" style="background:#2e7d32; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Mon profil</a>
            <a href="logout.php" class="btn-logout">Déconnexion</a>
        <?php else: ?>
            <a href="login.php" class="btn-login">Connexion</a>
            <a href="register.php" style="background:#4caf50; color:white; padding:8px 20px; border-radius:8px; text-decoration:none;">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<div class="page-hero">
    <h1>Concours administratifs</h1>
    <p>Préparez votre avenir avec la fonction publique</p>
</div>

<div class="container">
    <div class="concours-grid">
        <div class="concours-card">
            <div class="concours-title">Concours Administratif</div>
            <div class="concours-organisme"><i class="fas fa-building"></i> Fonction Publique</div>
            <div class="concours-info"><span class="info-label">Date limite:</span><span class="info-value">30/04/2026</span></div>
            <div class="concours-info"><span class="info-label">Postes:</span><span class="info-value">50</span></div>
            <div class="concours-info"><span class="info-label">Inscrits:</span><span class="info-value">234</span></div>
            <a href="#" class="btn-register" onclick="alert('Veuillez vous connecter pour vous inscrire')">S'inscrire</a>
        </div>
        <div class="concours-card">
            <div class="concours-title">Concours Technicien Supérieur</div>
            <div class="concours-organisme"><i class="fas fa-building"></i> Ministère de l'Éducation</div>
            <div class="concours-info"><span class="info-label">Date limite:</span><span class="info-value">15/05/2026</span></div>
            <div class="concours-info"><span class="info-label">Postes:</span><span class="info-value">30</span></div>
            <div class="concours-info"><span class="info-label">Inscrits:</span><span class="info-value">156</span></div>
            <a href="#" class="btn-register" onclick="alert('Veuillez vous connecter pour vous inscrire')">S'inscrire</a>
        </div>
        <div class="concours-card">
            <div class="concours-title">Concours Ingénieur d'État</div>
            <div class="concours-organisme"><i class="fas fa-building"></i> Ministère de l'Industrie</div>
            <div class="concours-info"><span class="info-label">Date limite:</span><span class="info-value">01/06/2026</span></div>
            <div class="concours-info"><span class="info-label">Postes:</span><span class="info-value">20</span></div>
            <div class="concours-info"><span class="info-label">Inscrits:</span><span class="info-value">89</span></div>
            <a href="#" class="btn-register" onclick="alert('Veuillez vous connecter pour vous inscrire')">S'inscrire</a>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

</body>
</html>
