<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}
$isAdmin = ($_SESSION['user_role'] === 'admin');
?>

<div class="main-content">
    <div class="top-bar">
        <h1><i class="fas fa-briefcase"></i> Gestion des offres d'emploi</h1>
        <div><?= htmlspecialchars($_SESSION['user_nom']) ?></div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>📋 Liste des offres d'emploi</h3>
            <?php if($isAdmin): ?><a href="#" class="btn-add"><i class="fas fa-plus"></i> Ajouter une offre</a><?php endif; ?>
        </div>
        <div style="overflow-x: auto;">
            <table><thead><tr><th>Titre</th><th>Ministère</th><th>Niveau</th><th>Date limite</th><th>Statut</th><?php if($isAdmin): ?><th>Actions</th><?php endif; ?></tr></thead>
            <tbody>
                <tr><td>Ingénieur Systèmes</td><td>Ministère Technologie</td><td>Master</td><td>30/04/2026</td><td><span class="badge">Ouvert</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?></tr>
                <tr><td>Technicien Informatique</td><td>Ministère Education</td><td>Bac+2</td><td>15/05/2026</td><td><span class="badge">Ouvert</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?></tr>
                <tr><td>Administrateur</td><td>Fonction Publique</td><td>Bac</td><td>01/06/2026</td><td><span class="badge">Fermé</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?></tr>
            </tbody></table>
        </div>
    </div>
</div>