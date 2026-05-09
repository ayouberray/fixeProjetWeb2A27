<?php
// traiter.php
require_once __DIR__ . "/../../../CONTROLLER/CandidatureController.php";
$ctrl = new CandidatureController();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action']; // 'accepter' ou 'refuser'
    $nouveauStatut = ($action === 'accepter') ? 'validee' : 'rejetee';
    $result = $ctrl->candidatureModel->updateStatut($id, $nouveauStatut);
    if ($result) {
        header("Location: lister.php?msg=traite");
        exit;
    } else {
        die("Erreur lors du traitement.");
    }
} else {
    die("Paramètres manquants.");
}
?>