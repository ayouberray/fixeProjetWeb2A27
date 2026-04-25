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
        <h1><i class="fas fa-calendar-check"></i> Gestion des rendez-vous</h1>
        <div><?= htmlspecialchars($_SESSION['user_nom']) ?></div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>📅 Liste des rendez-vous</h3>
            <?php if($isAdmin): ?><a href="#" class="btn-add"><i class="fas fa-plus"></i> Ajouter un rendez-vous</a><?php endif; ?>
        </div>
        <div style="overflow-x: auto;">
            <table><thead><tr><th>Citoyen</th><th>Service</th><th>Date</th><th>Heure</th><th>Statut</th><?php if($isAdmin): ?><th>Actions</th><?php endif; ?></tr></thead>
            <tbody>
                <tr><td>Sami Kammoun</td><td>Carte d'identité</td><td>15/04/2026</td><td>10:00</td><td><span class="badge-confirme">Confirmé</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Annuler</a></td><?php endif; ?></tr>
                <tr><td>Rania Gharbi</td><td>Passeport</td><td>16/04/2026</td><td>14:30</td><td><span class="badge-attente">En attente</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Annuler</a></td><?php endif; ?></tr>
                <tr><td>Youssef Mejri</td><td>Permis de conduire</td><td>17/04/2026</td><td>09:00</td><td><span class="badge-confirme">Confirmé</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Annuler</a></td><?php endif; ?></tr>
            </tbody></table>
        </div>
    </div>
</div>