const WHATSAPP = '558899567857';
const ROTAS_ENTREGA = window.ROTAS_ENTREGA || {};
let pedidoPendente = null;

function mostrarErroCheckout(mensagem) {
    const box = document.getElementById('checkout-erro');
    if (!box) return;
    box.textContent = mensagem;
    box.classList.remove('hidden');
}

function limparErroCheckout() {
    const box = document.getElementById('checkout-erro');
    if (!box) return;
    box.textContent = '';
    box.classList.add('hidden');
}

function pegarCarrinho() {
    try {
        const valor = JSON.parse(localStorage.getItem('carrinho'));
        return Array.isArray(valor) ? valor : [];
    } catch (_) {
        localStorage.removeItem('carrinho');
        return [];
    }
}

function formatarPreco(valor) {
    const numero = Number(valor) || 0;
    return numero.toLocaleString('pt-BR', {
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

function montarPayloadPedido() {
    const carrinho = pegarCarrinho();
    const nome = document.getElementById('nome').value.trim();
    const telefone = document.getElementById('telefone').value.trim();
    const tipoAtendimento = document.getElementById('tipo-atendimento').value;
    let endereco = document.getElementById('endereco').value.trim();
    const mesaNumero = document.getElementById('mesa-numero').value.trim();
    const rotaEntregaKey = document.getElementById('rota-entrega').value;
    const rotaSelecionada = ROTAS_ENTREGA[rotaEntregaKey] || null;
    const pagamento = document.getElementById('pagamento').value;
    const observacao = document.getElementById('observacao').value.trim();

    return {
        carrinho,
        nome,
        telefone,
        tipoAtendimento,
        endereco,
        mesaNumero,
        rotaEntregaKey,
        rotaSelecionada,
        pagamento,
        observacao
    };
}

function validarDadosAntesEnvio(dados) {
    if (dados.carrinho.length === 0) return 'Seu carrinho está vazio.';
    if (!dados.nome || !dados.telefone) return 'Preencha nome e telefone.';
    if (!dados.tipoAtendimento) return 'Selecione o tipo de atendimento.';
    if (!dados.pagamento) return 'Selecione a forma de pagamento.';
    if (dados.tipoAtendimento === 'entrega' && !dados.rotaSelecionada) return 'Selecione uma rota de entrega.';
    if (dados.tipoAtendimento === 'entrega' && !dados.endereco) return 'Informe o endereço para entrega.';
    return null;
}

function iniciarEnvioWhatsApp() {
    limparErroCheckout();
    const dados = montarPayloadPedido();
    const erroValidacao = validarDadosAntesEnvio(dados);
    if (erroValidacao) {
        mostrarErroCheckout(erroValidacao);
        alert(erroValidacao);
        return;
    }

    let subtotalPedido = 0;

    let itens = dados.carrinho.map(item => {
        let subtotalItem = Number(item.preco) * Number(item.quantidade);
        subtotalPedido += subtotalItem;

        const detalhe = item.detalhes ? ` (${item.detalhes})` : '';
        return `• ${item.quantidade}x ${item.nome}${detalhe} - ${formatarPreco(subtotalItem)}`;
    }).join('\n');

    const tipoFormatado = dados.tipoAtendimento === 'entrega' ? 'Entrega' : 'Retirada';
    const enderecoFormatado = dados.tipoAtendimento === 'entrega'
        ? (dados.endereco || 'Não informado')
        : 'Retirada no local';
    const rotaFormatada = dados.tipoAtendimento === 'entrega' && dados.rotaSelecionada ? dados.rotaSelecionada.nome : 'Não se aplica';
    const observacaoFormatada = dados.observacao || 'Nenhuma';
    const taxaEntrega = obterTaxaEntregaAtual();

    let mensagem = [
        `*NOVO PEDIDO*`,
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
        `Nome: ${dados.nome}`,
        `Telefone: ${dados.telefone}`,
        `Rota: ${rotaFormatada}`,
        `Endereço: ${enderecoFormatado}`,
        `Pagamento: ${dados.pagamento}`,
        `Observação: ${observacaoFormatada}`,
    ].join('\n');

    const texto = encodeURIComponent(mensagem);
    const urlWeb = `https://wa.me/${WHATSAPP}?text=${texto}`;
    const urlApp = `whatsapp://send?phone=${WHATSAPP}&text=${texto}`;

    pedidoPendente = {
        cliente_nome: dados.nome,
        cliente_telefone: dados.telefone,
        cliente_endereco: dados.tipoAtendimento === 'entrega' ? dados.endereco : '',
        tipo_atendimento: dados.tipoAtendimento,
        rota_entrega: dados.tipoAtendimento === 'entrega' ? dados.rotaEntregaKey : null,
        mesa_numero: dados.tipoAtendimento === 'retirada' ? dados.mesaNumero : null,
        taxa_entrega: taxaEntrega,
        forma_pagamento: dados.pagamento,
        observacao: dados.observacao,
        itens: dados.carrinho
    };

    const confirmarBtn = document.getElementById('confirmar-envio-btn');
    if (confirmarBtn) {
        confirmarBtn.classList.remove('hidden');
    }

    const isMobile = /Android|iPhone|iPad|iPod/i.test(navigator.userAgent || '');
    if (isMobile) {
        window.location.href = urlApp;
        setTimeout(() => {
            if (!document.hidden) {
                window.location.href = urlWeb;
            }
        }, 1200);
        setTimeout(() => {
            window.location.href = '/';
        }, 2600);
        return;
    }

    const popup = window.open(urlWeb, '_blank');
    if (!popup) {
        window.location.href = urlWeb;
    }
    setTimeout(() => {
        window.location.href = '/';
    }, 600);


}

function alterartipoatendimento() {
    const selectTipo = document.getElementById('tipo-atendimento');
    const campoEntrega = document.getElementById('campo-entrega');
    const campoMesa = document.getElementById('campo-mesa');
    const inputEndereco = document.getElementById('endereco');

    if (!selectTipo || !campoEntrega || !campoMesa || !inputEndereco) return;

    let tipo = selectTipo.value;

    if (tipo === 'entrega') {
        campoEntrega.style.display = 'block';
        campoEntrega.classList.remove('hidden');
        campoMesa.style.display = 'none';
        campoMesa.classList.add('hidden');
        document.getElementById('rota-entrega').disabled = false;
        document.getElementById('endereco').disabled = false;
        document.getElementById('mesa-numero').disabled = true;
        document.getElementById('mesa-numero').value = '';
    } else if (tipo === 'retirada') {
        campoEntrega.style.display = 'none';
        campoEntrega.classList.add('hidden');
        campoMesa.style.display = 'block';
        campoMesa.classList.remove('hidden');
        document.getElementById('rota-entrega').disabled = true;
        document.getElementById('endereco').disabled = true;
        document.getElementById('mesa-numero').disabled = false;
        document.getElementById('rota-entrega').value = '';
        inputEndereco.value = '';
    } else {
        campoEntrega.style.display = 'none';
        campoEntrega.classList.add('hidden');
        campoMesa.style.display = 'none';
        campoMesa.classList.add('hidden');
        document.getElementById('rota-entrega').disabled = true;
        document.getElementById('endereco').disabled = true;
        document.getElementById('mesa-numero').disabled = true;
        document.getElementById('rota-entrega').value = '';
        document.getElementById('mesa-numero').value = '';
        inputEndereco.value = '';
    }
}

async function confirmarEnvioWhatsApp() {
    if (!pedidoPendente) {
        const msg = 'Nenhum pedido pendente para confirmar.';
        mostrarErroCheckout(msg);
        alert(msg);
        return;
    }

    try {
        const response = await fetch('/pedidos', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: JSON.stringify(pedidoPendente)
        });

        if (!response.ok) {
            let erroMensagem = `Erro ao salvar pedido (${response.status}).`;
            try {
                const erroJson = await response.json();
                if (erroJson && erroJson.message) {
                    erroMensagem = erroJson.message;
                }
            } catch (_) {}
            mostrarErroCheckout(erroMensagem);
            alert(erroMensagem);
            return;
        }
    } catch (erro) {
        console.error('Falha de rede ao salvar pedido:', erro);
        const msg = 'Nao foi possivel conectar ao servidor para salvar o pedido.';
        mostrarErroCheckout(msg);
        alert(msg);
        return;
    }

    localStorage.removeItem('carrinho');
    pedidoPendente = null;
    window.location.href = '/';
}
function inicializarCheckout() {
    preencherRotasEntrega();

    const selectTipo = document.getElementById('tipo-atendimento');
    const selectRota = document.getElementById('rota-entrega');
    const selectPagamento = document.getElementById('pagamento');

    if (selectTipo && !selectTipo.value) {
        selectTipo.value = '';
    }
    if (selectRota && !selectRota.value) {
        selectRota.value = '';
    }
    if (selectPagamento && !selectPagamento.value) {
        selectPagamento.value = '';
    }

    if (selectRota) {
        selectRota.addEventListener('change', renderizarCheckout);
    }
    if (selectTipo) {
        selectTipo.addEventListener('change', alterartipoatendimento);
        selectTipo.addEventListener('change', renderizarCheckout);
    }
    window.addEventListener('storage', (event) => {
        if (event.key === 'carrinho') {
            renderizarCheckout();
        }
    });

    alterartipoatendimento();
    renderizarCheckout();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', inicializarCheckout);
} else {
    inicializarCheckout();
}
