<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\MaterialReceivedController; 

Route::middleware(['web', 'auth', 'role:admin,costing,engineering'])->group(function () {
    
    // 1. HALAMAN LIST DATA / TRACKING (Sub-menu: List Material Received)
    Route::get('/material-list', [MaterialReceivedController::class, 'index'])
        ->name('costing.material.list');

    // 2. HALAMAN FORM INPUT BARU (Sub-menu: Material Received Form)
    Route::get('/material-received', [MaterialReceivedController::class, 'create'])
        ->name('costing.material.received');

    // 3. HALAMAN PREVIEW DOKUMEN CETAK (Action Klik Mata)
    Route::get('/material-received/preview/{id}', [MaterialReceivedController::class, 'show'])
        ->name('costing.material.preview');

    // 4. PROSES SUBMIT & TTD BERJENJANG
    Route::post('/material-received/costing-store', [MaterialReceivedController::class, 'storeCostingSignature'])
        ->name('costing.signature.store');

    Route::post('/material-received/engineering-staff-sign/{id}', [MaterialReceivedController::class, 'signEngineeringStaff'])
        ->name('engineering.staff.sign');

    Route::post('/material-received/engineering-spv-approve/{id}', [MaterialReceivedController::class, 'approveEngineeringSpv'])
        ->name('engineering.spv.approve');

    // 🗑️ 5. PROSES HAPUS DATA & FILE FISIK TTD
    Route::delete('/material-received/delete/{id}', [MaterialReceivedController::class, 'destroy'])
        ->name('costing.material.delete');

    // ==========================================
    // RUTE COATING
    // ==========================================
    Route::get('/coating/material-receiving', [MaterialReceivedController::class, 'coatingIndex'])
        ->name('coating.material.received'); 

    Route::post('/coating/material-receiving/store', [MaterialReceivedController::class, 'store'])
        ->name('coating.material.received.store');
});