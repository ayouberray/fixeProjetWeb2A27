$directory = "c:\xampp\htdocs\Gestion_RDV\projet"
$files = Get-ChildItem -Path $directory -Recurse -Filter "*.php"

$newNavTemplate = @"
<div class="navbar-wrapper">
    <nav class="navbar floating-pill">
        <a href="/projet/index.php" class="nav-logo-link">
            <div class="logo-hybrid">
                <div class="logo-circle"><i class="fas fa-leaf"></i></div>
                <span class="logo-text-serif">InnoGov</span>
            </div>
        </a>
        <div class="nav-menu">
{nav_links}
        </div>
        <div class="nav-actions">
            <div class="lang-switcher-pill">
                <button class="lang-btn active">FR</button>
                <button class="lang-btn">AR</button>
            </div>
            <button class="icon-btn" title="Recherche"><i class="fas fa-search"></i></button>
            <a href="/projet/VIEW/frontoffice/citoyen-reserver-rdv.php" class="cta-search">Prendre RDV</a>
        </div>
    </nav>
</div>
"@

foreach ($file in $files) {
    $content = [IO.File]::ReadAllText($file.FullName)
    
    if ($content -match '(?s)<nav class="navbar">.*?</nav>') {
        $oldNav = $matches[0]
        
        $navLinks = ""
        if ($oldNav -match '(?s)<div class="nav-menu">(.*?)</div>\s*</div>\s*</nav>') {
            $innerMenu = $matches[1]
            $linkMatches = [regex]::Matches($innerMenu, '(?i)<a[^>]+class="nav-link[^"]*"[^>]*>.*?</a>')
            
            foreach ($match in $linkMatches) {
                $navLinks += "            " + $match.Value + "`r`n"
            }
        }
        
        $navLinks = $navLinks.TrimEnd("`r`n")
        $newNav = $newNavTemplate.Replace("{nav_links}", $navLinks)
        
        $newContent = $content.Replace($oldNav, $newNav)
        [IO.File]::WriteAllText($file.FullName, $newContent, [System.Text.Encoding]::UTF8)
        Write-Host "Updated $($file.FullName)"
    }
}
Write-Host "Done."
