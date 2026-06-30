# Script de migration SHAE -> projet professeur
# Executer dans PowerShell : .\migrate-shae.ps1

$ErrorActionPreference = "Stop"
$shae = "C:\Users\HP\Projects\shae"
$target = "C:\Users\HP\Projects\examen-php-laravel-l3"

Write-Host "=== Migration SHAE vers projet professeur ===" -ForegroundColor Cyan

# 1. Copier app/
Write-Host "Copie app/..."
Copy-Item -Path "$shae\app\*" -Destination "$target\app\" -Recurse -Force

# 2. Copier migrations SHAE (2024_*)
Write-Host "Copie migrations..."
Get-ChildItem "$shae\database\migrations\2024*.php" | Copy-Item -Destination "$target\database\migrations\" -Force
Get-ChildItem "$shae\database\migrations\*personal_access*" -ErrorAction SilentlyContinue | Copy-Item -Destination "$target\database\migrations\" -Force

# 3. Seeder
Write-Host "Copie seeder..."
Copy-Item "$shae\database\seeders\DatabaseSeeder.php" "$target\database\seeders\" -Force

# 4. Views et lang
Write-Host "Copie views et lang..."
Copy-Item -Path "$shae\resources\views\*" -Destination "$target\resources\views\" -Recurse -Force
if (Test-Path "$shae\resources\lang") {
    Copy-Item -Path "$shae\resources\lang\*" -Destination "$target\resources\lang\" -Recurse -Force
}

# 5. Routes
Write-Host "Copie routes..."
Copy-Item "$shae\routes\web.php" "$target\routes\" -Force
Copy-Item "$shae\routes\api.php" "$target\routes\" -Force

# 6. Config et docs
Write-Host "Copie config et docs..."
Copy-Item "$shae\config\shae.php" "$target\config\" -Force
Copy-Item -Path "$shae\docs\*" -Destination "$target\docs\" -Recurse -Force

# 7. README SHAE (backup du README prof)
if (-not (Test-Path "$target\README-EXAMEN.md")) {
    Copy-Item "$target\README.md" "$target\README-EXAMEN.md"
}
Copy-Item "$shae\README.md" "$target\README-SHAE.md" -Force

Write-Host ""
Write-Host "Migration fichiers terminee!" -ForegroundColor Green
Write-Host ""
Write-Host "Prochaines etapes manuelles :" -ForegroundColor Yellow
Write-Host "  1. cd $target"
Write-Host "  2. composer require laravel/sanctum barryvdh/laravel-dompdf"
Write-Host "  3. copy .env.example .env   (ou fusionner avec votre .env SHAE)"
Write-Host "  4. php artisan key:generate"
Write-Host "  5. php artisan migrate --seed"
Write-Host "  6. php artisan storage:link"
Write-Host "  7. php artisan serve"
Write-Host ""
Write-Host "Supprimer le dossier parasite : Examen\" -ForegroundColor Red
