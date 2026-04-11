<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])){ $_SESSION['user_id'] = 2; }

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;

$db = Config::getConnexion();
$sql = "SELECT * FROM rendez_vous WHERE id_rdv = :id AND id_citoyen = :citoyen";
$req = $db->prepare($sql);
$req->execute(['id' => $id_rdv, 'citoyen' => $_SESSION['user_id']]);
$rdv = $req->fetch();

if($rdv && $rdv['statut'] != 'annule' && $rdv['statut'] != 'termine'){
    $rdvController->annulerRendezVous($id_rdv);
}

header("Location: /projet/VIEW/frontoffice/citoyen-mes-rdv.php");
exit();
?>