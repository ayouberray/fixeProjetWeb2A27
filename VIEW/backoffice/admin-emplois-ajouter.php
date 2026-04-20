<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$erreur = '';
$agents = $ctrl->getAgents();
$services = $ctrl->getServices();
$shifts = $ctrl->getShifts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agent = trim($_POST['id_agent'] ?? '');
    $id_service = trim($_POST['id_service'] ?? '');
    $id_shift = trim($_POST['id_shift'] ?? '');
    $date_travail = trim($_POST['date_travail'] ?? '');

    if ($id_agent && $id_service && $id_shift && $date_travail) {
        $idsAgents = array_map('intval', array_column($agents, 'id'));
        $idsServices = array_map('intval', array_column($services, 'id_service'));
        $idsShifts = array_map('intval', array_column($shifts, 'id_shift'));

        $agentValide = ctype_digit($id_agent) && in_array((int) $id_agent, $idsAgents, true);
        $serviceValide = ctype_digit($id_service) && in_array((int) $id_service, $idsServices, true);
        $shiftValide = ctype_digit($id_shift) && in_array((int) $id_shift, $idsShifts, true);

        $dateObj = DateTime::createFromFormat('Y-m-d', $date_travail);
        $dateValide = $dateObj && $dateObj->format('Y-m-d') === $date_travail;

        if (!$agentValide || !$serviceValide || !$shiftValide || !$dateValide) {
            $erreur = theme_t('Controle de saisie invalide', '?????? ??????? ??? ????');
        } else {
            if ($ctrl->ajouterEmploi((int) $id_agent, (int) $id_service, (int) $id_shift, $date_travail)) {
                header('Location: ' . theme_url('VIEW/backoffice/admin-emplois-lister.php?toast=' . urlencode(theme_t('Emploi ajoute avec succes', '??? ????? ?????? ?????')) . '&type=success'));
                exit();
            }

            $erreur = theme_t('Erreur lors de l ajout', '??? ??? ????? ???????');
        }
    } else {
        $erreur = theme_t('Tous les champs sont obligatoires', '?? ?????? ???????');
    }
}

theme_render_start([
    'title' => theme_t('Ajouter un emploi', '????? ????'),
    'page_title' => theme_t('Ajouter un emploi', '????? ????'),
    'page_subtitle' => theme_t('Affectez un agent a un service et un shift avec une interface uniforme.', '???? ???? ??? ???? ??????? ??? ????? ?????.'),
    'nav_context' => 'emplois',
    'back_href' => theme_url('VIEW/backoffice/admin-emplois-lister.php'),
]);
?>
<div class="form-panel">
    <h2><?= htmlspecialchars(theme_t('Creation d un planning', '????? ????')) ?></h2>

    <?php if ($erreur !== ''): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group">
            <label for="id_agent"><?= htmlspecialchars(theme_t('Agent', '?????')) ?></label>
            <select id="id_agent" name="id_agent" required>
                <option value=""><?= htmlspecialchars(theme_t('Selectionner un agent', '???? ????')) ?></option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?= (int) $agent['id'] ?>"><?= htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="id_service"><?= htmlspecialchars(theme_t('Service', '??????')) ?></label>
            <select id="id_service" name="id_service" required>
                <option value=""><?= htmlspecialchars(theme_t('Selectionner un service', '???? ????')) ?></option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= (int) $service['id_service'] ?>"><?= htmlspecialchars($service['nom_service']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group form-group--full">
            <label for="id_shift"><?= htmlspecialchars(theme_t('Shift', '????????')) ?></label>
            <select id="id_shift" name="id_shift" required>
                <option value=""><?= htmlspecialchars(theme_t('Selectionner un shift', '???? ??????')) ?></option>
                <?php foreach ($shifts as $shift): ?>
                    <option value="<?= (int) $shift['id_shift'] ?>">
                        <?= htmlspecialchars($shift['nom_shift']) ?> - <?= htmlspecialchars(substr($shift['heure_debut'], 0, 5)) ?> / <?= htmlspecialchars(substr($shift['heure_fin'], 0, 5)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group form-group--full">
            <label for="date_travail"><?= htmlspecialchars(theme_t('Date du travail', '????? ?????')) ?></label>
            <input id="date_travail" type="date" name="date_travail" required>
        </div>

        <div class="form-actions form-group--full">
            <button type="submit" class="btn btn--primary">
                <i class="fa-solid fa-floppy-disk"></i>
                <?= htmlspecialchars(theme_t('Ajouter', '?????')) ?>
            </button>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php')) ?>" class="btn btn--ghost">
                <?= htmlspecialchars(theme_t('Annuler', '?????')) ?>
            </a>
        </div>
    </form>
</div>
<?php theme_render_end(); ?>

