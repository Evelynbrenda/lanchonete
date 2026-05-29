<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdutoController extends Controller
{
    public function index(Request $request)
    {
        $categorias = Categoria::orderBy('nome')->get();
        $categoriasAtivas = $categorias->where('ativo', true)->values();

        $produtos = Produto::with('categoria')
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = trim($request->busca);
                $query->where(function ($sub) use ($busca) {
                    $sub->where('nome', 'like', '%' . $busca . '%')
                        ->orWhere('descricao', 'like', '%' . $busca . '%');
                });
            })
            ->when($request->filled('categoria_id'), function ($query) use ($request) {
                $query->where('categoria_id', $request->categoria_id);
            })
            ->orderBy('categoria_id')
            ->orderBy('nome')
            ->paginate(24)
            ->withQueryString();

        return view('admin.produtos.index', compact('categorias', 'categoriasAtivas', 'produtos'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'preco' => 'required|numeric|min:0',
            'preco_p' => 'nullable|numeric|min:0',
            'preco_m' => 'nullable|numeric|min:0',
            'preco_g' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|image|max:4096',
            'ativo' => 'nullable|boolean',
            'disponibilidade_modo' => 'required|in:sempre,dias',
            'dias_disponiveis' => 'nullable|array',
            'dias_disponiveis.*' => 'integer|between:0,6',
        ]);

        $imagemPath = null;
        if ($request->hasFile('imagem')) {
            $imagemPath = $request->file('imagem')->store('produtos', 'public');
        }

        Produto::create([
            ...$dados,
            'imagem' => $imagemPath,
            'ativo' => (bool) ($dados['ativo'] ?? false),
            'dias_disponiveis' => $dados['disponibilidade_modo'] === 'dias'
                ? array_values(array_map('intval', $dados['dias_disponiveis'] ?? []))
                : null,
        ]);

        return redirect()->route('admin.produtos.index')->with('ok', 'Produto cadastrado.');
    }

    public function update(Request $request, Produto $produto)
    {
        $dados = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nome' => 'required|string|max:255',
            'descricao' => 'nullable|string|max:1000',
            'preco' => 'required|numeric|min:0',
            'preco_p' => 'nullable|numeric|min:0',
            'preco_m' => 'nullable|numeric|min:0',
            'preco_g' => 'nullable|numeric|min:0',
            'imagem' => 'nullable|image|max:4096',
            'remover_imagem' => 'nullable|boolean',
            'ativo' => 'nullable|boolean',
            'disponibilidade_modo' => 'required|in:sempre,dias',
            'dias_disponiveis' => 'nullable|array',
            'dias_disponiveis.*' => 'integer|between:0,6',
        ]);

        $imagemPath = $produto->imagem;
        if ((bool) ($dados['remover_imagem'] ?? false)) {
            if ($imagemPath) {
                Storage::disk('public')->delete($imagemPath);
            }
            $imagemPath = null;
        }

        if ($request->hasFile('imagem')) {
            if ($imagemPath) {
                Storage::disk('public')->delete($imagemPath);
            }
            $imagemPath = $request->file('imagem')->store('produtos', 'public');
        }

        $produto->update([
            ...$dados,
            'imagem' => $imagemPath,
            'ativo' => (bool) ($dados['ativo'] ?? false),
            'dias_disponiveis' => $dados['disponibilidade_modo'] === 'dias'
                ? array_values(array_map('intval', $dados['dias_disponiveis'] ?? []))
                : null,
        ]);

        return redirect()->route('admin.produtos.index')->with('ok', 'Produto atualizado.');
    }

    public function destroy(Produto $produto)
    {
        if ($produto->imagem) {
            Storage::disk('public')->delete($produto->imagem);
        }

        $produto->delete();

        return redirect()->route('admin.produtos.index')->with('ok', 'Produto removido.');
    }

    public function storeCategoria(Request $request)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:120',
            'ativo' => 'nullable|boolean',
            'usa_adicionais' => 'nullable|boolean',
            'adicionais_texto' => 'nullable|string|max:4000',
            'valor_adicional' => 'nullable|numeric|min:0|max:999.99',
            'usa_opcoes' => 'nullable|boolean',
            'opcoes_texto' => 'nullable|string|max:6000',
            'usa_tamanhos' => 'nullable|boolean',
        ]);

        $nome = trim($dados['nome']);
        $slugBase = Str::slug($nome);
        $slug = $slugBase !== '' ? $slugBase : 'categoria';
        $contador = 2;
        while (Categoria::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        Categoria::create([
            'nome' => $nome,
            'slug' => $slug,
            'ativo' => (bool) ($dados['ativo'] ?? false),
            'usa_adicionais' => (bool) ($dados['usa_adicionais'] ?? false),
            'adicionais_texto' => trim((string) ($dados['adicionais_texto'] ?? '')) ?: null,
            'valor_adicional' => max(0, (float) ($dados['valor_adicional'] ?? 0)),
            'usa_opcoes' => (bool) ($dados['usa_opcoes'] ?? false),
            'opcoes_texto' => trim((string) ($dados['opcoes_texto'] ?? '')) ?: null,
            'usa_tamanhos' => (bool) ($dados['usa_tamanhos'] ?? false),
        ]);

        return redirect()->route('admin.produtos.index')->with('ok', 'Categoria cadastrada.');
    }

    public function updateCategoria(Request $request, Categoria $categoria)
    {
        $dados = $request->validate([
            'nome' => 'required|string|max:120',
            'slug' => 'nullable|string|max:120',
            'ativo' => 'nullable|boolean',
            'usa_adicionais' => 'nullable|boolean',
            'adicionais_texto' => 'nullable|string|max:4000',
            'valor_adicional' => 'nullable|numeric|min:0|max:999.99',
            'usa_opcoes' => 'nullable|boolean',
            'opcoes_texto' => 'nullable|string|max:6000',
            'usa_tamanhos' => 'nullable|boolean',
        ]);

        $nome = trim($dados['nome']);
        $slugInput = trim((string) ($dados['slug'] ?? ''));
        $slugBase = Str::slug($slugInput !== '' ? $slugInput : $nome);
        $slug = $slugBase !== '' ? $slugBase : 'categoria';
        $contador = 2;
        while (Categoria::where('slug', $slug)->where('id', '!=', $categoria->id)->exists()) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        $categoria->update([
            'nome' => $nome,
            'slug' => $slug,
            'ativo' => (bool) ($dados['ativo'] ?? false),
            'usa_adicionais' => (bool) ($dados['usa_adicionais'] ?? false),
            'adicionais_texto' => trim((string) ($dados['adicionais_texto'] ?? '')) ?: null,
            'valor_adicional' => max(0, (float) ($dados['valor_adicional'] ?? 0)),
            'usa_opcoes' => (bool) ($dados['usa_opcoes'] ?? false),
            'opcoes_texto' => trim((string) ($dados['opcoes_texto'] ?? '')) ?: null,
            'usa_tamanhos' => (bool) ($dados['usa_tamanhos'] ?? false),
        ]);

        return redirect()->route('admin.produtos.index')->with('ok', 'Categoria atualizada.');
    }

    public function destroyCategoria(Categoria $categoria)
    {
        $temProdutos = Produto::where('categoria_id', $categoria->id)->exists();
        if ($temProdutos) {
            return redirect()->route('admin.produtos.index')->with('ok', 'Não é possível excluir categoria com produtos vinculados.');
        }

        $categoria->delete();

        return redirect()->route('admin.produtos.index')->with('ok', 'Categoria removida.');
    }
}
