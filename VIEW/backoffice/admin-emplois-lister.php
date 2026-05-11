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
$defaultNotificationPhone = '58739548';

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

function emploi_phone_display($phone = '') {
    global $defaultNotificationPhone;

    return $defaultNotificationPhone;
}

function emploi_phone_for_link($phone = '') {
    global $defaultNotificationPhone;
    $phone = $defaultNotificationPhone;
    $phone = preg_replace('/[^\d+]/', '', $phone);
    if (strpos($phone, '00') === 0) {
        $phone = '+' . substr($phone, 2);
    }
    if (strpos($phone, '+') !== 0 && preg_match('/^\d{8}$/', $phone)) {
        $phone = '+216' . $phone;
    }

    return $phone;
}

function emploi_sms_url($emploi) {
    $phone = emploi_phone_for_link();

    return 'sms:' . $phone . '?body=' . rawurlencode(emploi_message($emploi));
}

function emploi_whatsapp_url($emploi) {
    $phone = emploi_phone_for_link();
    $digits = preg_replace('/\D/', '', $phone);
    if ($digits !== '') {
        return 'https://wa.me/' . $digits . '?text=' . rawurlencode(emploi_message($emploi));
    }

    return 'https://wa.me/?text=' . rawurlencode(emploi_message($emploi));
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

<div class="advanced-stats" id="advancedStatsPanel" data-advanced-stats hidden>
    <div class="advanced-stats__head">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Statistique', 'Statistique')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Vue statistique dynamique', 'Vue statistique dynamique')) ?></h2>
        </div>
        <span class="advanced-stats__spinner" aria-hidden="true"></span>
    </div>

    <div class="advanced-stats__grid">
        <?php
        $statCards = [
            ['label' => theme_t('Planifies', 'Planifies'), 'value' => (int) ($stats['planifies'] ?? 0), 'percent' => round(((int) ($stats['planifies'] ?? 0) / $statsTotal) * 100), 'icon' => 'fa-hourglass-half', 'tone' => 'amber'],
            ['label' => theme_t('Termines', 'Termines'), 'value' => (int) ($stats['termines'] ?? 0), 'percent' => round(((int) ($stats['termines'] ?? 0) / $statsTotal) * 100), 'icon' => 'fa-circle-check', 'tone' => 'green'],
            ['label' => theme_t('Annules', 'Annules'), 'value' => (int) ($stats['annules'] ?? 0), 'percent' => round(((int) ($stats['annules'] ?? 0) / $statsTotal) * 100), 'icon' => 'fa-ban', 'tone' => 'red'],
            ['label' => theme_t('Cette semaine', 'Cette semaine'), 'value' => (int) ($stats['semaine'] ?? 0), 'percent' => round(((int) ($stats['semaine'] ?? 0) / $statsTotal) * 100), 'icon' => 'fa-calendar-week', 'tone' => 'blue'],
        ];
        ?>
        <article class="advanced-stat advanced-stat--total advanced-stat--teal">
            <span class="advanced-stat__icon"><i class="fa-solid fa-calendar-check"></i></span>
            <strong class="advanced-stat__number" data-countup="<?= (int) ($stats['total'] ?? 0) ?>"><?= (int) ($stats['total'] ?? 0) ?></strong>
            <span><?= htmlspecialchars(theme_t('Total emplois', 'Total emplois')) ?></span>
        </article>
        <article class="advanced-stat advanced-stat--total advanced-stat--violet">
            <span class="advanced-stat__icon"><i class="fa-solid fa-users"></i></span>
            <strong class="advanced-stat__number" data-countup="<?= (int) ($stats['agents'] ?? 0) ?>"><?= (int) ($stats['agents'] ?? 0) ?></strong>
            <span><?= htmlspecialchars(theme_t('Agents affectes', 'Agents affectes')) ?></span>
        </article>
        <article class="advanced-stat advanced-stat--total advanced-stat--cyan">
            <span class="advanced-stat__icon"><i class="fa-solid fa-calendar-day"></i></span>
            <strong class="advanced-stat__number" data-countup="<?= (int) ($stats['aujourdhui'] ?? 0) ?>"><?= (int) ($stats['aujourdhui'] ?? 0) ?></strong>
            <span><?= htmlspecialchars(theme_t('Aujourd hui', 'Aujourd hui')) ?></span>
        </article>

        <?php foreach ($statCards as $card): ?>
            <article class="advanced-stat advanced-stat--<?= htmlspecialchars($card['tone']) ?>">
                <div class="stat-ring stat-ring--<?= htmlspecialchars($card['tone']) ?>" style="--value: <?= (int) $card['percent'] ?>;">
                    <span><?= (int) $card['percent'] ?>%</span>
                </div>
                <div>
                    <span class="advanced-stat__icon"><i class="fa-solid <?= htmlspecialchars($card['icon']) ?>"></i></span>
                    <strong class="advanced-stat__number" data-countup="<?= (int) $card['value'] ?>"><?= (int) $card['value'] ?></strong>
                    <span><?= htmlspecialchars($card['label']) ?></span>
                    <div class="stat-progress" style="--value: <?= (int) $card['percent'] ?>;">
                        <span></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>

    <div class="stats-summary">
        <div>
            <strong><?= (int) ($stats['termines'] ?? 0) ?></strong>
            <span><?= htmlspecialchars(theme_t('missions terminees', 'missions terminees')) ?></span>
        </div>
        <div>
            <strong><?= (int) ($stats['planifies'] ?? 0) ?></strong>
            <span><?= htmlspecialchars(theme_t('missions a suivre', 'missions a suivre')) ?></span>
        </div>
        <div>
            <strong><?= (int) ($stats['annules'] ?? 0) ?></strong>
            <span><?= htmlspecialchars(theme_t('annulations', 'annulations')) ?></span>
        </div>
    </div>
