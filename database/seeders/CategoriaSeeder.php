<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['nome' => 'Hambúrgueres', 'slug' => 'hamburgueres'],
            ['nome' => 'Pizzas', 'slug' => 'pizzas'],
            ['nome' => 'Bebidas', 'slug' => 'bebidas'],
            ['nome' => 'Açais', 'slug' => 'acais'],
            ['nome' => 'Sucos', 'slug' => 'sucos'],
            ['nome' => 'Pastéis', 'slug' => 'pasteis'],
        ];

        foreach ($categorias as $categoria) {
            Categoria::updateOrCreate(
                ['slug' => $categoria['slug']],
                ['nome' => $categoria['nome']]
            );
        }
    }
}
