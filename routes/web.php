<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EngineeringOverviewController;
use App\Http\Controllers\Engineering\ListSparepartEngController;
use App\Http\Controllers\Production\ProductionOverviewController;
use App\Http\Controllers\Production\InProdController;
use App\Http\Controllers\Costing\CostingOverviewController;
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
use App\Http\Controllers\Costing\ApprovalController;
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
        Route::post('/admin/users/store', [UserController::class, 'store'])->name('users.store');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- GRUP ENGINEERING ---
    Route::middleware('role:admin,engineering')->group(function () {
        Route::get('/eng/overview', [EngineeringOverviewController::class, 'index'])->name('engineering.overview');
        Route::get('/eng/list-sparepart', [ListSparepartEngController::class, 'index'])->name('eng.list');
        
        Route::prefix('eng')->group(function () {
            Route::get('/list-sparepart/export', [StockEngineeringController::class, 'export'])->name('list-sparepart.export');
            Route::get('/out/manual', [StockOutEngineeringController::class, 'manual'])->name('eng.out.manual');
            Route::get('/out/scan', [StockOutEngineeringController::class, 'scan'])->name('eng.out.scan');
            Route::post('/out/store', [StockOutEngineeringController::class, 'store'])->name('eng.out.store');
            Route::resource('list-sparepart', ListSparepartEngController::class);
        });

        Route::get('/eng/approval', [ApprovalEngController::class, 'index'])->name('eng.approval');
        Route::get('/eng/approval/review/{id}', [ApprovalEngController::class, 'review'])->name('eng.approval.review');
        Route::post('/eng/approval/approve/{id}', [ApprovalEngController::class, 'approve'])->name('eng.approval.approve');
        Route::post('/eng/approval/reject/{id}', [ApprovalEngController::class, 'reject'])->name('eng.approval.reject');    
        
        // ROUTE PURCHASE REQUEST & HISTORY
        Route::get('/eng/purchase-request', [PurchaseRequestEngController::class, 'index'])->name('eng.pr.index');
        Route::post('/eng/purchase-request', [PurchaseRequestEngController::class, 'store'])->name('purchase.request.store'); 

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

        // 🚀 MAP ROUTE TRANSACTION (MENGGUNAKAN TRANSACTION CONTROLLER)
        Route::prefix('stock-eng/transaction')->name('stock_eng.transaction.')->group(function () {
            Route::get('/in', [TransactionController::class, 'indexIn'])->name('in');
            Route::get('/out', [TransactionController::class, 'indexOut'])->name('out');
            
            // --- BAGIAN RETURN ---
            Route::get('/return', [TransactionController::class, 'indexReturn'])->name('return');
            
            // 🌟 RUTE VIEW FORM MANUAL RETURN BIAR ENGGAK ROUTE NOT FOUND LAGI
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
        Route::get('/prod/request/list', [RequestProdController::class, 'listRequest'])->name('prod.request.list');
        Route::get('/prod/request/create', [RequestProdController::class, 'create'])->name('prod.request.create');
        Route::post('/prod/request/store', [RequestProdController::class, 'store'])->name('prod.request.store');
        Route::get('/prod/request/draft/{id}', [RequestProdController::class, 'editDraft'])->name('prod.request.editDraft');
        Route::put('/prod/request/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('prod.request.updateDraft');
        Route::put('/prod/request/update/{id}', [RequestProdController::class, 'update'])->name('prod.request.update');
        Route::get('/prod/request/preview/{id}', [RequestProdController::class, 'preview'])->name('prod.request.preview');
        Route::delete('/prod/request/delete/{id}', [RequestProdController::class, 'destroy'])->name('prod.request.destroy');
        Route::get('/prod/request/fetch-updates', [RequestProdController::class, 'fetchUpdates'])->name('prod.request.fetchUpdates');
        Route::get('/prod/overview', [ProductionOverviewController::class, 'index'])->name('prod.overview');
        
        Route::prefix('prod/transaction')->name('prod.transaction.')->group(function () {
            Route::get('/in', [InProdController::class, 'stockIn'])->name('in');
            Route::get('/out', [InProdController::class, 'stockOut'])->name('out');
            Route::post('/store', [InProdController::class, 'store'])->name('store');
        });
        
        Route::get('/production-dashboard', function () { return view('dashboard'); })->name('production.dashboard');
    });

    // --- GRUP COSTING ---
    Route::middleware('role:admin,costing')->group(function () {
        Route::get('/costing/overview', [CostingOverviewController::class, 'index'])->name('costing.overview');
        Route::get('/costing/incoming-pr', [ApprovalController::class, 'index'])->name('costing.pr.index');
        Route::put('/costing/incoming-pr/{id}/approve', [ApprovalController::class, 'approve'])->name('costing.pr.approve');
        Route::put('/costing/incoming-pr/{id}/reject', [ApprovalController::class, 'reject'])->name('costing.pr.reject');
        Route::get('/costing/material-received', [CostingOverviewController::class, 'materialReceived'])->name('costing.material.received');
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
        Route::get('/eng/in/scan', 'inScan')->name('eng.in.scan');
        Route::get('/eng/in/manual', 'inManual')->name('eng.in.manual');
        Route::post('/eng/in/update', 'updateStockIn')->name('stock.eng.in.update');
    });

    Route::get('/eng/in', [StockInEngineeringController::class, 'index'])->name('eng.in');
    Route::post('/eng/in/store', [StockInEngineeringController::class, 'store'])->name('eng.in.store');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/approval/history', [HistoryApprovalController::class, 'index'])->name('approval.history');
    Route::delete('/approval/history/{id}', [HistoryApprovalController::class, 'destroy'])->name('approval.history.destroy');
    
});