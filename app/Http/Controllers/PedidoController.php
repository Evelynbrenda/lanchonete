<?php

namespace App\Http\Controllers;

use App\Models\PedidoItem;
use App\Models\Produto;
use App\Models\DeliveryRoute;
use App\Models\SiteConfig;
use App\Models\Categoria;
use Illuminate\Http\Request;
use App\Models\Pedido;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PedidoController extends Controller
{
    private const ACAI_OPCOES_PADRAO = "Com leite em pó e leite condensado|10\nCom fruta (kiwi ou morango)|12\nCom cobertura (granola, pó de amendoim, chocobol, M&M)|12\nCompleto (fruta e cobertura)|14";
    private const BEBIDA_PRECOS = [
        'coca-1l' => 8.00,
        'cajuina-1l' => 7.00,
        'coca-2l' => 13.00,
        'cajuina-2l' => 12.00,
    ];

    public function store(Request $request)
    {

        $dados = $request->validate(
            [
                'cliente_nome' => 'required|string|max:255',
                'cliente_telefone' => 'required|string|max:20',
                'cliente_endereco' => 'nullable|string|max:255',
                'tipo_atendimento' => 'required|in:entrega,retirada',
                'rota_entrega' => 'nullable|string',
                'mesa_numero' => 'nullable|string|max:20',
                'taxa_entrega' => 'nullable|numeric|min:0',
                'forma_pagamento' => 'required|string|max:50',
                'observacao' => 'nullable|string|max:500',
                'itens' => 'required|array',
                'itens.*.id' => 'required|integer|exists:produtos,id',
                'itens.*.nome' => 'nullable|string|max:255',
                'itens.*.detalhes' => 'nullable|string|max:255',
                'itens.*.tamanho' => 'nullable|string|max:5',
                'itens.*.quantidade' => 'required|integer|min:1',
                'itens.*.preco' => 'required|numeric|min:0',
                'itens.*.adicionais' => 'nullable|integer|min:0',
                'itens.*.meio_a_meio' => 'nullable|boolean',
                'itens.*.sabor_2_id' => 'nullable|integer|exists:produtos,id',
                'itens.*.acai_opcao' => 'nullable|string|max:120',

            ]
        );

        if (!$this->lojaAbertaAgora()) {
            return response()->json([
                'message' => 'A loja está fechada no momento.',
            ], 422);
        }

        $rotaSelecionada = null;
        if ($dados['tipo_atendimento'] === 'entrega') {
            $rotaKey = $dados['rota_entrega'] ?? '';
            $rotaSelecionada = DeliveryRoute::query()
                ->where('slug', $rotaKey)
                ->where('ativo', true)
                ->first();
            if (!$rotaSelecionada) {
                return response()->json([
                    'message' => 'Rota de entrega invalida.',
                ], 422);
            }
        }

        if ($dados['tipo_atendimento'] === 'entrega' && empty(trim((string) ($dados['cliente_endereco'] ?? '')))) {
            return response()->json([
                'message' => 'Endereco e obrigatorio para entrega.',
            ], 422);
        }

        $idsProdutos = collect($dados['itens'])->pluck('id')
            ->merge(collect($dados['itens'])->pluck('sabor_2_id')->filter())
            ->unique()
            ->values()
            ->all();

        $produtos = Produto::with('categoria')
            ->whereIn('id', $idsProdutos)
            ->get()
            ->keyBy('id');

        $itensCalculados = [];
        $total = 0;
        foreach ($dados['itens'] as $item) {
            $produto = $produtos->get($item['id']);
            if (!$produto) {
                return response()->json(['message' => 'Produto inválido no carrinho.'], 422);
            }
            if (!$this->produtoDisponivelAgora($produto)) {
                return response()->json(['message' => "O produto {$produto->nome} está indisponível no momento."], 422);
            }

            $precoUnitario = $this->calcularPrecoItem($produto, $item, $produtos);
            $subtotal = $precoUnitario * $item['quantidade'];
            $total += $subtotal;

            $itensCalculados[] = [
                'item' => $item,
                'preco_unitario' => $precoUnitario,
                'subtotal' => $subtotal,
            ];
        }
        $taxaEntrega = $dados['tipo_atendimento'] === 'entrega'
            ? (float) $rotaSelecionada->taxa
            : 0;
        $total += $taxaEntrega;

        $observacoesExtras = [];
        if ($dados['tipo_atendimento'] === 'retirada') {
            if (!empty($dados['mesa_numero'])) {
                $observacoesExtras[] = 'Retirada: ' . $dados['mesa_numero'];
            }
        }
        if ($dados['tipo_atendimento'] === 'entrega') {
            $observacoesExtras[] = 'Rota: ' . $rotaSelecionada->nome;
        }
        if (!empty($dados['observacao'])) {
            $observacoesExtras[] = $dados['observacao'];
        }
        $observacoes = implode(' | ', $observacoesExtras);

        $pedido = Pedido::create([
            'nome_cliente' => $dados['cliente_nome'],
            'cliente_telefone' => $dados['cliente_telefone'],
            'cliente_endereco' => $dados['tipo_atendimento'] === 'retirada'
                ? ''
                : trim((string) ($dados['cliente_endereco'] ?? ($rotaSelecionada->endereco ?: $rotaSelecionada->nome))),
            'tipo_atendimento' => $dados['tipo_atendimento'],
            'forma_pagamento' => $dados['forma_pagamento'],
            'observacoes' => $observacoes ?: null,
            'total' => $total,
            'status' => 'novo',
        ]);

        foreach ($itensCalculados as $itemCalculado) {
            $item = $itemCalculado['item'];
            $tamanhoItem = $item['tamanho'] ?? null;
            if (empty($tamanhoItem) && !empty($item['detalhes'])) {
                if (preg_match('/tamanho[:\s]+([PMG])/i', $item['detalhes'], $match)) {
                    $tamanhoItem = strtoupper($match[1]);
                }
            }

            PedidoItem::create([
                'pedido_id' => $pedido->id,
                'produto_id' => $item['id'],
                'nome_item' => $item['nome'] ?? null,
                'detalhes_item' => $item['detalhes'] ?? null,
                'tamanho_item' => $tamanhoItem,
                'quantidade' => $item['quantidade'],
                'preco_unitario' => $itemCalculado['preco_unitario'],
                'subtotal' => $itemCalculado['subtotal'],
            ]);
        }

        return response()->json(
            [
                'pedido_id' => $pedido->id,
                'total' => $total,
                'taxa_entrega' => $taxaEntrega,
            ]
        );
    }

    private function lojaAbertaAgora(): bool
    {
        $diasAbertos = collect(explode(',', (string) SiteConfig::valor('loja_dias_abertos', '1,2,3,4,5,6')))
            ->map(fn ($d) => (int) trim($d))
            ->filter(fn ($d) => $d >= 0 && $d <= 6)
            ->values();
        $horarioAbertura = SiteConfig::valor('loja_horario_abertura', '18:00');
        $horarioFechamento = SiteConfig::valor('loja_horario_fechamento', '23:00');

        $agora = Carbon::now(config('app.timezone'));
        if (!$diasAbertos->contains((int) $agora->dayOfWeek)) {
            return false;
        }

        $atual = Carbon::createFromFormat('H:i', $agora->format('H:i'), config('app.timezone'));
        $abertura = Carbon::createFromFormat('H:i', $horarioAbertura, config('app.timezone'));
        $fechamento = Carbon::createFromFormat('H:i', $horarioFechamento, config('app.timezone'));

        if ($fechamento->lessThanOrEqualTo($abertura)) {
            return $atual->greaterThanOrEqualTo($abertura) || $atual->lessThanOrEqualTo($fechamento);
        }

        return $atual->between($abertura, $fechamento, true);
    }

    private function produtoDisponivelAgora(Produto $produto): bool
    {
        if (($produto->disponibilidade_modo ?? 'sempre') !== 'dias') {
            return true;
        }

        $diasProduto = collect($produto->dias_disponiveis ?? [])
            ->map(fn ($d) => (int) $d)
            ->filter(fn ($d) => $d >= 0 && $d <= 6)
            ->values();

        return $diasProduto->contains((int) Carbon::now(config('app.timezone'))->dayOfWeek);
    }

    private function calcularPrecoItem(Produto $produto, array $item, $produtos): float
    {
        $categoriaSlug = $produto->categoria?->slug ?? '';
        $categoriaSlugNormalizado = mb_strtolower($categoriaSlug);
        $nomeNormalizado = mb_strtolower($produto->nome);

        if ($categoriaSlugNormalizado === 'acais' || (bool) ($produto->categoria?->usa_opcoes ?? false)) {
            $opcoesAcai = $this->mapaOpcoesCategoria($produto->categoria);
            $chaveAcai = (string) ($item['acai_opcao'] ?? '');
            if ($chaveAcai === '') {
                $chaveAcai = $this->extrairChaveAcai($item, $opcoesAcai);
            }
            if (!$chaveAcai || !array_key_exists($chaveAcai, $opcoesAcai)) {
                throw ValidationException::withMessages([
                    'itens' => 'Opção inválida para esta categoria.',
                ]);
            }
            return (float) $opcoesAcai[$chaveAcai];
        }

        if ($categoriaSlugNormalizado === 'bebidas' && (str_contains($nomeNormalizado, 'coca') || str_contains($nomeNormalizado, 'caju'))) {
            $chaveBebida = $this->extrairChaveBebida($item);
            if (!$chaveBebida || !array_key_exists($chaveBebida, self::BEBIDA_PRECOS)) {
                throw ValidationException::withMessages([
                    'itens' => 'Opção de bebida inválida.',
                ]);
            }
            return self::BEBIDA_PRECOS[$chaveBebida];
        }

        if ($categoriaSlugNormalizado === 'pizzas') {
            $tamanho = strtoupper((string)($item['tamanho'] ?? 'M'));
            $precoPrimeiroSabor = $this->precoPizzaPorTamanho($produto, $tamanho);

            if (!empty($item['meio_a_meio']) && !empty($item['sabor_2_id'])) {
                $segundoSabor = $produtos->get((int) $item['sabor_2_id']);
                if (!$segundoSabor || mb_strtolower((string) ($segundoSabor->categoria?->slug ?? '')) !== 'pizzas') {
                    throw ValidationException::withMessages([
                        'itens' => 'Segundo sabor de pizza inválido.',
                    ]);
                }
                $precoSegundoSabor = $this->precoPizzaPorTamanho($segundoSabor, $tamanho);
                return ($precoPrimeiroSabor / 2) + ($precoSegundoSabor / 2);
            }

            return $precoPrimeiroSabor;
        }

        if (str_contains($categoriaSlugNormalizado, 'past') || (bool) ($produto->categoria?->usa_adicionais ?? false)) {
            $adicionais = max(0, (int)($item['adicionais'] ?? 0));
            $valorAdicionalPastel = max(0, (float)($produto->categoria->valor_adicional ?? 0));
            if ($valorAdicionalPastel <= 0) {
                $valorAdicionalPastel = max(0, (float) SiteConfig::valor('pastel_adicional_valor', '2'));
            }
            return (float)$produto->preco + ($adicionais * $valorAdicionalPastel);
        }

        return (float)$produto->preco;
    }

    private function precoPizzaPorTamanho(Produto $produto, string $tamanho): float
    {
        return match ($tamanho) {
            'P' => (float)($produto->preco_p ?? $produto->preco),
            'G' => (float)($produto->preco_g ?? $produto->preco),
            default => (float)($produto->preco_m ?? $produto->preco),
        };
    }

    private function extrairChaveAcai(array $item, array $mapaOpcoes): ?string
    {
        $detalhes = mb_strtolower((string)($item['detalhes'] ?? ''));
        foreach ($mapaOpcoes as $chave => $preco) {
            if (str_contains($detalhes, mb_strtolower((string) $chave))) {
                return (string) $chave;
            }
        }
        return null;
    }

    private function mapaOpcoesCategoria(?Categoria $categoria): array
    {
        $texto = trim((string) ($categoria->opcoes_texto ?? ''));
        if ($texto === '') {
            $texto = SiteConfig::valor('acai_opcoes', self::ACAI_OPCOES_PADRAO);
        }

        $mapa = [];
        foreach (preg_split('/\r\n|\r|\n/', (string) $texto) as $linha) {
            $linha = trim((string) $linha);
            if ($linha === '') {
                continue;
            }

            $partesPipe = array_map('trim', explode('|', $linha));
            if (count($partesPipe) >= 3) {
                $chave = $partesPipe[0];
                $nome = $partesPipe[1];
                $preco = (float) str_replace(',', '.', $partesPipe[2]);
            } elseif (count($partesPipe) === 2) {
                $nome = $partesPipe[0];
                $preco = (float) str_replace(',', '.', $partesPipe[1]);
                $chave = \Illuminate\Support\Str::slug($nome);
            } else {
                $partesHifen = preg_split('/\s*-\s*/', $linha);
                if (count($partesHifen) < 2) {
                    continue;
                }
                $precoRaw = array_pop($partesHifen);
                $nome = trim(implode(' - ', $partesHifen));
                $preco = (float) str_replace(',', '.', (string) $precoRaw);
                $chave = \Illuminate\Support\Str::slug($nome);
            }

            if (($chave ?? '') === '' || ($nome ?? '') === '' || $preco < 0) {
                continue;
            }

            $mapa[$chave] = max(0, $preco);
        }
        return $mapa;
    }

    private function extrairChaveBebida(array $item): ?string
    {
        $detalhes = mb_strtolower((string)($item['detalhes'] ?? ''));
        $tem2l = str_contains($detalhes, '2l') || str_contains($detalhes, '2 litros');
        $tem1l = str_contains($detalhes, '1l') || str_contains($detalhes, '1 litro');
        $temCoca = str_contains($detalhes, 'coca');
        $temCajuina = str_contains($detalhes, 'caju');

        if ($temCoca && $tem2l) return 'coca-2l';
        if ($temCoca && $tem1l) return 'coca-1l';
        if ($temCajuina && $tem2l) return 'cajuina-2l';
        if ($temCajuina && $tem1l) return 'cajuina-1l';

        return null;
    }
}
