@extends('layouts.app')

@section('title', 'Bayar Piutang')
@section('header', 'Input Pembayaran Piutang')

@section('content')
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Form Pembayaran Cicilan</h2>
            <p class="text-sm text-gray-500">Pelanggan: <b>{{ $pelanggan->nama_pelanggan }}</b></p>
        </div>

        <form action="{{ route('pembayaran-piutang.store') }}" method="POST">
            @csrf
            <input type="hidden" name="id_pelanggan" value="{{ $pelanggan->id_pelanggan }}">

            <div class="space-y-4">
                <div class="p-4 bg-red-50 rounded-lg border border-red-100 flex justify-between items-center">
                    <span class="text-red-700 font-medium">Sisa Hutang Saat Ini</span>
                    <span class="text-red-700 font-bold text-lg">Rp
                        {{ number_format($pelanggan->sisa_piutang, 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tgl_bayar" value="{{ date('Y-m-d') }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pembayaran (Rp)</label>
                    <input type="number" name="nominal_bayar" required min="1" max="{{ $pelanggan->sisa_piutang }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition font-bold text-green-700 text-lg"
                        placeholder="0">
                    <p class="text-xs text-gray-400 mt-1">Maksimal pembayaran sesuai sisa hutang.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Metode Bayar</label>
                    <select name="metode_bayar"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                        <option value="Tunai">Tunai / Cash</option>
                        <option value="Transfer">Transfer Bank</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea name="keterangan" rows="2" placeholder="Cth: Cicilan ke-1"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('piutang.show', $pelanggan->id_pelanggan) }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-sm">Simpan
                    Pembayaran</button>
            </div>
        </form>
    </div>
@endsection
