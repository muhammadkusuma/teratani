@extends('layouts.app')

@section('title', 'Tambah Produk')
@section('header', 'Input Produk Baru')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Form Data Produk</h2>
            <p class="text-sm text-gray-500">Isi detail produk dengan lengkap.</p>
        </div>

        <form action="{{ route('produk.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU / Kode Barang</label>
                    <input type="text" name="sku" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition font-mono uppercase"
                        placeholder="BRG-001">
                    <p class="text-xs text-gray-400 mt-1">Kode unik untuk identifikasi stok.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                        placeholder="Contoh: Pupuk Urea 5kg">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="id_kategori" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id_kategori }}">{{ $kat->nama_kategori }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <select name="satuan" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach ($satuans as $sat)
                            <option value="{{ $sat->nama_satuan }}">{{ $sat->nama_satuan }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Pokok (HPP)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="harga_pokok" required min="0"
                            class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="0">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Harga beli rata-rata dari supplier.</p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('produk.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg shadow-sm hover:bg-green-700 transition">
                    Simpan Produk
                </button>
            </div>
        </form>
    </div>
@endsection
