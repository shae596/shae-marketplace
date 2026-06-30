@echo off
cd /d "%~dp0"
echo Suppression du dossier Examen...
if exist Examen (
    rmdir /s /q Examen
    echo OK - Examen supprime.
) else (
    echo Examen deja absent.
)
pause
