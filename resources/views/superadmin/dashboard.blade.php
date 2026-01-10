@extends('layouts.app')

@section('title', 'Superadmin Dashboard')
@section('header', 'Panel Kontrol Utama')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-indigo-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Tenant</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalTenants }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Bisnis terdaftar</p>
                </div>
                <div class="p-3 bg-indigo-50 rounded-full text-indigo-600">
                    <i class="fas fa-building text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total User</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">{{ $totalUsers }}</h3>
                    <p class="text-xs text-gray-500 mt-1">Akun pengguna sistem</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Status Sistem</p>
                    <h3 class="text-lg font-bold text-green-600 mt-1">Operational</h3>
                    <p class="text-xs text-gray-500 mt-1">Server berjalan normal</p>
                </div>
                <div class="p-3 bg-green-50 rounded-full text-green-600">
                    <i class="fas fa-server text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h4 class="font-bold text-gray-800 text-lg mb-4">Aksi Cepat</h4>
        <div class="flex gap-4">
            <a href="#" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">
                <i class="fas fa-plus mr-2"></i> Tambah Tenant Baru
            </a>
            <a href="#" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
                <i class="fas fa-cog mr-2"></i> Pengaturan Global
            </a>
        </div>
    </div>
@endsection
