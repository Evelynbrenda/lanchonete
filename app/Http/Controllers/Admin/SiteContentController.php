<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryRoute;
use App\Models\SiteConfig;
use App\Models\SiteNotice;
use App\Models\SitePromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SiteContentController extends Controller
{
    public function index()
    {
        return view('admin.site.index', [
            'avisos' => SiteNotice::orderBy('ordem')->get(),
            'promocoes' => SitePromotion::orderBy('ordem')->get(),
            'rotasEntrega' => DeliveryRoute::orderBy('ordem')->orderBy('nome')->get(),
            'tituloSite' => SiteConfig::valor('titulo_site', 'Lanchonete'),
            'subtituloSite' => SiteConfig::valor('subtitulo_site', ''),
            'logoUrl' => SiteConfig::valor('logo_url', '/images/logo.png'),
            'sobreTitulo' => SiteConfig::valor('sobre_titulo', 'Sobre nós'),
            'sobreTexto' => SiteConfig::valor('sobre_texto', ''),
            'lojaDiasAbertos' => SiteConfig::valor('loja_dias_abertos', '1,2,3,4,5,6'),
            'lojaHorarioAbertura' => SiteConfig::valor('loja_horario_abertura', '18:00'),
            'lojaHorarioFechamento' => SiteConfig::valor('loja_horario_fechamento', '23:00'),
        ]);
    }

    public function updateConfigs(Request $request)
    {
        $secao = (string) $request->input('secao', '');
        $dados = [];

        if ($secao === 'funcionamento') {
            if (!$request->has('loja_dias_abertos')) {
                $request->merge([
                    'loja_dias_abertos' => [],
                ]);
            }

            if (!$request->filled('loja_horario_abertura')) {
                $request->merge([
                    'loja_horario_abertura' => '18:00',
                ]);
            }
            if (!$request->filled('loja_horario_fechamento')) {
                $request->merge([
                    'loja_horario_fechamento' => '23:00',
                ]);
            }

            $dados = $request->validate([
                'loja_dias_abertos' => 'nullable|array',
                'loja_dias_abertos.*' => 'integer|between:0,6',
                'loja_horario_abertura' => 'nullable|date_format:H:i',
                'loja_horario_fechamento' => 'nullable|date_format:H:i',
            ]);
        } else {
            $dados = $request->validate([
                'titulo_site' => 'required|string|max:120',
                'subtitulo_site' => 'nullable|string|max:255',
                'logo_file' => 'nullable|image|max:4096',
                'sobre_titulo' => 'required|string|max:120',
                'sobre_texto' => 'nullable|string|max:3000',
            ]);
        }

        if (array_key_exists('loja_dias_abertos', $dados)) {
            $dados['loja_dias_abertos'] = implode(',', array_values(array_map('intval', $dados['loja_dias_abertos'] ?? [])));
        }

        if ($request->hasFile('logo_file')) {
            $logoAtual = SiteConfig::valor('logo_url');
            if ($logoAtual && str_starts_with($logoAtual, '/storage/logos/')) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $logoAtual));
            }

            $path = $request->file('logo_file')->store('logos', 'public');
            $dados['logo_url'] = '/storage/' . $path;
        }

        unset($dados['logo_file']);

        foreach ($dados as $chave => $valor) {
            SiteConfig::definir($chave, $valor);
        }

        return back()->with('ok', 'Conteúdo institucional atualizado.');
    }

    public function storeAviso(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:120',
            'descricao' => 'nullable|string|max:800',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        SiteNotice::create([
            ...$dados,
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => (bool) ($dados['ativo'] ?? false),
        ]);

        return back()->with('ok', 'Aviso criado.');
    }

    public function updateAviso(Request $request, SiteNotice $aviso)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:120',
            'descricao' => 'nullable|string|max:800',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        $aviso->update([
            ...$dados,
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => (bool) ($dados['ativo'] ?? false),
        ]);

        return back()->with('ok', 'Aviso atualizado.');
    }

    public function destroyAviso(SiteNotice $aviso)
    {
        $aviso->delete();

        return back()->with('ok', 'Aviso removido.');
    }

    public function storePromocao(Request $request)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:120',
            'descricao' => 'nullable|string|max:800',
            'preco_destaque' => 'nullable|string|max:40',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        SitePromotion::create([
            ...$dados,
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => (bool) ($dados['ativo'] ?? false),
        ]);

        return back()->with('ok', 'Promoção criada.');
    }

    public function updatePromocao(Request $request, SitePromotion $promocao)
    {
        $dados = $request->validate([
            'titulo' => 'required|string|max:120',
            'descricao' => 'nullable|string|max:800',
            'preco_destaque' => 'nullable|string|max:40',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        $promocao->update([
            ...$dados,
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => (bool) ($dados['ativo'] ?? false),
        ]);

        return back()->with('ok', 'Promoção atualizada.');
    }

    public function destroyPromocao(SitePromotion $promocao)
    {
        $promocao->delete();

        return back()->with('ok', 'Promoção removida.');
    }

    public function storeRota(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:120',
            'endereco' => 'nullable|string|max:255',
            'taxa' => 'required|numeric|min:0',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        DeliveryRoute::create([
            'nome' => $dados['nome'],
            'slug' => $this->slugRotaUnico($dados['nome']),
            'endereco' => $dados['endereco'] ?? null,
            'taxa' => $dados['taxa'],
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => (bool) ($dados['ativo'] ?? false),
        ]);

        return back()->with('ok', 'Rota de entrega criada.');
    }

    public function updateRota(Request $request, DeliveryRoute $rota)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:120',
            'endereco' => 'nullable|string|max:255',
            'taxa' => 'required|numeric|min:0',
            'ordem' => 'nullable|integer|min:0',
            'ativo' => 'nullable|boolean',
        ]);

        $rota->update([
            'nome' => $dados['nome'],
            'slug' => $this->slugRotaUnico($dados['nome'], $rota->id),
            'endereco' => $dados['endereco'] ?? null,
            'taxa' => $dados['taxa'],
            'ordem' => $dados['ordem'] ?? 0,
            'ativo' => (bool) ($dados['ativo'] ?? false),
        ]);

        return back()->with('ok', 'Rota de entrega atualizada.');
    }

    public function destroyRota(DeliveryRoute $rota)
    {
        $rota->delete();

        return back()->with('ok', 'Rota de entrega removida.');
    }

    private function slugRotaUnico(string $nome, ?int $ignorarId = null): string
    {
        $base = Str::slug($nome);
        $base = $base !== '' ? $base : 'rota';
        $slug = $base;
        $contador = 2;

        while (
            DeliveryRoute::query()
                ->where('slug', $slug)
                ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
                ->exists()
        ) {
            $slug = $base . '-' . $contador;
            $contador++;
        }

        return $slug;
    }
}
