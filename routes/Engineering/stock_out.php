<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Engineering\StockOutEngineeringController;

Route::middleware(['auth'])->group(function () {
    // 1. Halaman utama list history stock out (Nama disesuaikan jadi eng.out agar singkron dengan view)
    Route::get('/eng/out', [StockOutEngineeringController::class, 'index'])->name('eng.out');
    
    // 2. Tampilan untuk Input Manual (Wajib lewat Controller agar bisa kirim data $stocks ke dropdown)
    Route::get('/eng/out/manual', [StockOutEngineeringController::class, 'manual'])->name('eng.out.manual');

    // 3. Tampilan untuk Scan Barcode (Lewat Controller agar terstruktur rapi mengikuti pola Manual In)
    Route::get('/eng/out/scan', [StockOutEngineeringController::class, 'scan'])->name('eng.out.scan');

    // 4. Proses simpan data transaksi out (Mengurangi stok utama & mencatat log)
    Route::post('/eng/out/store', [StockOutEngineeringController::class, 'store'])->name('eng.out.store');
});