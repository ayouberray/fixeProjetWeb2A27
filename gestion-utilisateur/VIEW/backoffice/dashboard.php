<?php
// Fichier: VIEW/backoffice/dashboard.php - Version simplifiée (sans CSS)

if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'admin' && $_SESSION['user_role'] !== 'agent')) {
    header('Location: ../frontoffice/login.php');
    exit();
}

$user_role = $_SESSION['user_role'];
$isAdmin = ($user_role === 'admin');

require_once '../../MODEL/Utilisateur.php';
$utilisateur = new Utilisateur();
$currentUser = $utilisateur->getById($_SESSION['user_id']);

// Chargement sécurisé de la classe Statistiques
$statistiques = null;
if (file_exists('../../MODEL/Statistiques.php')) {
    require_once '../../MODEL/Statistiques.php';
    try {
        $statistiques = new Statistiques();
    } catch (Exception $e) {
        $statistiques = null;
    }
}

// Période sélectionnée
$period = $_GET['period'] ?? '30days';
$validPeriods = ['7days', '30days', '90days', '365days'];
if (!in_array($period, $validPeriods)) $period = '30days';

// Données selon la période
switch($period) {
    case '7days':
        $currentPeriod = $utilisateur->countUsersByPeriod('week');
        $previousPeriod = $utilisateur->countUsersByPeriod('last_week');
        $periodLabel = '7 derniers jours';
        $comparisonLabel = 'vs semaine précédente';
        break;
    case '90days':
        $currentPeriod = $utilisateur->countUsersByPeriod('quarter');
        $previousPeriod = $utilisateur->countUsersByPeriod('last_quarter');
        $periodLabel = 'Ce trimestre';
        $comparisonLabel = 'vs trimestre précédent';
        break;
    case '365days':
        $currentPeriod = $utilisateur->countUsersByPeriod('year');
        $previousPeriod = $utilisateur->countUsersByPeriod('last_year');
        $periodLabel = 'Cette année';
        $comparisonLabel = 'vs année précédente';
        break;
    default: // 30days
        $currentPeriod = $utilisateur->countUsersByPeriod('month');
        $previousPeriod = $utilisateur->countUsersByPeriod('last_month');
        $periodLabel = 'Ce mois';
        $comparisonLabel = 'vs mois précédent';
}

// Statistiques principales
$users = $utilisateur->getAll();
$totalUsers = $utilisateur->countAll();
$totalCitoyens = $utilisateur->countByRole('user');
$totalAdmins = $utilisateur->countByRole('admin');
$totalAgents = $utilisateur->countByRole('agent');
$activeUsers = $utilisateur->countActiveUsers();

// Calculs de croissance
$growthPercentage = $previousPeriod > 0 ? round(($currentPeriod - $previousPeriod) / $previousPeriod * 100) : 0;
$engagementRate = $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100) : 0;
$growthClass = $growthPercentage >= 0 ? 'positive' : 'negative';
$growthIcon = $growthPercentage >= 0 ? 'arrow-up' : 'arrow-down';

// Types de comptes
$totalCitoyensStandard = $utilisateur->countByTypeCompte('citoyen');
$totalProfessionnels = $utilisateur->countByTypeCompte('professionnel');
$totalAgentsPublics = $utilisateur->countByTypeCompte('agent_public');
$completionRate = $utilisateur->getProfileCompletionRate();

// Données graphiques (6 derniers mois)
$userData = array_slice($utilisateur->getMonthlyRegistrations(), -6);
$months = array_slice(['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'], -6);

// Statistiques avancées avec fallback
if ($statistiques !== null) {
    try {
        $statsRecentes = $statistiques->getStatsRecentes();
        $progressionMensuelle = $statistiques->getProgressionMensuelle();
        $tauxConversion = $statistiques->getTauxConversion();
        $retentionUtilisateurs = $statistiques->getRetentionRate();
        $activiteParHeure = $statistiques->getActiviteParHeure();
        $statsParRegion = $statistiques->getStatsParRegion();
    } catch (Exception $e) {
        $statsRecentes = [];
        $progressionMensuelle = ['mois' => [], 'croissance' => []];
        $tauxConversion = 0;
        $retentionUtilisateurs = $engagementRate;
        $activiteParHeure = [45, 67, 89, 78, 92, 34, 12];
        $statsParRegion = $utilisateur->getStatsParVille();
    }
} else {
    $statsRecentes = [];
    $progressionMensuelle = ['mois' => [], 'croissance' => []];
    $tauxConversion = 0;
    $retentionUtilisateurs = $engagementRate;
    $activiteParHeure = [45, 67, 89, 78, 92, 34, 12];
    $statsParRegion = $utilisateur->getStatsParVille();
}
?>

