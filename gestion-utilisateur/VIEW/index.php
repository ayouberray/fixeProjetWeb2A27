<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Transformation numérique en Tunisie</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        /* ============================================
           INNOGOV - DESIGN SYSTEM (CSS INTÉGRÉ)
           Couleurs : #006D5B (vert principal)
        ============================================ */
        :root {
            --primary: #006D5B;
            --primary-dark: #004D3D;
            --primary-light: #E6F4F0;
            --secondary: #2E7D32;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-600: #475569;
            --gray-800: #1E293B;
            --shadow-sm: 0 2px 8px rgba(0, 109, 91, 0.08);
            --shadow-md: 0 4px 15px rgba(0, 109, 91, 0.12);
            --shadow-lg: 0 10px 30px rgba(0, 109, 91, 0.15);
            --shadow-hover: 0 20px 35px rgba(0, 109, 91, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--white);
            color: var(--gray-800);
            overflow-x: hidden;
        }

        /* ===== LOADER ===== */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            transition: opacity 0.5s ease;
        }

        .loader.hide {
            opacity: 0;
            pointer-events: none;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid var(--primary-light);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* ===== SCROLLBAR ===== */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: var(--gray-100); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-dark); }

        /* ===== NAVBAR ===== */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 2rem;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .navbar.scrolled {
            background: white;
            box-shadow: var(--shadow-md);
        }

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
            gap: 0.75rem;
            cursor: pointer;
            text-decoration: none;
            transition: transform 0.3s;
        }

        .logo:hover { transform: scale(1.02); }

        .logo-icon {
            width: 42px;
            height: 42px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
        }

        .logo-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary);
        }

        .logo-text span {
            font-weight: 400;
            color: var(--secondary);
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
            transition: color 0.3s;
        }

        .nav-links a:hover { color: var(--primary); }

        .lang-toggle {
            display: flex;
            gap: 0.5rem;
            background: var(--gray-100);
            padding: 0.3rem;
            border-radius: 30px;
        }

        .lang-btn {
            padding: 0.3rem 0.8rem;
            border: none;
            background: transparent;
            cursor: pointer;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .lang-btn.active {
            background: var(--primary);
            color: white;
        }

        .btn-login, .btn-register {
            padding: 8px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
        }

        .btn-login { background: var(--primary); color: white; }
        .btn-register { background: var(--secondary); color: white; }

        /* ===== HERO VIDEO ===== */
        .hero-video {
            position: relative;
            width: 100%;
            min-height: 100vh;
        }

        .hero-video video {
            display: block;
            width: 100%;
            height: auto;
            min-height: 100vh;
            object-fit: contain;
            background: #000;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.45);
            pointer-events: none;
        }

        .hero-content {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 2rem;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: fadeInUp 0.8s ease;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 0.8s ease 0.2s both;
        }

        .btn-hero {
            background: var(--primary);
            color: white;
            padding: 12px 30px;
            border-radius: 30px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-hero:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
        }

        /* ===== SECTIONS ===== */
        .section {
            padding: 5rem 2rem;
            position: relative;
            z-index: 10;
        }

        .section:nth-child(even) { background: var(--gray-50); }

        .container {
            max-width: 1280px;
            margin: 0 auto;
        }

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
            background: white;
            border-radius: 20px;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
        }

        .stat-label {
            color: var(--gray-600);
            margin-top: 0.5rem;
        }

        /* ===== SERVICES ===== */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .service-card {
            background: white;
            padding: 2rem;
            border-radius: 20px;
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
            cursor: pointer;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

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

        .service-icon i {
            font-size: 2rem;
            color: var(--primary);
        }

        .service-card h3 {
            margin-bottom: 0.5rem;
            color: var(--gray-800);
        }

        .service-card p {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        /* ===== NEWS ===== */
        .news-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
        }

        .news-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            transition: all 0.3s;
        }

        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-hover);
        }

        .news-image {
            height: 200px;
            background-size: cover;
            background-position: center;
            background-color: var(--primary-light);
        }

        .news-content {
            padding: 1.5rem;
        }

        .news-date {
            font-size: 0.8rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }

        .news-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .news-excerpt {
            color: var(--gray-600);
            font-size: 0.9rem;
        }

        /* ===== FOOTER ===== */
        .footer {
            background: var(--gray-800);
            color: #94a3b8;
            padding: 3rem 2rem 2rem;
            position: relative;
            z-index: 10;
        }

        .footer-container {
            max-width: 1280px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
        }

        .footer h4 {
            color: white;
            margin-bottom: 1rem;
        }

        .footer p {
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .footer a {
            color: #94a3b8;
            text-decoration: none;
        }

        .footer a:hover { color: var(--primary); }

        .footer-bottom {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #334155;
        }

        /* ===== SCROLL REVEAL ===== */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: all 0.8s ease;
        }

        .reveal.active {
            opacity: 1;
            transform: translateY(0);
        }

        /* ===== ANIMATIONS ===== */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 768px) {
            .stats-grid,
            .services-grid,
            .news-grid,
            .footer-container {
                grid-template-columns: 1fr;
            }
            
            .hero-content h1 {
                font-size: 2rem;
            }
            
            .navbar {
                padding: 1rem;
            }
            
            .nav-links {
                justify-content: center;
            }
            
            .section {
                padding: 3rem 1rem;
            }
        }
    </style>
