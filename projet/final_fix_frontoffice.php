<?php
$dir = __DIR__ . '/VIEW/frontoffice';
$files = glob("$dir/*.php");

$correctLogoLink = <<<HTML
<a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>
            </div>
        </a>
HTML;

$heroHtml = <<<HTML
<!-- HERO SECTION -->
<section class="hero">
    <div class="hero-slideshow">
        <img src="/Gestion_RDV/projet/assets/images/tunisia1.jpg" class="slide active" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia2.jpg" class="slide" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia3.jpg" class="slide" alt="Tunisie">
        <img src="/Gestion_RDV/projet/assets/images/tunisia4.jpg" class="slide" alt="Tunisie">
    </div>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <h1>Services Municipaux Digitalisés</h1>
        <p>Simplifiez vos démarches administratives en ligne</p>
        <div class="hero-buttons">
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="btn btn-primary">Prendre rendez-vous</a>
        </div>
    </div>
</section>
HTML;

foreach ($files as $filePath) {
    $content = file_get_contents($filePath);
    
    // 1. Remove ANY existing Hero
    $content = preg_replace('/<!-- HERO SECTION -->\s*<section class="hero">.*?<\/section>/s', '', $content);
    
    // 2. Fix the navbar-wrapper / logo-hybrid mess
    // Look for the start of the logo link and match until the end of the hero section or the stray </a>
    $content = preg_replace('/<a href="\/Gestion_RDV\/projet\/index\.php" class="nav-logo-link">.*?<\/a>/s', $correctLogoLink, $content);
    
    // 3. Inject Hero after navbar-wrapper
    $pos = strpos($content, '<div class="navbar-wrapper">');
    if ($pos !== false) {
        $wrapperEnd = strpos($content, '</div>', $pos); // End of inner nav? No.
        // Let's find the closing tag of the navbar-wrapper
        // The navbar-wrapper structure is: <div class="navbar-wrapper"><nav>...</nav></div>
        $navEnd = strpos($content, '</nav>', $pos);
        if ($navEnd !== false) {
            $wrapperClosing = strpos($content, '</div>', $navEnd);
            if ($wrapperClosing !== false) {
                $content = substr_replace($content, "\n\n" . $heroHtml, $wrapperClosing + 6, 0);
            }
        }
    }
    
    file_put_contents($filePath, $content);
    echo "Fixed Frontoffice: $filePath\n";
}
?>
