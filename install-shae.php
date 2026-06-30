<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Installation SHAE ===\n";
echo 'Base MySQL : '.config('database.connections.mysql.database')."\n\n";

try {
    DB::connection()->getPdo();
    echo "OK - MySQL connecte.\n\n";
} catch (Throwable $e) {
    echo "ERREUR MySQL: ".$e->getMessage()."\n";
    echo "Demarrez MySQL dans XAMPP/Laragon puis relancez install-shae.bat\n";
    exit(1);
}

echo "=== migrate:fresh --seed ===\n";
Artisan::call('migrate:fresh', ['--seed' => true, '--force' => true]);
echo Artisan::output();

Artisan::call('config:clear');
Artisan::call('view:clear');

echo "\nSUCCES. Comptes de test:\n";
echo "  Admin        : sharonemulembweng@gmail.com / password\n";
echo "  Gestionnaire : gestionnaire@exemple.com / password\n";
echo "  Client       : client@exemple.com / password\n";
echo "\nLancez: php artisan serve\n";
