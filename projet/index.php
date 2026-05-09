<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

// Pseudo-cron for reminders
$lock_file = __DIR__ . '/api/.reminder_last_run';
$should_run = true;
if (file_exists($lock_file)) {
    if (time() - (int)file_get_contents($lock_file) < 1800) $should_run = false;
}
if ($should_run) {
    file_put_contents($lock_file, time());
    register_shutdown_function(function() { @include __DIR__ . '/api/send_reminders.php'; });
}

require_once __DIR__."/MODEL/config.php";
$db = Config::getConnexion();

$totalCitoyens = $db->query("SELECT COUNT(*) as total FROM users WHERE role = 'citoyen'")->fetch();
$totalServices = $db->query("SELECT COUNT(*) as total FROM services WHERE statut = 'actif'")->fetch();
$totalRdvs = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut = 'termine'")->fetch();
$services = $db->query("SELECT * FROM services WHERE statut = 'actif' LIMIT 6")->fetchAll();

$parService = $db->query("
    SELECT s.nom_service, COUNT(r.id_rdv) as total 
    FROM services s
    LEFT JOIN rendez_vous r ON r.id_service = s.id_service
    WHERE s.statut = 'actif'
    GROUP BY s.id_service, s.nom_service
    ORDER BY total DESC
    LIMIT 4
")->fetchAll();

$news = [
    ['title' => 'InnoGov v2.0 est là !', 'date' => '09 Mai 2026', 'excerpt' => 'Découvrez une interface 3D totalement repensée pour vos démarches...', 'image' => '/Gestion_RDV/projet/assets/images/tunisia1.jpg'],
    ['title' => 'Nouveaux Services Digitaux', 'date' => '05 Mai 2026', 'excerpt' => 'La municipalité de Tunis étend son catalogue de services en ligne...', 'image' => '/Gestion_RDV/projet/assets/images/tunisia3.jpg'],
    ['title' => 'IA au service du citoyen', 'date' => '01 Mai 2026', 'excerpt' => 'Notre assistant InnoBot est désormais capable d\'analyser vos documents...', 'image' => '/Gestion_RDV/projet/assets/images/tunisia4.jpg']
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov | Modernité Municipale</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gestion_RDV/projet/assets/css/style.css??v=20260509_v9">
    <script src="/Gestion_RDV/projet/assets/js/script.js" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/Gestion_RDV/projet/index.php" class="nav-link active">Accueil</a>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link">Mes RDV</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle"><i class="fas fa-sun"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta">Prendre RDV</a>
        </div>
    </nav>
</div>

<!-- HERO -->
<section class="hero">
    <div class="hero-slideshow">
        <img src="/Gestion_RDV/projet/assets/images/tunisia1.jpg" class="slide active">
        <img src="/Gestion_RDV/projet/assets/images/tunisia2.jpg" class="slide">
        <img src="/Gestion_RDV/projet/assets/images/tunisia3.jpg" class="slide">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <div class="modal-badge reveal" style="background: rgba(255,255,255,0.2); color: white; border: 1px solid rgba(255,255,255,0.3);">
            ✨ Excellence Numérique
        </div>
        <h1 class="reveal">Demain se prépare<br>Aujourd'hui.</h1>
        <p class="reveal">Accédez à tous vos services municipaux avec une interface immersive et sécurisée.</p>
        <div class="hero-buttons reveal">
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
            <a href="#stats" class="btn btn-outline">Voir l'impact</a>
        </div>
    </div>
</section>

<!-- STATS -->
<section id="stats" class="section">
    <div class="container">
        <h2 class="section-title reveal">Municipalité en Chiffres</h2>
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
                <i class="fas fa-check-double"></i>
                <div class="number" data-target="<?= $totalRdvs['total'] ?>">0</div>
                <div class="label">RDV Traités</div>
            </div>
            <div class="stat-card reveal">
                <i class="fas fa-rocket"></i>
                <div class="number" data-target="99">0</div>
                <div class="label">Satisfaction</div>
            </div>
        </div>
    </div>
</section>

<!-- CHART & TOP SERVICES -->
<section class="section" style="background: var(--bg-page);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 40px; align-items: center;">
            <div class="card reveal" style="padding: 40px; border-radius: 30px; border: 1px solid var(--glass-border); box-shadow: var(--deep-shadow);">
                <h3 style="font-family: 'Montserrat'; font-weight: 800; margin-bottom: 30px;"><i class="fas fa-chart-pie" style="color: var(--primary);"></i> Répartition des Services</h3>
                <div style="height: 300px;"><canvas id="homeChart"></canvas></div>
            </div>
            <div class="reveal">
                <h2 style="font-family: 'Montserrat'; font-weight: 800; font-size: 2.5rem; margin-bottom: 20px;">Nos Services<br>les plus sollicités</h2>
                <p style="color: var(--gray-700); margin-bottom: 30px;">InnoGov analyse en temps réel les besoins des citoyens pour adapter les ressources municipales.</p>
                <div style="display: flex; flex-direction: column; gap: 15px;">
                    <?php foreach($parService as $ps): ?>
                        <div style="background: white; padding: 15px 20px; border-radius: 15px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 10px rgba(0,0,0,0.03);">
                            <span style="font-weight: 700;"><?= htmlspecialchars($ps['nom_service']) ?></span>
                            <span class="modal-badge" style="margin:0;"><?= $ps['total'] ?> RDV</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ACTUALITÉS -->
<section class="section">
    <div class="container">
        <h2 class="section-title reveal">Dernières Nouvelles</h2>
        <div class="news-grid">
            <?php foreach($news as $item): ?>
            <div class="news-card reveal">
                <div class="news-image"><img src="<?= $item['image'] ?>"></div>
                <div class="news-content">
                    <span class="news-date"><i class="far fa-calendar-alt"></i> <?= $item['date'] ?></span>
                    <h3 class="news-title"><?= $item['title'] ?></h3>
                    <p class="news-excerpt"><?= $item['excerpt'] ?></p>
                    <a href="#" class="btn btn-outline btn-sm" style="margin-top:20px; color: var(--primary); border-color: var(--primary);">Lire plus</a>
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
            <p>Le futur de la municipalité, aujourd'hui.</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-envelope"></i> Contact</h4>
            <p>contact@innogov.tn<br>+216 71 000 000</p>
        </div>
        <div class="footer-section">
            <h4><i class="fas fa-share-alt"></i> Réseaux</h4>
            <div style="display: flex; gap: 15px; font-size: 1.5rem; margin-top: 10px;">
                <i class="fab fa-facebook"></i><i class="fab fa-twitter"></i><i class="fab fa-linkedin"></i>
            </div>
        </div>
    </div>
    <div class="footer-bottom"><p>&copy; 2026 InnoGov - Propulsé par l'innovation</p></div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    const ctx = document.getElementById('homeChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?= json_encode(array_column($parService, 'nom_service')) ?>,
                datasets: [{
                    data: <?= json_encode(array_column($parService, 'total')) ?>,
                    backgroundColor: ['#3a5a2a', '#c07b3d', '#4ade80', '#fbbf24'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } },
                cutout: '75%'
            }
        });
    }
});
</script>

<!-- CHATBOT -->
<div id="chatbot-container" class="chatbot-container">
    <div class="chatbot-header">
        <div class="chatbot-title"><i class="fas fa-robot"></i> InnoBot</div>
        <button id="chatbot-close" class="chatbot-close"><i class="fas fa-times"></i></button>
    </div>
    <div id="chatbot-messages" class="chatbot-messages"></div>
    <div id="chatbot-options" class="chatbot-options"></div>
    <div class="chatbot-input-area">
        <button id="chatbot-upload-btn"><i class="fas fa-paperclip"></i></button>
        <input type="file" id="chatbot-file-input" style="display: none;">
        <input type="text" id="chatbot-input" placeholder="Posez une question...">
        <button id="chatbot-send-btn"><i class="fas fa-paper-plane"></i></button>
    </div>
</div>
<button id="chatbot-toggle" class="chatbot-toggle"><i class="fas fa-comment-dots"></i></button>

</body>
</html>
