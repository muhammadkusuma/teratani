@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('header', 'Laba Rugi (Profit & Loss)')

@section('content')
    <div class="max-w-4xl mx-auto">

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <form action="{{ route('laporan.keuangan') }}" method="GET"
                class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-gray-800 text-lg">Periode Laporan</h3>
                    <p class="text-sm text-gray-500">Pilih bulan dan tahun untuk melihat kinerja keuangan.</p>
                </div>
                <div class="flex gap-2">
                    <select name="bulan" class="px-4 py-2 border border-gray-300 rounded-lg">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('bulan', date('n')) == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 10)) }}
                            </option>
                        @endfor
                    </select>
                    <select name="tahun" class="px-4 py-2 border border-gray-300 rounded-lg">
                        @for ($y = date('Y'); $y >= date('Y') - 2; $y--)
                            <option value="{{ $y }}" {{ request('tahun', date('Y')) == $y ? 'selected' : '' }}>
                                {{ $y }}</option>
                        @endfor
                    </select>
                    <button type="submit"
                        class="px-4 py-2 bg-slate-800 text-white rounded-lg hover:bg-slate-900 font-bold">
                        Lihat
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">
            <div class="p-6 bg-slate-50 border-b border-gray-200 text-center">
                <h2 class="text-xl font-bold text-gray-800 uppercase tracking-widest">{{ Session::get('tenant_name') }}</h2>
                <p class="text-sm text-gray-500 font-bold">LAPORAN LABA RUGI</p>
                <p class="text-xs text-gray-400 mt-1">Periode:
                    {{ date('F', mktime(0, 0, 0, request('bulan', date('n')), 10)) }} {{ request('tahun', date('Y')) }}</p>
            </div>

            <div class="p-8">
                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 border-b border-gray-300 pb-2 mb-3 uppercase text-sm">1. Pendapatan
                        Usaha</h4>
                    <div class="flex justify-between items-center mb-2 px-4">
                        <span class="text-gray-600">Total Penjualan (Omset)</span>
                        <span class="font-bold text-gray-800">Rp
                            {{ number_format($totalPenjualan ?? 0, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 border-b border-gray-300 pb-2 mb-3 uppercase text-sm">2. Harga Pokok
                        Penjualan (HPP)</h4>
                    <div class="flex justify-between items-center mb-2 px-4">
                        <span class="text-gray-600">Total HPP Barang Terjual</span>
                        <span class="text-gray-800">( Rp {{ number_format($totalHPP ?? 0, 0, ',', '.') }} )</span>
                    </div>
                </div>

                <div class="flex justify-between items-center bg-gray-100 p-4 rounded-lg mb-8 border border-gray-200">
                    <span class="font-bold text-gray-800 uppercase">Laba Kotor</span>
                    @php $labaKotor = ($totalPenjualan ?? 0) - ($totalHPP ?? 0); @endphp
                    <span class="font-bold text-xl {{ $labaKotor >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        Rp {{ number_format($labaKotor, 0, ',', '.') }}
                    </span>
                </div>

                <div class="mb-6">
                    <h4 class="font-bold text-gray-700 border-b border-gray-300 pb-2 mb-3 uppercase text-sm">3. Biaya
                        Operasional</h4>

                    @forelse($listPengeluaran as $biaya)
                        <div class="flex justify-between items-center mb-2 px-4 text-sm">
                            <span class="text-gray-600">{{ $biaya->kategori_biaya }}</span>
                            <span class="text-gray-800">Rp {{ number_format($biaya->total, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="px-4 text-sm text-gray-400 italic">Tidak ada pengeluaran tercatat.</div>
                    @endforelse

                    <div class="flex justify-between items-center mt-3 pt-3 border-t border-dashed border-gray-300 px-4">
                        <span class="font-bold text-gray-600">Total Biaya</span>
                        <span class="font-bold text-red-600">( Rp {{ number_format($totalPengeluaran ?? 0, 0, ',', '.') }}
                            )</span>
                    </div>
                </div>

                <div class="flex justify-between items-center bg-slate-800 text-white p-5 rounded-xl shadow-lg mt-8">
                    <div>
                        <span class="block text-sm opacity-75 uppercase tracking-wider">Laba Bersih (Net Profit)</span>
                        <span class="text-xs opacity-50">Laba Kotor - Biaya Operasional</span>
                    </div>
                    @php $labaBersih = $labaKotor - ($totalPengeluaran ?? 0); @endphp
                    <span class="font-bold text-3xl {{ $labaBersih >= 0 ? 'text-green-400' : 'text-red-400' }}">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="bg-gray-50 p-4 text-center border-t border-gray-200 no-print">
                <button onclick="window.print()"
                    class="text-gray-500 hover:text-gray-800 font-bold text-sm flex items-center justify-center w-full">
                    <i class="fas fa-print mr-2"></i> Cetak Laporan
                </button>
            </div>
        </div>
    </div>

    <style>
        @media print {

            .no-print,
            header,
            nav,
            aside,
            form {
                display: none !important;
            }

            body {
                background: white;
                -webkit-print-color-adjust: exact;
            }

            .max-w-4xl {
                max-width: 100%;
                margin: 0;
                padding: 0;
            }

            .shadow-lg {
                shadow: none;
            }
        }
    </style>
@endsection
