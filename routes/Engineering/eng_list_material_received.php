<?php

use App\Http\Controllers\Engineering\EngMaterialReceivingController;
use Illuminate\Support\Facades\Route;

// Group Utama pake name .receiving.
Route::middleware(['web', 'auth'])->prefix('eng/material-receiving')->name('eng.material.receiving.')->group(function () {
    
    Route::get('/', [EngMaterialReceivingController::class, 'index'])->name('index');
    Route::get('/history', function() { return "Halaman History Material Received (Coming Soon)"; })->name('history');
    Route::get('/confirm', [EngMaterialReceivingController::class, 'create'])->name('create');
    Route::post('/store', [EngMaterialReceivingController::class, 'store'])->name('store');
    
    // 🌟 FIX: Ganti name('preview') menjadi name('show') agar sinkron dengan file Blade lu!
    Route::get('/{id}/preview', [EngMaterialReceivingController::class, 'show'])->name('show');
    
    Route::delete('/{id}/delete', [EngMaterialReceivingController::class, 'destroy'])->name('destroy');
});

// Group Alias pake name .receipt.
Route::middleware(['web', 'auth'])->prefix('eng/material-receiving-alias')->name('eng.material.receipt.')->group(function () {
    Route::get('/', [EngMaterialReceivingController::class, 'index'])->name('index');
    Route::get('/history', function() { return "Halaman History Material Received (Coming Soon)"; })->name('history');
});