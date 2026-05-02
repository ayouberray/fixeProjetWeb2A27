<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";

$rdvController = new RendezVousController();

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$filter_statut = $_GET['statut'] ?? '';

$list = $rdvController->getAllRendezVous($search, $sort, $filter_statut);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Liste des rendez-vous</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/projet/assets/css/style.css">
    <script src="/projet/assets/js/script.js" defer></script>
    <style>
        body { font-family: 'Inter', sans-serif; background: #f8fafc; }
        .hero { display: none; }
        
        .futuristic-container { max-width: 1200px; margin: 40px auto; position: relative; z-index: 1; padding: 0 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
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
            padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; transition: all 0.2s ease; text-decoration: none;
        }
        .btn-cyber-sm:hover { background: var(--primary-dark); }

        /* Accordion Styles */
        .accordion-item {
            background: #ffffff; border-radius: 12px;
            border: 1px solid #e2e8f0; margin-bottom: 15px; overflow: hidden;
            box-shadow: 0 2px 4px -1px rgba(0, 0, 0, 0.05); transition: all 0.2s ease;
        }
        
        .accordion-header {
            padding: 20px 25px; cursor: pointer; display: flex; justify-content: space-between; align-items: center;
            background: #ffffff; transition: background 0.2s ease;
        }
        .accordion-header:hover { background: #f8fafc; }
        .accordion-header h3 { margin: 0; font-size: 1.2rem; color: #1e293b; display: flex; align-items: center; gap: 10px; font-weight: 600; }
        .citoyen-badge { background: #f1f5f9; color: #475569; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; border: 1px solid #e2e8f0; }
        .accordion-toggle { color: #64748b; transition: transform 0.3s ease; }
        
        .accordion-content {
            padding: 0 25px; max-height: 0; overflow: hidden; transition: max-height 0.4s ease, padding 0.4s ease;
            background: #ffffff; border-top: 1px solid transparent;
        }
        .accordion-item.active .accordion-content { max-height: 2000px; padding: 25px; border-top-color: #e2e8f0; }
        .accordion-item.active .accordion-toggle { transform: rotate(180deg); }

        /* Table inside accordion */
        .cyber-table { width: 100%; border-collapse: collapse; }
        .cyber-table th { background: var(--primary); color: white; font-weight: 600; text-transform: uppercase; font-size: 13px; padding: 12px 15px; text-align: left; }
        .cyber-table td { padding: 15px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .cyber-table tr:last-child td { border-bottom: none; }
        .cyber-table tr:hover td { background: #f8fafc; }

        .btn-action { padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; text-decoration: none; transition: 0.2s; display: inline-block; margin-right: 5px; border: 1px solid transparent; }
        .btn-edit { background: #ffffff; color: var(--primary); border-color: var(--primary); }
        .btn-edit:hover { background: #f8fafc; }
        .btn-assign { background: #ffffff; color: #0d6efd; border-color: #0d6efd; }
        .btn-assign:hover { background: #f8fafc; }
        .btn-delete { background: #ffffff; color: #dc3545; border-color: #dc3545; }
        .btn-delete:hover { background: #f8fafc; }
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
                    <p class="logo-subtitle">Administration</p>
                </div>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link active">Rendez-vous</a>
            <a href="/projet/VIEW/backoffice/admin-services.php" class="nav-link">Services</a>
            <a href="/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
            <a href="/projet/index.php" class="nav-link">Espace citoyen</a>
        </div>
    </div>
</nav>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-users-cog" style="color: var(--primary);"></i> Liste des Rendez-vous</h2>
        <a href="/projet/VIEW/backoffice/admin-ajouter-rdv.php" class="btn-cyber-sm" style="display: inline-block;"><i class="fas fa-plus"></i> Nouveau RDV Admin</a>
    </div>
        
    <form class="filter-bar reveal" method="GET" action="">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="futuristic-input" placeholder="Rechercher Citoyen, ID, Service..." style="flex: 1; min-width: 250px;">
        
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

        <button type="submit" class="btn-cyber-sm"><i class="fas fa-search"></i> Filtrer</button>
        <a href="?" class="btn-cyber-sm" style="background: #e2e8f0; color: #475569;"><i class="fas fa-times"></i></a>
    </form>
        
    <div class="accordion-container">
        <?php 
        // Logique de regroupement par citoyen
        $groupedList = [];
        foreach($list as $rdv) {
            $citoyen = $rdv['citoyen_nom'];
            if (!isset($groupedList[$citoyen])) {
                $groupedList[$citoyen] = [];
            }
            $groupedList[$citoyen][] = $rdv;
        }
        ?>

        <?php if(!empty($groupedList)): ?>
            <?php foreach($groupedList as $citoyen => $rdvs): ?>
                <div class="accordion-item reveal">
                    <div class="accordion-header" onclick="this.parentElement.classList.toggle('active')">
                        <h3>
                            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($citoyen) ?> 
                            <span class="citoyen-badge"><?= count($rdvs) ?> RDV</span>
                        </h3>
                        <i class="fas fa-chevron-down accordion-toggle"></i>
                    </div>
                    <div class="accordion-content">
                        <table class="cyber-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Service</th>
                                    <th>Date/Heure</th>
                                    <th>Statut</th>
                                    <th>Agent Assigné</th>
                                    <th>Actions Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($rdvs as $rdv): ?>
                                <tr>
                                    <td><strong>#<?= $rdv['id_rdv'] ?></strong></td>
                                    <td><?= htmlspecialchars($rdv['service_nom']) ?></td>
                                    <td><?= date('d/m/Y H:i', strtotime($rdv['date_heure'])) ?></td>
                                    <td><span class="badge badge-<?= $rdv['statut'] ?>"><?= $rdv['statut'] ?></span></td>
                                    <td>
                                        <?php if($rdv['agent_nom']): ?>
                                            <i class="fas fa-user-check" style="color: #22c55e;"></i> <?= htmlspecialchars($rdv['agent_nom']) ?>
                                        <?php else: ?>
                                            <span style="color: #ef4444; font-size: 12px; font-weight: 600;"><i class="fas fa-exclamation-circle"></i> Non affecté</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="/projet/VIEW/backoffice/admin-modifier-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn-action btn-edit" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="/projet/VIEW/backoffice/admin-affecter-agent.php?id=<?= $rdv['id_rdv'] ?>" class="btn-action btn-assign" title="Affecter Agent">
                                            <i class="fas fa-user-plus"></i>
                                        </a>
                                        <a href="/projet/VIEW/backoffice/admin-supprimer-rdv.php?id=<?= $rdv['id_rdv'] ?>" class="btn-action btn-delete" onclick="return confirm('Supprimer ce RDV ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="text-align: center; padding: 60px; background: rgba(255,255,255,0.8); border-radius: 20px; border: 1px solid rgba(34,197,94,0.2);">
                <i class="fas fa-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                <p style="color: #64748b; font-size: 1.2rem;">Aucun citoyen trouvé avec ces critères.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<footer class="footer">
    <div class="footer-container">
        <div class="footer-section"><h4>InnoGov Admin</h4><p>Plateforme de gestion municipale</p></div>
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