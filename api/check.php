<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Domains\Identity\User::where('email', 'test@example.com')->first();
echo 'Permissions: ' . $user->getAllPermissions()->pluck('name') . "\n";
