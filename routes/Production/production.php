<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Production\RequestProdController;
use App\Http\Controllers\Production\StockProdController; // 1. WAJIB IMPORT CONTROLLER INI DI ATAS

// Semua route khusus Production Scanner / Inventory Sparepart Nozzle
Route::middleware(['web', 'auth'])->group(function () {
    
    // =========================================================================
    // GRUP 1: PRODUCTION REQUEST (Kodingan Lu yang Sudah Ada)
    // =========================================================================
    Route::prefix('production/request')->name('prod.request.')->group(function () {
        Route::get('/create', [RequestProdController::class, 'create'])->name('create');
        Route::post('/store', [RequestProdController::class, 'store'])->name('store');
        Route::get('/list', [RequestProdController::class, 'listRequest'])->name('list');
        Route::get('/draft/{id}/edit', [RequestProdController::class, 'editDraft'])->name('edit_draft');
        Route::put('/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('update_draft');
        Route::put('/{id}/update', [RequestProdController::class, 'update'])->name('update');
        Route::get('/{id}/preview', [RequestProdController::class, 'preview'])->name('preview');
        Route::delete('/{id}/delete', [RequestProdController::class, 'destroy'])->name('destroy');
    });

    // =========================================================================
    // GRUP 2: NEW UPDATE - PRODUCTION STOCK INVENTORY (TAMBAHKAN KODE INI)
    // =========================================================================
    Route::prefix('prod/stock')->name('stock.prod.')->group(function () {
        
        // Halaman Utama: Monitoring Stok Nozzle per Line
        Route::get('/', [StockProdController::class, 'index'])->name('index');
        
        // Proses Alokasi Add Line / Add Nozzle (Menerima log Out dari Engineering)
        Route::post('/store', [StockProdController::class, 'nozzleStore'])->name('nozzleStore');
        
        // Proses Edit / Penyesuaian Qty & Min Stock via Modal
        Route::put('/{id}', [StockProdController::class, 'update'])->name('update');
        
        // Proses Reset / Mengosongkan kembali info Nozzle di Line (DELETE)
        Route::delete('/{id}', [StockProdController::class, 'destroy'])->name('destroy');
        
        // Laporan Export CSV
        Route::get('/export/csv', [StockProdController::class, 'exportCSV'])->name('export.csv');
    });

});