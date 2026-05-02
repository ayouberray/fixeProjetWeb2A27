<?php
require_once __DIR__ . "/../../MODEL/Candidature.php";
require_once __DIR__ . "/../../MODEL/Offre.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header("Location: index.php"); exit; }

$candidatureModel = new Candidature();
$candidature = $candidatureModel->getById($id);
if (!$candidature) { header("Location: index.php"); exit; }

$offreModel = new Offre();
$offre = $offreModel->getById($candidature['id_offre']);
$pcName = gethostname();
$localUrl = "http://$pcName/ProjettWeb/index.php?controller=candidature&action=badge&id=" . $candidature['id_cond'];
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($localUrl);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation Candidature - InnoGov</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap');

        :root {
            --primary: #006D5B;
            --dark: #1a202c;
            --gray-bg: #f8fafc;
            --green-light: #e6f1ef;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: white;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            color: var(--dark);
        }

        .container {
            width: 100%;
            max-width: 500px;
            padding: 20px;
            text-align: center;
        }

        .avatar-section {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .avatar-icon {
            width: 80px;
            height: 80px;
            background: #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto;
            color: #718096;
            font-size: 40px;
        }

        .greeting {
            font-size: 18px;
            margin-bottom: 15px;
        }

        .greeting strong {
            font-weight: 800;
        }

        .info-text {
            color: #4a5568;
            font-size: 15px;
            line-height: 1.5;
            margin-bottom: 25px;
            padding: 0 10px;
        }
        .details-box {
            background-color: #f0f7f6;
            border-left: 4px solid #006D5B;
            padding: 25px;
            text-align: left;
            margin-bottom: 25px;
            border-radius: 4px;
        }

        .detail-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            font-size: 16px;
        }

        .detail-item:last-child {
            margin-bottom: 0;
        }

        .detail-item .icon {
            margin-right: 12px;
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        .detail-item .label {
            font-weight: 700;
            margin-right: 5px;
        }

        .detail-item .value {
            font-weight: 500;
        }

        .agent-name {
            color: #006D5B;
            font-weight: 700;
        }

        .footer-note {
            color: #718096;
            font-size: 14px;
            line-height: 1.5;
            margin-bottom: 30px;
        }
        .qr-section {
            margin: 20px 0;
            padding: 20px;
            background: white;
            border: 1px dashed #cbd5e0;
            border-radius: 12px;
        }

        .qr-section img {
            width: 180px;
            height: 180px;
            border: 2px solid #006D5B;
            padding: 5px;
            border-radius: 8px;
        }

        .qr-section p {
            font-size: 12px;
            color: #a0aec0;
            margin-top: 10px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .footer-banner {
            background-color: #1a202c;
            color: white;
            padding: 20px;
            border-radius: 8px;
            font-size: 13px;
            margin-top: 20px;
        }

        .footer-banner span {
            color: #48bb78;
            font-weight: 700;
        }

        .actions {
            margin-top: 25px;
            display: flex;
            gap: 10px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            background: white;
            cursor: pointer;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
            color: var(--dark);
            font-size: 14px;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
            border: none;
        }

        @media print {
            .actions { display: none; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="avatar-section">
        <div class="avatar-icon">
            <i class="fas fa-user"></i>
        </div>
    </div>

    <div class="greeting">
        Bonjour <strong><?= htmlspecialchars($candidature['prenom'] . ' ' . $candidature['nom']) ?></strong>,
    </div>

    <div class="info-text">
        Nous vous informons qu'un agent municipal a été affecté pour traiter votre candidature pour le poste de :
    </div>

    <div class="details-box">
        <div class="detail-item">
            <span class="icon">🎫</span>
            <span class="label">N° Candidature :</span>
            <span class="value">#<?= $candidature['id_cond'] ?></span>
        </div>
        <div class="detail-item">
            <span class="icon">🏢</span>
            <span class="label">Municipalité :</span>
            <span class="value"><?= htmlspecialchars($offre['entite'] ?? 'État Civil') ?></span>
        </div>
        <div class="detail-item">
            <span class="icon">📅</span>
            <span class="label">Date :</span>
            <span class="value"><?= date('d/m/Y', strtotime($candidature['date_cond'])) ?> à <?= date('H:i') ?></span>
        </div>
        <div class="detail-item">
            <span class="icon">👤</span>
            <span class="label">Agent en charge :</span>
            <span class="value agent-name">Sami Ben Salem</span>
        </div>
    </div>

    <div class="footer-note">
        Cet agent sera votre interlocuteur principal lors de votre passage à la municipalité pour l'entretien technique.
    </div>
    <div class="qr-section">
        <img src="<?= $qrUrl ?>" alt="QR Code Convocation">
        <p><i class="fas fa-qrcode"></i> Présentez ce code à l'entrée</p>
    </div>

    <div class="footer-banner">
        © 2026 <strong>InnoGov</strong> — <span>Municipalité</span>
    </div>

    <div class="actions">
        <button onclick="generateImage()" class="btn btn-primary"><i class="fas fa-image"></i> Télécharger en Image</button>
        <button onclick="generatePDF()" class="btn"><i class="fas fa-file-pdf"></i> PDF</button>
        <a href="index.php" class="btn"><i class="fas fa-home"></i> Accueil</a>
    </div>
</div>

<script>
function generatePDF() {
    const element = document.querySelector('.container');
    const opt = {
        margin:       10,
        filename:     'Convocation_InnoGov_<?= $candidature['id_cond'] ?>.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    document.querySelector('.actions').style.display = 'none';
    
    html2pdf().set(opt).from(element).save().then(() => {
        document.querySelector('.actions').style.display = 'flex';
    });
}

function generateImage() {
    const element = document.querySelector('.container');
    document.querySelector('.actions').style.display = 'none';

    html2canvas(element, {
        backgroundColor: "#ffffff",
        scale: 2 
    }).then(canvas => {
        const link = document.createElement('a');
        link.download = 'Carte_Identite_InnoGov_<?= $candidature['id_cond'] ?>.png';
        link.href = canvas.toDataURL("image/png");
        link.click();
        
        document.querySelector('.actions').style.display = 'flex';
    });
}
</script>

</body>
</html>
