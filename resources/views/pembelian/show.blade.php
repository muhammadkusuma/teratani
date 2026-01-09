@extends('layouts.app')

@section('title', 'Detail Pembelian')

@section('content')
    <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-between items-start border-b border-gray-100 pb-6 mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Faktur Pembelian</h2>
                <p class="text-gray-500">No: {{ $pembelian->no_faktur }}</p>
            </div>
            <div class="text-right">
                <h3 class="font-bold text-lg text-gray-700">{{ $pembelian->distributor->nama_distributor }}</h3>
                <p class="text-sm text-gray-500">{{ date('d F Y', strtotime($pembelian->tgl_pembelian)) }}</p>
                <span
                    class="inline-block mt-2 px-3 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold">{{ $pembelian->status_bayar }}</span>
            </div>
        </div>

        <table class="w-full text-sm mb-8">
            <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold">
                <tr>
                    <th class="px-4 py-3 text-left">Produk</th>
                    <th class="px-4 py-3 text-center">Qty</th>
                    <th class="px-4 py-3 text-right">Harga Beli</th>
                    <th class="px-4 py-3 text-right">Subtotal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($pembelian->details as $item)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $item->produk->nama_produk }}</td>
                        <td class="px-4 py-3 text-center">{{ $item->qty }}</td>
                        <td class="px-4 py-3 text-right">Rp {{ number_format($item->harga_beli_satuan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">Rp
                            {{ number_format($item->harga_beli_satuan * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-gray-50">
                    <td colspan="3" class="px-4 py-3 text-right font-bold text-gray-700">TOTAL</td>
                    <td class="px-4 py-3 text-right font-bold text-green-600 text-lg">Rp
                        {{ number_format($pembelian->total_harga, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="flex justify-end">
            <a href="{{ route('pembelian.index') }}"
                class="px-4 py-2 border border-gray-300 rounded text-gray-600 hover:bg-gray-50 transition">Kembali</a>
        </div>
    </div>
@endsection
