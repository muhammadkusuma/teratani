@extends('layouts.app')

@section('title', 'Edit Produk')
@section('header', 'Update Produk')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Edit: {{ $produk->nama_produk }}</h2>
        </div>

        <form action="{{ route('produk.update', $produk->id_produk) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU / Kode Barang</label>
                    <input type="text" name="sku" value="{{ old('sku', $produk->sku) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition font-mono uppercase">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Produk</label>
                    <input type="text" name="nama_produk" value="{{ old('nama_produk', $produk->nama_produk) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select name="id_kategori" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach ($kategoris as $kat)
                            <option value="{{ $kat->id_kategori }}"
                                {{ $produk->id_kategori == $kat->id_kategori ? 'selected' : '' }}>
                                {{ $kat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Satuan</label>
                    <select name="satuan" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        @foreach ($satuans as $sat)
                            <option value="{{ $sat->nama_satuan }}"
                                {{ $produk->satuan == $sat->nama_satuan ? 'selected' : '' }}>
                                {{ $sat->nama_satuan }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Pokok (HPP)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="harga_pokok" value="{{ old('harga_pokok', $produk->harga_pokok) }}"
                            required min="0"
                            class="w-full pl-10 px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                <a href="{{ route('produk.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg shadow-sm hover:bg-blue-700 transition">
                    Update Produk
                </button>
            </div>
        </form>
    </div>
@endsection
