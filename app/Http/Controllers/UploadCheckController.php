<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadCheckController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless(app()->environment('local'), 404);

        $configuredTmp = ini_get('upload_tmp_dir');
        $systemTmp = sys_get_temp_dir();
        $projectTmp = storage_path('framework/tmp');
        $publicStorage = public_path('storage');
        $storageLinked = is_dir($publicStorage) && (
            is_link($publicStorage)
            || is_dir($publicStorage.DIRECTORY_SEPARATOR.'products')
            || file_exists(storage_path('app/public'))
        );

        $checks = [
            'upload_tmp_dir (PHP)' => $configuredTmp ?: '(vide — utilise le dossier systeme)',
            'system_temp' => $systemTmp,
            'system_temp_writable' => is_writable($systemTmp) ? 'oui' : 'NON',
            'project_tmp' => $projectTmp,
            'project_tmp_exists' => is_dir($projectTmp) ? 'oui' : 'NON',
            'project_tmp_writable' => is_dir($projectTmp) && is_writable($projectTmp) ? 'oui' : 'NON',
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'storage_link' => $storageLinked ? 'oui' : 'NON — supprimez public/storage puis relancez lancer-shae.bat',
            'products_dir_writable' => Storage::disk('public')->directoryExists('products') || @mkdir(storage_path('app/public/products'), 0755, true)
                ? (is_writable(storage_path('app/public/products')) ? 'oui' : 'NON')
                : 'NON',
            'php_ini_loaded' => php_ini_loaded_file() ?: '(aucun)',
        ];

        if ($request->isMethod('post') && $request->hasFile('test_image')) {
            $file = $request->file('test_image');
            $checks['test_upload'] = $file->isValid()
                ? 'OK — '.$file->getClientOriginalName().' ('.$file->getSize().' octets)'
                : 'ECHEC code '.$file->getError().' — '.$this->uploadErrorLabel($file->getError());
        }

        return response()->view('dev.upload-check', compact('checks'));
    }

    private function uploadErrorLabel(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'fichier trop volumineux',
            UPLOAD_ERR_PARTIAL => 'upload partiel',
            UPLOAD_ERR_NO_FILE => 'aucun fichier',
            UPLOAD_ERR_NO_TMP_DIR => 'dossier temporaire manquant',
            UPLOAD_ERR_CANT_WRITE => 'impossible d\'ecrire sur le disque',
            UPLOAD_ERR_EXTENSION => 'extension PHP a bloque l\'upload',
            default => 'erreur inconnue',
        };
    }
}
