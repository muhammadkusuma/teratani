@extends('layouts.app')

@section('title', 'Riwayat Stok Opname')
@section('header', 'Riwayat Opname')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Log Penyesuaian Stok</h3>
            <a href="{{ route('stok.opname.create') }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium shadow-sm">
                <i class="fas fa-plus mr-2"></i> Input Opname Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4 text-center">Awal</th>
                        <th class="px-6 py-4 text-center">Akhir</th>
                        <th class="px-6 py-4 text-center">Selisih</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4">Petugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($opnames as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ date('d M Y H:i', strtotime($log->created_at)) }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $log->produk->nama_produk ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-gray-500">{{ $log->stok_awal }}</td>
                            <td class="px-6 py-4 text-center font-bold">{{ $log->stok_akhir }}</td>
                            <td class="px-6 py-4 text-center">
                                @php $selisih = $log->stok_akhir - $log->stok_awal; @endphp
                                @if ($selisih > 0)
                                    <span class="text-green-600 font-bold">+{{ $selisih }}</span>
                                @elseif($selisih < 0)
                                    <span class="text-red-600 font-bold">{{ $selisih }}</span>
                                @else
                                    <span class="text-gray-400">0</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 italic text-gray-500 max-w-xs truncate">{{ $log->keterangan }}</td>
                            <td class="px-6 py-4 text-xs">{{ $log->user->username ?? 'System' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada riwayat opname.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
