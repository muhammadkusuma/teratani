@extends('layouts.app')

@section('title', 'Buku Piutang')
@section('header', 'Daftar Piutang Pelanggan')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Rekap Hutang Pelanggan</h3>
                <p class="text-sm text-gray-500">Daftar pelanggan yang memiliki tagihan belum lunas.</p>
            </div>
            <div class="w-64">
                <input type="text" placeholder="Cari nama pelanggan..."
                    class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Pelanggan</th>
                        <th class="px-6 py-4">Wilayah</th>
                        <th class="px-6 py-4 text-right">Total Hutang</th>
                        <th class="px-6 py-4 text-right">Sisa Limit</th>
                        <th class="px-6 py-4 text-center">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($piutangs as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $p->nama_pelanggan }}</div>
                                <div class="text-xs text-gray-500">{{ $p->no_telp ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4">{{ $p->wilayah ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-bold text-red-600 text-base">
                                Rp {{ number_format($p->sisa_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right text-gray-500">
                                @php $sisaLimit = $p->limit_piutang - $p->sisa_piutang; @endphp
                                Rp {{ number_format($sisaLimit, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('piutang.show', $p->id_pelanggan) }}"
                                    class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold hover:bg-blue-100 transition">
                                    Kartu Piutang
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Tidak ada data piutang (Semua
                                lunas).</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
