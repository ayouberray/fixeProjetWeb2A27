<?php
require_once __DIR__ . "/CONTROLLER/OffreController.php";
require_once __DIR__ . "/CONTROLLER/CandidatureController.php";
require_once __DIR__ . "/MODEL/config.php";

$controller = isset($_GET['controller']) ? $_GET['controller'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($controller === 'offre') {
    $ctrl = new OffreController();
    switch ($action) {
        case 'lister': $ctrl->listerOffres(); break;
        case 'detail': if ($id) $ctrl->detailOffre($id); else header("Location: index.php"); break;
        case 'admin-lister': $ctrl->adminListerOffres(); break;
        case 'ajouter': $ctrl->ajouterOffre(); break;
        case 'modifier': if ($id) $ctrl->modifierOffre($id); break;
        case 'supprimer': if ($id) $ctrl->supprimerOffre($id); break;
        case 'carte': include __DIR__ . '/VIEW/frontoffice/map.php'; exit;
        default: $ctrl->listerOffres();
    }
    exit;
} elseif ($controller === 'candidature') {
    $ctrl = new CandidatureController();
    switch ($action) {
        case 'postuler': $ctrl->postuler(); break;
        case 'admin-lister': $ctrl->adminListerCandidatures(); break;
        case 'traiter': $ctrl->traiterCandidature(); break;
        case 'telecharger-cv': if ($id) $ctrl->telechargerCV($id); break;
        case 'badge': 
            $badgeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
            if ($badgeId) include __DIR__ . '/VIEW/frontoffice/badge.php';
            else header('Location: index.php');
            break;
        default: header("Location: index.php?controller=offre&action=lister");
    }
    exit;
}
$db = Config::getConnexion();
try {
    $test = $db->query("SELECT 1");
    echo "Connexion BDD OK<br>";
} catch (Exception $e) {
    die("Erreur BDD : " . $e->getMessage());
}
$totalOffres = $db->query("SELECT COUNT(*) as total FROM offre")->fetch();
$totalOffresOuvertes = $db->query("SELECT COUNT(*) as total FROM offre WHERE statut = 'Ouvert'")->fetch();
$totalCandidatures = $db->query("SELECT COUNT(*) as total FROM condidature")->fetch();
$dernieresOffres = $db->query("SELECT * FROM offre ORDER BY id_offre DESC LIMIT 6")->fetchAll();
$news = [
    ['title' => 'Lancement de la plateforme de recrutement', 'date' => '10 Avril 2025', 'excerpt' => 'Postulez en ligne aux offres municipales...', 'image' => '/ProjettWeb/assets/images/news1.jpg'],
    ['title' => 'Nouvelle offre : Développeur Full Stack', 'date' => '05 Avril 2025', 'excerpt' => 'Rejoignez la direction du numérique...', 'image' => '/ProjettWeb/assets/images/news2.jpg'],
    ['title' => 'Mise à jour du module candidature', 'date' => '01 Avril 2025', 'excerpt' => 'Déposez votre CV en toute simplicité.', 'image' => '/ProjettWeb/assets/images/news3.jpg']
];
?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>INNOC@V - Offres & Candidatures</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css?v=<?= time() ?>">
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo">
            <img src="/ProjettWeb/assets/images/logo.png" alt="INNOGOV" style="height: 60px; object-fit: contain;">
        </a>
        <div class="nav-menu">
            <a href="index.php" class="nav-link active" data-i18n="home">Accueil</a>
            <a href="index.php?controller=offre&action=lister" class="nav-link" data-i18n="offers">Offres</a>
            <a href="index.php?controller=offre&action=admin-lister" class="nav-link" data-i18n="admin">Admin Offres</a>
            <a href="index.php?controller=candidature&action=admin-lister" class="nav-link" data-i18n="candidatures">Candidatures</a>
            <div class="lang-toggle">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
                <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
                <a href="index.php?controller=offre&action=carte" class="lang-btn" title="Carte des municipalités" style="text-decoration:none; display:flex; align-items:center;">
                    <i class="fas fa-map-marked-alt"></i>
                </a>
            </div>
            <a href="index.php?controller=offre&action=lister" class="btn btn-primary btn-sm">
                <i class="fas fa-search"></i> <span data-i18n="seeOffers">Voir les offres</span>
            </a>
        </div>
    </div>
</nav>
<section class="hero">
    <video class="hero-video" autoplay loop muted playsinline>
        <source src="/ProjettWeb/assets/video/background.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="hero-badge" data-i18n="heroBadge">✨ Recrutement public simplifié</div>
        <h1 data-i18n="heroTitle">Offres d'emploi<br>municipales digitalisées</h1>
        <p data-i18n="heroDesc">Postulez en ligne aux offres de la fonction publique territoriale</p>
        <div class="hero-buttons">
            <a href="index.php?controller=offre&action=lister" class="btn btn-primary">
                <i class="fas fa-briefcase"></i> <span data-i18n="heroBtn1">Découvrir les offres</span>
            </a>
            <a href="#services" class="btn btn-outline">
                <i class="fas fa-info-circle"></i> <span data-i18n="heroBtn2">En savoir plus</span>
            </a>
        </div>
    </div>
</section>
<section class="section stats-section reveal">
    <div class="container">
        <h2 class="section-title" data-i18n="statsTitle">Chiffres Clés</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <i class="fas fa-briefcase"></i>
                <div class="number" data-target="<?= $totalOffres['total'] ?>">0</div>
                <div class="label" data-i18n="totalOffers">Total offres</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-door-open"></i>
                <div class="number" data-target="<?= $totalOffresOuvertes['total'] ?>">0</div>
                <div class="label" data-i18n="openOffers">Offres ouvertes</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-file-signature"></i>
                <div class="number" data-target="<?= $totalCandidatures['total'] ?>">0</div>
                <div class="label" data-i18n="totalApplications">Candidatures reçues</div>
            </div>
            <div class="stat-card">
                <i class="fas fa-chart-line"></i>
                <div class="number" data-target="98">0</div>
                <div class="label" data-i18n="satisfaction">Taux de satisfaction</div>
            </div>
        </div>
    </div>
</section>
<section id="services" class="section services-section reveal">
    <div class="container">
        <h2 class="section-title" data-i18n="latestOffers">📢 Dernières offres</h2>
        <div class="services-grid">
            <?php if (count($dernieresOffres) > 0): ?>
                <?php foreach ($dernieresOffres as $offre): ?>
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3><?= htmlspecialchars($offre['titre']) ?></h3>
                    <p><?= htmlspecialchars(substr($offre['description'], 0, 80)) ?>...</p>
                    <p><strong>Entité :</strong> <?= htmlspecialchars($offre['entite']) ?></p>
                    <p><strong>Date limite :</strong> <?= $offre['date_limite'] ?></p>
                    <a href="index.php?controller=offre&action=detail&id=<?= $offre['id_offre'] ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Voir l'offre
                    </a>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Aucune offre disponible pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>
<section class="section news-section reveal">
    <div class="container">
        <h2 class="section-title" data-i18n="newsTitle">Actualités recrutement</h2>
        <div class="news-grid">
            <?php foreach ($news as $item): ?>
            <div class="news-card">
                <div class="news-image">
                    <img src="<?= $item['image'] ?>" alt="Actualité">
                </div>
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> <?= $item['date'] ?></span>
                    <h3 class="news-title"><?= $item['title'] ?></h3>
                    <p class="news-excerpt"><?= $item['excerpt'] ?></p>
                    <a href="#" class="btn btn-outline btn-sm" data-i18n="readMore">Lire la suite</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<footer class="footer">
    <div class="footer-container">
        <div class="footer-section">
            <h4><i class="fas fa-building"></i> INNOC@V</h4>
            <p data-i18n="footerDesc">Plateforme de gestion des offres et candidatures pour les municipalités</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-phone"></i> Contact</h4>
            <p><i class="fas fa-phone-alt"></i> +216 70 000 000</p>
            <p><i class="fas fa-envelope"></i> recrutement@innocv.tn</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-clock"></i> Support</h4>
            <p data-i18n="monFri">Lundi - Vendredi: 8h30 - 15h30</p>
            <p data-i18n="online">Formulaire de contact 24h/24</p>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; 2025 INNOC@V - <span data-i18n="allRights">Tous droits réservés</span></p>
    </div>
</footer>

</body>
</html>