<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categoria;
use App\Models\SiteConfig;
use Carbon\Carbon;

class CardapioController extends Controller
{
    public function index()
    {
        $categorias = Categoria::with('produtos')->where('ativo', true)->get();
        $logoUrl = SiteConfig::valor('logo_url', '/images/logo.png');
        $lojaDiasAbertos = collect(explode(',', (string) SiteConfig::valor('loja_dias_abertos', '1,2,3,4,5,6')))
            ->map(fn ($d) => (int) trim($d))
            ->filter(fn ($d) => $d >= 0 && $d <= 6)
            ->values();
        $lojaHorarioAbertura = SiteConfig::valor('loja_horario_abertura', '18:00');
        $lojaHorarioFechamento = SiteConfig::valor('loja_horario_fechamento', '23:00');

        $agora = Carbon::now(config('app.timezone'));
        $diaSemanaAtual = (int) $agora->dayOfWeek;
        $lojaAbertaAgora = $this->lojaAbertaAgora($lojaDiasAbertos, $lojaHorarioAbertura, $lojaHorarioFechamento, $agora);

        return view('cardapio.index', compact(
            'categorias',
            'logoUrl',
            'lojaAbertaAgora',
            'diaSemanaAtual'
        ));
    }

    private function lojaAbertaAgora($diasAbertos, string $horarioAbertura, string $horarioFechamento, Carbon $agora): bool
    {
        if (!$diasAbertos->contains((int) $agora->dayOfWeek)) {
            return false;
        }

        $atual = Carbon::createFromFormat('H:i', $agora->format('H:i'), config('app.timezone'));
        $abertura = Carbon::createFromFormat('H:i', $horarioAbertura, config('app.timezone'));
        $fechamento = Carbon::createFromFormat('H:i', $horarioFechamento, config('app.timezone'));

        // Ex.: 18:00 -> 02:00 (vira a madrugada)
        if ($fechamento->lessThanOrEqualTo($abertura)) {
            return $atual->greaterThanOrEqualTo($abertura) || $atual->lessThanOrEqualTo($fechamento);
        }

        return $atual->between($abertura, $fechamento, true);
    }
}
