<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/pdf-report.php';

$ctrl = new ShiftController();
$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'periode' => trim($_GET['periode'] ?? ''),
    'sort' => trim($_GET['sort'] ?? 'heure_asc'),
];
$shifts = $ctrl->getAllShifts($filters);

function pdf_shift_duree_label($minutes) {
    $minutes = max(0, (int) $minutes);
    return intdiv($minutes, 60) . 'h ' . ($minutes % 60) . 'min';
}

function pdf_shift_periode_label($heureDebut) {
    $heure = substr((string) $heureDebut, 0, 5);
    if ($heure < '12:00') {
        return 'Matin';
    }
    if ($heure < '18:00') {
        return 'Apres-midi';
    }

    return 'Soir';
}

$pdf = new PdfReport('Liste des shifts');
$pdf->paragraph('Export genere le ' . date('d/m/Y H:i') . ' - ' . count($shifts) . ' resultat(s)');
$pdf->tableHeader(['ID', 'Nom', 'Periode', 'Debut', 'Fin', 'Duree', 'Emplois'], [34, 118, 78, 58, 58, 72, 94]);

foreach ($shifts as $shift) {
    $pdf->tableRow([
        (int) $shift['id_shift'],
        $shift['nom_shift'] ?? 'N/A',
        pdf_shift_periode_label($shift['heure_debut'] ?? ''),
        substr($shift['heure_debut'] ?? '00:00', 0, 5),
        substr($shift['heure_fin'] ?? '00:00', 0, 5),
        pdf_shift_duree_label((int) ($shift['duree_minutes'] ?? 0)),
        (int) ($shift['emplois_count'] ?? 0) . ' emploi(s)',
    ], [34, 118, 78, 58, 58, 72, 94]);
}

$pdf->output('shifts.pdf');
?>
