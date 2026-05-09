<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($offre['titre']) ?> - InnoGov</title>
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header class="main-header">
        <div class="container header-inner">
            <a href="index.php?controller=offre&action=lister" class="logo">
    <img src="/ProjettWeb/assets/css/logo.png" alt="Logo InnoGov" style="height: 40px;">
</a>
            <ul class="nav-links">
                <li><a href="index.php?controller=offre&action=lister">Offres</a></li>
            </ul>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <div class="card" style="margin-bottom: 2rem;">
            <h1 class="card-title"><?= htmlspecialchars($offre['titre']) ?></h1>
            <p><strong>Entité :</strong> <?= htmlspecialchars($offre['entite']) ?></p>
            <p><strong>Description :</strong></p>
            <p><?= nl2br(htmlspecialchars($offre['description'])) ?></p>
            <p><strong>Nombre de postes :</strong> <?= $offre['nombre_postes'] ?></p>
            <p><strong>Date limite :</strong> <?= $offre['date_limite'] ?></p>
        </div>

        <div class="card">
            <h2>Postuler</h2>
            <form action="index.php?controller=candidature&action=postuler" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="offre_id" value="<?= $offre['id_offre'] ?>">
                <div class="form-group">
                    <label class="form-label">Nom</label>
                    <input type="text" name="nom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Prénom</label>
                    <input type="text" name="prenom" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Téléphone</label>
                    <input type="tel" name="num_tel" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">CV (PDF/DOCX)</label>
                    <input type="file" name="cv" class="form-control" accept=".pdf,.docx" required>
                </div>
                <button type="submit" class="btn btn-primary">Envoyer ma candidature</button>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 InnoGov - Transformation numérique</p>
        </div>
    </footer>
</body>
</html>