<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$user_nom = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : 'Visiteur';
$user_prenom = $isLoggedIn ? ($_SESSION['user_prenom'] ?? '') : '';
$user_initials = $isLoggedIn ? strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) : 'MB';
$canEdit = $isLoggedIn && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'agent');

// Données statiques des rendez-vous
$rendezVous = [
    ['id' => 1, 'service' => "État civil", 'date' => "2026-05-10", 'heure' => "10:00", 'nom' => "Ahmed Ben Ali", 'telephone' => "99 123 456", 'email' => "ahmed@email.com", 'statut' => "confirme", 'notes' => ""],
    ['id' => 2, 'service' => "Carte d'identité nationale", 'date' => "2026-05-15", 'heure' => "14:30", 'nom' => "Sonia Gharbi", 'telephone' => "98 765 432", 'email' => "sonia@email.com", 'statut' => "en_attente", 'notes' => "Renouvellement carte"],
    ['id' => 3, 'service' => "Urbanisme", 'date' => "2026-05-05", 'heure' => "09:00", 'nom' => "Mehdi Khelil", 'telephone' => "97 654 321", 'email' => "mehdi@email.com", 'statut' => "termine", 'notes' => "Permis de construire"],
    ['id' => 4, 'service' => "Passeport biométrique", 'date' => "2026-05-20", 'heure' => "11:00", 'nom' => "Leila Mansour", 'telephone' => "96 543 210", 'email' => "leila@email.com", 'statut' => "annule", 'notes' => ""]
];

