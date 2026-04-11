<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;

if($id_rdv){
    $rdvController->adminSupprimerRendezVous($id_rdv);
    header("Location: /projet/VIEW/backoffice/admin-lister-rdv.php");
} else {
    header("Location: /projet/VIEW/backoffice/admin-lister-rdv.php");
}
exit();
?>