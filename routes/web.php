<?php

use App\Http\Controllers\Api\MedicineSyncController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KalenderHstController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PlantCatalogController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('tanaman-katalog', PlantCatalogController::class)->except(['destroy']);

Route::get('/data-obat', [MedicineController::class, 'index']);
Route::get('/data-obat/tambah', [MedicineController::class, 'create']);
Route::post('/data-obat', [MedicineController::class, 'store']);
Route::get('/data-obat/{medicine}/edit', [MedicineController::class, 'edit']);
Route::put('/data-obat/{medicine}', [MedicineController::class, 'update']);
Route::delete('/data-obat/{medicine}', [MedicineController::class, 'destroy']);
Route::delete('/data-obat/{medicine}/foto', [MedicineController::class, 'destroyPhoto']);

// Kalender HST / Tahapan
Route::get('/kalender-hst', [KalenderHstController::class, 'index'])->name('kalender-hst.index');
Route::get('/steps', [StepController::class, 'index'])->name('steps.index');
Route::get('/kalender-hst/tanaman/{crop}', [KalenderHstController::class, 'showCrop'])->name('kalender-hst.crop.show');
Route::post('/kalender-hst/tanaman', [KalenderHstController::class, 'storeCrop']);
Route::put('/kalender-hst/tanaman/{crop}', [KalenderHstController::class, 'updateCrop']);
Route::delete('/kalender-hst/tanaman/{crop}', [KalenderHstController::class, 'destroyCrop']);
Route::post('/kalender-hst/tanaman/{crop}/panen', [KalenderHstController::class, 'markAsHarvested']);
Route::post('/kalender-hst/tanaman/{crop}/kegiatan', [KalenderHstController::class, 'storeActivity']);
Route::put('/kalender-hst/kegiatan/{activity}', [KalenderHstController::class, 'updateActivity']);
Route::match(['post', 'patch'], '/kalender-hst/kegiatan/{activity}/toggle', [KalenderHstController::class, 'toggleActivityStatus']);
Route::delete('/kalender-hst/kegiatan/{activity}', [KalenderHstController::class, 'destroyActivity']);

// Offline PWA Sync Endpoints
Route::prefix('api')->group(function () {
    Route::get('/medicines', [MedicineSyncController::class, 'index']);
    Route::post('/medicines/sync', [MedicineSyncController::class, 'sync']);
});
