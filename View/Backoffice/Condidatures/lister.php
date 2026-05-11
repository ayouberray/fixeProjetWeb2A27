<?php
// $candidatures est disponible
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des candidatures - Admin</title>
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
        <h1>Liste des candidatures</h1>
        <table class="data-table">
            <thead>
                <tr><th>ID</th><th>Offre</th><th>Candidat</th><th>Email</th><th>Téléphone</th><th>CV</th><th>Statut</th><th>Action</th></tr>
            </thead>
            <tbody>
                <?php foreach ($candidatures as $c): ?>
                <tr id="candidature-<?= $c['id_cond'] ?>">
                    <td><?= $c['id_cond'] ?></td>
                    <td><?= htmlspecialchars($c['offre_titre'] ?? 'Offre inconnue') ?></td>
                    <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                    <td><?= htmlspecialchars($c['email']) ?></td>
                    <td><?= htmlspecialchars($c['num_tel']) ?></td>
                    <td>
                        <a href="index.php?controller=candidature&action=telecharger-cv&id=<?= $c['id_cond'] ?>" class="btn btn-outline btn-sm">
                            <i class="fas fa-download"></i> CV
                        </a>
                    </td>
                    <td class="statut">
                        <?php
                            $statutClass = '';
                            if ($c['statut'] == 'en_attend') $statutClass = 'badge-warning';
                            elseif ($c['statut'] == 'validee') $statutClass = 'badge-success';
                            else $statutClass = 'badge-danger';
                        ?>
                        <span class="badge <?= $statutClass ?>">
                            <?= $c['statut'] == 'en_attend' ? 'En attente' : ($c['statut'] == 'validee' ? 'Validée' : 'Rejetée') ?>
                        </span>
                    </span>
                    <td>
                        <?php if ($c['statut'] == 'en_attend'): ?>
                            <button onclick="traiter(<?= $c['id_cond'] ?>, 'accepter')" class="btn btn-secondary btn-sm">Accepter</button>
                            <button onclick="traiter(<?= $c['id_cond'] ?>, 'refuser')" class="btn btn-outline btn-sm">Refuser</button>
                        <?php else: ?>
                            Déjà traité
                        <?php endif; ?>
                    </span>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>

    <script>
        async function traiter(id, action) {
            const formData = new FormData();
            formData.append('id', id);
            formData.append('action', action);
            const response = await fetch('index.php?controller=candidature&action=traiter', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            if (result.success) {
                location.reload(); // Recharge la page pour voir le changement
            } else {
                alert('Erreur lors du traitement');
            }
        }
    </script>
</body>
</html>