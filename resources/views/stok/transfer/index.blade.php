@extends('layouts.app')

@section('title', 'Riwayat Transfer Stok')
@section('header', 'Transfer Antar Cabang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Log Mutasi Stok</h3>
            <a href="{{ route('stok.transfer.create') }}"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                <i class="fas fa-paper-plane mr-2"></i> Kirim Stok
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Dari Cabang</th>
                        <th class="px-6 py-4">Ke Cabang</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4 text-center">Jumlah</th>
                        <th class="px-6 py-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transfers as $tf)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ date('d M Y', strtotime($tf->created_at)) }}</td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $tf->tokoAsal->nama_toko }}
                                @if ($tf->id_toko_asal == Session::get('toko_id'))
                                    <span class="text-xs text-blue-500">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $tf->tokoTujuan->nama_toko }}
                                @if ($tf->id_toko_tujuan == Session::get('toko_id'))
                                    <span class="text-xs text-blue-500">(Anda)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $tf->produk->nama_produk }}</td>
                            <td class="px-6 py-4 text-center font-bold">{{ $tf->qty }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Selesai</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat transfer.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
