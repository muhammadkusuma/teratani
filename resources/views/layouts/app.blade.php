<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') - Teratani</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-slate-900 text-white flex-shrink-0 hidden md:flex flex-col">
            <div class="p-4 border-b border-slate-700">
                <h2 class="text-xl font-bold tracking-wider text-green-400">TERATANI</h2>
                <p class="text-xs text-slate-400 mt-1">
                    {{ Session::get('tenant_name', 'No Tenant') }}
                </p>
            </div>

            <nav class="flex-1 overflow-y-auto py-4 px-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center px-4 py-3 text-sm font-medium bg-slate-800 rounded-lg text-white">
                    <i class="fas fa-home w-6 text-center mr-2"></i> Dashboard
                </a>

                <p class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Transaksi</p>
                <a href="#"
                    class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fas fa-cash-register w-6 text-center mr-2"></i> Penjualan (POS)
                </a>
                <a href="#"
                    class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fas fa-shopping-cart w-6 text-center mr-2"></i> Pembelian
                </a>

                <p class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Inventori</p>
                <a href="#"
                    class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fas fa-box w-6 text-center mr-2"></i> Stok & Produk
                </a>

                <p class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Keuangan</p>
                <a href="#"
                    class="flex items-center px-4 py-2.5 text-sm font-medium text-slate-300 hover:bg-slate-800 hover:text-white rounded-lg transition">
                    <i class="fas fa-file-invoice-dollar w-6 text-center mr-2"></i> Piutang
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-red-400 bg-slate-800 hover:bg-red-900/30 rounded-lg transition">
                        <i class="fas fa-sign-out-alt mr-2"></i> Keluar
                    </button>
                </form>
            </div>
        </aside>

        <div class="flex-1 flex flex-col overflow-hidden">

            <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6 z-10">
                <button class="md:hidden text-gray-500 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <h2 class="text-lg font-semibold text-gray-700">@yield('header', 'Overview')</h2>

                <div class="flex items-center space-x-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-medium text-gray-800">{{ Session::get('username') }}</div>
                        <div class="text-xs text-gray-500">{{ Session::get('toko_name', 'Pusat') }}</div>
                    </div>
                    <div
                        class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-bold border border-green-200">
                        {{ substr(Session::get('username', 'U'), 0, 1) }}
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 p-6">
                @yield('content')
            </main>
        </div>
    </div>

</body>

</html>
