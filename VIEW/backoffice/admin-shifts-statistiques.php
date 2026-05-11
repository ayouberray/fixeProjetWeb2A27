<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new ShiftController();
$stats = $ctrl->getStatistiquesShifts();

function shift_stats_duree_label($minutes) {
    $minutes = max(0, (int) $minutes);
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return $hours . 'h ' . $mins . 'min';
}

theme_render_start([
    'title' => theme_t('Statistique shifts', 'Statistique shifts'),
    'page_title' => theme_t('Statistique shifts', 'Statistique shifts'),
    'page_subtitle' => theme_t('Analyse visuelle des horaires.', 'Analyse visuelle des horaires.'),
    'nav_context' => 'shifts',
    'back_href' => theme_url('VIEW/backoffice/admin-shifts-lister.php'),
    'back_label' => theme_t('Retour', 'Retour'),
]);
?>
<!-- Inclure Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="infographic-dashboard" id="shiftStatsDashboard">
    <div class="infographic-header">
        <h2><?= htmlspecialchars(theme_t('Analyse des Shifts - Infographie', 'تحليل المناوبات - إنفوجرافيك')) ?></h2>
    </div>

    <div class="info-quick-stats">
        <article class="info-stat-card">
            <i class="fa-solid fa-layer-group"></i>
            <div>
                <strong data-countup="<?= (int) $stats['total'] ?>"><?= (int) $stats['total'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Total Shifts', 'إجمالي المناوبات')) ?></span>
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
            <i class="fa-solid fa-stopwatch" style="color: #ec4899; background: #fdf2f8;"></i>
            <div>
                <strong data-countup="<?= (int) $stats['duree_max'] ?>"><?= (int) $stats['duree_max'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Durée Max (min)', 'أقصى مدة')) ?></span>
            </div>
        </article>
    </div>

    <div class="infographic-grid">
        <!-- Chart 1: Répartition par Période -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Répartition par Période', 'التوزيع حسب الفترة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartPeriode"></canvas>
            </div>
        </div>

        <!-- Chart 2: Distribution par Durée -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Distribution par Durée', 'التوزيع حسب المدة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartDuree"></canvas>
            </div>
        </div>

        <!-- Résumé textuel -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Résumé Flash', 'ملخص سريع')) ?></h3>
            </div>
            <div style="flex: 1; display: flex; flex-direction: column; justify-content: center; gap: 15px;">
                <div class="notification notification--info" style="margin:0;">
                    <i class="fa-solid fa-clock"></i>
                    <div>
                        <strong>Moyenne: <?= shift_stats_duree_label($stats['duree_moyenne']) ?></strong>
                    </div>
                </div>
                <div class="notification notification--warning" style="margin:0;">
                    <i class="fa-solid fa-bolt"></i>
                    <div>
                        <strong>Record: <?= shift_stats_duree_label($stats['duree_max']) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    var statsData = <?= json_encode($stats) ?>;
    
    // --- Chart Période ---
    new Chart(document.getElementById('chartPeriode'), {
        type: 'doughnut',
        data: {
            labels: statsData.par_periode.map(i => i.label),
            datasets: [{
                data: statsData.par_periode.map(i => i.value),
                backgroundColor: ['#f59e0b', '#10b981', '#3b82f6'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } },
            cutout: '65%'
        }
    });

    // --- Chart Durée ---
    new Chart(document.getElementById('chartDuree'), {
        type: 'pie',
        data: {
            labels: statsData.par_duree.map(i => i.label),
            datasets: [{
                data: statsData.par_duree.map(i => i.value),
                backgroundColor: ['#38bdf8', '#8b5cf6', '#ec4899'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // --- CountUp ---
    document.querySelectorAll('[data-countup]').forEach(function (el) {
        var target = parseInt(el.dataset.countup, 10);
        var count = 0;
        var duration = 1000;
        var increment = target / (duration / 16);
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
