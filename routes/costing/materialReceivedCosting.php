<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\MaterialReceivedController; 

Route::middleware(['web', 'auth', 'role:admin,costing,engineering'])->group(function () {
    
    // =========================================================================
    // 💰 SISI COSTING (MANAGEMENT DATA)
    // =========================================================================
    
    // 1. HALAMAN LIST DATA / TRACKING (Sub-menu: List Material Received)
    Route::get('/material-list', [MaterialReceivedController::class, 'index'])
        ->name('costing.material.list');

    // 2. HALAMAN FORM INPUT BARU (Mendukung Sistem Parsial Cicilan PR)
    Route::get('/material-received/{pr_id?}', [MaterialReceivedController::class, 'create'])
        ->name('costing.material.received');

    // 3. HALAMAN PREVIEW DOKUMEN CETAK / AJAX VIEW (Action Klik Mata)
    Route::get('/material-received/preview/{id}', [MaterialReceivedController::class, 'show'])
        ->name('costing.material.preview');

    // 4. PROSES SUBMIT DATA & TTD BERJENJANG (TAHAP 1: COSTING)
    Route::post('/material-received/store', [MaterialReceivedController::class, 'storeCostingSignature'])
        ->name('costing.material.store');

    // 5. PROSES HAPUS DATA & PEMBATALAN RECORD (Dipakai juga untuk fitur REJECT di sisi Eng)
    Route::delete('/material-received/delete/{id}', [MaterialReceivedController::class, 'destroy'])
        ->name('costing.material.delete');


    // =========================================================================
    // ⚙️ SISI ENGINEERING (MONITORING & VERIFIKASI TTD)
    // =========================================================================
    
    // 1. Halaman Utama List Monitoring Material Eng
    Route::get('/eng/material-receiving', [MaterialReceivedController::class, 'engIndex'])
        ->name('eng.material.receiving.index');

    // 2. Halaman Form Confirm / Checked (Tempat Input TTD Staff)
    Route::get('/eng/material-receiving/confirm/{id}', [MaterialReceivedController::class, 'engConfirm'])
        ->name('eng.material.receiving.create'); // Sesuai dengan name bawaan sistem awal lo

    // 3. TAHAP 2: Proses Verifikasi & TTD Staff (POST/PUT dari form Staff)
    Route::put('/eng/material-receiving/update/{id}', [MaterialReceivedController::class, 'signEngineeringStaff'])
        ->name('eng.material.receiving.update');

    // 4. TAHAP 3A: Halaman Form Approval Akhir Supervisor Engineering (GET)
    Route::get('/eng/material-receiving/approve/{id}', [MaterialReceivedController::class, 'engApprove'])
        ->name('eng.material.receiving.approve');

    // 5. TAHAP 3B: Proses Submit Approval Akhir Supervisor Engineering (POST)
    // 💡 URL diseragamkan menjadi '/eng/material-receiving/spv-approve/{id}' agar konsisten dengan prefix '/eng/' lainnya
    Route::post('/eng/material-receiving/spv-approve/{id}', [MaterialReceivedController::class, 'approveEngineeringSpv'])
        ->name('engineering.spv.approve');


    // =========================================================================
    // 🏬 RUTE KHUSUS SUB-COATING
    // =========================================================================
    Route::get('/coating/material-receiving', [MaterialReceivedController::class, 'index'])
        ->name('coating.material.received'); 
});