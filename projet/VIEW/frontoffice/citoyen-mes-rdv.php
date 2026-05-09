<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
if(!isset($_SESSION['user_id'])){
    $_SESSION['user_id'] = 2;
    $_SESSION['user_nom'] = "Ben Ali";
    $_SESSION['user_prenom'] = "Mohamed";
}
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";

$rdvController = new RendezVousController();
$db = Config::getConnexion();
$citoyen_nom = $_SESSION['user_prenom'] . ' ' . $_SESSION['user_nom'];

$success = ""; $error = "";

// Handle POST Actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id_rdv = $_POST['id_rdv'] ?? 0;

    if ($action == 'edit_rdv') {
        $id_service = $_POST['id_service'];
        $date_heure = $_POST['date_heure'];
        $motif = $_POST['motif'];
        
        $stmt = $db->prepare("UPDATE rendez_vous SET id_service = ?, date_heure = ?, motif = ? WHERE id_rdv = ?");
        if($stmt->execute([$id_service, $date_heure, $motif, $id_rdv])) {
            $success = "Rendez-vous modifié avec succès !";
        }
    } elseif ($action == 'cancel_rdv') {
        if ($rdvController->annulerRendezVous($id_rdv)) {
            $success = "Rendez-vous annulé.";
        }
    }
}

$services = $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif'")->fetchAll();
$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$filter_statut = $_GET['statut'] ?? '';
$list = $rdvController->getRendezVousByCitoyen($citoyen_nom, $search, $sort, $filter_statut);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes RDV | InnoGov</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800&family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/Gestion_RDV/projet/assets/css/style.css?v=20260509_v9">
    <script src="/Gestion_RDV/projet/assets/js/script.js" defer></script>
</head>
<body>

<div class="loader"><div class="spinner"></div></div>

<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>
            </div>
        </a>
        <div class="nav-menu">
            <a href="/Gestion_RDV/projet/index.php" class="nav-link">Accueil</a>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php" class="nav-link active">Mes RDV</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Admin</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle"><i class="fas fa-sun"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta"><i class="fas fa-calendar-plus"></i> Prendre RDV</a>
        </div>
    </nav>
</div>

<section class="hero" style="min-height: 40vh;">
    <div class="hero-slideshow"><img src="/Gestion_RDV/projet/assets/images/tunisia3.jpg" class="slide active"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Mon Espace Citoyen</h1>
        <p>Gérez vos rendez-vous en toute autonomie</p>
    </div>
</section>

