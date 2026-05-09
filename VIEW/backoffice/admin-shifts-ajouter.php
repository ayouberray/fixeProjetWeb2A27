<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../shared/theme.php';

$ctrl = new ShiftController();
$erreur = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom_shift'] ?? '');
    $debut = trim($_POST['heure_debut'] ?? '');
    $fin = trim($_POST['heure_fin'] ?? '');
    $heurePattern = '/^([01]\d|2[0-3]):[0-5]\d$/';

    if ($nom !== '' && $debut !== '' && $fin !== '') {
        if (!preg_match($heurePattern, $debut) || !preg_match($heurePattern, $fin)) {
            $erreur = theme_t('Format heure invalide', 'تنسيق ساعة غير صالح');
        } elseif ($debut >= $fin) {
            $erreur = theme_t('Heure debut doit etre inferieure a heure fin', 'ساعة البداية يجب ان تكون اقل من ساعة النهاية');
        } elseif ($ctrl->ajouterShift($nom, $debut, $fin)) {
            header('Location: ' . theme_url('VIEW/backoffice/admin-shifts-lister.php?toast=' . urlencode(theme_t('Shift ajoute avec succes', 'تمت اضافة المناوبة بنجاح')) . '&type=success'));
            exit();
        } else {
            $erreur = theme_t('Erreur lors de l ajout', 'خطأ اثناء الاضافة');
        }
    } else {
        $erreur = theme_t('Tous les champs sont obligatoires', 'كل الحقول مطلوبة');
    }
}

theme_render_start([
    'title' => theme_t('Ajouter un shift', 'اضافة مناوبة'),
    'page_title' => theme_t('Ajouter un shift', 'اضافة مناوبة'),
    'page_subtitle' => theme_t('Utilisez ce formulaire pour creer une nouvelle tranche horaire.', 'استخدم هذا النموذج لانشاء فترة عمل جديدة.'),
    'nav_context' => 'shifts',
    'back_href' => theme_url('VIEW/backoffice/admin-shifts-lister.php'),
]);
?>
<div class="form-panel">
    <h2><?= htmlspecialchars(theme_t('Formulaire de creation', 'نموذج الانشاء')) ?></h2>

    <?php if ($erreur !== ''): ?>
        <div class="alert alert--error"><?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>

    <form method="POST" class="form-grid">
        <div class="form-group form-group--full">
            <label for="nom_shift"><?= htmlspecialchars(theme_t('Nom du shift', 'اسم المناوبة')) ?></label>
            <input id="nom_shift" type="text" name="nom_shift" placeholder="<?= htmlspecialchars(theme_t('Ex : Matin', 'مثال: صباح')) ?>" required>
        </div>
        <div class="form-group">
            <label for="heure_debut"><?= htmlspecialchars(theme_t('Heure debut', 'ساعة البداية')) ?></label>
            <input id="heure_debut" type="time" name="heure_debut" required>
        </div>
        <div class="form-group">
            <label for="heure_fin"><?= htmlspecialchars(theme_t('Heure fin', 'ساعة النهاية')) ?></label>
            <input id="heure_fin" type="time" name="heure_fin" required>
        </div>
        <div class="form-actions form-group--full">
            <button type="submit" class="btn btn--primary">
                <i class="fa-solid fa-floppy-disk"></i>
                <?= htmlspecialchars(theme_t('Ajouter', 'اضافة')) ?>
            </button>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-lister.php')) ?>" class="btn btn--ghost">
                <?= htmlspecialchars(theme_t('Annuler', 'الغاء')) ?>
            </a>
        </div>
    </form>
</div>
<?php theme_render_end(); ?>
