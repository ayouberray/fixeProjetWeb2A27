<?php
// Vérifier si la session est déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_role']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}
$isAdmin = ($_SESSION['user_role'] === 'admin');
?>

<div class="top-bar">
    <h1><i class="fas fa-briefcase"></i> Gestion des offres d'emploi</h1>
    <div class="user-info">
        <span style="color: var(--gray-800); font-weight: 500;"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>📋 Liste des offres d'emploi</h3>
        <?php if($isAdmin): ?><a href="#" class="btn-add"><i class="fas fa-plus"></i> Ajouter une offre</a><?php endif; ?>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Ministère</th>
                    <th>Niveau</th>
                    <th>Date limite</th>
                    <th>Statut</th>
                    <?php if($isAdmin): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Développeur Full Stack</td>
                    <td>Ministère des Technologies</td>
                    <td>Senior</td>
                    <td>15/05/2026</td>
                    <td><span class="badge">Actif</span></td>
                    <?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?>
                </tr>
                <tr>
                    <td>Chef de Projet Digital</td>
                    <td>Ministère de l'Intérieur</td>
                    <td>Confirmé</td>
                    <td>20/05/2026</td>
                    <td><span class="badge">Actif</span></td>
                    <?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?>
                </tr>
                <tr>
                    <td>Data Analyst</td>
                    <td>Ministère des Finances</td>
                    <td>Junior</td>
                    <td>30/05/2026</td>
                    <td><span class="badge">Actif</span></td>
                    <?php if($isAdmin): ?><td><a href="#" class="btn-edit">Modifier</a><a href="#" class="btn-delete">Supprimer</a></td><?php endif; ?>
                </tr>
            </tbody>
        </table>
    </div>
</div>
