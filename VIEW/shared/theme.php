<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function theme_base_path() {
    static $basePath = null;

    if ($basePath === null) {
        $projectName = basename(dirname(__DIR__, 2));
        $basePath = '/' . rawurlencode($projectName);
    }

    return $basePath;
}

function theme_url($path = '') {
    $normalized = ltrim($path, '/');
    return theme_base_path() . ($normalized !== '' ? '/' . $normalized : '');
}

function theme_current_lang() {
    if (isset($_GET['lang']) && in_array($_GET['lang'], ['fr', 'ar'], true)) {
        $_SESSION['site_lang'] = $_GET['lang'];
    }

    return $_SESSION['site_lang'] ?? 'fr';
}

function theme_dir_attr() {
    return theme_current_lang() === 'ar' ? 'rtl' : 'ltr';
}

function theme_html_lang() {
    return theme_current_lang() === 'ar' ? 'ar' : 'fr';
}

function theme_t($fr, $ar = null) {
    $isArabic = theme_current_lang() === 'ar';
    if ($isArabic && $ar !== null && $ar !== '') {
        return $ar;
    }

    return $fr;
}

function theme_toggle_lang_url() {
    $targetLang = theme_current_lang() === 'ar' ? 'fr' : 'ar';
    $params = $_GET;
    $params['lang'] = $targetLang;
    return $_SERVER['PHP_SELF'] . '?' . http_build_query($params);
}

function theme_flash_from_query() {
    if (isset($_GET['toast'])) {
        return [
            'message' => trim((string) $_GET['toast']),
            'type' => $_GET['type'] ?? 'info',
        ];
    }

    return null;
}

