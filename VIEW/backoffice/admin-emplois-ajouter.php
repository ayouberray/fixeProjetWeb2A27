<?php
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new EmploiController();
$erreur = '';
$today = date('Y-m-d');
$agents = $ctrl->getAgents();
$services = $ctrl->getServices();
$shifts = $ctrl->getShifts();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_agent = trim($_POST['id_agent'] ?? '');
    $id_service = trim($_POST['id_service'] ?? '');
    $id_shift = trim($_POST['id_shift'] ?? '');
    $date_travail = trim($_POST['date_travail'] ?? '');

    if ($id_agent !== '' && $id_service !== '' && $id_shift !== '' && $date_travail !== '') {
        $idsAgents = array_map('intval', array_column($agents, 'id'));
        $idsServices = array_map('intval', array_column($services, 'id_service'));
        $idsShifts = array_map('intval', array_column($shifts, 'id_shift'));

        $agentValide = ctype_digit($id_agent) && in_array((int) $id_agent, $idsAgents, true);
        $serviceValide = ctype_digit($id_service) && in_array((int) $id_service, $idsServices, true);
        $shiftValide = ctype_digit($id_shift) && in_array((int) $id_shift, $idsShifts, true);

        $dateObj = DateTime::createFromFormat('Y-m-d', $date_travail);
        $dateValide = $dateObj && $dateObj->format('Y-m-d') === $date_travail;
        $dateNonPassee = $dateValide && $date_travail >= $today;

        if (!$dateNonPassee) {
            $erreur = theme_t('La date du travail doit etre aujourd hui ou ulterieure', 'La date du travail doit etre aujourd hui ou ulterieure');
        } elseif (!$agentValide || !$serviceValide || !$shiftValide) {
            $erreur = theme_t('Controle de saisie invalide', 'Controle de saisie invalide');
        } elseif ($ctrl->ajouterEmploi((int) $id_agent, (int) $id_service, (int) $id_shift, $date_travail)) {
            header('Location: ' . theme_url('VIEW/backoffice/admin-emplois-lister.php?toast=' . urlencode(theme_t('Emploi ajoute avec succes', 'Emploi ajoute avec succes')) . '&type=success'));
            exit();
        } else {
            $erreur = theme_t('Erreur lors de l ajout', 'Erreur lors de l ajout');
        }
    } else {
        $erreur = theme_t('Tous les champs sont obligatoires', 'Tous les champs sont obligatoires');
    }
}

