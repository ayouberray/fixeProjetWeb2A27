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
    <h1><i class="fas fa-comment-dots"></i> Gestion des réclamations</h1>
    <div class="user-info">
        <span style="color: var(--gray-800); font-weight: 500;"><?= htmlspecialchars($_SESSION['user_nom']) ?></span>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>⚠️ Liste des réclamations</h3>
    </div>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Citoyen</th>
                    <th>Sujet</th>
                    <th>Priorité</th>
                    <th>Date</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#REC-001</td>
                    <td>Leila Hamdi</td>
                    <td>Délai trop long</td>
                    <td class="priority-high">⚠️ Haute</td>
                    <td>05/04/2026</td>
                    <td><span class="badge-cours">En cours</span></td>
                    <td><a href="#" class="btn-edit">Traiter</a><a href="#" class="btn-view">Voir</a></td>
                </tr>
                <tr>
                    <td>#REC-002</td>
                    <td>Karim Sassi</td>
                    <td>Erreur dans document</td>
                    <td class="priority-medium">🟠 Moyenne</td>
                    <td>08/04/2026</td>
                    <td><span class="badge-resolu">Résolu</span></td>
                    <td><a href="#" class="btn-view">Voir</a></td>
                </tr>
                <tr>
                    <td>#REC-003</td>
                    <td>Nadia Riahi</td>
                    <td>Problème technique</td>
                    <td class="priority-high">⚠️ Haute</td>
                    <td>10/04/2026</td>
                    <td><span class="badge-nouveau">Nouveau</span></td>
                    <td><a href="#" class="btn-edit">Traiter</a><a href="#" class="btn-view">Voir</a></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
