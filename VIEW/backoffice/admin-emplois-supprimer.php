<?php
require_once __DIR__."/../../CONTROLLER/EmploiController.php";
$ctrl = new EmploiController();
$id = $_GET['id'] ?? 0;
if($id){ $ctrl->supprimerEmploi($id); }
header("Location: admin-emplois-lister.php");
exit();
?>


