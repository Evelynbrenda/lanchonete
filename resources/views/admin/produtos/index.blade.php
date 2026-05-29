<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produtos - Admin</title>
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
                <h1 class="text-3xl md:text-4xl font-black mt-1">Produtos</h1>
            </div>
            @include('admin.partials.hamburger-nav', ['active' => 'produtos'])
        </div>
    </header>

    @if (session('ok'))
        <div class="mb-4 bg-green-100 border border-green-300 text-green-900 rounded-2xl px-4 py-3">{{ session('ok') }}</div>
    @endif

    <nav class="bg-white border border-amber-100 rounded-2xl p-2 mb-4 shadow-soft">
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
            <button type="button" class="admin-tab-btn rounded-xl px-3 py-2.5 text-sm font-bold bg-zinc-900 text-white" data-tab="categorias">Categorias</button>
            <button type="button" class="admin-tab-btn rounded-xl px-3 py-2.5 text-sm font-bold bg-white text-zinc-700 border border-zinc-200" data-tab="novo">Novo produto</button>
            <button type="button" class="admin-tab-btn col-span-2 sm:col-span-1 rounded-xl px-3 py-2.5 text-sm font-bold bg-white text-zinc-700 border border-zinc-200" data-tab="cadastrados">Produtos cadastrados</button>
        </div>
    </nav>

    <section class="bg-white border border-amber-100 rounded-3xl p-5 mb-6 shadow-soft" data-admin-tab="categorias">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-3 mb-4">
            <h2 class="text-xl font-black">Categorias</h2>
            <p class="text-sm text-zinc-500 leading-tight">Tudo de categoria em um só lugar</p>
        </div>

        <details class="group border border-zinc-200 rounded-2xl bg-zinc-50/70 mb-4" open>
            <summary class="cursor-pointer list-none px-4 py-3 font-bold flex items-center justify-between">
                Cadastrar nova categoria
                <span class="text-zinc-500 text-sm group-open:rotate-180 transition">⌄</span>
            </summary>
            <div class="p-4 pt-0">
                <form action="{{ route('admin.categorias.store') }}" method="POST" class="grid md:grid-cols-2 xl:grid-cols-4 gap-2">
                    @csrf
                    <input type="text" name="nome" placeholder="Nome da categoria" class="md:col-span-2 border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white" required>
                    <label class="flex items-center gap-2 border border-zinc-200 rounded-2xl px-3 bg-white">
                        <input type="checkbox" name="ativo" value="1" checked>
                        Ativa
                    </label>
                    <button class="bg-zinc-900 text-white rounded-2xl py-2.5 font-bold hover:bg-zinc-800">Cadastrar categoria</button>

                    <label class="md:col-span-2 flex items-center gap-2 border border-zinc-200 rounded-2xl px-3 py-2 bg-white">
                        <input type="checkbox" name="usa_adicionais" value="1">
                        Usa adicionais?
                    </label>
                    <input type="number" step="0.01" min="0" name="valor_adicional" placeholder="Valor do adicional (R$)" class="border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                    <label class="flex items-center gap-2 border border-zinc-200 rounded-2xl px-3 py-2 bg-white">
                        <input type="checkbox" name="usa_tamanhos" value="1">
                        Usa tamanhos?
                    </label>
                    <label class="md:col-span-2 xl:col-span-4 flex items-center gap-2 border border-zinc-200 rounded-2xl px-3 py-2 bg-white">
                        <input type="checkbox" name="usa_opcoes" value="1">
                        Usa opções com preço? (ex.: tipos de açaí)
                    </label>
                    <textarea name="adicionais_texto" class="md:col-span-2 border border-zinc-200 rounded-2xl px-3 py-2.5 min-h-24 bg-white" placeholder="Adicionais (1 por linha)&#10;Queijo&#10;Presunto"></textarea>
                    <textarea name="opcoes_texto" class="md:col-span-2 border border-zinc-200 rounded-2xl px-3 py-2.5 min-h-24 bg-white" placeholder="Opções (1 por linha)&#10;Com fruta|12&#10;Completo|14"></textarea>
                </form>
            </div>
        </details>

        <div class="space-y-3">
            @foreach($categorias as $categoria)
                <details class="group border border-zinc-200 rounded-2xl bg-white" @if($loop->first) open @endif>
                    <summary class="cursor-pointer list-none px-4 py-3 flex items-center justify-between">
                        <div class="min-w-0">
                            <p class="font-bold truncate">{{ $categoria->nome }}</p>
                            <p class="text-xs text-zinc-500">slug: {{ $categoria->slug }} · {{ $categoria->ativo ? 'Ativa' : 'Inativa' }}</p>
                        </div>
                        <span class="text-zinc-500 text-sm group-open:rotate-180 transition">⌄</span>
                    </summary>

                    <div class="p-4 pt-0">
                        <form action="{{ route('admin.categorias.update', $categoria) }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-2 items-center">
                            @csrf
                            @method('PATCH')
                            <input type="text" name="nome" value="{{ $categoria->nome }}" class="md:col-span-2 border border-zinc-200 rounded-xl px-3 py-2 bg-white" required>
                            <input type="text" name="slug" value="{{ $categoria->slug }}" class="md:col-span-2 border border-zinc-200 rounded-xl px-3 py-2 bg-white" required>
                            <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-3 py-2 bg-white">
                                <input type="checkbox" name="ativo" value="1" @checked($categoria->ativo)>
                                Ativa
                            </label>
                            <label class="md:col-span-2 flex items-center gap-2 border border-zinc-200 rounded-xl px-3 py-2 bg-white">
                                <input type="checkbox" name="usa_adicionais" value="1" @checked($categoria->usa_adicionais)>
                                Usa adicionais
                            </label>
                            <input type="number" step="0.01" min="0" name="valor_adicional" value="{{ $categoria->valor_adicional }}" placeholder="Valor adicional" class="border border-zinc-200 rounded-xl px-3 py-2 bg-white">
                            <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-3 py-2 bg-white">
                                <input type="checkbox" name="usa_tamanhos" value="1" @checked($categoria->usa_tamanhos)>
                                Usa tamanhos
                            </label>
                            <label class="md:col-span-2 flex items-center gap-2 border border-zinc-200 rounded-xl px-3 py-2 bg-white">
                                <input type="checkbox" name="usa_opcoes" value="1" @checked($categoria->usa_opcoes)>
                                Usa opções com preço
                            </label>
                            <textarea name="adicionais_texto" class="md:col-span-2 border border-zinc-200 rounded-xl px-3 py-2 min-h-20 bg-white" placeholder="Adicionais (1 por linha)">{{ $categoria->adicionais_texto }}</textarea>
                            <textarea name="opcoes_texto" class="md:col-span-2 border border-zinc-200 rounded-xl px-3 py-2 min-h-20 bg-white" placeholder="Opções (1 por linha)">{{ $categoria->opcoes_texto }}</textarea>
                            <button class="md:col-span-2 bg-zinc-900 text-white rounded-xl py-2 text-sm font-bold hover:bg-zinc-800">Salvar</button>
                        </form>
                        <form action="{{ route('admin.categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('Excluir categoria?')" class="mt-2">
                            @csrf
                            @method('DELETE')
                            <button class="text-red-600 text-sm font-semibold">Excluir categoria</button>
                        </form>
                    </div>
                </details>
            @endforeach
        </div>
    </section>

    <section class="bg-white border border-amber-100 rounded-3xl p-5 mb-6 shadow-soft hidden" data-admin-tab="novo">
        <h2 class="text-xl font-black mb-4">Novo Produto</h2>

        <form action="{{ route('admin.produtos.store') }}" method="POST" enctype="multipart/form-data" class="grid lg:grid-cols-2 gap-3">
            @csrf

            <div>
                <label class="block text-sm font-bold mb-1">Categoria</label>
                <select id="nova-categoria-id" name="categoria_id" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white" required>
                    <option value="">Selecione</option>
                    @foreach ($categoriasAtivas as $categoria)
                        <option value="{{ $categoria->id }}" data-slug="{{ $categoria->slug }}">{{ $categoria->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Nome</label>
                <input type="text" name="nome" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            </div>

            <div class="lg:col-span-2">
                <label class="block text-sm font-bold mb-1">Descrição</label>
                <textarea name="descricao" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 min-h-20"></textarea>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Preço</label>
                <input type="number" name="preco" step="0.01" min="0" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5" required>
            </div>

            <div>
                <label class="block text-sm font-bold mb-1">Foto do produto</label>
                <input type="file" name="imagem" accept="image/*" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
            </div>

            <div id="novos-campos-pizza" class="lg:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-2 hidden bg-amber-50 border border-amber-200 rounded-2xl p-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Pizza P (opcional)</label>
                    <input type="number" name="preco_p" step="0.01" min="0" class="w-full border border-zinc-200 rounded-xl px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Pizza M (opcional)</label>
                    <input type="number" name="preco_m" step="0.01" min="0" class="w-full border border-zinc-200 rounded-xl px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Pizza G (opcional)</label>
                    <input type="number" name="preco_g" step="0.01" min="0" class="w-full border border-zinc-200 rounded-xl px-3 py-2">
                </div>
            </div>

            <label class="flex items-center gap-2 lg:col-span-2">
                <input type="checkbox" name="ativo" value="1" checked>
                <span class="font-bold text-sm">Produto ativo</span>
            </label>

            <div class="lg:col-span-2 border border-zinc-200 rounded-2xl p-3">
                <p class="text-sm font-bold mb-2">Disponibilidade do produto</p>
                <select name="disponibilidade_modo" id="nova-disponibilidade-modo" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 mb-2 bg-white">
                    <option value="sempre">Sempre disponível</option>
                    <option value="dias">Somente em dias específicos</option>
                </select>
                <div id="nova-disponibilidade-dias" class="hidden grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
                    @foreach([0=>'Domingo',1=>'Segunda',2=>'Terça',3=>'Quarta',4=>'Quinta',5=>'Sexta',6=>'Sábado'] as $diaValor => $diaLabel)
                        <label class="flex items-center gap-2 border border-zinc-200 rounded-xl px-2 py-2">
                            <input type="checkbox" name="dias_disponiveis[]" value="{{ $diaValor }}">
                            <span>{{ $diaLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button class="lg:col-span-2 bg-zinc-900 text-white rounded-2xl py-2.5 font-black hover:bg-zinc-800">Salvar produto</button>
        </form>
    </section>

    <section class="bg-white border border-amber-100 rounded-3xl p-5 mb-6 shadow-soft hidden" data-admin-tab="cadastrados">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-3 mb-4">
            <h2 class="text-xl font-black">Produtos Cadastrados</h2>
            <p class="text-sm text-zinc-500 leading-tight">Use o filtro para localizar mais rápido</p>
        </div>

        <form method="GET" class="grid md:grid-cols-4 gap-3 items-end mb-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-bold mb-1">Buscar produto</label>
                <input type="text" name="busca" value="{{ request('busca') }}" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5" placeholder="Nome ou descrição">
            </div>
            <div>
                <label class="block text-sm font-bold mb-1">Categoria</label>
                <select name="categoria_id" class="w-full border border-zinc-200 rounded-2xl px-3 py-2.5 bg-white">
                    <option value="">Todas</option>
                    @foreach($categoriasAtivas as $categoria)
                        <option value="{{ $categoria->id }}" @selected((string)request('categoria_id') === (string)$categoria->id)>{{ $categoria->nome }}</option>
                    @endforeach
                </select>
            </div>
            <button class="bg-zinc-900 text-white rounded-2xl py-2.5 font-bold hover:bg-zinc-800">Filtrar</button>
        </form>

        <div class="grid xl:grid-cols-2 gap-3">
            @foreach ($produtos as $produto)
                <article class="bg-white border border-amber-100 rounded-3xl p-4 shadow-soft min-w-0">
                <form action="{{ route('admin.produtos.update', $produto) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    @method('PATCH')

                    <div class="flex flex-col sm:flex-row gap-3">
                        <div class="w-full sm:w-24 h-36 sm:h-24 rounded-2xl overflow-hidden border border-zinc-200 bg-zinc-100 flex-shrink-0">
                            @if($produto->imagem_url)
                                <img src="{{ $produto->imagem_url }}" alt="{{ $produto->nome }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full text-xs text-zinc-500 flex items-center justify-center">Sem foto</div>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 gap-2 w-full">
                            <div>
                                <label class="block text-xs font-bold mb-1">Categoria</label>
                                <select name="categoria_id" class="categoria-produto w-full border border-zinc-200 rounded-xl px-2 py-2.5 bg-white" required>
                                    @foreach ($categorias as $categoria)
                                        <option value="{{ $categoria->id }}" data-slug="{{ $categoria->slug }}" @selected($produto->categoria_id === $categoria->id)>{{ $categoria->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold mb-1">Nome</label>
                                <input type="text" name="nome" value="{{ $produto->nome }}" class="w-full border border-zinc-200 rounded-xl px-2 py-2.5" required>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold mb-1">Descrição</label>
                        <input type="text" name="descricao" value="{{ $produto->descricao }}" class="w-full border border-zinc-200 rounded-xl px-2 py-2.5">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs font-bold mb-1">Preço</label>
                            <input type="number" step="0.01" min="0" name="preco" value="{{ $produto->preco }}" class="w-full border border-zinc-200 rounded-xl px-2 py-2.5" required>
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Nova foto</label>
                            <input type="file" name="imagem" accept="image/*" class="w-full border border-zinc-200 rounded-xl px-2 py-2.5 bg-white">
                        </div>
                    </div>

                    <div class="campos-pizza grid grid-cols-1 md:grid-cols-3 gap-2 {{ optional($produto->categoria)->slug === 'pizzas' ? '' : 'hidden' }} bg-amber-50 border border-amber-200 rounded-2xl p-3">
                        <div>
                            <label class="block text-xs font-bold mb-1">Pizza P</label>
                            <input type="number" step="0.01" min="0" name="preco_p" value="{{ $produto->preco_p }}" class="w-full border border-zinc-200 rounded-xl px-2 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Pizza M</label>
                            <input type="number" step="0.01" min="0" name="preco_m" value="{{ $produto->preco_m }}" class="w-full border border-zinc-200 rounded-xl px-2 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold mb-1">Pizza G</label>
                            <input type="number" step="0.01" min="0" name="preco_g" value="{{ $produto->preco_g }}" class="w-full border border-zinc-200 rounded-xl px-2 py-2">
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3 text-sm">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="ativo" value="1" @checked($produto->ativo)>
                            <span class="font-bold">Ativo</span>
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="remover_imagem" value="1">
                            <span>Remover foto atual</span>
                        </label>
                    </div>

                    <div class="border border-zinc-200 rounded-2xl p-3">
                        <p class="text-xs font-bold mb-2">Disponibilidade</p>
                        <select name="disponibilidade_modo" class="disponibilidade-modo w-full border border-zinc-200 rounded-xl px-2 py-2.5 mb-2 bg-white">
                            <option value="sempre" @selected(($produto->disponibilidade_modo ?? 'sempre') === 'sempre')>Sempre disponível</option>
                            <option value="dias" @selected(($produto->disponibilidade_modo ?? 'sempre') === 'dias')>Somente em dias específicos</option>
                        </select>
                        <div class="disponibilidade-dias {{ ($produto->disponibilidade_modo ?? 'sempre') === 'dias' ? '' : 'hidden' }} grid grid-cols-2 gap-2 text-xs">
                            @foreach([0=>'Domingo',1=>'Segunda',2=>'Terça',3=>'Quarta',4=>'Quinta',5=>'Sexta',6=>'Sábado'] as $diaValor => $diaLabel)
                                <label class="flex items-center gap-2 border border-zinc-200 rounded-lg px-2 py-2">
                                    <input type="checkbox" name="dias_disponiveis[]" value="{{ $diaValor }}" @checked(in_array($diaValor, $produto->dias_disponiveis ?? [], true))>
                                    <span>{{ $diaLabel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row flex-wrap gap-2 pt-1">
                        <button class="bg-zinc-900 text-white rounded-xl px-3 py-2 text-sm font-bold hover:bg-zinc-800 w-full sm:w-auto">Atualizar</button>
                </form>
                        <form action="{{ route('admin.produtos.destroy', $produto) }}" method="POST" onsubmit="return confirm('Remover este produto?')">
                            @csrf
                            @method('DELETE')
                            <button class="bg-red-600 text-white rounded-xl px-3 py-2 text-sm font-bold hover:bg-red-700 w-full sm:w-auto">Excluir</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="mt-6">{{ $produtos->links() }}</div>
    </section>
</div>
<script>
    const tabButtons = document.querySelectorAll('.admin-tab-btn');
    const tabSections = document.querySelectorAll('[data-admin-tab]');

    function ativarAba(tab) {
        tabSections.forEach((section) => {
            section.classList.toggle('hidden', section.dataset.adminTab !== tab);
        });

        tabButtons.forEach((button) => {
            const ativa = button.dataset.tab === tab;
            button.classList.toggle('bg-zinc-900', ativa);
            button.classList.toggle('text-white', ativa);
            button.classList.toggle('bg-white', !ativa);
            button.classList.toggle('text-zinc-700', !ativa);
            button.classList.toggle('border', !ativa);
            button.classList.toggle('border-zinc-200', !ativa);
        });
    }

    tabButtons.forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab || 'categorias';
            ativarAba(tab);
            const url = new URL(window.location.href);
            url.searchParams.set('aba', tab);
            window.history.replaceState({}, '', url);
        });
    });

    const abaInicial = new URLSearchParams(window.location.search).get('aba') || 'categorias';
    ativarAba(abaInicial);

    function atualizarCamposPizzaPorSelect(selectEl, containerEl) {
        if (!selectEl || !containerEl) return;
        const selected = selectEl.options[selectEl.selectedIndex];
        const slug = selected ? selected.dataset.slug : '';
        containerEl.classList.toggle('hidden', slug !== 'pizzas');
    }

    const selectNovaCategoria = document.getElementById('nova-categoria-id');
    const novosCamposPizza = document.getElementById('novos-campos-pizza');
    atualizarCamposPizzaPorSelect(selectNovaCategoria, novosCamposPizza);
    selectNovaCategoria?.addEventListener('change', () => {
        atualizarCamposPizzaPorSelect(selectNovaCategoria, novosCamposPizza);
    });

    const novaDisponibilidadeModo = document.getElementById('nova-disponibilidade-modo');
    const novaDisponibilidadeDias = document.getElementById('nova-disponibilidade-dias');
    const toggleNovaDisponibilidade = () => {
        novaDisponibilidadeDias.classList.toggle('hidden', novaDisponibilidadeModo.value !== 'dias');
    };
    toggleNovaDisponibilidade();
    novaDisponibilidadeModo?.addEventListener('change', toggleNovaDisponibilidade);

    document.querySelectorAll('article').forEach((card) => {
        const selectCategoria = card.querySelector('.categoria-produto');
        const camposPizza = card.querySelector('.campos-pizza');
        if (!selectCategoria || !camposPizza) return;

        atualizarCamposPizzaPorSelect(selectCategoria, camposPizza);
        selectCategoria.addEventListener('change', () => {
            atualizarCamposPizzaPorSelect(selectCategoria, camposPizza);
        });

        const disponibilidadeModo = card.querySelector('.disponibilidade-modo');
        const disponibilidadeDias = card.querySelector('.disponibilidade-dias');
        if (disponibilidadeModo && disponibilidadeDias) {
            const toggle = () => disponibilidadeDias.classList.toggle('hidden', disponibilidadeModo.value !== 'dias');
            toggle();
            disponibilidadeModo.addEventListener('change', toggle);
        }
    });
</script>
</body>
</html>
