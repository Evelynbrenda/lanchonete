@php
    $active = $active ?? '';
@endphp

<div class="relative">
    <button
        type="button"
        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-zinc-900 text-white font-bold border border-zinc-700 shadow-sm hover:bg-zinc-800 transition"
        onclick="this.nextElementSibling.classList.toggle('hidden')"
    >
        <span class="text-lg leading-none">☰</span>
        Menu
    </button>

    <div class="hidden absolute right-0 mt-2 w-64 bg-white text-zinc-800 border border-amber-100 rounded-2xl shadow-xl overflow-hidden z-40">
        <a href="{{ route('admin.dashboard.index') }}" class="block px-4 py-3.5 text-zinc-800 hover:bg-zinc-50 {{ $active === 'dashboard' ? 'bg-amber-50 font-bold text-zinc-900' : '' }}">Dashboard</a>
        <a href="{{ route('admin.pedidos.index') }}" class="block px-4 py-3.5 text-zinc-800 hover:bg-zinc-50 {{ $active === 'pedidos' ? 'bg-amber-50 font-bold text-zinc-900' : '' }}">Pedidos</a>
        <a href="{{ route('admin.produtos.index') }}" class="block px-4 py-3.5 text-zinc-800 hover:bg-zinc-50 {{ $active === 'produtos' ? 'bg-amber-50 font-bold text-zinc-900' : '' }}">Produtos</a>
        <a href="{{ route('admin.site.index') }}" class="block px-4 py-3.5 text-zinc-800 hover:bg-zinc-50 {{ $active === 'site' ? 'bg-amber-50 font-bold text-zinc-900' : '' }}">Conteúdo do Site</a>
        <a href="{{ url('/') }}" class="block px-4 py-3 text-zinc-800 hover:bg-zinc-50">Ver loja</a>
        <form action="{{ route('admin.logout') }}" method="POST" class="border-t border-zinc-100">
            @csrf
            <button class="w-full text-left px-4 py-3.5 text-red-600 hover:bg-red-50 font-bold">Sair</button>
        </form>
    </div>
</div>