$services = [
    1 => "État civil",
    2 => "Urbanisme et Permis de construire",
    3 => "Carte d'identité nationale",
    4 => "Passeport biométrique",
    5 => "Propreté et environnement",
    6 => "Impôts et taxes locales"
];
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <title>InnoGov - Gestion des rendez-vous | Municipalité Tunisienne</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ===== VARIABLES MODE CLAIR/SOMBRE ===== */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --accent: #ff8c42;
            --bg-body: linear-gradient(135deg, #f5f7fa 0%, #e9edf2 100%);
            --card-bg: #ffffff;
            --text-dark: #1a2a3e;
            --text-muted: #4a627a;
            --border-color: #e0e6ed;
            --navbar-bg: rgba(255, 255, 255, 0.98);
            --footer-bg: #0a2b3e;
            --footer-text: #a0c4d8;
            --shadow-sm: 0 2px 20px rgba(0,0,0,0.1);
            --shadow-md: 0 10px 30px rgba(0,0,0,0.08);
            --badge-confirme: #27ae60;
            --badge-attente: #f39c12;
            --badge-annule: #e74c3c;
            --badge-termine: #3498db;
        }

        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --accent: #ff8c42;
            --bg-body: linear-gradient(135deg, #0f0f1a 0%, #1a1a2e 100%);
            --card-bg: #16213e;
            --text-dark: #eeeeee;
            --text-muted: #a0a0a0;
            --border-color: #2c3e50;
            --navbar-bg: rgba(26, 26, 46, 0.98);
            --footer-bg: #0a0a0f;
            --footer-text: #888888;
            --shadow-sm: 0 2px 20px rgba(0,0,0,0.3);
            --shadow-md: 0 10px 30px rgba(0,0,0,0.4);
            --badge-confirme: #27ae60;
            --badge-attente: #f39c12;
            --badge-annule: #e74c3c;
            --badge-termine: #3498db;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-body);
            color: var(--text-dark);
            line-height: 1.6;
            transition: background 0.2s ease, color 0.2s ease;
        }

        /* ===== SWITCH MODE ===== */
        .theme-switch-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: 0.5rem;
        }
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 26px;
        }
        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: 0.2s;
            border-radius: 34px;
        }
        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: 0.2s;
            border-radius: 50%;
        }
        input:checked + .slider { background-color: var(--primary); }
        input:checked + .slider:before { transform: translateX(24px); }
        .theme-icon { font-size: 14px; }
        .light-icon, .dark-icon { display: none; }
        [data-theme="light"] .light-icon { display: inline; }
        [data-theme="light"] .dark-icon { display: none; }
        [data-theme="dark"] .light-icon { display: none; }
        [data-theme="dark"] .dark-icon { display: inline; }

        /* Loader */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: #0a2b3e;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s;
        }
        [data-theme="dark"] .loader {
            background: #0f0f1a;
        }
        .loader.hide {
            opacity: 0;
            visibility: hidden;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(255,255,255,0.3);
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Navbar */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        .navbar.scrolled { background: var(--card-bg); box-shadow: var(--shadow-sm); }
        .nav-container {
            max-width: 1280px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo {
            display: flex;
            align-items: center;
            text-decoration: none;
            flex-shrink: 0;
        }
        .logo-img {
            height: 40px;
            width: auto;
            max-width: 130px;
            object-fit: contain;
            display: block;
        }
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.2rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--text-muted);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }
        .nav-links a:hover { color: var(--primary); }
        .user-name {
            color: var(--primary);
            font-weight: 500;
            background: var(--primary-light);
            padding: 0.3rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
        }
        .btn-profiled {
            background: var(--primary);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-profiled:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-logout {
            background: #dc2626;
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        .btn-logout:hover { background: #b91c1c; transform: translateY(-1px); }
        .btn-login {
            background: transparent;
            color: var(--primary);
            border: 1.5px solid var(--primary);
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-login:hover { background: var(--primary); color: white; transform: translateY(-1px); }
        .btn-register {
            background: var(--primary);
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.85rem;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .btn-register:hover { background: var(--primary-dark); transform: translateY(-1px); }

        /* Hero Section */
        .hero {
            position: relative;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }
        .hero-video {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -2;
        }
        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }
        .hero-content {
            max-width: 800px;
            padding: 2rem;
            animation: fadeInUp 0.8s ease-out;
        }
        .hero-content h1 {
            font-size: 3rem;
            color: white;
            margin-bottom: 1rem;
            font-weight: 800;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .hero-content p {
            font-size: 1.2rem;
            color: rgba(255,255,255,0.9);
            margin-bottom: 1.5rem;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Section */
        .section {
            padding: 3rem 2rem;
        }
        .container {
            max-width: 1300px;
            margin: 0 auto;
        }
        .section-title {
            text-align: center;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--text-dark);
            position: relative;
        }
        .section-title:after {
            content: '';
            display: block;
            width: 60px;
            height: 3px;
            background: var(--primary);
            margin: 0.6rem auto 0;
            border-radius: 3px;
        }

        /* Navigation secondaire */
        .sub-nav {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .sub-nav-btn {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            padding: 0.8rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            color: var(--text-muted);
            cursor: pointer;
            transition: all 0.3s;
            font-size: 1rem;
        }
        .sub-nav-btn.active {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            box-shadow: 0 5px 15px rgba(0,109,91,0.3);
        }
        .sub-nav-btn:hover:not(.active) {
            border-color: var(--primary);
            color: var(--primary);
        }

        /* Rendez-vous Grid */
        .rdv-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 1.5rem;
        }
        .rdv-card {
            background: var(--card-bg);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .rdv-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        .rdv-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            padding: 1.2rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .rdv-service {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .rdv-status {
            padding: 0.2rem 0.8rem;
            border-radius: 20px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        .status-confirme { background: var(--badge-confirme); }
        .status-en-attente { background: var(--badge-attente); }
        .status-annule { background: var(--badge-annule); }
        .status-termine { background: var(--badge-termine); }
        .rdv-body {
            padding: 1.2rem 1.5rem;
        }
        .rdv-info {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 0.8rem;
            color: var(--text-muted);
        }
        .rdv-info i {
            width: 22px;
            color: var(--primary);
        }
        .rdv-footer {
            padding: 1rem 1.5rem 1.5rem;
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            border-top: 1px solid var(--border-color);
        }

        /* Formulaire */
        .rdv-form {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 2rem;
            max-width: 700px;
            margin: 0 auto;
            box-shadow: var(--shadow-md);
        }
        .rdv-form h3 {
            color: var(--text-dark);
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--text-dark);
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-family: inherit;
            transition: 0.3s;
            background: var(--card-bg);
            color: var(--text-dark);
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        .btn-submit {
            width: 100%;
            background: var(--primary);
            color: white;
            font-weight: 700;
            padding: 0.9rem;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn {
            display: inline-block;
            padding: 0.7rem 1.5rem;
            border-radius: 40px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn-success {
            background: #27ae60;
            color: white;
        }
        .btn-success:hover {
            background: #219a52;
        }
        .btn-danger {
            background: #e74c3c;
            color: white;
        }
        .btn-danger:hover {
            background: #c0392b;
        }
        .btn-sm {
            padding: 0.4rem 1rem;
            font-size: 0.85rem;
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem;
            background: var(--card-bg);
            border-radius: 24px;
            color: var(--text-muted);
        }
        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        /* Info banner */
        .info-banner {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid rgba(0, 109, 91, 0.2);
        }
        [data-theme="dark"] .info-banner {
            background: rgba(26, 26, 46, 0.95);
        }
        .info-banner i { color: var(--primary); margin-right: 0.5rem; }
        .info-banner a { color: var(--primary); font-weight: 600; text-decoration: none; }

        /* Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 12px 24px;
            border-radius: 40px;
            font-weight: 600;
            z-index: 9999;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0%); opacity: 1; }
        }

        /* Footer */
        .footer {
            background: var(--footer-bg);
            color: var(--footer-text);
            margin-top: 2rem;
        }
        .footer-container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 3rem 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .footer-section h4 {
            color: white;
            margin-bottom: 1rem;
        }
        .footer-section p {
            margin: 0.5rem 0;
        }
        .footer-section a {
            color: var(--footer-text);
            text-decoration: none;
            cursor: pointer;
        }
        .footer-section a:hover {
            color: var(--primary);
        }
        .footer-bottom {
            text-align: center;
            padding: 1.5rem;
            border-top: 1px solid #1e4a62;
            font-size: 0.8rem;
        }
        [data-theme="dark"] .footer-bottom {
            border-top-color: #2c3e50;
        }

        /* Reveal animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.6s ease;
        }
        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .navbar { padding: 0.5rem 1rem; }
            .nav-links { gap: 0.8rem; }
            .hero-content h1 { font-size: 1.8rem; }
            .hero-content p { font-size: 0.9rem; }
            .rdv-grid { grid-template-columns: 1fr; }
            .form-row { grid-template-columns: 1fr; }
            .section { padding: 2rem 1rem; }
        }
    </style>
</head>
<body>

<div class="loader" id="loader"><div class="spinner"></div></div>

<!-- NAVBAR -->
<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <img src="../../assets/images/logo.png" alt="Logo" class="logo-img">
        </a>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="#" id="servicesLink">Services</a>
            
            <!-- SWITCH MODE SOMBRE/CLAIR -->
            <div class="theme-switch-wrapper">
                <span class="theme-icon light-icon">☀️</span>
                <label class="theme-switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider"></span>
                </label>
                <span class="theme-icon dark-icon">🌙</span>
            </div>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="user-name">👋 <?= htmlspecialchars($user_prenom . ' ' . $user_nom) ?></span>
                <a href="profil.php" class="btn-profiled">Mon profil</a>
                <a href="logout.php" class="btn-logout">Déconnexion</a>
            <?php else: ?>
                <a href="login.php" class="btn-login">Connexion</a>
                <a href="register.php" class="btn-register">Inscription</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<!-- HERO SECTION - PLEIN ÉCRAN AVEC VIDÉO -->
<section class="hero">
    <video class="hero-video" autoplay loop muted playsinline>
        <source src="../../assets/video/background.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Gestion des Rendez-vous</h1>
        <p>Prenez et gérez vos rendez-vous avec les services municipaux facilement</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Mes rendez-vous</h2>
        
        <?php if(!$isLoggedIn): ?>
            <div class="info-banner">
                <i class="fas fa-info-circle"></i> Vous n'êtes pas connecté. 
                <a href="login.php">Connectez-vous</a> pour voir l'historique de vos rendez-vous et en prendre de nouveaux.
            </div>
        <?php endif; ?>
        
        <!-- Navigation secondaire -->
        <div class="sub-nav">
            <button class="sub-nav-btn active" data-tab="mesrdvs">Mes rendez-vous</button>
            <button class="sub-nav-btn" data-tab="nouveau">Nouveau rendez-vous</button>
        </div>

        <!-- Contenu Mes rendez-vous -->
        <div id="mesRdvsTab" class="tab-content">
            <div class="rdv-grid" id="rdvsGrid"></div>
        </div>

        <!-- Contenu Nouveau rendez-vous -->
        <div id="nouveauTab" class="tab-content" style="display: none;">
            <div class="rdv-form">
                <h3 style="margin-bottom: 1.5rem; text-align: center;">Prendre un rendez-vous</h3>
                <form id="rdvForm">
                    <div class="form-group">
                        <label>Service</label>
                        <select id="rdvService" required>
                            <option value="">Sélectionnez un service</option>
                            <?php foreach($services as $id => $service): ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($service) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" id="rdvDate" min="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Heure</label>
                            <select id="rdvHeure" required>
                                <option value="">Sélectionnez une heure</option>
                                <option value="09:00">09:00</option>
                                <option value="09:30">09:30</option>
                                <option value="10:00">10:00</option>
                                <option value="10:30">10:30</option>
                                <option value="11:00">11:00</option>
                                <option value="11:30">11:30</option>
                                <option value="14:00">14:00</option>
                                <option value="14:30">14:30</option>
                                <option value="15:00">15:00</option>
                                <option value="15:30">15:30</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Nom complet</label>
                            <input type="text" id="rdvNom" placeholder="Votre nom et prénom" required>
                        </div>
                        <div class="form-group">
                            <label>Téléphone</label>
                            <input type="tel" id="rdvTelephone" placeholder="XX XXX XXX" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="rdvEmail" placeholder="exemple@email.com" required>
                    </div>
                    <div class="form-group">
                        <label>Message / Notes</label>
                        <textarea rows="2" id="rdvNotes" placeholder="Informations complémentaires..."></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Confirmer mon rendez-vous</button>
                </form>
            </div>
        </div>
    </div>
</section>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4><i class="fas fa-building"></i> InnoGov</h4>
            <p>Plateforme de services municipaux<br>Modernisation de l'administration tunisienne</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-phone"></i> Contact</h4>
            <p><i class="fas fa-phone-alt"></i> +216 70 000 000</p>
            <p><i class="fas fa-envelope"></i> contact@innogov.tn</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-calendar-check"></i> Rendez-vous</h4>
            <p><a class="tab-link" data-tab="mesrdvs">Mes rendez-vous</a></p>
            <p><a class="tab-link" data-tab="nouveau">Nouveau rendez-vous</a></p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 InnoGov - Tous droits réservés</p>
    </div>
</footer>

<script>
    // ===== MODE SOMBRE/CLAIR =====
    (function() {
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        const THEME_KEY = 'innogov_theme';
        
        function setTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem(THEME_KEY, theme);
            if (themeToggle) themeToggle.checked = (theme === 'dark');
        }
        
        function initTheme() {
            const savedTheme = localStorage.getItem(THEME_KEY);
            if (savedTheme) {
                setTheme(savedTheme);
            } else {
                setTheme(window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
            }
        }
        
        if (themeToggle) {
            themeToggle.addEventListener('change', function() {
                setTheme(this.checked ? 'dark' : 'light');
            });
        }
        
        initTheme();
    })();

    // Données statiques des rendez-vous (sans base de données)
    let rendezVous = <?= json_encode($rendezVous) ?>;
    const servicesMap = <?= json_encode($services) ?>;
    const isLoggedIn = <?= json_encode($isLoggedIn) ?>;
    let currentLang = 'fr';
    let currentTab = 'mesrdvs';
    let nextId = rendezVous.length + 5;

    const translations = {
        fr: {
            "confirme": "Confirmé", "en_attente": "En attente", "annule": "Annulé", "termine": "Terminé",
            "Annuler": "Annuler", "Modifier": "Modifier", "Confirmer": "Confirmer"
        },
        ar: {
            "confirme": "مؤكد", "en_attente": "قيد الانتظار", "annule": "ملغى", "termine": "منتهي",
            "Annuler": "إلغاء", "Modifier": "تعديل", "Confirmer": "تأكيد"
        }
    };

    function t(text) {
        if (currentLang === 'ar' && translations.ar[text]) return translations.ar[text];
        return translations.fr[text] || text;
    }

    function getStatusClass(statut) {
        switch(statut) {
            case 'confirme': return 'status-confirme';
            case 'en_attente': return 'status-en-attente';
            case 'annule': return 'status-annule';
            case 'termine': return 'status-termine';
            default: return 'status-en-attente';
        }
    }

    function formatDate(dateStr) {
        let d = new Date(dateStr);
        if (currentLang === 'fr') {
            return d.toLocaleDateString('fr-FR');
        } else {
            return d.toLocaleDateString('ar-TN');
        }
    }

    function renderRdvs() {
        const container = document.getElementById('rdvsGrid');
        if (!container) return;
        
        if (rendezVous.length === 0) {
            container.innerHTML = `<div class="empty-state"><i class="fas fa-calendar-times"></i><p>Aucun rendez-vous trouvé</p></div>`;
            return;
        }
        
        container.innerHTML = rendezVous.map(rdv => `
            <div class="rdv-card reveal">
                <div class="rdv-header">
                    <span class="rdv-service"><i class="fas fa-concierge-bell"></i> ${rdv.service}</span>
                    <span class="rdv-status ${getStatusClass(rdv.statut)}">${t(rdv.statut)}</span>
                </div>
                <div class="rdv-body">
                    <div class="rdv-info"><i class="fas fa-calendar-alt"></i> ${formatDate(rdv.date)}</div>
                    <div class="rdv-info"><i class="fas fa-clock"></i> ${rdv.heure}</div>
                    <div class="rdv-info"><i class="fas fa-user"></i> ${rdv.nom}</div>
                    <div class="rdv-info"><i class="fas fa-phone"></i> ${rdv.telephone}</div>
                    ${rdv.notes ? `<div class="rdv-info"><i class="fas fa-comment"></i> ${rdv.notes}</div>` : ''}
                </div>
                <div class="rdv-footer">
                    ${rdv.statut === 'en_attente' && isLoggedIn ? `<button class="btn btn-success btn-sm annuler-rdv" data-id="${rdv.id}" data-action="confirmer"><i class="fas fa-check"></i> ${t('Confirmer')}</button>` : ''}
                    ${rdv.statut !== 'termine' && rdv.statut !== 'annule' && isLoggedIn ? `<button class="btn btn-danger btn-sm annuler-rdv" data-id="${rdv.id}" data-action="annuler"><i class="fas fa-times"></i> ${t('Annuler')}</button>` : ''}
                </div>
            </div>
        `).join('');
        attachRdvEvents();
        revealObserver();
    }

    function attachRdvEvents() {
        document.querySelectorAll('.annuler-rdv').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = parseInt(btn.getAttribute('data-id'));
                const action = btn.getAttribute('data-action');
                const rdv = rendezVous.find(r => r.id === id);
                if (rdv) {
                    if (action === 'annuler') {
                        rdv.statut = 'annule';
                        showToast('Rendez-vous annulé');
                    } else if (action === 'confirmer') {
                        rdv.statut = 'confirme';
                        showToast('Rendez-vous confirmé');
                    }
                    renderRdvs();
                }
            });
        });
    }

    function addRendezVous(service, date, heure, nom, telephone, email, notes) {
        const newRdv = {
            id: nextId++,
            service: service,
            date: date,
            heure: heure,
            nom: nom,
            telephone: telephone,
            email: email,
            statut: 'en_attente',
            notes: notes || ''
        };
        rendezVous.unshift(newRdv);
        renderRdvs();
        showToast('Rendez-vous ajouté avec succès !');
    }

    function showToast(msg) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.textContent = msg;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function revealObserver() {
        const reveals = document.querySelectorAll('.reveal');
        reveals.forEach(el => {
            const rect = el.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100) el.classList.add('active');
            else setTimeout(() => el.classList.add('active'), 100);
        });
    }

    // Gestion des onglets
    document.querySelectorAll('.sub-nav-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.sub-nav-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            currentTab = btn.getAttribute('data-tab');
            document.getElementById('mesRdvsTab').style.display = currentTab === 'mesrdvs' ? 'block' : 'none';
            document.getElementById('nouveauTab').style.display = currentTab === 'nouveau' ? 'block' : 'none';
            if (currentTab === 'mesrdvs') renderRdvs();
        });
    });

    document.querySelectorAll('.tab-link').forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const tab = link.getAttribute('data-tab');
            document.querySelector(`.sub-nav-btn[data-tab="${tab}"]`).click();
        });
    });

    // Formulaire nouveau rendez-vous
    const dateInput = document.getElementById('rdvDate');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.min = today;
    }

    document.getElementById('rdvForm')?.addEventListener('submit', (e) => {
        e.preventDefault();
        
        if (!isLoggedIn) {
            showToast('Veuillez vous connecter pour prendre un rendez-vous');
            setTimeout(() => { window.location.href = 'login.php'; }, 2000);
            return;
        }
        
        const serviceId = document.getElementById('rdvService').value;
        const service = servicesMap[serviceId];
        const date = document.getElementById('rdvDate').value;
        const heure = document.getElementById('rdvHeure').value;
        const nom = document.getElementById('rdvNom').value;
        const telephone = document.getElementById('rdvTelephone').value;
        const email = document.getElementById('rdvEmail').value;
        const notes = document.getElementById('rdvNotes').value;

        if (!service || !date || !heure || !nom || !telephone || !email) {
            showToast('Veuillez remplir tous les champs obligatoires');
            return;
        }

        addRendezVous(service, date, heure, nom, telephone, email, notes);
        document.getElementById('rdvForm').reset();
        document.querySelector('.sub-nav-btn[data-tab="mesrdvs"]').click();
    });

    // Loader et initialisation
    window.addEventListener('load', () => {
        document.getElementById('loader').classList.add('hide');
        setTimeout(() => document.getElementById('loader').style.display = 'none', 500);
        renderRdvs();
        revealObserver();
    });
</script>
</body>
</html>