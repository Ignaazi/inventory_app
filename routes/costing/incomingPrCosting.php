<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\ApprovalController;

Route::middleware('role:admin,costing')->group(function () {
    // Halaman Utama: Daftar Pending PR untuk Costing Audit
    Route::get('/incoming-pr', [ApprovalController::class, 'index'])->name('costing.pr.index');
    
    // TAMPILKAN FORM APPROVE BARU (Membuka views/cost_section/approve_PrForm.blade.php)
    Route::get('/incoming-pr/{id}/approve', [ApprovalController::class, 'approveForm'])->name('costing.pr.approveForm');
    
    // PROSES SIMPAN/SUBMIT APPROVAL (Dipanggil dari dalam form approve_PrForm)
    Route::put('/incoming-pr/{id}/approve', [ApprovalController::class, 'approve'])->name('costing.pr.approve');
    
    // PROSES REJECT ONSITE (Tetap langsung dieksekusi via SweetAlert2 dari tabel utama)
    Route::put('/incoming-pr/{id}/reject', [ApprovalController::class, 'reject'])->name('costing.pr.reject');
    
    // Halaman History PR Costing
    Route::get('/history-pr', [ApprovalController::class, 'history'])->name('costing.pr.history');

    // PREVIEW DETAIL ARSIP/HISTORY PR (Membuka views/cost_section/preview_approval_pr.blade.php)
    Route::get('/history-pr/{id}/preview', [ApprovalController::class, 'show'])->name('costing.pr.show');
});