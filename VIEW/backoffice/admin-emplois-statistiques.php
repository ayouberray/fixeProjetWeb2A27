<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$stats = $ctrl->getStatistiquesEmplois();

function emploi_stats_duree_label($minutes) {
    $minutes = max(0, (int) $minutes);
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return $hours . 'h ' . $mins . 'min';
}

theme_render_start([
    'title' => theme_t('Statistique des emplois', 'إحصائيات الوظائف'),
    'page_title' => theme_t('Statistique des emplois', 'إحصائيات الوظائف'),
    'page_subtitle' => theme_t('Analyse visuelle et infographic du planning.', 'تحليل مرئي وجرافيكي لجدول العمل.'),
    'nav_context' => 'emplois',
    'back_href' => theme_url('VIEW/backoffice/admin-emplois-lister.php'),
    'back_label' => theme_t('Retour', 'رجوع'),
]);
?>
<!-- Inclure Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="infographic-dashboard" id="emploiStatsDashboard">
    <div class="infographic-header">
        <h2><?= htmlspecialchars(theme_t('Analyse des Emplois - Infographie', 'تحليل الوظائف - إنفوجرافيك')) ?></h2>
    </div>

    <div class="info-quick-stats">
        <article class="info-stat-card">
            <i class="fa-solid fa-calendar-check"></i>
            <div>
                <strong data-countup="<?= (int) $stats['total'] ?>"><?= (int) $stats['total'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Total Emplois', 'إجمالي الوظائف')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-hourglass-half" style="color: #f59e0b; background: #fffbeb;"></i>
            <div>
                <strong data-countup="<?= (int) $stats['planifies'] ?>"><?= (int) $stats['planifies'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Planifiés', 'مخطط لها')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-business-time" style="color: #8b5cf6; background: #f5f3ff;"></i>
            <div>
                <strong data-countup="<?= (int) $stats['duree_moyenne'] ?>"><?= (int) $stats['duree_moyenne'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Durée Moyenne (min)', 'متوسط المدة')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-users" style="color: #10b981; background: #ecfdf5;"></i>
            <div>
                <strong data-countup="<?= (int) $stats['agents'] ?>"><?= (int) $stats['agents'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Agents Actifs', 'الوكلاء النشطون')) ?></span>
            </div>
        </article>
    </div>

    <div class="infographic-grid">
        <!-- Chart 1: Répartition par Statut -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Répartition par Statut', 'توزيع حسب الحالة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartStatut"></canvas>
            </div>
        </div>

        <!-- Chart 2: Répartition par Période -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Répartition par Période', 'التوزيع حسب الفترة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartPeriode"></canvas>
            </div>
        </div>

        <!-- Chart 3: Distribution par Service -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Top Services', 'أفضل الخدمات')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartService"></canvas>
            </div>
        </div>

        <!-- Chart 4: Tendance Mensuelle (Wide) -->
        <div class="info-card info-card--wide">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Évolution Mensuelle', 'التطور الشهري')) ?></h3>
                <span class="info-card__badge">6 Mois</span>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartMois"></canvas>
            </div>
        </div>

        <!-- Chart 5: Distribution Horaire -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Affluence par Heure', 'توزيع حسب الساعة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartHoraire"></canvas>
            </div>
        </div>

        <!-- Résumé Flash -->
        <div class="info-card info-card--summary">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Résumé Flash', 'ملخص سريع')) ?></h3>
            </div>
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 15px;">
                <div class="notification notification--info" style="margin:0;">
                    <i class="fa-solid fa-clock"></i>
                    <div>
                        <strong>Moyenne: <?= emploi_stats_duree_label($stats['duree_moyenne']) ?></strong>
                    </div>
                </div>
                <div class="notification notification--warning" style="margin:0;">
                    <i class="fa-solid fa-bolt"></i>
                    <div>
                        <strong>Record: <?= emploi_stats_duree_label($stats['duree_max']) ?></strong>
                    </div>
                </div>
                <div class="notification notification--success" style="margin:0;">
                    <i class="fa-solid fa-calendar-day"></i>
                    <div>
                        <strong>Aujourd'hui: <?= (int) $stats['aujourdhui'] ?> emplois</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var statsData = <?= json_encode($stats) ?>;
    var commonOptions = { responsive: true, maintainAspectRatio: false };

    // --- Chart Statut ---
    new Chart(document.getElementById('chartStatut'), {
        type: 'doughnut',
        data: {
            labels: ['Planifié', 'Terminé', 'Annulé'],
            datasets: [{
                data: [statsData.planifies || 0, statsData.termines || 0, statsData.annules || 0],
                backgroundColor: ['#f59e0b', '#10b981', '#ef4444'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: Object.assign({}, commonOptions, {
            plugins: { legend: { position: 'bottom' } },
            cutout: '70%'
        })
    });

    // --- Chart Période ---
    new Chart(document.getElementById('chartPeriode'), {
        type: 'pie',
        data: {
            labels: statsData.par_periode.map(i => i.label),
            datasets: [{
                data: statsData.par_periode.map(i => i.value),
                backgroundColor: ['#38bdf8', '#8b5cf6', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: Object.assign({}, commonOptions, {
            plugins: { legend: { position: 'bottom' } }
        })
    });

    // --- Chart Service ---
    new Chart(document.getElementById('chartService'), {
        type: 'bar',
        data: {
            labels: statsData.par_service.map(i => i.label),
            datasets: [{
                label: 'Emplois',
                data: statsData.par_service.map(i => i.value),
                backgroundColor: '#38bdf8',
                borderRadius: 6
            }]
        },
        options: Object.assign({}, commonOptions, {
            indexAxis: 'y',
            plugins: { legend: { display: false } }
        })
    });

    // --- Chart Mois ---
    new Chart(document.getElementById('chartMois'), {
        type: 'line',
        data: {
            labels: statsData.par_mois.map(i => i.label),
            datasets: [{
                label: 'Volume mensuel',
                data: statsData.par_mois.map(i => i.value),
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: commonOptions
    });

    // --- Chart Horaire ---
    new Chart(document.getElementById('chartHoraire'), {
        type: 'line',
        data: {
            labels: statsData.par_horaire.map(i => i.label),
            datasets: [{
                label: 'Emplois',
                data: statsData.par_horaire.map(i => i.value),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: commonOptions
    });

    // --- CountUp ---
    document.querySelectorAll('[data-countup]').forEach(function (el) {
        var target = parseInt(el.dataset.countup, 10);
        var count = 0;
        var increment = target / 60;
        if (target === 0) { el.innerText = '0'; return; }
        var timer = setInterval(function () {
            count += increment;
            if (count >= target) {
                el.innerText = target;
                clearInterval(timer);
            } else {
                el.innerText = Math.floor(count);
            }
        }, 16);
    });
})();
</script>
<?php theme_render_end(); ?>
