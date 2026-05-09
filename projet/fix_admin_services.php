<?php
// On admin-services.php, replace the "Prendre RDV" nav-cta with "Ajouter Service" button
$file = 'c:/xampp/htdocs/Gestion_RDV/projet/VIEW/backoffice/admin-services.php';
$content = file_get_contents($file);

// Replace the nav-cta for this page specifically
$content = str_replace(
    '<a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta">
                <i class="fas fa-calendar-plus"></i> Prendre RDV
            </a>',
    '<button onclick="openModal(\'addModal\')" class="nav-cta">
                <i class="fas fa-plus"></i> Ajouter Service
            </button>',
    $content
);

// Also fix the inline body style to use the CSS variable
$content = str_replace(
    'body { padding-top: 100px;  font-family: \'Inter\', sans-serif; background: #f8fafc; }',
    'body { padding-top: 100px;  font-family: \'Inter\', sans-serif; background: var(--bg-page); }',
    $content
);

// Fix the cyber-card and cyber-table to use CSS variables for dark mode
$content = str_replace(
    'background: #ffffff; border-radius: 12px;',
    'background: var(--bg-card); border-radius: 12px;',
    $content
);

$content = str_replace(
    '.futuristic-input {
            width: 100%; padding: 14px 15px; font-size: 15px; background: #ffffff; border: 1px solid #cbd5e1;',
    '.futuristic-input {
            width: 100%; padding: 14px 15px; font-size: 15px; background: var(--bg-card); border: 1px solid #cbd5e1;',
    $content
);

file_put_contents($file, $content);
echo "admin-services.php updated.\n";
