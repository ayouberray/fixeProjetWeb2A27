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
        <h1><i class="fas fa-graduation-cap"></i> Gestion des concours</h1>
        <div><?= htmlspecialchars($_SESSION['user_nom']) ?></div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>📢 Liste des concours</h3>
            <?php if($isAdmin): ?><a href="#" class="btn-add"><i class="fas fa-plus"></i> Ajouter un concours</a><?php endif; ?>
        </div>
        <div style="overflow-x: auto;">
            <table><thead><tr><th>Concours</th><th>Organisme</th><th>Date ouverture</th><th>Date clôture</th><th>Postes</th><th>Inscrits</th><th>Statut</th><?php if($isAdmin): ?><th>Actions</th><?php endif; ?></tr></thead>
            <tbody>
                <tr><td>Concours Administratif</td><td>Fonction Publique</td><td>01/03/2026</td><td>30/04/2026</td><td>50</td><td>234</td><td><span class="badge">Ouvert</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?></tr>
                <tr><td>Concours Technicien</td><td>Ministère Education</td><td>01/04/2026</td><td>15/05/2026</td><td>30</td><td>156</td><td><span class="badge">Ouvert</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?></tr>
                <tr><td>Concours Ingénieur</td><td>Ministère Industrie</td><td>15/03/2026</td><td>01/06/2026</td><td>20</td><td>89</td><td><span class="badge">Ouvert</span></td><?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?></tr>
            </tbody></table>
        </div>
    </div>
</div>