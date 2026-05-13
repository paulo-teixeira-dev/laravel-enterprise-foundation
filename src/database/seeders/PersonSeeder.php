<?php

namespace Database\Seeders;

use App\Models\PersonProfile;
use Illuminate\Database\Seeder;

class PersonSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $person_profile = [
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'birth_date' => '2000-01-01',
            'gender' => 'm',
            'nationality' => 'lorem',
            'address' => 'lorem',
            'number' => '123',
            'complement' => 'lorem',
            'neighborhood' => 'lorem',
            'city' => 'lorem',
            'state_id' => '35',
            'postal_code' => '00000-000',
        ];

        $person = PersonProfile::create($person_profile);

        $person->PersonContact()->create([
            'phone' => '00000000000',
            'email' => 'admin@example.com.br',
            'type' => 'per',
        ]);
    }
}
