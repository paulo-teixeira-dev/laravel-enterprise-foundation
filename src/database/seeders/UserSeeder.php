<?php

namespace Database\Seeders;

use App\Models\PersonProfile;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $person = PersonProfile::where('first_name', 'Admin')->first();
        $user = $person->User()->create([
            'email' => 'admin@example.com',
            'password' => 'admin@123',
        ]);

        $user->syncRoles('admin');
    }
}
