<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SitePromotion extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'preco_destaque',
        'ordem',
        'ativo',
    ];
}
