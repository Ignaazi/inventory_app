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
use App\Http\Controllers\Costing\ApprovalController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BarcodeParsingController;
use App\Http\Controllers\StockEngineeringController;
use App\Http\Controllers\StockInEngineeringController;

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

    // --- SESUAI SIDEBAR ASLI: /eng/overview ---
    Route::get('/eng/overview', [EngineeringOverviewController::class, 'index'])->name('engineering.overview');

    // --- SESUAI SIDEBAR ASLI: /admin/users ---
    Route::get('/admin/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/admin/users/store', [UserController::class, 'store'])->name('users.store');
    Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/admin/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');


    // --- LIST SPAREPART (Controller Terpisah) ---
    Route::get('/eng/list-sparepart', [ListSparepartEngController::class, 'index'])->name('eng.list');

    // --- ENGINEERING TRANSACTIONS (IN, OUT, DISPOSAL) ---
    Route::prefix('eng')->group(function () {
        // Dinonaktifkan agar tidak bentrok dengan StockEngineeringController di bawah
        // Route::get('/in', [TransactionController::class, 'indexIn'])->name('eng.in'); 
        
        Route::get('/out', [TransactionController::class, 'indexOut'])->name('eng.out');
        Route::get('/disposal', [TransactionController::class, 'indexDisposal'])->name('eng.disposal');
    });

    // --- ADDED: APPROVAL SYSTEM (Baru) ---
    Route::get('/eng/approval', [App\Http\Controllers\Engineering\ApprovalEngController::class, 'index'])->name('eng.approval');
    
    // --- ADDED: PURCHASE REQUEST (Baru) ---
    Route::get('/eng/purchase-request', [App\Http\Controllers\Engineering\PurchaseRequestEngController::class, 'index'])->name('eng.pr.index');

    // --- PRODUCTION OVERVIEW ---
    Route::get('/prod/overview', [ProductionOverviewController::class, 'index'])->name('prod.overview');

    // --- ADDED: LIST SPAREPART PRODUCTION (Baru) ---
    Route::get('/prod/list-sparepart', [App\Http\Controllers\Production\ListSparepartProdController::class, 'index'])->name('prod.list');

    // --- ADDED: STOCK PRODUCTION (Baru) ---
    Route::get('/prod/stock', [App\Http\Controllers\Production\StockProdController::class, 'index'])->name('prod.stock.index');

    // --- ADDED: REQUEST SPAREPART PRODUCTION (Baru) ---
    Route::get('/prod/request', [App\Http\Controllers\Production\RequestProdController::class, 'index'])->name('prod.request.index');

    // Struktur Grouping Production Transaction
    Route::prefix('prod/transaction')->name('prod.transaction.')->group(function () {
        Route::get('/in', [TransactionProdController::class, 'stockIn'])->name('in');
        Route::get('/out', [TransactionProdController::class, 'stockOut'])->name('out');
        Route::post('/store', [TransactionProdController::class, 'store'])->name('store');
    });

    // --- COSTING MODULE ---
    Route::prefix('costing')->name('costing.')->group(function () {
        Route::get('/overview', [CostingOverviewController::class, 'index'])->name('overview');
        Route::get('/incoming-pr', [ApprovalController::class, 'index'])->name('incoming.pr');
        Route::post('/incoming-pr/{id}/action', [ApprovalController::class, 'update'])->name('incoming.action');
        Route::get('/material-received', [App\Http\Controllers\Costing\MaterialReceivedController::class, 'index'])->name('material.received');
    });

    // --- MODULE LAINNYA ---
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/production-dashboard', function () {
        return view('dashboard'); 
    })->name('production.dashboard');

    Route::get('/eng/barcode-parsing', [BarcodeParsingController::class, 'index'])->name('barcode.parsing.index');
    Route::post('/eng/barcode-scan', [BarcodeParsingController::class, 'scan'])->name('barcode.parsing.scan');

    Route::prefix('eng')->group(function () {
        Route::resource('list-sparepart', ListSparepartEngController::class);
    });
    
    // ROUTE GROUP UNTUK STOCK ENGINEERING
    Route::controller(StockEngineeringController::class)->group(function () {
        // Tampilan Utama Master Data
        Route::get('/stock-engineering', 'index')->name('stock.eng.index');
        Route::post('/stock-engineering', 'store')->name('stock.eng.store');
        Route::put('/stock-engineering/{id}', 'update')->name('stock.eng.update');
        Route::delete('/stock-engineering/{id}', 'destroy')->name('stock.eng.destroy');
        Route::get('/stock-engineering-export', 'export')->name('stock.eng.export');
        Route::post('/rak-store', 'storeRak')->name('rak.store');

        // --- SISTEM TRANSACTION IN (3 PAGES) ---
        // 1. Halaman History (Main Page menggunakan in.blade.php lo)
        Route::get('/eng/in', 'indexIn')->name('eng.in'); 
        
        // 2. Halaman Scan Mode (in_scan.blade.php)
        Route::get('/eng/in/scan', 'inScan')->name('eng.in.scan');
        
        // 3. Halaman Manual Mode (in_manual.blade.php)
        Route::get('/eng/in/manual', 'inManual')->name('eng.in.manual');

        // Proses Update Stok (Satu endpoint untuk semua mode input)
        Route::post('/eng/in/update', 'updateStockIn')->name('stock.eng.in.update');
    });
    
        Route::get('/eng/in', [StockInEngineeringController::class, 'index'])->name('eng.in');
        Route::post('/eng/in/store', [StockInEngineeringController::class, 'store'])->name('eng.in.store');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});