</div>

<script>
(function () {
    var btn = document.getElementById('statsToggleBtn');
    var panel = document.getElementById('advancedStatsPanel');
    if (!btn || !panel || btn.dataset.ready === '1') return;
    btn.dataset.ready = '1';

    btn.addEventListener('click', function () {
        var isHidden = panel.hasAttribute('hidden');
        if (isHidden) {
            panel.removeAttribute('hidden');
            btn.setAttribute('aria-expanded', 'true');
            panel.querySelectorAll('.stat-ring').forEach(function (ring) {
                var target = parseInt(ring.style.getPropertyValue('--value') || '0', 10);
                var value = 0;
                var timer = setInterval(function () {
                    value += Math.max(1, Math.ceil(target / 24));
                    if (value >= target) {
                        value = target;
                        clearInterval(timer);
                    }
                    ring.style.setProperty('--progress', value);
                }, 24);
            });
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
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
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-ajouter.php')) ?>" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un emploi', 'Ajouter un emploi')) ?>
        </a>
    </div>

    <?php if (!empty($emplois)): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars(theme_t('Agent', 'Agent')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Telephone', 'Telephone')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Service', 'Service')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Shift', 'Shift')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Date', 'Date')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Statut', 'Statut')) ?></th>
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
                            <td>
                                <span class="badge"><i class="fa-solid fa-phone"></i> <?= htmlspecialchars(emploi_phone_display($emploi['agent_telephone'] ?? '')) ?></span>
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
                                <div class="actions">
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-modifier.php?id=' . $emploi['id_emploi'])) ?>" class="btn btn--warning">
                                        <i class="fa-solid fa-pen"></i>
                                        <?= htmlspecialchars(theme_t('Modifier', 'Modifier')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-supprimer.php?id=' . $emploi['id_emploi'])) ?>" class="btn btn--danger" onclick="return confirm('<?= htmlspecialchars(theme_t('Supprimer cet emploi ?', 'Supprimer cet emploi ?')) ?>');">
                                        <i class="fa-solid fa-trash"></i>
                                        <?= htmlspecialchars(theme_t('Supprimer', 'Supprimer')) ?>
                                    </a>
                                    <?php $message = emploi_message($emploi); ?>
                                    <a href="<?= htmlspecialchars(emploi_sms_url($emploi)) ?>" class="btn btn--ghost" title="<?= htmlspecialchars(theme_t('Envoyer SMS', 'Envoyer SMS')) ?>" data-sms-action data-phone="<?= htmlspecialchars(emploi_phone_for_link(), ENT_QUOTES, 'UTF-8') ?>" data-message="<?= htmlspecialchars(emploi_message($emploi), ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fa-solid fa-comment-sms"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars(emploi_whatsapp_url($emploi)) ?>" target="_blank" rel="noopener" class="btn btn--secondary" title="<?= htmlspecialchars(theme_t('Envoyer WhatsApp', 'Envoyer WhatsApp')) ?>">
                                        <i class="fa-brands fa-whatsapp"></i>
                                    </a>
                                    <a href="<?= htmlspecialchars(emploi_calendar_url($emploi)) ?>" target="_blank" rel="noopener" class="btn btn--ghost" title="<?= htmlspecialchars(theme_t('Ajouter au calendrier', 'Ajouter au calendrier')) ?>">
                                        <i class="fa-solid fa-calendar-plus"></i>
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
                <?php foreach (array_slice($dayItems, 0, 3) as $item): ?>
                    <div class="calendar-event">
                        <strong><?= htmlspecialchars(substr($item['heure_debut'] ?? '00:00', 0, 5)) ?></strong>
                        <span><?= htmlspecialchars(($item['agent_nom'] ?? 'N/A') . ' ' . ($item['agent_prenom'] ?? '')) ?></span>
                    </div>
                <?php endforeach; ?>
                <?php if (count($dayItems) > 3): ?>
                    <small class="muted">+<?= count($dayItems) - 3 ?> <?= htmlspecialchars(theme_t('autres', 'autres')) ?></small>
                <?php endif; ?>
            </div>
        <?php endfor; ?>
    </div>
</div>
<script>
(function () {
    var isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

    function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            return navigator.clipboard.writeText(text);
        }

        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        document.execCommand('copy');
        textarea.remove();
        return Promise.resolve();
    }

    document.querySelectorAll('[data-sms-action]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            if (isMobile) return;

            event.preventDefault();
            var text = 'Numero: ' + (link.dataset.phone || '') + "\nMessage: " + (link.dataset.message || '');
            copyText(text).then(function () {
                alert('Sur PC, Chrome ne peut pas ouvrir SMS directement.\nLe numero et le message sont copies:\n\n' + text);
            });
        });
    });
})();
</script>
<?php theme_render_end(); ?>
