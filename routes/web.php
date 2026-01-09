<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PembayaranPiutangController;
use Illuminate\Support\Facades\Route;

// Route Halaman Depan
Route::get('/', function () {
    return view('welcome');
});

// Route Auth
Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Route yang butuh Login dan Tenant/Toko Session
// Pastikan middleware 'tenant' (TenantScopeMiddleware) sudah didaftarkan di bootstrap/app.php
Route::middleware(['auth', 'tenant'])->group(function () {

    // Route Pembayaran Piutang
    Route::get('/pembayaran-piutang', [PembayaranPiutangController::class, 'index'])->name('pembayaran-piutang.index');
    Route::post('/pembayaran-piutang', [PembayaranPiutangController::class, 'store'])->name('pembayaran-piutang.store');
    Route::get('/pembayaran-piutang/{id}', [PembayaranPiutangController::class, 'show'])->name('pembayaran-piutang.show');
    Route::delete('/pembayaran-piutang/{id}', [PembayaranPiutangController::class, 'destroy'])->name('pembayaran-piutang.destroy');

    // ... tambahkan route controller lain di sini
});
