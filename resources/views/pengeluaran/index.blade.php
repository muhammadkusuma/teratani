@extends('layouts.app')

@section('title', 'Data Pengeluaran')
@section('header', 'Biaya Operasional')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Catatan Pengeluaran</h3>
                <p class="text-sm text-gray-500">Monitoring biaya operasional toko.</p>
            </div>
            <a href="{{ route('pengeluaran.create') }}"
                class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                <i class="fas fa-minus-circle mr-2"></i> Input Pengeluaran
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Keterangan</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengeluarans as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">{{ date('d M Y', strtotime($item->tgl_pengeluaran)) }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600 font-medium">
                                    {{ $item->kategori_biaya }}
                                </span>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate">{{ $item->keterangan }}</td>
                            <td class="px-6 py-4 text-right font-bold text-red-600">
                                Rp {{ number_format($item->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('pengeluaran.destroy', $item->id_pengeluaran) }}" method="POST"
                                    onsubmit="return confirm('Hapus data pengeluaran ini?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-600 transition"
                                        title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada data pengeluaran.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
