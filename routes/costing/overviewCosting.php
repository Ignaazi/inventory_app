<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\CostingOverviewController;

Route::middleware('role:admin,costing')->group(function () {
    // URL: /costing/overview
    Route::get('/overview', [CostingOverviewController::class, 'index'])->name('costing.overview');
});