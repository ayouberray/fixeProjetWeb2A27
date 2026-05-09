<?php
require_once __DIR__ . '/../../CONTROLLER/ShiftController.php';
require_once __DIR__ . '/../../CONTROLLER/EmploiController.php';
require_once __DIR__ . '/../shared/theme.php';

$shiftController = new ShiftController();
$emploiController = new EmploiController();

$shifts = $shiftController->getAllShifts();
$emplois = $emploiController->getAllEmplois();
$services = $emploiController->getServices();
$emploisRecents = array_slice($emplois, 0, 3);

$actualites = [
    [
        'titre' => theme_t('Optimisation des equipes terrain', 'تحسين فرق الميدان'),
        'date' => '13/04/2026',
        'extrait' => theme_t('Une meilleure repartition des shifts permet de fluidifier l accueil et les interventions municipales.', 'توزيع المناوبات بشكل افضل يجعل الاستقبال والتدخلات البلدية اكثر سلاسة.'),
        'image' => theme_url('assets/images/news/news1.jpg'),
    ],
    [
        'titre' => theme_t('Nouveau suivi des plannings', 'متابعة جديدة للجداول'),
        'date' => '11/04/2026',
        'extrait' => theme_t('Les responsables disposent maintenant d un tableau central pour suivre les affectations et les statuts.', 'اصبح لدى المسؤولين جدول مركزي لمتابعة التعيينات والحالات.'),
        'image' => theme_url('assets/images/news/news2.jpg'),
    ],
    [
        'titre' => theme_t('Vision moderne du backoffice', 'رؤية حديثة للوحة الادارة'),
        'date' => '09/04/2026',
        'extrait' => theme_t('Le portail adopte une experience plus lisible, rapide et homogene sur mobile et desktop.', 'البوابة تقدم تجربة اوضح واسرع ومتناسقة على الهاتف والحاسوب.'),
        'image' => theme_url('assets/images/news/news3.jpg'),
    ],
];

theme_render_start([
    'title' => theme_t('Accueil | Gestion des emplois', 'الرئيسية | ادارة الجداول'),
    'page_title' => theme_t('Portail de gestion des emplois et shifts', 'بوابة ادارة الجداول والمناوبات'),
    'page_subtitle' => theme_t('Une interface moderne pour piloter les horaires, les affectations et la coordination de service.', 'واجهة حديثة لادارة المواقيت والتعيينات وتنسيق الخدمات.'),
    'background' => 'video',
    'show_hero' => false,
    'nav_context' => 'home',
    'body_class' => 'home-page',
    'page_class' => 'page-shell--home',
    'content_class' => 'content-card--home',
]);
?>
<section class="home-hero reveal">
    <div class="home-hero__inner">
        <h1><?= htmlspecialchars(theme_t('Services Municipaux Digitalises', 'الخدمات البلدية الرقمية')) ?></h1>
        <p><?= htmlspecialchars(theme_t('Simplifiez la gestion des shifts et des emplois dans une interface claire et moderne.', 'قم بتبسيط ادارة المناوبات والجداول في واجهة حديثة وواضحة.')) ?></p>
        <div class="home-hero__actions">
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-lister.php')) ?>" class="btn btn--primary">
                <i class="fa-solid fa-clock"></i>
                <?= htmlspecialchars(theme_t('Voir Shifts', 'عرض المناوبات')) ?>
            </a>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php')) ?>" class="btn btn--ghost home-hero__ghost">
                <i class="fa-solid fa-calendar-days"></i>
                <?= htmlspecialchars(theme_t('Voir Emplois', 'عرض الجداول')) ?>
            </a>
        </div>
    </div>
</section>

<div class="home-sections">
<section class="section-head reveal">
    <div>
        <span class="eyebrow"><?= htmlspecialchars(theme_t('Chiffres cles', 'ارقام رئيسية')) ?></span>
        <h2><?= htmlspecialchars(theme_t('Vue instantanee de la plateforme', 'نظرة سريعة على المنصة')) ?></h2>
    </div>
    <p><?= htmlspecialchars(theme_t('Les compteurs s animent a l apparition pour donner un effet vivant et moderne.', 'تتحرك العدادات عند الظهور لاعطاء طابع حي وعصري.')) ?></p>
</section>
<section class="stats-grid">
    <article class="stat-card reveal">
        <div class="stat-value" data-countup="<?= count($shifts) ?>">0</div>
        <h3><?= htmlspecialchars(theme_t('Shifts actifs', 'مناوبات نشطة')) ?></h3>
        <p class="muted"><?= htmlspecialchars(theme_t('Tranches horaires pretes a etre assignees.', 'فترات عمل جاهزة للتعيين.')) ?></p>
    </article>
    <article class="stat-card reveal">
        <div class="stat-value" data-countup="<?= count($emplois) ?>">0</div>
        <h3><?= htmlspecialchars(theme_t('Emplois planifies', 'جداول مخططة')) ?></h3>
        <p class="muted"><?= htmlspecialchars(theme_t('Affectations visibles depuis le backoffice.', 'التعيينات ظاهرة من لوحة الادارة.')) ?></p>
    </article>
    <article class="stat-card reveal">
        <div class="stat-value" data-countup="<?= count($services) ?>">0</div>
        <h3><?= htmlspecialchars(theme_t('Services disponibles', 'خدمات متاحة')) ?></h3>
        <p class="muted"><?= htmlspecialchars(theme_t('Services actifs relies a la planification.', 'خدمات نشطة مرتبطة بالتخطيط.')) ?></p>
    </article>
