<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$token = trim($_GET['t'] ?? '');
$emploi = $ctrl->getEmploiByQrToken($token);

// Fonction pour l'URL de l'image QR (identique à celle du lister)
function qr_page_image_url($emploi, $size = 140) {
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = theme_url('VIEW/frontoffice/emploi-qr.php?t=' . rawurlencode($emploi['qr_token'] ?? ''));
    $url = $scheme . '://' . $host . $path;
    return 'https://api.qrserver.com/v1/create-qr-code/?size=' . (int) $size . 'x' . (int) $size . '&data=' . rawurlencode($url);
}

theme_render_start([
    'title' => theme_t('Pass Emploi - InnoGov', 'بطاقة العمل - إنوجوف'),
    'show_hero' => false,
    'show_loader' => false, // Désactiver le loader pour éviter les écrans blancs sur mobile
    'page_class' => 'page-shell--ticket',
    'body_class' => 'body--ticket',
]);
?>

<div class="ticket-layout">
    <!-- Top Bar style "Example" -->
    <header class="ticket-navbar">
        <div class="ticket-navbar__brand">
            <i class="fa-solid fa-building-columns"></i>
            <span>InnoGov</span>
        </div>
        <div class="ticket-navbar__link">
            <i class="fa-solid fa-clipboard-list"></i>
            <span><?= htmlspecialchars(theme_t('Planning', 'الجدول')) ?></span>
        </div>
    </header>

    <main class="ticket-main">
        <div class="ticket-hero-section">
            <a href="<?= theme_url('VIEW/frontoffice/index.php') ?>" class="ticket-back">
                <i class="fa-solid fa-chevron-left"></i>
                <?= htmlspecialchars(theme_t('Retour à l\'accueil', 'العودة للرئيسية')) ?>
            </a>
        </div>

        <div class="ticket-container">
            <?php if (!$emploi): ?>
                <div class="ticket-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <h2><?= htmlspecialchars(theme_t('Pass Invalide', 'بطاقة غير صالحة')) ?></h2>
                    <p><?= htmlspecialchars(theme_t('Ce code QR ne correspond à aucun emploi valide.', 'هذا الكود لا يتوافق مع أي وظيفة.')) ?></p>
                </div>
            <?php else: ?>
                <div class="ticket-card">
                    <div class="ticket-card__header">
                        <div class="ticket-card__title">
                             <i class="fa-solid fa-comment-dots"></i>
                             <h2>Emploi #<?= str_pad((string) $emploi['id_emploi'], 5, '0', STR_PAD_LEFT) ?></h2>
                        </div>
                        <div class="ticket-card__qr-wrap">
                            <div class="ticket-card__qr-box">
                                <div id="qrcode"></div>
                                <span>QR TICKET</span>
                            </div>
                        </div>
                    </div>

                    <div class="ticket-card__body">
                        <div class="ticket-info">
                            <div class="ticket-info__group">
                                <span><?= htmlspecialchars(theme_t('Agent', 'الوكيل')) ?></span>
                                <strong><?= htmlspecialchars(($emploi['agent_nom'] ?? 'N/A') . ' ' . ($emploi['agent_prenom'] ?? '')) ?></strong>
                            </div>
                            <div class="ticket-info__group">
                                <span><?= htmlspecialchars(theme_t('Service', 'الخدمة')) ?></span>
                                <strong><?= htmlspecialchars($emploi['nom_service'] ?? 'N/A') ?></strong>
                            </div>
                            <div class="ticket-info__group">
                                <span><?= htmlspecialchars(theme_t('Date', 'التاريخ')) ?></span>
                                <strong><?= htmlspecialchars(date('d/m/Y', strtotime($emploi['date_travail']))) ?></strong>
                            </div>
                            <div class="ticket-info__group">
                                <span><?= htmlspecialchars(theme_t('Horaire', 'التوقيت')) ?></span>
                                <strong><?= htmlspecialchars(substr($emploi['heure_debut'] ?? '00:00', 0, 5)) ?> - <?= htmlspecialchars(substr($emploi['heure_fin'] ?? '00:00', 0, 5)) ?></strong>
                            </div>
                        </div>
                        
                        <div class="ticket-status-row">
                             <span class="badge <?= $emploi['statut'] === 'termine' ? 'badge--success' : ($emploi['statut'] === 'annule' ? 'badge--danger' : 'badge--warning') ?>">
                                 <?= htmlspecialchars(theme_t(ucfirst($emploi['statut']), $emploi['statut'])) ?>
                             </span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Génération du QR Code selon l'exemple de votre ami
    <?php if ($emploi): 
        $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        // Utiliser l'URL complète pour que le scan fonctionne
        $fullUrl = $scheme . '://' . $host . theme_url('VIEW/frontoffice/emploi-qr.php?t=' . rawurlencode($emploi['qr_token'] ?? ''));
    ?>
    new QRCode(document.getElementById("qrcode"), {
        text: "<?= $fullUrl ?>",
        width: 100,
        height: 100,
        colorDark : "#006D5B",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.M
    });
    <?php endif; ?>
</script>

<style>
.body--ticket {
    background: #f8fafc !important;
    margin: 0;
}

.ticket-layout {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

.ticket-navbar {
    background: #fff;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
}

.ticket-navbar__brand {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #0f172a;
    font-weight: 800;
    font-size: 1.1rem;
}

.ticket-navbar__brand i {
    color: var(--primary);
}

.ticket-navbar__link {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #059669;
    font-weight: 700;
    font-size: 0.9rem;
}

.ticket-main {
    flex: 1;
    background: #065f46; /* Dark Green like example */
}

.ticket-hero-section {
    padding: 20px;
}

.ticket-back {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.ticket-container {
    padding: 0 20px 40px;
    display: flex;
    justify-content: center;
}

.ticket-card {
    background: #064e3b; /* Darker teal like example */
    width: 100%;
    max-width: 500px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 10px 25px rgba(0,0,0,0.2);
    color: #fff;
}

.ticket-card__header {
    padding: 25px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.ticket-card__title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.ticket-card__title i {
    font-size: 1.5rem;
    opacity: 0.8;
}

.ticket-card__title h2 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 700;
}

.ticket-card__qr-wrap {
    flex-shrink: 0;
}

.ticket-card__qr-box {
    background: #fff;
    padding: 8px;
    border-radius: 8px;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
}

.ticket-card__qr-box div {
    width: 100px;
    height: 100px;
}

.ticket-card__qr-box div img {
    width: 100%;
    height: 100%;
}

.ticket-card__qr-box span {
    color: #000;
    font-size: 0.6rem;
    font-weight: 800;
}

.ticket-card__body {
    padding: 0 25px 25px;
}

.ticket-info {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
    margin-bottom: 25px;
}

.ticket-info__group span {
    display: block;
    font-size: 0.7rem;
    color: rgba(255, 255, 255, 0.6);
    text-transform: uppercase;
    font-weight: 700;
    margin-bottom: 4px;
}

.ticket-info__group strong {
    font-size: 0.95rem;
}

.ticket-status-row {
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    padding-top: 20px;
}

.ticket-error {
    text-align: center;
    color: #fff;
    padding: 40px 20px;
}

.ticket-error i {
    font-size: 3rem;
    margin-bottom: 20px;
    color: #f87171;
}

@media (max-width: 480px) {
    .ticket-info { grid-template-columns: 1fr; }
}
</style>

<?php theme_render_end(['show_footer' => false]); ?>

