<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteNotice extends Model
{
    protected $fillable = [
        'titulo',
        'descricao',
        'ordem',
        'ativo',
    ];
}
