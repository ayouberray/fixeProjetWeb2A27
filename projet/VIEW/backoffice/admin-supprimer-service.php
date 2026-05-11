<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../MODEL/config.php";

$db = Config::getConnexion();
$id = $_GET['id'] ?? 0;

if($id){
    $db->prepare("DELETE FROM services WHERE id_service = ?")->execute([$id]);
}

header("Location: /Gestion_RDV/projet/VIEW/backoffice/admin-services.php");
exit();
?>
