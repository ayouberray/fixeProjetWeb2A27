<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'utilisateur est connecté (admin ou agent)
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$user_role = $_SESSION['user_role'];
$isAdmin = ($user_role === 'admin');
$isAgent = ($user_role === 'agent');

require_once '../../MODEL/Utilisateur.php';
$utilisateur = new Utilisateur();
$currentUser = $utilisateur->getById($_SESSION['user_id']);

// Récupérer les données
$users = $utilisateur->getAll();
$totalUsers = $utilisateur->countAll();
$totalCitoyens = $utilisateur->countByRole('user');
$totalAdmins = $utilisateur->countByRole('admin');
$totalAgents = $utilisateur->countByRole('agent');
?>

<div class="main-content">
    <div class="top-bar">
        <div class="page-title">
            <h1>Tableau de bord</h1>
        </div>
        <div class="admin-info">
            <span class="admin-badge"><?= ucfirst($user_role) ?></span>
            <span><?= htmlspecialchars($currentUser['prenom'] . ' ' . $currentUser['nom']) ?></span>
            <div class="admin-avatar"><?= strtoupper(substr($currentUser['prenom'], 0, 1)) ?></div>
        </div>
    </div>

    <!-- STATISTIQUES -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Total Utilisateurs</span>
                <div class="stat-icon"><i class="fas fa-users"></i></div>
            </div>
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <span>+12% ce mois</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Citoyens</span>
                <div class="stat-icon"><i class="fas fa-user"></i></div>
            </div>
            <div class="stat-value"><?= $totalCitoyens ?></div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <span>+8% ce mois</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Agents</span>
                <div class="stat-icon"><i class="fas fa-user-tie"></i></div>
            </div>
            <div class="stat-value"><?= $totalAgents ?></div>
            <div class="stat-change positive">
                <i class="fas fa-arrow-up"></i> <span>+5% ce mois</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <span class="stat-title">Administrateurs</span>
                <div class="stat-icon"><i class="fas fa-user-shield"></i></div>
            </div>
            <div class="stat-value"><?= $totalAdmins ?></div>
            <div class="stat-change neutral">
                <i class="fas fa-minus"></i> <span>Stable</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3>Activités récentes</h3>
        </div>
        <p>Tableau de bord avec statistiques des utilisateurs.</p>
    </div>
</div>