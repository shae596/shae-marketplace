# Supprime le dossier parasite Examen/ (vendor duplique)
$path = Join-Path $PSScriptRoot "Examen"
if (Test-Path $path) {
    Remove-Item -LiteralPath $path -Recurse -Force -ErrorAction Stop
    Write-Host "Dossier Examen supprime avec succes."
} else {
    Write-Host "Dossier Examen deja absent."
}
