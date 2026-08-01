<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\MaterialReceivedController; 

Route::middleware(['web', 'auth', 'role:admin,costing,engineering'])->group(function () {
    
    // 1. HALAMAN LIST DATA / TRACKING (Sub-menu: List Material Received)
    Route::get('/material-list', [MaterialReceivedController::class, 'index'])
        ->name('costing.material.list');

    // 2. HALAMAN FORM INPUT BARU (Mendukung Sistem Parsial Cicilan PR)
    Route::get('/material-received/{pr_id?}', [MaterialReceivedController::class, 'create'])
        ->name('costing.material.received');

    // 3. HALAMAN PREVIEW DOKUMEN VIA AJAX / JSON VIEW (Klik Mata)
    Route::get('/material-received/preview/{id}', [MaterialReceivedController::class, 'show'])
        ->name('costing.material.preview');

    // 4. PROSES SUBMIT DATA & TTD BERJENJANG 3 TAHAP
    // Tahap 1: Simpan Awal Kedatangan Barang oleh Costing (Status -> pending)
    Route::post('/material-received/store', [MaterialReceivedController::class, 'storeCostingSignature'])
        ->name('costing.material.store');

    // Tahap 2: Verifikasi & Pengecekan Fisik oleh Staff Engineering (Status -> checked)
    Route::post('/material-received/engineering-staff-sign/{id}', [MaterialReceivedController::class, 'signEngineeringStaff'])
        ->name('engineering.staff.sign');

    // Tahap 3: Final Approval & Validasi oleh Admin / Supervisor (Status -> approved)
    Route::post('/material-received/engineering-spv-approve/{id}', [MaterialReceivedController::class, 'approveEngineeringSpv'])
        ->name('engineering.spv.approve');

    // 5. PROSES HAPUS DATA & PEMBANTALAN RECORD
    Route::delete('/material-received/delete/{id}', [MaterialReceivedController::class, 'destroy'])
        ->name('costing.material.delete');
});