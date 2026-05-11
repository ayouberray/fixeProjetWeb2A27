<?php
$directory = 'c:/xampp/htdocs/Gestion_RDV/projet';

$newActionsTemplate = <<<HTML
<div class="nav-actions">
            <button class="icon-btn theme-toggle" title="Mode Sombre/Clair"><i class="fas fa-sun" id="theme-icon"></i></button>
            <button class="icon-btn" title="Recherche"><i class="fas fa-search"></i></button>
        </div>
HTML;

function processDir($dir) {
    global $newActionsTemplate;
    $files = glob($dir . '/*');
    foreach($files as $file) {
        if(is_dir($file)) {
            processDir($file);
        } else if(pathinfo($file, PATHINFO_EXTENSION) == 'php') {
            $content = file_get_contents($file);
            $originalContent = $content;
            
            // Replace the entire <div class="nav-actions">...</div> block
            $content = preg_replace('/<div class="nav-actions">.*?<\/div>\s*<\/nav>/s', $newActionsTemplate . "\n    </nav>", $content);
            
            if ($content !== $originalContent) {
                file_put_contents($file, $content);
                echo "Updated $file\n";
            }
        }
    }
}

processDir($directory);
echo "Done.\n";
