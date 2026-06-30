@echo off
chcp 65001 >nul
cd /d "%~dp0"
echo.
echo ========================================
echo   INSTALLATION SHAE
echo ========================================
echo.
echo 1. Demarrez MySQL dans XAMPP (voyant vert)
echo 2. Verifiez que la base "shae" existe dans phpMyAdmin
echo.
pause
echo.
if not exist .env copy .env.example .env
php generer-cle.php 2>nul
echo.
echo Migration + donnees de test...
php artisan migrate:fresh --seed --force
if errorlevel 1 (
    echo.
    echo ERREUR. Verifiez que MySQL est demarre et que la base "shae" existe.
    pause
    exit /b 1
)
php artisan config:clear
php artisan view:clear
if not exist "storage\framework\tmp" mkdir "storage\framework\tmp"
php artisan storage:link 2>nul
echo.
echo ========================================
echo   SUCCES ! Double-cliquez lancer-shae.bat
echo ========================================
pause
