<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->boolean('usa_adicionais')->default(false)->after('ativo');
            $table->text('adicionais_texto')->nullable()->after('usa_adicionais');
            $table->decimal('valor_adicional', 8, 2)->default(0)->after('adicionais_texto');
            $table->boolean('usa_opcoes')->default(false)->after('valor_adicional');
            $table->text('opcoes_texto')->nullable()->after('usa_opcoes');
            $table->boolean('usa_tamanhos')->default(false)->after('opcoes_texto');
        });
    }

    public function down(): void
    {
        Schema::table('categorias', function (Blueprint $table) {
            $table->dropColumn([
                'usa_adicionais',
                'adicionais_texto',
                'valor_adicional',
                'usa_opcoes',
                'opcoes_texto',
                'usa_tamanhos',
            ]);
        });
    }
};
