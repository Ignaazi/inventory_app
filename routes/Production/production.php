<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Production\RequestProdController;

// Semua route khusus Production Scanner / Inventory Sparepart Nozzle
Route::middleware(['web', 'auth'])->group(function () {
    
    // Mengelompokkan route production/request agar lebih rapi dan scannable
    Route::prefix('production/request')->name('prod.request.')->group(function () {
        
        // 1. Halaman Form Create Request Baru
        Route::get('/create', [RequestProdController::class, 'create'])->name('create');

        // 2. Proses Simpan Data Baru Pertama Kali
        Route::post('/store', [RequestProdController::class, 'store'])->name('store');

        // 3. Halaman Tabel List Semua Request (listRequestProd.blade.php)
        Route::get('/list', [RequestProdController::class, 'listRequest'])->name('list');

        // 4. Halaman Form Edit Khusus untuk Data Draft (draftRequestProd.blade.php)
        Route::get('/draft/{id}/edit', [RequestProdController::class, 'editDraft'])->name('edit_draft');

        // 5. Proses Update/Kirim Ulang Data dari Form Draft (Method PUT)
        Route::put('/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('update_draft');

        // ==================== TAMBAHAN & PERBAIKAN ROUTE BARU ====================
        
        /**
         * FIX UPDATE ACTION: Route ini wajib ada agar action update dari form edit draft 
         * atau update status request di list bisa dieksekusi oleh RequestProdController!
         */
        Route::put('/{id}/update', [RequestProdController::class, 'update'])->name('update');

        // 6. Halaman Preview Form Nozzle (Menampilkan formulir resmi/cetak)
        Route::get('/{id}/preview', [RequestProdController::class, 'preview'])->name('preview');

        // 7. Proses Hapus Data Request Permanen (Method DELETE)
        Route::delete('/{id}/delete', [RequestProdController::class, 'destroy'])->name('destroy');
        
    });

});