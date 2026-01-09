@extends('layouts.app')

@section('title', 'Edit Toko')
@section('header', 'Update Informasi Cabang')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Edit: {{ $toko->nama_toko }}</h2>
        </div>

        <form action="{{ route('toko.update', $toko->id_toko) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Cabang / Toko</label>
                    <input type="text" name="nama_toko" value="{{ old('nama_toko', $toko->nama_toko) }}" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp', $toko->no_telp) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">{{ old('alamat', $toko->alamat) }}</textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('toko.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Update Data</button>
            </div>
        </form>
    </div>
@endsection
