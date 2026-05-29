<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cardápio</title>
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
                        soft: '0 12px 30px rgba(9, 9, 9, 0.10)',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-brandBeige text-zinc-900">

    <header class="relative overflow-hidden bg-brandBlack text-white">
        <div class="absolute inset-0 bg-gradient-to-br from-brandBlack via-zinc-950 to-brandBlack"></div>
        <div class="absolute -top-10 -right-8 h-36 w-36 rounded-full bg-brandYellow/20 blur-3xl"></div>
        <div class="absolute -bottom-12 -left-8 h-44 w-44 rounded-full bg-brandOrange/20 blur-3xl"></div>

        <div class="relative max-w-6xl mx-auto px-4 pt-5 pb-6 sm:pt-6">
            <div class="flex items-center justify-between gap-2 mb-3">
                <a href="{{ url('/') }}" class="inline-flex items-center px-3 py-1.5 rounded-xl bg-white/10 text-xs font-bold border border-white/15">Início</a>
                <div class="flex items-center gap-2 shrink-0">
                    @if(!empty($logoUrl))
                        <img src="{{ $logoUrl }}" alt="Logo" class="hidden sm:block w-14 h-14 rounded-2xl object-cover">
                    @endif
                    <a href="{{ url('/checkout') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-brandYellow text-brandBlack font-black text-sm whitespace-nowrap">
                        Pedido
                        <span id="quantidade-carrinho" class="inline-flex items-center justify-center min-w-[1.4rem] h-6 px-1 rounded-full bg-brandBlack text-white text-xs">0</span>
                    </a>
                </div>
            </div>

            <div class="min-w-0">
                <h1 class="text-3xl font-black">Cardápio</h1>
                <p class="text-zinc-200 text-sm mt-1">Escolha seus itens e monte seu pedido</p>
            </div>
        </div>
    </header>

    <div class="max-w-6xl mx-auto px-4 py-6">
        @if(!($lojaAbertaAgora ?? true))
            <section class="bg-red-50 border border-red-200 text-red-800 rounded-2xl p-4 mb-6">
                <p class="font-black">Estamos fechados no momento.</p>
                <p class="text-sm mt-1">Você pode consultar o cardápio, mas novos pedidos estão temporariamente indisponíveis.</p>
            </section>
        @endif

        <section class="bg-white rounded-3xl p-5 shadow-soft mb-6 border border-amber-100">
            <div class="flex justify-between items-center mb-3">
                <h2 class="text-xl sm:text-2xl font-black">Seu Carrinho</h2>
                <span id="quantidade-itens" class="text-xs font-bold bg-zinc-100 border border-zinc-200 px-3 py-1 rounded-full">0 itens</span>
            </div>

            <div id="itens-carrinho" class="space-y-3">
                <div class="rounded-2xl border border-dashed border-zinc-300 bg-zinc-50 p-4 text-zinc-500 text-sm">
                    Seu carrinho está vazio. Adicione itens para continuar.
                </div>
            </div>

            <div class="mt-4 pt-4 border-t flex items-center justify-between">
                <strong class="text-base">Total</strong>
                <strong id="total-carrinho" class="text-3xl leading-none text-brandOrange font-black">R$ 0,00</strong>
            </div>

            <a href="{{ url('/checkout') }}" class="block mt-4 text-center bg-gradient-to-r from-brandYellow to-brandOrange text-brandBlack font-black py-3.5 rounded-2xl shadow-soft">
                Ir para checkout
            </a>
        </section>

        <section class="bg-white border border-amber-100 rounded-3xl p-4 mb-6 shadow-soft">
            <label for="filtro-produto" class="block text-sm font-bold mb-2">Buscar produto</label>
            <input
                id="filtro-produto"
                type="text"
                placeholder="Ex: pizza, hambúrguer, suco..."
                class="w-full border border-zinc-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-brandYellow"
            >

            <div class="mt-3 overflow-visible sm:overflow-x-auto pb-2" id="atalhos-categoria">
                <div class="flex flex-wrap sm:flex-nowrap gap-2 sm:min-w-max sm:pr-3">
                    <button
                        type="button"
                        class="categoria-btn whitespace-nowrap px-3 sm:px-4 py-2 rounded-2xl bg-zinc-900 text-white font-bold text-sm"
                        data-categoria="todas"
                    >
                        🍽️ Todas
                    </button>
                    @foreach ($categorias as $categoria)
                        @if($categoria->produtos->isNotEmpty())
                            <button
                                type="button"
                                class="categoria-btn whitespace-nowrap px-3 sm:px-4 py-2 rounded-2xl bg-white border border-zinc-200 font-bold text-sm"
                                data-categoria="{{ $categoria->id }}"
                            >
                                🍔 {{ $categoria->nome }}
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        </section>

        <p id="resultado-vazio" class="hidden bg-white border rounded-2xl p-5 text-zinc-500 mb-6">
            Nenhum produto encontrado com esse filtro.
        </p>

        <div id="lista-categorias">
        @foreach ($categorias as $categoria)
            @if($categoria->produtos->isNotEmpty())
            <section class="mb-10 categoria-bloco" data-categoria="{{ $categoria->id }}">

                <div class="flex items-center gap-3 mb-4">
                    <div class="h-[2px] flex-1 bg-amber-200"></div>
                    <h2 class="text-xl sm:text-2xl font-black whitespace-nowrap">{{ $categoria->nome }}</h2>
                    <div class="h-[2px] flex-1 bg-amber-200"></div>
                </div>

                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach ($categoria->produtos as $produto)
                        @php
                            $isPizza = $categoria->slug === 'pizzas';
                            $isPastel = str_contains($categoria->slug, 'past') || (bool)($categoria->usa_adicionais ?? false);
                            $isAcai = $categoria->slug === 'acais' || (bool)($categoria->usa_opcoes ?? false);
                            $isBebidaEspecial = $categoria->slug === 'bebidas' && (str_contains(mb_strtolower($produto->nome), 'coca') || str_contains(mb_strtolower($produto->nome), 'caju'));
                            $precoP = $produto->preco_p ?? $produto->preco;
                            $precoM = $produto->preco_m ?? $produto->preco;
                            $precoG = $produto->preco_g ?? $produto->preco;
                            $categoriaAdicionais = $categoria->adicionais_lista ?? [];
                            $categoriaValorAdicional = max(0, (float) ($categoria->valor_adicional ?? 0));
                            $categoriaOpcoes = $categoria->opcoes_lista ?? [];
                            $produtoDisponivelAgora = true;
                            if (($produto->disponibilidade_modo ?? 'sempre') === 'dias') {
                                $diasProduto = collect($produto->dias_disponiveis ?? [])->map(fn($d)=>(int)$d)->all();
                                $produtoDisponivelAgora = in_array((int)($diaSemanaAtual ?? -1), $diasProduto, true);
                            }
                            $podeAdicionar = ($lojaAbertaAgora ?? true) && $produtoDisponivelAgora;
                        @endphp
                        <article
                            class="bg-white rounded-3xl p-4 shadow-soft border border-amber-100 produto-card"
                            data-nome="{{ mb_strtolower($produto->nome) }}"
                            data-descricao="{{ mb_strtolower($produto->descricao ?? '') }}"
                        >
                            @if($produto->imagem)
                                <img
                                    src="{{ $produto->imagem_url }}"
                                    alt="{{ $produto->nome }}"
                                    class="w-full h-40 object-cover rounded-2xl mb-3 border border-amber-100"
                                >
                            @else
                                <div class="w-full h-40 rounded-2xl mb-3 border border-amber-100 bg-amber-100 flex items-center justify-center">
                                    <span class="text-amber-700 font-bold text-sm">Sem foto</span>
                                </div>
                            @endif

                            <h3 class="text-lg font-black leading-tight">{{ $produto->nome }}</h3>

                            <p class="text-zinc-600 mt-1 text-sm leading-relaxed">{{ $produto->descricao }}</p>
                            @if(!($lojaAbertaAgora ?? true))
                                <p class="mt-1 text-xs font-bold text-red-700">Indisponível fora do horário de funcionamento</p>
                            @elseif(!$produtoDisponivelAgora)
                                <p class="mt-1 text-xs font-bold text-red-700">Indisponível hoje</p>
                            @endif

                            @if($isPizza)
                                <div class="mt-3 p-3 rounded-2xl border border-zinc-200 bg-amber-50">
                                    <p class="text-xs font-bold mb-2 text-zinc-600">Escolha o tamanho</p>
                                    <select id="pizza-tamanho-{{ $produto->id }}" class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-sm bg-white">
                                        <option value="P" data-preco="{{ $precoP }}">Pequena - R$ {{ number_format($precoP, 2, ',', '.') }}</option>
                                        <option value="M" data-preco="{{ $precoM }}" selected>Média - R$ {{ number_format($precoM, 2, ',', '.') }}</option>
                                        <option value="G" data-preco="{{ $precoG }}">Grande - R$ {{ number_format($precoG, 2, ',', '.') }}</option>
                                    </select>
                                    <label class="mt-3 flex items-center gap-2 text-xs font-bold text-zinc-700">
                                        <input type="checkbox" id="pizza-meio-a-meio-{{ $produto->id }}" class="w-4 h-4 accent-zinc-900">
                                        Pizza meio a meio
                                    </label>
                                    <div id="pizza-segundo-sabor-wrap-{{ $produto->id }}" class="mt-2 hidden">
                                        <label class="block text-xs font-bold mb-1 text-zinc-600">Segundo sabor</label>
                                        <select id="pizza-segundo-sabor-{{ $produto->id }}" class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-sm bg-white">
                                            @foreach ($categoria->produtos as $saborPizza)
                                                @php
                                                    $saborPrecoP = $saborPizza->preco_p ?? $saborPizza->preco;
                                                    $saborPrecoM = $saborPizza->preco_m ?? $saborPizza->preco;
                                                    $saborPrecoG = $saborPizza->preco_g ?? $saborPizza->preco;
                                                @endphp
                                                <option
                                                    value="{{ $saborPizza->id }}"
                                                    data-nome="{{ $saborPizza->nome }}"
                                                    data-preco-p="{{ $saborPrecoP }}"
                                                    data-preco-m="{{ $saborPrecoM }}"
                                                    data-preco-g="{{ $saborPrecoG }}"
                                                    @selected($saborPizza->id === $produto->id)
                                                >
                                                    {{ $saborPizza->nome }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif

                            @if($isPastel && count($categoriaAdicionais) > 0)
                                <div class="mt-3 p-3 rounded-2xl border border-zinc-200 bg-amber-50">
                                    <p class="text-xs font-bold mb-2 text-zinc-600">Adicionais (R$ {{ number_format($categoriaValorAdicional, 2, ',', '.') }} cada)</p>
                                    <div id="pastel-adicionais-{{ $produto->id }}" class="grid grid-cols-2 gap-2 text-xs">
                                        @foreach ($categoriaAdicionais as $extraPastel)
                                            <label class="flex items-center gap-2 border border-zinc-200 bg-white rounded-xl px-2 py-2">
                                                <input type="checkbox" value="{{ $extraPastel }}" class="pastel-extra-checkbox w-4 h-4 accent-zinc-900">
                                                <span class="leading-tight">{{ $extraPastel }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if($isAcai && count($categoriaOpcoes) > 0)
                                <div class="mt-3 p-3 rounded-2xl border border-zinc-200 bg-amber-50">
                                    <p class="text-xs font-bold mb-2 text-zinc-600">Escolha o tipo de açaí</p>
                                    <select id="acai-opcao-{{ $produto->id }}" class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-sm bg-white">
                                        @foreach($categoriaOpcoes as $opcaoAcai)
                                            <option value="{{ $opcaoAcai['chave'] }}" data-preco="{{ $opcaoAcai['preco'] }}">
                                                {{ $opcaoAcai['nome'] }} — R$ {{ number_format((float)$opcaoAcai['preco'], 2, ',', '.') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if($isBebidaEspecial)
                                <div class="mt-3 p-3 rounded-2xl border border-zinc-200 bg-amber-50">
                                    <p class="text-xs font-bold mb-2 text-zinc-600">Escolha o volume</p>
                                    <select id="bebida-volume-{{ $produto->id }}" class="w-full border border-zinc-200 rounded-xl px-3 py-2 text-sm bg-white">
                                        @if(str_contains(mb_strtolower($produto->nome), 'coca'))
                                            <option value="coca-1l" data-preco="8">Coca 1L — R$ 8,00</option>
                                            <option value="coca-2l" data-preco="13">Coca 2L — R$ 13,00</option>
                                        @else
                                            <option value="cajuina-1l" data-preco="7">Cajuína 1L — R$ 7,00</option>
                                            <option value="cajuina-2l" data-preco="12">Cajuína 2L — R$ 12,00</option>
                                        @endif
                                    </select>
                                </div>
                            @endif

                            <div class="flex justify-between items-center mt-4">
                                <strong class="text-brandOrange text-2xl leading-none font-black">
                                    @if($isPizza)
                                        R$ {{ number_format(min($precoP, $precoM, $precoG), 2, ',', '.') }}
                                    @else
                                        R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                    @endif
                                </strong>

                                <button
                                    onclick="adicionarProdutoConfigurado(this)"
                                    data-id="{{ $produto->id }}"
                                    data-nome="{{ addslashes($produto->nome) }}"
                                    data-preco="{{ $produto->preco }}"
                                    data-categoria-slug="{{ $categoria->slug }}"
                                    data-valor-adicional="{{ $categoriaValorAdicional }}"
                                    data-preco-p="{{ $precoP }}"
                                    data-preco-m="{{ $precoM }}"
                                    data-preco-g="{{ $precoG }}"
                                    @disabled(!$podeAdicionar)
                                    class="px-4 py-2.5 rounded-2xl font-bold text-sm {{ $podeAdicionar ? 'bg-zinc-900 text-white' : 'bg-zinc-300 text-zinc-500 cursor-not-allowed' }}">
                                    {{ $podeAdicionar ? 'Adicionar' : 'Indisponível' }}
                                </button>
                            </div>

                        </article>
                    @endforeach

                </div>

            </section>
            @endif
        @endforeach
        </div>

    </div>

    <script src="/js/carrinho.js"></script>
    <script>
        const inputFiltro = document.getElementById('filtro-produto');
        const botoesCategoria = document.querySelectorAll('.categoria-btn');
        const blocosCategoria = document.querySelectorAll('.categoria-bloco');
        const cardsProduto = document.querySelectorAll('.produto-card');
        const avisoVazio = document.getElementById('resultado-vazio');

        let categoriaAtiva = 'todas';

        function aplicarFiltros() {
            const termo = (inputFiltro.value || '').toLowerCase().trim();
            let totalVisiveis = 0;

            blocosCategoria.forEach((bloco) => {
                const categoriaId = bloco.getAttribute('data-categoria');
                const cards = bloco.querySelectorAll('.produto-card');
                let visiveisNoBloco = 0;

                const categoriaLiberada = categoriaAtiva === 'todas' || categoriaAtiva === categoriaId;

                cards.forEach((card) => {
                    const nome = card.getAttribute('data-nome') || '';
                    const descricao = card.getAttribute('data-descricao') || '';
                    const atendeBusca = !termo || nome.includes(termo) || descricao.includes(termo);
                    const mostrar = categoriaLiberada && atendeBusca;

                    card.style.display = mostrar ? '' : 'none';
                    if (mostrar) {
                        visiveisNoBloco += 1;
                        totalVisiveis += 1;
                    }
                });

                bloco.style.display = visiveisNoBloco > 0 ? '' : 'none';
            });

            avisoVazio.classList.toggle('hidden', totalVisiveis > 0);
        }

        inputFiltro.addEventListener('input', aplicarFiltros);

        botoesCategoria.forEach((botao) => {
            botao.addEventListener('click', () => {
                categoriaAtiva = botao.getAttribute('data-categoria');

                botoesCategoria.forEach((outro) => {
                    const ativo = outro === botao;
                    outro.classList.toggle('bg-zinc-900', ativo);
                    outro.classList.toggle('text-white', ativo);
                    outro.classList.toggle('bg-white', !ativo);
                    outro.classList.toggle('border-zinc-200', !ativo);
                });

                aplicarFiltros();
            });
        });

        window.adicionarProdutoConfigurado = function (botao) {
            if (botao.disabled) return;
            const id = Number(botao.dataset.id);
            const nome = botao.dataset.nome;
            const precoBase = Number(botao.dataset.preco);
            const categoriaSlug = botao.dataset.categoriaSlug || '';
            let precoFinal = precoBase;
            let detalhes = '';
            let adicionais = 0;
            let tamanho = null;

            if (categoriaSlug === 'pizzas') {
                const select = document.getElementById(`pizza-tamanho-${id}`);
                const opcao = select.options[select.selectedIndex];
                tamanho = opcao.value;
                precoFinal = Number(opcao.dataset.preco || precoBase);
                detalhes = `Tamanho ${tamanho}`;

                const meioAMeioCheckbox = document.getElementById(`pizza-meio-a-meio-${id}`);
                const segundoSaborSelect = document.getElementById(`pizza-segundo-sabor-${id}`);
                const meioAMeioAtivo = Boolean(meioAMeioCheckbox && meioAMeioCheckbox.checked && segundoSaborSelect);

                if (meioAMeioAtivo) {
                    const segundaOpcao = segundoSaborSelect.options[segundoSaborSelect.selectedIndex];
                    const nomeSegundoSabor = segundaOpcao.dataset.nome || segundaOpcao.text || 'Sabor 2';
                    const precoSegundoSabor = Number(
                        tamanho === 'P' ? segundaOpcao.dataset.precoP :
                        (tamanho === 'G' ? segundaOpcao.dataset.precoG : segundaOpcao.dataset.precoM)
                    );
                    const precoPrimeiroSabor = Number(opcao.dataset.preco || precoBase);
                    precoFinal = (precoPrimeiroSabor / 2) + (precoSegundoSabor / 2);
                    detalhes = `Pizza meio a meio: ${nome} + ${nomeSegundoSabor} | Tamanho ${tamanho}`;
                }
            }

            const adicionaisContainer = document.getElementById(`pastel-adicionais-${id}`);
            if (adicionaisContainer) {
                const checkboxes = adicionaisContainer ? Array.from(adicionaisContainer.querySelectorAll('.pastel-extra-checkbox:checked')) : [];
                const extras = checkboxes.map((checkbox) => checkbox.value.trim()).filter(Boolean);
                const valorAdicional = Number(botao.dataset.valorAdicional || 0);
                adicionais = extras.length;
                precoFinal = precoBase + (adicionais * valorAdicional);
                if (adicionais > 0) {
                    detalhes = `Extras: ${extras.join(', ')} (+R$ ${(adicionais * valorAdicional).toFixed(2).replace('.', ',')})`;
                }
            }

            const selectAcai = document.getElementById(`acai-opcao-${id}`);
            if (selectAcai) {
                const opcaoAcai = selectAcai.options[selectAcai.selectedIndex];
                precoFinal = Number(opcaoAcai.dataset.preco || precoBase);
                detalhes = `Açaí: ${opcaoAcai.text.split(' — ')[0]}`;
            }

            if (categoriaSlug === 'bebidas') {
                const selectBebida = document.getElementById(`bebida-volume-${id}`);
                if (selectBebida) {
                    const opcaoBebida = selectBebida.options[selectBebida.selectedIndex];
                    precoFinal = Number(opcaoBebida.dataset.preco || precoBase);
                    detalhes = opcaoBebida.text.split(' - ')[0];
                }
            }

            window.adicionarAoCarrinho(id, nome, precoFinal, {
                categoriaSlug,
                tamanho,
                adicionais,
                detalhes,
                precoBase,
                meioAMeio: categoriaSlug === 'pizzas' && Boolean(document.getElementById(`pizza-meio-a-meio-${id}`)?.checked),
                sabor2Id: Number(document.getElementById(`pizza-segundo-sabor-${id}`)?.value || 0) || null,
                acaiOpcao: String(document.getElementById(`acai-opcao-${id}`)?.value || ''),
            });
        };

        document.querySelectorAll('[id^="pizza-meio-a-meio-"]').forEach((checkbox) => {
            checkbox.addEventListener('change', () => {
                const id = checkbox.id.replace('pizza-meio-a-meio-', '');
                const wrap = document.getElementById(`pizza-segundo-sabor-wrap-${id}`);
                if (wrap) {
                    wrap.classList.toggle('hidden', !checkbox.checked);
                }
            });
        });

        aplicarFiltros();
    </script>
</body>
</html>
