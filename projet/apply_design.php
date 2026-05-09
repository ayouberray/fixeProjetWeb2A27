<?php
$cssPath = 'c:/xampp/htdocs/Gestion_RDV/projet/assets/css/style.css';
$original = file_get_contents($cssPath);

// 1. NEW CSS VARIABLES - replace :root block and dark theme block
$oldRoot = '/:root \{.*?\[data-theme="dark"\] \{.*?\}/s';
$newRoot = <<<CSS
:root {
    --primary: #3a5a2a;
    --primary-dark: #2a4a1a;
    --primary-light: #eaf2e3;
    --bg-page: #f0ebe3;
    --bg-card: #ffffff;
    --secondary: #c07b3d;
    --secondary-dark: #a5672e;
    --success: #198754;
    --warning: #ffc107;
    --danger: #dc3545;
    --info: #0dcaf0;
    --dark: #1c2a1a;
    --gray-900: #2e3d2a;
    --gray-700: #4a5e44;
    --gray-500: #8a9e84;
    --gray-300: #d4ddd0;
    --gray-100: #f0ebe3;
    --white: #ffffff;
    --shadow-xs: 0 1px 2px rgba(30,45,25,0.04);
    --shadow-sm: 0 2px 6px rgba(30,45,25,0.06);
    --shadow-md: 0 6px 16px rgba(30,45,25,0.08);
    --shadow-lg: 0 16px 32px rgba(30,45,25,0.10);
    --shadow-xl: 0 24px 48px rgba(30,45,25,0.14);
    --shadow-primary: 0 8px 20px -6px rgba(58,90,42,0.35);
    --transition-fast: 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-base: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --radius-sm: 0.5rem;
    --radius-md: 0.75rem;
    --radius-lg: 1.2rem;
    --radius-xl: 2rem;
}

[data-theme="dark"] {
    --bg-page: #111814;
    --bg-card: #1c2420;
    --primary-light: #1a2e18;
    --gray-900: #d8e5d4;
    --gray-700: #9aad96;
    --gray-500: #607060;
    --gray-300: #2e3d2e;
    --gray-100: #111814;
    --white: #1c2420;
    --dark: #e8f0e4;
    --shadow-xs: 0 1px 2px rgba(0,0,0,0.3);
    --shadow-sm: 0 2px 6px rgba(0,0,0,0.3);
    --shadow-md: 0 6px 16px rgba(0,0,0,0.35);
    --shadow-lg: 0 16px 32px rgba(0,0,0,0.4);
    --shadow-xl: 0 24px 48px rgba(0,0,0,0.5);
}
CSS;

$css = preg_replace($oldRoot, $newRoot, $original);

// 2. Update body background
$css = preg_replace('/background:\s*var\(--gray-100\);(\s*color:\s*var\(--dark\);)/', 'background: var(--bg-page);$1', $css);

// 3. Transition on body
$css = str_replace(
    "overflow-x: hidden;\n}",
    "overflow-x: hidden;\n    transition: background 0.4s ease, color 0.4s ease;\n}",
    $css
);

// 4. Navbar pill - make it creamy/glassy
$css = str_replace(
    'background: rgba(255, 255, 255, 0.05); /* Wabi-sabi ultra transparent */',
    'background: rgba(240, 235, 227, 0.9);',
    $css
);
$css = str_replace(
    'border: 1px solid rgba(255, 255, 255, 0.1);',
    'border: 1px solid rgba(255, 255, 255, 0.7);',
    $css
);
$css = str_replace(
    'max-width: 1300px;',
    'max-width: 1000px;',
    $css
);

// 5. Logo subtitle style
$css = str_replace(
    "color: #1A2C3E;\n    letter-spacing: -0.5px;\n}",
    "color: var(--dark);\n    letter-spacing: -0.4px;\n}\n\n.logo-subtitle {\n    font-size: 10px;\n    font-weight: 500;\n    color: var(--gray-500);\n    display: block;\n    letter-spacing: 0.3px;\n}",
    $css
);

