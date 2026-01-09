@extends('layouts.app')

@section('title', 'Tambah Pelanggan')
@section('header', 'Input Pelanggan Baru')

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Form Data Pelanggan</h2>
        </div>

        <form action="{{ route('pelanggan.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pelanggan</label>
                    <input type="text" name="nama_pelanggan" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Wilayah / Alamat</label>
                    <input type="text" name="wilayah" placeholder="Contoh: Desa Suka Maju"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Limit Piutang (Max Hutang)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500 text-sm">Rp</span>
                        <input type="number" name="limit_piutang" value="0" min="0"
                            class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Batas maksimal nominal hutang yang diperbolehkan.</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('pelanggan.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">Simpan</button>
            </div>
        </form>
    </div>
@endsection
