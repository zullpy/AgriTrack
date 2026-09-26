<?php

use App\Http\Controllers\Api\KalenderHstSyncController;
use App\Http\Controllers\Api\KeuanganSyncController;
use App\Http\Controllers\Api\MedicineSyncController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KalenderHstController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PlantCatalogController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('tanaman-katalog', PlantCatalogController::class);

Route::get('/data-obat', [MedicineController::class, 'index']);
Route::get('/data-obat/tambah', [MedicineController::class, 'create']);
Route::post('/data-obat', [MedicineController::class, 'store']);
Route::get('/data-obat/{medicine}/edit', [MedicineController::class, 'edit']);
Route::put('/data-obat/{medicine}', [MedicineController::class, 'update']);
Route::delete('/data-obat/{medicine}', [MedicineController::class, 'destroy']);
Route::delete('/data-obat/{medicine}/foto', [MedicineController::class, 'destroyPhoto']);

// Tahapan & Sub-menu
Route::get('/steps', [StepController::class, 'index'])->name('steps.index');
Route::get('/steps/pengolahan-tanah', [StepController::class, 'pengolahanTanah'])->name('steps.pengolahan-tanah');
Route::post('/steps/pengolahan-tanah', [StepController::class, 'storePengolahanTanah'])->name('steps.pengolahan-tanah.store');
Route::put('/steps/pengolahan-tanah/{step}', [StepController::class, 'updatePengolahanTanah'])->name('steps.pengolahan-tanah.update');
Route::delete('/steps/pengolahan-tanah/{step}', [StepController::class, 'destroyPengolahanTanah'])->name('steps.pengolahan-tanah.destroy');
Route::post('/steps/pengolahan-tanah/{step}/foto', [StepController::class, 'uploadPhotoPengolahanTanah'])->name('steps.pengolahan-tanah.foto.upload');
Route::delete('/steps/pengolahan-tanah/{step}/foto', [StepController::class, 'destroyPhotoPengolahanTanah'])->name('steps.pengolahan-tanah.foto.destroy');
Route::get('/steps/penanaman-bibit', [StepController::class, 'penanamanBibit'])->name('steps.penanaman-bibit');

// Keuangan
Route::get('/keuangan', [KeuanganController::class, 'index'])->name('keuangan.index');
Route::post('/keuangan', [KeuanganController::class, 'store'])->name('keuangan.store');
Route::put('/keuangan/{transaction}', [KeuanganController::class, 'update'])->name('keuangan.update');
Route::delete('/keuangan/{transaction}', [KeuanganController::class, 'destroy'])->name('keuangan.destroy');
Route::post('/keuangan/kategori', [KeuanganController::class, 'storeCategory'])->name('keuangan.category.store');
Route::put('/keuangan/kategori/{category}', [KeuanganController::class, 'updateCategory'])->name('keuangan.category.update');
Route::delete('/keuangan/kategori/{category}', [KeuanganController::class, 'destroyCategory'])->name('keuangan.category.destroy');

// Kalender HST
Route::get('/kalender-hst', [KalenderHstController::class, 'index'])->name('kalender-hst.index');
Route::get('/kalender-hst/tanaman/{crop}', [KalenderHstController::class, 'showCrop'])->name('kalender-hst.crop.show');
Route::post('/kalender-hst/tanaman', [KalenderHstController::class, 'storeCrop']);
Route::put('/kalender-hst/tanaman/{crop}', [KalenderHstController::class, 'updateCrop']);
Route::delete('/kalender-hst/tanaman/{crop}', [KalenderHstController::class, 'destroyCrop']);
Route::post('/kalender-hst/tanaman/{crop}/panen', [KalenderHstController::class, 'markAsHarvested']);
Route::post('/kalender-hst/tanaman/{crop}/akhiri', [KalenderHstController::class, 'endCrop']);
Route::post('/kalender-hst/tanaman/{crop}/kegiatan', [KalenderHstController::class, 'storeActivity']);
Route::put('/kalender-hst/kegiatan/{activity}', [KalenderHstController::class, 'updateActivity']);
Route::match(['post', 'patch'], '/kalender-hst/kegiatan/{activity}/toggle', [KalenderHstController::class, 'toggleActivityStatus']);
Route::delete('/kalender-hst/kegiatan/{activity}/foto', [KalenderHstController::class, 'destroyPhoto']);
Route::delete('/kalender-hst/kegiatan/{activity}', [KalenderHstController::class, 'destroyActivity']);

// Offline PWA Sync Endpoints
Route::prefix('api')->group(function () {
    Route::get('/medicines', [MedicineSyncController::class, 'index']);
    Route::post('/medicines/sync', [MedicineSyncController::class, 'sync']);
    Route::post('/kalender-hst/sync', [KalenderHstSyncController::class, 'sync']);
    Route::get('/keuangan', [KeuanganSyncController::class, 'index']);
    Route::post('/keuangan/sync', [KeuanganSyncController::class, 'sync']);
});
