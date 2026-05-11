<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__.'/../vendor/phpmailer/Exception.php';
require_once __DIR__.'/../vendor/phpmailer/PHPMailer.php';
require_once __DIR__.'/../vendor/phpmailer/SMTP.php';

// CONFIGURATION SMTP Gmail
if (!defined('SMTP_USER')) define('SMTP_USER', 'contact.innogov@gmail.com');
if (!defined('SMTP_PASS')) define('SMTP_PASS', 'sijv nfoe cjvz pzke');
if (!defined('SMTP_FROM')) define('SMTP_FROM', 'contact.innogov@gmail.com');
if (!defined('SMTP_FROMNAME')) define('SMTP_FROMNAME', 'InnoGov - Municipalité');

function sendMunicipalEmail($to_email, $to_name, $subject, $html_body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        $mail->CharSet    = 'UTF-8';

        $mail->setFrom(SMTP_FROM, SMTP_FROMNAME);
        $mail->addAddress($to_email, $to_name);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $html_body;
        $mail->AltBody = strip_tags(str_replace(['<br>', '<br/>'], "\n", $html_body));

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function generateConfirmationHTML($rdv_data) {
    $date_formatee = date('d/m/Y à H:i', strtotime($rdv_data['date_heure']));
    return "
    <!DOCTYPE html>
    <html lang='fr'>
    <head><meta charset='UTF-8'><style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;margin:0;padding:20px;}
        .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1);}
        .header{background:linear-gradient(135deg,#10b981,#1e293b);color:#fff;padding:30px 40px;text-align:center;}
        .header h1{margin:0;font-size:22px;letter-spacing:1px;}
        .body{padding:35px 40px;}
        .status-icon{text-align:center;font-size:48px;color:#10b981;margin-bottom:10px;}
        .card{background:#f8fafc;border-left:4px solid #10b981;border-radius:8px;padding:20px 25px;margin:20px 0;}
        .card p{margin:8px 0;color:#334155;font-size:15px;}
        .btn{display:block;width:fit-content;margin:25px auto;background:#10b981;color:#fff;padding:14px 30px;border-radius:8px;text-decoration:none;font-weight:700;}
        .footer{background:#1e293b;color:#94a3b8;text-align:center;padding:20px;font-size:13px;}
    </style></head>
    <body><div class='wrap'>
        <div class='header'><h1>🏛️ InnoGov</h1><p>Confirmation de Réservation</p></div>
        <div class='body'>
            <div class='status-icon'>✅</div>
            <h2 style='text-align:center; color:#1e293b;'>Votre rendez-vous est confirmé !</h2>
            <p>Bonjour <strong>" . htmlspecialchars($rdv_data['citoyen_nom']) . "</strong>,</p>
            <p>Nous avons bien reçu votre demande de rendez-vous. Voici les détails :</p>
            <div class='card'>
                <p>🎫 <strong>N° RDV :</strong> #{$rdv_data['id_rdv']}</p>
                <p>🏢 <strong>Service :</strong> " . htmlspecialchars($rdv_data['nom_service'] ?? 'N/A') . "</p>
                <p>📅 <strong>Date :</strong> <strong>{$date_formatee}</strong></p>
            </div>
            <p style='font-size:14px; color:#64748b;'>Un email de rappel vous sera envoyé automatiquement avant le rendez-vous.</p>
            <a href='http://localhost/projet/VIEW/frontoffice/citoyen-mes-rdv.php' class='btn'>Gérer mes rendez-vous</a>
        </div>
        <div class='footer'><p>&copy; " . date('Y') . " InnoGov — Municipalité Tunisienne</p></div>
    </div></body></html>";
}

function generateStatusUpdateHTML($rdv_data) {
    $date_formatee = date('d/m/Y à H:i', strtotime($rdv_data['date_heure']));
    $status_labels = [
        'en_attente' => '⏳ En attente',
        'confirme' => '✅ Confirmé',
        'annule' => '❌ Annulé',
        'termine' => '🏁 Terminé'
    ];
    $status_colors = [
        'en_attente' => '#64748b',
        'confirme' => '#10b981',
        'annule' => '#ef4444',
        'termine' => '#3b82f6'
    ];
    $current_label = $status_labels[$rdv_data['statut']] ?? $rdv_data['statut'];
    $current_color = $status_colors[$rdv_data['statut']] ?? '#1e293b';

    return "
    <!DOCTYPE html>
    <html lang='fr'>
    <head><meta charset='UTF-8'><style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;margin:0;padding:20px;}
        .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1);}
        .header{background:linear-gradient(135deg,{$current_color},#1e293b);color:#fff;padding:30px;text-align:center;}
        .body{padding:35px 40px;}
        .status-badge{display:inline-block;padding:8px 20px;border-radius:30px;background:{$current_color};color:#fff;font-weight:700;margin-bottom:20px;}
        .card{background:#f8fafc;border-left:4px solid {$current_color};padding:20px;margin:20px 0;}
        .footer{background:#1e293b;color:#94a3b8;text-align:center;padding:20px;font-size:12px;}
    </style></head>
    <body><div class='wrap'>
        <div class='header'><h1>🏛️ InnoGov</h1><p>Mise à jour de votre rendez-vous</p></div>
        <div class='body'>
            <p>Bonjour <strong>" . htmlspecialchars($rdv_data['citoyen_nom']) . "</strong>,</p>
            <p>Le statut de votre rendez-vous vient d'être mis à jour par l'administration :</p>
            
            <div style='text-align:center;'><span class='status-badge'>{$current_label}</span></div>

            <div class='card'>
                <p>🎫 <strong>N° RDV :</strong> #{$rdv_data['id_rdv']}</p>
                <p>🏢 <strong>Service :</strong> " . htmlspecialchars($rdv_data['service_nom'] ?? 'N/A') . "</p>
                <p>📅 <strong>Date prévue :</strong> <strong>{$date_formatee}</strong></p>
            </div>
            
            <p style='font-size:14px; color:#64748b;'>Vous pouvez consulter les détails complets sur votre espace citoyen.</p>
        </div>
        <div class='footer'><p>&copy; " . date('Y') . " InnoGov — Municipalité</p></div>
    </div></body></html>";
}

function generateAgentAssignedHTML($rdv_data) {
    $date_formatee = date('d/m/Y à H:i', strtotime($rdv_data['date_heure']));
    return "
    <!DOCTYPE html>
    <html lang='fr'>
    <head><meta charset='UTF-8'><style>
        body{font-family:'Segoe UI',Arial,sans-serif;background:#f0f4f8;margin:0;padding:20px;}
        .wrap{max-width:600px;margin:0 auto;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 4px 20px rgba(0,0,0,.1);}
        .header{background:linear-gradient(135deg,#6366f1,#1e293b);color:#fff;padding:30px;text-align:center;}
        .body{padding:35px 40px;}
        .agent-icon{text-align:center;font-size:48px;color:#6366f1;margin-bottom:10px;}
        .card{background:#f8fafc;border-left:4px solid #6366f1;padding:20px;margin:20px 0;}
        .footer{background:#1e293b;color:#94a3b8;text-align:center;padding:20px;font-size:12px;}
    </style></head>
    <body><div class='wrap'>
        <div class='header'><h1>🏛️ InnoGov</h1><p>Un agent vous a été affecté</p></div>
        <div class='body'>
            <div class='agent-icon'>👤</div>
            <p>Bonjour <strong>" . htmlspecialchars($rdv_data['citoyen_nom']) . "</strong>,</p>
            <p>Nous vous informons qu'un agent municipal a été affecté pour traiter votre rendez-vous :</p>
            
            <div class='card'>
                <p>🎫 <strong>N° RDV :</strong> #{$rdv_data['id_rdv']}</p>
                <p>🏢 <strong>Service :</strong> " . htmlspecialchars($rdv_data['service_nom'] ?? 'N/A') . "</p>
                <p>📅 <strong>Date :</strong> <strong>{$date_formatee}</strong></p>
                <p>👤 <strong>Agent en charge :</strong> <strong style='color:#6366f1;'>" . htmlspecialchars($rdv_data['agent_nom']) . "</strong></p>
            </div>
            
            <p style='font-size:14px; color:#64748b;'>Cet agent sera votre interlocuteur principal lors de votre passage à la municipalité.</p>
        </div>
        <div class='footer'><p>&copy; " . date('Y') . " InnoGov — Municipalité</p></div>
    </div></body></html>";
}


