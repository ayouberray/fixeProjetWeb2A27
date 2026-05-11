<?php
require_once __DIR__ . "/../../../CONTROLLER/ReponseController.php";
require_once __DIR__ . "/../../../MODEL/config.php";

$ctrl = new ReponseController();
$id = $_GET['id'] ?? 0;

if($id){
    $ctrl->supprimerReponse($id);
}

header("Location: lister.php");
exit();
?>
