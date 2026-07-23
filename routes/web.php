<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InstitutionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReceiptTemplateController;
use App\Http\Controllers\SchoolYearController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TenantSwitchController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — SIP-SPP (Sistem Informasi Pembayaran SPP)
|--------------------------------------------------------------------------
*/

Route::get('/', fn () => redirect()->route('dashboard'));

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ================================================================
    // Super Admin: Switch Lembaga Aktif
    // ================================================================
    Route::get('/pilih-lembaga',         [TenantSwitchController::class, 'index']) ->name('tenant.switch.index');
    Route::post('/pilih-lembaga/clear',  [TenantSwitchController::class, 'clear']) ->name('tenant.switch.clear');
    Route::post('/pilih-lembaga/{id}',   [TenantSwitchController::class, 'switch'])->name('tenant.switch');

    // ================================================================
    // CRUD Lembaga — hanya Super Admin (via Policy)
    // ================================================================
    Route::prefix('lembaga')->name('institutions.')->group(function () {
        Route::get('/',              [InstitutionController::class, 'index'])     ->name('index');
        Route::get('/tambah',        [InstitutionController::class, 'create'])    ->name('create');
        Route::post('/',             [InstitutionController::class, 'store'])     ->name('store');
        Route::get('/{institution}',      [InstitutionController::class, 'show'])    ->name('show');
        Route::get('/{institution}/ubah', [InstitutionController::class, 'edit'])    ->name('edit');
        Route::put('/{institution}',      [InstitutionController::class, 'update'])  ->name('update');
        Route::delete('/{institution}',   [InstitutionController::class, 'destroy']) ->name('destroy');

        // Aksi khusus — institution bisa trashed untuk pulihkan
        Route::patch('/{institution}/toggle-aktif', [InstitutionController::class, 'toggleAktif'])->name('toggleAktif');
        Route::delete('/{institution}/logo',        [InstitutionController::class, 'hapusLogo'])  ->name('hapusLogo');
        // Gunakan ID biasa untuk pulihkan (karena withTrashed di controller)
        Route::patch('/pulihkan/{id}',              [InstitutionController::class, 'pulihkan'])   ->name('pulihkan');
    });

    // ================================================================
    // MODUL DATA MASTER (Memerlukan active_tenant)
    // ================================================================

    // Tahun Ajaran
    Route::prefix('tahun-ajaran')->name('school-years.')->group(function () {
        Route::get('/',                         [SchoolYearController::class, 'index'])    ->name('index');
        Route::get('/tambah',                   [SchoolYearController::class, 'create'])   ->name('create');
        Route::post('/',                        [SchoolYearController::class, 'store'])    ->name('store');
        Route::get('/{schoolYear}/ubah',        [SchoolYearController::class, 'edit'])     ->name('edit');
        Route::put('/{schoolYear}',             [SchoolYearController::class, 'update'])   ->name('update');
        Route::delete('/{schoolYear}',          [SchoolYearController::class, 'destroy'])  ->name('destroy');
        Route::patch('/{schoolYear}/set-aktif', [SchoolYearController::class, 'setAktif'])->name('setAktif');
    });

    // Kelas
    Route::prefix('kelas')->name('classes.')->group(function () {
        Route::get('/',              [ClassController::class, 'index'])   ->name('index');
        Route::get('/tambah',        [ClassController::class, 'create'])  ->name('create');
        Route::post('/',             [ClassController::class, 'store'])   ->name('store');
        Route::get('/{class}/ubah',  [ClassController::class, 'edit'])    ->name('edit');
        Route::put('/{class}',       [ClassController::class, 'update'])  ->name('update');
        Route::delete('/{class}',    [ClassController::class, 'destroy']) ->name('destroy');
    });

    // Siswa
    Route::prefix('siswa')->name('students.')->group(function () {
        Route::get('/',                         [StudentController::class, 'index'])         ->name('index');
        Route::get('/tambah',                   [StudentController::class, 'create'])        ->name('create');
        Route::post('/',                        [StudentController::class, 'store'])         ->name('store');
        Route::get('/export',                   [StudentController::class, 'export'])        ->name('export');
        Route::get('/template',                 [StudentController::class, 'template'])      ->name('template');
        Route::get('/import',                   [StudentController::class, 'importForm'])    ->name('import.form');
        Route::post('/import/preview',          [StudentController::class, 'importPreview']) ->name('import.preview');
        Route::post('/import',                  [StudentController::class, 'importProcess']) ->name('import.process');
        Route::delete('/bulk-delete',           [StudentController::class, 'bulkDestroy'])   ->name('bulkDestroy');
        Route::get('/{student}',                [StudentController::class, 'show'])          ->name('show');
        Route::get('/{student}/ubah',           [StudentController::class, 'edit'])          ->name('edit');
        Route::put('/{student}',                [StudentController::class, 'update'])        ->name('update');
        Route::delete('/{student}',             [StudentController::class, 'destroy'])       ->name('destroy');
    });

    // Jenis Pembayaran
    Route::prefix('jenis-pembayaran')->name('payment-types.')->group(function () {
        Route::get('/',                   [PaymentTypeController::class, 'index'])       ->name('index');
        Route::get('/tambah',             [PaymentTypeController::class, 'create'])      ->name('create');
        Route::post('/',                  [PaymentTypeController::class, 'store'])       ->name('store');
        Route::delete('/bulk-delete',     [PaymentTypeController::class, 'bulkDestroy'])->name('bulkDestroy');
        Route::get('/{paymentType}/ubah', [PaymentTypeController::class, 'edit'])        ->name('edit');
        Route::put('/{paymentType}',      [PaymentTypeController::class, 'update'])      ->name('update');
        Route::delete('/{paymentType}',   [PaymentTypeController::class, 'destroy'])     ->name('destroy');
    });

    // Tagihan
    Route::prefix('tagihan')->name('bills.')->group(function () {
        Route::get('/',              [BillController::class, 'index'])        ->name('index');
        Route::get('/generate',      [BillController::class, 'generateForm']) ->name('generate');
        Route::post('/preview',      [BillController::class, 'preview'])      ->name('preview');
        Route::post('/generate',     [BillController::class, 'generateStore'])->name('generate.store');
        Route::delete('/bulk-delete',[BillController::class, 'bulkDestroy'])  ->name('bulkDestroy');
        Route::delete('/{bill}',     [BillController::class, 'destroy'])      ->name('destroy');
    });

    // Pembayaran
    Route::prefix('pembayaran')->name('payments.')->group(function () {
        Route::get('/',                   [PaymentController::class, 'index'])      ->name('index');
        Route::get('/tambah',             [PaymentController::class, 'create'])     ->name('create');
        Route::post('/',                  [PaymentController::class, 'store'])      ->name('store');
        Route::get('/{payment}',          [PaymentController::class, 'show'])       ->name('show');
        Route::get('/{payment}/struk',    [PaymentController::class, 'cetakStruk']) ->name('struk');
        Route::delete('/{payment}',       [PaymentController::class, 'destroy'])    ->name('destroy');
    });

    // Template Struk
    Route::prefix('template-struk')->name('receipt-templates.')->group(function () {
        Route::get('/',                             [ReceiptTemplateController::class, 'index'])  ->name('index');
        Route::get('/tambah',                       [ReceiptTemplateController::class, 'create']) ->name('create');
        Route::post('/',                            [ReceiptTemplateController::class, 'store'])  ->name('store');
        Route::get('/{receiptTemplate}/ubah',       [ReceiptTemplateController::class, 'edit'])   ->name('edit');
        Route::put('/{receiptTemplate}',            [ReceiptTemplateController::class, 'update']) ->name('update');
        Route::delete('/{receiptTemplate}',         [ReceiptTemplateController::class, 'destroy'])->name('destroy');
        Route::get('/{receiptTemplate}/preview',    [ReceiptTemplateController::class, 'preview'])->name('preview');
    });

    // Profil Pengguna
    Route::get('/profil',    [ProfileController::class, 'edit'])   ->name('profile.edit');
    Route::patch('/profil',  [ProfileController::class, 'update']) ->name('profile.update');
    Route::delete('/profil', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
