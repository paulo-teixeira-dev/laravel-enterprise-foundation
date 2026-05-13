<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | PERMISSIONS (com wildcard pattern)
        |--------------------------------------------------------------------------
        */

        $permissions = [
            'user.index',
            'user.lookup',
            'user.store',
            'user.show',
            'user.update',
            'user.assign_role',
            'state.lookup',
            'role.index',
            'role.lookup',
            'role.store',
            'role.show',
            'role.update',
            'role.assign_permission',
            'person.index',
            'person.lookup',
            'person.store',
            'person.show',
            'person.update',
            'permission.lookup',
            'file.index',
            'file.lookup',
            'file.store',
            'file.show',
            'file.update',
            'audit.index',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api',
            ]);
        }
    }
}
