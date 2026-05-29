<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Produto extends Model
{
    use HasFactory;
    protected $fillable = [
        'categoria_id',
        'nome',
        'descricao',
        'preco',
        'preco_p',
        'preco_m',
        'preco_g',
        'imagem',
        'ativo',
        'disponibilidade_modo',
        'dias_disponiveis',
    ];

    protected $casts = [
        'ativo' => 'boolean',
        'dias_disponiveis' => 'array',
    ];

    public function categoria(){
        return $this->belongsTo(Categoria::class);
    }

    public function getImagemUrlAttribute(): ?string
    {
        if (!$this->imagem) {
            return null;
        }

        if (Str::startsWith($this->imagem, ['http://', 'https://'])) {
            return $this->imagem;
        }

        return Storage::url($this->imagem);
    }
}
