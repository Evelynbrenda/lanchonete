<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brandBlack: '#090909',
                        brandYellow: '#ffb000',
                        brandOrange: '#ff7a00',
                        brandBeige: '#fff8e7',
                    },
                    boxShadow: {
                        soft: '0 14px 30px rgba(9,9,9,0.08)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brandBeige text-zinc-900">
<div class="max-w-7xl mx-auto py-6 md:py-8 px-4">
    @php
        $labelsStatus = [
            'novo' => 'Novo',
            'preparando' => 'Preparando',
            'pronto' => 'Pronto',
            'saiu_para_entrega' => 'Saiu para entrega',
            'entregue' => 'Entregue',
            'cancelado' => 'Cancelado',
            'todos' => 'Todos',
        ];
    @endphp

    <header class="mb-6 rounded-3xl bg-gradient-to-r from-brandBlack via-zinc-900 to-brandBlack text-white p-5 md:p-6 shadow-soft">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs md:text-sm font-bold tracking-widest uppercase text-brandYellow">Painel Admin</p>
                <h1 class="text-3xl md:text-4xl font-black mt-1">Pedidos</h1>
            </div>
            @include('admin.partials.hamburger-nav', ['active' => 'pedidos'])
        </div>
    </header>

    <section class="grid sm:grid-cols-2 xl:grid-cols-6 gap-3 mb-4">
        @php
            $cards = [
                'novo' => ['Novo', 'bg-blue-50 border-blue-200 text-blue-800'],
                'preparando' => ['Preparando', 'bg-yellow-50 border-yellow-200 text-yellow-800'],
                'pronto' => ['Pronto', 'bg-purple-50 border-purple-200 text-purple-800'],
                'saiu_para_entrega' => ['Saiu para entrega', 'bg-orange-50 border-orange-200 text-orange-800'],
                'entregue' => ['Entregue', 'bg-green-50 border-green-200 text-green-800'],
                'cancelado' => ['Cancelado', 'bg-red-50 border-red-200 text-red-800'],
            ];
        @endphp
        @foreach($cards as $status => [$label, $class])
            <a href="{{ route('admin.pedidos.index', array_merge(request()->except('page'), ['status' => $status])) }}" class="border rounded-2xl p-3.5 {{ $class }} {{ $statusFiltro === $status ? 'ring-2 ring-zinc-900' : '' }} shadow-soft">
                <p class="text-xs font-bold uppercase tracking-wide">{{ $label }}</p>
                <p class="text-2xl font-black">{{ $contagensStatus[$status] ?? 0 }}</p>
            </a>
        @endforeach
    </section>

    <section class="bg-white border border-amber-100 rounded-3xl p-4 mb-6 shadow-soft">
        <form method="GET" class="grid lg:grid-cols-7 gap-3 items-end">
            <div class="lg:col-span-2">
                <label class="block text-sm font-bold mb-1">Buscar cliente ou telefone</label>
                <input type="text" name="busca" value="{{ request('busca') }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5" placeholder="Nome ou telefone">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Status</label>
                <select name="status" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                    @foreach ($statusDisponiveis as $status)
                        <option value="{{ $status }}" @selected($statusFiltro === $status)>{{ $labelsStatus[$status] ?? ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Atendimento</label>
                <select name="tipo_atendimento" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                    @foreach ($tiposAtendimentoDisponiveis as $tipo)
                        <option value="{{ $tipo }}" @selected($tipoAtendimentoFiltro === $tipo)>
                            {{ $tipo === 'todos' ? 'Todos' : ($tipo === 'entrega' ? 'Entrega' : 'Retirada no local') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Data início</label>
                <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Data fim</label>
                <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
            </div>

            <button class="bg-zinc-900 text-white rounded-2xl py-2.5 px-4 font-black hover:bg-zinc-800">Filtrar</button>
        </form>
    </section>

    <div class="grid gap-4">
        @forelse ($pedidos as $pedido)
            @php
                $statusClass = match($pedido->status) {
                    'novo' => 'border-blue-200 bg-blue-50/50',
                    'preparando' => 'border-yellow-200 bg-yellow-50/50',
                    'pronto' => 'border-purple-200 bg-purple-50/50',
                    'saiu_para_entrega' => 'border-orange-200 bg-orange-50/50',
                    'entregue' => 'border-green-200 bg-green-50/50',
                    'cancelado' => 'border-red-200 bg-red-50/50',
                    default => 'border-zinc-200 bg-white',
                };
            @endphp
            <article class="rounded-3xl p-4 md:p-5 border {{ $statusClass }} shadow-soft">
                <div class="flex flex-wrap justify-between gap-3">
                    <div>
                        <h2 class="text-xl md:text-2xl font-black">Pedido #{{ $pedido->id }}</h2>
                        <p class="text-zinc-500 text-sm">{{ $pedido->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    <strong class="text-2xl text-brandOrange">R$ {{ number_format($pedido->total, 2, ',', '.') }}</strong>
                </div>

                @php
                    $statusBadgeClass = match($pedido->status) {
                        'novo' => 'bg-blue-100 text-blue-900 border-blue-300',
                        'preparando' => 'bg-yellow-100 text-yellow-900 border-yellow-300',
                        'pronto' => 'bg-purple-100 text-purple-900 border-purple-300',
                        'saiu_para_entrega' => 'bg-orange-100 text-orange-900 border-orange-300',
                        'entregue' => 'bg-green-100 text-green-900 border-green-300',
                        'cancelado' => 'bg-red-100 text-red-900 border-red-300',
                        default => 'bg-zinc-100 text-zinc-900 border-zinc-300',
                    };
                @endphp

                <div class="mt-3 flex flex-wrap gap-2">
                    <span class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-wide {{ $pedido->tipo_atendimento === 'entrega' ? 'bg-orange-100 text-orange-900 border-orange-300' : 'bg-zinc-100 text-zinc-900 border-zinc-300' }}">
                        {{ $pedido->tipo_atendimento === 'entrega' ? 'Entrega' : 'Retirada' }}
                    </span>
                    <span class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-wide {{ $statusBadgeClass }}">
                        {{ $labelsStatus[$pedido->status] ?? ucfirst($pedido->status) }}
                    </span>
                    <span class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-wide bg-emerald-100 text-emerald-900 border-emerald-300">
                        {{ $pedido->forma_pagamento ?? 'Pagamento não informado' }}
                    </span>
                    @if($pedido->tipo_atendimento === 'entrega')
                        <span class="px-3 py-1.5 rounded-xl border text-xs font-black uppercase tracking-wide bg-amber-100 text-amber-900 border-amber-300">
                            {{ str_contains(mb_strtolower((string) $pedido->observacoes), 'rota:') ? 'Com rota definida' : 'Entrega' }}
                        </span>
                    @endif
                </div>

                <div class="mt-3 p-3 rounded-2xl bg-zinc-900 text-white">
                    <p class="text-[11px] uppercase tracking-widest text-zinc-300 font-black mb-2">Itens deste pedido</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($pedido->itens as $item)
                            <span class="px-2.5 py-1 rounded-lg bg-white/10 border border-white/20 text-xs font-bold">
                                {{ $item->quantidade }}x {{ $item->nome_item ?? $item->produto->nome ?? 'Produto' }}
                            </span>
                        @endforeach
                    </div>
                </div>

                <div class="grid xl:grid-cols-3 gap-4 mt-4">
                    <section class="space-y-1.5 text-sm bg-white/80 border border-white rounded-2xl p-3">
                        <p><strong>Cliente:</strong> {{ $pedido->nome_cliente }}</p>
                        <p><strong>Telefone:</strong> {{ $pedido->cliente_telefone }}</p>
                        <p><strong>Atendimento:</strong> {{ $pedido->tipo_atendimento === 'entrega' ? 'Entrega' : 'Retirada no local' }}</p>
                        @if($pedido->tipo_atendimento === 'entrega')
                            <p><strong>Endereço:</strong> {{ $pedido->cliente_endereco }}</p>
                        @endif
                        <p><strong>Pagamento:</strong> {{ $pedido->forma_pagamento ?? 'Não informado' }}</p>
                        @if($pedido->observacoes)
                            <div class="rounded-xl bg-amber-100 border border-amber-200 p-2 mt-2">
                                <p><strong>Observação:</strong> {{ $pedido->observacoes }}</p>
                            </div>
                        @endif
                        @php
                            $subtotalItens = $pedido->itens->sum('subtotal');
                            $taxaEntrega = max(0, (float)$pedido->total - (float)$subtotalItens);
                        @endphp
                        <div class="pt-2 mt-2 border-t border-zinc-200 space-y-1">
                            <p><strong>Subtotal itens:</strong> R$ {{ number_format($subtotalItens, 2, ',', '.') }}</p>
                            <p><strong>Taxa de entrega:</strong> R$ {{ number_format($taxaEntrega, 2, ',', '.') }}</p>
                            <p><strong>Total pedido:</strong> R$ {{ number_format($pedido->total, 2, ',', '.') }}</p>
                        </div>
                    </section>

                    <section class="space-y-2 max-h-72 overflow-auto pr-1">
                        @foreach ($pedido->itens as $item)
                            <div class="border border-zinc-200 rounded-2xl p-3 flex justify-between gap-2 bg-white">
                                <div>
                                    <p class="font-black">{{ $item->nome_item ?? $item->produto->nome ?? 'Produto removido' }}</p>
                                    @php
                                        $tamanhoExibicao = $item->tamanho_item;
                                        if (empty($tamanhoExibicao) && !empty($item->detalhes_item) && preg_match('/tamanho[:\s]+([PMG])/i', $item->detalhes_item, $matchTamanho)) {
                                            $tamanhoExibicao = strtoupper($matchTamanho[1]);
                                        }
                                    @endphp
                                    @if(!empty($tamanhoExibicao))
                                        <p class="text-xs text-zinc-500">Tamanho: {{ $tamanhoExibicao }}</p>
                                    @endif
                                    @if(!empty($item->detalhes_item))
                                        <p class="text-xs text-zinc-500">{{ $item->detalhes_item }}</p>
                                    @endif
                                    <p class="text-xs text-zinc-500">Qtd: {{ $item->quantidade }} · Unit: R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}</p>
                                </div>
                                <p class="font-bold text-brandOrange shrink-0">R$ {{ number_format($item->subtotal, 2, ',', '.') }}</p>
                            </div>
                        @endforeach
                    </section>

                    <section class="bg-white/80 border border-white rounded-2xl p-3">
                        <form action="{{ route('admin.pedidos.status', $pedido) }}" method="POST" class="space-y-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="filtro_status" value="{{ $statusFiltro }}">
                            <input type="hidden" name="filtro_busca" value="{{ request('busca') }}">
                            <input type="hidden" name="filtro_data_inicio" value="{{ request('data_inicio') }}">
                            <input type="hidden" name="filtro_data_fim" value="{{ request('data_fim') }}">
                            <input type="hidden" name="filtro_tipo_atendimento" value="{{ request('tipo_atendimento', 'todos') }}">
                            <input type="hidden" name="filtro_page" value="{{ $pedidos->currentPage() }}">
                            <label class="block text-sm font-bold">Status</label>
                            <select name="status" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                                @php
                                    $statusOpcoes = $pedido->tipo_atendimento === 'retirada'
                                        ? ['novo', 'preparando', 'pronto', 'entregue', 'cancelado']
                                        : ['novo', 'preparando', 'pronto', 'saiu_para_entrega', 'entregue', 'cancelado'];
                                @endphp
                                @foreach ($statusOpcoes as $status)
                                    <option value="{{ $status }}" @selected($pedido->status === $status)>{{ $labelsStatus[$status] ?? ucfirst($status) }}</option>
                                @endforeach
                            </select>
                            <button class="w-full bg-zinc-900 text-white rounded-2xl py-2.5 font-bold hover:bg-zinc-800">Atualizar status</button>
                        </form>
                    </section>
                </div>
            </article>
        @empty
            <div class="bg-white rounded-3xl border border-amber-100 p-8 text-center text-zinc-500 shadow-soft">Nenhum pedido encontrado.</div>
        @endforelse
    </div>

    <div class="mt-6">{{ $pedidos->links() }}</div>
</div>
</body>
</html>
