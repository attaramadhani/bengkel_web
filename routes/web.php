<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\BarangController;
use App\Http\Controllers\JasaController;
use App\Http\Controllers\TransaksiController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (semua user login)
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('transaksi', TransaksiController::class);

    // Admin Only - Kelola Master Data & Laporan
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('barang', BarangController::class);
        Route::resource('jasa', JasaController::class);
        Route::resource('user', UserController::class);
        Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    });
});

// Midtrans Callback (No Auth, No CSRF)
Route::post('/midtrans/callback', [TransaksiController::class, 'midtransCallback'])->name('midtrans.callback');
