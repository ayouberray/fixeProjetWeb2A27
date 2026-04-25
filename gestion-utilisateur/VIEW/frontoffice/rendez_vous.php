<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Prise de rendez-vous</title>
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
            max-width: 600px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        .form-card {
            background: white;
            border-radius: 24px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
        }
        .form-group { margin-bottom: 1.5rem; }
        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--gray-800);
        }
        input, select {
            width: 100%;
            padding: 0.85rem;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
        }
        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(0, 109, 91, 0.1);
        }
        .btn-submit {
            width: 100%;
            padding: 0.85rem;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .footer {
            background: #1a1a1a;
            color: #94a3b8;
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }
        @media (max-width: 768px) {
            .navbar { flex-direction: column; text-align: center; }
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
    <h1>Prenez rendez-vous</h1>
    <p>Choisissez votre service et votre créneau horaire</p>
</div>

<div class="container">
    <div class="form-card">
        <form action="#" method="POST" onsubmit="alert('Fonctionnalité à venir. Veuillez vous connecter.'); return false;">
            <div class="form-group">
                <label>Service</label>
                <select>
                    <option>Passeport</option>
                    <option>Carte d'identité nationale (CIN)</option>
                    <option>Permis de conduire</option>
                    <option>État civil</option>
                    <option>Certificat de résidence</option>
                </select>
            </div>
            <div class="form-group">
                <label>Date souhaitée</label>
                <input type="date">
            </div>
            <div class="form-group">
                <label>Créneau horaire</label>
                <select>
                    <option>09:00 - 10:00</option>
                    <option>10:00 - 11:00</option>
                    <option>11:00 - 12:00</option>
                    <option>14:00 - 15:00</option>
                    <option>15:00 - 16:00</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Réserver</button>
        </form>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

</body>
</html>