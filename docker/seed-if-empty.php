<?php

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

if (App\Models\User::query()->count() === 0) {
    $kernel->call('db:seed', ['--force' => true]);
    echo "[render] Database seeded\n";
} else {
    echo "[render] Seed skipped (users exist)\n";
}
