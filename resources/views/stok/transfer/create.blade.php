@extends('layouts.app')

@section('title', 'Kirim Stok')
@section('header', 'Transfer Stok Keluar')

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Form Pengiriman Stok</h2>
            <p class="text-sm text-gray-500">Pindahkan stok dari <b>{{ Session::get('toko_name') }}</b> ke cabang lain.</p>
        </div>

        <form action="{{ route('stok.transfer.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang Tujuan</label>
                    <select name="id_toko_tujuan" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Pilih Cabang Penerima --</option>
                        @foreach ($tokoTujuans as $toko)
                            <option value="{{ $toko->id_toko }}">{{ $toko->nama_toko }} - {{ $toko->alamat }}</option>
                        @endforeach
                    </select>
                    @if ($tokoTujuans->isEmpty())
                        <p class="text-xs text-red-500 mt-1">Tidak ada cabang lain yang tersedia.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Produk yang Dikirim</label>
                    <select name="id_produk" id="produkTransfer" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="">-- Pilih Produk --</option>
                        @foreach ($produks as $p)
                            <option value="{{ $p->id_produk }}" data-max="{{ $p->stok_fisik }}">
                                {{ $p->nama_produk }} (Tersedia: {{ $p->stok_fisik }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Qty)</label>
                    <input type="number" name="qty" id="qtyTransfer" required min="1"
                        placeholder="Masukkan jumlah..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    <p class="text-xs text-gray-500 mt-1">Stok akan berkurang dari gudang ini dan bertambah di tujuan.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="keterangan" rows="2" placeholder="Cth: Permintaan dari cabang..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('stok.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Kirim
                    Sekarang</button>
            </div>
        </form>
    </div>

    <script>
        // Validasi sederhana agar tidak input lebih dari stok tersedia
        document.getElementById('qtyTransfer').addEventListener('input', function() {
            var select = document.getElementById('produkTransfer');
            var max = select.options[select.selectedIndex].getAttribute('data-max');

            if (max && parseInt(this.value) > parseInt(max)) {
                alert('Jumlah melebihi stok yang tersedia (' + max + ')');
                this.value = max;
            }
        });
    </script>
@endsection
