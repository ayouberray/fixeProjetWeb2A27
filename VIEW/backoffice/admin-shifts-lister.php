<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new ShiftController();
$shifts = $ctrl->getAllShifts();

theme_render_start([
    'title' => theme_t('Gestion des shifts', '????? ?????????'),
    'page_title' => theme_t('Gestion des shifts', '????? ?????????'),
    'page_subtitle' => theme_t('Retrouvez tous les horaires dans un tableau harmonise avec actions rapides.', '???? ??? ?? ???????? ???? ???? ???? ?? ??????? ?????.'),
    'nav_context' => 'shifts',
]);
?>
<div class="table-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Backoffice', '???????')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Liste des shifts', '????? ?????????')) ?></h2>
        </div>
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-ajouter.php')) ?>" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un shift', '????? ??????')) ?>
        </a>
    </div>

    <?php if (!empty($shifts)): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars(theme_t('Nom', '?????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Heure debut', '??? ???????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Heure fin', '??? ???????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Duree', '?????')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Actions', '?????????')) ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($shifts as $shift): ?>
                        <?php
                        $debut = new DateTime($shift['heure_debut']);
                        $fin = new DateTime($shift['heure_fin']);
                        $diff = $fin->diff($debut);
                        $duree = $diff->h . 'h ' . $diff->i . 'min';
                        ?>
                        <tr>
                            <td><?= (int) $shift['id_shift'] ?></td>
                            <td><strong><?= htmlspecialchars($shift['nom_shift']) ?></strong></td>
                            <td><?= htmlspecialchars(substr($shift['heure_debut'], 0, 5)) ?></td>
                            <td><?= htmlspecialchars(substr($shift['heure_fin'], 0, 5)) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($duree) ?></span></td>
                            <td>
                                <div class="actions">
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-modifier.php?id=' . $shift['id_shift'])) ?>" class="btn btn--warning">
                                        <i class="fa-solid fa-pen"></i>
                                        <?= htmlspecialchars(theme_t('Modifier', '?????')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-supprimer.php?id=' . $shift['id_shift'])) ?>" class="btn btn--danger" onclick="return confirm('<?= htmlspecialchars(theme_t('Supprimer ce shift ?', '??? ??? ?????????')) ?>');">
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
            <h3><?= htmlspecialchars(theme_t('Aucun shift trouve', '?? ???? ???????')) ?></h3>
            <p><?= htmlspecialchars(theme_t('Commencez par ajouter votre premier horaire.', '???? ?????? ??? ?????.')) ?></p>
        </div>
    <?php endif; ?>
</div>
<?php theme_render_end(); ?>

