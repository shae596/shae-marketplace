@echo off
chcp 65001 >nul
cd /d "%~dp0"

if not exist "storage\framework\tmp" mkdir "storage\framework\tmp"
if not exist "storage\app\public\products" mkdir "storage\app\public\products"

REM Dossier temporaire Windows + PHP (chemins sans guillemets pour -d)
set "TEMP_DIR=%~dp0storage\framework\tmp"
set "TEMP=%TEMP_DIR%"
set "TMP=%TEMP_DIR%"
set "TMPDIR=%TEMP_DIR:\=/%"

echo.
echo ========================================
echo   SHAE - Demarrage
echo ========================================
echo.
echo   Site : http://127.0.0.1:8000
echo   Temp : %TMPDIR%
echo.
echo   NE FERMEZ PAS cette fenetre.
echo.

if not exist "public\storage" (
    php "%~dp0artisan" storage:link >nul 2>&1
)

cd /d "%~dp0public"

REM -d ajoute les regles au php.ini Herd (ne pas utiliser -c qui remplace tout le php.ini)
php -d upload_tmp_dir=%TMPDIR% -d upload_max_filesize=20M -d post_max_size=25M -d max_file_uploads=20 -S 127.0.0.1:8000 "%~dp0vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"

echo.
echo Serveur arrete.
pause
