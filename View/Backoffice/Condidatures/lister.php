<?php
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des candidatures - INNOC@V</title>
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
            <a href="index.php?controller=offre&action=admin-lister" class="nav-link">Offres</a>
            <a href="index.php?controller=candidature&action=admin-lister" class="nav-link active">Candidatures</a>
            <a href="index.php" class="nav-link">Accueil</a>
        </div>
        <div class="lang-toggle">
            <button class="lang-btn active" data-lang="fr">FR</button>
            <button class="lang-btn" data-lang="ar">AR</button>
            <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
        </div>
    </div>
</nav>

<main class="container" style="padding: 2rem 0;">
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-user-check"></i> Liste des candidatures</h2>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Offre</th>
                        <th>Candidat</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>CV</th>
                        <th>Statut</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($candidatures)): ?>
                        <tr><td colspan="8" style="text-align: center;">Aucune candidature trouvée.</td></tr>
                    <?php else: ?>
                        <?php foreach ($candidatures as $c): ?>
                        <tr id="candidature-<?= $c['id_cond'] ?>">
                            <td>#<?= $c['id_cond'] ?></td>
                            <td><?= htmlspecialchars($c['offre_titre'] ?? 'Offre inconnue') ?></td>
                            <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                            <td><?= htmlspecialchars($c['email']) ?></td>
                            <td><?= htmlspecialchars($c['num_tel']) ?></td>
                            <td>
                                <a href="index.php?controller=candidature&action=telecharger-cv&id=<?= $c['id_cond'] ?>" class="btn btn-outline btn-sm">
                                    <i class="fas fa-download"></i> CV
                                </a>
                            </td>
                            <td>
                                <?php
                                    $statutClass = '';
                                    if ($c['statut'] == 'en_attend') $statutClass = 'badge-warning';
                                    elseif ($c['statut'] == 'validee') $statutClass = 'badge-success';
                                    else $statutClass = 'badge-danger';
                                ?>
                                <span class="badge <?= $statutClass ?>">
                                    <?= $c['statut'] == 'en_attend' ? 'En attente' : ($c['statut'] == 'validee' ? 'Validée' : 'Rejetée') ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($c['statut'] == 'en_attend'): ?>
                                    <button onclick="traiter(<?= $c['id_cond'] ?>, 'accepter')" class="btn btn-primary btn-sm">
                                        <i class="fas fa-check"></i> Accepter
                                    </button>
                                    <button onclick="traiter(<?= $c['id_cond'] ?>, 'refuser')" class="btn btn-outline btn-sm">
                                        <i class="fas fa-times"></i> Refuser
                                    </button>
                                <?php else: ?>
                                    <span class="badge badge-info">Déjà traité</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; 2025 INNOC@V - Administration des candidatures</p>
    </div>
</footer>

<script>
    async function traiter(id, action) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('action', action);
        try {
            const response = await fetch('index.php?controller=candidature&action=traiter', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert('Erreur lors du traitement');
            }
        } catch (err) {
            alert('Erreur de connexion');
        }
    }
</script>

</body>
</html>