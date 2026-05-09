<?php
// Ce fichier est appelé par OffreController::adminListerOffres()
// $offres est disponible
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des offres - Admin</title>
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
                <li><a href="index.php?controller=offre&action=admin-lister">Offres</a></li>
                <li><a href="index.php?controller=candidature&action=admin-lister">Candidatures</a></li>
            </ul>
        </div>
    </header>

    <main class="container" style="padding-top: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1>Liste des offres</h1>
            <a href="index.php?controller=offre&action=ajouter" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une offre
            </a>
        </div>

        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Titre</th><th>Entité</th><th>Date limite</th><th>Postes</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
                <?php foreach ($offres as $offre): ?>
                <tr>
                    <td>#<?= $offre['id_offre'] ?></td>
                    <td><?= htmlspecialchars($offre['titre']) ?></td>
                    <td><?= htmlspecialchars($offre['entite']) ?></td>
                    <td><?= $offre['date_limite'] ?></td>
                    <td><?= $offre['nombre_postes'] ?></td>
                    <td>
                        <span class="badge badge-<?= $offre['statut'] == 'Ouvert' ? 'success' : 'danger' ?>">
                            <?= $offre['statut'] ?>
                        </span>
                    </span>
                    <td>
                        <a href="index.php?controller=offre&action=modifier&id=<?= $offre['id_offre'] ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="index.php?controller=offre&action=supprimer&id=<?= $offre['id_offre'] ?>" class="btn btn-outline btn-sm" onclick="return confirm('Supprimer cette offre ?')">
                            <i class="fas fa-trash"></i> Supprimer
                        </a>
                    </span>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 InnoGov - Administration</p>
        </div>
    </footer>
</body>
</html>