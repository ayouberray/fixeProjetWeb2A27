<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

require_once __DIR__."/MODEL/config.php";

$db = Config::getConnexion();

$totalCitoyens = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'citoyen'")->fetch();
$totalServices = $db->query("SELECT COUNT(*) as total FROM services WHERE statut = 'actif'")->fetch();
$totalRdvs = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut = 'termine'")->fetch();
$services = $db->query("SELECT * FROM services WHERE statut = 'actif' LIMIT 6")->fetchAll();

$news = [
    ['title' => 'Lancement de la plateforme InnoGov', 'date' => '10 Avril 2024', 'excerpt' => 'Nouvelle plateforme pour faciliter les démarches administratives...', 'image' => '/projet/assets/images/news/news1.jpg'],
    ['title' => 'Réunion du conseil municipal', 'date' => '05 Avril 2024', 'excerpt' => 'Discussion sur les projets de développement de la ville...', 'image' => '/projet/assets/images/news/news2.jpg'],
    ['title' => 'Nouveau service en ligne', 'date' => '01 Avril 2024', 'excerpt' => 'Découvrez notre nouveau service de prise de rendez-vous en ligne...', 'image' => '/projet/assets/images/news/news3.jpg']
];
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Services Municipaux Digitalisés</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-building"></i></div>
                <div class="logo-text">
                    <h1>InnoGov</h1>
                    <p>Municipalité Tunisienne</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/index.php" class="nav-link active">Accueil</a>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link">Mes RDV</a>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">عربي</button>
            </div>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary btn-sm">Prendre RDV</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION AVEC VIDÉO DYNAMIQUE -->
<section class="hero">
    <video class="hero-video" autoplay loop muted playsinline>
        <source src="/projet/assets/video/background.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne</p>
        <div class="hero-buttons">
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
            <a href="#services" class="btn btn-outline">En savoir plus</a>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Chiffres Clés</h2>
        <div class="stats-grid">
            <div class="stat-card reveal">
                <i class="fas fa-users"></i>
                <div class="number" data-target="<?= $totalCitoyens['total'] ?>">0</div>
                <div class="label">Citoyens</div>
            </div>
            <div class="stat-card reveal">
                <i class="fas fa-concierge-bell"></i>
                <div class="number" data-target="<?= $totalServices['total'] ?>">0</div>
                <div class="label">Services</div>
            </div>
            <div class="stat-card reveal">
                <i class="fas fa-calendar-check"></i>
                <div class="number" data-target="<?= $totalRdvs['total'] ?>">0</div>
                <div class="label">RDV traités</div>
            </div>
            <div class="stat-card reveal">
                <i class="fas fa-award"></i>
                <div class="number" data-target="5">0</div>
                <div class="label">Années d'expérience</div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="services">
    <div class="container">
        <h2 class="section-title">Nos Services</h2>
        <div class="services-grid">
            <?php foreach($services as $service): ?>
            <div class="service-card reveal">
                <div class="service-icon">
                    <i class="fas <?= $service['id_service'] == 1 ? 'fa-file-alt' : ($service['id_service'] == 2 ? 'fa-draw-polygon' : ($service['id_service'] == 3 ? 'fa-id-card' : ($service['id_service'] == 4 ? 'fa-road' : 'fa-trash-alt'))) ?>"></i>
                </div>
                <h3><?= htmlspecialchars($service['nom_service']) ?></h3>
                <p><?= htmlspecialchars($service['description'] ?? 'Service municipal disponible en ligne') ?></p>
                <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary btn-sm">Prendre RDV</a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">Actualités</h2>
        <div class="news-grid">
            <?php foreach($news as $item): ?>
            <div class="news-card reveal">
                <div class="news-image">
                    <img src="<?= $item['image'] ?>" alt="Actualité">
                </div>
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> <?= $item['date'] ?></span>
                    <h3 class="news-title"><?= $item['title'] ?></h3>
                    <p class="news-excerpt"><?= $item['excerpt'] ?></p>
                    <a href="#" class="btn btn-outline btn-sm" style="margin-top:15px;">Lire la suite</a>
                </div>
            </div>
            <?php endforeach; ?>
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
            <h4><i class="fas fa-clock"></i> Horaires</h4>
            <p>Lundi - Vendredi: 8h30 - 15h30</p>
            <p>Samedi - Dimanche: Fermé</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2024 InnoGov - Tous droits réservés</p>
    </div>
</footer>

</body>
</html>