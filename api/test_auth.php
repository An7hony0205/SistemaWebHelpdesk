<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Domains\Identity\User::where('email', 'test@example.com')->first();
$user->load('roles');
$user->permissions = $user->getAllPermissions()->pluck('name');

echo json_encode($user);
echo "\n";
