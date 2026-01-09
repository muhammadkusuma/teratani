@extends('layouts.auth')

@section('title', 'Pilih Bisnis')

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8 text-center bg-white border-b border-gray-100">
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center text-green-600 mb-4">
                <i class="fas fa-building text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">Pilih Bisnis</h1>
            <p class="text-sm text-gray-500 mt-2">Anda terdaftar di beberapa bisnis. Silakan pilih salah satu untuk
                melanjutkan.</p>
        </div>

        <div class="p-6 bg-gray-50 max-h-[400px] overflow-y-auto">
            @forelse($tenants as $tenant)
                <form action="{{ route('tenant.switch') }}" method="POST" class="mb-3">
                    @csrf
                    <input type="hidden" name="id_tenant" value="{{ $tenant->id_tenant }}">

                    <button type="submit"
                        class="w-full group text-left bg-white border border-gray-200 p-4 rounded-xl shadow-sm hover:shadow-md hover:border-green-500 hover:ring-1 hover:ring-green-500 transition-all duration-200 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-gray-100 text-gray-600 font-bold flex items-center justify-center group-hover:bg-green-600 group-hover:text-white transition-colors">
                                {{ strtoupper(substr($tenant->nama_bisnis, 0, 1)) }}
                            </div>

                            <div>
                                <h4 class="font-bold text-gray-800 group-hover:text-green-700 transition-colors">
                                    {{ $tenant->nama_bisnis }}</h4>
                                <span
                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 mt-1">
                                    <i class="fas fa-user-tag mr-1 text-xs"></i>
                                    {{ ucfirst(strtolower($tenant->role ?? 'Member')) }}
                                </span>
                            </div>
                        </div>

                        <div class="text-gray-400 group-hover:text-green-600">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                </form>
            @empty
                <div class="text-center py-8">
                    <div class="text-gray-300 mb-3">
                        <i class="fas fa-folder-open text-4xl"></i>
                    </div>
                    <p class="text-gray-500">Tidak ada data bisnis ditemukan.</p>
                </div>
            @endforelse
        </div>

        <div class="p-4 bg-white border-t border-gray-100 text-center">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-sm text-red-500 hover:text-red-700 font-medium transition">
                    <i class="fas fa-sign-out-alt mr-1"></i> Keluar / Ganti Akun
                </button>
            </form>
        </div>
    </div>
@endsection
