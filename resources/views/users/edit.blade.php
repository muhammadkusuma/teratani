@extends('layouts.app')

@section('title', 'Edit User')
@section('header', 'Edit Profil Staff')

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">

            <div class="mb-6 pb-4 border-b border-gray-100">
                <h2 class="text-lg font-bold text-gray-800">Edit Data: {{ $user->nama_lengkap }}</h2>
            </div>

            <form action="{{ route('users.update', $user->id_user) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="nama_lengkap" value="{{ old('nama_lengkap', $user->nama_lengkap) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan (Role)</label>
                        <select name="role" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition">
                            <option value="KASIR" {{ $user->role == 'KASIR' ? 'selected' : '' }}>KASIR</option>
                            <option value="MANAGER" {{ $user->role == 'MANAGER' ? 'selected' : '' }}>MANAGER</option>
                            @if ($user->role == 'OWNER')
                                <option value="OWNER" selected>OWNER</option>
                            @endif
                        </select>
                    </div>

                    <div class="col-span-2">
                        <label
                            class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:bg-gray-50">
                            <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                                class="form-checkbox h-5 w-5 text-green-600 rounded focus:ring-green-500">
                            <div>
                                <span class="text-sm font-medium text-gray-700 block">Akun Aktif</span>
                                <span class="text-xs text-gray-500">Jika dimatikan, user tidak akan bisa login.</span>
                            </div>
                        </label>
                    </div>

                    <div class="col-span-2 pt-4 border-t border-gray-100">
                        <h4 class="text-sm font-bold text-gray-800 mb-3">Ganti Password (Opsional)</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Password Baru</label>
                                <input type="password" name="password"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                                    placeholder="Kosongkan jika tidak diubah">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-500 mb-1">Ulangi Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 transition"
                                    placeholder="Ulangi password baru">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}"
                        class="px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-lg shadow-sm transition">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
