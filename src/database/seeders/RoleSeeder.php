<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        /*
       |--------------------------------------------------------------------------
       | ROLES
       |--------------------------------------------------------------------------
       */

        $roles = [
            'admin',
            'manager',
            'sales',
            'cashier',
            'finance',
            'staff',
            'technician',
            'support',
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate([
                'name' => $role,
                'guard_name' => 'api',
            ]);
        }
    }
}
