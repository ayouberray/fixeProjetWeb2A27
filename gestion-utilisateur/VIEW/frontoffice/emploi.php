<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$user_nom = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : 'Visiteur';
$user_prenom = $isLoggedIn ? ($_SESSION['user_prenom'] ?? '') : '';
$user_initials = $isLoggedIn ? strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) : 'MB';
$canEdit = $isLoggedIn && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'agent');

// Données statiques
$shifts = [
    ['id_shift' => 1, 'nom_shift' => 'Matin', 'heure_debut' => '08:00', 'heure_fin' => '12:00', 'duree' => '4h'],
    ['id_shift' => 2, 'nom_shift' => 'Après-midi', 'heure_debut' => '13:00', 'heure_fin' => '17:00', 'duree' => '4h'],
    ['id_shift' => 3, 'nom_shift' => 'Nuit', 'heure_debut' => '20:00', 'heure_fin' => '06:00', 'duree' => '10h'],
    ['id_shift' => 4, 'nom_shift' => 'Pleine journée', 'heure_debut' => '08:00', 'heure_fin' => '16:00', 'duree' => '8h']
];

$emplois = [
    ['id_emploi' => 101, 'agent_nom' => 'Ben Ali', 'agent_prenom' => 'Ahmed', 'nom_service' => 'Propreté', 'nom_shift' => 'Matin', 'heure_debut' => '08:00', 'heure_fin' => '12:00', 'date_travail' => '2026-05-15', 'statut' => 'planifie'],
    ['id_emploi' => 102, 'agent_nom' => 'Gharbi', 'agent_prenom' => 'Sonia', 'nom_service' => 'Espaces Verts', 'nom_shift' => 'Après-midi', 'heure_debut' => '13:00', 'heure_fin' => '17:00', 'date_travail' => '2026-05-16', 'statut' => 'termine'],
    ['id_emploi' => 103, 'agent_nom' => 'Khelil', 'agent_prenom' => 'Mehdi', 'nom_service' => 'Voirie', 'nom_shift' => 'Nuit', 'heure_debut' => '20:00', 'heure_fin' => '06:00', 'date_travail' => '2026-05-17', 'statut' => 'annule'],
    ['id_emploi' => 104, 'agent_nom' => 'Mansour', 'agent_prenom' => 'Leila', 'nom_service' => 'Administration', 'nom_shift' => 'Pleine journée', 'heure_debut' => '08:00', 'heure_fin' => '16:00', 'date_travail' => '2026-05-18', 'statut' => 'planifie']
];

