@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Overview Bisnis')

@section('content')
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-green-500 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Penjualan Hari Ini</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp 0</h3>
                    <p class="text-xs text-green-600 mt-1 flex items-center">
                        <i class="fas fa-arrow-up mr-1"></i> 0% dari kemarin
                    </p>
                </div>
                <div class="p-3 bg-green-50 rounded-full text-green-600">
                    <i class="fas fa-cash-register text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-yellow-500 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Total Piutang</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">Rp 0</h3>
                    <p class="text-xs text-gray-500 mt-1">Belum dibayar pelanggan</p>
                </div>
                <div class="p-3 bg-yellow-50 rounded-full text-yellow-600">
                    <i class="fas fa-file-invoice-dollar text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-red-500 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Stok Kritis</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
                    <p class="text-xs text-red-500 mt-1">Produk perlu restock</p>
                </div>
                <div class="p-3 bg-red-50 rounded-full text-red-600">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-5 border-l-4 border-blue-500 hover:shadow-md transition">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-semibold text-gray-400 uppercase tracking-wide">Pelanggan</p>
                    <h3 class="text-2xl font-bold text-gray-800 mt-1">0</h3>
                    <p class="text-xs text-gray-500 mt-1">Total data pelanggan</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-full text-blue-600">
                    <i class="fas fa-users text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <div class="flex justify-between items-center mb-6">
                <h4 class="font-bold text-gray-800 text-lg">Tren Penjualan</h4>
                <select
                    class="text-sm border-gray-300 rounded-md shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                    <option>Minggu Ini</option>
                    <option>Bulan Ini</option>
                </select>
            </div>
            <div class="h-64 bg-gray-50 rounded-lg flex items-center justify-center border-2 border-dashed border-gray-200">
                <p class="text-gray-400 text-sm">Grafik Penjualan akan muncul di sini</p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h4 class="font-bold text-gray-800 text-lg mb-4">Perlu Perhatian</h4>
            <div class="space-y-4">
                <div class="text-center py-8">
                    <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 mb-3">
                        <i class="fas fa-check text-green-600"></i>
                    </div>
                    <p class="text-sm text-gray-500">Semua operasional berjalan lancar.</p>
                </div>

                {{-- 
            <div class="flex items-center p-3 bg-red-50 rounded-lg">
                <div class="flex-shrink-0 text-red-500"><i class="fas fa-exclamation-circle"></i></div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-800">Pupuk Urea 5kg</p>
                    <p class="text-xs text-red-600">Sisa stok: 2 sak</p>
                </div>
            </div> 
            --}}
            </div>
        </div>
    </div>
@endsection
