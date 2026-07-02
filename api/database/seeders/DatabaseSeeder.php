<?php

namespace Database\Seeders;

use App\Domains\Identity\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $tenant = \App\Domains\Identity\Tenant::create([
            'name' => 'Organizacion Principal',
        ]);

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'tenant_id' => $tenant->id,
        ]);

        $this->call([
            RoleSeeder::class,
            CatalogSeeder::class,
        ]);
    }
}
