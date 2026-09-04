<?php

use App\Http\Controllers\MedicineController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/data-obat');
});

Route::get('/data-obat', [MedicineController::class, 'index']);
Route::get('/data-obat/tambah', [MedicineController::class, 'create']);
Route::post('/data-obat', [MedicineController::class, 'store']);
Route::get('/data-obat/{medicine}/edit', [MedicineController::class, 'edit']);
Route::put('/data-obat/{medicine}', [MedicineController::class, 'update']);
Route::delete('/data-obat/{medicine}', [MedicineController::class, 'destroy']);
