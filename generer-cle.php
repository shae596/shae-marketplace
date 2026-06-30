<?php

$envPath = __DIR__.'/.env';

if (! file_exists($envPath)) {
    copy(__DIR__.'/.env.example', $envPath);
}

$key = 'base64:'.base64_encode(random_bytes(32));
$content = file_get_contents($envPath);

if (preg_match('/^APP_KEY=.*$/m', $content)) {
    $content = preg_replace('/^APP_KEY=.*$/m', 'APP_KEY='.$key, $content);
} else {
    $content = "APP_KEY={$key}\n".$content;
}

file_put_contents($envPath, $content);

echo "APP_KEY generee avec succes.\n";
