<?php
require_once __DIR__."/../../CONTROLLER/employercontroller.php";
$employercontroller=new employeController();
$list=$employercontroller->getEmployes();

// Statistiques pour les cartes d'information
$totalEmployes = count($list);
$salaireMoyen = array_sum(array_column($list, 'salaire')) / ($totalEmployes ?: 1);
$salaireTotal = array_sum(array_column($list, 'salaire'));

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Employés - Administration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        /* Particules de fond */
        .background-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
        }

        .particle {
            position: absolute;
            width: 6px;
            height: 6px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            pointer-events: none;
        }

        @keyframes floatParticle {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-30px) rotate(180deg); }
        }

        /* Conteneur principal */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
            animation: fadeInUp 0.8s ease-out;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* En-tête */
        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 30px 40px;
            margin-bottom: 30px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
        }

        .header-title h1 {
            font-size: 32px;
            color: #333;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .header-title p {
            color: #666;
            font-size: 16px;
            margin-top: 5px;
        }

        .back-btn {
            background: linear-gradient(145deg, #667eea, #764ba2);
            color: white;
            border: none;
            padding: 14px 32px;
            border-radius: 15px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.4);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .back-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 30px -5px rgba(102, 126, 234, 0.6);
        }

        .back-btn i {
            font-size: 18px;
            transition: transform 0.3s ease;
        }

        .back-btn:hover i {
            transform: translateX(-5px);
        }

        /* Cartes de statistiques */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 10px 30px -5px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.5);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -5px rgba(102, 126, 234, 0.3);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(145deg, #667eea, #764ba2);
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
        }

        .stat-info h3 {
            color: #666;
            font-size: 15px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .stat-info .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            letter-spacing: -0.5px;
        }

        .stat-info .stat-unit {
            font-size: 14px;
            color: #999;
            margin-left: 5px;
        }

        /* Barre de recherche */
        .search-container {
            margin-bottom: 25px;
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 18px;
        }

        .search-box input {
            width: 100%;
            padding: 16px 20px 16px 55px;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            color: #333;
        }

        .search-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.2);
        }

        .search-box input::placeholder {
            color: #999;
        }

        .filter-btn {
            background: white;
            border: 2px solid rgba(255, 255, 255, 0.2);
            padding: 16px 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            background: #667eea;
            color: white;
            border-color: #667eea;
        }

        /* Conteneur du tableau */
        .table-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            padding: 25px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.3);
            overflow-x: auto;
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Tableau */
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 8px;
            font-size: 15px;
        }

        th {
            padding: 20px 15px;
            text-align: left;
            color: #666;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            background: rgba(102, 126, 234, 0.05);
        }

        th:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        th:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        td {
            padding: 18px 15px;
            background: white;
            color: #444;
            font-weight: 500;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
        }

        tr {
            transition: all 0.3s ease;
            cursor: pointer;
        }

        tr:hover td {
            background: linear-gradient(145deg, #f8f9ff, #ffffff);
            transform: scale(1.01);
            box-shadow: 0 10px 30px -10px rgba(102, 126, 234, 0.2);
        }

        td:first-child {
            border-top-left-radius: 15px;
            border-bottom-left-radius: 15px;
        }

        td:last-child {
            border-top-right-radius: 15px;
            border-bottom-right-radius: 15px;
        }

        /* Badges pour le salaire */
        .salary-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 30px;
            font-weight: 600;
            font-size: 14px;
        }

        .salary-high {
            background: linear-gradient(145deg, #10b98120, #05966920);
            color: #059669;
        }

        .salary-medium {
            background: linear-gradient(145deg, #f59e0b20, #d9770620);
            color: #d97706;
        }

        .salary-low {
            background: linear-gradient(145deg, #ef444420, #dc262620);
            color: #dc2626;
        }

        /* Année d'embauche */
        .year-badge {
            display: inline-block;
            padding: 6px 12px;
            background: #f1f5f9;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            color: #475569;
        }

        /* Icônes dans le tableau */
        .table-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(145deg, #667eea20, #764ba220);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            font-size: 18px;
        }

        /* Message si aucun employé */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-state i {
            font-size: 80px;
            color: #ccc;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            font-size: 24px;
            color: #666;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 20px;
            }

            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            th, td {
                padding: 15px 10px;
                font-size: 14px;
            }

            .salary-badge {
                padding: 6px 12px;
                font-size: 12px;
            }
        }

        /* Loading animation */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>
</head>
<body>
    <!-- Particules de fond -->
    <div class="background-particles" id="particles"></div>

    <div class="container">
        <!-- En-tête -->
        <div class="header">
            <div class="header-top">
                <div class="header-title">
                    <div class="header-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div>
                        <h1>Gestion des employés</h1>
                        <p><i class="fas fa-clock" style="margin-right: 5px;"></i> Mis à jour en temps réel</p>
                    </div>
                </div>
                
                <button class="back-btn" onclick="window.location.href='../frontoffice/adminpanel.php'">
                    <i class="fas fa-arrow-left"></i>
                    <span>Retour au tableau de bord</span>
                </button>
            </div>

            <!-- Cartes de statistiques -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Total employés</h3>
                        <div>
                            <span class="stat-value"><?php echo $totalEmployes; ?></span>
                            <span class="stat-unit">personnes</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-coins"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Salaire moyen</h3>
                        <div>
                            <span class="stat-value"><?php echo number_format($salaireMoyen, 0, ',', ' '); ?></span>
                            <span class="stat-unit">€</span>
                        </div>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <div class="stat-info">
                        <h3>Masse salariale</h3>
                        <div>
                            <span class="stat-value"><?php echo number_format($salaireTotal, 0, ',', ' '); ?></span>
                            <span class="stat-unit">€</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Barre de recherche -->
        <div class="search-container">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un employé par nom, prénom ou ID..." onkeyup="filterTable()">
            </div>
            <div class="filter-btn" onclick="exportToCSV()">
                <i class="fas fa-download"></i>
                <span>Exporter</span>
            </div>
        </div>

        <!-- Tableau des employés -->
        <div class="table-container">
            <table id="employeeTable">
                <thead>
                    <tr>
                        <th><i class="fas fa-id-card" style="margin-right: 8px;"></i>ID</th>
                        <th><i class="fas fa-user" style="margin-right: 8px;"></i>Nom</th>
                        <th><i class="fas fa-user" style="margin-right: 8px;"></i>Prénom</th>
                        <th><i class="fas fa-euro-sign" style="margin-right: 8px;"></i>Salaire</th>
                        <th><i class="fas fa-calendar" style="margin-right: 8px;"></i>Année d'embauche</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($list)): ?>
                        <?php foreach($list as $e): 
                            // Déterminer la classe du badge de salaire
                            $salaireClass = 'salary-medium';
                            if ($e['salaire'] > 5000) {
                                $salaireClass = 'salary-high';
                            } elseif ($e['salaire'] < 2500) {
                                $salaireClass = 'salary-low';
                            }
                        ?> 
                        <tr onclick="viewEmployeeDetails(<?php echo $e['id']; ?>)">
                            <td>
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <span class="table-icon">
                                        <i class="fas fa-id-badge"></i>
                                    </span>
                                    <span style="font-weight: 600; color: #667eea;">#<?php echo $e["id"]; ?></span>
                                </div>
                            </td>
                            <td><span style="font-weight: 500;"><?php echo htmlspecialchars($e["nom"]); ?></span></td>
                            <td><?php echo htmlspecialchars($e["prenom"]); ?></td>
                            <td>
                                <span class="salary-badge <?php echo $salaireClass; ?>">
                                    <i class="fas fa-euro-sign" style="margin-right: 4px; font-size: 12px;"></i>
                                    <?php echo number_format($e["salaire"], 0, ',', ' '); ?>
                                </span>
                            </td>
                            <td>
                                <span class="year-badge">
                                    <i class="far fa-calendar-alt" style="margin-right: 6px;"></i>
                                    <?php echo $e["anneEmbauche"]; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fas fa-users-slash"></i>
                                    <h3>Aucun employé trouvé</h3>
                                    <p>Il n'y a pas encore d'employés dans la base de données.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Création des particules de fond
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const numberOfParticles = 30;

            for (let i = 0; i < numberOfParticles; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Position aléatoire
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = Math.random() * 100 + '%';
                
                // Animation personnalisée
                const duration = Math.random() * 20 + 15;
                const delay = Math.random() * 5;
                particle.style.animation = `floatParticle ${duration}s infinite ease-in-out`;
                particle.style.animationDelay = delay + 's';
                
                particlesContainer.appendChild(particle);
            }
        }

        // Filtrage du tableau
        function filterTable() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toUpperCase();
            const table = document.getElementById('employeeTable');
            const tr = table.getElementsByTagName('tr');

            for (let i = 1; i < tr.length; i++) {
                let display = false;
                const tdArray = tr[i].getElementsByTagName('td');
                
                for (let j = 0; j < tdArray.length; j++) {
                    const td = tdArray[j];
                    if (td) {
                        const txtValue = td.textContent || td.innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            display = true;
                            break;
                        }
                    }
                }
                
                tr[i].style.display = display ? '' : 'none';
            }
        }

        // Export en CSV
        function exportToCSV() {
            const table = document.getElementById('employeeTable');
            const rows = table.getElementsByTagName('tr');
            const csv = [];
            
            for (let i = 0; i < rows.length; i++) {
                const row = [], cols = rows[i].getElementsByTagName('td');
                
                // Pour l'en-tête
                if (i === 0) {
                    const headers = rows[i].getElementsByTagName('th');
                    for (let j = 0; j < headers.length; j++) {
                        row.push('"' + headers[j].innerText.replace(/"/g, '""') + '"');
                    }
                } else {
                    for (let j = 0; j < cols.length; j++) {
                        // Nettoyer le texte des badges HTML
                        let text = cols[j].innerText.replace(/[#€]/g, '').trim();
                        row.push('"' + text.replace(/"/g, '""') + '"');
                    }
                }
                csv.push(row.join(';'));
            }

            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', 'employes_' + new Date().toISOString().slice(0,10) + '.csv');
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Détails de l'employé (à implémenter selon vos besoins)
        function viewEmployeeDetails(id) {
            // Rediriger vers la page de détails ou afficher un modal
            console.log('Voir détails employé:', id);
            // window.location.href = 'employee_details.php?id=' + id;
        }

        // Animation de chargement simulée
        function simulateLoading() {
            const tableBody = document.querySelector('tbody');
            if (tableBody.children.length === 0 || tableBody.children[0].innerHTML.includes('empty-state')) {
                return;
            }

            const rows = tableBody.getElementsByTagName('tr');
            for (let row of rows) {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
            }

            setTimeout(() => {
                for (let i = 0; i < rows.length; i++) {
                    setTimeout(() => {
                        rows[i].style.transition = 'all 0.5s ease';
                        rows[i].style.opacity = '1';
                        rows[i].style.transform = 'translateY(0)';
                    }, i * 100);
                }
            }, 100);
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            simulateLoading();

            // Raccourci clavier pour la recherche
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey && e.key === 'f') {
                    e.preventDefault();
                    document.getElementById('searchInput').focus();
                }
            });
        });
    </script>
</body>
</html>