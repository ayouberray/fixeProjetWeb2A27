<?php
session_start();
if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";

$ctrl = new ReclamationController();
$list = $ctrl->getReclamationByCitoyen($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Services Municipaux Digitalisés</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --bg-color: #F5FCF9;
            --text-color: #1A2E2A;
            --card-bg: white;
            --nav-bg: rgba(245, 252, 249, 0.95);
            --border-color: #E5E7EB;
            --th-bg: #F0FDF4;
            --th-color: #006D5B;
            --td-border: #E5E7EB;
            --nav-link-color: #2C5A4F;
        }

        [data-theme="dark"] {
            --bg-color: #0f172a;
            --text-color: #f8fafc;
            --card-bg: #1e293b;
            --nav-bg: rgba(15, 23, 42, 0.95);
            --border-color: #334155;
            --th-bg: #0f172a;
            --th-color: #38bdf8;
            --td-border: #334155;
            --nav-link-color: #94a3b8;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg-color);
            color: var(--text-color);
            min-height: 100vh;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        /* ========== SLIDESHOW BACKGROUND ========== */
        .hero {
            position: relative;
            min-height: 85vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            overflow: hidden;
        }

        .hero-slideshow {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .hero-slideshow .slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-size: cover;
            background-position: center;
            opacity: 0;
            transition: opacity 1.5s ease;
        }

        .hero-slideshow .slide.active {
            opacity: 5;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 109, 91, 0.75) 0%, rgba(0, 77, 61, 0.85) 100%);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 2rem;
            color: white;
        }

        .hero h1 {
            font-size: 48px;
            font-weight: 800;
            margin-bottom: 20px;
            font-family: 'Syne', sans-serif;
            color: white;
        }

        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }

        /* ========== NAVBAR ========== */
        .navbar {
            background: var(--nav-bg);
            backdrop-filter: blur(10px);
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 2rem;
            border-bottom: 1px solid var(--border-color);
            transition: background-color 0.3s ease;
        }
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            padding: 1rem 2rem;
            border-bottom: 1px solid rgba(0, 109, 91, 0.1);
        }

        .navbar-container {
            max-width: 1400px;
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
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, #006D5B, #004D3D);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 22px;
        }

        .logo-text h1 {
            font-size: 22px;
            font-weight: 800;
            color: #006D5B;
        }

        .logo-text p {
            font-size: 11px;
            color: #5C8B7E;
        }

        .nav-menu {
            display: flex;
            gap: 2rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: var(--nav-link-color);
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #006D5B;
        }

        [data-theme="dark"] .nav-link:hover {
            color: #38bdf8;
        }

        .lang-toggle {
            display: flex;
            gap: 5px;
            background: var(--card-bg);
            padding: 5px 10px;
            border-radius: 30px;
            border: 1px solid var(--border-color);
        }

        .lang-btn {
            background: none;
            border: none;
            padding: 5px 12px;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
        }

        .lang-btn.active {
            background: #006D5B;
            color: white;
        }

        /* ========== BOUTONS ET THEME ========== */
        .theme-btn {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--text-color);
            transition: color 0.3s;
        }
        .theme-btn:hover {
            color: #006D5B;
        }
        [data-theme="dark"] .theme-btn:hover {
            color: #38bdf8;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 32px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary {
            background: linear-gradient(135deg, #006D5B, #004D3D);
            color: white;
            box-shadow: 0 4px 16px rgba(0, 109, 91, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0, 109, 91, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: translateY(-3px);
        }

        /* ========== SECTION MES RÉCLAMATIONS (CARDS + SLIDESHOW) ========== */
        .reclamations-section {
            position: relative;
            padding: 60px 20px;
            overflow: hidden;
            min-height: 500px;
        }

        .rec-slideshow { position: absolute; inset: 0; z-index: 0; }
        .rec-slideshow .slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.5s ease; }
        .rec-slideshow .slide.active { opacity: 1; }
        .rec-overlay { position: absolute; inset: 0; background: linear-gradient(135deg, rgba(0,77,61,0.85) 0%, rgba(0,20,15,0.92) 100%); z-index: 1; }

        .rec-content { position: relative; z-index: 2; max-width: 1200px; margin: 0 auto; }

        .rec-header { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 35px; }
        .rec-header h2 { font-size: 28px; font-weight: 800; color: white; font-family: 'Syne', sans-serif; }

        .search-container {
            display: flex;
            align-items: center;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.3);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 10px 18px;
            width: 100%;
            max-width: 350px;
        }
        .search-container i { color: rgba(255,255,255,0.8); margin-right: 10px; }
        .search-input { border: none; background: transparent; outline: none; width: 100%; font-family: 'DM Sans', sans-serif; font-size: 14px; color: white; }
        .search-input::placeholder { color: rgba(255,255,255,0.6); }

        .cards-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

        .rec-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px;
            padding: 22px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .rec-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.3); border-color: rgba(255,255,255,0.4); }
        .rec-card-ref { font-size: 11px; font-weight: 700; color: rgba(255,255,255,0.55); text-transform: uppercase; letter-spacing: 1px; }
        .rec-card-objet { font-size: 17px; font-weight: 700; color: white; line-height: 1.4; }
        .rec-card-footer { display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 8px; margin-top: auto; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.15); }
        .rec-card-date { font-size: 12px; color: rgba(255,255,255,0.55); }

        .badge { padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; }
        .badge-soumise  { background: rgba(253,230,138,0.2); color: #FDE68A; border: 1px solid rgba(253,230,138,0.35); }
        .badge-en_cours { background: rgba(147,197,253,0.2); color: #93C5FD; border: 1px solid rgba(147,197,253,0.35); }
        .badge-traitee  { background: rgba(110,231,183,0.2); color: #6EE7B7; border: 1px solid rgba(110,231,183,0.35); }
        .badge-rejetee  { background: rgba(252,165,165,0.2); color: #FCA5A5; border: 1px solid rgba(252,165,165,0.35); }

        .btn-sm { display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px; font-size: 12px; font-weight: 600; background: rgba(255,255,255,0.15); color: white; border: 1px solid rgba(255,255,255,0.3); border-radius: 30px; text-decoration: none; transition: background 0.2s; }
        .btn-sm:hover { background: rgba(255,255,255,0.28); }

        .btn-deposer { display: inline-block; margin-top: 20px; background: rgba(255,255,255,0.15); color: white; padding: 12px 28px; border-radius: 30px; border: 2px solid rgba(255,255,255,0.4); text-decoration: none; font-weight: 600; transition: all 0.3s; }
        .btn-deposer:hover { background: rgba(255,255,255,0.25); }

        /* ========== FOOTER ========== */
        .footer {
            background: linear-gradient(180deg, #0D3328, #0A281E);
            color: white;
            padding: 40px 2rem 30px;
            margin-top: 60px;
        }

        .footer-container {
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
        }

        .footer-logo {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .footer-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
        }

        .footer-links a:hover {
            color: white;
        }

        @media (max-width: 768px) {
            .hero h1 { font-size: 32px; }
            .navbar-container { flex-direction: column; }
            .nav-menu { justify-content: center; }
            .hero-buttons { flex-direction: column; align-items: center; }
            .btn { width: 100%; justify-content: center; }
            .footer-links { flex-direction: column; gap: 10px; }
            .reclamations-section { margin: 20px; padding: 20px; overflow-x: auto; }
        }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="navbar-container">
        <a href="mes-reclamations.php" class="logo">
            <div class="logo-icon"><i class="fas fa-building"></i></div>
            <div class="logo-text">
                <h1>InnoGov</h1>
                <p>Municipalité Tunisienne</p>
            </div>
        </a>
        <div class="nav-menu">
            <a href="mes-reclamations.php" class="nav-link">Accueil</a>
            <a href="mes-reclamations.php" class="nav-link">Réclamation</a>
            <a href="../REPONSE/voir.php" class="nav-link">Réponse</a>
            <button class="theme-btn" id="themeToggle" title="Changer le thème">
                <i class="fas fa-moon"></i>
            </button>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">عربي</button>
            </div>
        </div>
    </div>
</nav>

<!-- HERO AVEC SLIDESHOW EN ARRIÈRE-PLAN -->
<section class="hero">
    <div class="hero-slideshow">
        <div class="slide active" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia1.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia2.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia3.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE/ASSETS/IMAGES/tunisia4.jpg');"></div>
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez la gestion des réclamations dans une interface claire et moderne.</p>
        <div class="hero-buttons">
            <a href="ajouter.php" class="btn btn-primary">📝 Déposer une réclamation</a>
            <a href="mes-reclamations.php" class="btn btn-outline">📋 Mes réclamations</a>
        </div>
    </div>
</section>

<!-- SECTION MES RÉCLAMATIONS -->
<section class="reclamations-section">
    <!-- Slideshow background -->
    <div class="rec-slideshow" id="recSlideshow">
        <div class="slide active" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia1.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia2.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia3.jpg');"></div>
        <div class="slide" style="background-image: url('/PROJETFIXE_v2/ASSETS/images/tunisia4.jpg');"></div>
    </div>
    <div class="rec-overlay"></div>

    <div class="rec-content">
        <div class="rec-header">
            <h2><i class="fas fa-list-alt"></i> Mes Réclamations</h2>
            <?php if(!empty($list)): ?>
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="Rechercher..." oninput="filterCards()">
            </div>
            <?php endif; ?>
        </div>

        <?php if(empty($list)): ?>
            <div style="text-align: center; padding: 60px; color: rgba(255,255,255,0.7);">
                <i class="fas fa-inbox fa-4x" style="margin-bottom: 20px; opacity: 0.5; display: block;"></i>
                <p style="font-size: 18px; margin-bottom: 20px;">Aucune réclamation pour le moment</p>
                <a href="ajouter.php" class="btn-deposer">📝 Déposer une réclamation</a>
            </div>
        <?php else: ?>
            <div class="cards-grid" id="cardsGrid">
                <?php foreach($list as $r): ?>
                <div class="rec-card" data-search="<?= strtolower(htmlspecialchars($r['reference'] . ' ' . $r['objet'] . ' ' . $r['statut'])) ?>">
                    <div class="rec-card-ref"><i class="fas fa-hashtag"></i> <?= htmlspecialchars($r['reference']) ?></div>
                    <div class="rec-card-objet"><?= htmlspecialchars($r['objet']) ?></div>
                    <div class="rec-card-footer">
                        <div>
                            <span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst(str_replace('_',' ',$r['statut'])) ?></span>
                        </div>
                        <div class="rec-card-date"><i class="far fa-calendar-alt"></i> <?= date('d/m/Y', strtotime($r['date_soumission'])) ?></div>
                    </div>
                    <a href="../REPONSE/voir.php?id_reclamation=<?= $r['id_reclamation'] ?>" class="btn-sm">
                        <i class="fas fa-eye"></i> Voir la réponse
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <div style="text-align:center; margin-top: 30px;">
                <a href="ajouter.php" class="btn-deposer">➕ Nouvelle réclamation</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- FOOTER -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-logo">InnoGov</div>
        <div class="footer-links">
            <a href="mes-reclamations.php">Accueil</a>
            <a href="mes-reclamations.php">Réclamation</a>
            <a href="../REPONSE/voir.php">Réponse</a>
            <a href="#">AR</a>
        </div>
        <div class="footer-copyright">Municipalité Tunisienne &copy; 2025</div>
    </div>
</footer>

<script>
    // SLIDESHOW AUTOMATIQUE toutes les 3 secondes
    const slides = document.querySelectorAll('.hero-slideshow .slide');
    if (slides.length > 0) {
        let currentSlide = 0;
        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (i === index) {
                    slide.classList.add('active');
                }
            });
        }
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
        setInterval(nextSlide, 3000);
    }

    // Langue toggle
    const langBtns = document.querySelectorAll('.lang-btn');
    langBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            langBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            if(this.dataset.lang === 'ar') {
                document.documentElement.dir = 'rtl';
            } else {
                document.documentElement.dir = 'ltr';
            }
        });
    });

    // Dark Mode Toggle
    const themeToggleBtn = document.getElementById('themeToggle');
    const htmlElement = document.documentElement;
    const themeIcon = themeToggleBtn.querySelector('i');

    // Vérifier le thème sauvegardé
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        htmlElement.setAttribute('data-theme', 'dark');
        themeIcon.classList.replace('fa-moon', 'fa-sun');
    }

    themeToggleBtn.addEventListener('click', () => {
        // Fonction vocale
        function dire(texte) {
            if ('speechSynthesis' in window) {
                window.speechSynthesis.cancel();
                let msg = new SpeechSynthesisUtterance(texte);
                msg.lang = 'en-US'; // Prononciation en anglais pour "Night" et "Light"
                msg.volume = 1;
                window.speechSynthesis.speak(msg);
            }
        }

        if (htmlElement.getAttribute('data-theme') === 'dark') {
            htmlElement.removeAttribute('data-theme');
            localStorage.setItem('theme', 'light');
            themeIcon.classList.replace('fa-sun', 'fa-moon');
            dire("Light");
            
            Swal.fire({
                title: 'Mode Clair Activé ☀️',
                text: 'L\'interface est passée en mode clair.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        } else {
            htmlElement.setAttribute('data-theme', 'dark');
            localStorage.setItem('theme', 'dark');
            themeIcon.classList.replace('fa-moon', 'fa-sun');
            dire("Night");
            
            Swal.fire({
                title: 'Mode Sombre Activé 🌙',
                text: 'L\'interface est passée en mode sombre.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: '#1e293b',
                color: '#f8fafc'
            });
        }
    });

    // Slideshow Section Réclamations
    const recSlides = document.querySelectorAll('#recSlideshow .slide');
    if (recSlides.length > 0) {
        let recCurrent = 0;
        setInterval(() => {
            recSlides[recCurrent].classList.remove('active');
            recCurrent = (recCurrent + 1) % recSlides.length;
            recSlides[recCurrent].classList.add('active');
        }, 3000);
    }

    // Recherche intelligente dans les cartes (ID exact ou globale)
    function filterCards() {
        const input = document.getElementById('searchInput');
        if (!input) return;
        const filter = input.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.rec-card');
        
        let isIdSearch = /^#?\d+$/.test(filter);
        let cleanFilter = filter.replace('#', '');

        cards.forEach(card => {
            if (isIdSearch) {
                // Recherche exacte par référence/ID
                const refEl = card.querySelector('.rec-card-ref');
                if (refEl) {
                    let refText = refEl.textContent.replace(/\D/g, ''); // Extraction des chiffres
                    card.style.display = (refText === cleanFilter) ? '' : 'none';
                }
            } else {
                // Recherche globale classique
                const text = card.getAttribute('data-search') || '';
                card.style.display = text.includes(filter) ? '' : 'none';
            }
        });
    }
</script>

</body>
</html>