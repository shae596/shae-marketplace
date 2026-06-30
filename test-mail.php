<?php

use Illuminate\Support\Facades\Mail;

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Test Mailtrap SHAE ===\n";
echo 'Mailer : '.config('mail.default')."\n";
echo 'Host   : '.config('mail.mailers.smtp.host')."\n\n";

if (config('mail.default') === 'log') {
    echo "ATTENTION: SMTP non configure (identifiants manquants ou placeholder).\n";
    echo "Copiez Username + Password depuis Mailtrap > Email Testing > Inboxes > SMTP\n\n";
}

try {
    Mail::raw('Test email SHAE — si vous voyez ceci dans Mailtrap, la config est OK.', function ($message) {
        $message->to('client@exemple.com')->subject('Test SHAE Mailtrap');
    });

    echo "SUCCES — Ouvrez Mailtrap > Email Testing > Inboxes et verifiez le message.\n";
} catch (Throwable $e) {
    echo "ERREUR: ".$e->getMessage()."\n\n";
    echo "Verifiez MAIL_USERNAME et MAIL_PASSWORD (Email Testing, pas live.smtp).\n";
    exit(1);
}
