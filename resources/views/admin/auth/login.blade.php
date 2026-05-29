<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Admin</title>
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
                        soft: '0 18px 40px rgba(9, 9, 9, 0.12)',
                    }
                }
            }
        };
    </script>
</head>
<body class="min-h-screen bg-brandBeige text-zinc-900">
    <div class="min-h-screen relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-brandBeige via-white to-brandBeige"></div>
        <div class="absolute -top-20 -left-16 h-72 w-72 rounded-full bg-brandYellow/20 blur-3xl"></div>
        <div class="absolute -bottom-24 -right-20 h-80 w-80 rounded-full bg-brandOrange/20 blur-3xl"></div>

        <main class="relative min-h-screen max-w-6xl mx-auto px-4 py-8 lg:py-12 flex items-center">
            <div class="w-full grid lg:grid-cols-2 gap-6 lg:gap-8 items-stretch">
                <section class="hidden lg:flex bg-brandBlack text-white rounded-3xl p-8 xl:p-10 shadow-soft flex-col justify-between">
                    <div>
                        <img src="/images/logo.png" alt="Logo Lanchonete do Márcio" class="w-20 h-20 rounded-2xl object-cover mb-6">
                        <h1 class="text-4xl font-black leading-tight">Painel Administrativo</h1>
                        <p class="mt-3 text-zinc-200 text-lg">Gerencie pedidos, produtos e o atendimento da lanchonete.</p>
                    </div>

                    <p class="inline-flex w-fit mt-8 px-4 py-2 rounded-full bg-white/10 text-brandYellow font-bold text-sm border border-white/20">
                        Acesso restrito à equipe.
                    </p>
                </section>

                <section class="bg-white rounded-3xl border border-amber-100 p-6 sm:p-8 shadow-soft flex flex-col justify-center">
                    <div class="lg:hidden mb-6">
                        <img src="/images/logo.png" alt="Logo Lanchonete do Márcio" class="w-16 h-16 rounded-2xl object-cover mb-4">
                        <h1 class="text-2xl sm:text-3xl font-black leading-tight">Painel Administrativo</h1>
                        <p class="mt-2 text-zinc-600 text-sm sm:text-base">Gerencie pedidos, produtos e o atendimento da lanchonete.</p>
                        <p class="mt-3 text-xs font-bold text-brandOrange">Acesso restrito à equipe.</p>
                    </div>

                    <h2 class="hidden lg:block text-2xl font-black mb-2">Acesso Administrativo</h2>
                    <p class="hidden lg:block text-zinc-500 mb-6">Informe a senha para entrar no painel.</p>

                    @if (session('erro'))
                        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl px-4 py-3 text-sm font-semibold">{{ session('erro') }}</div>
                    @endif

                    <form action="{{ route('admin.login.attempt') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="senha" class="block text-sm font-bold mb-2">Senha</label>
                            <input
                                id="senha"
                                type="password"
                                name="senha"
                                class="w-full border border-zinc-200 rounded-2xl px-4 py-3 outline-none focus:ring-2 focus:ring-brandYellow focus:border-brandYellow transition"
                                required
                            >
                            @error('senha')
                                <p class="mt-2 text-sm text-red-600 font-semibold">{{ $message }}</p>
                            @enderror
                        </div>

                        <button class="w-full bg-brandBlack text-white rounded-2xl py-3.5 font-black transition hover:bg-gradient-to-r hover:from-brandYellow hover:to-brandOrange hover:text-brandBlack">
                            Entrar no painel
                        </button>
                    </form>

                    <a href="{{ url('/') }}" class="block text-center mt-5 text-sm text-zinc-500 hover:text-zinc-700 transition">
                        Voltar para a loja
                    </a>
                </section>
            </div>
        </main>
    </div>
</body>
</html>
