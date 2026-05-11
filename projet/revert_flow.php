<?php

function revertFile($file) {
    if (!file_exists($file)) return;
    $content = file_get_contents($file);

    // 1. Revert class names
    $content = str_replace('class="accordion-item flow-item"', 'class="accordion-item reveal"', $content);
    $content = str_replace('class="rdv-card flow-item"', 'class="rdv-card reveal"', $content);
    $content = str_replace('class="filter-bar flow-item"', 'class="filter-bar reveal"', $content);
    $content = str_replace(' flow-item"', ' reveal"', $content);

    // 2. Remove injected CSS block
    // It starts at /* ===== SCROLL FLOW ANIMATION ===== */ and ends at </style>
    $cssPattern = '/\/\*\s*=====\s*SCROLL FLOW ANIMATION\s*=====\s*\*\/.*?<\/style>/s';
    $content = preg_replace($cssPattern, '</style>', $content);

    // 3. Remove injected JS block
    // It starts at // ===== SCROLL FLOW ANIMATION ===== and ends at </script>\n</body> or just </script>
    $jsPattern = '/\/\/\s*=====\s*SCROLL FLOW ANIMATION\s*=====\s*.*?<\/script>\s*<\/body>/s';
    $content = preg_replace($jsPattern, '</body>', $content);

    // There might be a lingering <script> tag if it was empty, let's clean it up
    $content = str_replace("<script>\n</body>", "</body>", $content);
    $content = str_replace("<script></script>\n</body>", "</body>", $content);

    file_put_contents($file, $content);
    echo basename($file) . " reverted.\n";
}

revertFile('c:/xampp/htdocs/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php');
revertFile('c:/xampp/htdocs/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php');

echo "Done.\n";
