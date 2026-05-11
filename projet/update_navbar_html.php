<?php
$directory = 'c:/xampp/htdocs/Gestion_RDV/projet';

function processFile($file) {
    $content = file_get_contents($file);
    $original = $content;

    // 1. Update logo to add subtitle
    $content = str_replace(
        '<span class="logo-text-serif">InnoGov</span>',
        '<span class="logo-text-serif">InnoGov<small class="logo-subtitle">Municipalite</small></span>',
        $content
    );

    // 2. Add CTA button in nav-actions (after lang-switcher-pill closing div)
    $content = str_replace(
        '</div>
        </div>
    </nav>
</div>',
        '</div>
            <a href="/Gestion_RDV/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="nav-cta">
                <i class="fas fa-calendar-plus"></i> Prendre RDV
            </a>
        </div>
    </nav>
</div>',
        $content
    );

    if ($content !== $original) {
        file_put_contents($file, $content);
        echo "Updated: $file\n";
    }
}

function processDir($dir) {
    $files = glob($dir . '/*');
    foreach ($files as $file) {
        if (is_dir($file)) {
            processDir($file);
        } elseif (pathinfo($file, PATHINFO_EXTENSION) === 'php' && strpos($file, 'update_') === false && strpos($file, 'apply_') === false && strpos($file, 'swap_') === false && strpos($file, 'fix_') === false) {
            processFile($file);
        }
    }
}

processDir($directory);
echo "Done.\n";
