<?php

$scrollCSS = <<<CSS

        /* ===== SCROLL FLOW ANIMATION ===== */
        @keyframes flowUp {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.97);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .flow-item {
            opacity: 0;
            transform: translateY(50px) scale(0.97);
            transition: none;
        }

        .flow-item.visible {
            animation: flowUp 0.55s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }
CSS;

$scrollJS = <<<JS

    // ===== SCROLL FLOW ANIMATION =====
    (function() {
        const items = document.querySelectorAll('.flow-item');
        if (!items.length) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry, i) => {
                if (entry.isIntersecting) {
                    // Staggered delay based on element index
                    const index = Array.from(items).indexOf(entry.target);
                    entry.target.style.animationDelay = (index * 0.07) + 's';
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.08 });

        items.forEach(item => observer.observe(item));
    })();
JS;

// ======= CITOYEN MES RDV =======
$file1 = 'c:/xampp/htdocs/Gestion_RDV/projet/VIEW/frontoffice/citoyen-mes-rdv.php';
$content1 = file_get_contents($file1);

// Add CSS before </style>
if (strpos($content1, 'SCROLL FLOW ANIMATION') === false) {
    $content1 = str_replace('</style>', $scrollCSS . "\n        </style>", $content1);
}

// Add class="flow-item" to .rdv-card divs (PHP loop output)
$content1 = preg_replace(
    '/<div class="rdv-card([^"]*)"/',
    '<div class="rdv-card$1 flow-item"',
    $content1
);

// Add JS before </body>
if (strpos($content1, 'SCROLL FLOW ANIMATION') === false || strpos($content1, 'flowUp') === false) {
    $content1 = str_replace('</body>', '<script>' . $scrollJS . '</script>' . "\n</body>", $content1);
}

file_put_contents($file1, $content1);
echo "citoyen-mes-rdv.php updated.\n";


// ======= ADMIN LISTER RDV =======
$file2 = 'c:/xampp/htdocs/Gestion_RDV/projet/VIEW/backoffice/admin-lister-rdv.php';
$content2 = file_get_contents($file2);

// Add CSS before </style>
if (strpos($content2, 'SCROLL FLOW ANIMATION') === false) {
    $content2 = str_replace('</style>', $scrollCSS . "\n        </style>", $content2);
}

// Add flow-item class to citoyen group cards (the grouped blocks)
// These are typically wrapped in a div. Let's find the pattern.
$content2 = preg_replace(
    '/<div class="citoyen-group([^"]*)"/',
    '<div class="citoyen-group$1 flow-item"',
    $content2
);

// Also try generic card class
$content2 = preg_replace(
    '/<div class="cyber-card([^"]*)"/',
    '<div class="cyber-card$1 flow-item"',
    $content2
);

// Add JS before </body>
$content2 = str_replace('</body>', '<script>' . $scrollJS . '</script>' . "\n</body>", $content2);

file_put_contents($file2, $content2);
echo "admin-lister-rdv.php updated.\n";

echo "Done!\n";
