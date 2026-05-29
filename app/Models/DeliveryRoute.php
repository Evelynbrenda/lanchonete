<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRoute extends Model
{
    use HasFactory;

    protected $fillable = [
        'nome',
        'slug',
        'endereco',
        'taxa',
        'ordem',
        'ativo',
    ];
}
