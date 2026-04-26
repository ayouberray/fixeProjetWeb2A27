<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new ShiftController();
$shifts = $ctrl->getAllShifts();

theme_render_start([
    'title' => theme_t('Gestion des shifts', 'إدارة المناوبات'),
    'page_title' => theme_t('Gestion des shifts', 'إدارة المناوبات'),
    'page_subtitle' => theme_t('Retrouvez tous les horaires dans un tableau harmonise avec actions rapides.', 'اعرض جميع المواقيت في جدول منسق مع اجراءات سريعة.'),
    'nav_context' => 'shifts',
]);
?>
<div class="table-panel">
    <div class="panel-toolbar">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Backoffice', 'لوحة الادارة')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Liste des shifts', 'قائمة المناوبات')) ?></h2>
        </div>
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-ajouter.php')) ?>" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un shift', 'اضافة مناوبة')) ?>
        </a>
    </div>

    <?php if (!empty($shifts)): ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= htmlspecialchars(theme_t('Nom', 'الاسم')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Heure debut', 'ساعة البداية')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Heure fin', 'ساعة النهاية')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Duree', 'المدة')) ?></th>
                        <th><?= htmlspecialchars(theme_t('Actions', 'الاجراءات')) ?></th>
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
                                        <?= htmlspecialchars(theme_t('Modifier', 'تعديل')) ?>
                                    </a>
                                    <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-supprimer.php?id=' . $shift['id_shift'])) ?>" class="btn btn--danger" onclick="return confirm('<?= htmlspecialchars(theme_t('Supprimer ce shift ?', 'حذف هذه المناوبة؟')) ?>');">
                                        <i class="fa-solid fa-trash"></i>
                                        <?= htmlspecialchars(theme_t('Supprimer', 'حذف')) ?>
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
            <h3><?= htmlspecialchars(theme_t('Aucun shift trouve', 'لا توجد مناوبات')) ?></h3>
            <p><?= htmlspecialchars(theme_t('Commencez par ajouter votre premier horaire.', 'ابدأ باضافة اول توقيت.')) ?></p>
        </div>
    <?php endif; ?>
</div>
<?php theme_render_end(); ?>
