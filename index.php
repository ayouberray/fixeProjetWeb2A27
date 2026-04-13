<?php
// index.php (racine du projet 2A27)
require_once __DIR__ . "/CONTROLLER/OffreController.php";
require_once __DIR__ . "/CONTROLLER/CandidatureController.php";

$controller = isset($_GET['controller']) ? $_GET['controller'] : 'offre';
$action = isset($_GET['action']) ? $_GET['action'] : 'lister';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($controller === 'offre') {
    $ctrl = new OffreController();
    switch ($action) {
        case 'lister':
            $ctrl->listerOffres();
            break;
        case 'detail':
            if ($id) $ctrl->detailOffre($id);
            else header("Location: index.php");
            break;
        case 'admin-lister':
            $ctrl->adminListerOffres();
            break;
        case 'ajouter':
            $ctrl->ajouterOffre();
            break;
        case 'modifier':
            if ($id) $ctrl->modifierOffre($id);
            break;
        case 'supprimer':
            if ($id) $ctrl->supprimerOffre($id);
            break;
        default:
            $ctrl->listerOffres();
    }
} elseif ($controller === 'candidature') {
    $ctrl = new CandidatureController();
    switch ($action) {
        case 'postuler':
            $ctrl->postuler();
            break;
        case 'admin-lister':
            $ctrl->adminListerCandidatures();
            break;
        case 'traiter':
            $ctrl->traiterCandidature();
            break;
        case 'telecharger-cv':
            if ($id) $ctrl->telechargerCV($id);
            break;
        default:
            header("Location: index.php?controller=offre&action=lister");
    }
} else {
    header("Location: index.php?controller=offre&action=lister");
}