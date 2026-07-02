<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Domains\Identity\User;
use Spatie\Permission\Models\Role;
use App\Domains\Identity\Tenant;
use Illuminate\Support\Facades\Hash;

$tenant = Tenant::first();
if (!$tenant) {
    $tenant = Tenant::create(['name' => 'Default Tenant', 'domain' => 'default']);
}

$roles = Role::all();
echo "=============================\n";
echo "USUARIOS DE PRUEBA POR ROL\n";
echo "=============================\n\n";

foreach ($roles as $role) {
    $email = strtolower($role->name) . '@test.com';
    $user = User::where('email', $email)->first();
    
    if (!$user) {
        $user = User::create([
            'tenant_id' => $tenant->id,
            'name' => 'Usuario ' . $role->name,
            'email' => $email,
            'password' => Hash::make('password')
        ]);
        $user->roles()->attach($role->id);
    }
    
    echo "Rol: " . $role->name . "\n";
    echo "Email: " . $user->email . "\n";
    echo "Password: password\n";
    echo "-----------------------------\n";
}

// User previously given
$adminUser = User::where('email', 'test@example.com')->first();
if ($adminUser) {
    echo "Rol: Admin Principal (Anterior)\n";
    echo "Email: test@example.com\n";
    echo "Password: password\n";
    echo "-----------------------------\n";
}
