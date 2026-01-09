@extends('layouts.app')

@section('title', 'Tambah Distributor')
@section('header', 'Tambah Distributor Baru')

@section('content')
    <div class="max-w-2xl mx-auto bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">Formulir Distributor</h2>
        </div>

        <form action="{{ route('distributor.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Distributor</label>
                    <input type="text" name="nama_distributor" required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="no_telp"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email (Opsional)</label>
                        <input type="email" name="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap</label>
                    <textarea name="alamat" rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"></textarea>
                </div>
            </div>

            <div class="flex justify-end space-x-3 mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('distributor.index') }}"
                    class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200">Batal</a>
                <button type="submit"
                    class="px-6 py-2 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700">Simpan Data</button>
            </div>
        </form>
    </div>
@endsection