$services = ['Propreté', 'Espaces Verts', 'Voirie', 'Administration', 'Urbanisme'];
$agents = [
    ['id' => 1, 'nom' => 'Ben Ali', 'prenom' => 'Ahmed'],
    ['id' => 2, 'nom' => 'Gharbi', 'prenom' => 'Sonia'],
    ['id' => 3, 'nom' => 'Khelil', 'prenom' => 'Mehdi'],
    ['id' => 4, 'nom' => 'Mansour', 'prenom' => 'Leila']
];
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <title>InnoGov | Gestion des emplois et shifts</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --secondary-dark: #1B5E20;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --info: #17A2B8;
            --dark: #1A2C3E;
            --gray-900: #2D3A4B;
            --gray-700: #4A5A6E;
            --gray-500: #8A99B0;
            --gray-300: #D1D9E6;
            --gray-100: #F5FCF9;
            --white: #FFFFFF;
            --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 8px -2px rgba(0,0,0,0.08);
            --shadow-lg: 0 12px 24px -8px rgba(0,0,0,0.12);
            --shadow-xl: 0 20px 40px -12px rgba(0,0,0,0.2);
            --shadow-primary: 0 8px 20px -6px rgba(0,109,91,0.4);
            --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-sm: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --bg-gradient-start: #f0fdf4;
            --bg-gradient-end: #dcfce7;
            --card-bg: #ffffff;
            --table-header-bg: #f8fafc;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
        }

        /* ===== MODE SOMBRE ===== */
        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --secondary-dark: #1B5E20;
            --success: #00A86B;
            --warning: #FFB800;
            --danger: #E31E24;
            --info: #17A2B8;
            --dark: #eeeeee;
            --gray-900: #e2e8f0;
            --gray-700: #cbd5e1;
            --gray-500: #94a3b8;
            --gray-300: #475569;
            --gray-100: #1e293b;
            --white: #1a1a2e;
            --bg-gradient-start: #0f0f1a;
            --bg-gradient-end: #1a1a2e;
            --card-bg: #16213e;
            --table-header-bg: #0f3460;
            --border-color: #2c3e50;
            --text-muted: #a0a0a0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
            transition: background 0.2s ease;
        }

        /* VIDEO BACKGROUND */
        .video-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            object-fit: cover;
        }
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.55);
            z-index: -1;
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

        /* ========== NAVBAR ========== */
        .navbar {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.5rem 2rem;
            border-bottom: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }
        [data-theme="dark"] .navbar {
            background: rgba(26, 26, 46, 0.98);
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
            color: var(--gray-700);
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

        /* ===== HERO SECTION PLEIN ÉCRAN ===== */
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
        .hero-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }
        .btn-hero {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 40px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
        }
        .btn-hero-primary {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .btn-hero-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.25);
        }
        .btn-hero-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        .btn-hero-outline:hover {
            background: white;
            color: var(--primary);
            transform: translateY(-3px);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ========== MAIN CONTENT ========== */
        .container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* Services Section */
        .services-section {
            padding: 3rem 0;
        }
        .section-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .section-title {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        .section-subtitle {
            color: var(--gray-500);
            font-size: 1rem;
        }
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        .service-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            box-shadow: var(--shadow-sm);
            cursor: pointer;
            border: 2px solid transparent;
        }
        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-xl);
            border-color: var(--primary);
        }
        .service-icon {
            width: 80px;
            height: 80px;
            background: var(--primary-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: var(--primary);
            font-size: 2rem;
            transition: all 0.3s;
        }
        .service-card:hover .service-icon {
            background: var(--primary);
            color: white;
            transform: scale(1.1);
        }
        .service-card h3 {
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        .service-card p {
            color: var(--gray-500);
            font-size: 0.85rem;
        }

        /* Onglets */
        .tabs-container {
            margin: 2rem 0;
        }
        .tabs {
            display: flex;
            gap: 1rem;
            border-bottom: 2px solid var(--border-color);
            padding-bottom: 0.5rem;
            flex-wrap: wrap;
        }
        .tab-btn {
            background: none;
            border: none;
            padding: 0.75rem 1.5rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--gray-500);
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px 8px 0 0;
            position: relative;
        }
        .tab-btn:hover {
            color: var(--primary);
            background: var(--primary-light);
        }
        .tab-btn.active {
            color: var(--primary);
            background: var(--primary-light);
        }
        .tab-btn.active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--primary);
            border-radius: 3px;
        }
        .tab-content {
            display: none;
            animation: fadeIn 0.4s ease;
        }
        .tab-content.active {
            display: block;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Cartes */
        .content-card {
            background: var(--card-bg);
            border-radius: 28px;
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.6rem 1.2rem;
            border-radius: 40px;
            font-weight: 500;
            text-decoration: none;
            transition: 0.2s;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }
        .btn--primary {
            background: var(--primary);
            color: white;
        }
        .btn--primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn--warning {
            background: #f59e0b;
            color: white;
        }
        .btn--danger {
            background: #ef4444;
            color: white;
        }

        /* Table styles */
        .table-panel {
            background: var(--card-bg);
            border-radius: 20px;
        }
        .panel-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            gap: 1rem;
        }
        .table-wrap {
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }
        th {
            background: var(--table-header-bg);
            font-weight: 600;
            color: var(--gray-700);
        }
        .actions {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }
        .badge--success { background: #dcfce7; color: #166534; }
        .badge--warning { background: #fef9c3; color: #854d0e; }
        .badge--danger { background: #fee2e2; color: #991b1b; }

        [data-theme="dark"] .badge--success { background: #1a4a2a; color: #a3e4b7; }
        [data-theme="dark"] .badge--warning { background: #4a3a1a; color: #f5d742; }
        [data-theme="dark"] .badge--danger { background: #4a1a1a; color: #f5a5a5; }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        /* Toast */
        .toast-stack {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
        }
        .toast {
            background: var(--card-bg);
            color: var(--dark);
            padding: 0.75rem 1.5rem;
            border-radius: 40px;
            margin-top: 0.5rem;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideIn 0.3s ease;
            border: 1px solid var(--border-color);
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Footer */
        .footer {
            background: var(--card-bg);
            color: var(--gray-500);
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
            border-top: 1px solid var(--border-color);
        }

        @media (max-width: 768px) {
            .navbar { padding: 0.5rem 1rem; }
            .logo-img { height: 32px; }
            .nav-links { gap: 0.8rem; }
            .hero-content h1 { font-size: 1.8rem; }
            .hero-content p { font-size: 0.9rem; }
            .container { padding: 0 1rem; }
            .services-grid { grid-template-columns: 1fr; }
            .tabs { flex-direction: column; gap: 0.5rem; border-bottom: none; }
            .tab-btn { text-align: center; border-radius: 40px; }
            .tab-btn.active::after { display: none; }
            .tab-btn.active { background: var(--primary); color: white; }
        }
    </style>
</head>
<body>

<!-- VIDEO BACKGROUND -->
<video class="video-bg" autoplay muted loop playsinline>
    <source src="../../assets/video/background.mp4" type="video/mp4">
</video>
<div class="bg-overlay"></div>

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

<!-- HERO SECTION -->
<section class="hero">
    <video class="hero-video" autoplay muted loop playsinline>
        <source src="../../assets/video/background.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Gestion des emplois et shifts</h1>
        <p>Gérez efficacement les horaires, les shifts et les affectations d'emplois</p>
        <div class="hero-buttons">
            <button class="btn-hero btn-hero-primary" onclick="scrollToSection('services')">
                <i class="fas fa-briefcase"></i> Voir les services
            </button>
            <button class="btn-hero btn-hero-outline" onclick="scrollToSection('tabs')">
                <i class="fas fa-calendar-alt"></i> Gérer les emplois
            </button>
        </div>
    </div>
</section>

<!-- SECTION SERVICES -->
<section id="services" class="services-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Nos Services en Ligne</h2>
            <p class="section-subtitle">Sélectionnez le service dont vous avez besoin et gérez vos plannifications immédiatement.</p>
        </div>
        <div class="services-grid">
            <div class="service-card" onclick="switchTab('shifts')">
                <div class="service-icon"><i class="fas fa-clock"></i></div>
                <h3>Shifts</h3>
                <p>Gérez les tranches horaires et les plannings des agents.</p>
            </div>
            <div class="service-card" onclick="switchTab('emplois')">
                <div class="service-icon"><i class="fas fa-briefcase"></i></div>
                <h3>Emplois</h3>
                <p>Affectez les agents aux différents services et shifts.</p>
            </div>
            <div class="service-card" onclick="if(<?= json_encode($canEdit) ?>) showAddShiftModal(); else showToast('Connectez-vous pour ajouter un shift', 'info');">
                <div class="service-icon"><i class="fas fa-plus"></i></div>
                <h3>Ajouter un shift</h3>
                <p>Créez de nouvelles tranches horaires pour les agents.</p>
            </div>
        </div>
    </div>
</section>

<!-- SECTION GESTION (TABS) -->
<div class="container" id="tabs">
    <div class="tabs-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="shifts">
                <i class="fas fa-clock"></i> Shifts
            </button>
            <button class="tab-btn" data-tab="emplois">
                <i class="fas fa-briefcase"></i> Emplois
            </button>
        </div>
    </div>

    <!-- Contenu Shifts -->
    <div id="shiftsTab" class="tab-content active">
        <div class="content-card">
            <div class="panel-toolbar">
                <div>
                    <h2><i class="fas fa-list"></i> Liste des shifts</h2>
                    <p style="color: var(--gray-500); font-size: 0.85rem;">Tranches horaires disponibles</p>
                </div>
                <?php if($canEdit): ?>
                    <button class="btn btn--primary" id="showAddShiftBtn">
                        <i class="fas fa-plus"></i> Ajouter un shift
                    </button>
                <?php endif; ?>
            </div>
            <div id="shiftListContainer">
                <?php if(count($shifts) > 0): ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nom</th>
                                    <th>Heure début</th>
                                    <th>Heure fin</th>
                                    <th>Durée</th>
                                    <?php if($canEdit): ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($shifts as $s): ?>
                                    <tr>
                                        <td><?= $s['id_shift'] ?></td>
                                        <td><strong><?= htmlspecialchars($s['nom_shift']) ?></strong></td>
                                        <td><?= $s['heure_debut'] ?></td>
                                        <td><?= $s['heure_fin'] ?></td>
                                        <td><span class="badge"><?= $s['duree'] ?></span></td>
                                        <?php if($canEdit): ?>
                                            <td class="actions">
                                                <button class="btn btn--warning edit-shift" data-id="<?= $s['id_shift'] ?>">
                                                    <i class="fas fa-pen"></i> Modifier
                                                </button>
                                                <button class="btn btn--danger delete-shift" data-id="<?= $s['id_shift'] ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-clock" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h3>Aucun shift trouvé</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Contenu Emplois -->
    <div id="emploisTab" class="tab-content">
        <div class="content-card">
            <div class="panel-toolbar">
                <div>
                    <h2><i class="fas fa-list"></i> Liste des emplois</h2>
                    <p style="color: var(--gray-500); font-size: 0.85rem;">Affectations des agents aux shifts</p>
                </div>
                <?php if($canEdit): ?>
                    <button class="btn btn--primary" id="showAddEmploiBtn">
                        <i class="fas fa-plus"></i> Ajouter un emploi
                    </button>
                <?php endif; ?>
            </div>
            <div id="emploiListContainer">
                <?php if(count($emplois) > 0): ?>
                    <div class="table-wrap">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Agent</th>
                                    <th>Service</th>
                                    <th>Shift</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <?php if($canEdit): ?>
                                        <th>Actions</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($emplois as $e): 
                                    $badgeClass = '';
                                    if($e['statut'] === 'termine') $badgeClass = 'badge--success';
                                    elseif($e['statut'] === 'annule') $badgeClass = 'badge--danger';
                                    else $badgeClass = 'badge--warning';
                                ?>
                                    <tr>
                                        <td><?= $e['id_emploi'] ?></td>
                                        <td><?= htmlspecialchars($e['agent_nom'] . ' ' . $e['agent_prenom']) ?></td>
                                        <td><?= htmlspecialchars($e['nom_service']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($e['nom_shift']) ?></strong>
                                            <br><span style="font-size: 0.75rem; color: var(--gray-500);"><?= $e['heure_debut'] ?> - <?= $e['heure_fin'] ?></span>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($e['date_travail'])) ?></td>
                                        <td><span class="badge <?= $badgeClass ?>"><?= ucfirst($e['statut']) ?></span></td>
                                        <?php if($canEdit): ?>
                                            <td class="actions">
                                                <button class="btn btn--warning edit-emploi" data-id="<?= $e['id_emploi'] ?>">
                                                    <i class="fas fa-pen"></i> Modifier
                                                </button>
                                                <button class="btn btn--danger delete-emploi" data-id="<?= $e['id_emploi'] ?>">
                                                    <i class="fas fa-trash"></i> Supprimer
                                                </button>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-briefcase" style="font-size: 3rem; opacity: 0.5;"></i>
                        <h3>Aucun emploi trouvé</h3>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

<div class="toast-stack" id="toastStack"></div>

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

    // Données statiques
    const staticData = {
        shifts: <?= json_encode($shifts) ?>,
        emplois: <?= json_encode($emplois) ?>,
        services: <?= json_encode($services) ?>,
        agents: <?= json_encode($agents) ?>
    };

    const canEdit = <?= json_encode($canEdit) ?>;
    let currentTab = 'shifts';

    function showToast(msg, type = 'success') {
        const stack = document.getElementById('toastStack');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> ${msg}`;
        stack.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    function scrollToSection(id) {
        document.getElementById(id).scrollIntoView({ behavior: 'smooth' });
    }

    // Gestion des onglets
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(tab => {
            tab.classList.remove('active');
        });
        document.getElementById(tabId + 'Tab').classList.add('active');
        
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            if(btn.getAttribute('data-tab') === tabId) {
                btn.classList.add('active');
            }
        });
        currentTab = tabId;
        scrollToSection('tabs');
    }

    // Écouteurs d'onglets
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            switchTab(tabId);
        });
    });

    // Actions Shifts (démo)
    document.querySelectorAll('.delete-shift').forEach(btn => {
        btn.addEventListener('click', () => {
            showToast("Suppression de shift (démo statique)", "info");
        });
    });

    document.querySelectorAll('.edit-shift').forEach(btn => {
        btn.addEventListener('click', () => {
            showToast("Modification de shift (démo statique)", "info");
        });
    });

    // Actions Emplois (démo)
    document.querySelectorAll('.delete-emploi').forEach(btn => {
        btn.addEventListener('click', () => {
            showToast("Suppression d'emploi (démo statique)", "info");
        });
    });

    document.querySelectorAll('.edit-emploi').forEach(btn => {
        btn.addEventListener('click', () => {
            showToast("Modification d'emploi (démo statique)", "info");
        });
    });

    // Boutons ajout (démo)
    document.getElementById('showAddShiftBtn')?.addEventListener('click', () => {
        showToast("Ajout de shift (démo statique)", "info");
    });

    document.getElementById('showAddEmploiBtn')?.addEventListener('click', () => {
        showToast("Ajout d'emploi (démo statique)", "info");
    });

    // NAVBAR SCROLL
    window.addEventListener('scroll', () => {
        const navbar = document.getElementById('navbar');
        if (window.scrollY > 50) navbar.classList.add('scrolled');
        else navbar.classList.remove('scrolled');
    });
</script>
</body>
</html>