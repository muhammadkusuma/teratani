@extends('layouts.app')

@section('title', 'Tambah User')
@section('header', 'Tambah Staff Baru')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">Formulir Staff Baru</h2>
                <p class="text-sm text-gray-500">Buat akun untuk karyawan Anda agar dapat mengakses sistem.</p>
            </div>

            <form action="{{ route('users.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="Contoh: Budi Santoso">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="username_staff">
                        <p class="text-xs text-gray-400 mt-1">Digunakan untuk login.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan (Role)</label>
                        <select name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                            <option value="KASIR">KASIR</option>
                            <option value="MANAGER">MANAGER</option>
                        </select>
                        <p class="text-xs text-gray-400 mt-1">Manager memiliki akses lebih luas daripada Kasir.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="********">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ulangi Password</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                            placeholder="********">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-sm transition">
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
