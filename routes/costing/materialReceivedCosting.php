<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\MaterialReceivedController; 

Route::middleware(['web', 'auth', 'role:admin,costing,engineering'])->group(function () {
    
    // =========================================================================
    // 💰 SISI COSTING (MANAGEMENT DATA)
    // =========================================================================
    
    // 1. HALAMAN LIST DATA / TRACKING (Sub-menu: List Material Received)
    Route::get('/material-list', [MaterialReceivedController::class, 'index'])
        ->middleware('role:admin,costing')
        ->name('costing.material.list');

    // 2. HALAMAN FORM INPUT BARU (Mendukung Sistem Parsial Cicilan PR)
    Route::get('/material-received/{pr_id?}', [MaterialReceivedController::class, 'create'])
        ->middleware('role:admin,costing')
        ->name('costing.material.received');

    // 3. HALAMAN PREVIEW DOKUMEN CETAK / AJAX VIEW (Action Klik Mata)
    Route::get('/material-received/preview/{id}', [MaterialReceivedController::class, 'show'])
        ->middleware('role:admin,costing,engineering')
        ->name('costing.material.preview');

    // 4. PROSES SUBMIT DATA & TTD BERJENJANG (TAHAP 1: COSTING)
    Route::post('/material-received/store', [MaterialReceivedController::class, 'storeCostingSignature'])
        ->middleware('role:admin,costing')
        ->name('costing.material.store');

    // 5. PROSES HAPUS DATA & PEMBATALAN RECORD (Dipakai juga untuk fitur REJECT di sisi Eng)
    Route::delete('/material-received/delete/{id}', [MaterialReceivedController::class, 'destroy'])
        ->name('costing.material.delete');


    // =========================================================================
    // ⚙️ SISI ENGINEERING (MONITORING & VERIFIKASI TTD)
    // =========================================================================
    
    // 1. Halaman Utama List Monitoring Material Eng (Hanya status pending & checked)
    Route::get('/eng/material-receiving', [MaterialReceivedController::class, 'engIndex'])
        ->middleware('role:admin,costing,engineering')
        ->name('eng.material.receiving.index');

    // 🌟 2. HALAMAN HISTORY MATERIAL RECEIVED (Hanya status approved / closed)
    Route::get('/eng/material-receiving-history', [MaterialReceivedController::class, 'engHistory'])
        ->middleware('role:admin,costing,engineering')
        ->name('eng.material.receiving.history');

    // 3. Halaman Form Confirm / Checked (Tempat Input TTD Staff)
    Route::get('/eng/material-receiving/confirm/{id}', [MaterialReceivedController::class, 'engConfirm'])
        ->middleware('role:admin,costing,engineering')
        ->name('eng.material.receiving.create'); 

    // 4. TAHAP 2: Proses Verifikasi & TTD Staff (POST/PUT dari form Staff)
    Route::put('/eng/material-receiving/update/{id}', [MaterialReceivedController::class, 'signEngineeringStaff'])
        ->middleware('role:admin,costing,engineering')
        ->name('eng.material.receiving.update');

    // 5. TAHAP 3A: Halaman Form Approval Akhir Supervisor Engineering (GET)
    Route::get('/eng/material-receiving/approve/{id}', [MaterialReceivedController::class, 'engApprove'])
        ->middleware('role:admin,costing,engineering')
        ->name('eng.material.receiving.approve');

    // 6. TAHAP 3B: Proses Submit Approval Akhir Supervisor Engineering (POST)
    Route::post('/eng/material-receiving/spv-approve/{id}', [MaterialReceivedController::class, 'approveEngineeringSpv'])
        ->middleware('role:admin,costing,engineering')
        ->name('engineering.spv.approve');


    // =========================================================================
    // 🏬 RUTE KHUSUS SUB-COATING
    // =========================================================================
    Route::get('/coating/material-receiving', [MaterialReceivedController::class, 'index'])
        ->name('coating.material.received'); 
});
