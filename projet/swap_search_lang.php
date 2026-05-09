<?php
$directory = 'c:/xampp/htdocs/Gestion_RDV/projet';

function processDir($dir) {
    $files = glob($dir . '/*');
    foreach($files as $file) {
        if(is_dir($file)) {
            processDir($file);
        } else if(pathinfo($file, PATHINFO_EXTENSION) == 'php' && strpos($file, 'update_') === false) {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Replace search icon with lang switcher pill
            $content = str_replace(
                '<div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>',
                '<div class="lang-switcher-pill">
                <button class="lang-btn active" data-lang="fr">FR</button>
                <button class="lang-btn" data-lang="ar">AR</button>
            </div>',
                $content
            );
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "Updated $file\n";
            }
        }
    }
}

processDir($directory);
echo "Done.\n";
