# Lance SHAE avec upload_tmp_dir force via -d (conserve le php.ini Herd complet)
$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot

New-Item -ItemType Directory -Force -Path "storage\framework\tmp" | Out-Null
New-Item -ItemType Directory -Force -Path "storage\app\public\products" | Out-Null

$tmpWin = (Resolve-Path "storage\framework\tmp").Path
$tmpDir = $tmpWin -replace '\\', '/'
$env:TEMP = $tmpWin
$env:TMP = $tmpWin

if (-not (Test-Path "public\storage")) {
    php (Join-Path $PSScriptRoot "artisan") storage:link 2>$null
}

Write-Host ""
Write-Host "========================================"
Write-Host "  SHAE - Demarrage"
Write-Host "========================================"
Write-Host ""
Write-Host "  Site : http://127.0.0.1:8000"
Write-Host "  Temp : $tmpDir"
Write-Host ""

$server = Join-Path $PSScriptRoot "vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"
Set-Location (Join-Path $PSScriptRoot "public")

php -d "upload_tmp_dir=$tmpDir" -d upload_max_filesize=20M -d post_max_size=25M -d max_file_uploads=20 -S 127.0.0.1:8000 $server
