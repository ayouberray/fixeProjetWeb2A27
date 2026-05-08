<?php
// Fichier: CONFIG/config.php (MailConfig)
// Configuration complète pour l'envoi d'emails

require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class MailConfig {
    private $mail;
    
    public function __construct() {
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    private function setupSMTP() {
        try {
            // Configuration SMTP Gmail
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = 'ayouberray7@gmail.com';
            $this->mail->Password = 'xffa poan rsex jyhe';
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            
            // Configuration de l'expéditeur
            $this->mail->setFrom('no-reply@innogov.tn', 'InnoGov - Services Citoyens');
            
            // Format HTML
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            
            // Désactiver le debug (mettre 2 pour tester)
            $this->mail->SMTPDebug = 0;
            
        } catch (Exception $e) {
            error_log("Erreur configuration email: " . $this->mail->ErrorInfo);
        }
    }
    
    // ==================== EMAIL RÉINITIALISATION MOT DE PASSE ====================
    
    public function sendResetEmail($toEmail, $toName, $resetLink) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = '🔐 Réinitialisation de votre mot de passe - InnoGov';
            $this->mail->Body = $this->getResetTemplate($toName, $resetLink);
            $this->mail->AltBody = "Bonjour $toName,\n\nRéinitialisation : $resetLink\n\nExpire dans 1 heure.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur envoi email: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function getResetTemplate($name, $resetLink) {
        return '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>Réinitialisation mot de passe</title>
        <style>
            body{font-family:"Segoe UI",Arial;background:#f4f4f4;margin:0;padding:0}
            .container{max-width:600px;margin:20px auto;background:white;border-radius:10px}
            .header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}
            .content{padding:40px 30px}
            .button{display:inline-block;padding:12px 30px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border-radius:5px;margin:20px 0}
            .warning{background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0}
            .footer{background:#f8f9fa;padding:20px;text-align:center;font-size:12px;color:#666}
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header"><h1>🔐 InnoGov</h1><p>Services citoyens</p></div>
                <div class="content">
                    <h2>Bonjour ' . htmlspecialchars($name) . ',</h2>
                    <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
                    <div style="text-align:center"><a href="' . $resetLink . '" class="button">🔑 Réinitialiser</a></div>
                    <div class="warning">⚠️ Ce lien expire dans 1 heure.</div>
                    <p>Ou copiez ce lien : <a href="' . $resetLink . '">' . $resetLink . '</a></p>
                </div>
                <div class="footer"><p>© 2026 InnoGov</p></div>
            </div>
        </body>
        </html>';
    }
    
    // ==================== EMAIL CONFIRMATION D'INSCRIPTION ====================
    
    public function sendVerificationEmail($toEmail, $toName, $verifLink) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = '✅ Confirmez votre inscription - InnoGov';
            $this->mail->Body = $this->getVerificationTemplate($toName, $verifLink);
            $this->mail->AltBody = "Bonjour $toName,\n\nConfirmez votre inscription : $verifLink\n\nCe lien expire dans 24 heures.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur envoi email vérification: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function getVerificationTemplate($name, $verifLink) {
        return '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>Confirmation inscription - InnoGov</title>
        <style>
            body{font-family:"Segoe UI",Arial;background:#f4f4f4;margin:0;padding:0}
            .container{max-width:600px;margin:20px auto;background:white;border-radius:10px}
            .header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}
            .content{padding:40px 30px}
            .button{display:inline-block;padding:12px 30px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border-radius:5px;margin:20px 0}
            .warning{background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0}
            .footer{background:#f8f9fa;padding:20px;text-align:center;font-size:12px;color:#666}
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header"><h1>✅ Bienvenue sur InnoGov</h1><p>Confirmez votre inscription</p></div>
                <div class="content">
                    <h2>Bonjour ' . htmlspecialchars($name) . ',</h2>
                    <p>Merci de vous être inscrit sur InnoGov !</p>
                    <p>Pour activer votre compte et accéder à tous nos services, veuillez confirmer votre adresse email.</p>
                    <div style="text-align:center"><a href="' . $verifLink . '" class="button">✅ Confirmer mon compte</a></div>
                    <div class="warning">⚠️ Ce lien expire dans 24 heures. Si vous n\'êtes pas à l\'origine de cette inscription, ignorez cet email.</div>
                    <p>Ou copiez ce lien : <a href="' . $verifLink . '">' . $verifLink . '</a></p>
                </div>
                <div class="footer"><p>© 2026 InnoGov - Plateforme de services citoyens</p></div>
            </div>
        </body>
        </html>';
    }
    
    // ==================== EMAIL PRÉ-INSCRIPTION (AVANT ENREGISTREMENT) ====================
    
    public function sendPreRegistrationEmail($toEmail, $toName, $confirmLink) {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = '✅ Confirmez votre inscription - InnoGov';
            $this->mail->Body = $this->getPreRegistrationTemplate($toName, $confirmLink);
            $this->mail->AltBody = "Bonjour $toName,\n\nConfirmez votre inscription : $confirmLink\n\nCe lien expire dans 24 heures.\n\nAucune information n'est enregistrée tant que vous n'avez pas confirmé votre email.";
            
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Erreur envoi email pré-inscription: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    private function getPreRegistrationTemplate($name, $confirmLink) {
        return '
        <!DOCTYPE html>
        <html>
        <head><meta charset="UTF-8"><title>Finalisez votre inscription - InnoGov</title>
        <style>
            body{font-family:"Segoe UI",Arial;background:#f4f4f4;margin:0;padding:0}
            .container{max-width:600px;margin:20px auto;background:white;border-radius:10px}
            .header{background:linear-gradient(135deg,#667eea,#764ba2);color:white;padding:30px;text-align:center;border-radius:10px 10px 0 0}
            .content{padding:40px 30px}
            .button{display:inline-block;padding:12px 30px;background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border-radius:5px;margin:20px 0}
            .warning{background:#fff3cd;border-left:4px solid #ffc107;padding:15px;margin:20px 0}
            .info{background:#e8f4f8;border-left:4px solid #2196F3;padding:15px;margin:20px 0}
            .footer{background:#f8f9fa;padding:20px;text-align:center;font-size:12px;color:#666}
        </style>
        </head>
        <body>
            <div class="container">
                <div class="header"><h1>✅ Finalisez votre inscription</h1><p>InnoGov - Services Citoyens</p></div>
                <div class="content">
                    <h2>Bonjour ' . htmlspecialchars($name) . ',</h2>
                    <p>Merci de vous être inscrit sur InnoGov !</p>
                    <div class="info">
                        <strong>📌 Important :</strong> Aucune information n\'est encore enregistrée. Vous devez confirmer votre email pour finaliser votre inscription.
                    </div>
                    <div style="text-align:center"><a href="' . $confirmLink . '" class="button">✅ Confirmer mon inscription</a></div>
                    <div class="warning">⚠️ Ce lien expire dans 24 heures. Si vous n\'êtes pas à l\'origine de cette inscription, ignorez cet email.</div>
                    <p>Ou copiez ce lien dans votre navigateur :<br><a href="' . $confirmLink . '">' . $confirmLink . '</a></p>
                    <hr style="margin:30px 0">
                    <p style="font-size:12px; color:#888;">Une fois votre email confirmé, votre compte sera automatiquement créé et vous pourrez vous connecter.</p>
                </div>
                <div class="footer"><p>© 2026 InnoGov - Plateforme de services citoyens</p></div>
            </div>
        </body>
        </html>';
    }
}
?>