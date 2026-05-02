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
$shiftContacts = $ctrl->getContactsByShiftIds(array_column($shifts, 'id_shift'));
$defaultNotificationPhone = '58739548';

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

function shift_contact_message($contact) {
    $agent = trim(($contact['agent_nom'] ?? '') . ' ' . ($contact['agent_prenom'] ?? ''));
    $date = date('d/m/Y', strtotime($contact['date_travail']));
    $heureDebut = substr($contact['heure_debut'] ?? '00:00', 0, 5);
    $heureFin = substr($contact['heure_fin'] ?? '00:00', 0, 5);

    return "Bonjour " . ($agent !== '' ? $agent : 'agent') . ", rappel: votre shift " . ($contact['nom_shift'] ?? 'N/A') . " est planifie le $date de $heureDebut a $heureFin pour le service " . ($contact['nom_service'] ?? 'N/A') . ".";
}

theme_render_start([
    'title' => theme_t('Gestion des shifts', 'Gestion des shifts'),
    'page_title' => theme_t('Gestion des shifts', 'Gestion des shifts'),
    'page_subtitle' => theme_t('Statistique, recherche, tri et suivi des shifts.', 'Statistique, recherche, tri et suivi des shifts.'),
    'nav_context' => 'shifts',
]);
?>
<div class="stats-launcher">
    <a class="btn btn--primary stats-toggle" href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-statistiques.php')) ?>">
        <i class="fa-solid fa-chart-pie"></i>
        <?= htmlspecialchars(theme_t('Statistique', 'Statistique')) ?>
    </a>
</div>

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
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-ajouter.php')) ?>" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un shift', 'Ajouter un shift')) ?>
        </a>
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
                        <th><?= htmlspecialchars(theme_t('Envoyer emploi', 'Envoyer emploi')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Actions', 'Actions')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shifts as $shift): ?>
                        <?php
                        $dureeMinutes = (int) ($shift['duree_minutes'] ?? 0);
                        $emploisCount = (int) ($shift['emplois_count'] ?? 0);
                        $emploisPlanifies = (int) ($shift['emplois_planifies'] ?? 0);
                        $contacts = $shiftContacts[(int) $shift['id_shift']] ?? [];
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
                                <?php if (!empty($contacts)): ?>
                                    <?php foreach (array_slice($contacts, 0, 3) as $contact): ?>
                                        <div class="contact-row">
                                            <div>
                                                <strong><?= htmlspecialchars(trim(($contact['agent_nom'] ?? '') . ' ' . ($contact['agent_prenom'] ?? ''))) ?></strong><br>
                                                <span class="muted">
                                                    <?= htmlspecialchars(date('d/m/Y', strtotime($contact['date_travail']))) ?>
                                                    - <?= htmlspecialchars(substr($contact['heure_debut'] ?? '00:00', 0, 5)) ?>
                                                    /
                                                    <?= htmlspecialchars(substr($contact['heure_fin'] ?? '00:00', 0, 5)) ?>
                                                </span>
                                            </div>
                                            <div class="send-emploi" data-send-emploi>
                                                <label class="sr-only" for="send_phone_<?= (int) $contact['id_emploi'] ?>"><?= htmlspecialchars(theme_t('Numero destinataire', 'Numero destinataire')) ?></label>
                                                <input id="send_phone_<?= (int) $contact['id_emploi'] ?>" type="tel" inputmode="tel" value="<?= htmlspecialchars($defaultNotificationPhone, ENT_QUOTES, 'UTF-8') ?>" placeholder="<?= htmlspecialchars(theme_t('Numero...', 'Numero...')) ?>" data-send-phone>
                                                <button type="button" class="btn btn--secondary" data-send-button data-message="<?= htmlspecialchars(shift_contact_message($contact), ENT_QUOTES, 'UTF-8') ?>">
                                                    <i class="fa-solid fa-paper-plane"></i>
                                                    <?= htmlspecialchars(theme_t('Envoyer', 'Envoyer')) ?>
                                                </button>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                    <?php if (count($contacts) > 3): ?>
                                        <span class="muted">+<?= count($contacts) - 3 ?> <?= htmlspecialchars(theme_t('autres contacts', 'autres contacts')) ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="muted"><?= htmlspecialchars(theme_t('Aucun agent planifie a venir', 'Aucun agent planifie a venir')) ?></span>
                                <?php endif; ?>
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
<script>
(function () {
    var isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent);

    function normalizePhone(phone) {
        phone = (phone || '').trim().replace(/[^\d+]/g, '');
        if (phone.indexOf('00') === 0) {
            phone = '+' + phone.substring(2);
        }
        if (phone.charAt(0) !== '+' && /^\d{8}$/.test(phone)) {
            phone = '+216' + phone;
        }
        return phone;
    }

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

    document.querySelectorAll('[data-send-emploi]').forEach(function (row) {
        var input = row.querySelector('[data-send-phone]');
        var button = row.querySelector('[data-send-button]');
        if (!input || !button) return;

        button.addEventListener('click', function () {
            var phone = normalizePhone(input.value);
            if (!phone) {
                input.focus();
                return;
            }

            if (isMobile) {
                window.location.href = 'sms:' + phone + '?body=' + encodeURIComponent(button.dataset.message || '');
                return;
            }

            var text = 'Numero: ' + phone + "\nMessage: " + (button.dataset.message || '');
            copyText(text).then(function () {
                alert('Sur PC, Chrome ne peut pas ouvrir SMS directement.\nLe numero et le message sont copies:\n\n' + text);
            });
        });
    });
})();
</script>
<?php theme_render_end(); ?>
