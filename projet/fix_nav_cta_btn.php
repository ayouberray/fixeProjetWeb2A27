<?php
$cssPath = 'c:/xampp/htdocs/Gestion_RDV/projet/assets/css/style.css';
$css = file_get_contents($cssPath);

$addition = <<<CSS

/* nav-cta also works as <button> */
button.nav-cta {
    cursor: pointer;
    font-family: 'Inter', sans-serif;
}
CSS;

if (strpos($css, 'nav-cta also works') === false) {
    file_put_contents($cssPath, $css . $addition);
    echo "CSS fix appended.\n";
} else {
    echo "Already exists.\n";
}
