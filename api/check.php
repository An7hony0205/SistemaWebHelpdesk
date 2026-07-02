<?php

use App\Domains\Identity\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::where('email', 'test@example.com')->first();
echo 'Permissions: '.$user->getAllPermissions()->pluck('name')."\n";
