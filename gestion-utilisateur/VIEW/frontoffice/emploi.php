<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Offres d'emploi</title>
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
    <h1>Offres d'emploi</h1>
    <p>Découvrez les opportunités dans la fonction publique</p>
</div>

<div class="container">
    <div class="jobs-grid">
        <div class="job-card">
            <div class="job-title">Développeur Full Stack</div>
            <div class="job-ministry"><i class="fas fa-building"></i> Ministère des Technologies</div>
            <div class="job-description">Développement d'applications web pour la transformation numérique.</div>
            <div class="job-footer">
                <span class="job-date"><i class="far fa-calendar"></i> Date limite: 15/05/2026</span>
                <a href="#" class="btn-apply" onclick="alert('Veuillez vous connecter pour postuler')">Postuler</a>
            </div>
        </div>
        <div class="job-card">
            <div class="job-title">Chef de Projet Digital</div>
            <div class="job-ministry"><i class="fas fa-building"></i> Ministère de l'Intérieur</div>
            <div class="job-description">Pilotage des projets de dématérialisation.</div>
            <div class="job-footer">
                <span class="job-date"><i class="far fa-calendar"></i> Date limite: 20/05/2026</span>
                <a href="#" class="btn-apply" onclick="alert('Veuillez vous connecter pour postuler')">Postuler</a>
            </div>
        </div>
        <div class="job-card">
            <div class="job-title">Data Analyst</div>
            <div class="job-ministry"><i class="fas fa-building"></i> Ministère des Finances</div>
            <div class="job-description">Analyse des données et reporting.</div>
            <div class="job-footer">
                <span class="job-date"><i class="far fa-calendar"></i> Date limite: 30/05/2026</span>
                <a href="#" class="btn-apply" onclick="alert('Veuillez vous connecter pour postuler')">Postuler</a>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

</body>
</html>
