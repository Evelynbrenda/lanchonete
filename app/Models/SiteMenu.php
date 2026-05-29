<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteMenu extends Model
{
    protected $fillable = [
        'label',
        'url',
        'ordem',
        'ativo',
    ];
}
