<?php

use App\Enums\UserRole;
use App\Models\User;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

User::updateOrCreate(
    ['email' => 'sharonemulembweng@gmail.com'],
    [
        'name' => 'MULEMBWE NGUBA SHARONE',
        'password' => 'password',
        'phone' => '+243900000001',
        'role' => UserRole::Admin,
        'is_active' => true,
        'email_verified_at' => now(),
    ]
);

User::where('email', 'admin@exemple.com')->delete();

echo "Admin SHAE : sharonemulembweng@gmail.com / password\n";
