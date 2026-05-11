$cssPath = "c:\xampp\htdocs\Gestion_RDV\projet\assets\css\style.css"
$content = [System.IO.File]::ReadAllText($cssPath, [System.Text.Encoding]::UTF8)

# 1. Replace hardcoded whites for backgrounds
$content = $content -replace 'background:\s*white;?', 'background: var(--white);'
$content = $content -replace 'background-color:\s*white;?', 'background-color: var(--white);'
$content = $content -replace 'background:\s*#ffffff;?', 'background: var(--white);'
$content = $content -replace 'background:\s*#fff;?', 'background: var(--white);'

# 2. Add dark mode variables after :root
$darkModeVars = @"

[data-theme="dark"] {
    --primary-light: #053b2d;
    --gray-900: #e0e0e0;
    --gray-700: #a0a0a0;
    --gray-500: #707070;
    --gray-300: #333333;
    --gray-100: #121212;
    --white: #1e1e1e;
    --dark: #f5f7fa;
    --shadow-xs: 0 1px 2px rgba(255,255,255,0.02);
    --shadow-sm: 0 2px 4px rgba(255,255,255,0.02);
    --shadow-md: 0 4px 8px -2px rgba(255,255,255,0.05);
    --shadow-lg: 0 12px 24px -8px rgba(255,255,255,0.05);
    --shadow-xl: 0 20px 40px -12px rgba(255,255,255,0.08);
}
"@

if (-not ($content -match '\[data-theme="dark"\]')) {
    $content = $content -replace '(--radius-xl:\s*1\.5rem;\s*\})', "`$1`r`n$darkModeVars"
}

# 3. Add text color transition globally
if (-not ($content -match 'color: var\(--dark\);')) {
    $content = $content -replace '(body\s*\{[^}]*)', "`$1`r`n    color: var(--dark);"
}

[System.IO.File]::WriteAllText($cssPath, $content, [System.Text.Encoding]::UTF8)
Write-Host "style.css updated for dark mode."
