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
    
    // ==========================================
    // ---         TRANSAKSI MASUK (IN)       ---
    // ==========================================
    Route::get('/in', [InProdController::class, 'stockIn'])->name('in');
    Route::get('/in/manual', [InProdController::class, 'manualIn'])->name('in.manual');
    Route::post('/in/manual/store', [InProdController::class, 'storeManualIn'])->name('in.store_manual');
    Route::get('/get-eng-detail/{id}', [InProdController::class, 'getEngineeringDetail'])->name('get_eng_detail');
    
    // Rute Halaman Scan IN & Arah Submit Form Scan IN
    Route::get('/in/scan', [InProdController::class, 'scanIn'])->name('in.scan');
    Route::post('/in/scan/store', [InProdController::class, 'storeManualIn'])->name('in.store_scan'); 

    // ==========================================
    // ---        TRANSAKSI KELUAR (OUT)      ---
    // ==========================================
    // 1. Halaman Utama Tabel Log Riwayat OUT
    Route::get('/out', [OutProdController::class, 'stockOut'])->name('out');
    
    // 2. Form Pembuatan Manual OUT (Peta Method Disamakan: manualOut)
    Route::get('/out/manual', [OutProdController::class, 'manualOut'])->name('out.manual');
    
    // 3. Eksekusi Simpan Submit Form Manual OUT
    Route::post('/out/manual/store', [OutProdController::class, 'storeManualOut'])->name('out.manual.store');
    
    // 4. Terminal Mesin Live Scan OUT 
    Route::get('/out/scan', [OutProdController::class, 'scanOut'])->name('out.scan');
    
    // 5. ✅ FIX 404: Rute POST Endpoint AJAX dari scan_out.blade.php
    Route::post('/out/scan/store', [OutProdController::class, 'storeScanOut'])->name('out.store_scan');
    
    // 6. API Live Fetch Detail Item berdasarkan ID Pilihan
    Route::get('/out/detail/{id}', [OutProdController::class, 'getInProductionDetail'])->name('out.detail');
    
});