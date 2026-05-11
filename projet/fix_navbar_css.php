<?php
$cssPath = 'c:/xampp/htdocs/Gestion_RDV/projet/assets/css/style.css';
$css = file_get_contents($cssPath);

// ===== 1. NAVBAR PILL - Back to transparent glassmorphism =====
$css = preg_replace(
    '/max-width:\s*1000px;\s*background:\s*rgba\(240,\s*235,\s*227,\s*0\.9\);/',
    'max-width: 1100px;
    background: rgba(255, 255, 255, 0.08);',
    $css
);

$css = str_replace(
    'border: 1px solid rgba(255, 255, 255, 0.7);',
    'border: 1px solid rgba(255, 255, 255, 0.15);',
    $css
);

// Add transition for scroll effect
$css = str_replace(
    'transition: var(--transition-base);
}

/* LEFT: Logo */',
    'transition: background 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease;
}

/* Navbar solid state when scrolled */
.navbar.floating-pill.scrolled {
    background: rgba(248, 244, 238, 0.97) !important;
    border-color: rgba(220, 215, 205, 0.9) !important;
    box-shadow: 0 8px 32px rgba(30, 45, 25, 0.14) !important;
}

[data-theme="dark"] .navbar.floating-pill {
    background: rgba(17, 24, 20, 0.12);
    border-color: rgba(255, 255, 255, 0.07);
}

[data-theme="dark"] .navbar.floating-pill.scrolled {
    background: rgba(28, 36, 28, 0.97) !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* LEFT: Logo */',
    $css
);

// ===== 2. LOGO TEXT - white on hero, dark when scrolled =====
$css = preg_replace(
    '/\.logo-text-serif\s*\{[^}]+\}/',
    '.logo-text-serif {
    font-family: \'Fraunces\', serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: #ffffff;
    letter-spacing: -0.4px;
    line-height: 1.1;
    text-shadow: 0 1px 4px rgba(0,0,0,0.3);
    transition: color 0.4s ease, text-shadow 0.4s ease;
}
.navbar.floating-pill.scrolled .logo-text-serif {
    color: var(--dark);
    text-shadow: none;
}
.logo-subtitle {
    font-size: 10px;
    font-weight: 400;
    opacity: 0.75;
    display: block;
    letter-spacing: 0.4px;
}',
    $css
);

// ===== 3. NAV LINKS - white on hero, dark when scrolled =====
$css = preg_replace(
    '/\.nav-link\s*\{[^}]+\}/',
    '.nav-link {
    font-family: \'Nunito\', sans-serif;
    font-weight: 600;
    font-size: 14px;
    color: rgba(255, 255, 255, 0.92);
    text-decoration: none;
    position: relative;
    padding: 5px 0;
    transition: color 0.3s ease;
    text-shadow: 0 1px 3px rgba(0,0,0,0.25);
}
.navbar.floating-pill.scrolled .nav-link {
    color: #4A5A6E;
    text-shadow: none;
}',
    $css
);

// Nav link hover/active states
$css = str_replace(
    ".nav-link:hover,\n.nav-link.active {\n    color: #C18C5D;\n}",
    ".nav-link:hover,
.nav-link.active {
    color: rgba(255,255,255,1);
}
.navbar.floating-pill.scrolled .nav-link:hover,
.navbar.floating-pill.scrolled .nav-link.active {
    color: #5D7052;
}",
    $css
);

// ===== 4. LANG SWITCHER - transparent on hero, subtle when scrolled =====
// Pill container
$css = preg_replace(
    '/\.lang-switcher-pill\s*\{[^}]+\}/',
    '.lang-switcher-pill {
    display: flex;
    background: rgba(255, 255, 255, 0.15);
    border-radius: 50px;
    padding: 3px;
    border: 1px solid rgba(255,255,255,0.2);
    transition: background 0.4s ease, border-color 0.4s ease;
}
.navbar.floating-pill.scrolled .lang-switcher-pill {
    background: rgba(58, 90, 42, 0.07);
    border-color: rgba(58,90,42,0.12);
}',
    $css
);

// Lang button base
$css = preg_replace(
    '/\.lang-btn\s*\{[^}]+\}/',
    '.lang-btn {
    border: none;
    background: transparent;
    padding: 5px 12px;
    border-radius: 50px;
    font-family: \'Nunito\', sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: rgba(255,255,255,0.85);
    cursor: pointer;
    transition: all 0.25s ease;
    letter-spacing: 0.5px;
}
.navbar.floating-pill.scrolled .lang-btn {
    color: #4A5A6E;
}',
    $css
);

// Lang button ACTIVE - always fixed green
$css = preg_replace(
    '/\.lang-btn\.active\s*\{[^}]+\}/',
    '.lang-btn.active {
    background: #5D7052 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 6px rgba(58,90,42,0.3);
}',
    $css
);

// ===== 5. ICON BTN - white circle on hero, green on scrolled =====
$css = preg_replace(
    '/\.icon-btn\s*\{[^}]+\}/',
    '.icon-btn {
    background: rgba(255,255,255,0.15);
    border: none;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    font-size: 1rem;
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}
.icon-btn:hover {
    background: rgba(255,255,255,0.28);
    transform: scale(1.05);
}
.navbar.floating-pill.scrolled .icon-btn {
    background: rgba(58,90,42,0.08);
    color: #5D7052;
}
.navbar.floating-pill.scrolled .icon-btn:hover {
    background: rgba(58,90,42,0.15);
}',
    $css
);

// Remove old icon-btn:hover
$css = str_replace(
    ".icon-btn:hover {\n    transform: translateY(-2px);\n    color: #C18C5D;\n}",
    '',
    $css
);

file_put_contents($cssPath, $css);
echo "Navbar CSS fixed!\n";
