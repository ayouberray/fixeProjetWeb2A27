<?php
// Mapping of admin page filename => [icon, label, action]
// action can be a URL or an onclick JS call
$pageActions = [
    'admin-lister-rdv.php'      => ['fa-plus-circle', 'Ajouter RDV',    'href="/Gestion_RDV/projet/VIEW/backoffice/admin-ajouter-rdv.php"'],
    'admin-ajouter-rdv.php'     => ['fa-list',        'Voir les RDV',   'href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php"'],
    'admin-modifier-rdv.php'    => ['fa-list',        'Voir les RDV',   'href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php"'],
    'admin-affecter-agent.php'  => ['fa-list',        'Voir les RDV',   'href="/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php"'],
    'admin-services.php'        => ['fa-plus',        'Ajouter Service','onclick="openModal(\'addModal\')"'],
    'admin-stats-rdv.php'       => ['fa-download',    'Exporter',       'onclick="window.print()"'],
    'admin-rappels.php'         => ['fa-envelope',    'Envoyer rappels','href="#"'],
];

$backofficeDir = 'c:/xampp/htdocs/Gestion_RDV/projet/VIEW/backoffice';

// The old CTA to replace
$oldCta = '<a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta">
                <i class="fas fa-calendar-plus"></i> Prendre RDV
            </a>';

foreach ($pageActions as $filename => [$icon, $label, $action]) {
    $path = "$backofficeDir/$filename";
    if (!file_exists($path)) continue;

    $content = file_get_contents($path);

    // Build new CTA
    if (strpos($action, 'href=') === 0) {
        $newCta = "<a $action class=\"nav-cta\">
                <i class=\"fas $icon\"></i> $label
            </a>";
    } else {
        $newCta = "<button $action class=\"nav-cta\">
                <i class=\"fas $icon\"></i> $label
            </button>";
    }

    $updated = str_replace($oldCta, $newCta, $content);

    // Also fix body bg for dark mode
    $updated = str_replace(
        'background: #f8fafc;',
        'background: var(--bg-page);',
        $updated
    );

    if ($updated !== $content) {
        file_put_contents($path, $updated);
        echo "Updated: $filename\n";
    } else {
        // Maybe the old CTA text is slightly different, try alternate
        $altOld = '<a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta">
                <i class="fas fa-calendar-plus"></i> Prendre RDV
            </a>';
        $updated2 = str_replace($altOld, $newCta, $content);
        if ($updated2 !== $content) {
            file_put_contents($path, $updated2);
            echo "Updated (alt): $filename\n";
        } else {
            echo "Skipped (not found): $filename\n";
        }
    }
}

echo "Done.\n";
