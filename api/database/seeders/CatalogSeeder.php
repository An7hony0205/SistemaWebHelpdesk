<?php

namespace Database\Seeders;

use App\Domains\Administration\Priority;
use App\Domains\Administration\Status;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['name' => 'Abierto', 'color' => '#10B981'],
            ['name' => 'En Progreso', 'color' => '#3B82F6'],
            ['name' => 'Pendiente', 'color' => '#F59E0B'],
            ['name' => 'Resuelto', 'color' => '#6366F1'],
            ['name' => 'Cerrado', 'color' => '#6B7280'],
        ];

        foreach ($statuses as $status) {
            Status::firstOrCreate(['name' => $status['name']], $status);
        }

        $priorities = [
            ['name' => 'Baja', 'level' => 1, 'color' => '#10B981'],
            ['name' => 'Media', 'level' => 2, 'color' => '#F59E0B'],
            ['name' => 'Alta', 'level' => 3, 'color' => '#EF4444'],
            ['name' => 'Urgente', 'level' => 4, 'color' => '#991B1B'],
        ];

        foreach ($priorities as $priority) {
            Priority::firstOrCreate(['name' => $priority['name']], $priority);
        }
    }
}
