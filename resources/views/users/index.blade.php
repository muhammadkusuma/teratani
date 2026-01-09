@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('header', 'Daftar Staff & Karyawan')

@section('content')
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Data Pengguna</h3>
                <p class="text-sm text-gray-500">Kelola akses staff untuk bisnis {{ Session::get('tenant_name') }}</p>
            </div>
            <a href="{{ route('users.create') }}"
                class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition shadow-sm flex items-center">
                <i class="fas fa-user-plus mr-2"></i> Tambah Staff
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-gray-700 font-semibold uppercase tracking-wider text-xs">
                    <tr>
                        <th class="px-6 py-4">Nama Lengkap</th>
                        <th class="px-6 py-4">Username</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 font-medium text-gray-800">
                                {{ $user->nama_lengkap }}
                                @if ($user->id_user == Session::get('user_id'))
                                    <span class="ml-2 text-xs text-blue-500 bg-blue-50 px-2 py-0.5 rounded">(Saya)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">{{ $user->username }}</td>
                            <td class="px-6 py-4">
                                @php
                                    $roleColor = match ($user->role) {
                                        'OWNER' => 'bg-purple-100 text-purple-700 border-purple-200',
                                        'MANAGER' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'KASIR' => 'bg-green-100 text-green-700 border-green-200',
                                        default => 'bg-gray-100 text-gray-700 border-gray-200',
                                    };
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-semibold border {{ $roleColor }}">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                @if ($user->is_active)
                                    <span class="inline-flex items-center text-green-600">
                                        <span class="h-2 w-2 bg-green-500 rounded-full mr-2"></span> Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-red-500">
                                        <span class="h-2 w-2 bg-red-500 rounded-full mr-2"></span> Non-Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center space-x-2">
                                    @if ($user->role !== 'OWNER')
                                        <a href="{{ route('users.access', $user->id_user) }}"
                                            class="p-2 bg-yellow-50 text-yellow-600 rounded-lg hover:bg-yellow-100 transition"
                                            title="Hak Akses Toko">
                                            <i class="fas fa-store-alt"></i>
                                        </a>
                                    @endif

                                    <a href="{{ route('users.edit', $user->id_user) }}"
                                        class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 transition"
                                        title="Edit Profil">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if ($user->id_user != Session::get('user_id'))
                                        <form action="{{ route('users.destroy', $user->id_user) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition"
                                                title="Hapus User">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada staff yang ditambahkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
