@extends('layouts.app')

@section('title', 'Data Cabang Toko')
@section('header', 'Manajemen Toko & Cabang')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Daftar Cabang</h3>
                <p class="text-sm text-gray-500">Kelola lokasi fisik toko/gudang Anda.</p>
            </div>
            <a href="{{ route('toko.create') }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium flex items-center shadow-sm">
                <i class="fas fa-store-alt mr-2"></i> Tambah Toko Baru
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tokos as $toko)
                <div class="bg-white rounded-xl border border-gray-200 p-5 hover:shadow-md transition relative group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-3 bg-green-50 rounded-lg text-green-600 text-xl">
                            <i class="fas fa-store"></i>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('toko.edit', $toko->id_toko) }}"
                                class="text-gray-400 hover:text-blue-500 transition">
                                <i class="fas fa-pen"></i>
                            </a>
                            @if (!$toko->is_pusat)
                                {{-- Jangan hapus toko pusat sembarangan --}}
                                <form action="{{ route('toko.destroy', $toko->id_toko) }}" method="POST"
                                    onsubmit="return confirm('Hapus cabang ini? Data transaksi terkait mungkin ikut terhapus!');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-gray-400 hover:text-red-500 transition">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <h4 class="font-bold text-gray-800 text-lg mb-1">{{ $toko->nama_toko }}</h4>
                    <p class="text-sm text-gray-500 mb-4 h-10 overflow-hidden">{{ $toko->alamat }}</p>

                    <div class="flex items-center text-sm text-gray-600 mb-2">
                        <i class="fas fa-phone-alt w-6 text-center mr-2 text-gray-400"></i> {{ $toko->no_telp ?? '-' }}
                    </div>

                    @if ($toko->is_pusat)
                        <span
                            class="absolute top-4 right-12 bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded font-bold">PUSAT</span>
                    @endif
                </div>
            @empty
                <div class="col-span-3 text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-200">
                    <p class="text-gray-500">Belum ada data toko.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
