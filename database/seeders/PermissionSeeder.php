<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\Permissions\PermissionRegistry;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionRegistry::definitions() as $definition) {
            Permission::query()->updateOrCreate(
                ['name' => $definition['name'], 'guard_name' => 'web'],
                []
            );
        }

        foreach (PermissionRegistry::defaultRoles() as $roleDefinition) {
            $role = Role::query()->firstOrCreate([
                'name' => $roleDefinition['name'],
                'guard_name' => 'web',
            ]);

            $role->display_name = $roleDefinition['display_name'];
            $role->save();

            $role->syncPermissions($roleDefinition['permissions']);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
