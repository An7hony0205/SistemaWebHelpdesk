<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::firstOrCreate(['name' => 'ver tickets']);
        Permission::firstOrCreate(['name' => 'crear tickets']);
        Permission::firstOrCreate(['name' => 'editar tickets']);
        Permission::firstOrCreate(['name' => 'cerrar tickets']);
        Permission::firstOrCreate(['name' => 'gestionar categorias']);
        Permission::firstOrCreate(['name' => 'gestionar usuarios']);

        // Crear roles y asignar permisos
        $roleUsuario = Role::firstOrCreate(['name' => 'Usuario Final']);
        $roleUsuario->givePermissionTo(['ver tickets', 'crear tickets']);

        $roleSoporte = Role::firstOrCreate(['name' => 'Soporte']);
        $roleSoporte->givePermissionTo(['ver tickets', 'crear tickets', 'editar tickets', 'cerrar tickets']);

        $roleAdmin = Role::firstOrCreate(['name' => 'Admin']);
        $roleAdmin->givePermissionTo(Permission::all());
    }
}
