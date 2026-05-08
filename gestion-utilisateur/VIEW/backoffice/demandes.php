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
    <h1><i class="fas fa-file-alt"></i> Gestion des demandes</h1>
    <div class="user-info">
        <span style="color: var(--gray-800); font-weight: 500;"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>📋 Liste des demandes</h3>
        <?php if($isAdmin): ?><a href="#" class="btn-add"><i class="fas fa-plus"></i> Nouvelle demande</a><?php endif; ?>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Citoyen</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#DEM-001</td>
                    <td>Ahmed Ben Ali</td>
                    <td>Passeport</td>
                    <td>10/04/2026</td>
                    <td><span class="badge-cours">En cours</span></td>
                    <td><a href="#" class="btn-edit">Traiter</a><a href="#" class="btn-view">Voir</a></td>
                </tr>
                <tr>
                    <td>#DEM-002</td>
                    <td>Sarra Mansouri</td>
                    <td>CIN</td>
                    <td>12/04/2026</td>
                    <td><span class="badge-approuve">Approuvé</span></td>
                    <td><a href="#" class="btn-view">Voir</a></td>
                </tr>
                <tr>
                    <td>#DEM-003</td>
                    <td>Mohamed Karray</td>
                    <td>Extrait naissance</td>
                    <td>14/04/2026</td>
                    <td><span class="badge-attente">En attente</span></td>
                    <td><a href="#" class="btn-edit">Traiter</a><a href="#" class="btn-view">Voir</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