function theme_render_start(array $options = []) {
    $title = $options['title'] ?? 'Gestion des emplois';
    $pageTitle = $options['page_title'] ?? $title;
    $pageSubtitle = $options['page_subtitle'] ?? '';
    $bodyClass = trim('theme-body ' . ($options['body_class'] ?? ''));
    $pageClass = trim('page-shell ' . ($options['page_class'] ?? ''));
    $backgroundMode = $options['background'] ?? 'slideshow';
    $showHero = $options['show_hero'] ?? true;
    $showLoader = $options['show_loader'] ?? true;
    $flash = $options['flash'] ?? theme_flash_from_query();
    $slideshowImages = $options['slideshow_images'] ?? [
        theme_url('assets/images/tunisia1.jpg'),
        theme_url('assets/images/tunisia2.jpg'),
        theme_url('assets/images/tunisia3.jpg'),
        theme_url('assets/images/tunisia4.jpg'),
    ];
    $videoPath = $options['video_path'] ?? theme_url('assets/video/background.mp4');
    $navContext = $options['nav_context'] ?? 'front';
    $backHref = $options['back_href'] ?? null;
    $backLabel = $options['back_label'] ?? theme_t('Retour', 'رجوع');
    $contentClass = trim('content-card ' . ($options['content_class'] ?? ''));
    ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(theme_html_lang(), ENT_QUOTES, 'UTF-8') ?>" dir="<?= htmlspecialchars(theme_dir_attr(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8R9E+T9c6E6Bj5VY0RvrKeOuFzG3g9Q==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <link rel="stylesheet" href="<?= htmlspecialchars(theme_url('assets/css/style.css?v=1.2'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="<?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') ?>"
      data-background-mode="<?= htmlspecialchars($backgroundMode, ENT_QUOTES, 'UTF-8') ?>"
      data-bg-images="<?= htmlspecialchars(json_encode($slideshowImages), ENT_QUOTES, 'UTF-8') ?>">
    <?php if ($showLoader): ?>
    <div class="page-loader" id="pageLoader" aria-hidden="true">
        <div class="page-loader__spinner"></div>
    </div>
    <script>
        // Sécurité : masquer le loader quoi qu'il arrive après 1.5s
        setTimeout(function() {
            var l = document.getElementById('pageLoader');
            if(l) { l.classList.add('is-hidden'); setTimeout(function(){ l.remove(); }, 400); }
        }, 1500);
    </script>
    <?php endif; ?>

    <div class="background-stage" aria-hidden="true">
        <div class="background-stage__overlay"></div>
        <div class="background-stage__slides">
            <div class="background-stage__slide is-active"></div>
            <div class="background-stage__slide"></div>
        </div>
        <video class="background-stage__video" autoplay muted loop playsinline>
            <source src="<?= htmlspecialchars($videoPath, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
        </video>
    </div>

    <div class="toast-stack" id="toastStack">
        <?php if ($flash && !empty($flash['message'])): ?>
            <div class="toast toast--<?= htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') ?>" data-toast>
                <?= htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>
    </div>

    <header class="topbar">
        <a class="brand" href="<?= htmlspecialchars(theme_url('VIEW/frontoffice/index.php'), ENT_QUOTES, 'UTF-8') ?>">
            <span class="brand__logo"><i class="fa-solid fa-building-columns"></i></span>
            <span class="brand__text">
                <strong>InnoGov</strong>
                <small><?= htmlspecialchars(theme_t('Municipalite Tunisienne', 'بلدية تونسية'), ENT_QUOTES, 'UTF-8') ?></small>
            </span>
        </a>
        <nav class="topbar__nav">
            <a href="<?= htmlspecialchars(theme_url('VIEW/frontoffice/index.php'), ENT_QUOTES, 'UTF-8') ?>" class="<?= $navContext === 'home' ? 'is-active' : '' ?>">
                <?= htmlspecialchars(theme_t('Accueil', 'الرئيسية'), ENT_QUOTES, 'UTF-8') ?>
            </a>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-lister.php'), ENT_QUOTES, 'UTF-8') ?>" class="<?= $navContext === 'shifts' ? 'is-active' : '' ?>">
                <?= htmlspecialchars(theme_t('Shifts', 'المناوبات'), ENT_QUOTES, 'UTF-8') ?>
            </a>
            <a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php'), ENT_QUOTES, 'UTF-8') ?>" class="<?= $navContext === 'emplois' ? 'is-active' : '' ?>">
                <?= htmlspecialchars(theme_t('Emplois', 'الجداول'), ENT_QUOTES, 'UTF-8') ?>
            </a>
        </nav>
        <a href="<?= htmlspecialchars(theme_toggle_lang_url(), ENT_QUOTES, 'UTF-8') ?>" class="lang-toggle" aria-label="Language toggle">
            <i class="fa-solid fa-language"></i>
            <?= htmlspecialchars(theme_current_lang() === 'ar' ? 'FR' : 'AR', ENT_QUOTES, 'UTF-8') ?>
        </a>
    </header>

    <main class="<?= htmlspecialchars($pageClass, ENT_QUOTES, 'UTF-8') ?>">
        <?php if ($showHero): ?>
            <section class="page-hero reveal">
                <div>
                    <span class="eyebrow"><?= htmlspecialchars(theme_t('Interface harmonisee', 'واجهة موحدة'), ENT_QUOTES, 'UTF-8') ?></span>
                    <h1><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></h1>
                    <?php if ($pageSubtitle !== ''): ?>
                        <p><?= htmlspecialchars($pageSubtitle, ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
                <?php if ($backHref): ?>
                    <a href="<?= htmlspecialchars($backHref, ENT_QUOTES, 'UTF-8') ?>" class="btn btn--ghost">
                        <i class="fa-solid fa-arrow-left"></i>
                        <?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <section class="<?= htmlspecialchars($contentClass, ENT_QUOTES, 'UTF-8') ?> reveal">
    <?php
}

function theme_render_end(array $options = []) {
    $showFooter = $options['show_footer'] ?? true;
    ?>
        </section>
    </main>

    <?php if ($showFooter): ?>
        <footer class="site-footer">
            <div>
                <h3><?= htmlspecialchars(theme_t('Coordonnees', 'بيانات الاتصال'), ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars(theme_t('Municipalite - Gestion des emplois et shifts', 'البلدية - إدارة الجداول والمناوبات'), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars(theme_t('Email : contact@municipalite.tn', 'البريد: contact@municipalite.tn'), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars(theme_t('Telephone : +216 70 000 000', 'الهاتف: +216 70 000 000'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <h3><?= htmlspecialchars(theme_t('Horaires', 'التوقيت'), ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars(theme_t('Lun - Ven : 08:00 - 17:00', 'الإثنين - الجمعة: 08:00 - 17:00'), ENT_QUOTES, 'UTF-8') ?></p>
                <p><?= htmlspecialchars(theme_t('Support planning en temps reel', 'دعم التخطيط في الوقت الحقيقي'), ENT_QUOTES, 'UTF-8') ?></p>
            </div>
            <div>
                <h3><?= htmlspecialchars(theme_t('Navigation', 'التنقل'), ENT_QUOTES, 'UTF-8') ?></h3>
                <p><a href="<?= htmlspecialchars(theme_url('VIEW/frontoffice/index.php'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(theme_t('Accueil', 'الرئيسية'), ENT_QUOTES, 'UTF-8') ?></a></p>
                <p><a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-shifts-lister.php'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(theme_t('Gestion des shifts', 'إدارة المناوبات'), ENT_QUOTES, 'UTF-8') ?></a></p>
                <p><a href="<?= htmlspecialchars(theme_url('VIEW/backoffice/admin-emplois-lister.php'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(theme_t('Gestion des emplois', 'إدارة الجداول'), ENT_QUOTES, 'UTF-8') ?></a></p>
            </div>
        </footer>
    <?php endif; ?>

    <script src="<?= htmlspecialchars(theme_url('assets/js/script.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    
    <!-- Chatbot Widget Direct Embed -->
    <div class="chatbot" id="chatbot" style="position:fixed; bottom:30px; right:30px; z-index:9999;">
        <button class="chatbot__toggle" id="chatbotToggle" aria-label="Ouvrir le chatbot" style="width:64px; height:64px; border-radius:50%; background:linear-gradient(135deg, #006D5B, #004D3D); color:#fff; border:none; cursor:pointer; box-shadow:0 10px 30px rgba(0,0,0,0.2); display:grid; place-items:center; font-size:1.8rem;">
            <i class="fa-solid fa-robot"></i>
            <span class="chatbot__badge" id="chatbotBadge" style="display:none; position:absolute; top:-5px; right:-5px; width:24px; height:24px; background:#ff4757; color:white; border-radius:50%; font-size:0.75rem; font-weight:800; display:flex; align-items:center; justify-content:center; border:3px solid #fff;">1</span>
        </button>
        
        <div class="chatbot__window" id="chatbotWindow" hidden style="position:absolute; bottom:80px; right:0; width:360px; height:500px; background:#fff; border-radius:20px; box-shadow:0 20px 50px rgba(0,0,0,0.15); display:flex; flex-direction:column; overflow:hidden;">
            <div class="chatbot__header" style="background:linear-gradient(135deg, #006D5B, #004D3D); color:#fff; padding:20px; display:flex; justify-content:space-between; align-items:center;">
                <div class="chatbot__info" style="display:flex; align-items:center; gap:12px;">
                    <i class="fa-solid fa-robot" style="font-size:1.5rem;"></i>
                    <div style="line-height:1.2;">
                        <strong style="display:block;">Assistant InnoGov</strong>
                        <small style="opacity:0.8; font-size:0.8rem;">En ligne</small>
                    </div>
                </div>
                <button class="chatbot__close" id="chatbotClose" style="background:rgba(255,255,255,0.1); border:none; color:#fff; width:32px; height:32px; border-radius:50%; cursor:pointer;"><i class="fa-solid fa-xmark"></i></button>
            </div>
            
            <div class="chatbot__messages" id="chatbotMessages" style="flex:1; padding:20px; overflow-y:auto; display:flex; flex-direction:column; gap:12px; background:#f8fafc;">
                <div class="chatbot__msg chatbot__msg--bot" style="max-width:80%; padding:12px 16px; border-radius:15px; font-size:0.92rem; line-height:1.4; background:#fff; color:#133a33; align-self:flex-start; border-bottom-left-radius:2px; box-shadow:0 4px 6px rgba(0,0,0,0.05);">
                    Bonjour ! Je suis votre assistant intelligent. Comment puis-je vous aider aujourd'hui ?
                </div>
            </div>
            
            <form class="chatbot__input" id="chatbotForm" style="padding:15px; background:#fff; border-top:1px solid #e2e8f0; display:flex; gap:10px;">
                <input type="text" id="chatbotInput" placeholder="Posez une question..." autocomplete="off" style="flex:1; border:1px solid #e2e8f0; border-radius:999px; padding:10px 18px; font-size:0.9rem; outline:none;">
                <button type="submit" style="background:#006D5B; color:#fff; border:none; width:40px; height:40px; border-radius:50%; cursor:pointer;"><i class="fa-solid fa-paper-plane"></i></button>
            </form>
        </div>
    </div>

    <script>
    (function() {
        const toggle = document.getElementById('chatbotToggle');
        const windowEl = document.getElementById('chatbotWindow');
        const close = document.getElementById('chatbotClose');
        const form = document.getElementById('chatbotForm');
        const input = document.getElementById('chatbotInput');
        const messages = document.getElementById('chatbotMessages');
        const badge = document.getElementById('chatbotBadge');

        toggle.addEventListener('click', () => {
            const isHidden = windowEl.hasAttribute('hidden');
            if (isHidden) {
                windowEl.removeAttribute('hidden');
                input.focus();
                badge.style.display = 'none';
            } else {
                windowEl.setAttribute('hidden', '');
            }
        });

        close.addEventListener('click', () => {
            windowEl.setAttribute('hidden', '');
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const text = input.value.trim();
            if (!text) return;

            appendMessage(text, 'user');
            input.value = '';

            const typingId = 'typing-' + Date.now();
            const typingEl = document.createElement('div');
            typingEl.className = 'chatbot__msg chatbot__msg--bot';
            typingEl.id = typingId;
            typingEl.style.cssText = 'max-width:80%; padding:12px 16px; border-radius:15px; font-size:0.92rem; line-height:1.4; background:#fff; color:#133a33; align-self:flex-start; border-bottom-left-radius:2px; box-shadow:0 4px 6px rgba(0,0,0,0.05);';
            typingEl.textContent = '...';
            messages.appendChild(typingEl);
            messages.scrollTop = messages.scrollHeight;

            try {
                const formData = new FormData();
                formData.append('message', text);

                const response = await fetch('<?= theme_url("CONTROLLER/ChatbotController.php") ?>', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                
                document.getElementById(typingId).remove();
                appendMessage(data.response, 'bot');
            } catch (err) {
                document.getElementById(typingId).remove();
                appendMessage("Désolé, une erreur est survenue.", 'bot');
            }
        });

        function appendMessage(text, type) {
            const msg = document.createElement('div');
            msg.className = `chatbot__msg chatbot__msg--${type}`;
            msg.style.cssText = type === 'bot' 
                ? 'max-width:80%; padding:12px 16px; border-radius:15px; font-size:0.92rem; line-height:1.4; background:#fff; color:#133a33; align-self:flex-start; border-bottom-left-radius:2px; box-shadow:0 4px 6px rgba(0,0,0,0.05);'
                : 'max-width:80%; padding:12px 16px; border-radius:15px; font-size:0.92rem; line-height:1.4; background:#006D5B; color:#fff; align-self:flex-end; border-bottom-right-radius:2px;';
            msg.textContent = text;
            messages.appendChild(msg);
            messages.scrollTop = messages.scrollHeight;
        }

        setTimeout(() => {
            if (windowEl.hasAttribute('hidden')) {
                badge.style.display = 'flex';
            }
        }, 2000);
    })();
    </script>
</body>
</html>
    <?php
}
?>
