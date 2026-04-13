<?php
require_once __DIR__."/../../CONTROLLER/ShiftController.php";
$ctrl = new ShiftController();
$id = $_GET['id'] ?? 0;
if($id){ $ctrl->supprimerShift($id); }
header("Location: admin-shifts-lister.php");
exit();
?>


