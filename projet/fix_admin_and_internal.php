<?php
$dir = __DIR__;
$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$phpFiles = [];
foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $phpFiles[] = $file->getRealPath();
    }
}

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

foreach ($phpFiles as $filePath) {
    if (basename($filePath) === 'fix_admin_and_internal.php' || basename($filePath) === 'index.php') continue;
    
    $content = file_get_contents($filePath);
    $changed = false;
    
    // 1. Remove ANY existing Hero Section
    $content = preg_replace('/<!-- HERO SECTION -->\s*<section class="hero">.*?<\/section>/s', '', $content);
    
    // 2. Fix broken logo links (closing </a> after the logo-hybrid div)
    // Looking for logo-hybrid div then possibly the removed hero then the stray </a>
    $content = preg_replace('/(<div class="logo-hybrid">.*?<\/div>)\s*<\/a>/s', '$1' . "\n        </a>", $content);
    
    // 3. Inject Hero ONLY for Frontoffice pages
    if (strpos($filePath, 'VIEW' . DIRECTORY_SEPARATOR . 'frontoffice') !== false) {
        if (strpos($content, '<div class="navbar-wrapper">') !== false) {
            // Find the end of the navbar-wrapper div
            // The navbar-wrapper div usually contains a nav and ends with </div>\n</div>
            $pos = strpos($content, '<div class="navbar-wrapper">');
            $endPos = strpos($content, '</div>', $pos + 30); // Skip the wrapper open
            $finalEndPos = strpos($content, '</div>', $endPos + 1); // This should be the wrapper close
            
            if ($finalEndPos !== false) {
                $content = substr_replace($content, "\n\n" . $heroHtml, $finalEndPos + 6, 0);
                $changed = true;
            }
        }
    } else {
        // For Admin (backoffice), ensure we have enough padding to see content
        if (strpos($content, 'body {') !== false && strpos($content, 'padding-top') === false) {
             $content = str_replace('body {', 'body { padding-top: 100px; ', $content);
             $changed = true;
        }
        $changed = true; // We always want to save because we removed the hero
    }
    
    file_put_contents($filePath, $content);
    echo "Fixed: $filePath\n";
}
?>
