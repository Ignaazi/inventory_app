<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EngineeringOverviewController;
use App\Http\Controllers\Engineering\TransactionController;
use App\Http\Controllers\Engineering\ListSparepartEngController;
use App\Http\Controllers\Production\ProductionOverviewController;
use App\Http\Controllers\Production\TransactionProdController;
use App\Http\Controllers\Costing\CostingOverviewController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Costing\ApprovalController;
use App\Http\Controllers\StockEngineeringController;
use App\Http\Controllers\StockInEngineeringController;
use App\Http\Controllers\EngOverview\BarcodeParsingController;
use App\Http\Controllers\EngOverview\DbBarcodeController;
use App\Http\Controllers\EngOverview\TypeBarcodeController;
use App\Http\Controllers\Engineering\ApprovalEngController;
use App\Http\Controllers\Engineering\HistoryApprovalController;
use App\Http\Controllers\Production\RequestProdController;

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

    // --- SIDEBAR ROUTES ---
    Route::get('/eng/overview', [EngineeringOverviewController::class, 'index'])->name('engineering.overview');
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // --- LIST SPAREPART ---
    Route::get('/eng/list-sparepart', [ListSparepartEngController::class, 'index'])->name('eng.list');

    // --- ENGINEERING TRANSACTIONS ---
    Route::prefix('eng')->group(function () {
        Route::get('/out', [TransactionController::class, 'indexOut'])->name('eng.out');
        Route::get('/disposal', [TransactionController::class, 'indexDisposal'])->name('eng.disposal');
        Route::get('/list-sparepart/export', [App\Http\Controllers\StockEngineeringController::class, 'export'])
         ->name('list-sparepart.export');
    });
    
    // A. SISI ENGINEERING - APPROVAL SYSTEM
    Route::get('/eng/approval', [ApprovalEngController::class, 'index'])->name('eng.approval');
    Route::get('/eng/approval/review/{id}', [ApprovalEngController::class, 'review'])->name('eng.approval.review');
    Route::post('/eng/approval/approve/{id}', [ApprovalEngController::class, 'approve'])->name('eng.approval.approve');
    Route::post('/eng/approval/reject/{id}', [ApprovalEngController::class, 'reject'])->name('eng.approval.reject');    
    
    // B. SISI PRODUCTION - REQUEST NOZZLE SYSTEM
    Route::get('/prod/request/list', [RequestProdController::class, 'listRequest'])->name('prod.request.list');
    Route::get('/prod/request/create', [RequestProdController::class, 'create'])->name('prod.request.create');
    Route::post('/prod/request/store', [RequestProdController::class, 'store'])->name('prod.request.store');
    Route::get('/prod/request/draft/{id}', [RequestProdController::class, 'editDraft'])->name('prod.request.editDraft');
    Route::put('/prod/request/draft/{id}/update', [RequestProdController::class, 'updateDraft'])->name('prod.request.updateDraft');
    Route::put('/prod/request/update/{id}', [RequestProdController::class, 'update'])->name('prod.request.update');
    Route::get('/prod/request/preview/{id}', [RequestProdController::class, 'preview'])->name('prod.request.preview');
    Route::delete('/prod/request/delete/{id}', [RequestProdController::class, 'destroy'])->name('prod.request.destroy');
    
    // 🔥 ROUTE BARU: Endpoint API JSON Real-time untuk Polling di Halaman Blade
    Route::get('/prod/request/fetch-updates', [RequestProdController::class, 'fetchUpdates'])->name('prod.request.fetchUpdates');

    // --- PURCHASE REQUEST ---
    Route::get('/eng/purchase-request', [App\Http\Controllers\Engineering\PurchaseRequestEngController::class, 'index'])->name('eng.pr.index');

    // --- PRODUCTION OVERVIEW ---
    Route::get('/prod/overview', [ProductionOverviewController::class, 'index'])->name('prod.overview');

    // --- COSTING SYSTEM ROUTES ---
    Route::get('/costing/overview', [CostingOverviewController::class, 'index'])->name('costing.overview');
    Route::get('/costing/incoming-pr', [CostingOverviewController::class, 'incomingPr'])->name('costing.incoming.pr');
    Route::get('/costing/material-received', [CostingOverviewController::class, 'materialReceived'])->name('costing.material.received');

    // Production Transaction
    Route::prefix('prod/transaction')->name('prod.transaction.')->group(function () {
        Route::get('/in', [TransactionProdController::class, 'stockIn'])->name('in');
        Route::get('/out', [TransactionProdController::class, 'stockOut'])->name('out');
        Route::post('/store', [TransactionProdController::class, 'store'])->name('store');
    });
    
    // --- MODULE LAINNYA ---
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/production-dashboard', function () { return view('dashboard'); })->name('production.dashboard');

    // Barcode Parsing
    Route::get('/eng/barcode-parsing', [BarcodeParsingController::class, 'index'])->name('barcode.parsing.index');
    Route::post('/eng/barcode-scan', [BarcodeParsingController::class, 'scan'])->name('barcode.parsing.scan');

    Route::prefix('eng')->group(function () {
        Route::resource('list-sparepart', ListSparepartEngController::class);
    });
    
    // STOCK ENGINEERING
    Route::controller(StockEngineeringController::class)->group(function () {
        Route::get('/stock-engineering', 'index')->name('stock.eng.index');
        Route::post('/stock-engineering', 'store')->name('stock.eng.store');
        Route::put('/stock-engineering/{id}', 'update')->name('stock.eng.update');
        Route::delete('/stock-engineering/{id}', 'destroy')->name('stock.eng.destroy');
        Route::get('/stock-engineering-export', 'export')->name('stock.eng.export');
        Route::post('/rak-store', 'storeRak')->name('rak.store');

        Route::get('/eng/in', 'indexIn')->name('eng.in'); 
        Route::get('/eng/in/scan', 'inScan')->name('eng.in.scan');
        Route::get('/eng/in/manual', 'inManual')->name('eng.in.manual');
        Route::post('/eng/in/update', 'updateStockIn')->name('stock.eng.in.update');
    });
    
    Route::get('/eng/in', [StockInEngineeringController::class, 'index'])->name('eng.in');
    Route::post('/eng/in/store', [StockInEngineeringController::class, 'store'])->name('eng.in.store');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::prefix('eng-overview')->group(function () {
        Route::get('/barcode-parsing', [BarcodeParsingController::class, 'index'])->name('barcode.parsing');
        Route::post('/barcode-parsing/store', [BarcodeParsingController::class, 'store']);
        Route::get('/barcode-parsing/get-configs', [BarcodeParsingController::class, 'getConfigs']);
    
        Route::get('/db-barcode', [DbBarcodeController::class, 'index'])->name('barcode.db');
        Route::delete('/db-barcode/{id}', [DbBarcodeController::class, 'destroy'])->name('barcode.db.delete');
    
        Route::get('/type-barcode', [TypeBarcodeController::class, 'index'])->name('barcode.type');
        Route::delete('/type-barcode/{id}', [TypeBarcodeController::class, 'destroy'])->name('barcode.type.delete');
    });
    
    // --- ROUTE HISTORY APPROVAL (FIXED) ---
    Route::get('/approval/history', [HistoryApprovalController::class, 'index'])->name('approval.history');
    Route::delete('/approval/history/{id}', [HistoryApprovalController::class, 'destroy'])->name('approval.history.destroy');
    
});