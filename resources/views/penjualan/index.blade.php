@extends('layouts.app')

@section('title', 'Riwayat Penjualan')
@section('header', 'Data Transaksi')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Riwayat Transaksi</h3>
            <a href="{{ route('penjualan.create') }}"
                class="px-4 py-2 bg-slate-800 hover:bg-slate-900 text-white rounded-lg text-sm font-medium shadow-sm">
                <i class="fas fa-cash-register mr-2"></i> Buka Kasir
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">No Faktur</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4 text-right">Total</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($penjualans as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono font-bold text-gray-800">{{ $trx->no_faktur }}</td>
                            <td class="px-6 py-4">{{ date('d M Y H:i', strtotime($trx->tgl_transaksi)) }}</td>
                            <td class="px-6 py-4">{{ $trx->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-green-600">
                                Rp {{ number_format($trx->total_netto, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($trx->status_bayar == 'Lunas')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Lunas</span>
                                @else
                                    <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full">Belum
                                        Lunas</span>
                                @endif
                                <div class="text-xs text-gray-400 mt-1">{{ $trx->metode_bayar }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('penjualan.show', $trx->id_penjualan) }}"
                                    class="text-blue-600 hover:underline text-xs font-bold">
                                    <i class="fas fa-print mr-1"></i> Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