// 6. Nav-link - cleaner style
$css = str_replace(
    "font-family: 'Nunito', sans-serif;\n    font-weight: 600;\n    color: #4A5A6E;",
    "font-family: 'Inter', sans-serif;\n    font-size: 14px;\n    font-weight: 500;\n    color: var(--gray-700);",
    $css
);
$css = str_replace(
    "background: #C18C5D; /* Terre cuite */",
    "background: var(--primary);",
    $css
);
$css = str_replace(
    ".nav-link:hover,\n.nav-link.active {\n    color: #C18C5D;\n}",
    ".nav-link:hover, .nav-link.active {\n    color: var(--primary);\n    font-weight: 600;\n}",
    $css
);

// 7. Lang switcher pill
$css = str_replace(
    'background: rgba(0, 0, 0, 0.05);',
    'background: rgba(58,90,42,0.08);',
    $css
);
$css = str_replace(
    "font-family: 'Nunito', sans-serif;\n    font-weight: 600;\n    color: #4A5A6E;\n    cursor: pointer;",
    "font-family: 'Inter', sans-serif;\n    font-size: 12px;\n    font-weight: 600;\n    color: var(--gray-700);\n    cursor: pointer;",
    $css
);
$css = str_replace(
    "background: var(--white);\n    box-shadow: 0 2px 5px rgba(0,0,0,0.1);\n    color: #5D7052;",
    "background: var(--primary);\n    color: white;\n    box-shadow: 0 2px 6px rgba(58,90,42,0.25);",
    $css
);

// 8. Icon btn circular
$css = str_replace(
    "background: transparent;\n    border: none;\n    font-size: 1.2rem;\n    color: #5D7052;\n    cursor: pointer;\n    transition: transform 0.4s ease;\n    display: flex;\n    align-items: center;\n    justify-content: center;",
    "background: rgba(58,90,42,0.08);\n    border: none;\n    width: 34px;\n    height: 34px;\n    border-radius: 50%;\n    font-size: 1rem;\n    color: var(--primary);\n    cursor: pointer;\n    transition: var(--transition-base);\n    display: flex;\n    align-items: center;\n    justify-content: center;",
    $css
);
$css = str_replace(
    ".icon-btn:hover {\n    transform: translateY(-2px);\n    color: #C18C5D;\n}",
    ".icon-btn:hover {\n    background: rgba(58,90,42,0.15);\n    transform: scale(1.05);\n}",
    $css
);

// 9. Add nav-cta class after icon-btn styles
$css = str_replace(
    "/* Fix for body padding",
    "/* Nav CTA Button */\n.nav-cta {\n    background: var(--primary);\n    color: white !important;\n    font-family: 'Inter', sans-serif;\n    font-size: 13px;\n    font-weight: 600;\n    padding: 9px 20px;\n    border-radius: 50px;\n    text-decoration: none;\n    display: inline-flex;\n    align-items: center;\n    gap: 7px;\n    transition: all 0.3s ease;\n    white-space: nowrap;\n}\n.nav-cta:hover {\n    background: var(--primary-dark);\n    transform: translateY(-1px);\n    box-shadow: 0 4px 14px rgba(58,90,42,0.4);\n    color: white;\n}\n\n/* Fix for body padding",
    $css
);

// 10. Stat cards - big Fraunces number, no icon, no border-bottom, clean
$css = str_replace(
    "border-bottom: 3px solid var(--primary);",
    "border: 1px solid rgba(58,90,42,0.07);",
    $css
);
$css = str_replace(
    ".stat-card .number {\n    font-size: 42px;\n    font-weight: 800;\n    color: var(--primary);\n    line-height: 1;\n}",
    ".stat-card .number {\n    font-family: 'Fraunces', serif;\n    font-size: 54px;\n    font-weight: 700;\n    color: var(--dark);\n    line-height: 1;\n    letter-spacing: -2px;\n}",
    $css
);
$css = str_replace(
    ".stat-card .label {\n    font-size: 16px;\n    color: var(--gray-700);\n    margin-top: 10px;\n}",
    ".stat-card .label {\n    font-size: 10px;\n    font-weight: 700;\n    color: var(--gray-500);\n    margin-top: 10px;\n    text-transform: uppercase;\n    letter-spacing: 1.5px;\n}",
    $css
);

