<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\PedidoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $dados = $request->validate([
            'data_inicio' => 'nullable|date',
            'data_fim' => 'nullable|date|after_or_equal:data_inicio',
        ]);

        $inicioMes = !empty($dados['data_inicio'])
            ? Carbon::parse($dados['data_inicio'])->startOfDay()
            : Carbon::now()->startOfMonth()->startOfDay();

        $fimMes = !empty($dados['data_fim'])
            ? Carbon::parse($dados['data_fim'])->endOfDay()
            : Carbon::now()->endOfMonth()->endOfDay();

        $baseMes = Pedido::query()->whereBetween('created_at', [$inicioMes, $fimMes]);

        $totalPedidosMes = (clone $baseMes)->count();
        $pedidosEntreguesMes = (clone $baseMes)->where('status', 'entregue')->count();
        $pedidosCanceladosMes = (clone $baseMes)->where('status', 'cancelado')->count();

        $faturamentoMes = (clone $baseMes)
            ->whereIn('status', ['novo', 'preparando', 'pronto', 'entregue'])
            ->sum('total');

        $statusResumo = (clone $baseMes)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $maisPedidosNoMes = PedidoItem::query()
            ->join('pedidos', 'pedido_items.pedido_id', '=', 'pedidos.id')
            ->leftJoin('produtos', 'pedido_items.produto_id', '=', 'produtos.id')
            ->whereBetween('pedidos.created_at', [$inicioMes, $fimMes])
            ->where('pedidos.status', '!=', 'cancelado')
            ->selectRaw("pedido_items.produto_id, COALESCE(produtos.nome, 'Produto removido') as nome, SUM(pedido_items.quantidade) as quantidade")
            ->groupBy('pedido_items.produto_id', 'produtos.nome')
            ->orderByDesc('quantidade')
            ->limit(10)
            ->get();

        $enderecosQueMaisPediram = Pedido::query()
            ->whereBetween('created_at', [$inicioMes, $fimMes])
            ->where('status', '!=', 'cancelado')
            ->whereNotNull('cliente_endereco')
            ->where('cliente_endereco', '!=', '')
            ->selectRaw('cliente_endereco, COUNT(*) as total')
            ->groupBy('cliente_endereco')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('admin.dashboard.index', compact(
            'inicioMes',
            'fimMes',
            'totalPedidosMes',
            'pedidosEntreguesMes',
            'pedidosCanceladosMes',
            'faturamentoMes',
            'statusResumo',
            'maisPedidosNoMes',
            'enderecosQueMaisPediram'
        ));
    }
}
