@extends('layouts.app')

@section('title', 'Stok Barang')
@section('header', 'Monitoring Stok Cabang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Stok: {{ Session::get('toko_name') }}</h3>
                <p class="text-sm text-gray-500">Daftar inventaris yang tersedia di cabang ini.</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('stok.opname.create') }}"
                    class="px-3 py-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-clipboard-check mr-1"></i> Stok Opname
                </a>
                <a href="{{ route('stok.transfer.create') }}"
                    class="px-3 py-2 bg-blue-100 text-blue-700 hover:bg-blue-200 rounded-lg text-sm font-medium transition">
                    <i class="fas fa-exchange-alt mr-1"></i> Transfer Stok
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Stok Fisik</th>
                        <th class="px-6 py-4 text-right">Harga Jual (Cabang)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($stoks as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $item->produk->nama_produk }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ $item->produk->sku }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    class="px-2 py-1 bg-gray-100 rounded text-xs">{{ $item->produk->kategori->nama_kategori ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="font-bold text-lg {{ $item->stok_fisik <= 5 ? 'text-red-600' : 'text-gray-800' }}">
                                    {{ $item->stok_fisik }}
                                </span>
                                <span class="text-xs text-gray-500 ml-1">{{ $item->produk->satuan }}</span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-800">
                                Rp {{ number_format($item->harga_jual_cabang, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if ($item->stok_fisik <= 0)
                                    <span
                                        class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs font-bold">Habis</span>
                                @elseif($item->stok_fisik <= 5)
                                    <span
                                        class="px-2 py-1 bg-yellow-100 text-yellow-600 rounded-full text-xs font-bold">Menipis</span>
                                @else
                                    <span
                                        class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs font-bold">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                Belum ada data stok di toko ini. <br>
                                <span class="text-xs">Lakukan Pembelian atau Transfer Stok masuk.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
