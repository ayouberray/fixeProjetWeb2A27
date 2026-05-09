<?php
// $offres est disponible depuis OffreController::adminListerOffres()
$showCandidaturesFor = isset($_GET['show_candidatures']) ? (int)$_GET['show_candidatures'] : null;
$candidatures = [];
$offreTitre = '';
if ($showCandidaturesFor) {
    $candidatureModel = new Candidature();
    $candidatures = $candidatureModel->getByOffre($showCandidaturesFor);
    // Récupérer le titr
    $offreModel = new Offre();
    $offre = $offreModel->getById($showCandidaturesFor);
    $offreTitre = $offre ? htmlspecialchars($offre['titre']) : 'Offre inconnue';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des offres - INNOC@V</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/ProjettWeb/assets/css/style.css?v=<?= time() ?>">
    <script src="/ProjettWeb/assets/js/script.js?v=<?= time() ?>" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>
<nav class="navbar">
    <div class="navbar-container">
        <a href="index.php" class="logo">
            <img src="/ProjettWeb/assets/images/logo.png" alt="INNOGOV" style="height: 60px; object-fit: contain;">
        </a>
        <div class="nav-menu">
            <a href="index.php?controller=offre&action=admin-lister" class="nav-link active">Offres</a>
            <a href="index.php?controller=candidature&action=admin-lister" class="nav-link">Candidatures</a>
            <a href="index.php" class="nav-link">Accueil</a>
        </div>
        <div class="lang-toggle">
            <button class="lang-btn active" data-lang="fr">FR</button>
            <button class="lang-btn" data-lang="ar">AR</button>
            <button id="theme-toggle" class="lang-btn" title="Mode sombre"><i class="fas fa-moon"></i></button>
        </div>
    </div>
</nav>

<main class="container" style="padding: 2rem 0;">
    <!-- Tableau de bord Statistiques PRO -->
    <style>
        .stats-grid-pro { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
        .stat-card-pro {
            background: var(--white); border-radius: 1.2rem; padding: 1.5rem;
            box-shadow: var(--shadow-md); position: relative; overflow: hidden;
            display: flex; flex-direction: column; gap: 0.5rem; transition: 0.3s;
            border-left: 5px solid var(--primary);
        }
        .stat-card-pro:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
        .stat-card-pro.warning { border-left-color: #FFB800; }
        .stat-card-pro.success { border-left-color: #00A86B; }
        .stat-card-pro.info { border-left-color: #3B82F6; }
        .stat-card-pro .icon { font-size: 2rem; margin-bottom: 0.3rem; }
        .stat-card-pro .num { font-size: 2.5rem; font-weight: 800; color: var(--primary); line-height: 1; }
        .stat-card-pro.warning .num { color: #FFB800; }
        .stat-card-pro.success .num { color: #00A86B; }
        .stat-card-pro.info .num { color: #3B82F6; }
        .stat-card-pro .lbl { font-size: 0.9rem; color: var(--gray-700); font-weight: 600; }
        .stat-card-pro .badge-trend {
            display: inline-flex; align-items: center; gap: 4px;
            font-size: 0.75rem; font-weight: 700; padding: 2px 8px;
            border-radius: 1rem; margin-top: 0.3rem; width: fit-content;
        }
        .trend-up { background: #d1fae5; color: #065f46; }
        .trend-warn { background: #fff3cd; color: #856404; }
        .progress-bar-wrap { background: var(--gray-300); border-radius: 1rem; height: 8px; margin-top: 0.5rem; overflow: hidden; }
        .progress-bar-fill { height: 100%; border-radius: 1rem; background: linear-gradient(90deg, var(--primary), var(--secondary)); transition: width 1.5s ease; }
        .stat-bg-icon { position: absolute; right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.07; }
    </style>
    <div class="stats-grid-pro">
        <div class="stat-card-pro">
            <span class="stat-bg-icon">💼</span>
            <div class="icon">📋</div>
            <div class="num counter" data-target="<?= $stats['total_offres'] ?? 0 ?>">0</div>
            <div class="lbl">Total des offres</div>
        </div>
        <div class="stat-card-pro warning">
            <span class="stat-bg-icon">⚡</span>
            <div class="icon">⏰</div>
            <div class="num counter" data-target="<?= $stats['offres_urgentes'] ?? 0 ?>">0</div>
            <div class="lbl">Offres urgentes (&lt; 7 jours)</div>
            <?php if (($stats['offres_urgentes'] ?? 0) > 0): ?>
            <span class="badge-trend trend-warn">⚠️ Action requise</span>
            <?php endif; ?>
        </div>
        <div class="stat-card-pro info">
            <span class="stat-bg-icon">📄</span>
            <div class="icon">📨</div>
            <div class="num counter" data-target="<?= $stats['total_candidatures'] ?? 0 ?>">0</div>
            <div class="lbl">Candidatures reçues</div>
            <span class="badge-trend trend-up">📈 +<?= $stats['candidatures_semaine'] ?? 0 ?> cette semaine</span>
        </div>
        <div class="stat-card-pro warning">
            <span class="stat-bg-icon">⌛</span>
            <div class="icon">🕐</div>
            <div class="num counter" data-target="<?= $stats['candidatures_attente'] ?? 0 ?>">0</div>
            <div class="lbl">En attente de traitement</div>
            <?php if (($stats['candidatures_attente'] ?? 0) > 0): ?>
            <span class="badge-trend trend-warn">⚠️ À traiter</span>
            <?php endif; ?>
        </div>
        <div class="stat-card-pro success" style="grid-column: span 1;">
            <span class="stat-bg-icon">✅</span>
            <div class="icon">📊</div>
            <div class="num"><?= $stats['taux_traitement'] ?? 0 ?>%</div>
            <div class="lbl">Taux de traitement</div>
            <div class="progress-bar-wrap">
                <div class="progress-bar-fill" style="width: 0%" data-width="<?= $stats['taux_traitement'] ?? 0 ?>"></div>
            </div>
        </div>
    </div>
    <script>
        // Compteurs animés
        document.querySelectorAll('.counter').forEach(el => {
            const target = parseInt(el.getAttribute('data-target')) || 0;
            let count = 0;
            const step = Math.max(1, Math.ceil(target / 40));
            const timer = setInterval(() => {
                count = Math.min(count + step, target);
                el.textContent = count;
                if (count >= target) clearInterval(timer);
            }, 30);
        });
        document.querySelectorAll('.progress-bar-fill').forEach(bar => {
            const w = bar.getAttribute('data-width') || 0;
            setTimeout(() => bar.style.width = w + '%', 300);
        });
    </script>
    <form class="search-wrapper" method="GET" action="index.php" style="display: flex; gap: 1rem; align-items: center; background: var(--white); border-radius: 3rem; padding: 0.3rem 0.3rem 0.3rem 1.5rem; box-shadow: var(--shadow-md); border: 1px solid var(--gray-300); margin-bottom: 2rem;">
        <input type="hidden" name="controller" value="offre">
        <input type="hidden" name="action" value="admin-lister">
        <i class="fas fa-search" style="color: var(--gray-500);"></i>
        <input type="text" name="search" placeholder="Rechercher une offre (titre, entité)..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="flex: 1; border: none; padding: 0.8rem 0; background: transparent; color: var(--dark); outline: none;">
        
        <select name="sort" style="border: none; background: transparent; color: var(--dark); outline: none; margin-right: 1rem; cursor: pointer; font-family: inherit; font-weight: 500;">
            <option value="id_offre DESC" <?= ($_GET['sort'] ?? '') == 'id_offre DESC' ? 'selected' : '' ?>>Plus récentes</option>
            <option value="titre ASC" <?= ($_GET['sort'] ?? '') == 'titre ASC' ? 'selected' : '' ?>>Titre (A-Z)</option>
            <option value="date_limite ASC" <?= ($_GET['sort'] ?? '') == 'date_limite ASC' ? 'selected' : '' ?>>Date limite (proche)</option>
        </select>
        <button type="submit" style="background: var(--primary); border: none; border-radius: 2rem; padding: 0.7rem 1.8rem; color: white; font-weight: 600; cursor: pointer; transition: 0.3s;"><i class="fas fa-filter"></i> Filtrer</button>
    </form>
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-briefcase"></i> Liste des offres</h2>
            <a href="index.php?controller=offre&action=ajouter" class="btn btn-primary">
                <i class="fas fa-plus"></i> Ajouter une offre
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>ID</th><th>Titre</th><th>Entité</th><th>Date limite</th><th>Postes</th><th>Statut</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if (empty($offres)): ?>
                        <tr><td colspan="7" style="text-align: center;">Aucune offre trouvée.<?php else: ?>
                        <?php foreach ($offres as $offre): ?>
                        <tr>
                            <td>#<?= $offre['id_offre'] ?></td>
                            <td><?= htmlspecialchars($offre['titre']) ?></td>
                            <td><?= htmlspecialchars($offre['entite']) ?></td>
                            <td><?= $offre['date_limite'] ?></td>
                            <td><?= $offre['nombre_postes'] ?></td>
                            <td><span class="badge <?= $offre['statut'] == 'Ouvert' ? 'badge-success' : 'badge-danger' ?>"><?= $offre['statut'] ?></span><td>
                            <td class="actions">
                                <a href="?controller=offre&action=admin-lister&show_candidatures=<?= $offre['id_offre'] ?>" class="btn btn-outline btn-sm" title="Consulter les candidatures">
                                    <i class="fas fa-users"></i> Consulter
                                </a>
                                <a href="index.php?controller=offre&action=modifier&id=<?= $offre['id_offre'] ?>" class="btn btn-outline btn-sm" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="index.php?controller=offre&action=supprimer&id=<?= $offre['id_offre'] ?>" class="btn btn-outline btn-sm" title="Supprimer" onclick="return confirm('Supprimer cette offre ?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php if ($showCandidaturesFor && !empty($candidatures)): ?>
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h2><i class="fas fa-users"></i> Candidatures pour l'offre : <?= $offreTitre ?></h2>
            <a href="?controller=offre&action=admin-lister" class="btn btn-outline btn-sm">
                <i class="fas fa-times"></i> Fermer
            </a>
        </div>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr><th>ID</th><th>Candidat</th><th>Email</th><th>Téléphone</th><th>CV</th><th>Statut</th><th>Date de candidature</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($candidatures as $c): ?>
                    <tr>
                        <td>#<?= $c['id_cond'] ?></td>
                        <td><?= htmlspecialchars($c['prenom'] . ' ' . $c['nom']) ?></td>
                        <td><?= htmlspecialchars($c['email']) ?></td>
                        <td><?= htmlspecialchars($c['num_tel']) ?></td>
                        <td><a href="index.php?controller=candidature&action=telecharger-cv&id=<?= $c['id_cond'] ?>" class="btn btn-outline btn-sm"><i class="fas fa-download"></i> CV</a></td>
                        <td>
                            <?php
                            if ($c['statut'] == 'en_attend') echo '<span class="badge badge-warning">En attente</span>';
                            elseif ($c['statut'] == 'validee') echo '<span class="badge badge-success">Validée</span>';
                            else echo '<span class="badge badge-danger">Rejetée</span>';
                            ?>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($c['date_cond'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php elseif ($showCandidaturesFor && empty($candidatures)): ?>
    <div class="card" style="margin-top: 2rem; background: #f8d7da; color: #721c24;">
        <p><i class="fas fa-info-circle"></i> Aucune candidature trouvée pour cette offre.</p>
        <a href="?controller=offre&action=admin-lister" class="btn btn-outline btn-sm">Fermer</a>
    </div>
    <?php endif; ?>
</main>

<footer class="footer">
    <div class="footer-bottom">
        <p>&copy; 2025 INNOC@V - Administration des offres</p>
    </div>
</footer>
</body>
</html>