</head>
<body>

<!-- LOADER -->
<div id="loader" class="loader">
    <div class="spinner"></div>
</div>

<!-- NAVBAR -->
<nav id="navbar" class="navbar">
    <div class="nav-container">
        <a href="index.php" class="logo">
            <div class="logo-icon"><i class="fas fa-leaf"></i></div>
            <div class="logo-text">inno<span>Gov</span></div>
        </a>
        <div class="nav-links">
            <a href="#">Accueil</a>
            <a href="#services">Services</a>
            <a href="#contact">Contact</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="gestion-utilisateur/VIEW/frontoffice/login.php" class="btn-login">Connexion</a>
            <a href="gestion-utilisateur/VIEW/frontoffice/register.php" class="btn-register">Inscription</a>
        </div>
    </div>
</nav>

<!-- HERO AVEC VIDÉO -->
<section class="hero-video">
    <video autoplay muted loop playsinline>
        <source src="gestion-utilisateur/assets/video/background.mp4" type="video/mp4">
        Votre navigateur ne supporte pas la vidéo.
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Bienvenue sur innoGov</h1>
        <p>Digitaliser aujourd'hui, servir mieux demain</p>
        <a href="gestion-utilisateur/VIEW/frontoffice/register.php" class="btn-hero">Créer un compte</a>
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
            <div class="service-card reveal">
                <div class="service-icon"><i class="fas fa-briefcase"></i></div>
                <h3>Offres d'emploi</h3>
                <p>Consultez les offres dans la fonction publique</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon"><i class="fas fa-graduation-cap"></i></div>
                <h3>Concours</h3>
                <p>Inscrivez-vous aux concours administratifs</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon"><i class="fas fa-calendar-check"></i></div>
                <h3>Rendez-vous</h3>
                <p>Prenez RDV en ligne</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon"><i class="fas fa-file-alt"></i></div>
                <h3>Demandes</h3>
                <p>Passeport, CIN, extraits...</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon"><i class="fas fa-comment-dots"></i></div>
                <h3>Réclamations</h3>
                <p>Signalez un problème</p>
            </div>
            <div class="service-card reveal">
                <div class="service-icon"><i class="fas fa-robot"></i></div>
                <h3>Assistant IA</h3>
                <p>Chatbot intelligent 24h/24</p>
            </div>
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
                <div class="news-image" style="background-image: url('gestion-utilisateur/assets/images/news/news1.jpg');"></div>
                <div class="news-content">
                    <div class="news-date">15 Avril 2026</div>
                    <h3 class="news-title">Nouveau service en ligne</h3>
                    <p class="news-excerpt">Le renouvellement du passeport est désormais possible en ligne.</p>
                </div>
            </div>
            <div class="news-card reveal">
                <div class="news-image" style="background-image: url('gestion-utilisateur/assets/images/news/news2.jpg');"></div>
                <div class="news-content">
                    <div class="news-date">10 Avril 2026</div>
                    <h3 class="news-title">Concours de la fonction publique</h3>
                    <p class="news-excerpt">Les inscriptions sont ouvertes jusqu'au 30 mai.</p>
                </div>
            </div>
            <div class="news-card reveal">
                <div class="news-image" style="background-image: url('gestion-utilisateur/assets/images/news/news3.jpg');"></div>
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
            <p><a href="#">Accueil</a></p>
            <p><a href="#services">Services</a></p>
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
    // ===== LOADER =====
    window.addEventListener('load', function() {
        const loader = document.getElementById('loader');
        if (loader) {
            setTimeout(() => {
                loader.classList.add('hide');
            }, 500);
        }
    });

    // ===== NAVBAR SCROLL =====
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('navbar');
        if (navbar) {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    });

    // ===== SCROLL REVEAL =====
    const revealElements = document.querySelectorAll('.reveal');
    function checkReveal() {
        revealElements.forEach(element => {
            const rect = element.getBoundingClientRect();
            if (rect.top < window.innerHeight - 100) {
                element.classList.add('active');
            }
        });
    }
    window.addEventListener('scroll', checkReveal);
    window.addEventListener('load', checkReveal);

    // ===== COMPTEURS ANIMÉS =====
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

    // ===== TOGGLE LANGUE =====
    let currentLang = 'fr';
    const langBtns = document.querySelectorAll('.lang-btn');
    langBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const lang = btn.getAttribute('data-lang');
            currentLang = lang;
            langBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            if (lang === 'ar') {
                document.body.style.direction = 'rtl';
                document.body.style.textAlign = 'right';
            } else {
                document.body.style.direction = 'ltr';
                document.body.style.textAlign = 'left';
            }
        });
    });

    // ===== SMOOTH SCROLL =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
</script>
</body>
</html>