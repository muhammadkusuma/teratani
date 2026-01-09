@extends('layouts.auth')

@section('title', 'Daftar Akun Baru')

@section('content')
    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Mulai Bisnis Anda</h1>
            <p class="text-sm text-gray-500 mt-2">Daftarkan bisnis pertanian/toko Anda di Teratani</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded">
                <ul class="list-disc pl-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST">
            @csrf

            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Informasi Bisnis</h3>
                <div class="mb-4">
                    <label for="nama_bisnis" class="block text-sm font-medium text-gray-700 mb-1">Nama Bisnis / Toko</label>
                    <input type="text" name="nama_bisnis" id="nama_bisnis" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                        placeholder="Contoh: UD Tani Makmur">
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Akun Pemilik</h3>

                <div class="mb-4">
                    <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" id="nama_lengkap" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                        placeholder="Nama lengkap pemilik">
                </div>

                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                    <input type="text" name="username" id="username" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                        placeholder="username_anda">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="mb-4">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                            placeholder="Minimal 8 karakter">
                    </div>
                    <div class="mb-4">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Ulangi
                            Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                            placeholder="Ketik ulang password">
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg shadow-md transition duration-200">
                Daftar Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            Sudah punya akun? <a href="{{ route('login.form') }}" class="text-green-600 hover:underline">Masuk disini</a>
        </div>
    </div>
@endsection
