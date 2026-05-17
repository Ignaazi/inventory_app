<?php

use App\Http\Controllers\StockEngineeringController;
use Illuminate\Support\Facades\Route;

// Route khusus untuk manajemen rak engineering
Route::post('/rak/store', [StockEngineeringController::class, 'storeRak'])->name('rak.store');
Route::delete('/rak/destroy/{id}', [StockEngineeringController::class, 'destroyRak'])->name('rak.destroy');