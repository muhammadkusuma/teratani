@extends('layouts.app')

@section('title', 'Data Distributor')
@section('header', 'Master Distributor')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Daftar Distributor / Supplier</h3>
                <p class="text-sm text-gray-500">Kelola data pemasok barang toko Anda.</p>
            </div>
            <a href="{{ route('distributor.create') }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                <i class="fas fa-truck mr-2"></i> Tambah Distributor
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Nama Distributor</th>
                        <th class="px-6 py-4">Kontak / Telepon</th>
                        <th class="px-6 py-4">Alamat</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($distributors as $item)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $item->nama_distributor }}</td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span>{{ $item->no_telp }}</span>
                                    <span class="text-xs text-gray-400">{{ $item->email ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 max-w-xs truncate">{{ $item->alamat }}</td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('distributor.edit', $item->id_distributor) }}"
                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('distributor.destroy', $item->id_distributor) }}" method="POST"
                                        onsubmit="return confirm('Hapus data distributor ini?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data distributor.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
