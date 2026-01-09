@extends('layouts.app')

@section('title', 'Hak Akses Toko')
@section('header', 'Kelola Akses Cabang')

@section('content')
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">

            <div class="p-6 bg-slate-50 border-b border-gray-100 flex items-center space-x-4">
                <div
                    class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-xl">
                    {{ substr($user->username, 0, 1) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800">{{ $user->nama_lengkap }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->role }} - {{ $user->username }}</p>
                </div>
            </div>

            <div class="p-6">
                <h3 class="text-gray-700 font-semibold mb-4">Pilih Toko yang dapat diakses:</h3>

                <form action="{{ route('users.access.update', $user->id_user) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-3 mb-6 max-h-80 overflow-y-auto pr-2">
                        @forelse($tokos as $toko)
                            <label
                                class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition">
                                <div class="flex items-center h-5">
                                    <input type="checkbox" name="id_toko[]" value="{{ $toko->id_toko }}"
                                        {{ in_array($toko->id_toko, $userAccessIds) ? 'checked' : '' }}
                                        class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500 focus:ring-2">
                                </div>
                                <div class="ml-4">
                                    <span class="block text-sm font-medium text-gray-900">{{ $toko->nama_toko }}</span>
                                    <span
                                        class="block text-xs text-gray-500">{{ $toko->alamat ?? 'Alamat tidak tersedia' }}</span>
                                </div>
                            </label>
                        @empty
                            <div class="text-center py-6 border-2 border-dashed border-gray-200 rounded-lg">
                                <p class="text-gray-400 text-sm">Belum ada data toko di tenant ini.</p>
                                <a href="{{ route('toko.create') }}"
                                    class="text-green-600 text-xs font-bold hover:underline">Buat Toko Dulu</a>
                            </div>
                        @endforelse
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                        <p class="text-xs text-gray-400 italic">User akan diminta memilih toko saat login.</p>
                        <div class="space-x-2">
                            <a href="{{ route('users.index') }}"
                                class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition text-sm">
                                Batal
                            </a>
                            <button type="submit"
                                class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-sm transition text-sm">
                                Simpan Akses
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
