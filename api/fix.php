<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use App\Domains\Identity\User;
$u = User::where('email', 'usuario final@test.com')->first();
if($u) {
    $u->email = 'usuario_final@test.com';
    $u->save();
    echo "Fixed";
}
