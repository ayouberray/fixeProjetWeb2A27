<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Confirmation - InnoGov</title>
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
</head>
<body>
    <header class="main-header">
            <a href="index.php?controller=offre&action=lister" class="logo" style="display: flex; align-items: center; gap: 10px; text-decoration: none; font-weight: bold; font-size: 1.5rem; color: var(--primary);">
                <i class="fas fa-briefcase"></i> INNOC@V
            </a>
            <div class="lang-toggle">
                <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
            </div>
        </div>
    </header>

    <main class="container" style="padding-top: 4rem; text-align: center;">
        <div class="card" style="max-width: 500px; margin: 0 auto;">
            <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--secondary); margin-bottom: 1rem;"></i>
            <h1>Merci !</h1>
            <p>Votre candidature a bien été envoyée.</p>
            <a href="index.php?controller=offre&action=lister" class="btn btn-primary">Retour aux offres</a>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 InnoGov - Transformation numérique</p>
        </div>
    </footer>
</body>
</html>