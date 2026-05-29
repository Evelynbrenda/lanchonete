<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;
    protected $fillable = [
        'nome_cliente',
        'cliente_telefone',
        'cliente_endereco',
        'tipo_atendimento',
        'forma_pagamento',
        'observacoes',
        'total',
        'status',
    ];

    public function itens()
    {
        return $this->hasMany(PedidoItem::class);
    }

}
