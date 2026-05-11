<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$id = (int) ($_GET['id'] ?? 0);

if ($id) {
    $ctrl->supprimerEmploi($id);
}

header('Location: ' . theme_url('VIEW/backoffice/admin-emplois-lister.php?toast=' . urlencode(theme_t('Emploi supprime avec succes', 'تم حذف الجدول بنجاح')) . '&type=success'));
exit();
?>