<!-- Statistiques principales -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Utilisateurs</span>
            <div class="stat-icon"><i class="fas fa-users"></i></div>
        </div>
        <div class="stat-value"><?= number_format($totalUsers) ?></div>
        <div class="stat-change <?= $growthClass ?>">
            <i class="fas fa-<?= $growthIcon ?>"></i>
            <span><?= abs($growthPercentage) ?>% <?= $comparisonLabel ?></span>
        </div>
        <div class="stat-detail" style="font-size: 0.7rem; color: var(--text-muted);"><?= $totalCitoyens ?> citoyens • <?= $totalAgents ?> agents • <?= $totalAdmins ?> admins</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Nouvelles inscriptions</span>
            <div class="stat-icon"><i class="fas fa-user-plus"></i></div>
        </div>
        <div class="stat-value"><?= number_format($currentPeriod) ?></div>
        <div class="stat-change <?= $growthClass ?>">
            <i class="fas fa-<?= $growthIcon ?>"></i>
            <span><?= abs($growthPercentage) ?>% de croissance</span>
        </div>
        <div class="stat-detail" style="font-size: 0.7rem; color: var(--text-muted);">Période : <?= $periodLabel ?></div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Taux d'engagement</span>
            <div class="stat-icon"><i class="fas fa-chart-line"></i></div>
        </div>
        <div class="stat-value"><?= $engagementRate ?>%</div>
        <div class="stat-change <?= $engagementRate >= 50 ? 'positive' : 'neutral' ?>">
            <i class="fas fa-user-check"></i>
            <span><?= number_format($activeUsers) ?> actifs / <?= number_format($totalUsers) ?> total</span>
        </div>
        <div class="stat-detail" style="font-size: 0.7rem; color: var(--text-muted);">Utilisateurs avec statut actif</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Taux de rétention</span>
            <div class="stat-icon"><i class="fas fa-heart"></i></div>
        </div>
        <div class="stat-value"><?= number_format($retentionUtilisateurs) ?>%</div>
        <div class="stat-change positive">
            <i class="fas fa-arrow-trend-up"></i>
            <span>Utilisateurs actifs</span>
        </div>
        <div class="stat-detail" style="font-size: 0.7rem; color: var(--text-muted);">Rétention globale</div>
    </div>
</div>

<!-- Statistiques rapides -->
<div class="quick-stats">
    <div class="quick-stat-card">
        <div class="quick-stat-title">Complétion des profils</div>
        <div class="progress-value"><?= $completionRate ?>%</div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $completionRate ?>%"></div>
        </div>
        <div class="progress-label" style="font-size: 0.7rem; color: var(--text-muted);">Profils complets</div>
    </div>
    
    <div class="quick-stat-card">
        <div class="quick-stat-title">Répartition par type</div>
        <div style="margin: 10px 0;">
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.85rem;">Citoyens</span>
                <strong><?= number_format($totalCitoyensStandard) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                <span style="font-size: 0.85rem;">Professionnels</span>
                <strong><?= number_format($totalProfessionnels) ?></strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span style="font-size: 0.85rem;">Agents publics</span>
                <strong><?= number_format($totalAgentsPublics) ?></strong>
            </div>
        </div>
    </div>
    
    <div class="quick-stat-card">
        <div class="quick-stat-title">Performance</div>
        <div class="progress-value"><?= $tauxConversion ?>%</div>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $tauxConversion ?>%"></div>
        </div>
        <div class="progress-label" style="font-size: 0.7rem; color: var(--text-muted);">Taux de conversion</div>
    </div>
</div>

<!-- Graphiques -->
<div class="stats-grid" style="margin-bottom: 30px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-line" style="color: #2E7D32;"></i> Évolution des inscriptions</h3>
            <span style="font-size: 0.8rem; color: var(--text-muted);">6 derniers mois</span>
        </div>
        <canvas id="userChart" style="max-height: 300px; width: 100%;"></canvas>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-chart-pie" style="color: #2E7D32;"></i> Répartition par rôle</h3>
        </div>
        <canvas id="roleChart" style="max-height: 300px; width: 100%;"></canvas>
    </div>
