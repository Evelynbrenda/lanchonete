<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Categoria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'ativo',
        'usa_adicionais',
        'adicionais_texto',
        'valor_adicional',
        'usa_opcoes',
        'opcoes_texto',
        'usa_tamanhos',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'usa_adicionais' => 'boolean',
        'usa_opcoes' => 'boolean',
        'usa_tamanhos' => 'boolean',
        'valor_adicional' => 'decimal:2',
    ];

    public function produtos(){
        return $this->hasMany(Produto::class);
    }

    public function getAdicionaisListaAttribute(): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $this->adicionais_texto))
            ->map(fn ($item) => trim((string) $item))
            ->filter()
            ->values()
            ->all();
    }

    public function getOpcoesListaAttribute(): array
    {
        $lista = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $this->opcoes_texto) as $linha) {
            $linha = trim((string) $linha);
            if ($linha === '') {
                continue;
            }

            $partesPipe = array_map('trim', explode('|', $linha));
            if (count($partesPipe) >= 3) {
                $chave = $partesPipe[0];
                $nome = $partesPipe[1];
                $preco = (float) str_replace(',', '.', $partesPipe[2]);
            } elseif (count($partesPipe) === 2) {
                $nome = $partesPipe[0];
                $preco = (float) str_replace(',', '.', $partesPipe[1]);
                $chave = \Illuminate\Support\Str::slug($nome);
            } else {
                $partesHifen = preg_split('/\s*-\s*/', $linha);
                if (count($partesHifen) < 2) {
                    continue;
                }
                $precoRaw = array_pop($partesHifen);
                $nome = trim(implode(' - ', $partesHifen));
                $preco = (float) str_replace(',', '.', (string) $precoRaw);
                $chave = \Illuminate\Support\Str::slug($nome);
            }

            if ($nome === '' || $preco < 0) {
                continue;
            }

            if (($chave ?? '') === '') {
                $chave = \Illuminate\Support\Str::slug($nome);
            }

            $lista[] = [
                'chave' => $chave,
                'nome' => $nome,
                'preco' => max(0, $preco),
            ];
        }

        return $lista;
    }
}
