<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new ShiftController();
$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'periode' => trim($_GET['periode'] ?? ''),
    'sort' => trim($_GET['sort'] ?? 'heure_asc'),
];
$shifts = $ctrl->getAllShifts($filters);
$notifications = $ctrl->getNotificationsShifts();
$stats = $ctrl->getStatistiquesShifts();

function shift_duree_label($minutes) {
    $minutes = max(0, (int) $minutes);
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;

    return $hours . 'h ' . $mins . 'min';
}

function shift_periode_label($heureDebut) {
    $heure = substr((string) $heureDebut, 0, 5);
    if ($heure < '12:00') {
        return theme_t('Matin', 'Matin');
    }
    if ($heure < '18:00') {
        return theme_t('Apres-midi', 'Apres-midi');
    }

    return theme_t('Soir', 'Soir');
}

function shift_export_pdf_url() {
    return theme_url('VIEW/backoffice/admin-shifts-export-pdf.php?' . http_build_query($_GET));
}

theme_render_start([
    'title' => theme_t('Gestion des shifts', 'Gestion des shifts'),
    'page_title' => theme_t('Gestion des shifts', 'Gestion des shifts'),
    'page_subtitle' => theme_t('Statistique, recherche, tri et suivi des shifts.', 'Statistique, recherche, tri et suivi des shifts.'),
    'nav_context' => 'shifts',
]);
?>
<div class="stats-launcher">
    <button type="button" class="btn btn--primary stats-toggle" id="shiftStatsToggleBtn" data-stats-toggle aria-expanded="false" aria-controls="shiftStatsPanel">
        <i class="fa-solid fa-chart-pie"></i>
        <?= htmlspecialchars(theme_t('Statistique', 'Statistique')) ?>
    </button>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="infographic-dashboard infographic-dashboard--shifts" id="shiftStatsPanel" data-advanced-stats hidden>
    <div class="infographic-header">
        <h2><?= htmlspecialchars(theme_t('Survey Statistics Infographic - Shifts', 'Survey Statistics Infographic - Shifts')) ?></h2>
    </div>

    <div class="info-quick-stats">
        <article class="info-stat-card">
            <i class="fa-solid fa-layer-group"></i>
            <div>
                <strong data-countup="<?= (int) ($stats['total'] ?? 0) ?>"><?= (int) ($stats['total'] ?? 0) ?></strong>
                <span><?= htmlspecialchars(theme_t('Total Shifts', 'Total Shifts')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-link"></i>
            <div>
                <strong data-countup="<?= (int) ($stats['emplois_lies'] ?? 0) ?>"><?= (int) ($stats['emplois_lies'] ?? 0) ?></strong>
                <span><?= htmlspecialchars(theme_t('Emplois lies', 'Emplois lies')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-business-time" style="color: #8b5cf6; background: #f5f3ff;"></i>
            <div>
                <strong data-countup="<?= (int) ($stats['duree_moyenne'] ?? 0) ?>"><?= (int) ($stats['duree_moyenne'] ?? 0) ?></strong>
                <span><?= htmlspecialchars(theme_t('Duree moyenne min', 'Duree moyenne min')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-stopwatch" style="color: #ec4899; background: #fdf2f8;"></i>
            <div>
                <strong data-countup="<?= (int) ($stats['duree_max'] ?? 0) ?>"><?= (int) ($stats['duree_max'] ?? 0) ?></strong>
                <span><?= htmlspecialchars(theme_t('Duree max min', 'Duree max min')) ?></span>
            </div>
        </article>
    </div>

    <div class="infographic-grid">
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Repartition par periode', 'Repartition par periode')) ?></h3>
                <span class="info-card__badge">Live</span>
            </div>
            <div class="info-chart-wrap">
                <canvas id="shiftChartPeriode"></canvas>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Distribution par duree', 'Distribution par duree')) ?></h3>
                <span class="info-card__badge">3 groupes</span>
            </div>
            <div class="info-chart-wrap">
                <canvas id="shiftChartDuree"></canvas>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Etat d utilisation', 'Etat d utilisation')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="shiftChartUsage"></canvas>
            </div>
        </div>

        <div class="info-card info-card--wide">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Top shifts par utilisation', 'Top shifts par utilisation')) ?></h3>
                <span class="info-card__badge"><?= count($stats['par_utilisation'] ?? []) ?> items</span>
            </div>
            <div class="info-chart-wrap">
                <canvas id="shiftChartTop"></canvas>
            </div>
        </div>

        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Depart par heure', 'Depart par heure')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="shiftChartHoraire"></canvas>
            </div>
        </div>

        <div class="info-card info-card--summary">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Resume flash', 'Resume flash')) ?></h3>
            </div>
            <div class="shift-summary-stack">
                <div class="notification notification--info">
                    <i class="fa-solid fa-clock"></i>
                    <div><strong><?= htmlspecialchars(theme_t('Moyenne', 'Moyenne')) ?>: <?= shift_duree_label((int) ($stats['duree_moyenne'] ?? 0)) ?></strong></div>
                </div>
                <div class="notification notification--warning">
                    <i class="fa-solid fa-bolt"></i>
                    <div><strong><?= htmlspecialchars(theme_t('Record', 'Record')) ?>: <?= shift_duree_label((int) ($stats['duree_max'] ?? 0)) ?></strong></div>
                </div>
                <div class="notification notification--success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div><strong><?= (int) ($stats['shifts_utilises'] ?? 0) ?> <?= htmlspecialchars(theme_t('shift(s) utilises', 'shift(s) utilises')) ?></strong></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var btn = document.getElementById('shiftStatsToggleBtn');
    var panel = document.getElementById('shiftStatsPanel');
    if (!btn || !panel || btn.dataset.ready === '1') return;
    btn.dataset.ready = '1';

    var charts = {};
    var statsData = <?= json_encode($stats, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}' ?>;

    function animateCounters() {
        panel.querySelectorAll('[data-countup]').forEach(function (el) {
            var target = parseInt(el.dataset.countup, 10) || 0;
            var count = 0;
            var increment = Math.max(1, target / 55);
            if (target === 0) {
                el.textContent = '0';
                return;
            }
            var timer = setInterval(function () {
                count += increment;
                if (count >= target) {
                    el.textContent = target;
                    clearInterval(timer);
                } else {
                    el.textContent = Math.floor(count);
                }
            }, 16);
        });
    }

    function initCharts() {
        if (Object.keys(charts).length > 0 || typeof Chart === 'undefined') return;

        var palette = ['#38bdf8', '#f59e0b', '#10b981', '#8b5cf6', '#ec4899', '#ef4444'];
        var emptyOptions = { responsive: true, maintainAspectRatio: false };

        charts.periode = new Chart(document.getElementById('shiftChartPeriode'), {
            type: 'doughnut',
            data: {
                labels: (statsData.par_periode || []).map(function (i) { return i.label; }),
                datasets: [{ data: (statsData.par_periode || []).map(function (i) { return i.value; }), backgroundColor: ['#f59e0b', '#10b981', '#3b82f6'], borderWidth: 0, hoverOffset: 10 }]
            },
            options: Object.assign({}, emptyOptions, { plugins: { legend: { position: 'bottom' } }, cutout: '68%' })
        });

        charts.duree = new Chart(document.getElementById('shiftChartDuree'), {
            type: 'pie',
            data: {
                labels: (statsData.par_duree || []).map(function (i) { return i.label; }),
                datasets: [{ data: (statsData.par_duree || []).map(function (i) { return i.value; }), backgroundColor: ['#38bdf8', '#8b5cf6', '#ec4899'], borderWidth: 0 }]
            },
            options: Object.assign({}, emptyOptions, { plugins: { legend: { position: 'bottom' } } })
        });

        charts.usage = new Chart(document.getElementById('shiftChartUsage'), {
            type: 'doughnut',
            data: {
                labels: ['Utilises', 'Non utilises'],
                datasets: [{ data: [statsData.shifts_utilises || 0, statsData.shifts_non_utilises || 0], backgroundColor: ['#10b981', '#ef4444'], borderWidth: 0 }]
            },
            options: Object.assign({}, emptyOptions, { plugins: { legend: { position: 'bottom' } }, cutout: '62%' })
        });

        charts.top = new Chart(document.getElementById('shiftChartTop'), {
            type: 'bar',
            data: {
                labels: (statsData.par_utilisation || []).map(function (i) { return i.label; }),
                datasets: [{ label: 'Emplois', data: (statsData.par_utilisation || []).map(function (i) { return i.value; }), backgroundColor: palette, borderRadius: 8 }]
            },
            options: Object.assign({}, emptyOptions, {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, grid: { color: '#f1f5f9' } }, y: { grid: { display: false } } }
            })
        });

        charts.horaire = new Chart(document.getElementById('shiftChartHoraire'), {
            type: 'line',
            data: {
                labels: (statsData.par_horaire || []).map(function (i) { return i.label; }),
                datasets: [{ label: 'Shifts', data: (statsData.par_horaire || []).map(function (i) { return i.value; }), borderColor: '#38bdf8', backgroundColor: 'rgba(56, 189, 248, 0.12)', fill: true, tension: 0.35, pointRadius: 5, pointBackgroundColor: '#38bdf8' }]
            },
            options: Object.assign({}, emptyOptions, { scales: { y: { beginAtZero: true }, x: { grid: { display: false } } } })
        });
    }

    btn.addEventListener('click', function () {
        var isHidden = panel.hasAttribute('hidden');
        if (isHidden) {
            panel.removeAttribute('hidden');
            btn.setAttribute('aria-expanded', 'true');
            initCharts();
            animateCounters();
            setTimeout(function () { panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' }); }, 50);
            return;
        }

        panel.setAttribute('hidden', '');
        btn.setAttribute('aria-expanded', 'false');
    });
})();
</script>

<div class="table-panel utility-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Simple', 'Simple')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Recherche, tri et notifications', 'Recherche, tri et notifications')) ?></h2>
        </div>
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-lister.php')) ?>" class="btn btn--ghost">
            <i class="fa-solid fa-rotate-left"></i>
            <?= htmlspecialchars(theme_t('Reinitialiser', 'Reinitialiser')) ?>
        </a>
    </div>

    <form method="GET" class="filter-grid">
        <div class="form-group">
            <label for="search"><?= htmlspecialchars(theme_t('Recherche', 'Recherche')) ?></label>
            <input id="search" name="search" type="search" value="<?= htmlspecialchars($filters['search']) ?>" placeholder="<?= htmlspecialchars(theme_t('Nom du shift...', 'Nom du shift...')) ?>">
        </div>
        <div class="form-group">
            <label for="periode"><?= htmlspecialchars(theme_t('Periode', 'Periode')) ?></label>
            <select id="periode" name="periode">
                <option value=""><?= htmlspecialchars(theme_t('Toutes les periodes', 'Toutes les periodes')) ?></option>
                <option value="matin" <?= $filters['periode'] === 'matin' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Matin', 'Matin')) ?></option>
                <option value="apres_midi" <?= $filters['periode'] === 'apres_midi' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Apres-midi', 'Apres-midi')) ?></option>
                <option value="soir" <?= $filters['periode'] === 'soir' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Soir', 'Soir')) ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="sort"><?= htmlspecialchars(theme_t('Tri', 'Tri')) ?></label>
            <select id="sort" name="sort">
                <option value="heure_asc" <?= $filters['sort'] === 'heure_asc' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Heure debut croissante', 'Heure debut croissante')) ?></option>
                <option value="heure_desc" <?= $filters['sort'] === 'heure_desc' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Heure debut decroissante', 'Heure debut decroissante')) ?></option>
                <option value="nom" <?= $filters['sort'] === 'nom' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Nom A-Z', 'Nom A-Z')) ?></option>
                <option value="duree_desc" <?= $filters['sort'] === 'duree_desc' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Duree longue', 'Duree longue')) ?></option>
                <option value="usage_desc" <?= $filters['sort'] === 'usage_desc' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Plus utilise', 'Plus utilise')) ?></option>
            </select>
        </div>
        <div class="form-actions filter-actions">
            <button type="submit" class="btn btn--primary">
                <i class="fa-solid fa-magnifying-glass"></i>
                <?= htmlspecialchars(theme_t('Appliquer', 'Appliquer')) ?>
            </button>
        </div>
    </form>

    <div class="notification-list">
        <?php if (!empty($notifications)): ?>
            <?php foreach ($notifications as $notification): ?>
                <div class="notification notification--<?= htmlspecialchars($notification['type']) ?>">
                    <i class="fa-solid <?= htmlspecialchars($notification['icon']) ?>"></i>
                    <div>
                        <strong><?= htmlspecialchars($notification['title']) ?></strong>
                        <p><?= htmlspecialchars($notification['message']) ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="notification notification--success">
                <i class="fa-solid fa-circle-check"></i>
                <div>
                    <strong><?= htmlspecialchars(theme_t('Tout est clair', 'Tout est clair')) ?></strong>
                    <p><?= htmlspecialchars(theme_t('Aucune alerte importante pour les shifts.', 'Aucune alerte importante pour les shifts.')) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="table-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Backoffice', 'Backoffice')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Liste des shifts', 'Liste des shifts')) ?></h2>
            <p class="muted"><?= count($shifts) ?> <?= htmlspecialchars(theme_t('resultat(s) affiche(s)', 'resultat(s) affiche(s)')) ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(shift_export_pdf_url()) ?>" class="btn btn--secondary">
                <i class="fa-solid fa-file-pdf"></i>
                <?= htmlspecialchars(theme_t('Exporter PDF', 'Exporter PDF')) ?>
            </a>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-ajouter.php')) ?>" class="btn btn--primary">
                <i class="fa-solid fa-plus"></i>
                <?= htmlspecialchars(theme_t('Ajouter un shift', 'Ajouter un shift')) ?>
            </a>
        </div>
    </div>

    <?php if (!empty($shifts)): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars(theme_t('Nom', 'Nom')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Periode', 'Periode')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Heure debut', 'Heure debut')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Heure fin', 'Heure fin')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Duree', 'Duree')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Utilisation', 'Utilisation')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Actions', 'Actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shifts as $shift): ?>
                        <?php
                        $dureeMinutes = (int) ($shift['duree_minutes'] ?? 0);
                        $emploisCount = (int) ($shift['emplois_count'] ?? 0);
                        $emploisPlanifies = (int) ($shift['emplois_planifies'] ?? 0);
                        ?>
                        <tr>
                            <td><?= (int) $shift['id_shift'] ?></td>
                            <td><strong><?= htmlspecialchars($shift['nom_shift']) ?></strong></td>
                            <td><span class="badge"><?= htmlspecialchars(shift_periode_label($shift['heure_debut'])) ?></span></td>
                            <td><?= htmlspecialchars(substr($shift['heure_debut'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars(substr($shift['heure_fin'], 0, 5)) ?></td>
                            <td><span class="badge"><?= htmlspecialchars(shift_duree_label($dureeMinutes)) ?></span></td>
                            <td>
                                <strong><?= $emploisCount ?></strong> <?= htmlspecialchars(theme_t('emploi(s)', 'emploi(s)')) ?><br>
                                <span class="muted"><?= $emploisPlanifies ?> <?= htmlspecialchars(theme_t('planifie(s)', 'planifie(s)')) ?></span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-modifier.php?id=' . $shift['id_shift'])) ?>" class="btn btn--warning">
                                        <i class="fa-solid fa-pen"></i>
                                        <?= htmlspecialchars(theme_t('Modifier', 'Modifier')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-supprimer.php?id=' . $shift['id_shift'])) ?>" class="btn btn--danger" onclick="return confirm('<?= htmlspecialchars(theme_t('Supprimer ce shift ?', 'Supprimer ce shift ?')) ?>');">
                                        <i class="fa-solid fa-trash"></i>
                                        <?= htmlspecialchars(theme_t('Supprimer', 'Supprimer')) ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3><?= htmlspecialchars(theme_t('Aucun shift trouve', 'Aucun shift trouve')) ?></h3>
            <p><?= htmlspecialchars(theme_t('Ajustez les filtres ou ajoutez un shift pour commencer.', 'Ajustez les filtres ou ajoutez un shift pour commencer.')) ?></p>
        </div>
    <?php endif; ?>
</div>
<?php theme_render_end(); ?>
