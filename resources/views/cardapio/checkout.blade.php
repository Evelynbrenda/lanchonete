<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body class="bg-orange-50 text-zinc-900">

<div class="max-w-5xl mx-auto py-8 px-5">

    <header class="flex flex-wrap items-center justify-between gap-3 mb-6">
        <h1 class="text-4xl font-black">Finalizar Pedido</h1>
        <div class="flex gap-2">
            <a href="{{ url('/') }}" class="px-4 py-2 rounded-xl bg-white border font-bold">Início</a>
            <a href="{{ url('/cardapio') }}" class="px-4 py-2 rounded-xl bg-zinc-900 text-white font-bold">Voltar ao Cardápio</a>
        </div>
    </header>

    <div class="bg-white border rounded-2xl p-4 mb-6 flex flex-wrap items-center gap-2 text-sm">
        <span class="px-3 py-1 rounded-full bg-zinc-900 text-white font-bold">1. Cardápio</span>
        <span class="text-zinc-400">→</span>
        <span class="px-3 py-1 rounded-full bg-orange-500 text-white font-bold">2. Checkout</span>
        <span class="text-zinc-400">→</span>
        <span class="px-3 py-1 rounded-full bg-white border font-bold">3. Envio WhatsApp</span>
    </div>

    <div class="grid lg:grid-cols-2 gap-6 lg:gap-10">

        <section class="bg-white rounded-3xl p-6 shadow-sm border border-orange-100">

            <h2 class="text-2xl font-black mb-5">
                Seus Dados
            </h2>

            <div class="space-y-4">

                <div>
                    <label class="font-bold block mb-2">
                        Nome
                    </label>

                    <input
                        id="nome"
                        autocomplete="off"
                        class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        placeholder="Seu nome"
                    >
                </div>

                <div>
                    <label class="font-bold block mb-2">
                        Telefone
                    </label>

                    <input
                        id="telefone"
                        autocomplete="off"
                        class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        placeholder="(99) 99999-9999"
                    >
                </div>

                <div>
                    <label class="font-bold block mb-2">
                        Tipo de Atendimento
                    </label>

                    <div class="relative">
                        <select
                            id="tipo-atendimento"
                            class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 pr-10 appearance-none outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        >
                            <option value="">Selecione o tipo</option>
                            <option value="entrega">Entrega</option>
                            <option value="retirada">Retirada no local</option>
                        </select>
                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500">▾</span>
                    </div>
                </div>

                <div id="campo-entrega">
                    <label class="font-bold block mb-2">
                        Rota de entrega
                    </label>

                    <div class="relative">
                        <select
                            id="rota-entrega"
                            class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 pr-10 appearance-none outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        ></select>
                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500">▾</span>
                    </div>

                    <label class="font-bold block mt-3 mb-2">
                        Endereço de entrega
                    </label>

                    <input
                        id="endereco"
                        autocomplete="off"
                        class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        placeholder="Rua, número e complemento"
                    >
                </div>

                <div id="campo-mesa" class="hidden">
                    <label class="font-bold block mb-2">
                        Referência de retirada (opcional)
                    </label>

                    <input
                        id="mesa-numero"
                        autocomplete="off"
                        class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        placeholder="Ex: balcão, nome ou horário"
                    >
                </div>

                <div>
                    <label class="font-bold block mb-2">
                        Forma de pagamento
                    </label>

                    <div class="relative">
                        <select
                            id="pagamento"
                            class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 pr-10 appearance-none outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        >
                            <option value="">Selecione o pagamento</option>
                            <option>Pix</option>
                            <option>Dinheiro</option>
                            <option>Cartão</option>
                        </select>
                        <span class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-zinc-500">▾</span>
                    </div>
                </div>

                <div>
                    <label class="font-bold block mb-2">
                        Observação (informe se precisará de troco caso o pagamento seja em dinheiro)
                    </label>

                    <textarea
                        id="observacao"
                        autocomplete="off"
                        class="w-full bg-white border-2 border-zinc-200 rounded-2xl px-4 py-3 min-h-28 outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100"
                        placeholder="Ex: troco para R$ 100, sem cebola, ponto de referência..."
                    ></textarea>
                </div>

            </div>

        </section>

        <aside class="bg-white rounded-3xl p-6 shadow-sm border border-orange-100">

            <h2 class="text-2xl font-black mb-5">
                Resumo do Pedido
            </h2>

            <div
                id="resumo-pedido"
                class="space-y-4"
            ></div>

            <div class="border-t mt-5 pt-5 space-y-2">
                <div class="flex justify-between text-sm">
                    <span>Subtotal</span>
                    <strong id="subtotal-checkout">R$ 0,00</strong>
                </div>

                <div class="flex justify-between text-sm">
                    <span>Taxa de entrega</span>
                    <strong id="taxa-entrega-checkout">R$ 0,00</strong>
                </div>

                <div class="flex justify-between">

                    <strong>
                        Total
                    </strong>

                    <strong
                        id="total-checkout"
                        class="text-orange-600 text-xl"
                    >
                        R$ 0,00
                    </strong>
                </div>

            </div>

            <button
                onclick="enviarWhatsApp()"
                class="w-full bg-green-600 text-white rounded-2xl py-4 font-black mt-8"
            >
                Enviar Pedido no WhatsApp
            </button>

            <a href="{{ url('/cardapio') }}" class="block w-full text-center bg-zinc-900 text-white rounded-2xl py-3 font-black mt-3">
                Continuar Comprando
            </a>

        </aside>

    </div>

</div>

@php
    $rotasEntregaJson = [];
    foreach (($rotasEntrega ?? []) as $rota) {
        $rotasEntregaJson[$rota->slug] = [
            'nome' => $rota->nome,
            'endereco' => $rota->endereco ?: $rota->nome,
            'taxa' => (float) $rota->taxa,
        ];
    }
@endphp
<script>
    window.ROTAS_ENTREGA = @json($rotasEntregaJson);
</script>
<script src="{{ asset('js/checkout.js') }}"></script>

</body>
</html>