<div class="futuristic-container">
    <?php if($success): ?><div class="alert alert-success reveal active"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

    <div class="filter-bar reveal active">
        <form method="GET" style="display: flex; gap: 15px; flex: 1; align-items: center;">
            <div style="position: relative; flex: 2;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-500);"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="futuristic-input" placeholder="Rechercher par service, ID..." style="width: 100%; padding-left: 45px;">
            </div>
            
            <select name="sort" class="futuristic-input" style="flex: 1;">
                <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Plus récent</option>
                <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Plus ancien</option>
                <option value="service_asc" <?= $sort == 'service_asc' ? 'selected' : '' ?>>Service (A-Z)</option>
            </select>

            <select name="statut" class="futuristic-input" style="flex: 1;">
                <option value="">Tous les statuts</option>
                <option value="en_attente" <?= $filter_statut == 'en_attente' ? 'selected' : '' ?>>En attente</option>
                <option value="confirme" <?= $filter_statut == 'confirme' ? 'selected' : '' ?>>Confirmé</option>
                <option value="termine" <?= $filter_statut == 'termine' ? 'selected' : '' ?>>Terminé</option>
                <option value="annule" <?= $filter_statut == 'annule' ? 'selected' : '' ?>>Annulé</option>
            </select>

            <button type="submit" class="btn btn-primary" style="padding: 12px 25px;"><i class="fas fa-filter"></i> Filtrer</button>
        </form>
    </div>

    <div class="rdv-grid">
        <?php foreach($list as $rdv): ?>
            <div class="rdv-card reveal" data-rdv='<?= json_encode($rdv) ?>'>
                <div class="rdv-header">
                    <div class="rdv-service"><?= htmlspecialchars($rdv['service_nom']) ?></div>
                    <span class="badge badge-<?= $rdv['statut'] ?>"><?= ucfirst($rdv['statut']) ?></span>
                </div>
                <div class="rdv-info"><i class="fas fa-clock"></i> <?= date('d/m/Y - H:i', strtotime($rdv['date_heure'])) ?></div>
                <div class="rdv-agent-box">
                    <i class="fas fa-user-tie"></i> <?= $rdv['agent_nom'] ?: 'En attente d\'affectation' ?>
                </div>
                <div class="rdv-actions" style="margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(0,0,0,0.05); display: flex; gap: 10px;">
                    <?php if($rdv['statut'] == 'en_attente'): ?>
                        <button class="btn-rdv-action btn-rdv-edit" onclick="openEditModal(this)" style="flex: 1; padding: 12px;">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button class="btn-rdv-action btn-rdv-delete" onclick="openCancelModal(this)" style="flex: 1; padding: 12px;">
                            <i class="fas fa-trash-alt"></i> Supprimer
                        </button>
                    <?php else: ?>
                        <div style="width: 100%; text-align: center; font-size: 0.85rem; color: var(--gray-500); font-style: italic; background: var(--bg-page); padding: 10px; border-radius: 10px;">
                            <i class="fas fa-lock"></i> Modifications closes pour ce statut
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="editModal" class="premium-modal">
    <div class="modal-3d-box">
        <button class="modal-close-btn" onclick="closePremiumModal('editModal')"><i class="fas fa-times"></i></button>
        <h3 class="modal-title-glow">Modifier RDV</h3>
        <form method="POST">
            <input type="hidden" name="action" value="edit_rdv">
            <input type="hidden" name="id_rdv" id="edit-id">
            <div class="form-group">
                <label class="form-label">Service</label>
                <select name="id_service" id="edit-service" class="glass-input">
                    <?php foreach($services as $s): ?>
                        <option value="<?= $s['id_service'] ?>"><?= htmlspecialchars($s['nom_service']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nouvelle Date & Heure</label>
                <input type="datetime-local" name="date_heure" id="edit-date" class="glass-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Motif</label>
                <textarea name="motif" id="edit-motif" class="glass-input" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Enregistrer</button>
        </form>
    </div>
</div>

<!-- MODAL CANCEL -->
<div id="cancelModal" class="premium-modal">
    <div class="modal-3d-box" style="text-align: center;">
        <button class="modal-close-btn" onclick="closePremiumModal('cancelModal')"><i class="fas fa-times"></i></button>
        <div style="font-size: 50px; color: var(--danger); margin-bottom: 20px;"><i class="fas fa-calendar-times"></i></div>
        <h3 class="modal-title-glow">Annuler ?</h3>
        <p class="modal-subtitle">Souhaitez-vous vraiment annuler ce rendez-vous ?</p>
        <form method="POST">
            <input type="hidden" name="action" value="cancel_rdv">
            <input type="hidden" name="id_rdv" id="cancel-id">
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closePremiumModal('cancelModal')">Non</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--danger);">Oui, annuler</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.rdv);
    document.getElementById('edit-id').value = data.id_rdv;
    document.getElementById('edit-service').value = data.id_service;
    document.getElementById('edit-date').value = data.date_heure.replace(' ', 'T');
    document.getElementById('edit-motif').value = data.motif || '';
    openPremiumModal('editModal');
}
function openCancelModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.rdv);
    document.getElementById('cancel-id').value = data.id_rdv;
    openPremiumModal('cancelModal');
}
</script>
</body>
</html>
