<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - SIP-SPP (Sistem Informasi Pembayaran SPP)
|--------------------------------------------------------------------------
*/

// Redirect root ke dashboard (atau login jika belum auth)
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Route yang memerlukan autentikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil Pengguna (dari Breeze)
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // TODO: Tambahkan route modul SPP di sini
    // Route::prefix('siswa')->name('siswa.')->middleware('tenant')->group(...)
    // Route::prefix('tagihan')->name('tagihan.')->middleware('tenant')->group(...)
    // Route::prefix('pembayaran')->name('pembayaran.')->middleware('tenant')->group(...)
    // Route::prefix('laporan')->name('laporan.')->middleware('tenant')->group(...)
});

require __DIR__.'/auth.php';
