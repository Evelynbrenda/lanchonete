<?php

namespace Database\Seeders;

use App\Models\DeliveryRoute;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DeliveryRouteSeeder extends Seeder
{
    public function run(): void
    {
        $rotas = [
            ['nome' => 'Brejinho', 'taxa' => 5.00],
            ['nome' => 'Riacho', 'taxa' => 5.00],
            ['nome' => 'Sítio do meio', 'taxa' => 4.00],
            ['nome' => 'Barro branco', 'taxa' => 4.00],
            ['nome' => 'Minha casa minha vida', 'taxa' => 5.00],
            ['nome' => 'Barreiras', 'taxa' => 10.00],
            ['nome' => 'Forquilha', 'taxa' => 8.00],
            ['nome' => 'Vila Cruz', 'taxa' => 7.00],
            ['nome' => 'Usina', 'taxa' => 7.00],
        ];

        foreach ($rotas as $index => $rota) {
            DeliveryRoute::updateOrCreate(
                ['slug' => Str::slug($rota['nome'])],
                [
                    'nome' => $rota['nome'],
                    'endereco' => $rota['nome'],
                    'taxa' => $rota['taxa'],
                    'ordem' => $index,
                    'ativo' => true,
                ]
            );
        }
    }
}
