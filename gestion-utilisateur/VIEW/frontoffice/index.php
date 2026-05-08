<?php
session_start();
?>

<!DOCTYPE html>
<html lang="fr" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="user-id" content="<?php echo $_SESSION['user_id'] ?? ''; ?>">
    <title>innoGov | Espace Citoyen</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ===== VARIABLES MODE CLAIR/SOMBRE ===== */
        :root {
            /* Mode clair (défaut) */
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-hover: 0 20px 25px -5px rgb(0 0 0 / 0.1);
            --bg-body: #ffffff;
            --card-bg: #ffffff;
            --footer-bg: #1a1a1a;
            --footer-text: #94a3b8;
            --border-light: rgba(0, 0, 0, 0.05);
            --navbar-bg: rgba(255, 255, 255, 0.98);
            --text-muted: #475569;
        }

        /* Mode sombre */
        [data-theme="dark"] {
            --primary: #2ecc71;
            --primary-dark: #27ae60;
            --primary-light: #1a3a2a;
            --secondary: #2E7D32;
            --white: #1a1a2e;
            --gray-100: #1a1a2e;
            --gray-200: #16213e;
            --gray-600: #a0a0a0;
            --gray-800: #eeeeee;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.4);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.5);
            --shadow-hover: 0 20px 25px -5px rgba(0, 0, 0, 0.6);
            --bg-body: #0f0f1a;
            --card-bg: #16213e;
            --footer-bg: #0a0a0f;
            --footer-text: #888888;
            --border-light: rgba(255, 255, 255, 0.05);
            --navbar-bg: rgba(26, 26, 46, 0.98);
            --text-muted: #a0a0a0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--gray-800);
        }

        /* ===== SWITCH MODE SOMBRE/CLAIR (dans navbar) ===== */
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

        input:checked + .slider {
            background-color: var(--primary);
        }

        input:checked + .slider:before {
            transform: translateX(24px);
        }

        .theme-icon {
            font-size: 14px;
        }

        .light-icon, .dark-icon {
            display: none;
        }

        [data-theme="light"] .light-icon {
            display: inline;
        }

        [data-theme="light"] .dark-icon {
            display: none;
        }

        [data-theme="dark"] .light-icon {
            display: none;
        }

        [data-theme="dark"] .dark-icon {
            display: inline;
        }

        /* Adaptation pour le mode sombre */
        [data-theme="dark"] .stat-card,
        [data-theme="dark"] .service-card,
        [data-theme="dark"] .news-card {
            background: var(--card-bg);
        }

        [data-theme="dark"] .service-icon {
            background: var(--primary-light);
        }

        [data-theme="dark"] .navbar {
            background: var(--navbar-bg);
            border-bottom-color: var(--border-light);
        }

        [data-theme="dark"] .navbar.scrolled {
            background: var(--card-bg);
        }

        [data-theme="dark"] .section:nth-child(even) {
            background: var(--gray-200);
        }

        [data-theme="dark"] .user-name {
            background: var(--primary-light);
            color: var(--primary);
        }

        [data-theme="dark"] .btn-login {
            color: var(--primary);
            border-color: var(--primary);
        }

        [data-theme="dark"] .btn-login:hover {
            background: var(--primary);
            color: white;
        }

        [data-theme="dark"] .footer {
            background: var(--footer-bg);
            color: var(--footer-text);
        }

        [data-theme="dark"] .news-excerpt,
        [data-theme="dark"] .service-card p,
        [data-theme="dark"] .stat-label,
        [data-theme="dark"] .section-subtitle {
            color: var(--text-muted);
        }

        /* ===== LOADER ===== */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--bg-body);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.3s ease;
        }
        .loader.hide { opacity: 0; pointer-events: none; }
        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--primary-light);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }

        /* ===== NAVBAR PROFESSIONNELLE ===== */
        .navbar {
            background: var(--navbar-bg);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 0.5rem 2rem;
            border-bottom: 1px solid var(--border-light);
            transition: background 0.2s ease;
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
            color: var(--gray-600);
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
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
            transition: all 0.2s ease;
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
            transition: all 0.2s ease;
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
            transition: all 0.2s ease;
        }
        .btn-register:hover { background: var(--primary-dark); transform: translateY(-1px); }

        /* ===== HERO VIDEO ===== */
        .hero-video {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .hero-video video {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.5) 0%, rgba(0, 109, 91, 0.3) 100%);
            z-index: 1;
        }
        .hero-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: min(100%, 900px);
            text-align: center;
            color: white;
            padding: 0 1.5rem;
            z-index: 2;
        }
        .hero-content h1 {
            font-size: clamp(2rem, 4vw, 3.5rem);
            font-weight: 800;
            margin-bottom: 1rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }
        .hero-content p {
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto 1.75rem;
            opacity: 0.95;
        }
        .btn-hero {
            background: var(--primary);
            color: white;
            border-radius: 40px;
            padding: 0.85rem 1.8rem;
            border: none;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }
        .btn-hero:hover { background: var(--primary-dark); transform: translateY(-2px); }
        .scroll-indicator {
            position: absolute;
            bottom: 32px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 10;
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            cursor: pointer;
            transition: transform 0.2s ease;
        }
        .scroll-indicator:hover { transform: translateX(-50%) translateY(-3px); }

        /* ===== SECTIONS ===== */
        .section { padding: 5rem 2rem; position: relative; z-index: 10; }
        .section:nth-child(even) { background: var(--gray-100); }
        .container { max-width: 1280px; margin: 0 auto; }
        .section-title {
            text-align: center;
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        .section-subtitle {
            text-align: center;
            color: var(--gray-600);
            margin-bottom: 3rem;
        }

        /* ===== STATS ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }
        .stat-card {
            text-align: center;
            padding: 2rem;
            background: var(--card-bg);
            border-radius: 24px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
        .stat-number { font-size: 2.8rem; font-weight: 800; color: var(--primary); }
        .stat-label { color: var(--gray-600); margin-top: 0.5rem; }

        /* ===== SERVICES ===== */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        .service-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 24px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            text-decoration: none;
            display: block;
            color: inherit;
        }
        .service-card:hover { transform: translateY(-6px); box-shadow: var(--shadow-md); }
        .service-icon {
            width: 70px;
            height: 70px;
            background: var(--primary-light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
        }
        .service-icon i { font-size: 2rem; color: var(--primary); }
        .service-card h3 { margin-bottom: 0.5rem; color: var(--gray-800); }
        .service-card p { color: var(--gray-600); font-size: 0.9rem; line-height: 1.5; }

        /* ===== NEWS ===== */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }
        .news-card {
            background: var(--card-bg);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .news-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-md); }
        .news-image { height: 200px; background-size: cover; background-position: center; }
        .news-content { padding: 1.5rem; }
        .news-date { font-size: 0.8rem; color: var(--primary); margin-bottom: 0.5rem; }
        .news-title { font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; }
        .news-excerpt { color: var(--gray-600); font-size: 0.9rem; line-height: 1.5; }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--footer-bg);
            color: var(--footer-text);
            text-align: center;
            padding: 3rem 2rem 2rem;
            margin-top: 2rem;
        }
        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            text-align: left;
        }
        .footer h4 { color: white; margin-bottom: 1rem; font-size: 1rem; }
        .footer p { margin-bottom: 0.5rem; font-size: 0.85rem; }
        .footer a { color: var(--footer-text); text-decoration: none; transition: color 0.2s ease; }
        .footer a:hover { color: var(--primary); }
        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #334155;
            font-size: 0.8rem;
        }

        /* ===== SCROLL REVEAL ===== */
        .reveal { opacity: 0; transform: translateY(30px); transition: all 0.6s ease; }
        .reveal.active { opacity: 1; transform: translateY(0); }

        @media (max-width: 1024px) {
            .stats-grid, .services-grid, .news-grid, .footer-container { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 768px) {
            .stats-grid, .services-grid, .news-grid, .footer-container { grid-template-columns: 1fr; }
            .navbar { padding: 0.4rem 1rem; }
            .logo-img { height: 32px; }
            .nav-links { gap: 0.8rem; }
            .hero-content h1 { font-size: 1.8rem; }
            .hero-content p { font-size: 0.9rem; }
            .btn-hero { padding: 0.6rem 1.2rem; font-size: 0.85rem; }
            .section { padding: 3rem 1rem; }
        }
    </style>
</head>
<body>

<!-- LOADER -->
<div id="loader" class="loader"><div class="spinner"></div></div>

<!-- NAVBAR -->
<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <img src="../../assets/images/logo.png" alt="Logo" class="logo-img">
        </a>
        <div class="nav-links">
            <a href="index.php">Accueil</a>
            <a href="#" id="servicesLink">Services</a>
            
            <!-- SWITCH MODE SOMBRE/CLAIR DANS LA NAVBAR -->
            <div class="theme-switch-wrapper">
                <span class="theme-icon light-icon">☀️</span>
                <label class="theme-switch">
                    <input type="checkbox" id="theme-toggle">
                    <span class="slider"></span>
                </label>
                <span class="theme-icon dark-icon">🌙</span>
            </div>
            
            <?php if(isset($_SESSION['user_id'])): ?>
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

<!-- HERO AVEC VIDÉO -->
<section class="hero-video">
    <video autoplay muted loop playsinline>
        <source src="../../assets/video/background.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo.
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne, 24h/24 et 7j/7</p>
        <?php if(!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="btn-hero"><i class="fas fa-user-plus"></i> Créer un compte</a>
        <?php endif; ?>
    </div>
    <div class="scroll-indicator" onclick="document.getElementById('services').scrollIntoView({behavior: 'smooth'})">
        <i class="fas fa-chevron-down"></i>
    </div>
</section>

<!-- CHIFFRES CLÉS -->
<section class="section">
    <div class="container">
        <h2 class="section-title reveal">Chiffres clés</h2>
        <p class="section-subtitle reveal">innoGov en quelques chiffres</p>
        <div class="stats-grid">
            <div class="stat-card reveal">
                <div class="stat-number counter" data-target="50000">0</div>
                <div class="stat-label">Citoyens connectés</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-number counter" data-target="120">0</div>
                <div class="stat-label">Services en ligne</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-number counter" data-target="98">0</div>
                <div class="stat-label">Taux de satisfaction (%)</div>
            </div>
            <div class="stat-card reveal">
                <div class="stat-number counter" data-target="24">0</div>
                <div class="stat-label">Assistance 24h/24</div>
            </div>
        </div>
    </div>
</section>

<!-- SERVICES -->
<section id="services" class="section">
    <div class="container">
        <h2 class="section-title reveal">Nos services</h2>
        <p class="section-subtitle reveal">Des services digitaux simples et rapides</p>
        <div class="services-grid">
            <a href="emploi.php?section=emplois" class="service-card reveal">
                <div class="service-icon"><i class="fas fa-briefcase"></i></div>
                <h3>Offres d'emploi</h3>
                <p>Consultez les offres dans la fonction publique</p>
            </a>
            <a href="concours.php" class="service-card reveal">
                <div class="service-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Concours</h3>
                <p>Inscrivez-vous aux concours administratifs</p>
            </a>
            <a href="rendez_vous.php" class="service-card reveal">
                <div class="service-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Rendez-vous</h3>
                <p>Prenez RDV en ligne</p>
            </a>
            <a href="demandes.php" class="service-card reveal">
                <div class="service-icon"><i class="fas fa-file-alt"></i></div>
                <h3>Demandes</h3>
                <p>Passeport, CIN, extraits...</p>
            </a>
            <a href="reclamations.php" class="service-card reveal">
                <div class="service-icon"><i class="fas fa-comment-dots"></i></div>
                <h3>Réclamations</h3>
                <p>Signalez un problème</p>
            </a>
        </div>
    </div>
</section>

<!-- ACTUALITÉS -->
<section class="section">
    <div class="container">
        <h2 class="section-title reveal">Actualités</h2>
        <p class="section-subtitle reveal">Restez informés</p>
        <div class="news-grid">
            <div class="news-card reveal">
                <div class="news-image" style="background-image: url('../../assets/images/news/news1.jpg');"></div>
                <div class="news-content">
                    <div class="news-date">15 Avril 2026</div>
                    <h3 class="news-title">Nouveau service en ligne</h3>
                    <p class="news-excerpt">Le renouvellement du passeport est désormais possible en ligne.</p>
                </div>
            </div>
            <div class="news-card reveal">
                <div class="news-image" style="background-image: url('../../assets/images/news/news2.jpg');"></div>
                <div class="news-content">
                    <div class="news-date">10 Avril 2026</div>
                    <h3 class="news-title">Concours de la fonction publique</h3>
                    <p class="news-excerpt">Les inscriptions sont ouvertes jusqu'au 30 mai.</p>
                </div>
            </div>
            <div class="news-card reveal">
                <div class="news-image" style="background-image: url('../../assets/images/news/news3.jpg');"></div>
                <div class="news-content">
                    <div class="news-date">1 Avril 2026</div>
                    <h3 class="news-title">Transformation numérique</h3>
                    <p class="news-excerpt">Lancement de la nouvelle plateforme innoGov.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer" id="contact">
    <div class="footer-container">
        <div>
            <h4>innoGov</h4>
            <p>Digitaliser aujourd'hui, servir mieux demain</p>
            <p>🇹🇳 Tunisie</p>
        </div>
        <div>
            <h4>Liens rapides</h4>
            <p><a href="index.php">Accueil</a></p>
            <p><a href="#" id="servicesLinkFooter">Services</a></p>
            <p><a href="#contact">Contact</a></p>
        </div>
        <div>
            <h4>Horaires</h4>
            <p>Lundi - Vendredi: 8h00 - 17h00</p>
            <p>Samedi: 9h00 - 13h00</p>
            <p>Dimanche: Fermé</p>
        </div>
        <div>
            <h4>Contact</h4>
            <p><i class="fas fa-phone"></i> +216 70 000 000</p>
            <p><i class="fas fa-envelope"></i> contact@innogov.tn</p>
            <p><i class="fas fa-map-marker-alt"></i> Tunis, Tunisie</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2026 innoGov - Tous droits réservés</p>
    </div>
</footer>

<script>
    // ===== MODE SOMBRE/CLAIR - INSTANTANÉ =====
    (function() {
        const themeToggle = document.getElementById('theme-toggle');
        const htmlElement = document.documentElement;
        const THEME_KEY = 'innogov_theme';
        
        function setTheme(theme) {
            htmlElement.setAttribute('data-theme', theme);
            localStorage.setItem(THEME_KEY, theme);
            if (themeToggle) {
                themeToggle.checked = (theme === 'dark');
            }
        }
        
        function initTheme() {
            const savedTheme = localStorage.getItem(THEME_KEY);
            if (savedTheme) {
                setTheme(savedTheme);
            } else {
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                setTheme(prefersDark ? 'dark' : 'light');
            }
        }
        
        if (themeToggle) {
            themeToggle.addEventListener('change', function() {
                const newTheme = this.checked ? 'dark' : 'light';
                setTheme(newTheme);
                // Pas d'animation, pas de délai - changement instantané
            });
        }
        
        initTheme();
    })();

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

    // SCROLL TO SERVICES
    function scrollToServices() {
        const servicesSection = document.getElementById('services');
        if (servicesSection) {
            servicesSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    const servicesLink = document.getElementById('servicesLink');
    const servicesLinkFooter = document.getElementById('servicesLinkFooter');
    if (servicesLink) servicesLink.addEventListener('click', function(e) { e.preventDefault(); scrollToServices(); });
    if (servicesLinkFooter) servicesLinkFooter.addEventListener('click', function(e) { e.preventDefault(); scrollToServices(); });

    // SCROLL REVEAL
    const revealElements = document.querySelectorAll('.reveal');
    function checkReveal() {
        revealElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100) element.classList.add('active');
        });
    }
    window.addEventListener('scroll', checkReveal);
    window.addEventListener('load', checkReveal);

    // COMPTEURS ANIMÉS
    const counters = document.querySelectorAll('.counter');
    let animated = false;
    function animateCounters() {
        if (animated) return;
        const triggerPoint = window.innerHeight * 0.8;
        const countersSection = document.querySelector('.stats-grid');
        if (countersSection && countersSection.getBoundingClientRect().top < triggerPoint) {
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-target'));
                let current = 0;
                const increment = target / 50;
                const updateCounter = () => {
                    current += increment;
                    if (current < target) {
                        counter.innerText = Math.ceil(current);
                        requestAnimationFrame(updateCounter);
                    } else {
                        counter.innerText = target;
                    }
                };
                updateCounter();
            });
            animated = true;
        }
    }
    window.addEventListener('scroll', animateCounters);
    window.addEventListener('load', animateCounters);
</script>
</body>
</html>