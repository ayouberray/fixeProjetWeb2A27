<?php
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$user_nom = $isLoggedIn ? ($_SESSION['user_nom'] ?? 'Utilisateur') : 'Visiteur';
$user_prenom = $isLoggedIn ? ($_SESSION['user_prenom'] ?? '') : '';
$user_initials = $isLoggedIn ? strtoupper(substr($user_prenom, 0, 1) . substr($user_nom, 0, 1)) : 'MB';
$canEdit = $isLoggedIn && ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'agent');

// Données statiques des concours
$concours = [
    [
        'id' => 1,
        'titre' => 'Concours Administratif',
        'organisme' => 'Fonction Publique',
        'date_limite' => '30/04/2026',
        'postes' => 50,
        'inscrits' => 234,
        'description' => 'Recrutement pour les postes administratifs dans les ministères.',
        'type' => 'administratif'
    ],
    [
        'id' => 2,
        'titre' => 'Concours Technicien Supérieur',
        'organisme' => 'Ministère de l\'Éducation',
        'date_limite' => '15/05/2026',
        'postes' => 30,
        'inscrits' => 156,
        'description' => 'Techniciens supérieurs pour les établissements scolaires.',
        'type' => 'technique'
    ],
    [
        'id' => 3,
        'titre' => 'Concours Ingénieur d\'État',
        'organisme' => 'Ministère de l\'Industrie',
        'date_limite' => '01/06/2026',
        'postes' => 20,
        'inscrits' => 89,
        'description' => 'Ingénieurs pour les projets industriels et technologiques.',
        'type' => 'ingenieur'
    ],
    [
        'id' => 4,
        'titre' => 'Concours Agent de Maîtrise',
        'organisme' => 'Municipalité de Tunis',
        'date_limite' => '10/06/2026',
        'postes' => 45,
        'inscrits' => 312,
        'description' => 'Agents pour les services municipaux.',
        'type' => 'technique'
    ]
];
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="user-id" content="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <title>InnoGov | Concours administratifs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css">
    <style>
        /* ===== VARIABLES MODE CLAIR/SOMBRE ===== */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --gray-700: #4A5A6E;
            --gray-800: #1E293B;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            --bg-gradient-start: #f0fdf4;
            --bg-gradient-end: #dcfce7;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-muted: #64748b;
            --stats-bg: #ffffff;
            --footer-bg: #1a1a1a;
            --footer-text: #94a3b8;
            --navbar-bg: rgba(255, 255, 255, 0.96);
        }

        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --white: #1a1a2e;
            --gray-100: #1a1a2e;
            --gray-200: #16213e;
            --gray-600: #a0a0a0;
            --gray-700: #cbd5e1;
            --gray-800: #eeeeee;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
            --bg-gradient-start: #0f0f1a;
            --bg-gradient-end: #1a1a2e;
            --card-bg: #16213e;
            --border-color: #2c3e50;
            --text-muted: #a0a0a0;
            --stats-bg: #16213e;
            --footer-bg: #0a0a0f;
            --footer-text: #888888;
            --navbar-bg: rgba(26, 26, 46, 0.96);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, var(--bg-gradient-start) 0%, var(--bg-gradient-end) 100%);
            min-height: 100vh;
            overflow-x: hidden;
            transition: background 0.2s ease;
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

        /* ===== LOADER ===== */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--primary);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }
        .loader.hide { opacity: 0; pointer-events: none; }
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid rgba(255,255,255,0.3);
            border-top: 3px solid white;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ===== NAVBAR ===== */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.6rem 2rem;
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
            transition: transform 0.2s ease;
        }
        .logo:hover { transform: scale(1.02); }
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
            gap: 1.5rem;
            flex-wrap: wrap;
        }
        .nav-links a {
            text-decoration: none;
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            cursor: pointer;
        }
        .nav-links a:hover { color: var(--primary); }
        .lang-toggle {
            display: flex;
            gap: 0.3rem;
            background: var(--gray-100);
            padding: 0.2rem;
            border-radius: 30px;
        }
        .lang-btn {
            padding: 0.3rem 0.8rem;
            border: none;
            background: transparent;
            border-radius: 30px;
            font-weight: 500;
            font-size: 0.75rem;
            cursor: pointer;
            color: var(--gray-600);
        }
        .lang-btn.active { background: var(--primary); color: white; }
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
            font-size: 0.85rem;
            transition: all 0.2s ease;
        }
        .btn-profiled:hover { background: var(--primary-dark); transform: translateY(-1px); }
        .btn-logout {
            background: #dc2626;
            color: white;
            padding: 0.4rem 1.2rem;
            border-radius: 30px;
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.2s ease;
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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* ===== CONTAINER ===== */
        .container {
            max-width: 1280px;
            margin: 2rem auto;
            padding: 0 2rem;
        }

        /* ===== CONCOURS GRID ===== */
        .concours-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(380px, 1fr));
            gap: 2rem;
        }
        .concours-card {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 1.8rem;
            box-shadow: var(--shadow-md);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .concours-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }
        .concours-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-xl);
        }
        .concours-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gray-800);
            margin-bottom: 0.5rem;
        }
        .concours-organisme {
            color: var(--primary);
            font-size: 0.85rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .concours-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 0.8rem 0;
            padding: 0.6rem 0;
            border-bottom: 1px solid var(--border-color);
        }
        .info-label {
            font-weight: 600;
            color: var(--gray-600);
            font-size: 0.85rem;
        }
        .info-value {
            color: var(--gray-800);
            font-weight: 700;
            font-size: 1rem;
        }
        .concours-description {
            color: var(--gray-600);
            font-size: 0.85rem;
            line-height: 1.5;
            margin: 1rem 0;
            padding-top: 0.5rem;
        }
        .btn-register {
            background: var(--primary);
            color: white;
            padding: 0.7rem 1.5rem;
            border-radius: 40px;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            width: 100%;
            justify-content: center;
            margin-top: 1rem;
            border: none;
            cursor: pointer;
        }
        .btn-register:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }
        .btn-register.disabled {
            background: var(--gray-300);
            cursor: not-allowed;
            opacity: 0.6;
        }
        [data-theme="dark"] .btn-register.disabled {
            background: var(--gray-600);
        }

        /* ===== INFO BANNER ===== */
        .info-banner {
            background: var(--card-bg);
            backdrop-filter: blur(8px);
            border-radius: 16px;
            padding: 1rem 1.5rem;
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        .info-banner i { color: var(--primary); margin-right: 0.5rem; }
        .info-banner a { color: var(--primary); font-weight: 600; text-decoration: none; }

        /* ===== STATS ===== */
        .stats-section {
            padding: 3rem 0;
            background: var(--stats-bg);
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: center;
        }
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--primary);
        }
        .stat-card p {
            color: var(--gray-600);
            font-size: 0.85rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--footer-bg);
            color: var(--footer-text);
            text-align: center;
            padding: 2rem;
            margin-top: 3rem;
        }

        /* ===== TOAST ===== */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: var(--primary);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 40px;
            z-index: 1000;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @media (max-width: 768px) {
            .navbar { padding: 0.4rem 1rem; }
            .logo-img { height: 32px; }
            .nav-links { gap: 0.8rem; }
            .hero-content h1 { font-size: 1.8rem; }
            .hero-content p { font-size: 0.9rem; }
            .container { padding: 0 1rem; }
            .concours-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>

<div class="loader" id="loader"><div class="spinner"></div></div>

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
            <a href="index.php#services">Services</a>
            
            <!-- SWITCH MODE SOMBRE/CLAIR -->
            <div class="theme-switch-wrapper">
                <span class="theme-icon light-icon">☀️</span>
                <label class="theme-switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider"></span>
                </label>
                <span class="theme-icon dark-icon">🌙</span>
            </div>
            
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <?php if($isLoggedIn): ?>
                <span class="user-name">👋 <?= htmlspecialchars($_SESSION['user_nom']) ?></span>
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
        <h1>Concours administratifs</h1>
        <p>Préparez votre avenir avec la fonction publique tunisienne</p>
    </div>
</section>

<!-- STATS SECTION -->
<section class="stats-section">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?= count($concours) ?></h3>
                <p>Concours actifs</p>
            </div>
            <div class="stat-card">
                <h3><?= array_sum(array_column($concours, 'postes')) ?></h3>
                <p>Postes à pourvoir</p>
            </div>
            <div class="stat-card">
                <h3><?= array_sum(array_column($concours, 'inscrits')) ?>+</h3>
                <p>Candidats inscrits</p>
            </div>
            <div class="stat-card">
                <h3>98%</h3>
                <p>Taux de satisfaction</p>
            </div>
        </div>
    </div>
</section>

<!-- CONCOURS SECTION -->
<div class="container">
    <?php if(!$isLoggedIn): ?>
        <div class="info-banner">
            <i class="fas fa-info-circle"></i> Vous n'êtes pas connecté. 
            <a href="login.php">Connectez-vous</a> pour vous inscrire aux concours.
        </div>
    <?php endif; ?>

    <div class="concours-grid">
        <?php foreach($concours as $c): ?>
            <div class="concours-card" data-aos="fade-up">
                <div class="concours-title"><?= htmlspecialchars($c['titre']) ?></div>
                <div class="concours-organisme">
                    <i class="fas fa-building"></i> <?= htmlspecialchars($c['organisme']) ?>
                </div>
                <div class="concours-info">
                    <span class="info-label"><i class="far fa-calendar-alt"></i> Date limite</span>
                    <span class="info-value"><?= $c['date_limite'] ?></span>
                </div>
                <div class="concours-info">
                    <span class="info-label"><i class="fas fa-users"></i> Postes disponibles</span>
                    <span class="info-value"><?= $c['postes'] ?></span>
                </div>
                <div class="concours-info">
                    <span class="info-label"><i class="fas fa-user-check"></i> Candidats inscrits</span>
                    <span class="info-value"><?= $c['inscrits'] ?></span>
                </div>
                <div class="concours-description">
                    <?= htmlspecialchars($c['description']) ?>
                </div>
                <?php if($isLoggedIn): ?>
                    <a href="#" class="btn-register" onclick="showToast('Inscription au concours (démo)'); return false;">
                        <i class="fas fa-pen-alt"></i> S'inscrire
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn-register">
                        <i class="fas fa-sign-in-alt"></i> Se connecter pour s'inscrire
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Digitaliser aujourd'hui, servir mieux demain</p>
    <p style="font-size: 0.8rem; margin-top: 0.5rem;">🇹🇳 Tunisie</p>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
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

    AOS.init({
        duration: 800,
        once: true,
        offset: 100
    });

    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = '<i class="fas fa-check-circle"></i> ' + message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }

    // LOADER
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) setTimeout(() => loader.classList.add('hide'), 500);
    });

    // NAVBAR SCROLL
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            if (window.scrollY > 50) navbar.classList.add('scrolled');
            else navbar.classList.remove('scrolled');
        }
    });

    // LANGUE
    const langBtns = document.querySelectorAll('.lang-btn');
    langBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.getAttribute('data-lang');
            langBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (lang === 'ar') {
                document.body.style.direction = 'rtl';
                document.body.style.textAlign = 'right';
                // Ajuster les icônes pour RTL
                document.querySelectorAll('.concours-info .info-label i').forEach(icon => {
                    icon.style.marginLeft = '8px';
                    icon.style.marginRight = '0';
                });
            } else {
                document.body.style.direction = 'ltr';
                document.body.style.textAlign = 'left';
                document.querySelectorAll('.concours-info .info-label i').forEach(icon => {
                    icon.style.marginLeft = '0';
                    icon.style.marginRight = '8px';
                });
            }
        });
    });
</script>
</body>
</html>