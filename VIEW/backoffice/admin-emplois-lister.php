<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'statut' => trim($_GET['statut'] ?? ''),
    'id_service' => trim($_GET['id_service'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
    'sort' => trim($_GET['sort'] ?? 'date_desc'),
];
$emplois = $ctrl->getAllEmplois($filters);
$stats = $ctrl->getStatistiquesEmplois();
$notifications = $ctrl->getNotificationsEmplois();
$services = $ctrl->getServices();
$calendarMonth = preg_match('/^\d{4}-\d{2}$/', $_GET['month'] ?? '') ? $_GET['month'] : date('Y-m');
$calendarItems = $ctrl->getCalendrierEmplois($calendarMonth);
$statsTotal = max(1, (int) ($stats['total'] ?? 0));

function emploi_badge_class($statut) {
    if ($statut === 'termine') {
        return 'badge--success';
    }
    if ($statut === 'annule') {
        return 'badge--danger';
    }

    return 'badge--warning';
}

function emploi_status_label($statut) {
    if ($statut === 'termine') {
        return theme_t('Termine', 'Termine');
    }
    if ($statut === 'annule') {
        return theme_t('Annule', 'Annule');
    }

    return theme_t('Planifie', 'Planifie');
}

function emploi_query_url($params = []) {
    return '?' . http_build_query(array_merge($_GET, $params));
}

function emploi_export_pdf_url() {
    return theme_url('VIEW/backoffice/admin-emplois-export-pdf.php?' . http_build_query($_GET));
}

function emploi_message($emploi) {
    $agent = trim(($emploi['agent_nom'] ?? '') . ' ' . ($emploi['agent_prenom'] ?? ''));
    $date = date('d/m/Y', strtotime($emploi['date_travail']));
    $heureDebut = substr($emploi['heure_debut'] ?? '00:00', 0, 5);
    $heureFin = substr($emploi['heure_fin'] ?? '00:00', 0, 5);

    return "Bonjour " . ($agent !== '' ? $agent : 'agent') . ", vous etes planifie le $date de $heureDebut a $heureFin pour le service " . ($emploi['nom_service'] ?? 'N/A') . ".";
}

function emploi_calendar_url($emploi) {
    $date = date('Ymd', strtotime($emploi['date_travail']));
    $end = date('Ymd', strtotime($emploi['date_travail'] . ' +1 day'));
    $title = 'Emploi - ' . ($emploi['nom_shift'] ?? 'Shift');
    $details = emploi_message($emploi);

    return 'https://calendar.google.com/calendar/render?action=TEMPLATE&text=' . rawurlencode($title) . '&dates=' . $date . '/' . $end . '&details=' . rawurlencode($details);
}

function emploi_absolute_url($path) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') === '443');
    $scheme = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $hostOnly = preg_replace('/:\d+$/', '', $host);
    $port = '';
    if (preg_match('/:(\d+)$/', $host, $matches) && !in_array($matches[1], ['80', '443'], true)) {
        $port = ':' . $matches[1];
    }

    if (in_array($hostOnly, ['localhost', '127.0.0.1', '::1'], true)) {
        $lanHost = emploi_lan_host();
        if ($lanHost !== null) {
            $host = $lanHost . $port;
        }
    }

    return $scheme . '://' . $host . $path;
}

