<?php
$cssPath = 'c:/xampp/htdocs/Gestion_RDV/projet/assets/css/style.css';
$existing = file_get_contents($cssPath);

$additions = <<<CSS

/* ========== DESIGN POLISH (reference match) ========== */

/* Section titles use Fraunces serif */
.section-title, .card-title, h2, h3 {
    font-family: 'Fraunces', serif;
}

/* Section title - left-aligned with emoji icon like reference */
.section-title {
    text-align: left;
    font-size: 28px;
    font-weight: 700;
    margin-bottom: 28px;
    color: var(--dark);
    letter-spacing: -0.5px;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-title::after {
    display: none; /* Remove old underline bar */
}

/* Stat cards grid spacing */
.stats-grid {
    margin-bottom: 48px;
}

/* Dark mode on stat icon (hide cleanly) */
.stat-card i { display: none; }

/* Section background alternation */
.section-alt {
    background: var(--bg-card);
}

/* Hero overlay - more cinematic dark like reference */
.hero-overlay {
    background: linear-gradient(180deg, rgba(0,0,0,0.45) 0%, rgba(15,40,10,0.65) 100%);
}

/* Buttons fully pill-shaped */
.btn, .btn-primary, .btn-outline, .btn-secondary {
    border-radius: 50px;
}

/* Footer dark bg */
.footer {
    background: var(--dark);
}

/* Form controls adapt to dark */
.form-control {
    background: var(--bg-card);
    color: var(--dark);
    border-color: var(--gray-300);
}

/* Padding top for pages without hero smaller due to narrower navbar */
body:not(:has(.hero)) {
    padding-top: 90px;
}

/* Table header */
.table thead tr {
    background: var(--primary);
}

/* News card */
.news-card {
    background: var(--bg-card);
}

/* Logo circle green */
.logo-circle {
    background-color: var(--primary);
}
CSS;

// Only append if not already added
if (strpos($existing, 'DESIGN POLISH (reference match)') === false) {
    file_put_contents($cssPath, $existing . $additions);
    echo "Polish CSS appended.\n";
} else {
    echo "Already applied.\n";
}
