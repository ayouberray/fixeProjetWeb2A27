<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../MODEL/config.php";

$db = Config::getConnexion();

$total = $db->query("SELECT COUNT(*) as total FROM rendez_vous")->fetch();
$en_attente = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='en_attente'")->fetch();
$confirme = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='confirme'")->fetch();
$annule = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='annule'")->fetch();
$termine = $db->query("SELECT COUNT(*) as total FROM rendez_vous WHERE statut='termine'")->fetch();

// Par service avec JOIN
$parService = $db->query("
    SELECT s.nom_service, COUNT(*) as total 
    FROM rendez_vous r
    JOIN services s ON r.id_service = s.id_service
    GROUP BY r.id_service, s.nom_service
    ORDER BY total DESC
")->fetchAll();

$parAgent = $db->query("SELECT agent_nom, COUNT(*) as total FROM rendez_vous WHERE agent_nom IS NOT NULL GROUP BY agent_nom ORDER BY total DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin - Statistiques</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gestion_RDV/projet/assets/css/style.css?v=20260509_v9">
    <script src="/Gestion_RDV/projet/assets/js/script.js" defer></script>
    <style>
        body {  font-family: 'Inter', sans-serif; background: var(--bg-page); }
        
        
        .futuristic-container { max-width: 1200px; margin: 40px auto; position: relative; z-index: 1; padding: 0 20px; }
        .page-header { text-align: center; margin-bottom: 40px; }
        .page-header h2 { font-size: 2rem; color: #1e293b; font-weight: 700; margin-bottom: 10px; }
        
        .card {
            background: #ffffff; border-radius: 12px;
            border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
            padding: 30px; margin-bottom: 30px;
        }

        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: #ffffff; padding: 25px; border-radius: 12px; text-align: center;
            border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 15px rgba(0,0,0,0.05); }
        .stat-card i { font-size: 2rem; color: var(--primary); margin-bottom: 15px; }
        .stat-card .number { font-size: 2.5rem; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
        .stat-card .label { color: #64748b; font-size: 0.95rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        .table { width: 100%; border-collapse: collapse; }
        .table th { background: var(--primary); color: white; font-weight: 600; text-transform: uppercase; font-size: 13px; padding: 12px 15px; text-align: left; }
        .table td { padding: 15px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        .table tr:last-child td { border-bottom: none; }
        .table tr:hover td { background: var(--bg-page); }
        
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 600; }
        .badge-confirme { background: #dcfce7; color: #16a34a; }
        .badge-primary { background: #e0e7ff; color: #4f46e5; }

        /* Animations */
        .stat-card, .card, .chart-container-card {
            opacity: 0;
            transform: translateY(20px);
            animation: slideUp 0.6s ease forwards;
        }
        @keyframes slideUp {
            to { opacity: 1; transform: translateY(0); }
        }
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .chart-container-card { animation-delay: 0.6s; }
    </style>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Rendez-vous</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-services.php" class="nav-link">Services</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link active">Statistiques</a>
            <a href="/Gestion_RDV/projet/index.php" class="nav-link">Espace citoyen</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle" title="Mode Sombre/Clair"><i class="fas fa-sun"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <button onclick="window.print()" class="nav-cta">
                <i class="fas fa-file-pdf"></i> Exporter
            </button>
        </div>
    </nav>
</div>

<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-slideshow">
        <img src="/Gestion_RDV/projet/assets/images/tunisia1.jpg" class="slide active" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia2.jpg" class="slide" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia3.jpg" class="slide" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia4.jpg" class="slide" alt="Tunisie">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Administration Municipale</h1>
        <p>Gérez les services et les rendez-vous en toute simplicité</p>
    </div>
</section>

<div class="futuristic-container">
    <div class="page-header">
        <h2><i class="fas fa-chart-line" style="color: var(--primary);"></i> Statistiques des rendez-vous</h2>
        <p>Analyse des données et de l'activité</p>
    </div>

    <div class="card reveal">
        <div class="stats-grid">
            <div class="stat-card"><i class="fas fa-calendar-alt"></i><div class="number"><?= $total['total'] ?></div><div class="label">Total RDV</div></div>
            <div class="stat-card"><i class="fas fa-clock"></i><div class="number"><?= $en_attente['total'] ?></div><div class="label">En attente</div></div>
            <div class="stat-card"><i class="fas fa-check-circle"></i><div class="number"><?= $confirme['total'] ?></div><div class="label">Confirmés</div></div>
            <div class="stat-card"><i class="fas fa-times-circle"></i><div class="number"><?= $annule['total'] ?></div><div class="label">Annulés</div></div>
            <div class="stat-card"><i class="fas fa-check-double"></i><div class="number"><?= $termine['total'] ?></div><div class="label">Traités</div></div>
        </div>
        
        <!-- NOUVELLE DISPOSITION GRAPHIQUE -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px; margin-top: 40px;">
            <!-- Chart Statuts -->
            <div class="chart-container-card card" style="padding: 20px; border: 1px solid #e2e8f0;">
                <h3 style="margin-bottom: 20px; text-align: center; font-size: 1.2rem; color: #1e293b;"><i class="fas fa-chart-pie" style="color: var(--primary);"></i> Répartition par Statut</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="statutsChart"></canvas>
                </div>
            </div>

            <!-- Chart Services -->
            <div class="chart-container-card card" style="padding: 20px; border: 1px solid #e2e8f0;">
                <h3 style="margin-bottom: 20px; text-align: center; font-size: 1.2rem; color: #1e293b;"><i class="fas fa-concierge-bell" style="color: var(--primary);"></i> RDV par Service</h3>
                <div style="height: 300px; position: relative;">
                    <canvas id="servicesChart"></canvas>
                </div>
            </div>
            
            <!-- Chart Agents -->
            <div class="chart-container-card card" style="padding: 20px; border: 1px solid #e2e8f0;">
                <h3 style="margin-bottom: 20px; text-align: center; font-size: 1.2rem; color: #1e293b;"><i class="fas fa-users" style="color: var(--primary);"></i> RDV par Agent</h3>
                <div style="height: 300px; position: relative;">
                    <?php if(empty($parAgent)): ?>
                        <p style="text-align: center; color: #888; margin-top: 100px;">Aucun agent affecté</p>
                    <?php else: ?>
                        <canvas id="agentsChart"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <h3 class="section-title reveal" style="margin-top: 40px;"><i class="fas fa-table"></i> Données Détaillées (Cliquer pour voir les détails)</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
            <div class="table-wrapper reveal">
                <table class="table">
                    <thead><tr><th>Service</th><th>Nombre de RDV</th></tr></thead>
                    <tbody>
                        <?php foreach($parService as $s): ?>
                        <tr style="cursor: pointer;" onclick="showDetails('<?= htmlspecialchars($s['nom_service']) ?>', 'service')">
                            <td><i class="fas fa-folder-open" style="color: var(--primary);"></i> <?= htmlspecialchars($s['nom_service']) ?></td>
                            <td><span class="badge badge-confirme"><?= $s['total'] ?> RDV</span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-wrapper reveal">
                <table class="table">
                    <thead><tr><th>Agent</th><th>Nombre de RDV</th></tr></thead>
                    <tbody>
                        <?php foreach($parAgent as $a): ?>
                        <tr style="cursor: pointer;" onclick="showDetails('<?= htmlspecialchars($a['agent_nom']) ?>', 'agent')">
                            <td><i class="fas fa-user-tie" style="color: var(--secondary);"></i> <?= htmlspecialchars($a['agent_nom']) ?></td>
                            <td><span class="badge badge-termine"><?= $a['total'] ?> RDV</span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($parAgent)): ?>
                        <tr><td colspan="2" style="text-align:center">Aucun agent affecté</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- DRILL-DOWN MODAL -->
<div id="details-modal" class="premium-modal">
    <div class="modal-3d-box" style="max-width: 800px;">
        <button class="modal-close-btn" onclick="closePremiumModal('details-modal')">&times;</button>
        <div class="modal-badge" id="modal-type-badge">Détails</div>
        <h2 class="modal-title-glow" id="modal-detail-title">Chargement...</h2>
        <p class="modal-subtitle">Liste des rendez-vous correspondants</p>
        
        <div id="modal-content-list" style="max-height: 400px; overflow-y: auto; margin-top: 20px;">
            <div class="loader-inline" style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin fa-2x" style="color: var(--primary);"></i>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
window.addEventListener('DOMContentLoaded', () => {
    // Les données PHP converties en JSON
    const statutsData = [
        <?= (int)$en_attente['total'] ?>, 
        <?= (int)$confirme['total'] ?>, 
        <?= (int)$annule['total'] ?>, 
        <?= (int)$termine['total'] ?>
    ];
    
    const servicesLabels = <?= json_encode(array_column($parService, 'nom_service')) ?>;
    const servicesData = <?= json_encode(array_column($parService, 'total')) ?>;
    
    const agentsLabels = <?= json_encode(array_column($parAgent, 'agent_nom')) ?>;
    const agentsData = <?= json_encode(array_column($parAgent, 'total')) ?>;

    // Configuration globale
    Chart.defaults.font.family = "'Roboto', sans-serif";
    Chart.defaults.color = '#64748b';

    // Graphique : Statuts
    new Chart(document.getElementById('statutsChart'), {
        type: 'doughnut',
        data: {
            labels: ['En attente', 'Confirmés', 'Annulés', 'Traités'],
            datasets: [{
                data: statutsData,
                backgroundColor: ['#f59e0b', '#3b82f6', '#ef4444', '#10b981'],
                borderWidth: 0,
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                animateRotate: true,
                animateScale: true,
                duration: 2000,
                easing: 'easeOutElastic'
            },
            plugins: {
                legend: { position: 'bottom' }
            },
            cutout: '65%'
        }
    });

    // Graphique : Services
    new Chart(document.getElementById('servicesChart'), {
        type: 'bar',
        data: {
            labels: servicesLabels,
            datasets: [{
                label: 'Nombre de RDV',
                data: servicesData,
                backgroundColor: 'rgba(58, 90, 42, 0.8)',
                borderColor: '#3a5a2a',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 2000,
                easing: 'easeOutBounce'
            },
            plugins: {
                legend: { display: false }
            },
            scales: { 
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false } }
            }
        }
    });

    // Graphique : Agents
    if(document.getElementById('agentsChart')) {
        new Chart(document.getElementById('agentsChart'), {
            type: 'bar',
            data: {
                labels: agentsLabels,
                datasets: [{
                    label: 'Nombre de RDV',
                    data: agentsData,
                    backgroundColor: 'rgba(192, 123, 61, 0.8)',
                    borderColor: '#c07b3d',
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 2500,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                },
                scales: { 
                    y: { beginAtZero: true, ticks: { stepSize: 1 } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
    }
});

async function showDetails(name, type) {
    openPremiumModal('details-modal');
    document.getElementById('modal-detail-title').textContent = name;
    document.getElementById('modal-type-badge').textContent = type === 'service' ? 'Par Service' : 'Par Agent';
    const container = document.getElementById('modal-content-list');
    container.innerHTML = '<div style="text-align:center; padding:40px;"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

    try {
        // We use the admin-lister-rdv.php as a source or create a small API endpoint
        // For now, let's assume we can fetch data. We'll use the existing controller logic via a hidden fetch if needed, 
        // but since we are in PHP, let's just use a simple fetch to a helper API we'll create.
        const response = await fetch(`/Gestion_RDV/projet/api/get_rdv_details.php?type=${type}&name=${encodeURIComponent(name)}`);
        const data = await response.json();

        if (data.length === 0) {
            container.innerHTML = '<p style="text-align:center; padding:20px;">Aucun rendez-vous trouvé.</p>';
            return;
        }

        let html = '<div style="display:flex; flex-direction:column; gap:10px;">';
        data.forEach(rdv => {
            html += `
                <div style="background:var(--bg-page); padding:15px; border-radius:15px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="font-weight:700;">${rdv.citoyen_nom}</div>
                        <div style="font-size:0.85rem; color:var(--gray-700);">${rdv.date_heure}</div>
                    </div>
                    <div class="badge badge-${rdv.statut}">${rdv.statut}</div>
                </div>
            `;
        });
        html += '</div>';
        container.innerHTML = html;
    } catch (error) {
        container.innerHTML = '<p style="color:var(--danger); text-align:center;">Erreur lors du chargement des données.</p>';
    }
}
</script>
</body>
</html>
