<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$erreur = '';
$today = date('Y-m-d');
$id = (int) ($_GET['id'] ?? 0);
$emploi = $id ? $ctrl->getEmploiById($id) : null;
$agents = $ctrl->getAgents();
$services = $ctrl->getServices();
$shifts = $ctrl->getShifts();

if (!$emploi) {
    $erreur = theme_t('Emploi non trouve', 'Emploi non trouve');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agent = trim($_POST['id_agent'] ?? '');
    $id_service = trim($_POST['id_service'] ?? '');
    $id_shift = trim($_POST['id_shift'] ?? '');
    $date_travail = trim($_POST['date_travail'] ?? '');
    $statut = trim($_POST['statut'] ?? 'planifie');

    if ($id_agent !== '' && $id_service !== '' && $id_shift !== '' && $date_travail !== '') {
        $idsAgents = array_map('intval', array_column($agents, 'id'));
        $idsServices = array_map('intval', array_column($services, 'id_service'));
        $idsShifts = array_map('intval', array_column($shifts, 'id_shift'));
        $statutsValides = ['planifie', 'termine', 'annule'];

        $agentValide = ctype_digit($id_agent) && in_array((int) $id_agent, $idsAgents, true);
        $serviceValide = ctype_digit($id_service) && in_array((int) $id_service, $idsServices, true);
        $shiftValide = ctype_digit($id_shift) && in_array((int) $id_shift, $idsShifts, true);
        $statutValide = in_array($statut, $statutsValides, true);

        $dateObj = DateTime::createFromFormat('Y-m-d', $date_travail);
        $dateValide = $dateObj && $dateObj->format('Y-m-d') === $date_travail;
        $dateNonPassee = $dateValide && $date_travail >= $today;

        if (!$dateNonPassee) {
            $erreur = theme_t('La date du travail doit etre aujourd hui ou ulterieure', 'La date du travail doit etre aujourd hui ou ulterieure');
        } elseif (!$agentValide || !$serviceValide || !$shiftValide || !$statutValide) {
            $erreur = theme_t('Controle de saisie invalide', 'Controle de saisie invalide');
        } elseif ($ctrl->modifierEmploi($id, (int) $id_agent, (int) $id_service, (int) $id_shift, $date_travail, $statut)) {
            header('Location: ' . theme_url('VIEW/backoffice/admin-emplois-lister.php?toast=' . urlencode(theme_t('Emploi modifie avec succes', 'Emploi modifie avec succes')) . '&type=success'));
            exit();
        } else {
            $erreur = theme_t('Erreur lors de la modification', 'Erreur lors de la modification');
        }
    } else {
        $erreur = theme_t('Tous les champs sont obligatoires', 'Tous les champs sont obligatoires');
    }
}

theme_render_start([
    'title' => theme_t('Modifier un emploi', 'Modifier un emploi'),
    'page_title' => theme_t('Modifier un emploi', 'Modifier un emploi'),
    'page_subtitle' => theme_t('Mettez a jour les affectations et les statuts depuis un formulaire uniforme.', 'Mettez a jour les affectations.'),
    'nav_context' => 'emplois',
    'back_href' => theme_url('VIEW/backoffice/admin-emplois-lister.php'),
]);
?>
<div class="form-panel">
    <h2><?= htmlspecialchars(theme_t('Edition du planning', 'Edition du planning')) ?></h2>

    <?php if ($erreur !== ''): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <?php if ($emploi): ?>
        <form method="POST" class="form-grid">
            <div class="form-group">
                <label for="id_agent"><?= htmlspecialchars(theme_t('Agent', 'Agent')) ?></label>
                <select id="id_agent" name="id_agent" required>
                    <option value=""><?= htmlspecialchars(theme_t('Selectionner un agent', 'Selectionner un agent')) ?></option>
                    <?php foreach ($agents as $agent): ?>
                        <option value="<?= (int) $agent['id'] ?>" <?= (int) $agent['id'] === (int) $emploi['id_agent'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_service"><?= htmlspecialchars(theme_t('Service', 'Service')) ?></label>
                <select id="id_service" name="id_service" required>
                    <option value=""><?= htmlspecialchars(theme_t('Selectionner un service', 'Selectionner un service')) ?></option>
                    <?php foreach ($services as $service): ?>
                        <option value="<?= (int) $service['id_service'] ?>" <?= (int) $service['id_service'] === (int) $emploi['id_service'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($service['nom_service']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group form-group--full">
                <label for="id_shift"><?= htmlspecialchars(theme_t('Shift', 'Shift')) ?></label>
                <select id="id_shift" name="id_shift" required>
                    <option value=""><?= htmlspecialchars(theme_t('Selectionner un shift', 'Selectionner un shift')) ?></option>
                    <?php foreach ($shifts as $shift): ?>
                        <option value="<?= (int) $shift['id_shift'] ?>" <?= (int) $shift['id_shift'] === (int) $emploi['id_shift'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($shift['nom_shift']) ?> - <?= htmlspecialchars(substr($shift['heure_debut'], 0, 5)) ?> / <?= htmlspecialchars(substr($shift['heure_fin'], 0, 5)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="date_travail"><?= htmlspecialchars(theme_t('Date du travail', 'Date du travail')) ?></label>
                <input id="date_travail" type="date" name="date_travail" value="<?= htmlspecialchars($emploi['date_travail']) ?>" min="<?= htmlspecialchars($today) ?>" required>
            </div>

            <div class="form-group">
                <label for="statut"><?= htmlspecialchars(theme_t('Statut', 'Statut')) ?></label>
                <select id="statut" name="statut" required>
                    <option value="planifie" <?= $emploi['statut'] === 'planifie' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Planifie', 'Planifie')) ?></option>
                    <option value="termine" <?= $emploi['statut'] === 'termine' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Termine', 'Termine')) ?></option>
                    <option value="annule" <?= $emploi['statut'] === 'annule' ? 'selected' : '' ?>><?= htmlspecialchars(theme_t('Annule', 'Annule')) ?></option>
                </select>
            </div>

            <div class="form-actions form-group--full">
                <button type="submit" class="btn btn--primary">
                    <i class="fa-solid fa-pen-to-square"></i>
                    <?= htmlspecialchars(theme_t('Enregistrer', 'Enregistrer')) ?>
                </button>
                <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php')) ?>" class="btn btn--ghost">
                    <?= htmlspecialchars(theme_t('Annuler', 'Annuler')) ?>
                </a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php theme_render_end(); ?>
