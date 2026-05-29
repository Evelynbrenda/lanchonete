window.pegarCarrinho = function () {
    return JSON.parse(localStorage.getItem('carrinho')) || [];
};

window.salvarCarrinho = function (carrinho) {
    localStorage.setItem('carrinho', JSON.stringify(carrinho));
};

window.formatarPreco = function (valor) {
    return valor.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
};

window.criarChaveCarrinho = function (id, opcoes = {}) {
    const tamanho = opcoes.tamanho || '';
    const adicionais = Number(opcoes.adicionais || 0);
    const detalhes = (opcoes.detalhes || '').trim().toLowerCase();
    const meioAMeio = opcoes.meioAMeio ? '1' : '0';
    const sabor2Id = Number(opcoes.sabor2Id || 0);
    return `${id}::${tamanho}::${adicionais}::${meioAMeio}::${sabor2Id}::${detalhes}`;
};

window.adicionarAoCarrinho = function (id, nome, preco, opcoes = {}) {
    preco = Number(preco);

    let carrinho = window.pegarCarrinho();
    const chave = window.criarChaveCarrinho(id, opcoes);

    let produtoExiste = carrinho.find(item => item.chave === chave);

    if (produtoExiste) {
        produtoExiste.quantidade += 1;
    } else {
        carrinho.push({
            chave,
            id,
            nome,
            preco,
            quantidade: 1,
            categoria_slug: opcoes.categoriaSlug || null,
            tamanho: opcoes.tamanho || null,
            adicionais: Number(opcoes.adicionais || 0),
            meio_a_meio: Boolean(opcoes.meioAMeio),
            sabor_2_id: Number(opcoes.sabor2Id || 0) || null,
            acai_opcao: opcoes.acaiOpcao || null,
            detalhes: opcoes.detalhes || '',
            preco_base: Number(opcoes.precoBase || preco),
        });
    }

    window.salvarCarrinho(carrinho);
    window.renderizarCarrinho();
};

window.removerDoCarrinho = function (chave) {

    let carrinho = window.pegarCarrinho();

    carrinho = carrinho.filter(item => item.chave !== chave);

    window.salvarCarrinho(carrinho);

    window.renderizarCarrinho();
};

window.alterarQuantidadeCarrinho = function (chave, delta) {
    let carrinho = window.pegarCarrinho();
    let item = carrinho.find(produto => produto.chave === chave);

    if (!item) return;

    item.quantidade += delta;

    if (item.quantidade <= 0) {
        carrinho = carrinho.filter(produto => produto.chave !== chave);
    }

    window.salvarCarrinho(carrinho);
    window.renderizarCarrinho();
};

window.renderizarCarrinho = function () {

    let carrinho = window.pegarCarrinho();

    let container = document.getElementById('itens-carrinho');

    let quantidade = document.getElementById('quantidade-carrinho') || document.getElementById('quantidade-itens');

    let totalElemento = document.getElementById('total-carrinho');

    if (!container || !quantidade || !totalElemento) return;

    if (carrinho.length === 0) {

        container.innerHTML = `
            <p class="text-zinc-500">
                Seu carrinho está vazio.
            </p>
        `;

        quantidade.innerText = '0 itens';

        totalElemento.innerText = 'R$ 0,00';

        return;
    }

    let total = 0;

    container.innerHTML = carrinho.map(item => {

       let subtotal = Number(item.preco) * Number(item.quantidade);
        total += subtotal;

        return `
            <div class="flex justify-between items-center border rounded-2xl p-4">

                <div>
                    <h3 class="font-black">
                        ${item.nome}
                    </h3>
                    ${item.detalhes ? `<p class="text-xs text-zinc-500">${item.detalhes}</p>` : ''}

                    <p class="text-zinc-500 text-sm">
                        Quantidade: ${item.quantidade}
                    </p>
                </div>

                <div class="text-right">

                    <p class="font-black text-orange-600">
                        ${window.formatarPreco(subtotal)}
                    </p>

                    <div class="flex items-center justify-end gap-2 mt-2">
                        <button
                            onclick="alterarQuantidadeCarrinho('${item.chave}', -1)"
                            class="border rounded-lg w-7 h-7 leading-none"
                        >
                            -
                        </button>
                        <button
                            onclick="alterarQuantidadeCarrinho('${item.chave}', 1)"
                            class="border rounded-lg w-7 h-7 leading-none"
                        >
                            +
                        </button>
                        <button
                            onclick="removerDoCarrinho('${item.chave}')"
                            class="text-red-500 text-sm"
                        >
                            Remover
                        </button>
                    </div>

                </div>

            </div>
        `;

    }).join('');

    quantidade.innerText = `${carrinho.length} itens`;

    totalElemento.innerText = window.formatarPreco(total);
};

window.renderizarCarrinho();
