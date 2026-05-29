<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\SiteConfig;
use App\Models\SiteNotice;
use App\Models\SitePromotion;

class SiteController extends Controller
{
    public function index()
    {
        $avisos = SiteNotice::where('ativo', true)->orderBy('ordem')->get();
        $promocoes = SitePromotion::where('ativo', true)->orderBy('ordem')->get();
        $categorias = Categoria::with(['produtos' => function ($query) {
            $query->where('ativo', true)->orderBy('nome');
        }])->where('ativo', true)->orderBy('nome')->get();

        return view('site.index', [
            'avisos' => $avisos,
            'promocoes' => $promocoes,
            'categorias' => $categorias,
            'tituloSite' => SiteConfig::valor('titulo_site', 'Lanchonete'),
            'subtituloSite' => SiteConfig::valor('subtitulo_site', 'Monte seu pedido em poucos cliques.'),
            'logoUrl' => SiteConfig::valor('logo_url', '/images/logo.png'),
            'sobreTitulo' => SiteConfig::valor('sobre_titulo', 'Sobre nós'),
            'sobreTexto' => SiteConfig::valor('sobre_texto', 'Uma lanchonete familiar focada em sabor, preço justo e agilidade.'),
        ]);
    }
}
