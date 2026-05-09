$jsPath = "c:\xampp\htdocs\Gestion_RDV\projet\assets\js\script.js"
$content = [System.IO.File]::ReadAllText($jsPath, [System.Text.Encoding]::UTF8)

$darkModeLogic = @"

// ========== DARK MODE TOGGLE ==========
function initDarkMode() {
    // Try to find all theme toggle buttons in case there are multiple
    const themeToggleBtns = document.querySelectorAll('.theme-toggle');
    
    // Check saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    
    function applyTheme(theme) {
        if(theme === 'dark') {
            document.documentElement.setAttribute('data-theme', 'dark');
            themeToggleBtns.forEach(btn => {
                const icon = btn.querySelector('i');
                if(icon) {
                    icon.classList.remove('fa-sun');
                    icon.classList.add('fa-moon');
                }
            });
        } else {
            document.documentElement.removeAttribute('data-theme');
            themeToggleBtns.forEach(btn => {
                const icon = btn.querySelector('i');
                if(icon) {
                    icon.classList.remove('fa-moon');
                    icon.classList.add('fa-sun');
                }
            });
        }
    }
    
    // Apply initial
    applyTheme(savedTheme);
    
    // Toggle on click
    themeToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            applyTheme(newTheme);
            localStorage.setItem('theme', newTheme);
        });
    });
}
"@

if (-not ($content -match 'initDarkMode')) {
    # Append the function definition
    $content = $content -replace '(// ========== INITIALISATION ==========)', "$darkModeLogic`r`n`r`n`$1"
    
    # Inject the call inside DOMContentLoaded
    $content = $content -replace '(autoScrollAfterSlideshow\(\);)', "`$1`r`n    initDarkMode();"
}

[System.IO.File]::WriteAllText($jsPath, $content, [System.Text.Encoding]::UTF8)
Write-Host "script.js updated correctly."
