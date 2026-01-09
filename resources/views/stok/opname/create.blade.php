@extends('layouts.app')

@section('title', 'Input Stok Opname')
@section('header', 'Form Stok Opname')

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Sesuaikan Stok Fisik</h2>
            <p class="text-sm text-gray-500">Update jumlah stok sesuai perhitungan fisik real.</p>
        </div>

        <form action="{{ route('stok.opname.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk</label>
                    <select name="id_produk" id="produkSelect" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($produks as $p)
                            <option value="{{ $p->id_produk }}" data-stok="{{ $p->stok_fisik ?? 0 }}">
                                {{ $p->nama_produk }} (Stok Sistem: {{ $p->stok_fisik ?? 0 }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-500 mb-1">Stok Sistem</label>
                        <input type="text" id="stokSistem" readonly
                            class="w-full px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg text-gray-500"
                            value="0">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-800 mb-1">Stok Fisik (Real)</label>
                        <input type="number" name="stok_fisik" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition font-bold text-green-700">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan / Alasan</label>
                    <textarea name="keterangan" rows="2" required
                        placeholder="Contoh: Barang rusak, salah hitung, atau barang temuan."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('stok.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">Simpan
                    Perubahan</button>
            </div>
        </form>
    </div>

    <script>
        // Script Sederhana untuk update Stok Sistem saat produk dipilih
        document.getElementById('produkSelect').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var stok = selectedOption.getAttribute('data-stok');
            document.getElementById('stokSistem').value = stok;
        });
    </script>
@endsection
