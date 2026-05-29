const WHATSAPP = '558899567857';
const ROTAS_ENTREGA = window.ROTAS_ENTREGA || {};

function pegarCarrinho() {
    return JSON.parse(localStorage.getItem('carrinho')) || [];
}

function formatarPreco(valor) {
    return valor.toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    });
}

function preencherRotasEntrega() {
    const selectRota = document.getElementById('rota-entrega');
    if (!selectRota) return;

    if (Object.keys(ROTAS_ENTREGA).length === 0) {
        selectRota.innerHTML = '<option value="">Nenhuma rota disponível</option>';
        return;
    }

    const opcoes = Object.entries(ROTAS_ENTREGA).map(([chave, rota]) => {
        return `<option value="${chave}">${rota.nome} - ${rota.endereco} (${formatarPreco(rota.taxa)})</option>`;
    }).join('');

    selectRota.innerHTML = `<option value="">Selecione a rota de entrega</option>${opcoes}`;
}

function obterRotaSelecionada() {
    const rotaKey = document.getElementById('rota-entrega').value;
    return {
        key: rotaKey,
        rota: ROTAS_ENTREGA[rotaKey] || null,
    };
}

function obterTaxaEntregaAtual() {
    const tipoAtendimento = document.getElementById('tipo-atendimento').value;
    if (tipoAtendimento !== 'entrega') return 0;

    const { rota } = obterRotaSelecionada();
    return rota ? Number(rota.taxa) : 0;
}

function renderizarCheckout() {

    let carrinho = pegarCarrinho();

    let resumo = document.getElementById('resumo-pedido');

    let totalElemento = document.getElementById('total-checkout');
    let subtotalElemento = document.getElementById('subtotal-checkout');
    let taxaEntregaElemento = document.getElementById('taxa-entrega-checkout');
    if (!resumo || !totalElemento || !subtotalElemento || !taxaEntregaElemento) return;

    let subtotalPedido = 0;

    if (carrinho.length === 0) {
        resumo.innerHTML = `
            <div class="border rounded-2xl p-4 text-sm text-zinc-500">
                Seu carrinho está vazio.
            </div>
        `;
        subtotalElemento.innerText = formatarPreco(0);
        taxaEntregaElemento.innerText = formatarPreco(0);
        totalElemento.innerText = formatarPreco(0);
        return;
    }

    resumo.innerHTML = carrinho.map(item => {

        let subtotalItem = (Number(item.preco) || 0) * (Number(item.quantidade) || 0);

        subtotalPedido += subtotalItem;

        return `
            <div class="flex justify-between border rounded-2xl p-4">

                <div>
                    <h3 class="font-black">
                        ${item.nome}
                    </h3>
                    ${item.detalhes ? `<p class="text-xs text-zinc-500">${item.detalhes}</p>` : ''}

                    <p class="text-sm text-zinc-500">
                        Quantidade: ${item.quantidade}
                    </p>
                </div>

                <strong class="text-orange-600">
                    ${formatarPreco(subtotalItem)}
                </strong>

            </div>
        `;

    }).join('');

    let taxaEntrega = obterTaxaEntregaAtual();
    let total = subtotalPedido + taxaEntrega;

    subtotalElemento.innerText = formatarPreco(subtotalPedido);
    taxaEntregaElemento.innerText = formatarPreco(taxaEntrega);
    totalElemento.innerText = formatarPreco(total);
}

