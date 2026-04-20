<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new ShiftController();
$id = (int) ($_GET['id'] ?? 0);

if ($id) {
    $ctrl->supprimerShift($id);
}

header('Location: ' . theme_url('VIEW/backoffice/admin-shifts-lister.php?toast=' . urlencode(theme_t('Shift supprime avec succes', '?? ??? ???????? ?????')) . '&type=success'));
exit();
?>

