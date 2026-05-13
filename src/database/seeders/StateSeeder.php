<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $states = [
            ['id' => '11', 'acronym' => 'RO', 'name' => 'Rondônia'],
            ['id' => '12', 'acronym' => 'AC', 'name' => 'Acre'],
            ['id' => '13', 'acronym' => 'AM', 'name' => 'Amazonas'],
            ['id' => '14', 'acronym' => 'RR', 'name' => 'Roraima'],
            ['id' => '15', 'acronym' => 'PA', 'name' => 'Pará'],
            ['id' => '16', 'acronym' => 'AP', 'name' => 'Amapá'],
            ['id' => '17', 'acronym' => 'TO', 'name' => 'Tocantins'],
            ['id' => '21', 'acronym' => 'MA', 'name' => 'Maranhão'],
            ['id' => '22', 'acronym' => 'PI', 'name' => 'Piauí'],
            ['id' => '23', 'acronym' => 'CE', 'name' => 'Ceará'],
            ['id' => '24', 'acronym' => 'RN', 'name' => 'Rio Grande do Norte'],
            ['id' => '25', 'acronym' => 'PB', 'name' => 'Paraíba'],
            ['id' => '26', 'acronym' => 'PE', 'name' => 'Pernambuco'],
            ['id' => '27', 'acronym' => 'AL', 'name' => 'Alagoas'],
            ['id' => '28', 'acronym' => 'SE', 'name' => 'Sergipe'],
            ['id' => '29', 'acronym' => 'BA', 'name' => 'Bahia'],
            ['id' => '31', 'acronym' => 'MG', 'name' => 'Minas Gerais'],
            ['id' => '32', 'acronym' => 'ES', 'name' => 'Espírito Santo'],
            ['id' => '33', 'acronym' => 'RJ', 'name' => 'Rio de Janeiro'],
            ['id' => '35', 'acronym' => 'SP', 'name' => 'São Paulo'],
            ['id' => '41', 'acronym' => 'PR', 'name' => 'Paraná'],
            ['id' => '42', 'acronym' => 'SC', 'name' => 'Santa Catarina'],
            ['id' => '43', 'acronym' => 'RS', 'name' => 'Rio Grande do Sul'],
            ['id' => '50', 'acronym' => 'MS', 'name' => 'Mato Grosso do Sul'],
            ['id' => '51', 'acronym' => 'MT', 'name' => 'Mato Grosso'],
            ['id' => '52', 'acronym' => 'GO', 'name' => 'Goiás'],
            ['id' => '53', 'acronym' => 'DF', 'name' => 'Distrito Federal'],
        ];

        State::insert($states);
    }
}
