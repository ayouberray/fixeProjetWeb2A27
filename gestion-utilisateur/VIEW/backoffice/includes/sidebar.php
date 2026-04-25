<?php
// Sidebar réutilisable pour toutes les pages du backoffice
$current_page = $_GET['page'] ?? 'dashboard';
$isAdmin = ($_SESSION['user_role'] === 'admin');
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">inno<span>Gov</span></div>
        <div class="sidebar-subtitle">Espace <?= $_SESSION['user_role'] === 'admin' ? 'Administration' : 'Agent' ?></div>
    </div>
    <div class="sidebar-nav">
        <a href="backoffice.php?page=dashboard" class="sidebar-nav-item <?= $current_page == 'dashboard' ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i> Dashboard
        </a>
        
        <?php if($isAdmin): ?>
            <a href="backoffice.php?page=liste_utilisateurs" class="sidebar-nav-item <?= $current_page == 'liste_utilisateurs' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Utilisateurs
            </a>
        <?php endif; ?>
        
        <a href="backoffice.php?page=emploi" class="sidebar-nav-item <?= $current_page == 'emploi' ? 'active' : '' ?>">
            <i class="fas fa-briefcase"></i> Emploi
        </a>
        
        <a href="backoffice.php?page=concours" class="sidebar-nav-item <?= $current_page == 'concours' ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i> Concours
        </a>
        
        <a href="backoffice.php?page=rendez_vous" class="sidebar-nav-item <?= $current_page == 'rendez_vous' ? 'active' : '' ?>">
            <i class="fas fa-calendar-check"></i> Rendez-vous
        </a>
        
        <a href="backoffice.php?page=demandes" class="sidebar-nav-item <?= $current_page == 'demandes' ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i> Demandes
        </a>
        
        <a href="backoffice.php?page=reclamations" class="sidebar-nav-item <?= $current_page == 'reclamations' ? 'active' : '' ?>">
            <i class="fas fa-comment-dots"></i> Réclamations
        </a>
        
        <a href="../frontoffice/profil.php" class="sidebar-nav-item">
            <i class="fas fa-user"></i> Mon profil
        </a>
        
        <a href="../frontoffice/logout.php" class="sidebar-nav-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1);">
            <i class="fas fa-sign-out-alt"></i> Déconnexion
        </a>
    </div>
</div>

<style>
    .sidebar {
        width: 280px;
        background: linear-gradient(180deg, #0d3320 0%, #1a1a1a 100%);
        color: white;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        overflow-y: auto;
        transition: all 0.3s ease;
        z-index: 1000;
        box-shadow: 4px 0 20px rgba(0,0,0,0.1);
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

    .sidebar-logo span { color: white; background: none; -webkit-background-clip: unset; }
    .sidebar-subtitle { font-size: 11px; color: rgba(255,255,255,0.5); margin-top: 5px; }
    .sidebar-nav { padding: 20px 0; }
    
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

    .sidebar-nav-item i { width: 24px; font-size: 1.1rem; }
    .menu-toggle { display: none; position: fixed; top: 15px; left: 15px; z-index: 1001; background: #2E7D32; color: white; border: none; padding: 10px 15px; border-radius: 10px; cursor: pointer; }

    @media (max-width: 768px) {
        .sidebar { transform: translateX(-100%); }
        .sidebar.open { transform: translateX(0); }
        .menu-toggle { display: block; }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (!document.querySelector('.menu-toggle')) {
            const menuBtn = document.createElement('button');
            menuBtn.className = 'menu-toggle';
            menuBtn.innerHTML = '<i class="fas fa-bars"></i> Menu';
            menuBtn.onclick = function() { document.getElementById('sidebar').classList.toggle('open'); };
            document.body.appendChild(menuBtn);
        }
    });
</script>