<?php

use App\Http\Controllers\Api\KalenderHstSyncController;
use App\Http\Controllers\Api\KeuanganSyncController;
use App\Http\Controllers\Api\MedicineSyncController;
use App\Http\Controllers\Api\StepSyncController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KalenderHstController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\PlantCatalogController;
use App\Http\Controllers\StepController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::resource('tanaman-katalog', PlantCatalogController::class);

Route::get('/data-obat', [MedicineController::class, 'index']);
Route::get('/data-obat/tambah', [MedicineController::class, 'create']);
Route::post('/data-obat', [MedicineController::class, 'store']);
Route::get('/data-obat/kategori', [MedicineController::class, 'getCategories'])->name('data-obat.category.index');
Route::post('/data-obat/kategori', [MedicineController::class, 'storeCategory'])->name('data-obat.category.store');
Route::match(['put', 'post', 'patch'], '/data-obat/kategori/{category}', [MedicineController::class, 'updateCategory'])->name('data-obat.category.update');
Route::match(['delete', 'post'], '/data-obat/kategori/{category}', [MedicineController::class, 'destroyCategory'])->name('data-obat.category.destroy');
Route::get('/data-obat/{medicine}/edit', [MedicineController::class, 'edit']);
Route::put('/data-obat/{medicine}', [MedicineController::class, 'update']);
Route::post('/data-obat/{medicine}/pembelian', [MedicineController::class, 'storePurchase'])->name('data-obat.pembelian.store');
Route::delete('/data-obat/{medicine}', [MedicineController::class, 'destroy']);
Route::delete('/data-obat/{medicine}/foto', [MedicineController::class, 'destroyPhoto']);

// Stok Obat & Pembelian
Route::get('/stok-obat', [MedicineController::class, 'stok'])->name('stok-obat.index');
Route::get('/data-obat/stok', function () {
    return redirect('/stok-obat');
});
Route::post('/stok-obat/pembelian', [MedicineController::class, 'storeGeneralPurchase'])->name('stok-obat.pembelian.store');

// Tahapan & Sub-menu
Route::get('/steps', [StepController::class, 'index'])->name('steps.index');
Route::get('/steps/pengolahan-tanah', [StepController::class, 'pengolahanTanah'])->name('steps.pengolahan-tanah');
Route::post('/steps/pengolahan-tanah', [StepController::class, 'storePengolahanTanah'])->name('steps.pengolahan-tanah.store');
Route::put('/steps/pengolahan-tanah/{step}', [StepController::class, 'updatePengolahanTanah'])->name('steps.pengolahan-tanah.update');
Route::delete('/steps/pengolahan-tanah/{step}', [StepController::class, 'destroyPengolahanTanah'])->name('steps.pengolahan-tanah.destroy');
Route::post('/steps/pengolahan-tanah/{step}/foto', [StepController::class, 'uploadPhotoPengolahanTanah'])->name('steps.pengolahan-tanah.foto.upload');
Route::delete('/steps/pengolahan-tanah/{step}/foto', [StepController::class, 'destroyPhotoPengolahanTanah'])->name('steps.pengolahan-tanah.foto.destroy');
// Tahapan Penanaman Bibit (Multi-Bibit & SOP Langkah)
Route::get('/steps/penanaman-bibit', [StepController::class, 'penanamanBibit'])->name('steps.penanaman-bibit');
Route::post('/steps/penanaman-bibit/seeds', [StepController::class, 'storePlantingSeed'])->name('steps.penanaman-bibit.seeds.store');
Route::put('/steps/penanaman-bibit/seeds/{seed}', [StepController::class, 'updatePlantingSeed'])->name('steps.penanaman-bibit.seeds.update');
Route::delete('/steps/penanaman-bibit/seeds/{seed}', [StepController::class, 'destroyPlantingSeed'])->name('steps.penanaman-bibit.seeds.destroy');

Route::post('/steps/penanaman-bibit/seeds/{seed}/steps', [StepController::class, 'storePlantingStep'])->name('steps.penanaman-bibit.steps.store');
Route::put('/steps/penanaman-bibit/steps/{step}', [StepController::class, 'updatePlantingStep'])->name('steps.penanaman-bibit.steps.update');
Route::delete('/steps/penanaman-bibit/steps/{step}', [StepController::class, 'destroyPlantingStep'])->name('steps.penanaman-bibit.steps.destroy');

Route::post('/steps/penanaman-bibit/steps/{step}/foto', [StepController::class, 'uploadPhotoPlantingStep'])->name('steps.penanaman-bibit.steps.foto.upload');
Route::delete('/steps/penanaman-bibit/steps/{step}/foto', [StepController::class, 'destroyPhotoPlantingStep'])->name('steps.penanaman-bibit.steps.foto.destroy');

