<?php

// 1. Fix admin-lister-rdv.php
$fileAdmin = 'c:/xampp/htdocs/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php';
$contentAdmin = file_get_contents($fileAdmin);

// Add flow-item to the accordion items
$contentAdmin = str_replace('<div class="accordion-item reveal">', '<div class="accordion-item flow-item">', $contentAdmin);

// The filter bar has 'reveal', let's change it to 'flow-item' too
$contentAdmin = str_replace('<form class="filter-bar reveal"', '<form class="filter-bar flow-item"', $contentAdmin);

file_put_contents($fileAdmin, $contentAdmin);
echo "admin-lister-rdv.php fixed.\n";


// 2. Fix citoyen-mes-rdv.php
$fileCitoyen = 'c:/xampp/htdocs/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php';
$contentCitoyen = file_get_contents($fileCitoyen);

// Clean up if we ended up with 'reveal flow-item' or 'flow-item flow-item'
$contentCitoyen = str_replace('reveal flow-item', 'flow-item', $contentCitoyen);
$contentCitoyen = str_replace('flow-item flow-item', 'flow-item', $contentCitoyen);

// The filter bar
$contentCitoyen = str_replace('<form class="filter-bar reveal"', '<form class="filter-bar flow-item"', $contentCitoyen);

file_put_contents($fileCitoyen, $contentCitoyen);
echo "citoyen-mes-rdv.php fixed.\n";