async function enviarWhatsApp() {
    let carrinho = pegarCarrinho();

    if (carrinho.length === 0) {
        alert('Seu carrinho está vazio.');
        return;
    }

    let nome = document.getElementById('nome').value.trim();
    let telefone = document.getElementById('telefone').value.trim();
    let tipoAtendimento = document.getElementById('tipo-atendimento').value;
    let endereco = document.getElementById('endereco').value.trim();
    let mesaNumero = document.getElementById('mesa-numero').value.trim();
    let rotaEntregaKey = document.getElementById('rota-entrega').value;
    let rotaSelecionada = ROTAS_ENTREGA[rotaEntregaKey] || null;
    let pagamento = document.getElementById('pagamento').value;
    let observacao = document.getElementById('observacao').value.trim();

    if (!nome || !telefone) {
        alert('Preencha nome e telefone.');
        return;
    }

    if (!tipoAtendimento) {
        alert('Selecione o tipo de atendimento.');
        return;
    }

    if (!pagamento) {
        alert('Selecione a forma de pagamento.');
        return;
    }

    if (tipoAtendimento === 'entrega' && !rotaSelecionada) {
        alert('Selecione uma rota de entrega.');
        return;
    }

    let taxaEntrega = obterTaxaEntregaAtual();
    if (tipoAtendimento === 'entrega') {
        if (!endereco) {
            alert('Informe o endereço para entrega.');
            return;
        }
    } else {
        endereco = '';
    }
    let pedido;

    try {
        const response = await fetch('/pedidos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify({
                cliente_nome: nome,
                cliente_telefone: telefone,
                cliente_endereco: endereco,
                tipo_atendimento: tipoAtendimento,
                rota_entrega: tipoAtendimento === 'entrega' ? rotaEntregaKey : null,
                mesa_numero: tipoAtendimento === 'retirada' ? mesaNumero : null,
                taxa_entrega: taxaEntrega,
                forma_pagamento: pagamento,
                observacao: observacao,
                itens: carrinho
            })
        });

        if (!response.ok) {
            let erroMensagem = `Erro ao salvar pedido (${response.status}).`;
            try {
                const erroJson = await response.json();
                if (erroJson && erroJson.message) {
                    erroMensagem = erroJson.message;
                }
            } catch (_) {
                const erroTexto = await response.text();
                console.error('Erro ao salvar pedido:', erroTexto);
            }
            alert(erroMensagem);
            return;
        }

        pedido = await response.json();
    } catch (erro) {
        console.error('Falha de rede ao salvar pedido:', erro);
        alert('Nao foi possivel conectar ao servidor para salvar o pedido.');
        return;
    }

    let subtotalPedido = 0;

    let itens = carrinho.map(item => {
        let subtotalItem = Number(item.preco) * Number(item.quantidade);
        subtotalPedido += subtotalItem;

        const detalhe = item.detalhes ? ` (${item.detalhes})` : '';
        return `• ${item.quantidade}x ${item.nome}${detalhe} - ${formatarPreco(subtotalItem)}`;
    }).join('\n');

    const tipoFormatado = tipoAtendimento === 'entrega' ? 'Entrega' : 'Retirada';
    const enderecoFormatado = tipoAtendimento === 'entrega'
        ? (endereco || 'Não informado')
        : 'Retirada no local';
    const rotaFormatada = tipoAtendimento === 'entrega' && rotaSelecionada ? rotaSelecionada.nome : 'Não se aplica';
    const observacaoFormatada = observacao || 'Nenhuma';

    let mensagem = [
        `*NOVO PEDIDO #${pedido.pedido_id}*`,
        '',
        `*Data/Hora:* ${new Date().toLocaleString('pt-BR')}`,
        `*Tipo:* ${tipoFormatado}`,
        '',
        `*ITENS DO PEDIDO*`,
        itens,
        '',
        `*RESUMO*`,
        `Subtotal: ${formatarPreco(subtotalPedido)}`,
        `Taxa de entrega: ${formatarPreco(taxaEntrega)}`,
        `*Total: ${formatarPreco(subtotalPedido + taxaEntrega)}*`,
        '',
        `*DADOS DO CLIENTE*`,
        `Nome: ${nome}`,
        `Telefone: ${telefone}`,
        `Rota: ${rotaFormatada}`,
        `Endereço: ${enderecoFormatado}`,
        `Pagamento: ${pagamento}`,
        `Observação: ${observacaoFormatada}`,
    ].join('\n');

    let url = `https://wa.me/${WHATSAPP}?text=${encodeURIComponent(mensagem)}`;

    localStorage.removeItem('carrinho');

    window.open(url, '_blank');
    setTimeout(() => {
        window.location.href = '/';
    }, 400);


}

function alterartipoatendimento() {
    let tipo = document.getElementById('tipo-atendimento').value;

    let campoEntrega = document.getElementById('campo-entrega');
    let campoMesa = document.getElementById('campo-mesa');
    let inputEndereco = document.getElementById('endereco');

    if (tipo === 'entrega') {
        campoEntrega.style.display = 'block';
        campoMesa.classList.add('hidden');
    } else if (tipo === 'retirada') {
        campoEntrega.style.display = 'none';
        campoMesa.classList.remove('hidden');
        inputEndereco.value = '';
    } else {
        campoEntrega.style.display = 'none';
        campoMesa.classList.add('hidden');
        inputEndereco.value = '';
    }
}

preencherRotasEntrega();
document.getElementById('rota-entrega').addEventListener('change', () => {
    renderizarCheckout();
});
document.getElementById('tipo-atendimento').addEventListener('change', alterartipoatendimento);
document.getElementById('tipo-atendimento').addEventListener('change', renderizarCheckout);
window.addEventListener('storage', (event) => {
    if (event.key === 'carrinho') {
        renderizarCheckout();
    }
});
alterartipoatendimento();
renderizarCheckout();