// Catatan Penting Budidaya
Route::get('/steps/catatan', [StepController::class, 'catatan'])->name('steps.catatan');
Route::post('/steps/catatan', [StepController::class, 'storeCatatan'])->name('steps.catatan.store');
Route::put('/steps/catatan/{note}', [StepController::class, 'updateCatatan'])->name('steps.catatan.update');
Route::delete('/steps/catatan/{note}', [StepController::class, 'destroyCatatan'])->name('steps.catatan.destroy');

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
    Route::get('/steps/pengolahan-tanah', [StepSyncController::class, 'getPengolahanTanah']);
    Route::post('/steps/pengolahan-tanah/sync', [StepSyncController::class, 'syncPengolahanTanah']);
    Route::get('/steps/penanaman-bibit', [StepSyncController::class, 'getPenanamanBibit']);
    Route::post('/steps/penanaman-bibit/sync', [StepSyncController::class, 'syncPenanamanBibit']);
});

// Remote Migration & Maintenance Runner for Shared Hosting (InfinityFree)
Route::get('/run-migrate', function () {
    $expectedToken = env('MIGRATE_TOKEN', 'pertanian2026');
    $token = request('token');

    if (empty($token) || $token !== $expectedToken) {
        abort(403, 'Akses Ditolak: Token tidak valid. Masukkan ?token='.$expectedToken);
    }

    $action = request('action', 'migrate');
    $validActions = [
        'migrate' => ['cmd' => 'migrate', 'params' => ['--force' => true], 'label' => 'php artisan migrate --force'],
        'status' => ['cmd' => 'migrate:status', 'params' => [], 'label' => 'php artisan migrate:status'],
        'optimize-clear' => ['cmd' => 'optimize:clear', 'params' => [], 'label' => 'php artisan optimize:clear'],
    ];

    if (! isset($validActions[$action])) {
        $action = 'migrate';
    }

    $config = $validActions[$action];
    $success = true;
    $output = '';

    try {
        Artisan::call($config['cmd'], $config['params']);
        $output = trim(Artisan::output());
        if (empty($output)) {
            $output = 'Perintah berhasil dijalankan tanpa output khusus.';
        }
    } catch (Throwable $e) {
        $success = false;
        $output = "Terjadi Kesalahan:\n".$e->getMessage();
    }

    $escapedOutput = htmlspecialchars($output, ENT_QUOTES, 'UTF-8');
    $bgStatus = $success ? 'bg-emerald-500' : 'bg-rose-500';
    $textStatus = $success ? 'Berhasil' : 'Gagal';
    $tokenQuery = '?token='.urlencode($token);
    $cmdLabel = htmlspecialchars($config['label'], ENT_QUOTES, 'UTF-8');

    return response(<<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Runner - Web Pertanian</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8 flex flex-col items-center justify-center font-sans">
    <div class="w-full max-w-2xl bg-slate-800 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-5 border-b border-slate-700/70 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-3">
                <span class="w-3.5 h-3.5 rounded-full {$bgStatus} animate-pulse"></span>
                <div>
                    <h1 class="font-bold text-base text-white tracking-wide">Artisan Hosting Runner</h1>
                    <p class="text-xs text-slate-400 font-mono">{$cmdLabel}</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold {$bgStatus} text-white">
                {$textStatus}
            </span>
        </div>

        <!-- Terminal Output -->
        <div class="p-5 bg-slate-950 font-mono text-xs sm:text-sm text-emerald-400 overflow-x-auto whitespace-pre-wrap leading-relaxed border-b border-slate-800 selection:bg-emerald-900 selection:text-white">{$escapedOutput}</div>

        <!-- Control Actions -->
        <div class="p-4 bg-slate-900/60 flex items-center justify-between flex-wrap gap-2 text-xs">
            <div class="flex items-center gap-1.5 flex-wrap">
                <a href="/run-migrate{$tokenQuery}&action=migrate" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-semibold transition-all">
                    Run Migrate
                </a>
                <a href="/run-migrate{$tokenQuery}&action=status" class="px-3 py-1.5 rounded-lg bg-slate-700 hover:bg-slate-600 text-slate-200 font-medium transition-all">
                    Cek Status
                </a>
                <a href="/run-migrate{$tokenQuery}&action=optimize-clear" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-500 text-white font-semibold transition-all">
                    Clear Cache
                </a>
            </div>
            <a href="/data-obat" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 font-medium transition-all ml-auto">
                &larr; Ke Data Obat
            </a>
        </div>
    </div>
</body>
</html>
HTML
    )->header('Content-Type', 'text/html');
});
