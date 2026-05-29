<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conteúdo do Site - Admin</title>
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
<div class="max-w-7xl mx-auto py-6 md:py-8 px-4 space-y-6">
    <header class="rounded-3xl bg-gradient-to-r from-brandBlack via-zinc-900 to-brandBlack text-white p-5 md:p-6 shadow-soft">
        <div class="flex items-start justify-between gap-4">
            <div>
                <p class="text-xs md:text-sm font-bold tracking-widest uppercase text-brandYellow">Painel Admin</p>
                <h1 class="text-3xl md:text-4xl font-black mt-1">Conteúdo do Site</h1>
            </div>
            @include('admin.partials.hamburger-nav', ['active' => 'site'])
        </div>
    </header>

    @if(session('ok'))
        <div class="bg-green-100 border border-green-300 text-green-900 rounded-2xl px-4 py-3">{{ session('ok') }}</div>
    @endif

    <nav class="bg-white border border-amber-100 rounded-3xl p-3 shadow-soft">
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">
            <button type="button" class="site-tab-btn rounded-2xl px-3 py-2.5 text-sm font-semibold bg-zinc-900 text-white shadow-sm transition" data-tab="funcionamento">Funcionamento</button>
            <button type="button" class="site-tab-btn rounded-2xl px-3 py-2.5 text-sm font-semibold bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 transition" data-tab="institucional">Institucional</button>
            <button type="button" class="site-tab-btn rounded-2xl px-3 py-2.5 text-sm font-semibold bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 transition" data-tab="avisos">Avisos</button>
            <button type="button" class="site-tab-btn rounded-2xl px-3 py-2.5 text-sm font-semibold bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 transition" data-tab="promocoes">Promoções</button>
            <button type="button" class="site-tab-btn col-span-2 sm:col-span-3 lg:col-span-1 rounded-2xl px-3 py-2.5 text-sm font-semibold bg-zinc-50 text-zinc-700 border border-zinc-200 hover:bg-zinc-100 transition" data-tab="rotas">Rotas</button>
        </div>
    </nav>

    <section class="bg-white rounded-3xl border border-amber-100 p-5 shadow-soft" data-site-tab="funcionamento">
        <h2 class="text-xl font-black mb-4">Funcionamento da Loja</h2>
        <form action="{{ route('admin.site.configs.update') }}" method="POST" class="space-y-3">
            @csrf
            @method('PATCH')
            <input type="hidden" name="secao" value="funcionamento">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                @php
                    $diasAbertosSelecionados = collect(explode(',', (string)($lojaDiasAbertos ?? '')))->map(fn($d)=>(int)trim($d))->all();
                @endphp
                @foreach([0=>'Domingo',1=>'Segunda',2=>'Terça',3=>'Quarta',4=>'Quinta',5=>'Sexta',6=>'Sábado'] as $diaValor => $diaLabel)
                    <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-2 py-2">
                        <input type="checkbox" name="loja_dias_abertos[]" value="{{ $diaValor }}" @checked(in_array($diaValor, $diasAbertosSelecionados, true))>
                        <span>{{ $diaLabel }}</span>
                    </label>
                @endforeach
            </div>
            <div class="grid md:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Horário de abertura</label>
                    <input type="time" name="loja_horario_abertura" value="{{ $lojaHorarioAbertura }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Horário de fechamento</label>
                    <input type="time" name="loja_horario_fechamento" value="{{ $lojaHorarioFechamento }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                </div>
            </div>
            <button class="bg-zinc-900 text-white rounded-2xl py-2.5 px-4 font-bold hover:bg-zinc-800">Salvar funcionamento</button>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-amber-100 p-5 shadow-soft hidden" data-site-tab="institucional">
        <h2 class="text-xl font-black mb-4">Institucional</h2>
        <form action="{{ route('admin.site.configs.update') }}" method="POST" enctype="multipart/form-data" class="grid md:grid-cols-2 gap-3">
            @csrf
            @method('PATCH')
            <input type="hidden" name="secao" value="institucional">
            <div>
                <label class="block text-sm font-bold mb-1">Título do site</label>
                <input name="titulo_site" value="{{ $tituloSite }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Subtítulo</label>
                <input name="subtitulo_site" value="{{ $subtituloSite }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Logo (arquivo)</label>
                <input type="file" name="logo_file" accept="image/*" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                @if(!empty($logoUrl))
                    <p class="text-xs text-zinc-500 mt-1">Logo atual:</p>
                    <img src="{{ $logoUrl }}" alt="Logo atual" class="w-20 h-20 rounded-2xl object-cover border border-amber-200 mt-1">
                @endif
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Título Sobre nós</label>
                <input name="sobre_titulo" value="{{ $sobreTitulo }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1">Texto Sobre nós</label>
                <textarea name="sobre_texto" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 min-h-24">{{ $sobreTexto }}</textarea>
            </div>
            <button class="md:col-span-2 bg-zinc-900 text-white rounded-2xl py-2.5 font-bold hover:bg-zinc-800">Salvar institucional</button>
        </form>
    </section>

    <section class="bg-white rounded-3xl border border-amber-100 p-5 shadow-soft hidden" data-site-tab="avisos">
        <h2 class="text-xl font-black mb-4">Avisos</h2>
        <form action="{{ route('admin.site.avisos.store') }}" method="POST" class="grid md:grid-cols-5 gap-2 mb-4">
            @csrf
            <input name="titulo" placeholder="Título" class="border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            <input name="descricao" placeholder="Descrição" class="border border-zinc-200 rounded-2xl px-3 py-2.5">
            <input type="number" name="ordem" placeholder="Ordem" class="border border-zinc-200 rounded-2xl px-3 py-2.5">
            <label class="flex items-center gap-2 border border-zinc-200 rounded-2xl px-3"><input type="checkbox" name="ativo" value="1" checked> Ativo</label>
            <button class="bg-brandYellow rounded-2xl py-2.5 font-bold">Adicionar</button>
        </form>
        @foreach($avisos as $aviso)
            <div class="border border-zinc-200 rounded-2xl p-3 mb-3">
                <form action="{{ route('admin.site.avisos.update', $aviso) }}" method="POST" class="grid md:grid-cols-6 gap-2 mb-2">
                    @csrf @method('PATCH')
                    <input name="titulo" value="{{ $aviso->titulo }}" class="border border-zinc-200 rounded-xl px-3 py-2" required>
                    <input name="descricao" value="{{ $aviso->descricao }}" class="border border-zinc-200 rounded-xl px-3 py-2 md:col-span-2">
                    <input type="number" name="ordem" value="{{ $aviso->ordem }}" class="border border-zinc-200 rounded-xl px-3 py-2">
                    <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-3"><input type="checkbox" name="ativo" value="1" @checked($aviso->ativo)> Ativo</label>
                    <button class="bg-zinc-900 text-white rounded-xl py-2">Salvar</button>
                </form>
                <form action="{{ route('admin.site.avisos.destroy', $aviso) }}" method="POST" onsubmit="return confirm('Excluir aviso?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 text-sm font-semibold">Excluir aviso</button>
                </form>
            </div>
        @endforeach
    </section>

    <section class="bg-white rounded-3xl border border-amber-100 p-5 shadow-soft hidden" data-site-tab="promocoes">
        <h2 class="text-xl font-black mb-4">Promoções</h2>
        <form action="{{ route('admin.site.promocoes.store') }}" method="POST" class="grid md:grid-cols-6 gap-2 mb-4">
            @csrf
            <input name="titulo" placeholder="Título" class="border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            <input name="descricao" placeholder="Descrição" class="border border-zinc-200 rounded-2xl px-3 py-2.5 md:col-span-2">
            <input name="preco_destaque" placeholder="Preço destaque" class="border border-zinc-200 rounded-2xl px-3 py-2.5">
            <input type="number" name="ordem" placeholder="Ordem" class="border border-zinc-200 rounded-2xl px-3 py-2.5">
            <label class="flex items-center gap-2 border border-zinc-200 rounded-2xl px-3"><input type="checkbox" name="ativo" value="1" checked> Ativo</label>
            <button class="bg-brandYellow rounded-2xl py-2.5 font-bold">Adicionar</button>
        </form>
        @foreach($promocoes as $promocao)
            <div class="border border-zinc-200 rounded-2xl p-3 mb-3">
                <form action="{{ route('admin.site.promocoes.update', $promocao) }}" method="POST" class="grid md:grid-cols-7 gap-2 mb-2">
                    @csrf @method('PATCH')
                    <input name="titulo" value="{{ $promocao->titulo }}" class="border border-zinc-200 rounded-xl px-3 py-2" required>
                    <input name="descricao" value="{{ $promocao->descricao }}" class="border border-zinc-200 rounded-xl px-3 py-2 md:col-span-2">
                    <input name="preco_destaque" value="{{ $promocao->preco_destaque }}" class="border border-zinc-200 rounded-xl px-3 py-2">
                    <input type="number" name="ordem" value="{{ $promocao->ordem }}" class="border border-zinc-200 rounded-xl px-3 py-2">
                    <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-3"><input type="checkbox" name="ativo" value="1" @checked($promocao->ativo)> Ativo</label>
                    <button class="bg-zinc-900 text-white rounded-xl py-2">Salvar</button>
                </form>
                <form action="{{ route('admin.site.promocoes.destroy', $promocao) }}" method="POST" onsubmit="return confirm('Excluir promoção?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 text-sm font-semibold">Excluir promoção</button>
                </form>
            </div>
        @endforeach
    </section>

    <section class="bg-white rounded-3xl border border-amber-100 p-5 shadow-soft hidden" data-site-tab="rotas">
        <h2 class="text-xl font-black mb-4">Rotas de Entrega</h2>
        <form action="{{ route('admin.site.rotas.store') }}" method="POST" class="grid md:grid-cols-6 gap-2 mb-4">
            @csrf
            <input name="nome" placeholder="Nome da rota" class="border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            <input name="endereco" placeholder="Endereço exibido" class="border border-zinc-200 rounded-2xl px-3 py-2.5">
            <input type="number" name="taxa" step="0.01" min="0" placeholder="Taxa" class="border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            <input type="number" name="ordem" placeholder="Ordem" class="border border-zinc-200 rounded-2xl px-3 py-2.5">
            <label class="flex items-center gap-2 border border-zinc-200 rounded-2xl px-3"><input type="checkbox" name="ativo" value="1" checked> Ativo</label>
            <button class="bg-brandYellow rounded-2xl py-2.5 font-bold">Adicionar</button>
        </form>

        @foreach($rotasEntrega as $rota)
            <div class="border border-zinc-200 rounded-2xl p-3 mb-3">
                <form action="{{ route('admin.site.rotas.update', $rota) }}" method="POST" class="grid md:grid-cols-7 gap-2 mb-2">
                    @csrf @method('PATCH')
                    <input name="nome" value="{{ $rota->nome }}" class="border border-zinc-200 rounded-xl px-3 py-2" required>
                    <input name="endereco" value="{{ $rota->endereco }}" class="border border-zinc-200 rounded-xl px-3 py-2 md:col-span-2">
                    <input type="number" name="taxa" step="0.01" min="0" value="{{ $rota->taxa }}" class="border border-zinc-200 rounded-xl px-3 py-2" required>
                    <input type="number" name="ordem" value="{{ $rota->ordem }}" class="border border-zinc-200 rounded-xl px-3 py-2">
                    <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-3"><input type="checkbox" name="ativo" value="1" @checked($rota->ativo)> Ativo</label>
                    <button class="bg-zinc-900 text-white rounded-xl py-2">Salvar</button>
                </form>
                <form action="{{ route('admin.site.rotas.destroy', $rota) }}" method="POST" onsubmit="return confirm('Excluir rota?')">
                    @csrf @method('DELETE')
                    <button class="text-red-600 text-sm font-semibold">Excluir rota</button>
                </form>
            </div>
        @endforeach
    </section>
</div>
<script>
    const siteTabButtons = document.querySelectorAll('.site-tab-btn');
    const siteTabSections = document.querySelectorAll('[data-site-tab]');

    function ativarAbaSite(tab) {
        siteTabSections.forEach((section) => {
            section.classList.toggle('hidden', section.dataset.siteTab !== tab);
        });

        siteTabButtons.forEach((button) => {
            const ativa = button.dataset.tab === tab;
            button.classList.toggle('bg-zinc-900', ativa);
            button.classList.toggle('text-white', ativa);
            button.classList.toggle('bg-zinc-50', !ativa);
            button.classList.toggle('text-zinc-700', !ativa);
            button.classList.toggle('border', !ativa);
            button.classList.toggle('border-zinc-200', !ativa);
        });
    }

    siteTabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab || 'funcionamento';
            ativarAbaSite(tab);
            const url = new URL(window.location.href);
            url.searchParams.set('aba_site', tab);
            window.history.replaceState({}, '', url);
        });
    });

    const abaSiteInicial = new URLSearchParams(window.location.search).get('aba_site') || 'funcionamento';
    ativarAbaSite(abaSiteInicial);
</script>
</body>
</html>
