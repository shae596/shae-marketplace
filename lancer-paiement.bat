@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo.
echo ========================================
echo   SHAE - Paiement mobile (LabPay)
echo ========================================
echo.

REM --- 1. MySQL (XAMPP) ---
netstat -an | findstr ":3306" | findstr "LISTENING" >nul
if errorlevel 1 (
    echo [1/3] Demarrage MySQL XAMPP...
    if exist "C:\xampp\mysql_start.bat" (
        call "C:\xampp\mysql_start.bat" >nul 2>&1
        timeout /t 4 /nobreak >nul
    ) else (
        echo ERREUR: MySQL n'est pas demarre. Ouvrez XAMPP et cliquez Start sur MySQL.
        pause
        exit /b 1
    )
) else (
    echo [1/3] MySQL OK
)

REM --- 2. Serveur SHAE ---
netstat -an | findstr "127.0.0.1:8000" | findstr "LISTENING" >nul
if errorlevel 1 (
    echo [2/3] Demarrage serveur SHAE...
    start "SHAE serveur" cmd /k "%~dp0lancer-shae.bat"
    timeout /t 3 /nobreak >nul
) else (
    echo [2/3] Serveur SHAE deja actif
)

REM --- 3. Tunnel HTTPS (cloudflared) ---
set "CF="
if exist "%~dp0tools\cloudflared.exe" set "CF=%~dp0tools\cloudflared.exe"
if exist "C:\Users\HP\cloudflared\cloudflared.exe" set "CF=C:\Users\HP\cloudflared\cloudflared.exe"
if exist "%USERPROFILE%\Downloads\cloudflared.exe" set "CF=%USERPROFILE%\Downloads\cloudflared.exe"

where cloudflared >nul 2>&1
if not errorlevel 1 if not defined CF set "CF=cloudflared"

if not defined CF (
    echo.
    echo ERREUR: cloudflared introuvable.
    echo.
    echo Telechargez cloudflared-windows-amd64.exe depuis:
    echo   https://github.com/cloudflare/cloudflared/releases/latest
    echo.
    echo Placez-le ici: %~dp0tools\cloudflared.exe
    echo Puis relancez ce script.
    echo.
    pause
    exit /b 1
)

echo [3/3] Demarrage tunnel HTTPS...
echo.
echo IMPORTANT:
echo   - Une NOUVELLE fenetre "SHAE tunnel" va s'ouvrir
echo   - Copiez l'URL https://....trycloudflare.com affichee
echo   - Ouvrez CETTE URL dans le navigateur (pas localhost, pas une ancienne URL)
echo   - NE FERMEZ PAS les fenetres SHAE serveur et SHAE tunnel
echo.
start "SHAE tunnel HTTPS" cmd /k ""%CF%" tunnel --url http://127.0.0.1:8000"

echo Script termine. Utilisez l'URL du tunnel pour tester le paiement LabPay.
pause
