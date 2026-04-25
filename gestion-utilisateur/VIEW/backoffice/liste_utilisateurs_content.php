<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est admin
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../frontoffice/login.php');
    exit();
}

require_once '../../MODEL/Utilisateur.php';

$utilisateur = new Utilisateur();

// Récupérer les données
$users = $utilisateur->getAll();
$totalUsers = $utilisateur->countAll();
$totalCitoyens = $utilisateur->countByRole('user');
$totalAdmins = $utilisateur->countByRole('admin');
$totalAgents = $utilisateur->countByRole('agent');
?>

<div class="main-content">
    <div class="top-bar">
        <h1>Gestion des utilisateurs</h1>
        <div>Admin: <?= htmlspecialchars($_SESSION['user_nom'] ?? 'Admin') ?></div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div>Total utilisateurs</div>
            <div class="stat-value"><?= $totalUsers ?></div>
        </div>
        <div class="stat-card">
            <div>Citoyens</div>
            <div class="stat-value"><?= $totalCitoyens ?></div>
        </div>
        <div class="stat-card">
            <div>Agents</div>
            <div class="stat-value"><?= $totalAgents ?></div>
        </div>
        <div class="stat-card">
            <div>Administrateurs</div>
            <div class="stat-value"><?= $totalAdmins ?></div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3>📋 Liste des utilisateurs</h3>
            <div style="display: flex; gap: 10px;">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="🔍 Rechercher..." onkeyup="filterTable()">
                    <button onclick="filterTable()"><i class="fas fa-search"></i> Rechercher</button>
                </div>
                <a href="ajouter_utilisateur.php" class="btn btn-add">+ Ajouter</a>
            </div>
        </div>
        <div style="overflow-x: auto;">
            <table id="userTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Email</th>
                        <th>CIN</th>
                        <th>Téléphone</th>
                        <th>Type</th>
                        <th>Rôle</th>
                        <th>Date inscription</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php foreach($users as $user): ?>
                    <tr>
                        <td><?= $user['id'] ?></td>
                        <td class="search-nom"><?= htmlspecialchars($user['nom']) ?></td>
                        <td class="search-prenom"><?= htmlspecialchars($user['prenom']) ?></td>
                        <td class="search-email"><?= htmlspecialchars($user['email']) ?></td>
                        <td class="search-cin"><?= $user['cin'] ?></td>
                        <td><?= $user['telephone'] ?></td>
                        <td>
                            <?php 
                            if($user['type_compte'] == 'citoyen') echo 'Citoyen';
                            elseif($user['type_compte'] == 'professionnel') echo 'Professionnel';
                            else echo 'Agent public';
                            ?>
                        </td>
                        <td>
                            <?php
                            if($user['role'] == 'admin') {
                                echo '<span class="badge badge-admin">Admin</span>';
                            } elseif($user['role'] == 'agent') {
                                echo '<span class="badge badge-agent">Agent</span>';
                            } else {
                                echo '<span class="badge badge-user">User</span>';
                            }
                            ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                        <td>
                            <a href="modifier_utilisateur.php?id=<?= $user['id'] ?>" class="btn btn-edit">Modifier</a>
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <a href="../../CONTROLLER/UtilisateurController.php?action=delete&id=<?= $user['id'] ?>" 
                                   class="btn btn-delete" 
                                   onclick="return confirm('Supprimer cet utilisateur ?')">Supprimer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase().trim();
    const table = document.getElementById('userTable');
    const rows = table.getElementsByTagName('tr');
    let visibleCount = 0;
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        let found = false;
        
        const nomCell = row.querySelector('.search-nom');
        const prenomCell = row.querySelector('.search-prenom');
        const emailCell = row.querySelector('.search-email');
        const cinCell = row.querySelector('.search-cin');
        
        const nom = nomCell ? nomCell.textContent.toLowerCase() : '';
        const prenom = prenomCell ? prenomCell.textContent.toLowerCase() : '';
        const email = emailCell ? emailCell.textContent.toLowerCase() : '';
        const cin = cinCell ? cinCell.textContent.toLowerCase() : '';
        
        if (nom.includes(filter) || prenom.includes(filter) || email.includes(filter) || cin.includes(filter)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
}
</script>