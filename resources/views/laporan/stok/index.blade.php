@extends('layouts.app')

@section('title', 'Laporan Mutasi Stok')
@section('header', 'Pergerakan Barang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

        <form action="{{ route('laporan.stok') }}" method="GET"
            class="bg-gray-50 p-4 rounded-xl border border-gray-200 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Pilih Produk</label>
                    <select name="id_produk"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        <option value="">-- Semua Produk --</option>
                        @foreach ($produks as $p)
                            <option value="{{ $p->id_produk }}"
                                {{ request('id_produk') == $p->id_produk ? 'selected' : '' }}>
                                {{ $p->nama_produk }} ({{ $p->sku }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Periode</label>
                    <input type="date" name="tanggal" value="{{ request('tanggal', date('Y-m-d')) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <button type="submit"
                        class="w-full px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-search mr-2"></i> Cari Data
                    </button>
                </div>
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-3">Waktu</th>
                        <th class="px-6 py-3">Produk</th>
                        <th class="px-6 py-3 text-center">Jenis Mutasi</th>
                        <th class="px-6 py-3 text-center">Jumlah</th>
                        <th class="px-6 py-3 text-center">Stok Akhir</th>
                        <th class="px-6 py-3">Keterangan / Ref</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">{{ date('d/m/Y H:i', strtotime($log->created_at)) }}</td>
                            <td class="px-6 py-3 font-medium text-gray-800">{{ $log->produk->nama_produk ?? '-' }}</td>
                            <td class="px-6 py-3 text-center">
                                @if ($log->jenis_mutasi == 'Masuk')
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-700 rounded text-xs font-bold">MASUK</span>
                                @elseif($log->jenis_mutasi == 'Keluar')
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded text-xs font-bold">KELUAR</span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-bold">ADJUST</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-center font-mono font-bold">
                                {{ $log->jumlah }}
                            </td>
                            <td class="px-6 py-3 text-center font-mono text-gray-500">
                                {{ $log->stok_akhir }}
                            </td>
                            <td class="px-6 py-3 text-xs italic">{{ $log->keterangan }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada pergerakan stok pada
                                kriteria ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
