<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIP-SPP (Sistem Informasi Pembayaran SPP)
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'));

// Route yang memerlukan autentikasi
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================================================================
    // CRUD Lembaga — hanya Super Admin (via Policy)
    // ================================================================
    Route::prefix('lembaga')->name('institutions.')->group(function () {
        Route::get('/',          [InstitutionController::class, 'index'])    ->name('index');
        Route::get('/tambah',    [InstitutionController::class, 'create'])   ->name('create');
        Route::post('/',         [InstitutionController::class, 'store'])    ->name('store');
        Route::get('/{institution}',       [InstitutionController::class, 'show'])   ->name('show');
        Route::get('/{institution}/ubah',  [InstitutionController::class, 'edit'])   ->name('edit');
        Route::put('/{institution}',       [InstitutionController::class, 'update']) ->name('update');
        Route::delete('/{institution}',    [InstitutionController::class, 'destroy'])->name('destroy');

        // Aksi khusus
        Route::patch('/{institution}/toggle-aktif', [InstitutionController::class, 'toggleAktif'])->name('toggleAktif');
        Route::delete('/{institution}/logo',        [InstitutionController::class, 'hapusLogo'])  ->name('hapusLogo');
        Route::patch('/{institution}/pulihkan',     [InstitutionController::class, 'pulihkan'])   ->name('pulihkan');
    });

    // Profil Pengguna
    Route::get('/profil',   [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profil',[ProfileController::class, 'destroy'])->name('profile.destroy');

    // TODO: Modul Data Siswa
    // TODO: Modul Tagihan SPP
    // TODO: Modul Pembayaran
    // TODO: Modul Kas
    // TODO: Modul Laporan
});

require __DIR__.'/auth.php';