</div>

<!-- Répartition géographique -->
<?php if (!empty($statsParRegion)): ?>
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-map-marker-alt" style="color: #2E7D32;"></i> Répartition par ville</h3>
    </div>
    <?php 
    $maxRegion = !empty($statsParRegion) ? max(array_column($statsParRegion, 'total')) : 1;
    foreach($statsParRegion as $region): 
    ?>
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 12px; border-bottom: 1px solid var(--border-color);">
        <div style="display: flex; align-items: center; gap: 10px; min-width: 150px;">
            <i class="fas fa-city" style="color: var(--primary);"></i>
            <span><?= htmlspecialchars($region['region']) ?></span>
        </div>
        <div style="flex: 1; height: 8px; background: var(--border-color); border-radius: 4px; overflow: hidden; margin: 0 15px;">
            <div style="height: 100%; width: <?= $maxRegion > 0 ? ($region['total'] / $maxRegion) * 100 : 0 ?>%; background: linear-gradient(90deg, #2E7D32, #4CAF50); border-radius: 4px;"></div>
        </div>
        <div style="min-width: 100px; text-align: right; font-weight: 700; color: var(--primary);"><?= number_format($region['total']) ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Derniers utilisateurs -->
<div class="card">
    <div class="card-header">
        <h3><i class="fas fa-clock"></i> Dernières inscriptions</h3>
        <?php if($isAdmin): ?>
            <a href="liste_utilisateurs.php" class="btn-add" style="padding: 5px 15px; font-size: 0.75rem;">Voir tous →</a>
        <?php endif; ?>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Utilisateur</th>
                    <th>Email</th>
                    <th>Type</th>
                    <th>Rôle</th>
                    <th>Ville</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach(array_slice($users, 0, 5) as $user): ?>
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar <?= $user['role'] ?>"><?= strtoupper(substr($user['prenom'], 0, 1)) ?></div>
                            <span><?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?></span>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?php
                        $typeLabels = [
                            'citoyen' => '<span class="badge badge-citoyen">Citoyen</span>',
                            'professionnel' => '<span class="badge badge-professionnel">Professionnel</span>',
                            'agent_public' => '<span class="badge badge-agent-public">Agent public</span>'
                        ];
                        echo $typeLabels[$user['type_compte']] ?? '<span class="badge badge-user">Standard</span>';
                        ?>
                    </td>
                    <td>
                        <?php
                        if($user['role'] == 'admin') echo '<span class="badge badge-admin">Admin</span>';
                        elseif($user['role'] == 'agent') echo '<span class="badge badge-agent">Agent</span>';
                        else echo '<span class="badge badge-user">Citoyen</span>';
                        ?>
                    </td>
                    <td><?= htmlspecialchars($user['ville']) ?></td>
                    <td><?= date('d/m/Y', strtotime($user['date_creation'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<footer class="footer">
    <p>&copy; 2026 innoGov - Plateforme administrative</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Graphique des inscriptions
const ctx = document.getElementById('userChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($months) ?>,
        datasets: [{
            label: 'Nouvelles inscriptions',
            data: <?= json_encode($userData) ?>,
            borderColor: '#2E7D32',
            backgroundColor: 'rgba(46, 125, 50, 0.05)',
            borderWidth: 3,
            pointBackgroundColor: '#4CAF50',
            pointBorderColor: '#fff',
            pointRadius: 5,
            pointHoverRadius: 7,
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false },
            tooltip: { callbacks: { label: function(context) { return `${context.parsed.y} inscriptions`; } } }
        },
        scales: { y: { beginAtZero: true, grid: { color: 'var(--border-color)' }, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
    }
});

// Graphique des rôles
const ctxPie = document.getElementById('roleChart').getContext('2d');
new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: ['Citoyens', 'Agents', 'Administrateurs'],
        datasets: [{ data: [<?= $totalCitoyens ?>, <?= $totalAgents ?>, <?= $totalAdmins ?>], backgroundColor: ['#2E7D32', '#4CAF50', '#81C784'], borderWidth: 0, hoverOffset: 10 }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { position: 'bottom' },
            tooltip: { callbacks: { label: function(context) { const total = <?= $totalUsers ?>; const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) : 0; return `${context.label}: ${context.parsed} (${percentage}%)`; } } }
        },
        cutout: '65%'
    }
});
</script>