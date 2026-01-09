@extends('layouts.app')

@section('title', 'Riwayat Pembelian')
@section('header', 'Pembelian Stok')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Riwayat Belanja</h3>
            <a href="{{ route('pembelian.create') }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                <i class="fas fa-cart-plus mr-2"></i> Input Pembelian
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">No Faktur</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Distributor</th>
                        <th class="px-6 py-4 text-right">Total Belanja</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pembelians as $beli)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono font-medium">{{ $beli->no_faktur }}</td>
                            <td class="px-6 py-4">{{ date('d M Y', strtotime($beli->tgl_pembelian)) }}</td>
                            <td class="px-6 py-4">{{ $beli->distributor->nama_distributor ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-gray-800">
                                Rp {{ number_format($beli->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($beli->status_bayar == 'Lunas')
                                    <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">Lunas</span>
                                @else
                                    <span class="px-2 py-1 bg-red-100 text-red-700 text-xs rounded-full">Hutang</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('pembelian.show', $beli->id_pembelian) }}"
                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Belum ada data pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