theme_render_start([
    'title' => theme_t('Ajouter un emploi', 'Ajouter un emploi'),
    'page_title' => theme_t('Ajouter un emploi', 'Ajouter un emploi'),
    'page_subtitle' => theme_t('Affectez un agent a un service et un shift avec une interface uniforme.', 'Affectez un agent a un service et un shift.'),
    'nav_context' => 'emplois',
    'back_href' => theme_url('VIEW/backoffice/admin-emplois-lister.php'),
]);
?>
<div class="form-panel">
    <h2><?= htmlspecialchars(theme_t('Creation d un planning', 'Creation d un planning')) ?></h2>

    <?php if ($erreur !== ''): ?>
        <div class="alert alert--error" id="server-error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <div class="alert alert--error" id="js-error" style="display:none;"></div>

    <form method="POST" class="form-grid" id="form-ajouter-emploi" novalidate>
        <div class="form-group">
            <label for="id_agent">
                <?= htmlspecialchars(theme_t('Agent', 'Agent')) ?>
                <span class="required-star" aria-hidden="true">*</span>
            </label>
            <select id="id_agent" name="id_agent">
                <option value=""><?= htmlspecialchars(theme_t('Selectionner un agent', 'Selectionner un agent')) ?></option>
                <?php foreach ($agents as $agent): ?>
                    <option value="<?= (int) $agent['id'] ?>">
                        <?= htmlspecialchars($agent['nom'] . ' ' . $agent['prenom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="err-id_agent"></small>
        </div>

        <div class="form-group">
            <label for="id_service">
                <?= htmlspecialchars(theme_t('Service', 'Service')) ?>
                <span class="required-star" aria-hidden="true">*</span>
            </label>
            <select id="id_service" name="id_service">
                <option value=""><?= htmlspecialchars(theme_t('Selectionner un service', 'Selectionner un service')) ?></option>
                <?php foreach ($services as $service): ?>
                    <option value="<?= (int) $service['id_service'] ?>"><?= htmlspecialchars($service['nom_service']) ?></option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="err-id_service"></small>
        </div>

        <div class="form-group form-group--full">
            <label for="id_shift">
                <?= htmlspecialchars(theme_t('Shift', 'Shift')) ?>
                <span class="required-star" aria-hidden="true">*</span>
            </label>
            <select id="id_shift" name="id_shift">
                <option value=""><?= htmlspecialchars(theme_t('Selectionner un shift', 'Selectionner un shift')) ?></option>
                <?php foreach ($shifts as $shift): ?>
                    <option value="<?= (int) $shift['id_shift'] ?>">
                        <?= htmlspecialchars($shift['nom_shift']) ?> - <?= htmlspecialchars(substr($shift['heure_debut'], 0, 5)) ?> / <?= htmlspecialchars(substr($shift['heure_fin'], 0, 5)) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small class="field-error" id="err-id_shift"></small>
        </div>

        <div class="form-group form-group--full">
            <label for="date_travail">
                <?= htmlspecialchars(theme_t('Date du travail', 'Date du travail')) ?>
                <span class="required-star" aria-hidden="true">*</span>
            </label>
            <input id="date_travail" type="date" name="date_travail" min="<?= htmlspecialchars($today) ?>">
            <small class="field-error" id="err-date_travail"></small>
        </div>

        <div class="form-actions form-group--full">
            <button type="submit" class="btn btn--primary" id="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i>
                <?= htmlspecialchars(theme_t('Ajouter', 'Ajouter')) ?>
            </button>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php')) ?>" class="btn btn--ghost">
                <?= htmlspecialchars(theme_t('Annuler', 'Annuler')) ?>
            </a>
        </div>
    </form>
</div>

<style>
.field-error {
    display: block;
    min-height: 1.1em;
    margin-top: 0.3rem;
    font-size: 0.8rem;
    color: #f87171;
    font-weight: 500;
}
.required-star {
    color: #f87171;
    margin-left: 2px;
}
select.is-invalid,
input.is-invalid {
    border-color: #f87171 !important;
    box-shadow: 0 0 0 2px rgba(248, 113, 113, 0.2);
}
select.is-valid,
input.is-valid {
    border-color: #4ade80 !important;
    box-shadow: 0 0 0 2px rgba(74, 222, 128, 0.15);
}
</style>

<script>
(function () {
    'use strict';

    var form = document.getElementById('form-ajouter-emploi');
    var jsError = document.getElementById('js-error');
    var btnSubmit = document.getElementById('btn-submit');
    var today = '<?= $today ?>';

    function showFieldError(fieldId, message) {
        var el = document.getElementById('err-' + fieldId);
        var input = document.getElementById(fieldId);
        if (el) el.textContent = message;
        if (input) {
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
        }
    }

    function clearFieldError(fieldId) {
        var el = document.getElementById('err-' + fieldId);
        var input = document.getElementById(fieldId);
        if (el) el.textContent = '';
        if (input) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
        }
    }

    function validateSelect(fieldId, emptyMessage, invalidMessage) {
        var val = document.getElementById(fieldId).value;
        if (!val) {
            showFieldError(fieldId, emptyMessage);
            return false;
        }
        if (!/^\d+$/.test(val) || parseInt(val, 10) <= 0) {
            showFieldError(fieldId, invalidMessage);
            return false;
        }
        clearFieldError(fieldId);
        return true;
    }

    function validateDate() {
        var val = document.getElementById('date_travail').value;
        if (!val) {
            showFieldError('date_travail', 'La date du travail est obligatoire.');
            return false;
        }
        if (!/^\d{4}-\d{2}-\d{2}$/.test(val) || val < today) {
            showFieldError('date_travail', 'La date doit etre aujourd hui ou ulterieure.');
            return false;
        }
        clearFieldError('date_travail');
        return true;
    }

    document.getElementById('id_agent').addEventListener('change', function () {
        validateSelect('id_agent', 'Veuillez selectionner un agent.', 'Agent invalide.');
    });
    document.getElementById('id_service').addEventListener('change', function () {
        validateSelect('id_service', 'Veuillez selectionner un service.', 'Service invalide.');
    });
    document.getElementById('id_shift').addEventListener('change', function () {
        validateSelect('id_shift', 'Veuillez selectionner un shift.', 'Shift invalide.');
    });
    document.getElementById('date_travail').addEventListener('change', validateDate);
    document.getElementById('date_travail').addEventListener('input', validateDate);

    form.addEventListener('submit', function (event) {
        var okAgent = validateSelect('id_agent', 'Veuillez selectionner un agent.', 'Agent invalide.');
        var okService = validateSelect('id_service', 'Veuillez selectionner un service.', 'Service invalide.');
        var okShift = validateSelect('id_shift', 'Veuillez selectionner un shift.', 'Shift invalide.');
        var okDate = validateDate();

        if (!okAgent || !okService || !okShift || !okDate) {
            event.preventDefault();
            jsError.textContent = 'Veuillez corriger les erreurs ci-dessus avant de soumettre.';
            jsError.style.display = 'block';
            jsError.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            return;
        }

        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Enregistrement...';
    });
})();
</script>
<?php theme_render_end(); ?>
