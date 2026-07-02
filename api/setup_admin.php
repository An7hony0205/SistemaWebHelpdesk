<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

use App\Domains\Identity\Tenant;
use App\Domains\Identity\User;
use Illuminate\Contracts\Console\Kernel;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

$tenant = Tenant::first();
if (! $tenant) {
    $tenant = Tenant::create(['name' => 'Organizacion Principal']);
}

$user = User::where('email', 'test@example.com')->first();
if (! $user) {
    $user = User::create([
        'name' => 'Admin Test',
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
        'tenant_id' => $tenant->id,
    ]);
}

Permission::firstOrCreate(['name' => 'manage settings']);
Permission::firstOrCreate(['name' => 'manage kb']);
$role = Role::firstOrCreate(['name' => 'Admin']);
$role->givePermissionTo(Permission::all());
$user->assignRole('Admin');

echo "Admin user setup complete.\n";
