<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Production\RequestProdController;
use App\Http\Controllers\Production\ProductionOverviewController;
use App\Http\Controllers\Production\InProdController;
use App\Http\Controllers\Production\OutProdController;

// 📦 MODUL REQUEST PRODUCTION
Route::prefix('prod/request')->group(function () {
    Route::get('/list', [RequestProdController::class, 'listRequest'])->name('prod.request.list');
    Route::get('/create', [RequestProdController::class, 'create'])->name('prod.request.create');
    Route::post('/store', [RequestProdController::class, 'store'])->name('prod.request.store');
    Route::get('/draft/{id}', [RequestProdController::class, 'editDraft'])->name('prod.request.editDraft');
    Route::put('/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('prod.request.updateDraft');
    Route::put('/update/{id}', [RequestProdController::class, 'update'])->name('prod.request.update');
    Route::get('/preview/{id}', [RequestProdController::class, 'preview'])->name('prod.request.preview');
    Route::delete('/delete/{id}', [RequestProdController::class, 'destroy'])->name('prod.request.destroy');
    Route::get('/fetch-updates', [RequestProdController::class, 'fetchUpdates'])->name('prod.request.fetchUpdates');
});

Route::prefix('production/request')->group(function () {
    Route::post('/store', [RequestProdController::class, 'store']);
});

// 📊 OVERVIEW PRODUCTION
Route::get('/prod/overview', [ProductionOverviewController::class, 'index'])->name('prod.overview');
Route::get('/production-dashboard', function () { return view('dashboard'); })->name('production.dashboard');

// 🛠️ SEKTOR SINKRONISASI PRODUCTION (IN & OUT TRANSACTION)
Route::prefix('prod/transaction')->name('prod.transaction.')->group(function () {
    
    // --- TRANSAKSI MASUK (IN) ---
    Route::get('/in', [InProdController::class, 'stockIn'])->name('in');
    Route::get('/in/manual', [InProdController::class, 'manualIn'])->name('in.manual');
    Route::post('/in/manual/store', [InProdController::class, 'storeManualIn'])->name('in.store_manual');
    Route::get('/get-eng-detail/{id}', [InProdController::class, 'getEngineeringDetail'])->name('get_eng_detail');
    
    // ✨ FIX 404: Rute Halaman Scan & Arah Submit Form Scan
    Route::get('/in/scan', [InProdController::class, 'scanIn'])->name('in.scan');
    Route::post('/in', [InProdController::class, 'storeManualIn'])->name('in.store_scan'); // Menangani POST ke /prod/transaction/in dari form scan

    // --- TRANSAKSI KELUAR (OUT) ---
    Route::get('/out', [OutProdController::class, 'stockOut'])->name('out');
    Route::post('/out/manual/store', [OutProdController::class, 'storeManualOut'])->name('out.manual.store');
    Route::get('/out/detail/{id}', [OutProdController::class, 'getInProductionDetail']);
    
});