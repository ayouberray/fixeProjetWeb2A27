<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";

$rdvController = new RendezVousController();
$citoyen_nom = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$filter_statut = $_GET['statut'] ?? '';

$list = $rdvController->getRendezVousByCitoyen($citoyen_nom, $search, $sort, $filter_statut);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes rendez-vous - InnoGov</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hero { display: none; }
        
        .futuristic-container { max-width: 1000px; margin: 60px auto; position: relative; z-index: 1; }
        .page-header { text-align: left; margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center; }
        .page-header h2 { font-size: 1.8rem; color: #1e293b; font-weight: 700; margin: 0; }
        
        .filter-bar {
            background: #ffffff;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 30px;
            border: 1px solid #e2e8f0;
            display: flex; gap: 15px; flex-wrap: wrap; align-items: center;
        }

        .futuristic-input {
            padding: 10px 15px; font-size: 14px; background: #ffffff; border: 1px solid #cbd5e1;
            border-radius: 8px; color: #1e293b; transition: all 0.2s ease; outline: none;
        }
        .futuristic-input:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(8, 84, 64, 0.1); }

        .btn-cyber-sm {
            background: var(--primary); color: white; border: none;
            padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s ease;
        }
        .btn-cyber-sm:hover { background: var(--primary-dark); }

        .rdv-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; }

        .rdv-card {
            background: #ffffff; border-radius: 12px;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 20px; position: relative; overflow: hidden; transition: all 0.2s ease;
        }
        .rdv-card:hover { transform: translateY(-3px); box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); }
        .rdv-card::before {
            content: ''; position: absolute; top: 0; left: 0; width: 100%; height: 4px;
            background: var(--primary);
        }

        .rdv-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px; }
        .rdv-service { font-size: 1.1rem; font-weight: 700; color: #1e293b; margin-bottom: 5px; display: block; }
        .rdv-date { font-size: 0.85rem; color: #64748b; display: flex; align-items: center; gap: 8px; margin-bottom: 10px; }
        .rdv-agent { font-size: 0.85rem; color: #475569; margin-bottom: 15px; padding: 6px 10px; background: #f8fafc; border-radius: 6px; border: 1px solid #e2e8f0; }
        
        .rdv-actions { display: flex; gap: 10px; margin-top: 15px; border-top: 1px solid #e2e8f0; padding-top: 15px; }
        .btn-action { flex: 1; text-align: center; padding: 8px; border-radius: 6px; font-size: 13px; font-weight: 600; text-decoration: none; transition: 0.2s; border: 1px solid transparent; }
        .btn-edit { background: #ffffff; color: var(--primary); border-color: var(--primary); }
        .btn-edit:hover { background: #f8fafc; }
        .btn-delete { background: #ffffff; color: #dc3545; border-color: #dc3545; }
        .btn-delete:hover { background: #f8fafc; }
        .btn-disabled { opacity: 0.5; pointer-events: none; }
    </style>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<nav class="navbar">
    <div class="navbar-container">
        <a href="/projet/index.php" style="text-decoration: none;">
            <div class="logo">
                <img src="/projet/assets/images/innogov-logo.png" alt="InnoGov" class="logo-img">
                <div class="logo-text">
                    <p class="logo-subtitle">Municipalité Tunisienne</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/index.php" class="nav-link">Accueil</a>
            <a href="/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link active">Mes RDV</a>
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary btn-sm">Prendre RDV</a>
        </div>
    </div>
</nav>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-slideshow">
        <img src="/projet/assets/images/tunisia1.jpg" class="slide active" alt="Tunisie">
        <img src="/projet/assets/images/tunisia2.jpg" class="slide" alt="Tunisie">
        <img src="/projet/assets/images/tunisia3.jpg" class="slide" alt="Tunisie">
        <img src="/projet/assets/images/tunisia4.jpg" class="slide" alt="Tunisie">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne</p>
        <div class="hero-buttons">
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
            <a href="#services" class="btn btn-outline">En savoir plus</a>
        </div>
    </div>
</section>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-history" style="color: var(--primary);"></i> Mes RDV</h2>
        <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn-cyber-sm" style="text-decoration: none;"><i class="fas fa-plus"></i> Nouvelle Réservation</a>
    </div>
        
    <form class="filter-bar reveal" method="GET" action="">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="futuristic-input" placeholder="Rechercher..." style="flex: 1; min-width: 200px;">
        
        <select name="sort" class="futuristic-input">
            <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Plus récent</option>
            <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Plus ancien</option>
            <option value="service_asc" <?= $sort == 'service_asc' ? 'selected' : '' ?>>Service (A-Z)</option>
        </select>

        <select name="statut" class="futuristic-input">
            <option value="">Tous les statuts</option>
            <option value="en_attente" <?= $filter_statut == 'en_attente' ? 'selected' : '' ?>>En attente</option>
            <option value="termine" <?= $filter_statut == 'termine' ? 'selected' : '' ?>>Terminé</option>
            <option value="annule" <?= $filter_statut == 'annule' ? 'selected' : '' ?>>Annulé</option>
        </select>

        <button type="submit" class="btn-cyber-sm"><i class="fas fa-search"></i></button>
        <a href="?" class="btn-cyber-sm" style="background: #e2e8f0; color: #475569; text-decoration: none;"><i class="fas fa-times"></i></a>
    </form>
        
    <div class="rdv-grid">
        <?php if(!empty($list)): ?>
            <?php foreach($list as $rdv): ?>
                <?php 
                $date_rdv = strtotime($rdv['date_heure']);
                $date_now = time();
                $estPasse = ($date_rdv < $date_now);
                $disabled = ($estPasse || $rdv['statut'] == 'termine') ? 'btn-disabled' : '';
                ?>
                <div class="rdv-card reveal">
                    <div class="rdv-header">
                        <div class="rdv-service"><?= htmlspecialchars($rdv['service_nom']) ?></div>
                        <span class="badge badge-<?= $rdv['statut'] ?>"><?= $rdv['statut'] ?></span>
                    </div>
                    <div class="rdv-date">
                        <i class="fas fa-clock" style="color: #22c55e;"></i> <?= date('d/m/Y - H:i', strtotime($rdv['date_heure'])) ?>
                    </div>
                    <div class="rdv-agent">
                        <i class="fas fa-user-tie" style="color: #64748b;"></i> 
                        <?= $rdv['agent_nom'] ? htmlspecialchars($rdv['agent_nom']) : 'Agent non assigné' ?>
                    </div>
                    <?php if(!empty($rdv['motif'])): ?>
                        <p style="font-size: 13px; color: #64748b; margin-bottom: 0; font-style: italic;">"<?= htmlspecialchars($rdv['motif']) ?>"</p>
                    <?php endif; ?>
                    
                    <div class="rdv-actions">
                        <a href="/projet/VIEW/frontoffice/citoyen-modifier-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn-action btn-edit <?= $disabled ?>"><i class="fas fa-pen"></i> Modifier</a>
                        <a href="/projet/VIEW/frontoffice/citoyen-annuler-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn-action btn-delete" onclick="return confirm('⚠️ Annuler définitivement ce rendez-vous ?')"><i class="fas fa-trash"></i> Annuler</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px; background: rgba(255,255,255,0.8); border-radius: 20px;">
                <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: #64748b; font-size: 1.2rem;">Aucun rendez-vous trouvé.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>InnoGov</h4><p>Plateforme de services municipaux</p></div>
        <div class="footer-section"><h4>Contact</h4><p>Tel: +216 70 000 000</p></div>
        <div class="footer-section"><h4>Horaires</h4><p>Lun-Ven: 8h30 - 15h30</p></div>
    </div>
    <div class="footer-bottom"><p>&copy; 2024 InnoGov - Tous droits réservés</p></div>
</footer>

<script>
window.addEventListener('DOMContentLoaded', () => {
    const searchParam = new URLSearchParams(window.location.search).get('search');
    // Vérifier si la recherche est uniquement numérique (donc une recherche d'ID)
    if (searchParam && !isNaN(searchParam) && searchParam.trim() !== "") {
        const trs = document.querySelectorAll('tbody tr');
        // Si le tableau contient une seule ligne indiquant "Aucun rendez-vous trouvé"
        if (trs.length === 1 && trs[0].textContent.includes("Aucun rendez-vous")) {
            alert("L'ID exact " + searchParam + " n'existe pas.");
        }
    }
});
</script>
</body>
</html>