<?php
$directory = 'c:/xampp/htdocs/Gestion_RDV/projet';

$newNavTemplate = <<<HTML
<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/Gestion_RDV/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov</span>
            </div>
        </a>
        <div class="nav-menu">
{nav_links}
        </div>
        <div class="nav-actions">
            <button class="icon-btn theme-toggle" title="Mode Sombre/Clair"><i class="fas fa-sun" id="theme-icon"></i></button>
            <button class="icon-btn" title="Recherche"><i class="fas fa-search"></i></button>
        </div>
    </nav>
</div>
HTML;

function processDir($dir) {
    global $newNavTemplate;
    $files = glob($dir . '/*');
    foreach($files as $file) {
        if(is_dir($file)) {
            processDir($file);
        } else if(pathinfo($file, PATHINFO_EXTENSION) == 'php') {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // 1. Fix paths
            $content = str_replace('"/Gestion_RDV/projet/', '"/Gestion_RDV/projet/', $content);
            $content = str_replace("'/Gestion_RDV/projet/", "'/Gestion_RDV/projet/", $content);
            
            // 2. Fix CSS cache
            $content = str_replace('style.css??v=20260509_v9">', 'style.css??v=20260509_v9">', $content);
            
            // 3. Update Navbar
            if (preg_match('/<nav class="navbar">.*?<\/nav>/s', $content, $matches)) {
                $oldNav = $matches[0];
                
                $navLinks = "";
                if (preg_match('/<div class="nav-menu">(.*?)<\/div>\s*<\/div>\s*<\/nav>/s', $oldNav, $menuMatches)) {
                    $innerMenu = $menuMatches[1];
                    if (preg_match_all('/<a[^>]+class="nav-link[^"]*"[^>]*>.*?<\/a>/i', $innerMenu, $linkMatches)) {
                        foreach($linkMatches[0] as $link) {
                            $navLinks .= "            " . $link . "\n";
                        }
                    }
                }
                
                $navLinks = rtrim($navLinks, "\n");
                $newNav = str_replace('{nav_links}', $navLinks, $newNavTemplate);
                
                $content = str_replace($oldNav, $newNav, $content);
            }
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "Updated $file\n";
            }
        }
    }
}

processDir($directory);
echo "Done.\n";
