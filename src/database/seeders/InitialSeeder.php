<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InitialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            StateSeeder::class,
            PermissionSeeder::class,
            RoleSeeder::class,
            PersonSeeder::class,
            UserSeeder::class,
        ]);
    }
}
