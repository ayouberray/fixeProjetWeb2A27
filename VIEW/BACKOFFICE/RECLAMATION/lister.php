<?php
require_once __DIR__ . "/../../../CONTROLLER/ReclamationController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReclamationController();
$reclamations = $ctrl->getAllReclamations();
$stats = $ctrl->getStatistiques();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>InnoGov - Gestion des réclamations</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: #F5F7FA; }
        
        .admin-sidebar { position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #0D3328 0%, #0A281E 100%); color: white; z-index: 100; overflow-y: auto; }
        .sidebar-header { padding: 24px 20px; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .logo-mini { display: flex; align-items: center; gap: 10px; font-size: 20px; font-weight: 700; font-family: 'Syne', sans-serif; }
        .logo-mini i { font-size: 28px; color: #006D5B; }
        .sidebar-subtitle { font-size: 12px; color: rgba(255,255,255,0.4); margin-top: 8px; }
        .sidebar-nav { padding: 0 16px; }
        .sidebar-link { display: flex; align-items: center; gap: 12px; padding: 12px 16px; margin-bottom: 4px; color: rgba(255,255,255,0.7); text-decoration: none; border-radius: 12px; transition: all 0.3s; font-size: 14px; font-weight: 500; }
        .sidebar-link i { width: 20px; font-size: 16px; }
        .sidebar-link:hover { background: rgba(255,255,255,0.08); color: white; }
        .sidebar-link.active { background: #006D5B; color: white; }
        .sidebar-divider { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.3); padding: 16px 16px 8px; margin-top: 8px; }
        
        .admin-main { flex: 1; margin-left: 280px; }
        .admin-topbar { background: white; padding: 16px 24px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #E5E7EB; position: sticky; top: 0; z-index: 99; }
        .menu-toggle-btn { display: none; background: none; border: none; font-size: 20px; cursor: pointer; color: #006D5B; }
        .user-info { display: flex; align-items: center; gap: 10px; padding: 8px 16px; background: #F3F4F6; border-radius: 30px; font-size: 14px; font-weight: 500; }
        .user-info i { font-size: 20px; color: #006D5B; }
        .admin-content { padding: 24px; }
        
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; border-radius: 16px; padding: 20px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .stat-card:hover { transform: translateY(-2px); }
        .stat-info h3 { font-size: 13px; color: #6B7280; margin-bottom: 8px; }
        .stat-number { font-size: 28px; font-weight: 700; color: #006D5B; font-family: 'Syne', sans-serif; }
        .stat-icon { width: 48px; height: 48px; background: #E6F4F0; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #006D5B; font-size: 24px; }
        
        .card { background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; }
        .card-title { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .card-body { padding: 20px 24px; }
        .table-wrapper { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 12px 16px; background: #F9FAFB; color: #6B7280; font-weight: 600; font-size: 13px; cursor: pointer; user-select: none; transition: background 0.2s; }
        .table th:hover { background: #F3F4F6; color: #006D5B; }
        .table th i { margin-left: 5px; font-size: 11px; color: #9CA3AF; }
        .table td { padding: 12px 16px; border-bottom: 1px solid #F0F0F0; font-size: 14px; }
        .table tr:hover td { background: #F9FAFB; }
        
        .search-container { display: flex; align-items: center; background: #F3F4F6; border-radius: 8px; padding: 6px 12px; margin-left: auto; width: 300px; }
        .search-container i { color: #6B7280; margin-right: 8px; }
        .search-input { border: none; background: transparent; outline: none; width: 100%; font-family: 'DM Sans', sans-serif; font-size: 13px; }
        
        .chart-container { background: white; border-radius: 16px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; justify-content: center; align-items: center; height: 300px; }
        .charts-wrapper { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        
        .badge { display: inline-flex; padding: 4px 10px; border-radius: 30px; font-size: 12px; font-weight: 600; }
        .badge-soumise { background: #FEF3C7; color: #D97706; }
        .badge-en_cours { background: #DBEAFE; color: #2563EB; }
        .badge-traitee { background: #D1FAE5; color: #006D5B; }
        .badge-rejetee { background: #FEE2E2; color: #DC2626; }
        
        .btn { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 500; text-decoration: none; transition: all 0.2s; border: none; cursor: pointer; }
        .btn-primary { background: #006D5B; color: white; }
        .btn-primary:hover { background: #004D3D; }
        .btn-info { background: #3B82F6; color: white; }
        .btn-info:hover { background: #2563EB; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; }
        .modal-content { background: white; border-radius: 20px; width: 550px; max-width: 90%; max-height: 80vh; overflow-y: auto; animation: modalFadeIn 0.3s ease; }
        @keyframes modalFadeIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .modal-header { padding: 20px 24px; border-bottom: 1px solid #E5E7EB; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; background: white; }
        .modal-header h3 { font-size: 18px; font-weight: 600; display: flex; align-items: center; gap: 8px; color: #1A2E2A; }
        .close-modal { background: none; border: none; font-size: 24px; cursor: pointer; color: #6B7280; }
        .close-modal:hover { color: #DC2626; }
        .modal-body { padding: 20px 24px; }
        .modal-footer { padding: 16px 24px; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end; background: #F9FAFB; }
        
        .detail-row { display: flex; padding: 10px 0; border-bottom: 1px solid #F0F0F0; }
        .detail-label { width: 110px; font-weight: 600; color: #374151; font-size: 13px; }
        .detail-value { flex: 1; color: #1A2E2A; font-size: 13px; word-break: break-word; }
        
        @media (max-width: 768px) { 
            .admin-sidebar { transform: translateX(-100%); } 
            .admin-sidebar.open { transform: translateX(0); } 
            .admin-main { margin-left: 0; } 
            .menu-toggle-btn { display: block; }
            .detail-label { width: 100px; }
        }
    </style>
</head>
<body>

<div style="display: flex; min-height: 100vh;">
    <aside class="admin-sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo-mini"><i class="fas fa-building"></i><span>InnoGov</span></div>
            <p class="sidebar-subtitle">Administration</p>
        </div>
        <nav class="sidebar-nav">
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-tachometer-alt"></i><span>Tableau de bord</span></a>
            <div class="sidebar-divider">GESTION</div>
            <a href="lister.php" class="sidebar-link active"><i class="fas fa-comment-dots"></i><span>Réclamations</span></a>
            <a href="../REPONSE/lister.php" class="sidebar-link"><i class="fas fa-reply"></i><span>Réponses</span></a>
            <div class="sidebar-divider">SYSTÈME</div>
            <a href="../../../index.php" class="sidebar-link"><i class="fas fa-sign-out-alt"></i><span>Retour au site</span></a>
        </nav>
    </aside>
    
    <main class="admin-main">
        <div class="admin-topbar">
            <button class="menu-toggle-btn" id="menuToggle"><i class="fas fa-bars"></i></button>
            <div style="display: flex; align-items: center; gap: 15px;">
                <button onclick="genererPDF()" class="btn btn-primary" id="btnExportPDF" style="background: #1A2E2A; box-shadow: 0 4px 12px rgba(26, 46, 42, 0.2); padding: 8px 16px;">
                    <i class="fas fa-file-pdf" style="color: #F87171;"></i> Rapport PDF
                </button>
                <div class="user-info"><i class="fas fa-user-circle"></i><span>Admin Système</span></div>
            </div>
        </div>
        
        <div class="admin-content">
            <div class="stats-grid">
                <div class="stat-card"><div class="stat-info"><h3>Total réclamations</h3><div class="stat-number"><?= $stats['total'] ?></div></div><div class="stat-icon"><i class="fas fa-comment-dots"></i></div></div>
                <div class="stat-card"><div class="stat-info"><h3>En attente</h3><div class="stat-number"><?= $stats['soumise'] ?></div></div><div class="stat-icon"><i class="fas fa-clock"></i></div></div>
                <div class="stat-card"><div class="stat-info"><h3>En cours</h3><div class="stat-number"><?= $stats['en_cours'] ?></div></div><div class="stat-icon"><i class="fas fa-spinner"></i></div></div>
                <div class="stat-card"><div class="stat-info"><h3>Traitées</h3><div class="stat-number"><?= $stats['traitee'] ?></div></div><div class="stat-icon"><i class="fas fa-check-circle"></i></div></div>
            </div>
            
            <div class="charts-wrapper">
                <div class="chart-container">
                    <canvas id="reclamationStatusChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="reclamationBarChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="reclamationPriorityChart"></canvas>
                </div>
                <div class="chart-container">
                    <canvas id="reclamationCategoryChart"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2 class="card-title"><i class="fas fa-list"></i> Liste des réclamations</h2>
                    <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center; flex: 1; justify-content: flex-end;">
                        <div class="search-container">
                            <i class="fas fa-search"></i>
                            <input type="text" class="search-input" id="searchInput" placeholder="Rechercher une réclamation..." onkeyup="searchTable()">
                        </div>
                        <a href="ajouter.php" class="btn btn-primary btn-sm"><i class="fas fa-plus"></i> Nouvelle réclamation</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-wrapper">
                        <table class="table" id="dataTable">
                            <thead>
                                <tr>
                                    <th onclick="sortTable(0)">ID <i class="fas fa-sort"></i></th>
                                    <th onclick="sortTable(1)">Réf. <i class="fas fa-sort"></i></th>
                                    <th onclick="sortTable(2)">Citoyen <i class="fas fa-sort"></i></th>
                                    <th onclick="sortTable(3)">Objet <i class="fas fa-sort"></i></th>
                                    <th onclick="sortTable(4)">Statut <i class="fas fa-sort"></i></th>
                                    <th onclick="sortTable(5)">Date <i class="fas fa-sort"></i></th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($reclamations as $r): ?>
                                <tr>
                                    <td>#<?= $r['id_reclamation'] ?></td>
                                    <td><?= htmlspecialchars($r['reference']) ?></td>
                                    <td><?= htmlspecialchars($r['citoyen']) ?></td>
                                    <td><?= htmlspecialchars(substr($r['objet'], 0, 40)) ?>...</td>
                                    <td><span class="badge badge-<?= $r['statut'] ?>"><?= $r['statut'] ?></span></td>
                                    <td><?= date('d/m/Y', strtotime($r['date_soumission'])) ?></td>
                                    <td>
                                        <button class="btn btn-info btn-sm" onclick='showDetails(<?= json_encode($r, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>)'><i class="fas fa-eye"></i> Détails</button>
                                        <a href="../REPONSE/ajouter.php?id_reclamation=<?= $r['id_reclamation'] ?>" class="btn btn-primary btn-sm">Répondre</a>
                                        <a href="modifier.php?id=<?= $r['id_reclamation'] ?>" class="btn btn-primary btn-sm">Modifier</a>
                                        <a href="supprimer.php?id=<?= $r['id_reclamation'] ?>" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i> Supprimer</a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<div id="detailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3><i class="fas fa-info-circle"></i> Détails de la réclamation</h3>
            <button class="close-modal" onclick="closeModal()">&times;</button>
        </div>
        <div class="modal-body" id="modalBody"></div>
        <div class="modal-footer">
            <button class="btn btn-primary" onclick="closeModal()">Fermer</button>
        </div>
    </div>
</div>

<!-- Le template PDF a été supprimé pour générer directement depuis le tableau -->

<script src="../../../ASSETS/JS/script.js"></script>
<script>
    // Fonction PDF Generator (Dynamique basé sur le tableau visible)
    function genererPDF() {
        const btn = document.getElementById('btnExportPDF');
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Génération...';
        btn.disabled = true;

        const today = new Date();
        const dateStr = today.toLocaleDateString('fr-FR') + ' à ' + today.toLocaleTimeString('fr-FR', {hour: '2-digit', minute:'2-digit'});

        // Cloner le tableau actuel pour le manipuler
        const originalTable = document.getElementById('dataTable');
        const tableClone = originalTable.cloneNode(true);
        
        // Nettoyer et styliser le clone
        const ths = tableClone.querySelectorAll('th');
        const actionColIndex = ths.length - 1; // La dernière colonne est "Actions"
        
        if(ths[actionColIndex] && ths[actionColIndex].innerText.includes('Action')) {
            ths[actionColIndex].remove();
        }
        
        const trs = tableClone.querySelectorAll('tbody tr');
        let visibleCount = 0;
        
        trs.forEach((tr) => {
            // Si la ligne est cachée par la recherche, on la supprime du clone
            if (tr.style.display === 'none') {
                tr.remove();
                return;
            }
            
            visibleCount++;
            const tds = tr.querySelectorAll('td');
            if (tds.length > actionColIndex) {
                tds[actionColIndex].remove(); // Enlever la cellule d'action
            }
            
            // Styliser les cellules en Inline CSS pour html2pdf
            tr.style.backgroundColor = visibleCount % 2 === 0 ? '#F9FAFB' : '#FFFFFF';
            tds.forEach(td => {
                td.style.padding = '10px';
                td.style.border = '1px solid #E5E7EB';
                td.style.fontSize = '12px';
            });
        });
        
        // Styliser l'en-tête du tableau
        tableClone.querySelectorAll('th').forEach(th => {
            th.style.backgroundColor = '#006D5B';
            th.style.color = 'white';
            th.style.padding = '12px 10px';
            th.style.border = '1px solid #004D3D';
            th.style.fontSize = '13px';
            th.style.textAlign = 'left';
            
            // Enlever l'icône de tri
            const icon = th.querySelector('i');
            if(icon) icon.remove();
        });
        
        tableClone.style.width = '100%';
        tableClone.style.borderCollapse = 'collapse';
        tableClone.style.marginBottom = '40px';
        
        // Récupérer les statistiques actuelles depuis le DOM
        const statNumbers = document.querySelectorAll('.stat-number');
        const statTotal = statNumbers[0] ? statNumbers[0].innerText : '0';
        const statAttente = statNumbers[1] ? statNumbers[1].innerText : '0';
        const statEnCours = statNumbers[2] ? statNumbers[2].innerText : '0';
        const statTraitees = statNumbers[3] ? statNumbers[3].innerText : '0';
        
        // Construire le code HTML complet sous forme de chaîne de caractères
        const pdfHTML = `
            <div style="padding: 30px; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; color: #1A2E2A; background: white;">
                <div style="text-align: center; border-bottom: 3px solid #006D5B; padding-bottom: 20px; margin-bottom: 30px;">
                    <h1 style="color: #006D5B; margin-bottom: 10px; font-size: 26px;">Rapport Analytique des Réclamations</h1>
                    <p style="font-size: 13px; color: #6B7280;">Document généré le ${dateStr} | Plateforme InnoGov</p>
                </div>
                
                <!-- Section Statistiques -->
                <h2 style="color: #1A2E2A; font-size: 16px; margin-bottom: 15px; border-left: 4px solid #006D5B; padding-left: 10px;">1. Bilan Global</h2>
                <div style="display: flex; gap: 15px; margin-bottom: 30px;">
                    <div style="flex: 1; background: #F9FAFB; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #E5E7EB;">
                        <div style="font-size: 10px; color: #6B7280; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Total</div>
                        <div style="font-size: 22px; font-weight: bold; color: #111827;">${statTotal}</div>
                    </div>
                    <div style="flex: 1; background: #FEF3C7; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #FDE68A;">
                        <div style="font-size: 10px; color: #B45309; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">En Attente</div>
                        <div style="font-size: 22px; font-weight: bold; color: #92400E;">${statAttente}</div>
                    </div>
                    <div style="flex: 1; background: #DBEAFE; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #BFDBFE;">
                        <div style="font-size: 10px; color: #1D4ED8; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">En Cours</div>
                        <div style="font-size: 22px; font-weight: bold; color: #1E40AF;">${statEnCours}</div>
                    </div>
                    <div style="flex: 1; background: #D1FAE5; padding: 15px; border-radius: 8px; text-align: center; border: 1px solid #A7F3D0;">
                        <div style="font-size: 10px; color: #047857; text-transform: uppercase; font-weight: bold; margin-bottom: 5px;">Traitées</div>
                        <div style="font-size: 22px; font-weight: bold; color: #065F46;">${statTraitees}</div>
                    </div>
                </div>

                <!-- Section Tableau -->
                <h2 style="color: #1A2E2A; font-size: 16px; margin-bottom: 15px; border-left: 4px solid #006D5B; padding-left: 10px;">2. Registre Filtré</h2>
                ${tableClone.outerHTML}
                
                <div style="text-align: center; font-size: 11px; color: #9CA3AF; border-top: 1px solid #E5E7EB; padding-top: 20px;">
                    Document confidentiel réservé à l'administration - Municipalité Tunisienne &copy; ${today.getFullYear()}
                </div>
            </div>
        `;

        const opt = {
            margin:       0.5,
            filename:     'InnoGov_Rapport_Reclamations.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2 },
            jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
        };

        // Passer directement la chaîne HTML génère le PDF sans problème d'affichage
        html2pdf().set(opt).from(pdfHTML).save().then(() => {
            btn.innerHTML = '<i class="fas fa-check"></i> Terminé';
            btn.style.background = '#10B981';
            
            setTimeout(() => {
                btn.innerHTML = originalContent;
                btn.style.background = '#1A2E2A';
                btn.disabled = false;
            }, 3000);
        });
    }

    const menuToggle = document.getElementById('menuToggle');
    const sidebar = document.getElementById('sidebar');
    if(menuToggle && sidebar) {
        menuToggle.addEventListener('click', () => { sidebar.classList.toggle('open'); });
    }

    // Statistiques Dynamiques (Chart.js)
    const statsData = {
        labels: ['Soumise', 'En cours', 'Traitée', 'Rejetée'],
        datasets: [{
            data: [<?= $stats['soumise'] ?>, <?= $stats['en_cours'] ?>, <?= $stats['traitee'] ?>, <?= $stats['rejetee'] ?>],
            backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
            hoverBackgroundColor: ['#D97706', '#2563EB', '#059669', '#DC2626'],
            borderWidth: 0
        }]
    };

    // Doughnut Chart
    const ctxPie = document.getElementById('reclamationStatusChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'doughnut',
        data: statsData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Répartition par Statut', font: { family: "'DM Sans', sans-serif", size: 16 } }
            },
            cutout: '65%'
        }
    });

    // Bar Chart
    const ctxBar = document.getElementById('reclamationBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['Soumise', 'En cours', 'Traitée', 'Rejetée'],
            datasets: [{
                label: 'Nombre de réclamations',
                data: [<?= $stats['soumise'] ?>, <?= $stats['en_cours'] ?>, <?= $stats['traitee'] ?>, <?= $stats['rejetee'] ?>],
                backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Volume de réclamations', font: { family: "'DM Sans', sans-serif", size: 16 } }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                x: { grid: { display: false } }
            }
        }
    });

    // Statistiques avancées : Traitement des données JS
    const reclamationsData = <?= json_encode($reclamations, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>;
    
    // Calcul des priorités
    let priorities = { 'basse': 0, 'moyenne': 0, 'haute': 0, 'urgente': 0 };
    let categories = {};

    reclamationsData.forEach(r => {
        // Comptage par priorité
        let p = r.priorite ? r.priorite.toLowerCase() : 'non définie';
        if(priorities[p] !== undefined) priorities[p]++;
        else priorities[p] = 1;

        // Comptage par catégorie
        let c = r.categorie || 'Autre';
        categories[c] = (categories[c] || 0) + 1;
    });

    // Graphique Polar Area pour les priorités
    const ctxPriority = document.getElementById('reclamationPriorityChart').getContext('2d');
    new Chart(ctxPriority, {
        type: 'polarArea',
        data: {
            labels: Object.keys(priorities).map(k => k.charAt(0).toUpperCase() + k.slice(1)),
            datasets: [{
                data: Object.values(priorities),
                backgroundColor: ['#10B981', '#F59E0B', '#EF4444', '#7F1D1D'],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                title: { display: true, text: 'Répartition par Priorité', font: { family: "'DM Sans', sans-serif", size: 16 } }
            }
        }
    });

    // Graphique Bar Horizontal pour les Catégories
    const sortedCategories = Object.entries(categories).sort((a, b) => b[1] - a[1]); // Tri décroissant
    const topCategories = sortedCategories.slice(0, 5); // Garder le top 5
    const ctxCat = document.getElementById('reclamationCategoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'bar',
        data: {
            labels: topCategories.map(c => c[0]),
            datasets: [{
                label: 'Nombre de demandes',
                data: topCategories.map(c => c[1]),
                backgroundColor: '#006D5B',
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y', // Barres horizontales
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                title: { display: true, text: 'Top 5 des Catégories', font: { family: "'DM Sans', sans-serif", size: 16 } }
            },
            scales: {
                x: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                y: { grid: { display: false } }
            }
        }
    });

    // Recherche intelligente dans le tableau (ID exact ou recherche globale)
    function searchTable() {
        let input = document.getElementById("searchInput");
        let filter = input.value.toUpperCase().trim();
        let table = document.getElementById("dataTable");
        let tr = table.getElementsByTagName("tr");

        // Détecter si on recherche spécifiquement un ID (commence par # ou est un nombre exact)
        let isIdSearch = /^#?\d+$/.test(filter);
        let cleanFilter = filter.replace('#', '');

        for (let i = 1; i < tr.length; i++) {
            let tds = tr[i].getElementsByTagName("td");
            if (tds.length > 0) {
                let match = false;

                if (isIdSearch) {
                    // Recherche exacte dans la première colonne (ID)
                    let idText = tds[0].textContent || tds[0].innerText;
                    let cleanId = idText.replace(/\D/g, ''); // Garder uniquement les chiffres
                    
                    if (cleanId === cleanFilter) {
                        match = true;
                    }
                } else {
                    // Recherche classique globale sur toute la ligne
                    let rowText = tr[i].textContent || tr[i].innerText;
                    if (rowText.toUpperCase().indexOf(filter) > -1) {
                        match = true;
                    }
                }

                tr[i].style.display = match ? "" : "none";
            }
        }
    }

    // Tri des colonnes du tableau (Alphabétique et Numérique)
    let sortDirections = {};
    function sortTable(n) {
        let table, rows, switching, i, x, y, shouldSwitch;
        table = document.getElementById("dataTable");
        switching = true;
        
        // Déterminer la direction de tri pour cette colonne (croissant par défaut)
        sortDirections[n] = sortDirections[n] === 'asc' ? 'desc' : 'asc';
        let isAsc = sortDirections[n] === 'asc';
        
        let headers = table.getElementsByTagName("th");
        for(let j=0; j<headers.length-1; j++) {
            headers[j].innerHTML = headers[j].innerHTML.replace(/fa-sort-(up|down)/, 'fa-sort');
        }
        
        if(isAsc) {
            headers[n].innerHTML = headers[n].innerHTML.replace('fa-sort', 'fa-sort-up');
        } else {
            headers[n].innerHTML = headers[n].innerHTML.replace('fa-sort', 'fa-sort-down');
        }

        while (switching) {
            switching = false;
            rows = table.rows;
            for (i = 1; i < (rows.length - 1); i++) {
                shouldSwitch = false;
                x = rows[i].getElementsByTagName("TD")[n];
                y = rows[i + 1].getElementsByTagName("TD")[n];
                
                // Récupérer le texte propre
                let cmpX = x.textContent || x.innerText;
                let cmpY = y.textContent || y.innerText;
                cmpX = cmpX.trim();
                cmpY = cmpY.trim();
                
                // Gestion spécifique pour les dates au format JJ/MM/AAAA ou JJ/MM/AAAA HH:MM:SS
                let dateRegex = /^(\d{2})\/(\d{2})\/(\d{4})/;
                let isDate = dateRegex.test(cmpX) && dateRegex.test(cmpY);
                
                let comparison = 0;
                
                if (isDate) {
                    let px = cmpX.match(dateRegex);
                    let py = cmpY.match(dateRegex);
                    let dx = new Date(px[3], px[2] - 1, px[1]);
                    let dy = new Date(py[3], py[2] - 1, py[1]);
                    comparison = dx - dy;
                } else {
                    // Comparaison intelligente : gère l'alphabet et les nombres de façon naturelle
                    comparison = cmpX.localeCompare(cmpY, undefined, { numeric: true, sensitivity: 'base' });
                }

                if (isAsc) {
                    if (comparison > 0) { shouldSwitch = true; break; }
                } else {
                    if (comparison < 0) { shouldSwitch = true; break; }
                }
            }
            if (shouldSwitch) {
                rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                switching = true;
            }
        }
    }
</script>
</body>
</html>
