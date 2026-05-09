<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/pdf-report.php';

$ctrl = new EmploiController();
$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'statut' => trim($_GET['statut'] ?? ''),
    'id_service' => trim($_GET['id_service'] ?? ''),
    'date_from' => trim($_GET['date_from'] ?? ''),
    'date_to' => trim($_GET['date_to'] ?? ''),
    'sort' => trim($_GET['sort'] ?? 'date_desc'),
];
$emplois = $ctrl->getAllEmplois($filters);

function pdf_emploi_status_label($statut) {
    if ($statut === 'termine') {
        return 'Termine';
    }
    if ($statut === 'annule') {
        return 'Annule';
    }

    return 'Planifie';
}

$pdf = new PdfReport('Liste des emplois');
$pdf->paragraph('Export genere le ' . date('d/m/Y H:i') . ' - ' . count($emplois) . ' resultat(s)');
$pdf->tableHeader(['ID', 'Agent', 'Service', 'Shift', 'Date', 'Statut'], [34, 108, 104, 118, 72, 76]);

foreach ($emplois as $emploi) {
    $agent = trim(($emploi['agent_nom'] ?? 'N/A') . ' ' . ($emploi['agent_prenom'] ?? ''));
    $shift = ($emploi['nom_shift'] ?? 'N/A') . ' ' . substr($emploi['heure_debut'] ?? '00:00', 0, 5) . '-' . substr($emploi['heure_fin'] ?? '00:00', 0, 5);
    $date = !empty($emploi['date_travail']) ? date('d/m/Y', strtotime($emploi['date_travail'])) : 'N/A';

    $pdf->tableRow([
        (int) $emploi['id_emploi'],
        $agent,
        $emploi['nom_service'] ?? 'N/A',
        $shift,
        $date,
        pdf_emploi_status_label($emploi['statut'] ?? 'planifie'),
    ], [34, 108, 104, 118, 72, 76]);
}

$pdf->output('emplois.pdf');
?>
