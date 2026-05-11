<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}

require_once __DIR__."/../../CONTROLLER/RendezVousController.php";

$rdvController = new RendezVousController();
$id_rdv = $_GET['id'] ?? 0;

$citoyen_nom = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];
$rdv = $rdvController->getRendezVousById($id_rdv);

// Vérifier que le citoyen est bien le propriétaire
if($rdv && $rdv['citoyen_nom'] == $citoyen_nom){
    // SUPPRIMER au lieu de changer le statut
    $rdvController->supprimerRendezVous($id_rdv);
}

header("Location: /projet/VIEW/frontoffice/citoyen-mes-rdv.php");
exit();
?>