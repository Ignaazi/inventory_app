<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EngineeringOverviewController;
use App\Http\Controllers\Engineering\ListSparepartEngController;
use App\Http\Controllers\Production\ProductionOverviewController;
use App\Http\Controllers\Production\InProdController;
use App\Http\Controllers\Production\OutProdController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockEngineeringController;
use App\Http\Controllers\StockInEngineeringController;
use App\Http\Controllers\EngOverview\BarcodeParsingController;
use App\Http\Controllers\EngOverview\DbBarcodeController;
use App\Http\Controllers\EngOverview\TypeBarcodeController;
use App\Http\Controllers\Engineering\ApprovalEngController;
use App\Http\Controllers\Engineering\HistoryApprovalController;
use App\Http\Controllers\Production\RequestProdController;
use App\Http\Controllers\Engineering\StockOutEngineeringController;
use App\Http\Controllers\Engineering\PurchaseRequestEngController;
use App\Http\Controllers\Engineering\PurchaseRequestHistoryEngController;
use App\Http\Controllers\Engineering\TransactionController;

// 1. Redirect Halaman Utama
Route::get('/', function () {
    return redirect('/login');
});

// 2. Grup Route untuk Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 3. Grup Route untuk Auth (Sudah Login)
Route::middleware('auth')->group(function () {
    
    // Dashboard Utama (Admin)
    Route::get('/admin', function () {
        return view('admin'); 
    })->name('dashboard');

    // --- GRUP ADMIN (Full Access) ---
    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/admin/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/admin/users/store', [UserController::class, 'store'])->name('users.store');
        Route::get('/admin/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit'); 
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- GRUP ENGINEERING ---
    Route::middleware('role:admin,engineering')->group(function () {
        
        Route::prefix('eng')->group(function () {
            Route::get('/list-sparepart/export', [StockEngineeringController::class, 'export'])->name('list-sparepart.export');
            Route::get('/out/manual', [StockOutEngineeringController::class, 'manual'])->name('eng.out.manual');
            Route::get('/out/scan', [StockOutEngineeringController::class, 'scan'])->name('eng.out.scan');
            Route::post('/out/store', [StockOutEngineeringController::class, 'store'])->name('eng.out.store');
            
            // UPDATE: Cukup gunakan Resource Route ini saja.
            // Secara otomatis menyediakan rute URL /eng/list-sparepart dan /eng/list-sparepart/create 
            // dengan name route: list-sparepart.index & list-sparepart.create
            Route::resource('list-sparepart', ListSparepartEngController::class);
        });

        Route::get('/eng/approval', [ApprovalEngController::class, 'index'])->name('eng.approval');
        Route::get('/eng/approval/review/{id}', [ApprovalEngController::class, 'review'])->name('eng.approval.review');
        Route::post('/eng/approval/approve/{id}', [ApprovalEngController::class, 'approve'])->name('eng.approval.approve');
        Route::post('/eng/approval/reject/{id}', [ApprovalEngController::class, 'reject'])->name('eng.approval.reject');    
        
                // =========================================================================
        // 🚀 ROUTE PURCHASE REQUEST & FLOW VERIFIKASI (UPDATED WITHOUT COSTING MODULE)
        // =========================================================================

        // Opsi 1: Nama alias diganti jadi 'purchase.request.index' agar sinkron dengan blade
        Route::get('/eng/purchase-request', [PurchaseRequestEngController::class, 'index'])->name('purchase.request.index');
        Route::post('/eng/purchase-request', [PurchaseRequestEngController::class, 'store'])->name('purchase.request.store'); 

        // Rute Verifikasi Meja Kerja 1 (Admin / Checker)
        Route::get('/eng/purchase-request/list', [PurchaseRequestEngController::class, 'listRequests'])->name('purchase.request.list');
        Route::post('/eng/purchase-request/{id}/reject', [PurchaseRequestEngController::class, 'rejectRequest'])->name('purchase.request.reject');

        // 🔄 ALUR BARU JALUR CHECKED
        Route::get('/eng/purchase-request/{id}/check', [PurchaseRequestEngController::class, 'checkedView'])->name('purchase.request.checked.view');
        Route::post('/eng/purchase-request/{id}/check-confirm', [PurchaseRequestEngController::class, 'checkRequest'])->name('purchase.request.check');

        // Rute History Manajemen Pengajuan PR
        Route::get('/eng/purchase-request/history', [PurchaseRequestHistoryEngController::class, 'index'])->name('purchase.request.history');
        Route::get('/eng/purchase-request/{id}/preview', [PurchaseRequestHistoryEngController::class, 'preview'])->name('purchase.request.preview');
        Route::get('/eng/purchase-request/{id}/edit', [PurchaseRequestHistoryEngController::class, 'edit'])->name('purchase.request.edit');
        Route::put('/eng/purchase-request/{id}/update', [PurchaseRequestHistoryEngController::class, 'update'])->name('purchase.request.update');
        Route::delete('/eng/purchase-request/{id}/delete', [PurchaseRequestHistoryEngController::class, 'destroy'])->name('purchase.request.delete');

        // BARCODE PARSING & SETTINGS
        Route::get('/eng/barcode-parsing', [BarcodeParsingController::class, 'index'])->name('barcode.parsing.index');
        Route::post('/eng/barcode-scan', [BarcodeParsingController::class, 'scan'])->name('barcode.parsing.scan');

        Route::prefix('eng-overview')->group(function () {
            Route::get('/barcode-parsing', [BarcodeParsingController::class, 'index'])->name('barcode.parsing');
            Route::post('/barcode-parsing/store', [BarcodeParsingController::class, 'store']);
            Route::get('/barcode-parsing/get-configs', [BarcodeParsingController::class, 'getConfigs']);
            Route::get('/db-barcode', [DbBarcodeController::class, 'index'])->name('barcode.db');
            Route::delete('/db-barcode/{id}', [DbBarcodeController::class, 'destroy'])->name('barcode.db.delete');
            Route::get('/type-barcode', [TypeBarcodeController::class, 'index'])->name('barcode.type');
            Route::delete('/type-barcode/{id}', [TypeBarcodeController::class, 'destroy'])->name('barcode.type.delete');
        });

        // MAP ROUTE TRANSACTION (MENGGUNAKAN TRANSACTION CONTROLLER)
        Route::prefix('stock-eng/transaction')->name('stock_eng.transaction.')->group(function () {
            Route::get('/in', [TransactionController::class, 'indexIn'])->name('in');
            Route::get('/out', [TransactionController::class, 'indexOut'])->name('out');
            
            // --- BAGIAN RETURN ---
            Route::get('/return', [TransactionController::class, 'indexReturn'])->name('return');
            
            Route::get('/return/manual', function () {
                $stocks = \App\Models\StockEng::all(); 
                $raks = \App\Models\Rak::all(); 
                return view('stock_eng.transaction.return_manual', compact('stocks', 'raks'));
            })->name('return.manual');

            Route::post('/return/store', [TransactionController::class, 'storeReturn'])->name('return.store');
            
            // --- BAGIAN DISPOSAL ---
            Route::get('/disposal', [TransactionController::class, 'indexDisposal'])->name('disposal');
            Route::post('/disposal/store', [TransactionController::class, 'storeDisposal'])->name('disposal.store');
        });
    });
    
    // --- GRUP PRODUCTION ---
    Route::middleware('role:admin,production')->group(function () {
        
        // Bungkus rute request ke dalam satu grup biar konsisten
        Route::prefix('prod/request')->group(function () {
            Route::get('/list', [RequestProdController::class, 'listRequest'])->name('prod.request.list');
            Route::get('/create', [RequestProdController::class, 'create'])->name('prod.request.create');
            Route::post('/store', [RequestProdController::class, 'store'])->name('prod.request.store');
            Route::get('/draft/{id}', [RequestProdController::class, 'editDraft'])->name('prod.request.editDraft');
            Route::put('/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('prod.request.updateDraft');
            Route::put('/update/{id}', [RequestProdController::class, 'update'])->name('prod.request.update');
            Route::get('/preview/{id}', [RequestProdController::class, 'preview'])->name('prod.request.preview');
            Route::delete('/delete/{id}', [RequestProdController::class, 'destroy'])->name('prod.request.destroy');
            Route::get('/fetch-updates', [RequestProdController::class, 'fetchUpdates'])->name('prod.request.fetchUpdates');
        });

        // Duplikat grup di atas khusus pakai kata 'production' buat jaga-jaga kalau sistem lu nge-redirect paksa ke kata lengkap
        Route::prefix('production/request')->group(function () {
            Route::post('/store', [RequestProdController::class, 'store']);
        });

        Route::get('/prod/overview', [ProductionOverviewController::class, 'index'])->name('prod.overview');
        
        // 🛠️ SEKTOR SINKRONISASI PRODUCTION (IN & OUT MANAGEMENT)
        Route::prefix('prod/transaction')->name('prod.transaction.')->group(function () {
            Route::get('/in', [InProdController::class, 'stockIn'])->name('in');
            Route::get('/get-eng-detail/{id}', [InProdController::class, 'getEngineeringDetail'])->name('get_eng_detail');
            Route::get('/in/manual', [InProdController::class, 'manualIn'])->name('in.manual');
            Route::post('/in/manual/store', [InProdController::class, 'storeManualIn'])->name('in.store_manual');
            Route::post('/store', [InProdController::class, 'store'])->name('store'); 

            Route::get('/out', [OutProdController::class, 'stockOut'])->name('out');
            Route::post('/out/manual/store', [OutProdController::class, 'storeManualOut'])->name('out.manual.store');
            Route::get('/out/detail/{id}', [OutProdController::class, 'getInProductionDetail']); 
        });
        
        Route::get('/production-dashboard', function () { return view('dashboard'); })->name('production.dashboard');
    });
    
    // --- SHARED / GLOBAL ---
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    
    Route::controller(StockEngineeringController::class)->group(function () {
        Route::get('/stock-engineering', 'index')->name('stock.eng.index');
        Route::post('/stock-engineering', 'store')->name('stock.eng.store');
        Route::put('/stock-engineering/{id}', 'update')->name('stock.eng.update');
        Route::delete('/stock-engineering/{id}', 'destroy')->name('stock.eng.destroy');
        Route::get('/stock-engineering-export', 'export')->name('stock.eng.export');
        Route::post('/rak-store', 'storeRak')->name('rak.store');
        Route::post('/eng/in/update', 'updateStockIn')->name('stock.eng.in.update');
    });

    // --- MODUL UTAMA STOCK IN ENGINEERING ---
    Route::get('/eng/in', [StockInEngineeringController::class, 'index'])->name('eng.in');
    Route::get('/eng/in/manual', [StockInEngineeringController::class, 'manual'])->name('eng.in.manual'); 
    Route::get('/eng/in/scan', [StockInEngineeringController::class, 'scan'])->name('eng.in.scan');
    Route::post('/eng/in/store', [StockInEngineeringController::class, 'store'])->name('eng.in.store');
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // RUTE APPROVAL HISTORY & PREVIEW JALUR ASLI BLADE LO
    Route::get('/approval/history', [HistoryApprovalController::class, 'index'])->name('approval.history');
    Route::get('/engineering/approval-history/preview/{id}', [HistoryApprovalController::class, 'preview'])->name('approval.history.preview');
    Route::delete('/approval/history/{id}', [HistoryApprovalController::class, 'destroy'])->name('approval.history.destroy');
    
});