// 11. Hero h1 to Fraunces serif
$css = str_replace(
    ".hero h1 {\n    font-size: 48px;\n    font-weight: 800;\n    margin-bottom: 20px;\n    text-shadow: 2px 2px 4px rgba(0,0,0,0.3);\n}",
    ".hero-badge {\n    display: inline-flex;\n    align-items: center;\n    gap: 8px;\n    background: rgba(255,255,255,0.18);\n    backdrop-filter: blur(8px);\n    border: 1px solid rgba(255,255,255,0.25);\n    border-radius: 50px;\n    padding: 8px 18px;\n    font-size: 13px;\n    font-weight: 600;\n    color: white;\n    margin-bottom: 1.5rem;\n}\n\n.hero h1 {\n    font-family: 'Fraunces', serif;\n    font-size: clamp(38px, 5vw, 62px);\n    font-weight: 700;\n    margin-bottom: 1.2rem;\n    line-height: 1.15;\n    letter-spacing: -1px;\n    text-shadow: 0 2px 20px rgba(0,0,0,0.4);\n}",
    $css
);

// 12. Dark mode for navbar
$css = str_replace(
    "/* Dark Mode Navbar Overrides */\n[data-theme=\"dark\"] .logo-text-serif { color: var(--dark); }\n[data-theme=\"dark\"] .nav-link { color: var(--gray-300); }\n[data-theme=\"dark\"] .icon-btn { color: #8fbc8f; }\n[data-theme=\"dark\"] .hero-content h1 { color: #ffffff; }\n[data-theme=\"dark\"] .hero-content p { color: #e0e0e0; }",
    "/* Dark Mode Overrides */\n[data-theme=\"dark\"] .navbar.floating-pill { background: rgba(28,36,28,0.9); border-color: rgba(255,255,255,0.06); }\n[data-theme=\"dark\"] .logo-text-serif { color: var(--dark); }\n[data-theme=\"dark\"] .nav-link { color: var(--gray-700); }\n[data-theme=\"dark\"] .icon-btn { background: rgba(255,255,255,0.08); color: #a8d4a0; }\n[data-theme=\"dark\"] .card { background: var(--bg-card); }\n[data-theme=\"dark\"] .table-wrapper { background: var(--bg-card); }\n[data-theme=\"dark\"] .form-control { background: var(--bg-card); color: var(--dark); border-color: var(--gray-300); }\n[data-theme=\"dark\"] .stat-card { background: var(--bg-card); }\n[data-theme=\"dark\"] body { padding-top: 100px;  background: var(--bg-page); }\n[data-theme=\"dark\"] .loader { background: var(--bg-page); }",
    $css
);

// 13. Cards use bg-card
$css = str_replace(
    ".card {\n    background: var(--white);",
    ".card {\n    background: var(--bg-card);",
    $css
);
$css = str_replace(
    ".stat-card {\n    background: var(--white);",
    ".stat-card {\n    background: var(--bg-card);",
    $css
);
$css = str_replace(
    ".service-card {\n    background: var(--white);",
    ".service-card {\n    background: var(--bg-card);",
    $css
);
$css = str_replace(
    ".news-card {\n    background: var(--white);",
    ".news-card {\n    background: var(--bg-card);",
    $css
);
$css = str_replace(
    ".table-wrapper {\n    overflow-x: auto;\n    background: var(--white);",
    ".table-wrapper {\n    overflow-x: auto;\n    background: var(--bg-card);",
    $css
);

file_put_contents($cssPath, $css);
echo "CSS updated successfully!\n";
