<?php

use App\Http\Controllers\Production\ListLineProductionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    // Group Route untuk Master Line Production
    Route::get('/prod/list-line-production', [ListLineProductionController::class, 'index'])->name('prod.line.index');
    Route::post('/prod/list-line-production', [ListLineProductionController::class, 'store'])->name('prod.line.store');
    Route::put('/prod/list-line-production/{id}', [ListLineProductionController::class, 'update'])->name('prod.line.update');
    Route::delete('/prod/list-line-production/{id}', [ListLineProductionController::class, 'destroy'])->name('prod.line.destroy');
});