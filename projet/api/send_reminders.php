<?php
/**
 * Script de rappels automatiques multi-niveaux utilisant une table séparée (rappels_log)
 * Ce script est appelé via pseudo-CRON dans index.php.
 */

require_once __DIR__.'/mail_sender.php';
require_once __DIR__.'/../MODEL/config.php';

$db = Config::getConnexion();

// DÉFINITION DES NIVEAUX DE RAPPEL
$rappel_levels = [
    [
        'type'       => '1mois',
        'label'      => '1 mois',
        'emoji'      => '📅',
        'color'      => '#7c3aed',
        'sql_before' => 'DATE_ADD(NOW(), INTERVAL 31 DAY)',
        'sql_after'  => 'DATE_ADD(NOW(), INTERVAL 28 DAY)',
        'subject'    => '📅 Dans 1 mois : Votre RDV #%d'
    ],
    [
        'type'       => '1semaine',
        'label'      => '1 semaine',
        'emoji'      => '🗓️',
        'color'      => '#2563eb',
        'sql_before' => 'DATE_ADD(NOW(), INTERVAL 8 DAY)',
        'sql_after'  => 'DATE_ADD(NOW(), INTERVAL 6 DAY)',
        'subject'    => '🗓️ Dans 1 semaine : Votre RDV #%d'
    ],
    [
        'type'       => '1jour',
        'label'      => '24 heures',
        'emoji'      => '⏰',
        'color'      => '#f59e0b',
        'sql_before' => 'DATE_ADD(NOW(), INTERVAL 25 HOUR)',
        'sql_after'  => 'DATE_ADD(NOW(), INTERVAL 23 HOUR)',
        'subject'    => '⏰ Demain : Votre RDV #%d'
    ],
    [
        'type'       => '1heure',
        'label'      => '1 heure',
        'emoji'      => '🔔',
        'color'      => '#ef4444',
        'sql_before' => 'DATE_ADD(NOW(), INTERVAL 70 MINUTE)',
        'sql_after'  => 'DATE_ADD(NOW(), INTERVAL 50 MINUTE)',
        'subject'    => '🔔 Dans 1 heure : Votre RDV #%d'
    ],
];

foreach ($rappel_levels as $level) {
    // Sélectionner les RDV qui n'ont pas encore reçu ce type de rappel spécifique
    $sql = "
        SELECT r.id_rdv, r.citoyen_nom, r.citoyen_email, r.date_heure, s.nom_service, r.motif
        FROM rendez_vous r
        LEFT JOIN services s ON r.id_service = s.id_service
        WHERE 
            r.citoyen_email IS NOT NULL AND r.citoyen_email != ''
            AND r.statut NOT IN ('annule', 'termine')
            AND r.date_heure BETWEEN {$level['sql_after']} AND {$level['sql_before']}
            AND NOT EXISTS (
                SELECT 1 FROM rappels_log rl 
                WHERE rl.id_rdv = r.id_rdv AND rl.type_rappel = '{$level['type']}'
            )
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $rdvs = $stmt->fetchAll();

    foreach ($rdvs as $rdv) {
        $date_formatee = date('d/m/Y à H:i', strtotime($rdv['date_heure']));
        $subject = sprintf($level['subject'], $rdv['id_rdv']);
        
        // Utilisation de la fonction partagée du mail_sender.php (on adapte un peu le HTML)
        $html = generateReminderHTML($rdv, $level, $date_formatee);

        if (sendMunicipalEmail($rdv['citoyen_email'], $rdv['citoyen_nom'], $subject, $html)) {
            // Enregistrement dans la table séparée
            $db->prepare("INSERT INTO rappels_log (id_rdv, type_rappel) VALUES (?, ?)")
               ->execute([$rdv['id_rdv'], $level['type']]);
        }
    }
}

function generateReminderHTML($rdv, $level, $date_formatee) {
    return "
    <!DOCTYPE html>
    <html lang='fr'>
    <head><meta charset='UTF-8'><style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;margin:0;padding:20px;}
        .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1);}
        .header{background:linear-gradient(135deg,{$level['color']},#1e293b);color:#fff;padding:30px;text-align:center;}
        .body{padding:35px 40px;}
        .countdown{text-align:center;font-size:40px;color:{$level['color']};margin-bottom:20px;}
        .card{background:#f8fafc;border-left:4px solid {$level['color']};padding:20px;margin:20px 0;}
        .btn{display:block;width:fit-content;margin:25px auto;background:{$level['color']};color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;font-weight:700;}
        .footer{background:#1e293b;color:#94a3b8;text-align:center;padding:20px;font-size:12px;}
    </style></head>
    <body><div class='wrap'>
        <div class='header'><h1>🏛️ InnoGov</h1><p>Rappel Automatique</p></div>
        <div class='body'>
            <div class='countdown'>{$level['emoji']}<br><small style='font-size:16px;'>Rappel : {$level['label']} avant</small></div>
            <p>Bonjour <strong>" . htmlspecialchars($rdv['citoyen_nom']) . "</strong>,</p>
            <p>Ceci est un rappel pour votre rendez-vous :</p>
            <div class='card'>
                <p>🎫 <strong>N° RDV :</strong> #{$rdv['id_rdv']}</p>
                <p>🏢 <strong>Service :</strong> " . htmlspecialchars($rdv['nom_service'] ?? 'N/A') . "</p>
                <p>📅 <strong>Date :</strong> <strong style='color:{$level['color']}'>{$date_formatee}</strong></p>
            </div>
            <a href='http://localhost/projet/VIEW/frontoffice/citoyen-mes-rdv.php' class='btn'>Consulter mon RDV</a>
        </div>
        <div class='footer'><p>&copy; " . date('Y') . " InnoGov — Municipalité</p></div>
    </div></body></html>";
}
?>
