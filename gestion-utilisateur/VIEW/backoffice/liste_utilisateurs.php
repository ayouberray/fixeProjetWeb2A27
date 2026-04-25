<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>innoGov | Admin - Utilisateurs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: #f5f5f5;
        }
        .admin-container {
            display: flex;
        }
        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #0d3320 0%, #1a1a1a 100%);
            color: white;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            transition: all 0.3s ease;
            z-index: 100;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
        }
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-logo {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .sidebar-logo span {
            color: white;
            background: none;
            -webkit-background-clip: unset;
        }
        .sidebar-subtitle {
            font-size: 11px;
            color: rgba(255,255,255,0.5);
            margin-top: 5px;
        }
        .sidebar-nav {
            padding: 20px 0;
        }
        .sidebar-nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.3s;
            margin: 4px 12px;
            border-radius: 12px;
        }
        .sidebar-nav-item:hover {
            background: rgba(46, 125, 50, 0.3);
            color: white;
        }
        .sidebar-nav-item.active {
            background: linear-gradient(135deg, #2E7D32, #1b5e20);
            color: white;
            box-shadow: 0 4px 15px rgba(46,125,50,0.3);
        }
        .sidebar-nav-item i {
            width: 24px;
            font-size: 1.1rem;
        }
        .main-content {
            margin-left: 280px;
            flex: 1;
            padding: 20px;
        }
        .top-bar {
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #2E7D32;
        }
        .stat-value {
            font-size: 32px;
            font-weight: bold;
            color: #2E7D32;
        }
        .card {
            background: white;
            border-radius: 10px;
            padding: 20px;
        }
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }
        .search-box {
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .search-box input {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            font-size: 14px;
            font-family: 'Inter', sans-serif;
        }
        .search-box input:focus {
            outline: none;
            border-color: #2E7D32;
        }
        .search-box button {
            background: #2E7D32;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        .search-box button:hover {
            background: #1b5e20;
        }
        .clear-search {
            background: #666;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
        }
        .clear-search:hover {
            background: #555;
        }
        .search-info {
            background: #e8f5e9;
            padding: 8px 15px;
            border-radius: 8px;
            color: #2E7D32;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background: #f8f9fa;
        }
        .btn {
            padding: 5px 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
        }
        .btn-add {
            background: #2E7D32;
            color: white;
        }
        .btn-edit {
            background: #2196F3;
            color: white;
        }
        .btn-delete {
            background: #f44336;
            color: white;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
        }
        .badge-admin {
            background: #E3F2FD;
            color: #1976D2;
        }
        .badge-user {
            background: #E8F5E9;
            color: #2E7D32;
        }
        .badge-agent {
            background: #FFF3E0;
            color: #E65100;
        }
        .no-result {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); position: fixed; z-index: 1000; }
            .main-content { margin-left: 0; }
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
            .search-box input { width: 200px; }
            .card-header { flex-direction: column; align-items: stretch; }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <?php include 'includes/sidebar.php'; ?>
    
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
                        <input type="text" id="searchInput" placeholder="🔍 Rechercher par nom, email, CIN..." onkeyup="filterTable()">
                        <button onclick="filterTable()"><i class="fas fa-search"></i> Rechercher</button>
                        <button onclick="clearSearch()" class="clear-search"><i class="fas fa-times"></i> Effacer</button>
                    </div>
                    <a href="ajouter_utilisateur.php" class="btn btn-add">+ Ajouter</a>
                </div>
            </div>
            <div id="searchInfo" class="search-info" style="margin-bottom: 15px; display: none;"></div>
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
                <div id="noResult" class="no-result" style="display: none;">
                    <i class="fas fa-search" style="font-size: 48px; opacity: 0.5;"></i>
                    <p>Aucun utilisateur trouvé</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function filterTable() {
    const input = document.getElementById('searchInput');
    const filter = input.value.toLowerCase().trim();
    const table = document.getElementById('userTable');
    const rows = table.getElementsByTagName('tr');
    const searchInfo = document.getElementById('searchInfo');
    const noResult = document.getElementById('noResult');
    let visibleCount = 0;
    
    // Parcourir toutes les lignes du tableau (sauter l'en-tête)
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        let found = false;
        
        // Récupérer les cellules à rechercher
        const nomCell = row.querySelector('.search-nom');
        const prenomCell = row.querySelector('.search-prenom');
        const emailCell = row.querySelector('.search-email');
        const cinCell = row.querySelector('.search-cin');
        
        const nom = nomCell ? nomCell.textContent.toLowerCase() : '';
        const prenom = prenomCell ? prenomCell.textContent.toLowerCase() : '';
        const email = emailCell ? emailCell.textContent.toLowerCase() : '';
        const cin = cinCell ? cinCell.textContent.toLowerCase() : '';
        
        // Vérifier si le texte correspond
        if (filter === '') {
            found = true;
        } else {
            if (nom.includes(filter) || 
                prenom.includes(filter) || 
                email.includes(filter) || 
                cin.includes(filter)) {
                found = true;
            }
        }
        
        // Afficher ou masquer la ligne
        if (found) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    }
    
    // Afficher le message de recherche
    if (filter !== '') {
        searchInfo.style.display = 'block';
        searchInfo.innerHTML = `<i class="fas fa-search"></i> Résultats : ${visibleCount} utilisateur(s) trouvé(s) pour "${filter}"`;
        if (visibleCount === 0) {
            noResult.style.display = 'block';
        } else {
            noResult.style.display = 'none';
        }
    } else {
        searchInfo.style.display = 'none';
        noResult.style.display = 'none';
    }
}

function clearSearch() {
    document.getElementById('searchInput').value = '';
    filterTable();
}
</script>

</body>
</html>