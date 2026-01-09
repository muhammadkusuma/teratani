@extends('layouts.app')

@section('title', 'Detail Transaksi')

@section('content')
    <div class="max-w-md mx-auto bg-white p-8 shadow-lg border border-gray-200 mt-10">
        <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
            <h2 class="font-bold text-xl uppercase">{{ Session::get('tenant_name') }}</h2>
            <h3 class="font-bold text-lg text-gray-600">{{ Session::get('toko_name') }}</h3>
            <p class="text-xs text-gray-500 mt-1">
                {{ $penjualan->toko->alamat ?? '' }}<br>
                Telp: {{ $penjualan->toko->no_telp ?? '-' }}
            </p>
        </div>

        <div class="flex justify-between text-xs text-gray-600 mb-4">
            <div>
                <p>No: {{ $penjualan->no_faktur }}</p>
                <p>Tgl: {{ date('d/m/Y H:i', strtotime($penjualan->tgl_transaksi)) }}</p>
            </div>
            <div class="text-right">
                <p>Kasir: {{ $penjualan->user->username ?? 'Admin' }}</p>
                <p>Plg: {{ $penjualan->pelanggan->nama_pelanggan ?? 'Umum' }}</p>
            </div>
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="border-b border-gray-300">
                <tr class="text-left">
                    <th class="py-1">Item</th>
                    <th class="py-1 text-center">Qty</th>
                    <th class="py-1 text-right">Harga</th>
                    <th class="py-1 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($penjualan->details as $item)
                    <tr>
                        <td class="py-1">{{ $item->produk->nama_produk }}</td>
                        <td class="py-1 text-center">{{ $item->qty }}</td>
                        <td class="py-1 text-right">{{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                        <td class="py-1 text-right">{{ number_format($item->harga_satuan * $item->qty, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-dashed border-gray-300 pt-2 mb-6">
            <div class="flex justify-between font-bold text-lg">
                <span>Total</span>
                <span>Rp {{ number_format($penjualan->total_netto, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between text-sm text-gray-600 mt-1">
                <span>Pembayaran</span>
                <span>{{ $penjualan->metode_bayar }}</span>
            </div>
        </div>

        <div class="text-center text-xs text-gray-500 mb-6">
            <p>Terima kasih atas kunjungan Anda.</p>
            <p>Barang yang sudah dibeli tidak dapat ditukar/dikembalikan.</p>
        </div>

        <div class="flex flex-col gap-2 no-print">
            <button onclick="window.print()"
                class="w-full py-2 bg-slate-800 text-white rounded font-bold hover:bg-slate-900">
                <i class="fas fa-print mr-2"></i> Cetak Struk
            </button>
            <a href="{{ route('penjualan.index') }}"
                class="w-full py-2 bg-gray-200 text-gray-700 rounded font-bold text-center hover:bg-gray-300">
                Kembali
            </a>
        </div>
    </div>

    <style>
        @media print {

            .no-print,
            nav,
            header,
            aside {
                display: none !important;
            }

            body {
                background: white;
            }

            .max-w-md {
                max-width: 100%;
                box-shadow: none;
                border: none;
                margin: 0;
                padding: 0;
            }
        }
    </style>
@endsection
