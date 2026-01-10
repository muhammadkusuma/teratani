<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\LaporanPenjualanController;
use App\Http\Controllers\LaporanStokController;
use App\Http\Controllers\PembayaranPiutangController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\PenjualanController;
use App\Http\Controllers\PiutangController;
use App\Http\Controllers\ProdukController;
use App\Http\Controllers\SatuanController;
use App\Http\Controllers\StokController;
use App\Http\Controllers\SuperAdminController;
use App\Http\Controllers\TenantSwitchController;
use App\Http\Controllers\TokoSwitchController;
use Illuminate\Support\Facades\Route;

// Import controller lain sesuai kebutuhan...

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ====================================================
// 1. PUBLIC ROUTES (Halaman Depan & Login)
// ====================================================

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ====================================================
// 2. AUTHENTICATED ROUTES
// ====================================================

Route::middleware(['auth'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminController::class, 'index'])
        ->name('dashboard.superadmin');

    // --- PILIH TENANT (Jika user punya banyak bisnis) ---
    // Pastikan TenantSwitchController memiliki method 'index' dan 'switch'
    Route::get('/tenant/select', [TenantSwitchController::class, 'index'])->name('tenant.select');
    Route::post('/tenant/select', [TenantSwitchController::class, 'switch'])->name('tenant.switch');

    // --- PILIH TOKO (Switch Toko dalam Tenant yang sama) ---
    Route::get('/toko/select', [TokoSwitchController::class, 'index'])->name('toko.select');
    Route::post('/toko/switch', [TokoSwitchController::class, 'switch'])->name('toko.switch');

    // ====================================================
    // 3. TENANT SCOPED ROUTES (Butuh Session Tenant & Toko)
    // ====================================================
    // Middleware 'tenant' akan memastikan user sudah memilih tenant & toko

    Route::middleware(['tenant'])->group(function () {

        // --- DASHBOARD ---
        Route::get('/dashboard', function () {
            // Bisa diarahkan ke view dashboard atau controller
            return view('dashboard');
        })->name('dashboard');

        // --- PEMBAYARAN PIUTANG (YANG ANDA MINTA) ---
        // Resource route mencakup: index, store, show, update, destroy
        Route::resource('pembayaran-piutang', PembayaranPiutangController::class);

        // --- MODUL UTAMA LAINNYA ---
        // (Aktifkan/uncomment saat Anda siap menggunakan fiturnya)

        // Penjualan & Piutang
        Route::resource('penjualan', PenjualanController::class);
        Route::resource('piutang', PiutangController::class);

        // Stok & Produk
        Route::resource('stok', StokController::class);
        Route::resource('produk', ProdukController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('satuan', SatuanController::class);

        // Keuangan & Laporan
        Route::resource('pengeluaran', PengeluaranController::class);
        Route::get('laporan/penjualan', [LaporanPenjualanController::class, 'index'])->name('laporan.penjualan');
        Route::get('laporan/stok', [LaporanStokController::class, 'index'])->name('laporan.stok');

    });
});
