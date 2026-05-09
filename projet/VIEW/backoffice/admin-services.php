<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/ServiceController.php";
require_once __DIR__."/../../MODEL/config.php";

$serviceController = new ServiceController();
$db = Config::getConnexion();

$success = ""; $error = "";

// Handle POST Actions
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id_s = $_POST['id_service'] ?? 0;

    if ($action == 'add_service' || $action == 'edit_service') {
        $nom = $_POST['nom_service'];
        $desc = $_POST['description'];
        $duree = $_POST['duree_moyenne'];
        $statut = $_POST['statut'] ?? 'actif';
        
        if ($action == 'add_service') {
            $stmt = $db->prepare("INSERT INTO services (nom_service, description, duree_moyenne, statut) VALUES (?, ?, ?, ?)");
            if($stmt->execute([$nom, $desc, $duree, $statut])) $success = "Service ajouté !";
        } else {
            $stmt = $db->prepare("UPDATE services SET nom_service = ?, description = ?, duree_moyenne = ?, statut = ? WHERE id_service = ?");
            if($stmt->execute([$nom, $desc, $duree, $statut, $id_s])) $success = "Service mis à jour !";
        }
    } elseif ($action == 'delete_service') {
        $stmt = $db->prepare("DELETE FROM services WHERE id_service = ?");
        if($stmt->execute([$id_s])) $success = "Service supprimé.";
    }
}

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'nom_asc';
$filter_statut = $_GET['statut'] ?? '';
$list = $serviceController->getAllServices($search, $sort, $filter_statut);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Services | InnoGov</title>
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
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link">Rendez-vous</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-services.php" class="nav-link active">Services</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
            <a href="/Gestion_RDV/projet/index.php" class="nav-link">Espace citoyen</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle"><i class="fas fa-sun"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <button class="nav-cta" onclick="openAddModal()"><i class="fas fa-plus"></i> Nouveau Service</button>
        </div>
    </nav>
</div>

<section class="hero" style="min-height: 40vh;">
    <div class="hero-slideshow"><img src="/Gestion_RDV/projet/assets/images/tunisia2.jpg" class="slide active"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Catalogue des Services</h1>
        <p>Gérez les prestations municipales avec agilité</p>
    </div>
</section>

<div class="futuristic-container">
    <?php if($success): ?><div class="alert alert-success reveal active"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

    <div class="filter-bar reveal active">
        <form method="GET" style="display: flex; gap: 15px; flex: 1; align-items: center;">
            <div style="position: relative; flex: 1;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-500);"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="futuristic-input" placeholder="Rechercher un service..." style="width: 100%; padding-left: 45px;">
            </div>
            <select name="statut" class="futuristic-input">
                <option value="">Tous les statuts</option>
                <option value="actif" <?= $filter_statut == 'actif' ? 'selected' : '' ?>>Actif</option>
                <option value="inactif" <?= $filter_statut == 'inactif' ? 'selected' : '' ?>>Inactif</option>
            </select>
            <button type="submit" class="btn btn-primary" style="padding: 12px 25px;"><i class="fas fa-search"></i> Rechercher</button>
        </form>
    </div>

    <div class="rdv-grid">
        <?php foreach($list as $s): ?>
            <div class="rdv-card reveal" data-service='<?= json_encode($s) ?>'>
                <div class="rdv-header">
                    <div class="rdv-service"><?= htmlspecialchars($s['nom_service']) ?></div>
                    <span class="badge badge-<?= $s['statut'] ?>"><?= ucfirst($s['statut']) ?></span>
                </div>
                <p style="color: var(--gray-600); font-size: 0.9rem; margin: 15px 0; line-height: 1.4;">
                    <?= $s['description'] ?: 'Aucune description.' ?>
                </p>
                <div class="rdv-info"><i class="fas fa-clock"></i> <?= $s['duree_moyenne'] ?> minutes</div>
                <div class="rdv-actions" style="margin-top: 20px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.05);">
                    <button class="btn btn-outline btn-sm" onclick="openEditModal(this)"><i class="fas fa-edit"></i> Modifier</button>
                    <button class="btn btn-outline btn-sm" onclick="openDeleteModal(this)" style="color: var(--danger);"><i class="fas fa-trash"></i> Supprimer</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- MODAL ADD/EDIT -->
<div id="serviceModal" class="premium-modal">
    <div class="modal-3d-box">
        <button class="modal-close-btn" onclick="closePremiumModal('serviceModal')"><i class="fas fa-times"></i></button>
        <h3 class="modal-title-glow" id="modal-title">Nouveau Service</h3>
        <form method="POST">
            <input type="hidden" name="action" id="modal-action" value="add_service">
            <input type="hidden" name="id_service" id="modal-id">
            <div class="form-group">
                <label class="form-label">Nom du Service</label>
                <input type="text" name="nom_service" id="modal-nom" class="glass-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="modal-desc" class="glass-input" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Durée (min)</label>
                <input type="number" name="duree_moyenne" id="modal-duree" class="glass-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="statut" id="modal-statut" class="glass-input">
                    <option value="actif">Actif</option>
                    <option value="inactif">Inactif</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Enregistrer</button>
        </form>
    </div>
</div>

<!-- MODAL DELETE -->
<div id="deleteModal" class="premium-modal">
    <div class="modal-3d-box" style="text-align: center;">
        <button class="modal-close-btn" onclick="closePremiumModal('deleteModal')"><i class="fas fa-times"></i></button>
        <div style="font-size: 50px; color: var(--danger); margin-bottom: 20px;"><i class="fas fa-trash-alt"></i></div>
        <h3 class="modal-title-glow">Supprimer ?</h3>
        <p class="modal-subtitle">Voulez-vous supprimer ce service ?</p>
        <form method="POST">
            <input type="hidden" name="action" value="delete_service">
            <input type="hidden" name="id_service" id="delete-id">
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closePremiumModal('deleteModal')">Non</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--danger);">Oui, supprimer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openAddModal() {
    document.getElementById('modal-title').textContent = "Nouveau Service";
    document.getElementById('modal-action').value = "add_service";
    document.getElementById('modal-nom').value = "";
    document.getElementById('modal-desc').value = "";
    document.getElementById('modal-duree').value = "";
    document.getElementById('modal-statut').value = "actif";
    openPremiumModal('serviceModal');
}
function openEditModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.service);
    document.getElementById('modal-title').textContent = "Modifier Service";
    document.getElementById('modal-action').value = "edit_service";
    document.getElementById('modal-id').value = data.id_service;
    document.getElementById('modal-nom').value = data.nom_service;
    document.getElementById('modal-desc').value = data.description || '';
    document.getElementById('modal-duree').value = data.duree_moyenne;
    document.getElementById('modal-statut').value = data.statut;
    openPremiumModal('serviceModal');
}
function openDeleteModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.service);
    document.getElementById('delete-id').value = data.id_service;
    openPremiumModal('deleteModal');
}
</script>
</body>
</html>
