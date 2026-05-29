<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Categoria;
use App\Models\Produto;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hamburgueres = Categoria::where('slug', 'hamburgueres')->firstOrFail();
        $pizzas = Categoria::where('slug', 'pizzas')->firstOrFail();
        $bebidas = Categoria::where('slug', 'bebidas')->firstOrFail();
        $acais = Categoria::where('slug', 'acais')->firstOrFail();
        $sucos = Categoria::where('slug', 'sucos')->firstOrFail();
        $pasteis = Categoria::where('slug', 'pasteis')->firstOrFail();

        $produtos = [
            [
                'categoria_id' => $hamburgueres->id,
                'nome' => 'Hambúrguer Completo',
                'descricao' => 'Pão, carne, queijo, ovo e salada',
                'preco' => 10.00,
            ],
            [
                'categoria_id' => $pizzas->id,
                'nome' => 'Pizza Calabresa',
                'descricao' => 'Calabresa, queijo e orégano',
                'preco' => 35.00,
            ],
            [
                'categoria_id' => $bebidas->id,
                'nome' => 'Refrigerante Lata',
                'descricao' => '350ml',
                'preco' => 6.00,
            ],
            [
                'categoria_id' => $acais->id,
                'nome' => 'Açai',
                'descricao' => '400ml',
                'preco' => 10.00,
            ],
        ];

        foreach ($produtos as $produto) {
            Produto::updateOrCreate(
                ['categoria_id' => $produto['categoria_id'], 'nome' => $produto['nome']],
                ['descricao' => $produto['descricao'], 'preco' => $produto['preco']]
            );
        }
    }
}
