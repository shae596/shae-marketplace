# Copie rapide des vues SHAE restantes (1 commande)
$src = "C:\Users\HP\Projects\shae\resources\views"
$dst = "C:\Users\HP\Projects\examen-php-laravel-l3\resources\views"
$dirs = @("admin","client","payments","products","profile","emails")
foreach ($d in $dirs) {
    Copy-Item -Path "$src\$d" -Destination "$dst\$d" -Recurse -Force
}
Write-Host "Vues copiees. Verifiez: $dst"
