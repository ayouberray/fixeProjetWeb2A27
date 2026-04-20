<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$emplois = $ctrl->getAllEmplois();

function emploi_badge_class($statut) {
    if ($statut === 'termine') {
        return 'badge badge--success';
    }
    if ($statut === 'annule') {
        return 'badge badge--danger';
    }
    return 'badge badge--warning';
}

theme_render_start([
    'title' => theme_t('Gestion des emplois', '????? ???????'),
    'page_title' => theme_t('Gestion des emplois', '????? ???????'),
    'page_subtitle' => theme_t('Pilotez les affectations des agents avec un affichage plus propre et plus moderne.', '?? ?????? ??????? ??????? ??? ??? ???? ????? ?????.'),
    'nav_context' => 'emplois',
]);
?>
<div class="table-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Backoffice', '???????')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Liste des emplois', '????? ???????')) ?></h2>
        </div>
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-ajouter.php')) ?>" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un emploi', '????? ????')) ?>
        </a>
    </div>

    <?php if (!empty($emplois)): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars(theme_t('Agent', '?????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Service', '??????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Shift', '????????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Date', '???????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Statut', '??????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Actions', '?????????')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($emplois as $emploi): ?>
                        <tr>
                            <td><?= (int) $emploi['id_emploi'] ?></td>
                            <td><?= htmlspecialchars(($emploi['agent_nom'] ?? 'N/A') . ' ' . ($emploi['agent_prenom'] ?? '')) ?></td>
                            <td><?= htmlspecialchars($emploi['nom_service'] ?? 'N/A') ?></td>
                            <td>
                                <strong><?= htmlspecialchars($emploi['nom_shift'] ?? 'N/A') ?></strong><br>
                                <span class="muted"><?= htmlspecialchars(substr($emploi['heure_debut'] ?? '00:00', 0, 5)) ?> - <?= htmlspecialchars(substr($emploi['heure_fin'] ?? '00:00', 0, 5)) ?></span>
                            </td>
                            <td><?= htmlspecialchars(date('d/m/Y', strtotime($emploi['date_travail']))) ?></td>
                            <td>
                                <span class="<?= emploi_badge_class($emploi['statut']) ?>">
                                    <?= htmlspecialchars($emploi['statut'] === 'termine' ? theme_t('Termine', '?????') : ($emploi['statut'] === 'annule' ? theme_t('Annule', '????') : theme_t('Planifie', '????'))) ?>
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-modifier.php?id=' . $emploi['id_emploi'])) ?>" class="btn btn--warning">
                                        <i class="fa-solid fa-pen"></i>
                                        <?= htmlspecialchars(theme_t('Modifier', '?????')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-supprimer.php?id=' . $emploi['id_emploi'])) ?>" class="btn btn--danger" onclick="return confirm('<?= htmlspecialchars(theme_t('Supprimer cet emploi ?', '??? ??? ???????')) ?>');">
                                        <i class="fa-solid fa-trash"></i>
                                        <?= htmlspecialchars(theme_t('Supprimer', '???')) ?>
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
            <h3><?= htmlspecialchars(theme_t('Aucun emploi trouve', '?? ???? ?????')) ?></h3>
            <p><?= htmlspecialchars(theme_t('Ajoutez un planning pour commencer a organiser les affectations.', '??? ????? ????? ?? ????? ?????????.')) ?></p>
        </div>
    <?php endif; ?>
</div>
<?php theme_render_end(); ?>