</section>

<section class="section-head reveal">
    <div>
        <span class="eyebrow"><?= htmlspecialchars(theme_t('Services', 'الخدمات')) ?></span>
        <h2><?= htmlspecialchars(theme_t('Des modules clairs pour mieux travailler', 'وحدات واضحة لعمل افضل')) ?></h2>
    </div>
</section>
<section class="features-grid">
    <article class="feature-card reveal">
        <div class="feature-icon"><i class="fa-solid fa-clock"></i></div>
        <h3><?= htmlspecialchars(theme_t('Gestion des shifts', 'ادارة المناوبات')) ?></h3>
        <p class="muted"><?= htmlspecialchars(theme_t('Creation, modification et suppression des horaires avec une presentation plus moderne.', 'انشاء وتعديل وحذف المواقيت بعرض اكثر حداثة.')) ?></p>
    </article>
    <article class="feature-card reveal">
        <div class="feature-icon"><i class="fa-solid fa-users-gear"></i></div>
        <h3><?= htmlspecialchars(theme_t('Affectation des agents', 'تعيين الاعوان')) ?></h3>
        <p class="muted"><?= htmlspecialchars(theme_t('Associez agents, services et shifts dans des formulaires harmonises.', 'اربط الاعوان والخدمات والمناوبات عبر نماذج منسقة.')) ?></p>
    </article>
    <article class="feature-card reveal">
        <div class="feature-icon"><i class="fa-solid fa-chart-line"></i></div>
        <h3><?= htmlspecialchars(theme_t('Suivi de l activite', 'متابعة النشاط')) ?></h3>
        <p class="muted"><?= htmlspecialchars(theme_t('Consultez rapidement les listes et les statuts via des tableaux lisibles.', 'اعرض القوائم والحالات بسرعة عبر جداول سهلة القراءة.')) ?></p>
    </article>
</section>

<section class="section-head reveal">
    <div>
        <span class="eyebrow"><?= htmlspecialchars(theme_t('Jointure visible', 'الربط ظاهر')) ?></span>
        <h2><?= htmlspecialchars(theme_t('Emplois relies aux shifts', 'الجداول المرتبطة بالمناوبات')) ?></h2>
    </div>
    <p><?= htmlspecialchars(theme_t('Chaque emploi affiche maintenant son shift associe avec ses horaires.', 'كل جدول يعرض الآن المناوبة المرتبطة به مع توقيتها.')) ?></p>
</section>
<section class="news-grid">
    <?php foreach ($emploisRecents as $emploi): ?>
        <article class="news-card reveal">
            <span class="badge"><?= htmlspecialchars(date('d/m/Y', strtotime($emploi['date_travail']))) ?></span>
            <h3><?= htmlspecialchars(($emploi['agent_nom'] ?? 'N/A') . ' ' . ($emploi['agent_prenom'] ?? '')) ?></h3>
            <p class="muted">
                <?= htmlspecialchars(theme_t('Service', 'الخدمة')) ?>:
                <?= htmlspecialchars($emploi['nom_service'] ?? 'N/A') ?>
            </p>
            <p class="muted">
                <?= htmlspecialchars(theme_t('Shift', 'المناوبة')) ?>:
                <?= htmlspecialchars($emploi['nom_shift'] ?? 'N/A') ?>
                (<?= htmlspecialchars(substr($emploi['heure_debut'] ?? '00:00', 0, 5)) ?> -
                <?= htmlspecialchars(substr($emploi['heure_fin'] ?? '00:00', 0, 5)) ?>)
            </p>
            <p class="muted">
                <?= htmlspecialchars(theme_t('Statut', 'الحالة')) ?>:
                <?= htmlspecialchars($emploi['statut'] ?? 'planifie') ?>
            </p>
        </article>
    <?php endforeach; ?>
</section>

<section class="section-head reveal">
    <div>
        <span class="eyebrow"><?= htmlspecialchars(theme_t('Actualites', 'اخر الاخبار')) ?></span>
        <h2><?= htmlspecialchars(theme_t('Trois cartes d information', 'ثلاث بطاقات معلومات')) ?></h2>
    </div>
</section>
<section class="news-grid">
    <?php foreach ($actualites as $news): ?>
        <article class="news-card reveal">
            <div class="news-card__media" style="--card-image: url('<?= htmlspecialchars($news['image']) ?>');"></div>
            <span class="badge"><?= htmlspecialchars($news['date']) ?></span>
            <h3><?= htmlspecialchars($news['titre']) ?></h3>
            <p class="muted"><?= htmlspecialchars($news['extrait']) ?></p>
        </article>
    <?php endforeach; ?>
</section>

<section class="cta-panel reveal" style="margin-top: 24px;">
    <div class="section-head">
        <div>
            <span class="eyebrow"><?= htmlspecialchars(theme_t('Demarrage rapide', 'بداية سريعة')) ?></span>
            <h2><?= htmlspecialchars(theme_t('Passez directement a la gestion', 'انتقل مباشرة الى الادارة')) ?></h2>
        </div>
    </div>
    <div class="hero-actions">
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-ajouter.php')) ?>" class="btn btn--primary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un shift', 'اضافة مناوبة')) ?>
        </a>
        <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-ajouter.php')) ?>" class="btn btn--secondary">
            <i class="fa-solid fa-plus"></i>
            <?= htmlspecialchars(theme_t('Ajouter un emploi', 'اضافة جدول')) ?>
        </a>
    </div>
</section>
</div>
<?php theme_render_end(); ?>
