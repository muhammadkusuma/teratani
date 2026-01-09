@extends('layouts.app')

@section('title', 'Kartu Piutang')
@section('header', 'Rincian Piutang')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 md:col-span-2">
            <h3 class="font-bold text-gray-800 text-lg mb-4">{{ $pelanggan->nama_pelanggan }}</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-gray-500 block">Wilayah / Alamat:</span>
                    <span class="font-medium">{{ $pelanggan->wilayah ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-500 block">Kontak:</span>
                    <span class="font-medium">{{ $pelanggan->no_telp ?? '-' }}</span>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-pink-600 rounded-xl shadow-lg text-white p-6">
            <p class="text-red-100 text-sm font-medium mb-1">Total Sisa Hutang</p>
            <h2 class="text-3xl font-bold">Rp {{ number_format($pelanggan->sisa_piutang, 0, ',', '.') }}</h2>
            <div class="mt-4 pt-4 border-t border-white/20">
                <a href="{{ route('pembayaran-piutang.create', ['id_pelanggan' => $pelanggan->id_pelanggan]) }}"
                    class="block w-full text-center bg-white text-red-600 font-bold py-2 rounded-lg hover:bg-red-50 transition">
                    <i class="fas fa-wallet mr-2"></i> Bayar Cicilan
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-bold text-gray-800 mb-4">Kartu Riwayat (Mutasi Piutang)</h4>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-4 py-3">Tanggal</th>
                        <th class="px-4 py-3">Ref (Faktur)</th>
                        <th class="px-4 py-3">Keterangan</th>
                        <th class="px-4 py-3 text-right text-red-600">Debet (+)</th>
                        <th class="px-4 py-3 text-right text-green-600">Kredit (-)</th>
                        <th class="px-4 py-3 text-right">Saldo Akhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php $saldo = 0; @endphp
                    @forelse($histori as $row)
                        @php
                            // Logika saldo berjalan
                            if ($row->jenis_mutasi == 'DEBET') {
                                // Hutang bertambah
                                $saldo += $row->nominal;
                            } else {
                                // Bayar hutang
                                $saldo -= $row->nominal;
                            }
                        @endphp
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">{{ date('d/m/Y', strtotime($row->created_at)) }}</td>
                            <td class="px-4 py-3 font-mono text-xs">
                                @if ($row->penjualan)
                                    <a href="{{ route('penjualan.show', $row->penjualan->id_penjualan) }}"
                                        class="text-blue-500 hover:underline">
                                        {{ $row->penjualan->no_faktur }}
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3">{{ $row->keterangan }}</td>
                            <td class="px-4 py-3 text-right font-medium text-red-600">
                                @if ($row->jenis_mutasi == 'DEBET')
                                    Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-green-600">
                                @if ($row->jenis_mutasi == 'KREDIT')
                                    Rp {{ number_format($row->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-bold text-gray-800">
                                Rp {{ number_format($saldo, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
