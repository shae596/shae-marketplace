@echo off
cd /d "%~dp0"
echo === Reparation erreur 500 SHAE ===
if not exist .env copy .env.example .env
php generer-cle.php
php artisan config:clear
php artisan view:clear
php artisan cache:clear
echo.
echo Relancez: php artisan serve
pause
