<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Admin</title>
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
    <header class="mb-6 rounded-3xl bg-gradient-to-r from-brandBlack via-zinc-900 to-brandBlack text-white p-5 md:p-6 shadow-soft">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs md:text-sm font-bold tracking-widest uppercase text-brandYellow">Painel Admin</p>
                <h1 class="text-3xl md:text-4xl font-black mt-1">Dashboard de Vendas</h1>
                <p class="text-zinc-300 mt-2 text-sm md:text-base">Período: {{ $inicioMes->format('d/m/Y') }} até {{ $fimMes->format('d/m/Y') }}</p>
            </div>
            @include('admin.partials.hamburger-nav', ['active' => 'dashboard'])
        </div>
    </header>

    <section class="bg-white border border-amber-100 rounded-3xl p-4 mb-6 shadow-soft">
        <form method="GET" class="grid sm:grid-cols-3 gap-3 items-end">
            <div>
                <label class="block text-sm font-bold mb-1">Data início</label>
                <input
                    type="date"
                    name="data_inicio"
                    value="{{ request('data_inicio', $inicioMes->format('Y-m-d')) }}"
                    class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white"
                >
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Data fim</label>
                <input
                    type="date"
                    name="data_fim"
                    value="{{ request('data_fim', $fimMes->format('Y-m-d')) }}"
                    class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white"
                >
            </div>
            <button class="bg-zinc-900 text-white rounded-2xl py-2.5 px-4 font-black hover:bg-zinc-800">Filtrar período</button>
        </form>
    </section>

    <section class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <article class="bg-white border border-amber-100 rounded-3xl p-4 shadow-soft">
            <p class="text-sm text-zinc-500">Pedidos no mês</p>
            <p class="text-3xl font-black mt-1">{{ $totalPedidosMes }}</p>
        </article>
        <article class="bg-green-50 border border-green-200 rounded-3xl p-4 shadow-soft">
            <p class="text-sm text-green-800">Entregues</p>
            <p class="text-3xl font-black text-green-800 mt-1">{{ $pedidosEntreguesMes }}</p>
        </article>
        <article class="bg-red-50 border border-red-200 rounded-3xl p-4 shadow-soft">
            <p class="text-sm text-red-800">Cancelados</p>
            <p class="text-3xl font-black text-red-800 mt-1">{{ $pedidosCanceladosMes }}</p>
        </article>
        <article class="bg-gradient-to-r from-brandYellow to-brandOrange border border-amber-300 rounded-3xl p-4 shadow-soft text-brandBlack">
            <p class="text-sm font-bold">Faturamento</p>
            <p class="text-3xl font-black mt-1">R$ {{ number_format($faturamentoMes, 2, ',', '.') }}</p>
        </article>
    </section>

    <section class="grid xl:grid-cols-3 gap-4 mb-6">
        <article class="bg-white border border-amber-100 rounded-3xl p-4 shadow-soft">
            <h2 class="font-black mb-3 text-lg">Resumo por status</h2>
            <div class="space-y-2 text-sm">
                @php
                    $labelsStatus = [
                        'novo' => 'Novo',
                        'preparando' => 'Preparando',
                        'pronto' => 'Pronto',
                        'saiu_para_entrega' => 'Saiu para entrega',
                        'entregue' => 'Entregue',
                        'cancelado' => 'Cancelado',
                    ];
                @endphp
                @foreach (['novo', 'preparando', 'pronto', 'saiu_para_entrega', 'entregue', 'cancelado'] as $status)
                    <div class="flex justify-between items-center border border-zinc-200 rounded-2xl px-3 py-2.5">
                        <span>{{ $labelsStatus[$status] }}</span>
                        <strong class="text-base">{{ $statusResumo[$status] ?? 0 }}</strong>
                    </div>
                @endforeach
            </div>
        </article>

        <article class="bg-white border border-amber-100 rounded-3xl p-4 shadow-soft xl:col-span-2">
            <h2 class="font-black mb-3 text-lg">Produtos Mais Pedidos No Mês</h2>
            <div class="space-y-2">
                @forelse($maisPedidosNoMes as $item)
                    <div class="flex justify-between items-center border border-zinc-200 rounded-2xl px-3 py-2.5">
                        <span>{{ $item->nome }}</span>
                        <strong>{{ $item->quantidade }} un.</strong>
                    </div>
                @empty
                    <p class="text-zinc-500">Sem dados no período.</p>
                @endforelse
            </div>
        </article>
    </section>

    <section class="bg-white border border-amber-100 rounded-3xl p-4 shadow-soft">
        <h2 class="font-black mb-3 text-lg">Endereços Que Mais Pediram No Mês</h2>
        <div class="space-y-2">
            @forelse($enderecosQueMaisPediram as $endereco)
                <div class="flex justify-between items-center border border-zinc-200 rounded-2xl px-3 py-2.5 gap-3">
                    <span class="truncate">{{ $endereco->cliente_endereco }}</span>
                    <strong class="shrink-0">{{ $endereco->total }} pedidos</strong>
                </div>
            @empty
                <p class="text-zinc-500">Sem dados de entrega no período.</p>
            @endforelse
        </div>
    </section>
</div>
</body>
</html>
