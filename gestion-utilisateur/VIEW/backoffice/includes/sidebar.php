<?php
// Fichier: VIEW/backoffice/includes/sidebar.php
$currentPage = $_GET['page'] ?? 'dashboard';
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">inno<span>Gov</span></div>
        <div class="sidebar-subtitle">Plateforme administrative</div>
    </div>
    
    <div class="sidebar-nav">
        <a href="backoffice.php?page=dashboard" class="sidebar-nav-item <?= $currentPage == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        
        <?php if(($_SESSION['user_role'] ?? '') === 'admin'): ?>
        <a href="backoffice.php?page=liste_utilisateurs" class="sidebar-nav-item <?= $currentPage == 'liste_utilisateurs' ? 'active' : '' ?>">
            <i class="fas fa-users"></i> Utilisateurs
        </a>
        <?php endif; ?>
        
        <a href="backoffice.php?page=demandes" class="sidebar-nav-item <?= $currentPage == 'demandes' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Demandes
        </a>
        
        <a href="backoffice.php?page=reclamations" class="sidebar-nav-item <?= $currentPage == 'reclamations' ? 'active' : '' ?>">
            <i class="fas fa-exclamation-circle"></i> Réclamations
        </a>
        
        <a href="backoffice.php?page=rendez_vous" class="sidebar-nav-item <?= $currentPage == 'rendez_vous' ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Rendez-vous
        </a>
        
        <a href="backoffice.php?page=concours" class="sidebar-nav-item <?= $currentPage == 'concours' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i> Concours
        </a>
        
        <a href="backoffice.php?page=emploi" class="sidebar-nav-item <?= $currentPage == 'emploi' ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i> Emplois
        </a>
        
        <div style="border-top: 1px solid var(--sidebar-border); margin: 15px 0;"></div>
        
        <a href="../frontoffice/logout.php" class="sidebar-nav-item" style="color: #ef4444;">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</div>