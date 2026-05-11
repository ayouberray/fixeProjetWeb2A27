$directory = "c:\xampp\htdocs\Gestion_RDV\projet"
$files = Get-ChildItem -Path $directory -Recurse -Filter "*.php"

foreach ($file in $files) {
    $content = [IO.File]::ReadAllText($file.FullName)
    
    if ($content -match '<link rel="stylesheet" href="/projet/assets/css/style\.css">') {
        $newContent = $content -replace '<link rel="stylesheet" href="/projet/assets/css/style\.css">', '<link rel="stylesheet" href="/projet/assets/css/style.css?v=<?= time() ?>">'
        [IO.File]::WriteAllText($file.FullName, $newContent, [System.Text.Encoding]::UTF8)
        Write-Host "Updated $($file.FullName)"
    }
}
Write-Host "Cache busting applied."
