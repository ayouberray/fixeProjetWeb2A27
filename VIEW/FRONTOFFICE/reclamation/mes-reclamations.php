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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #F5FCF9;
            color: #1A2E2A;
            min-height: 100vh;
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
            background: rgba(245, 252, 249, 0.95);
            backdrop-filter: blur(10px);
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
            color: #2C5A4F;
            font-weight: 500;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: #006D5B;
        }

        .lang-toggle {
            display: flex;
            gap: 5px;
            background: #EBF7F3;
            padding: 5px 10px;
            border-radius: 30px;
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

        /* ========== BOUTONS ========== */
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

        /* ========== CARTE MES RÉCLAMATIONS ========== */
        .reclamations-section {
            background: white;
            border-radius: 30px;
            margin: 40px auto;
            padding: 30px;
            max-width: 1200px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
        }

        .reclamations-section h2 {
            font-size: 24px;
            color: #006D5B;
            margin-bottom: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            text-align: left;
            padding: 15px;
            background: #F0FDF4;
            color: #006D5B;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #E5E7EB;
        }

        .badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-soumise { background: #FEF3C7; color: #92400E; }
        .badge-en_cours { background: #DBEAFE; color: #1E40AF; }
        .badge-traitee { background: #D1FAE5; color: #065F46; }
        .badge-rejetee { background: #FEE2E2; color: #991B1B; }

        .btn-sm {
            display: inline-block;
            padding: 6px 12px;
            font-size: 12px;
            background: #006D5B;
            color: white;
            border-radius: 8px;
            text-decoration: none;
        }

        .btn-deposer {
            display: inline-block;
            margin-top: 20px;
            background: #006D5B;
            color: white;
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
        }

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
<div class="reclamations-section">
    <h2><i class="fas fa-list-alt"></i> Mes réclamations</h2>
    
    <?php if(empty($list)): ?>
        <div style="text-align: center; padding: 40px; color: #9CA3AF;">
            <i class="fas fa-inbox fa-3x" style="margin-bottom: 15px; opacity: 0.3;"></i>
            <p>Aucune réclamation pour le moment</p>
            <a href="ajouter.php" class="btn-deposer">📝 Déposer une réclamation</a>
        </div>
    <?php else: ?>
        <div style="overflow-x: auto;">
            <table class="table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Objet</th>
                        <th>Statut</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($list as $r): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r['reference']) ?></strong></td>
                        <td><?= htmlspecialchars(substr($r['objet'], 0, 50)) ?>...</td>
                        <td><span class="badge badge-<?= $r['statut'] ?>"><?= ucfirst($r['statut']) ?></span></td>
                        <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                        <td><a href="../REPONSE/voir.php?id_reclamation=<?= $r['id_reclamation'] ?>" class="btn-sm"><i class="fas fa-eye"></i> Voir réponse</a></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

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
</script>

</body>
</html>