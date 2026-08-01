<?php

use App\Http\Controllers\Costing\MaterialReceivedController;
use Illuminate\Support\Facades\Route;

// Group Utama pake name eng.material.receiving.
Route::middleware(['web', 'auth'])->prefix('eng/material-receiving')->name('eng.material.receiving.')->group(function () {
    
    // Halaman List Utama Monitor untuk Engineering (Menembak engIndex)
    Route::get('/', [MaterialReceivedController::class, 'engIndex'])->name('index');
    
    // Halaman History
    Route::get('/history', function() { return "Halaman History Material Received (Coming Soon)"; })->name('history');
    
    // Halaman Form Tanda Tangan Digital Engineering (Menembak engConfirm dengan parameter ID)
    Route::get('/confirm/{id}', [MaterialReceivedController::class, 'engConfirm'])->name('create');
    
    // TAHAP 2: Eksekusi TTD Staff Engineering (Menembak signEngineeringStaff)
    Route::post('/staff-sign/{id}', [MaterialReceivedController::class, 'signEngineeringStaff'])->name('staff.sign');
    
    // TAHAP 3: Eksekusi Approval SPV Engineering (Menembak approveEngineeringSpv)
    Route::post('/spv-approve/{id}', [MaterialReceivedController::class, 'approveEngineeringSpv'])->name('spv.approve');
    
    // Preview Laporan Live Cetak (Menembak show)
    Route::get('/{id}/preview', [MaterialReceivedController::class, 'show'])->name('show');
    
    // Hapus Laporan (Menembak destroy)
    Route::delete('/{id}/delete', [MaterialReceivedController::class, 'destroy'])->name('destroy');
});

// Group Alias tetap aman terarah ke method engIndex
Route::middleware(['web', 'auth'])->prefix('eng/material-receiving-alias')->name('eng.material.receipt.')->group(function () {
    Route::get('/', [MaterialReceivedController::class, 'engIndex'])->name('index');
    Route::get('/history', function() { return "Halaman History Material Received (Coming Soon)"; })->name('history');
});