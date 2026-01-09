@extends('layouts.auth')

@section('title', 'Pilih Cabang Toko')

@section('content')
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="p-8 bg-slate-900 text-white text-center relative overflow-hidden">
            <div
                class="absolute top-0 left-0 w-full h-full opacity-10 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')]">
            </div>

            <div class="relative z-10">
                <p class="text-slate-400 text-xs uppercase tracking-widest mb-1">Anda mengakses bisnis:</p>
                <h1 class="text-2xl font-bold text-green-400 mb-4">{{ Session::get('tenant_name', 'Nama Bisnis') }}</h1>

                <div
                    class="inline-flex items-center px-3 py-1 rounded-full bg-slate-800 border border-slate-700 text-xs text-slate-300">
                    <i class="fas fa-user-shield mr-2"></i> Role: {{ Session::get('tenant_role', 'User') }}
                </div>
            </div>
        </div>

        <div class="p-6">
            <h2 class="text-gray-700 font-bold text-lg mb-4 text-center">Pilih Cabang / Toko</h2>

            <div class="space-y-3 max-h-[350px] overflow-y-auto pr-1 custom-scrollbar">
                @forelse($tokos as $toko)
                    <form action="{{ route('toko.switch') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_toko" value="{{ $toko->id_toko }}">

                        <button type="submit"
                            class="w-full group bg-white border border-gray-200 hover:border-green-500 hover:shadow-md rounded-xl p-4 text-left transition-all duration-200 flex items-center justify-between">
                            <div class="flex items-start space-x-4">
                                <div
                                    class="flex-shrink-0 w-12 h-12 rounded-lg bg-green-50 text-green-600 flex items-center justify-center text-xl group-hover:bg-green-600 group-hover:text-white transition-colors">
                                    <i class="fas fa-store"></i>
                                </div>

                                <div>
                                    <h4 class="font-bold text-gray-800 group-hover:text-green-700 transition-colors">
                                        {{ $toko->nama_toko }}
                                    </h4>
                                    <p class="text-xs text-gray-500 mt-1 truncate max-w-[200px]">
                                        <i class="fas fa-map-marker-alt mr-1"></i>
                                        {{ $toko->alamat ?? 'Alamat tidak diset' }}
                                    </p>
                                </div>
                            </div>

                            <div class="text-gray-300 group-hover:text-green-500 transition-colors">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </button>
                    </form>
                @empty
                    <div class="text-center py-6 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <div class="text-gray-400 mb-2">
                            <i class="fas fa-store-slash text-3xl"></i>
                        </div>
                        <p class="text-gray-500 text-sm font-medium">Belum ada toko di bisnis ini.</p>
                        @if (Session::get('tenant_role') == 'OWNER' || Session::get('tenant_role') == 'SUPERADMIN')
                            <a href="#"
                                class="mt-3 inline-block text-xs font-bold text-green-600 hover:text-green-800 underline">
                                + Buat Toko Baru
                            </a>
                        @else
                            <p class="text-xs text-red-400 mt-1">Hubungi Owner untuk akses.</p>
                        @endif
                    </div>
                @endforelse
            </div>
        </div>

        <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-center">
            <a href="{{ route('tenant.select') }}"
                class="text-sm text-gray-500 hover:text-gray-800 font-medium transition flex items-center">
                <i class="fas fa-exchange-alt mr-2"></i> Ganti Bisnis (Tenant)
            </a>
        </div>
    </div>

    <style>
        /* Custom Scrollbar untuk list yang panjang */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #d1d5db;
            border-radius: 3px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #9ca3af;
        }
    </style>
@endsection
