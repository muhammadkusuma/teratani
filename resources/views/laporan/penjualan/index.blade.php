@extends('layouts.app')

@section('title', 'Laporan Penjualan')
@section('header', 'Laporan Transaksi')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
        <form action="{{ route('laporan.penjualan') }}" method="GET"
            class="flex flex-col md:flex-row gap-4 items-end mb-6 pb-6 border-b border-gray-100">
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date', date('Y-m-01')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            <div class="w-full md:w-auto">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date', date('Y-m-d')) }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            <button type="submit"
                class="px-6 py-2 bg-slate-800 text-white font-bold rounded-lg hover:bg-slate-900 transition shadow-sm">
                <i class="fas fa-filter mr-2"></i> Tampilkan
            </button>
            <a href="{{ route('laporan.penjualan') }}"
                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium">Reset</a>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="p-4 bg-green-50 rounded-xl border border-green-100">
                <p class="text-sm text-green-600 font-medium">Total Omset (Bruto)</p>
                <h3 class="text-2xl font-bold text-green-800 mt-1">Rp {{ number_format($totalOmset ?? 0, 0, ',', '.') }}
                </h3>
            </div>
            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-sm text-blue-600 font-medium">Jumlah Transaksi</p>
                <h3 class="text-2xl font-bold text-blue-800 mt-1">{{ $penjualans->count() }} <span
                        class="text-sm font-normal text-blue-600">Nota</span></h3>
            </div>
            <div class="p-4 bg-yellow-50 rounded-xl border border-yellow-100">
                <p class="text-sm text-yellow-600 font-medium">Rata-rata per Nota</p>
                @php $avg = $penjualans->count() > 0 ? ($totalOmset / $penjualans->count()) : 0; @endphp
                <h3 class="text-2xl font-bold text-yellow-800 mt-1">Rp {{ number_format($avg, 0, ',', '.') }}</h3>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-3">Tanggal</th>
                        <th class="px-6 py-3">No Faktur</th>
                        <th class="px-6 py-3">Pelanggan</th>
                        <th class="px-6 py-3">Metode Bayar</th>
                        <th class="px-6 py-3 text-right">Total Belanja</th>
                        <th class="px-6 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($penjualans as $trx)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-3">{{ date('d/m/Y H:i', strtotime($trx->tgl_transaksi)) }}</td>
                            <td class="px-6 py-3 font-mono text-xs">{{ $trx->no_faktur }}</td>
                            <td class="px-6 py-3">{{ $trx->pelanggan->nama_pelanggan ?? 'Umum' }}</td>
                            <td class="px-6 py-3">
                                <span
                                    class="px-2 py-1 rounded text-xs border {{ $trx->metode_bayar == 'Tunai' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-yellow-50 border-yellow-100 text-yellow-600' }}">
                                    {{ $trx->metode_bayar }}
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-gray-800">
                                Rp {{ number_format($trx->total_netto, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-center">
                                <a href="{{ route('penjualan.show', $trx->id_penjualan) }}"
                                    class="text-blue-600 hover:text-blue-800 text-xs font-bold">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">Tidak ada data penjualan pada
                                periode ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
