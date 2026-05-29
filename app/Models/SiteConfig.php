<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteConfig extends Model
{
    protected $fillable = [
        'chave',
        'valor',
    ];

    public static function valor(string $chave, ?string $default = null): ?string
    {
        return static::where('chave', $chave)->value('valor') ?? $default;
    }

    public static function definir(string $chave, ?string $valor): void
    {
        static::updateOrCreate(['chave' => $chave], ['valor' => $valor]);
    }
}
