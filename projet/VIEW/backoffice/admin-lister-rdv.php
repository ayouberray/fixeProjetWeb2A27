<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once __DIR__."/../../CONTROLLER/RendezVousController.php";
require_once __DIR__."/../../MODEL/config.php";
require_once __DIR__."/../../api/mail_sender.php";

$rdvController = new RendezVousController();
$db = Config::getConnexion();

// Fetch Agents & Services for Modals
$agentsList = $db->query("SELECT id, CONCAT(prenom, ' ', nom) as nom_complet FROM users WHERE role = 'agent'")->fetchAll();
$servicesList = $db->query("SELECT id_service, nom_service FROM services WHERE statut = 'actif'")->fetchAll();

$success = ""; $error = "";

// Handle POST Actions (Modals)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];
    $id_rdv = $_POST['id_rdv'] ?? 0;

    if ($action == 'assign_agent') {
        $agent_nom = $_POST['agent_nom'];
        if ($rdvController->affecterAgent($id_rdv, $agent_nom)) {
            $success = "Agent affecté avec succès !";
            // Notify Citoyen logic here...
        }
    } elseif ($action == 'edit_rdv') {
        $citoyen_nom = $_POST['citoyen_nom'];
        $id_service = $_POST['id_service'];
        $date_heure = $_POST['date_heure'];
        $statut = $_POST['statut'];
        $motif = $_POST['motif'];
        if ($rdvController->adminModifierRendezVous($id_rdv, $citoyen_nom, $id_service, $date_heure, $statut, $motif)) {
            $success = "Rendez-vous mis à jour !";
        }
    } elseif ($action == 'delete_rdv') {
        if ($rdvController->adminSupprimerRendezVous($id_rdv)) {
            $success = "Rendez-vous supprimé.";
        }
    }
}

$search = $_GET['search'] ?? '';
$sort = $_GET['sort'] ?? 'date_desc';
$filter_statut = $_GET['statut'] ?? '';
$list = $rdvController->getAllRendezVous($search, $sort, $filter_statut);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin RDV | InnoGov</title>
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
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php" class="nav-link active">Rendez-vous</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-services.php" class="nav-link">Services</a>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-stats-rdv.php" class="nav-link">Statistiques</a>
            <a href="/Gestion_RDV/projet/index.php" class="nav-link">Espace citoyen</a>
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle"><i class="fas fa-sun"></i></button>
            <div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>
            <a href="/Gestion_RDV/projet/VIEW/backoffice/admin-ajouter-rdv.php" class="nav-cta"><i class="fas fa-plus"></i> Nouveau RDV</a>
        </div>
    </nav>
</div>

<section class="hero" style="min-height: 40vh;">
    <div class="hero-slideshow"><img src="/Gestion_RDV/projet/assets/images/tunisia1.jpg" class="slide active"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Gestion des Rendez-vous</h1>
        <p>Interface administrative haute performance</p>
    </div>
</section>

