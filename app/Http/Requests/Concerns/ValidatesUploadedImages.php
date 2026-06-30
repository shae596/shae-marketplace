<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;

trait ValidatesUploadedImages
{
    protected function prepareForValidation(): void
    {
        $file = $this->file('image');

        if (! $file instanceof UploadedFile) {
            return;
        }

        if (! $file->isValid()) {
            throw ValidationException::withMessages([
                'image' => [$this->uploadErrorMessage($file->getError())],
            ]);
        }
    }

    protected function uploadErrorMessage(int $code): string
    {
        $max = config('shae.upload.max_kilobytes', 10240);

        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "Le fichier est trop volumineux (maximum {$max} Ko sur ce serveur). Augmentez upload_max_filesize dans php.ini ou relancez via lancer-shae.bat.",
            UPLOAD_ERR_PARTIAL => 'Le fichier n\'a été que partiellement téléversé. Réessayez.',
            UPLOAD_ERR_NO_FILE => 'Aucun fichier n\'a été sélectionné.',
            UPLOAD_ERR_NO_TMP_DIR => 'Dossier temporaire PHP manquant. Fermez le serveur et relancez via lancer-shae.bat (ou .\\lancer-shae.ps1). Test : http://127.0.0.1:8000/dev/upload-check',
            UPLOAD_ERR_CANT_WRITE => 'Impossible d\'écrire le fichier sur le serveur. Vérifiez les droits sur storage/.',
            UPLOAD_ERR_EXTENSION => 'Une extension PHP a bloqué le téléversement.',
            default => 'Échec du téléversement (code '.$code.'). Utilisez un JPG ou PNG.',
        };
    }
}