function emploi_lan_host() {
    $output = function_exists('shell_exec') ? shell_exec('ipconfig') : null;
    if (is_string($output) && preg_match_all('/IPv4[^\r\n:]*[.: ]+([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)/i', $output, $matches)) {
        $ips = (array) $matches[1];
        // Chercher une IP qui ressemble à un vrai LAN (pas virtuel comme VMware 92.x ou 42.x)
        foreach ($ips as $ip) {
            if (preg_match('/^(192\.168\.[01]\.|172\.16\.|10\.)/', $ip)) {
                return $ip;
            }
        }
        // Sinon, prendre la première valide qui n'est pas localhost ou APIPA
        foreach ($ips as $ip) {
            if (strpos($ip, '169.254.') !== 0 && $ip !== '127.0.0.1') {
                return $ip;
            }
        }
    }

    $serverAddr = $_SERVER['SERVER_ADDR'] ?? '';
    if (filter_var($serverAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && strpos($serverAddr, '127.') !== 0) {
        return $serverAddr;
    }

    return null;
}

function emploi_qr_page_url($emploi) {
    return emploi_absolute_url(theme_url('VIEW/frontoffice/emploi-qr.php?t=' . rawurlencode($emploi['qr_token'] ?? '')));
}

function emploi_qr_image_url($emploi, $size = 140) {
    $url = emploi_qr_page_url($emploi);

    return 'https://api.qrserver.com/v1/create-qr-code/?size=' . (int) $size . 'x' . (int) $size . '&data=' . rawurlencode($url);
}

theme_render_start([
    'title' => theme_t('Gestion des emplois', 'Gestion des emplois'),
    'page_title' => theme_t('Gestion des emplois', 'Gestion des emplois'),
    'page_subtitle' => theme_t('Statistique, recherche, tri, notifications et outils pour garder le planning bien organise.', 'Statistique, recherche, tri, notifications et outils.'),
    'nav_context' => 'emplois',
]);
?>
<div class="stats-launcher">
    <button type="button" class="btn btn--primary stats-toggle" id="statsToggleBtn" data-stats-toggle aria-expanded="false" aria-controls="advancedStatsPanel">
        <i class="fa-solid fa-chart-pie"></i>
        <?= htmlspecialchars(theme_t('Statistique', 'Statistique')) ?>
    </button>
</div>

<!-- Inclure Chart.js via CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="infographic-dashboard infographic-dashboard--emplois" id="advancedStatsPanel" data-advanced-stats hidden>
    <div class="infographic-header">
        <h2><?= htmlspecialchars(theme_t('Tableau de Bord Infographique - Emplois', 'لوحة التحكم الجرافيكية - الوظائف')) ?></h2>
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
            <i class="fa-solid fa-hourglass-half"></i>
            <div>
                <strong data-countup="<?= (int) $stats['planifies'] ?>"><?= (int) $stats['planifies'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Planifiés', 'مخطط لها')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-circle-check" style="color: #10b981; background: #ecfdf5;"></i>
            <div>
                <strong data-countup="<?= (int) $stats['termines'] ?>"><?= (int) $stats['termines'] ?></strong>
                <span><?= htmlspecialchars(theme_t('Terminés', 'مكتملة')) ?></span>
            </div>
        </article>
        <article class="info-stat-card">
            <i class="fa-solid fa-users" style="color: #8b5cf6; background: #f5f3ff;"></i>
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
                <span class="info-card__badge">Live</span>
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

        <!-- Chart 3: Top Services -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Top Services', 'أفضل الخدمات')) ?></h3>
                <span class="info-card__badge"><?= count($stats['par_service']) ?> items</span>
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

        <!-- Chart 5: Distribution par Shift -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Distribution par Shift', 'التوزيع حسب المناوبة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartShift"></canvas>
            </div>
        </div>

        <!-- Chart 6: Affluence par Heure -->
        <div class="info-card">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Affluence par Heure', 'توزيع حسب الساعة')) ?></h3>
            </div>
            <div class="info-chart-wrap">
                <canvas id="chartHoraire"></canvas>
            </div>
        </div>
        
        <div class="info-card info-card--summary">
            <div class="info-card__head">
                <h3><?= htmlspecialchars(theme_t('Résumé Flash', 'ملخص سريع')) ?></h3>
            </div>
            <div class="shift-summary-stack">
                <div class="notification notification--info">
                    <i class="fa-solid fa-clock"></i>
                    <div><strong><?= htmlspecialchars(theme_t('Aujourd\'hui', 'اليوم')) ?>: <?= (int) $stats['aujourdhui'] ?> emplois</strong></div>
                </div>
                <div class="notification notification--warning">
                    <i class="fa-solid fa-bolt"></i>
                    <div><strong>Durée Moyenne: <?= (int) $stats['duree_moyenne'] ?> min</strong></div>
                </div>
                <div class="notification notification--success">
                    <i class="fa-solid fa-circle-check"></i>
                    <div><strong>Semaine: <?= (int) $stats['semaine'] ?> planifiés</strong></div>
                </div>
            </div>
            <div style="margin-top: 15px; text-align: center;">
                <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-statistiques.php')) ?>" class="btn btn--ghost btn--sm">
                    <?= htmlspecialchars(theme_t('Voir analyse détaillée', 'عرض التحليل التفصيلي')) ?>
                    <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    var btn = document.getElementById('statsToggleBtn');
    var panel = document.getElementById('advancedStatsPanel');
    if (!btn || !panel || btn.dataset.ready === '1') return;
    btn.dataset.ready = '1';

    var charts = {};
    var statsData = <?= json_encode($stats, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?: '{}' ?>;

    function animateCounters() {
        panel.querySelectorAll('[data-countup]').forEach(function (el) {
            var target = parseInt(el.dataset.countup, 10) || 0;
            var count = 0;
            var increment = Math.max(1, target / 50);
            if (target === 0) { el.textContent = '0'; return; }
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

        var commonOptions = { responsive: true, maintainAspectRatio: false };

        // --- Chart Statut ---
        charts.statut = new Chart(document.getElementById('chartStatut'), {
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
            options: Object.assign({}, commonOptions, { plugins: { legend: { position: 'bottom' } }, cutout: '70%' })
        });

        // --- Chart Période ---
        charts.periode = new Chart(document.getElementById('chartPeriode'), {
            type: 'pie',
            data: {
                labels: (statsData.par_periode || []).map(i => i.label),
                datasets: [{
                    data: (statsData.par_periode || []).map(i => i.value),
                    backgroundColor: ['#38bdf8', '#8b5cf6', '#ec4899'],
                    borderWidth: 0
                }]
            },
            options: Object.assign({}, commonOptions, { plugins: { legend: { position: 'bottom' } } })
        });

        // --- Chart Service ---
        charts.service = new Chart(document.getElementById('chartService'), {
            type: 'pie',
            data: {
                labels: (statsData.par_service || []).map(i => i.label),
                datasets: [{
                    data: (statsData.par_service || []).map(i => i.value),
                    backgroundColor: ['#38bdf8', '#8b5cf6', '#ec4899', '#10b981', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: Object.assign({}, commonOptions, { plugins: { legend: { position: 'bottom' } } })
        });

        // --- Chart Shift ---
        charts.shift = new Chart(document.getElementById('chartShift'), {
            type: 'bar',
            data: {
                labels: (statsData.par_shift || []).map(i => i.label),
                datasets: [{
                    label: 'Emplois',
                    data: (statsData.par_shift || []).map(i => i.value),
                    backgroundColor: '#8b5cf6',
                    borderRadius: 6
                }]
            },
            options: Object.assign({}, commonOptions, {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: { x: { grid: { display: false } }, y: { grid: { display: false } } }
            })
        });

        // --- Chart Mois ---
        charts.mois = new Chart(document.getElementById('chartMois'), {
            type: 'line',
            data: {
                labels: (statsData.par_mois || []).map(i => i.label),
                datasets: [{
                    label: 'Emplois',
                    data: (statsData.par_mois || []).map(i => i.value),
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                    fill: true,
                    tension: 0.4,
                    pointRadius: 5
                }]
            },
            options: commonOptions
        });

        // --- Chart Horaire ---
        charts.horaire = new Chart(document.getElementById('chartHoraire'), {
            type: 'line',
            data: {
                labels: (statsData.par_horaire || []).map(i => i.label),
                datasets: [{
                    label: 'Affluence',
                    data: (statsData.par_horaire || []).map(i => i.value),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: commonOptions
        });
    }

    btn.addEventListener('click', function () {
        var isHidden = panel.hasAttribute('hidden');
        if (isHidden) {
            panel.removeAttribute('hidden');
            btn.setAttribute('aria-expanded', 'true');
            initCharts();
            animateCounters();
            setTimeout(function() {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }, 50);
        } else {
            panel.setAttribute('hidden', '');
            btn.setAttribute('aria-expanded', 'false');
        }
    });
})();
</script>

<div class="table-panel utility-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Simple', 'Simple')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Recherche, tri et notifications', 'Recherche, tri et notifications')) ?></h2>
        </div>
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php')) ?>" class="btn btn--ghost">
            <i class="fa-solid fa-rotate-left"></i>
            <?= htmlspecialchars(theme_t('Reinitialiser', 'Reinitialiser')) ?>
        </a>
    </div>

    <form method="GET" class="filter-grid">
        <div class="form-group">
            <label for="search"><?= htmlspecialchars(theme_t('Recherche', 'Recherche')) ?></label>
            <input id="search" name="search" type="search" value="<?= htmlspecialchars($filters['search']) ?>" placeholder="<?= htmlspecialchars(theme_t('Agent, service, shift, date...', 'Agent, service, shift, date...')) ?>">
        </div>
        <div class="form-group">
            <label for="statut"><?= htmlspecialchars(theme_t('Statut', 'Statut')) ?></label>
            <select id="statut" name="statut">
                <option value=""><?= htmlspecialchars(theme_t('Tous les statuts', 'Tous les statuts')) ?></option>
                <?php foreach (['planifie', 'termine', 'annule'] as $statut): ?>
                    <option value="<?= htmlspecialchars($statut) ?>" <?= $filters['statut'] === $statut ? 'selected' : '' ?>><?= htmlspecialchars(emploi_status_label($statut)) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="id_service"><?= htmlspecialchars(theme_t('Service', 'Service')) ?></label>
            <select id="id_service" name="id_service">
                <option value=""><?= htmlspecialchars(theme_t('Tous les services', 'Tous les services')) ?></option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= (int) $service['id_service'] ?>" <?= (int) $filters['id_service'] === (int) $service['id_service'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($service['nom_service']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="sort"><?= htmlspecialchars(theme_t('Tri', 'Tri')) ?></label>
            <select id="sort" name="sort">
                <option value="date_desc" <?= $filters['sort'] === 'date_desc' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Date recente', 'Date recente')) ?></option>
                <option value="date_asc" <?= $filters['sort'] === 'date_asc' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Date ancienne', 'Date ancienne')) ?></option>
                <option value="agent" <?= $filters['sort'] === 'agent' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Agent A-Z', 'Agent A-Z')) ?></option>
                <option value="service" <?= $filters['sort'] === 'service' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Service A-Z', 'Service A-Z')) ?></option>
                <option value="shift" <?= $filters['sort'] === 'shift' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Shift horaire', 'Shift horaire')) ?></option>
                <option value="statut" <?= $filters['sort'] === 'statut' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Statut', 'Statut')) ?></option>
            </select>
        </div>
        <div class="form-group">
            <label for="date_from"><?= htmlspecialchars(theme_t('Du', 'Du')) ?></label>
            <input id="date_from" name="date_from" type="date" value="<?= htmlspecialchars($filters['date_from']) ?>">
        </div>
        <div class="form-group">
            <label for="date_to"><?= htmlspecialchars(theme_t('Au', 'Au')) ?></label>
            <input id="date_to" name="date_to" type="date" value="<?= htmlspecialchars($filters['date_to']) ?>">
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
                    <p><?= htmlspecialchars(theme_t('Aucune alerte importante pour le planning.', 'Aucune alerte importante pour le planning.')) ?></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="table-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Backoffice', 'Backoffice')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Liste des emplois', 'Liste des emplois')) ?></h2>
            <p class="muted"><?= count($emplois) ?> <?= htmlspecialchars(theme_t('resultat(s) affiche(s)', 'resultat(s) affiche(s)')) ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(emploi_export_pdf_url()) ?>" class="btn btn--secondary">
                <i class="fa-solid fa-file-pdf"></i>
                <?= htmlspecialchars(theme_t('Exporter PDF', 'Exporter PDF')) ?>
            </a>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-ajouter.php')) ?>" class="btn btn--primary">
                <i class="fa-solid fa-plus"></i>
                <?= htmlspecialchars(theme_t('Ajouter un emploi', 'Ajouter un emploi')) ?>
            </a>
        </div>
    </div>

    <?php if (!empty($emplois)): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars(theme_t('Agent', 'Agent')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Service', 'Service')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Shift', 'Shift')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Date', 'Date')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Statut', 'Statut')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Code QR', 'Code QR')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Actions avancees', 'Actions avancees')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emplois as $emploi): ?>
                        <tr>
                            <td><?= (int) $emploi['id_emploi'] ?></td>
                            <td>
                                <?= htmlspecialchars(($emploi['agent_nom'] ?? 'N/A') . ' ' . ($emploi['agent_prenom'] ?? '')) ?>
                            </td>
                            <td><?= htmlspecialchars($emploi['nom_service'] ?? 'N/A') ?></td>
                            <td>
                                <strong><?= htmlspecialchars($emploi['nom_shift'] ?? 'N/A') ?></strong><br>
                                <span class="muted"><?= htmlspecialchars(substr($emploi['heure_debut'] ?? '00:00', 0, 5)) ?> - <?= htmlspecialchars(substr($emploi['heure_fin'] ?? '00:00', 0, 5)) ?></span><br>
                                <span class="muted"><?= htmlspecialchars(theme_t('Shift lie', 'Shift lie')) ?>: #<?= (int) ($emploi['id_shift'] ?? 0) ?></span>
                            </td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($emploi['date_travail']))) ?></td>
                            <td>
                                <span class="<?= emploi_badge_class($emploi['statut']) ?>">
                                    <?= htmlspecialchars(emploi_status_label($emploi['statut'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if (!empty($emploi['qr_token'])): ?>
                                    <a href="<?= htmlspecialchars(emploi_qr_page_url($emploi), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="qr-preview" title="<?= htmlspecialchars(theme_t('Voir emploi par QR', 'Voir emploi par QR')) ?>">
                                        <img src="<?= htmlspecialchars(emploi_qr_image_url($emploi, 96), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars(theme_t('Code QR emploi', 'Code QR emploi')) ?>">
                                    </a>
                                <?php else: ?>
                                    <span class="muted"><?= htmlspecialchars(theme_t('QR manquant', 'QR manquant')) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-modifier.php?id=' . $emploi['id_emploi'])) ?>" class="btn btn--warning">
                                        <i class="fa-solid fa-pen"></i>
                                        <?= htmlspecialchars(theme_t('Modifier', 'Modifier')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-supprimer.php?id=' . $emploi['id_emploi'])) ?>" class="btn btn--danger" onclick="return confirm('<?= htmlspecialchars(theme_t('Supprimer cet emploi ?', 'Supprimer cet emploi ?')) ?>');">
                                        <i class="fa-solid fa-trash"></i>
                                        <?= htmlspecialchars(theme_t('Supprimer', 'Supprimer')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(emploi_calendar_url($emploi)) ?>" target="_blank" rel="noopener" class="btn btn--ghost" title="<?= htmlspecialchars(theme_t('Ajouter au calendrier', 'Ajouter au calendrier')) ?>">
                                        <i class="fa-solid fa-calendar-plus"></i>
                                    </a>
                                    <?php if (!empty($emploi['qr_token'])): ?>
                                        <a href="<?= htmlspecialchars(emploi_qr_page_url($emploi), ENT_QUOTES, 'UTF-8') ?>" target="_blank" rel="noopener" class="btn btn--secondary" title="<?= htmlspecialchars(theme_t('Ouvrir le QR', 'Ouvrir le QR')) ?>">
                                            <i class="fa-solid fa-qrcode"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <h3><?= htmlspecialchars(theme_t('Aucun emploi trouve', 'Aucun emploi trouve')) ?></h3>
            <p><?= htmlspecialchars(theme_t('Ajustez les filtres ou ajoutez un planning pour commencer.', 'Ajustez les filtres ou ajoutez un planning pour commencer.')) ?></p>
        </div>
    <?php endif; ?>
</div>

<div class="table-panel advanced-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Avancee', 'Avancee')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Calendrier mensuel', 'Calendrier mensuel')) ?></h2>
        </div>
        <div class="actions">
            <a class="btn btn--ghost" href="<?= htmlspecialchars(emploi_query_url(['month' => date('Y-m', strtotime($calendarMonth . '-01 -1 month'))])) ?>">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
            <strong class="calendar-title"><?= htmlspecialchars(date('m/Y', strtotime($calendarMonth . '-01'))) ?></strong>
            <a class="btn btn--ghost" href="<?= htmlspecialchars(emploi_query_url(['month' => date('Y-m', strtotime($calendarMonth . '-01 +1 month'))])) ?>">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        </div>
    </div>

    <div class="calendar-wrapper">
        <div class="calendar-header-days">
            <div><?= htmlspecialchars(theme_t('Lun', 'الإثنين')) ?></div>
            <div><?= htmlspecialchars(theme_t('Mar', 'الثلاثاء')) ?></div>
            <div><?= htmlspecialchars(theme_t('Mer', 'الأربعاء')) ?></div>
            <div><?= htmlspecialchars(theme_t('Jeu', 'الخميس')) ?></div>
            <div><?= htmlspecialchars(theme_t('Ven', 'الجمعة')) ?></div>
            <div><?= htmlspecialchars(theme_t('Sam', 'السبت')) ?></div>
            <div><?= htmlspecialchars(theme_t('Dim', 'الأحد')) ?></div>
        </div>
        <div class="calendar-grid">
            <?php
            $monthStart = $calendarMonth . '-01';
            $daysInMonth = (int) date('t', strtotime($monthStart));
            $firstWeekday = (int) date('N', strtotime($monthStart));
            for ($blank = 1; $blank < $firstWeekday; $blank++):
            ?>
                <div class="calendar-day calendar-day--blank"></div>
            <?php endfor; ?>

            <?php for ($day = 1; $day <= $daysInMonth; $day++):
                $dateKey = $calendarMonth . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
                $dayItems = $calendarItems[$dateKey] ?? [];
            ?>
                <div class="calendar-day <?= $dateKey === date('Y-m-d') ? 'is-today' : '' ?>">
                    <div class="calendar-day__head">
                        <strong><?= $day ?></strong>
                        <?php if (!empty($dayItems)): ?>
                            <span class="badge"><?= count($dayItems) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="calendar-events">
                        <?php foreach (array_slice($dayItems, 0, 3) as $item): ?>
                            <div class="calendar-event" title="<?= htmlspecialchars(($item['agent_nom'] ?? '') . ' - ' . ($item['nom_service'] ?? '')) ?>">
                                <strong><?= htmlspecialchars(substr($item['heure_debut'] ?? '00:00', 0, 5)) ?></strong>
                                <span><?= htmlspecialchars($item['agent_nom'] ?? 'N/A') ?></span>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($dayItems) > 3): ?>
                            <div class="calendar-more">+<?= count($dayItems) - 3 ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>
<?php theme_render_end(); ?>
