<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $tituloSite }}</title>
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
                        soft: '0 10px 30px rgba(9, 9, 9, 0.10)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brandBeige text-zinc-900">
<header class="relative overflow-hidden bg-brandBlack text-white">
    <div class="absolute inset-0 bg-gradient-to-br from-brandBlack via-zinc-950 to-brandBlack"></div>
    <div class="absolute -top-14 -right-10 h-44 w-44 rounded-full bg-brandYellow/20 blur-3xl"></div>
    <div class="absolute -bottom-16 -left-10 h-52 w-52 rounded-full bg-brandOrange/20 blur-3xl"></div>

    <div class="relative max-w-6xl mx-auto px-4 pt-7 pb-8 sm:pt-10 sm:pb-10">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs uppercase tracking-[0.18em] text-brandYellow font-bold">Lanchonete do Márcio</p>
                <h1 class="mt-2 text-3xl sm:text-4xl font-black leading-tight">{{ $tituloSite }}</h1>
                <p class="mt-2 text-sm sm:text-base text-zinc-200 max-w-sm">O sabor da sua fome em poucos cliques.</p>
            </div>
            @if(!empty($logoUrl))
                <img src="{{ $logoUrl }}" alt="Logo" class="w-24 h-24 sm:w-28 sm:h-28 rounded-3xl object-cover shrink-0 shadow-soft">
            @endif
        </div>

        <div class="mt-6">
            <a href="{{ url('/cardapio') }}" class="inline-flex items-center justify-center w-full sm:w-auto px-7 py-4 rounded-2xl bg-gradient-to-r from-brandYellow to-brandOrange text-brandBlack font-black text-lg shadow-soft">
                Fazer pedido
            </a>
        </div>
    </div>
</header>

<main class="max-w-6xl mx-auto px-4 py-6 space-y-7">
    <section id="avisos" class="space-y-3">
        <div class="flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black">Avisos</h2>
            <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Importante</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @forelse($avisos as $aviso)
                <article class="bg-white rounded-3xl border border-amber-100 p-4 shadow-soft">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brandYellow/20 text-brandOrange flex items-center justify-center shrink-0">🔔</div>
                        <div>
                            <h3 class="font-black text-base leading-tight">{{ $aviso->titulo }}</h3>
                            <p class="text-zinc-600 mt-1 text-sm">{{ $aviso->descricao }}</p>
                        </div>
                    </div>
                </article>
            @empty
                <article class="bg-white rounded-3xl border border-amber-100 p-4 shadow-soft">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brandYellow/20 text-brandOrange flex items-center justify-center shrink-0">🕒</div>
                        <p class="text-sm font-semibold">Funcionamento: 18h às 23h</p>
                    </div>
                </article>
                <article class="bg-white rounded-3xl border border-amber-100 p-4 shadow-soft">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brandYellow/20 text-brandOrange flex items-center justify-center shrink-0">📍</div>
                        <p class="text-sm font-semibold">Taxa de entrega calculada pelo bairro</p>
                    </div>
                </article>
                <article class="bg-white rounded-3xl border border-amber-100 p-4 shadow-soft sm:col-span-2 lg:col-span-1">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-brandYellow/20 text-brandOrange flex items-center justify-center shrink-0">💬</div>
                        <p class="text-sm font-semibold">Pedido enviado direto pelo WhatsApp</p>
                    </div>
                </article>
            @endforelse
        </div>
    </section>

    @if($promocoes->isNotEmpty())
        <section id="promocoes" class="space-y-3">
            <h2 class="text-xl sm:text-2xl font-black">Promoções</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($promocoes as $promocao)
                    <article class="relative overflow-hidden rounded-3xl p-5 sm:p-6 bg-zinc-900 text-white shadow-soft">
                        <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-brandYellow/20 blur-2xl"></div>
                        <div class="absolute -left-12 -bottom-12 w-36 h-36 rounded-full bg-brandOrange/20 blur-2xl"></div>
                        <div class="relative">
                            <p class="inline-flex px-3 py-1 rounded-full bg-brandYellow text-brandBlack text-xs font-black uppercase tracking-wide">Oferta especial</p>
                            <h3 class="text-xl font-black mt-3">{{ $promocao->titulo }}</h3>
                            <p class="text-zinc-200 mt-1 text-sm">{{ $promocao->descricao }}</p>
                            @if($promocao->preco_destaque)
                                <p class="text-4xl font-black text-brandYellow mt-4 leading-none">{{ $promocao->preco_destaque }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <section id="sobre" class="bg-white rounded-3xl border border-amber-100 p-5 sm:p-6 shadow-soft space-y-4">
        <div>
            <h2 class="text-2xl font-black">Mais Que Uma Lanchonete</h2>
            <p class="text-zinc-600 mt-1 text-sm">{{ $sobreTexto }}</p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
            <article class="rounded-2xl bg-brandBeige p-3 border border-amber-100">
                <p class="text-sm font-bold">Atendimento rápido</p>
            </article>
            <article class="rounded-2xl bg-brandBeige p-3 border border-amber-100">
                <p class="text-sm font-bold">Pedido fácil</p>
            </article>
            <article class="rounded-2xl bg-brandBeige p-3 border border-amber-100">
                <p class="text-sm font-bold">Qualidade e sabor</p>
            </article>
            <article class="rounded-2xl bg-brandBeige p-3 border border-amber-100">
                <p class="text-sm font-bold">Entrega organizada</p>
            </article>
        </div>
    </section>
</main>

</body>
</html>
