$cssPath = "c:\xampp\htdocs\Gestion_RDV\projet\assets\css\style.css"
$content = [System.IO.File]::ReadAllText($cssPath, [System.Text.Encoding]::UTF8)

$navDarkOverrides = @"

/* Dark Mode Navbar Overrides */
[data-theme="dark"] .logo-text-serif { color: var(--dark); }
[data-theme="dark"] .nav-link { color: var(--gray-300); }
[data-theme="dark"] .icon-btn { color: #8fbc8f; }
[data-theme="dark"] .hero-content h1 { color: #ffffff; }
[data-theme="dark"] .hero-content p { color: #e0e0e0; }

"@

if (-not ($content -match 'Dark Mode Navbar Overrides')) {
    $content = $content + $navDarkOverrides
    [System.IO.File]::WriteAllText($cssPath, $content, [System.Text.Encoding]::UTF8)
}
Write-Host "Navbar dark mode overrides added."
