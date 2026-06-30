@echo off
chcp 65001 >nul
cd /d "%~dp0"

echo.
echo ========================================
echo   SHAE - Preparation push GitHub
echo ========================================
echo.

git status -sb
echo.

set /p REPO_URL="Collez l'URL de VOTRE repo GitHub (ex: https://github.com/VOTRE-NOM/shae-marketplace.git): "

if "%REPO_URL%"=="" (
    echo Annule: URL vide.
    pause
    exit /b 1
)

echo.
echo Etape 1: commit local...
git add -A
git status -sb
echo.
set /p CONFIRM="Creer le commit maintenant ? (O/N): "
if /I not "%CONFIRM%"=="O" (
    echo Annule.
    pause
    exit /b 0
)

git commit -m "SHAE marketplace: LabPay, deploiement Render Docker MySQL"
if errorlevel 1 (
    echo Rien a committer ou erreur commit.
)

echo.
echo Etape 2: remote GitHub...
git remote get-url origin 2>nul | findstr /I "Dr-Lab1" >nul
if not errorlevel 1 (
    git remote rename origin upstream 2>nul
    echo Remote Dr-Lab1 renomme en upstream.
)

git remote get-url origin 2>nul | findstr /I "github" >nul
if errorlevel 1 (
    git remote add origin "%REPO_URL%"
) else (
    git remote set-url origin "%REPO_URL%"
)

echo.
echo Etape 3: push...
git branch -M main 2>nul
git push -u origin main
if errorlevel 1 git push -u origin master

echo.
echo Termine. Ouvrez Render.com et connectez ce depot.
pause