<div class="futuristic-container">
    <?php if($success): ?><div class="alert alert-success reveal active"><i class="fas fa-check-circle"></i> <?= $success ?></div><?php endif; ?>

    <div class="filter-bar reveal active">
        <form method="GET" style="display: flex; gap: 15px; flex: 1; align-items: center;">
            <div style="position: relative; flex: 1;">
                <i class="fas fa-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--gray-500);"></i>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="futuristic-input" placeholder="Rechercher Citoyen, ID, Service..." style="width: 100%; padding-left: 45px;">
            </div>
            <select name="sort" class="futuristic-input">
                <option value="date_desc" <?= $sort == 'date_desc' ? 'selected' : '' ?>>Plus récent</option>
                <option value="date_asc" <?= $sort == 'date_asc' ? 'selected' : '' ?>>Plus ancien</option>
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
                <div class="rdv-citoyen"><i class="fas fa-user"></i> <?= htmlspecialchars($rdv['citoyen_nom']) ?></div>
                <div class="rdv-info"><i class="fas fa-calendar"></i> <?= date('d/m/Y - H:i', strtotime($rdv['date_heure'])) ?></div>
                <div class="rdv-agent-box">
                    <i class="fas fa-user-tie"></i> <?= $rdv['agent_nom'] ?: 'Non assigné' ?>
                </div>
                <div class="rdv-actions">
                    <button class="btn-rdv-action btn-rdv-edit" onclick="openEditModal(this)"><i class="fas fa-edit"></i> Edit</button>
                    <button class="btn-rdv-action btn-rdv-assign" onclick="openAssignModal(this)"><i class="fas fa-user-plus"></i> Assign</button>
                    <button class="btn-rdv-action btn-rdv-delete" onclick="openDeleteModal(this)"><i class="fas fa-trash"></i> Del</button>
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
                <label class="form-label">Citoyen</label>
                <input type="text" name="citoyen_nom" id="edit-citoyen" class="glass-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Service</label>
                <select name="id_service" id="edit-service" class="glass-input">
                    <?php foreach($servicesList as $s): ?>
                        <option value="<?= $s['id_service'] ?>"><?= htmlspecialchars($s['nom_service']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Date & Heure</label>
                <input type="datetime-local" name="date_heure" id="edit-date" class="glass-input" required>
            </div>
            <div class="form-group">
                <label class="form-label">Statut</label>
                <select name="statut" id="edit-statut" class="glass-input">
                    <option value="en_attente">En attente</option>
                    <option value="confirme">Confirmé</option>
                    <option value="termine">Terminé</option>
                    <option value="annule">Annulé</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Motif</label>
                <textarea name="motif" id="edit-motif" class="glass-input" rows="2"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Mettre à jour</button>
        </form>
    </div>
</div>

<!-- MODAL ASSIGN -->
<div id="assignModal" class="premium-modal">
    <div class="modal-3d-box">
        <button class="modal-close-btn" onclick="closePremiumModal('assignModal')"><i class="fas fa-times"></i></button>
        <h3 class="modal-title-glow">Affecter Agent</h3>
        <form method="POST">
            <input type="hidden" name="action" value="assign_agent">
            <input type="hidden" name="id_rdv" id="assign-id">
            <div class="form-group">
                <label class="form-label">Agent Municipal</label>
                <select name="agent_nom" class="glass-input" required>
                    <option value="" disabled selected>-- Choisir un agent --</option>
                    <?php foreach($agentsList as $a): ?>
                        <option value="<?= htmlspecialchars($a['nom_complet']) ?>"><?= htmlspecialchars($a['nom_complet']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width: 100%;">Confirmer l'affectation</button>
        </form>
    </div>
</div>

<!-- MODAL DELETE -->
<div id="deleteModal" class="premium-modal">
    <div class="modal-3d-box" style="text-align: center;">
        <button class="modal-close-btn" onclick="closePremiumModal('deleteModal')"><i class="fas fa-times"></i></button>
        <div style="font-size: 50px; color: var(--danger); margin-bottom: 20px;"><i class="fas fa-exclamation-triangle"></i></div>
        <h3 class="modal-title-glow">Supprimer ?</h3>
        <p class="modal-subtitle">Cette action est irréversible. Voulez-vous continuer ?</p>
        <form method="POST">
            <input type="hidden" name="action" value="delete_rdv">
            <input type="hidden" name="id_rdv" id="delete-id">
            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="button" class="btn btn-outline" style="flex: 1;" onclick="closePremiumModal('deleteModal')">Annuler</button>
                <button type="submit" class="btn btn-primary" style="flex: 1; background: var(--danger);">Supprimer</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.rdv);
    document.getElementById('edit-id').value = data.id_rdv;
    document.getElementById('edit-citoyen').value = data.citoyen_nom;
    document.getElementById('edit-service').value = data.id_service;
    document.getElementById('edit-date').value = data.date_heure.replace(' ', 'T');
    document.getElementById('edit-statut').value = data.statut;
    document.getElementById('edit-motif').value = data.motif || '';
    openPremiumModal('editModal');
}
function openAssignModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.rdv);
    document.getElementById('assign-id').value = data.id_rdv;
    openPremiumModal('assignModal');
}
function openDeleteModal(btn) {
    const data = JSON.parse(btn.closest('.rdv-card').dataset.rdv);
    document.getElementById('delete-id').value = data.id_rdv;
    openPremiumModal('deleteModal');
}
</script>
</body>
</html>
