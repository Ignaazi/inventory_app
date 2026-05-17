<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Production\RequestProdController;

// Semua route khusus Production Scanner / Inventory Sparepart Nozzle
Route::middleware(['web', 'auth'])->group(function () {
    
    // 1. Halaman Form Create Request Baru
    Route::get('/production/request/create', [RequestProdController::class, 'create'])->name('prod.request.create');

    // 2. Proses Simpan Data Baru Pertama Kali
    Route::post('/production/request/store', [RequestProdController::class, 'store'])->name('prod.request.store');

    // 3. Halaman Tabel List Semua Request (listRequestProd.blade.php)
    Route::get('/production/request/list', [RequestProdController::class, 'listRequest'])->name('prod.request.list');

    // 4. Halaman Form Edit Khusus untuk Data Draft (draftRequestProd.blade.php)
    Route::get('/production/request/draft/{id}/edit', [RequestProdController::class, 'editDraft'])->name('prod.request.edit_draft');

    // 5. Proses Update/Kirim Ulang Data dari Form Draft (Method PUT)
    Route::put('/production/request/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('prod.request.update_draft');

});