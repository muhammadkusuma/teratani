@extends('layouts.app')

@section('title', 'Data Produk')
@section('header', 'Master Produk')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Katalog Produk</h3>
                <p class="text-sm text-gray-500">Database seluruh barang dagangan Anda.</p>
            </div>
            <div class="flex space-x-2">
                <input type="text" placeholder="Cari nama / SKU..."
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500">

                <a href="{{ route('produk.create') }}"
                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                    <i class="fas fa-box-open mr-2"></i> Tambah Produk
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">SKU / Kode</th>
                        <th class="px-6 py-4">Nama Produk</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-right">Harga Pokok (HPP)</th>
                        <th class="px-6 py-4 text-center">Satuan</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($produks as $produk)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $produk->sku }}</td>
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $produk->nama_produk }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-gray-100 rounded text-xs text-gray-600">
                                    {{ $produk->kategori->nama_kategori ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-gray-700">
                                Rp {{ number_format($produk->harga_pokok, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">{{ $produk->satuan }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('produk.edit', $produk->id_produk) }}"
                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('produk.destroy', $produk->id_produk) }}" method="POST"
                                        onsubmit="return confirm('Hapus produk ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100" title="Hapus">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-300 mb-2"><i class="fas fa-box text-4xl"></i></div>
                                <p class="text-gray-500">Belum ada produk yang ditambahkan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100">
            {{-- {{ $produks->links() }} --}}
        </div>
    </div>
@endsection
