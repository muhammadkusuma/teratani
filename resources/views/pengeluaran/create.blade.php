@extends('layouts.app')

@section('title', 'Input Pengeluaran')
@section('header', 'Catat Biaya Baru')

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Form Pengeluaran</h2>
            <p class="text-sm text-gray-500">Catat setiap uang keluar agar keuangan terpantau.</p>
        </div>

        <form action="{{ route('pengeluaran.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" name="tgl_pengeluaran" value="{{ date('Y-m-d') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori Biaya</label>
                    <select name="kategori_biaya" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                        <option value="Operasional">Operasional Toko (Listrik, Air, Internet)</option>
                        <option value="Gaji">Gaji Karyawan</option>
                        <option value="Transportasi">Transportasi / Bensin</option>
                        <option value="Perlengkapan">Perlengkapan / ATK</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal (Rp)</label>
                    <input type="number" name="nominal" required min="0" placeholder="0"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan Detail</label>
                    <textarea name="keterangan" rows="3" required placeholder="Contoh: Bayar tagihan listrik bulan Januari"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-red-500 focus:border-red-500 transition"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('pengeluaran.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-red-600 text-white font-bold rounded-lg hover:bg-red-700 shadow-sm">Simpan</button>
            </div>
        </form>
    </div>
@endsection
