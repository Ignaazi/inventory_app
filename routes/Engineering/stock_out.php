<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Engineering\StockOutEngineeringController;

Route::middleware(['auth'])->group(function () {
    // 1. Halaman utama list history stock out (Mengarah ke Controller)
    Route::get('/eng/out', [StockOutEngineeringController::class, 'index'])->name('eng.out.index');
    
    // 2. Proses simpan data transaksi out
    Route::post('/eng/out/store', [StockOutEngineeringController::class, 'store'])->name('eng.out.store');
    
    // 3. Jalur aman untuk view scan & manual (Sudah diarahkan ke dalam folder stock_eng.transaction)
    Route::get('/eng/out/scan', function() { 
        return view('stock_eng.transaction.out_scan'); 
    })->name('eng.out.scan');

    Route::get('/eng/out/manual', function() { 
        return view('stock_eng.transaction.out_manual'); 
    })->name('eng.out.manual');
});