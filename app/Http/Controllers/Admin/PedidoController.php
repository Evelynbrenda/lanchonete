<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pedido;

class PedidoController extends Controller
{
    private const STATUS_PEDIDO = ['novo', 'preparando', 'pronto', 'saiu_para_entrega', 'entregue', 'cancelado'];

    public function index(Request $request)
    {
        $statusFiltro = $request->input('status', 'novo');
        $tipoAtendimentoFiltro = $request->input('tipo_atendimento', 'todos');

        $pedidos = Pedido::with('itens.produto')
            ->when($statusFiltro !== 'todos', function ($query) use ($statusFiltro) {
                $query->where('status', $statusFiltro);
            })
            ->when($tipoAtendimentoFiltro !== 'todos', function ($query) use ($tipoAtendimentoFiltro) {
                $query->where('tipo_atendimento', $tipoAtendimentoFiltro);
            })
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = trim($request->busca);
                $query->where(function ($sub) use ($busca) {
                    $sub->where('nome_cliente', 'like', '%' . $busca . '%')
                        ->orWhere('cliente_telefone', 'like', '%' . $busca . '%');
                });
            })
            ->when($request->filled('data_inicio'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->data_inicio);
            })
            ->when($request->filled('data_fim'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->data_fim);
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $statusDisponiveis = [...self::STATUS_PEDIDO, 'todos'];
        $contagensStatus = Pedido::selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $tiposAtendimentoDisponiveis = ['entrega', 'retirada', 'todos'];

        return view('admin.pedidos.index', compact('pedidos', 'statusDisponiveis', 'statusFiltro', 'contagensStatus', 'tipoAtendimentoFiltro', 'tiposAtendimentoDisponiveis'));
    }

    public function status(Request $request, Pedido $pedido)
    {
        $statusPermitidos = $pedido->tipo_atendimento === 'retirada'
            ? ['novo', 'preparando', 'pronto', 'entregue', 'cancelado']
            : self::STATUS_PEDIDO;

        $request->validate([
            'status' => 'required|in:' . implode(',', $statusPermitidos),
        ]);

        $pedido->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.pedidos.index', [
            'status' => $request->input('filtro_status', 'novo'),
            'busca' => $request->input('filtro_busca'),
            'data_inicio' => $request->input('filtro_data_inicio'),
            'data_fim' => $request->input('filtro_data_fim'),
            'tipo_atendimento' => $request->input('filtro_tipo_atendimento', 'todos'),
            'page' => $request->input('filtro_page'),
        ]);
    }
}
