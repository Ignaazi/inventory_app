<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EngineeringOverviewController;
use App\Http\Controllers\Engineering\ListSparepartEngController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockEngineeringController;
use App\Http\Controllers\Engineering\StockInEngineeringController;
use App\Http\Controllers\EngOverview\BarcodeParsingController;
use App\Http\Controllers\EngOverview\DbBarcodeController;
use App\Http\Controllers\EngOverview\TypeBarcodeController;
use App\Http\Controllers\Engineering\ApprovalEngController;
use App\Http\Controllers\Engineering\HistoryApprovalController;
use App\Http\Controllers\Engineering\StockOutEngineeringController;
use App\Http\Controllers\Engineering\PurchaseRequestEngController;
use App\Http\Controllers\Engineering\PurchaseRequestHistoryEngController;
use App\Http\Controllers\Engineering\TransactionController;

// 🎯 SPLIT ARCHITECTURE CONTROLLER DISPOSAL
use App\Http\Controllers\Engineering\TransactionDisposalController;
use App\Http\Controllers\Engineering\DisposalEngineeringController;

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
            
            Route::resource('list-sparepart', ListSparepartEngController::class);
        });

        // 🛠️ MODUL UTAMA STOCK IN ENGINEERING (Amankan di dalam role engineering)
        Route::get('/eng/in', [StockInEngineeringController::class, 'index'])->name('eng.in');
        Route::get('/eng/in/index', [StockInEngineeringController::class, 'index'])->name('eng.in.index'); // Alias pendukung view baru
        Route::get('/eng/in/manual', [StockInEngineeringController::class, 'manual'])->name('eng.in.manual'); 
        Route::get('/eng/in/scan', [StockInEngineeringController::class, 'scan'])->name('eng.in.scan');
        Route::post('/eng/in/store', [StockInEngineeringController::class, 'store'])->name('eng.in.store');
        Route::post('/eng/in/scan/store', [StockInEngineeringController::class, 'storeScan'])->name('eng.in.scan.store'); // Handler submit scanner

        Route::get('/eng/approval', [ApprovalEngController::class, 'index'])->name('eng.approval');
        Route::get('/eng/approval/review/{id}', [ApprovalEngController::class, 'review'])->name('eng.approval.review');
        Route::post('/eng/approval/approve/{id}', [ApprovalEngController::class, 'approve'])->name('eng.approval.approve');
        Route::post('/eng/approval/reject/{id}', [ApprovalEngController::class, 'reject'])->name('eng.approval.reject');    
        
        // ROUTE PURCHASE REQUEST & FLOW VERIFIKASI
        Route::get('/eng/purchase-request', [PurchaseRequestEngController::class, 'index'])->name('purchase.request.index');
        Route::post('/eng/purchase-request', [PurchaseRequestEngController::class, 'store'])->name('purchase.request.store'); 

        Route::get('/eng/purchase-request/list', [PurchaseRequestEngController::class, 'listRequests'])->name('purchase.request.list');
        Route::post('/eng/purchase-request/{id}/reject', [PurchaseRequestEngController::class, 'rejectRequest'])->name('purchase.request.reject');

        Route::get('/eng/purchase-request/{id}/check', [PurchaseRequestEngController::class, 'checkedView'])->name('purchase.request.checked.view');
        Route::post('/eng/purchase-request/{id}/check-confirm', [PurchaseRequestEngController::class, 'checkRequest'])->name('purchase.request.check');

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
            Route::get('/barcode-parsing-in', [BarcodeParsingController::class, 'indexIn'])->name('barcode.parsing.in');
            Route::post('/barcode-parsing', [BarcodeParsingController::class, 'store'])->name('barcode.parsing.store');
            Route::post('/barcode-parsing/store', [BarcodeParsingController::class, 'store']);
            Route::get('/barcode-parsing/get-configs', [BarcodeParsingController::class, 'getConfigs']);
            Route::get('/db-barcode', [DbBarcodeController::class, 'index'])->name('barcode.db');
            Route::delete('/db-barcode/{id}', [DbBarcodeController::class, 'destroy'])->name('barcode.db.delete');
            Route::get('/type-barcode', [TypeBarcodeController::class, 'index'])->name('barcode.type');
            Route::delete('/type-barcode/{id}', [TypeBarcodeController::class, 'destroy'])->name('barcode.type.delete');
        });

        // MAP ROUTE TRANSACTION (MENGGUNAKAN TRANSACTION CONTROLLER & DISPOSAL CONTROLLER BARU)
        Route::prefix('stock-eng/transaction')->name('stock_eng.transaction.')->group(function () {
            Route::get('/in', [TransactionController::class, 'indexIn'])->name('in');
            Route::get('/out', [TransactionController::class, 'indexOut'])->name('out');
            
            Route::get('/return', [TransactionController::class, 'indexReturn'])->name('return');
            Route::get('/return/manual', function () {
                $stocks = \App\Models\StockEng::all(); 
                $raks = \App\Models\Rak::all(); 
                return view('stock_eng.transaction.return_manual', compact('stocks', 'raks'));
            })->name('return.manual');

            Route::post('/return/store', [TransactionController::class, 'storeReturn'])->name('return.store');
            
            // 🛠️ FIX SYSTEM: Pembaruan rute terarah split arsitektur modul Disposal Engineering
            Route::get('/disposal', [TransactionDisposalController::class, 'index'])->name('disposal');
            Route::get('/disposal/scan', [DisposalEngineeringController::class, 'scanView'])->name('disposal.scan');
            Route::post('/disposal/scan/process', [DisposalEngineeringController::class, 'processScan'])->name('disposal.scan.process');
        });
    });
    
    // --- 🚀 GRUP PRODUCTION ---
    Route::middleware('role:admin,production')->group(function () {
        require base_path('routes/Production/transaction.php');
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
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // RUTE APPROVAL HISTORY & PREVIEW
    Route::get('/approval/history', [HistoryApprovalController::class, 'index'])->name('approval.history');
    Route::get('/engineering/approval-history/preview/{id}', [HistoryApprovalController::class, 'preview'])->name('approval.history.preview');
    Route::delete('/approval/history/{id}', [HistoryApprovalController::class, 'destroy'])->name('approval.history.destroy');
    
});