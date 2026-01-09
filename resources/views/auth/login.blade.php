@extends('layouts.auth')

@section('title', 'Masuk Aplikasi')

@section('content')
    <div class="bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-800">Teratani SaaS</h1>
            <p class="text-sm text-gray-500 mt-2">Masuk untuk mengelola bisnis Anda</p>
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

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="mb-5">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username / Email</label>
                <input type="text" name="username" id="username" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                    placeholder="Masukkan username atau email">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                    placeholder="••••••••">
            </div>

            <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2.5 rounded-lg shadow-md transition duration-200">
                Masuk Sekarang
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            Belum punya akun? <a href="#" class="text-green-600 hover:underline">Hubungi Admin</a>
        </div>
    </div>
@endsection
