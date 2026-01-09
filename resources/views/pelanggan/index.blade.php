@extends('layouts.app')

@section('title', 'Data Pelanggan')
@section('header', 'Database Pelanggan')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-800 text-lg">Daftar Pelanggan</h3>
            <a href="{{ route('pelanggan.create') }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                <i class="fas fa-user-plus mr-2"></i> Tambah Pelanggan
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Nama Pelanggan</th>
                        <th class="px-6 py-4">Wilayah</th>
                        <th class="px-6 py-4 text-right">Limit Piutang</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pelanggans as $p)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-bold text-gray-800">{{ $p->nama_pelanggan }}</td>
                            <td class="px-6 py-4">{{ $p->wilayah ?? '-' }}</td>
                            <td class="px-6 py-4 text-right font-mono text-gray-700">
                                Rp {{ number_format($p->limit_piutang, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('pelanggan.edit', $p->id_pelanggan) }}"
                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('pelanggan.destroy', $p->id_pelanggan) }}" method="POST"
                                        onsubmit="return confirm('Hapus pelanggan ini?');">
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
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data pelanggan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
