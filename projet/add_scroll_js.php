<?php
$jsPath = 'c:/xampp/htdocs/Gestion_RDV/projet/assets/js/script.js';
$js = file_get_contents($jsPath);

$scrollCode = <<<JS


// ========== NAVBAR SCROLL EFFECT ==========
(function() {
    const navbar = document.querySelector('.navbar.floating-pill');
    if (!navbar) return;

    function handleNavbarScroll() {
        if (window.scrollY > 60) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }

    // Run on load in case page is already scrolled
    handleNavbarScroll();
    window.addEventListener('scroll', handleNavbarScroll, { passive: true });
})();
JS;

if (strpos($js, 'NAVBAR SCROLL EFFECT') === false) {
    file_put_contents($jsPath, $js . $scrollCode);
    echo "Scroll effect added to script.js\n";
} else {
    echo "Already exists.\n";
}
