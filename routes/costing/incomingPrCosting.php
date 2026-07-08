<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\ApprovalController;

Route::middleware('role:admin,costing')->group(function () {
    // URL: /costing/incoming-pr
    Route::get('/incoming-pr', [ApprovalController::class, 'index'])->name('costing.pr.index');
    Route::put('/incoming-pr/{id}/approve', [ApprovalController::class, 'approve'])->name('costing.pr.approve');
    Route::put('/incoming-pr/{id}/reject', [ApprovalController::class, 'reject'])->name('costing.pr.reject');
});