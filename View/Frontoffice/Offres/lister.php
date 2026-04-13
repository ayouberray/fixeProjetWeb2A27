<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offres d'emploi - InnoGov</title>
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Vidéo d'arrière-plan -->
<div class="video-background">
    
    <div class="overlay"></div>
</div>

<!-- En-tête (identique) -->
<header class="main-header">
    <div class="container header-inner">
        <a href="index.php?controller=offre&action=lister" class="logo">
            <img src="/ProjettWeb/assets/css/logo.png" alt="InnoGov" style="height: 60px;">
        </a>
        <ul class="nav-links">
            <li><a href="index.php?controller=offre&action=lister">Offres</a></li>
            <li><a href="#">Services</a></li>
            <li><a href="#">Contact</a></li>
        </ul>
    </div>
</header>

<!-- Section Hero avec barre de recherche (style Hukoomi) -->
<section class="hero-search">
    <div class="container">
        <h1>Bienvenue sur InnoGov</h1>
        <p>Simplifions ensemble vos démarches administratives</p>
        <form class="search-form" action="index.php" method="GET">
            <input type="hidden" name="controller" value="offre">
            <input type="hidden" name="action" value="lister">
            <div class="search-wrapper">
                <i class="fas fa-search search-icon"></i>
                <input type="text" name="search" class="search-input" placeholder="Rechercher une offre, un service...">
                <button type="submit" class="search-button btn-primary">Rechercher</button>
            </div>
        </form>
    </div>
</section>

<!-- Contenu principal : liste des offres -->
<main class="container" style="padding-top: 2rem;">
    <h1>Offres d'emploi</h1>
    <div class="grid-2">
        <?php foreach ($offres as $offre): ?>
            <div class="card">
                <h3 class="card-title"><?= htmlspecialchars($offre['titre']) ?></h3>
                <p class="card-text"><?= nl2br(htmlspecialchars(substr($offre['description'], 0, 100))) ?>...</p>
                <a href="index.php?controller=offre&action=detail&id=<?= $offre['id_offre'] ?>" class="btn btn-primary">Voir plus</a>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<footer class="footer">
    <div class="container">
        <p>&copy; 2025 InnoGov - Transformation numérique</p>
    </div>
</footer>

</body>
</html>