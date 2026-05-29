<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CardapioController;
use App\Http\Controllers\PedidoController;
use App\Models\Pedido;
use App\Http\Controllers\Admin\PedidoController as AdminPedidoController;
use App\Http\Controllers\Admin\ProdutoController as AdminProdutoController;
use App\Http\Controllers\SiteController;
use App\Http\Controllers\Admin\SiteContentController as AdminSiteContentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Models\DeliveryRoute;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return app(SiteController::class)->index();
});
Route::get('/painel', function () {
    if (session('admin_authed')) {
        return redirect('/admin/dashboard');
    }
    return view('admin.auth.login');
})->name('admin.login.form');
Route::post('/painel', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'senha' => 'required|string',
    ]);

    $senhaCorreta = (string) env('ADMIN_PANEL_PASSWORD', '');
    if ($senhaCorreta === '') {
        abort(500, 'ADMIN_PANEL_PASSWORD não configurada.');
    }

    if (!hash_equals($senhaCorreta, (string) $request->senha)) {
        return back()->with('erro', 'Senha inválida.');
    }

    $request->session()->put('admin_authed', true);
    $request->session()->regenerate();

    return redirect('/admin/dashboard');
})->middleware('throttle:10,1')->name('admin.login.attempt');
Route::post('/painel/sair', function (\Illuminate\Http\Request $request) {
    $request->session()->forget('admin_authed');
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('admin.logout');
Route::get('/cardapio', [CardapioController::class, 'index'])->name('cardapio.index');
Route::get('/checkout', function () {
    $rotasEntrega = DeliveryRoute::query()
        ->where('ativo', true)
        ->orderBy('ordem')
        ->orderBy('nome')
        ->get(['nome', 'slug', 'endereco', 'taxa']);

    return view('cardapio.checkout', compact('rotasEntrega'));
})->name('checkout.index');
Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');

Route::prefix('admin')->middleware('admin.auth')->group(function () {
    Route::get('/', function () {
        return redirect('/admin/dashboard');
    });
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard.index');

    Route::get('/pedidos', [AdminPedidoController::class, 'index'])
        ->name('admin.pedidos.index');

    Route::patch('/pedidos/{pedido}/status', [AdminPedidoController::class, 'status'])
        ->name('admin.pedidos.status');

    Route::get('/produtos', [AdminProdutoController::class, 'index'])
        ->name('admin.produtos.index');
    Route::post('/produtos', [AdminProdutoController::class, 'store'])
        ->name('admin.produtos.store');
    Route::patch('/produtos/{produto}', [AdminProdutoController::class, 'update'])
        ->name('admin.produtos.update');
    Route::delete('/produtos/{produto}', [AdminProdutoController::class, 'destroy'])
        ->name('admin.produtos.destroy');
    Route::post('/categorias', [AdminProdutoController::class, 'storeCategoria'])
        ->name('admin.categorias.store');
    Route::patch('/categorias/{categoria}', [AdminProdutoController::class, 'updateCategoria'])
        ->name('admin.categorias.update');
    Route::delete('/categorias/{categoria}', [AdminProdutoController::class, 'destroyCategoria'])
        ->name('admin.categorias.destroy');

    Route::get('/site', [AdminSiteContentController::class, 'index'])
        ->name('admin.site.index');
    Route::patch('/site/configs', [AdminSiteContentController::class, 'updateConfigs'])
        ->name('admin.site.configs.update');

    Route::post('/site/avisos', [AdminSiteContentController::class, 'storeAviso'])
        ->name('admin.site.avisos.store');
    Route::patch('/site/avisos/{aviso}', [AdminSiteContentController::class, 'updateAviso'])
        ->name('admin.site.avisos.update');
    Route::delete('/site/avisos/{aviso}', [AdminSiteContentController::class, 'destroyAviso'])
        ->name('admin.site.avisos.destroy');

    Route::post('/site/promocoes', [AdminSiteContentController::class, 'storePromocao'])
        ->name('admin.site.promocoes.store');
    Route::patch('/site/promocoes/{promocao}', [AdminSiteContentController::class, 'updatePromocao'])
        ->name('admin.site.promocoes.update');
    Route::delete('/site/promocoes/{promocao}', [AdminSiteContentController::class, 'destroyPromocao'])
        ->name('admin.site.promocoes.destroy');

    Route::post('/site/rotas', [AdminSiteContentController::class, 'storeRota'])
        ->name('admin.site.rotas.store');
    Route::patch('/site/rotas/{rota}', [AdminSiteContentController::class, 'updateRota'])
        ->name('admin.site.rotas.update');
    Route::delete('/site/rotas/{rota}', [AdminSiteContentController::class, 'destroyRota'])
        ->name('admin.site.rotas.destroy');
});
