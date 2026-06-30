@echo off
cd /d "%~dp0"
php artisan config:clear
php test-mail.php
pause
