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
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --shadow-sm: 0 2px 8px rgba(0, 109, 91, 0.08);
            --shadow-md: 0 4px 15px rgba(0, 109, 91, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            min-height: 100vh;
        }
        .navbar {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(10px);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            text-decoration: none;
        }
        .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, #2e7d32, #4caf50);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: white;
        }
        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #1b5e20;
        }
        .logo-text span { font-weight: 400; color: #4caf50; }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            color: #334155;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-links a:hover { color: #2e7d32; }
        .btn-logout {
            background: #dc2626;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
        }
        .btn-login {
            background: #2e7d32;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            text-decoration: none;
        }
        .page-hero {
            background: linear-gradient(135deg, #1b5e20, #2e7d32);
            color: white;
            text-align: center;
            padding: 3rem 2rem;
            margin-top: 70px;
        }
        .page-hero h1 { font-size: 2rem; margin-bottom: 0.5rem; }
        .container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .jobs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        .job-card {
            background: white;
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }
        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .job-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 0.5rem;
        }
        .job-ministry {
            color: #2e7d32;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }
        .job-description {
            color: #64748b;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 1rem;
        }
        .job-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid #e2e8f0;
        }
        .job-date { font-size: 0.75rem; color: #94a3b8; }
        .btn-apply {
            background: #2e7d32;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 25px;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-apply:hover { background: #1b5e20; transform: translateY(-2px); }
        .footer {
            background: #1a1a1a;
            color: #94a3b8;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
            .jobs-grid { grid-template-columns: 1fr; }
        }
    </style>
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