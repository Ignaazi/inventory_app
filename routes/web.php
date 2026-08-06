<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EngineeringOverviewController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\StockEngineeringController;
use App\Http\Controllers\Engineering\ListSparepartEngController;
use App\Http\Controllers\Engineering\StockInEngineeringController;
use App\Http\Controllers\Engineering\StockOutEngineeringController;
use App\Http\Controllers\Engineering\StockReturnEngineeringController;
use App\Http\Controllers\Engineering\ApprovalEngController;
use App\Http\Controllers\Engineering\HistoryApprovalController;
use App\Http\Controllers\Engineering\PurchaseRequestEngController;
use App\Http\Controllers\Engineering\PurchaseRequestHistoryEngController;
use App\Http\Controllers\Engineering\TransactionController;
use App\Http\Controllers\Engineering\DisposalEngineeringController;
use App\Http\Controllers\EngOverview\BarcodeParsingController;
use App\Http\Controllers\EngOverview\DbBarcodeController;

// Import Controller Costing Section
use App\Http\Controllers\Costing\ApprovalController as CostingApprovalController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. Redirect Halaman Utama
Route::get('/', function () {
    return redirect('/login');
});

// 2. Grup Route Guest (Belum Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 3. Grup Route Auth (Sudah Login)
Route::middleware('auth')->group(function () {

    // Dashboard Utama
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // --- GRUP ADMIN (Full Access) ---
    Route::middleware('role:admin')->group(function () {
        Route::controller(UserController::class)->prefix('admin/users')->name('users.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{user}', 'update')->name('update');
            Route::delete('/{user}', 'destroy')->name('destroy');
        });
    });

    // --- GRUP ENGINEERING ---
    Route::middleware('role:admin,engineering')->group(function () {

        // Modul Utama Engineering (/eng/...)
        Route::prefix('eng')->group(function () {

            // 🔥 Engineering Overview Dashboard Page
            Route::get('/overview', [EngineeringOverviewController::class, 'index'])->name('eng.overview');

            // Sparepart
            Route::get('/list-sparepart/export', [StockEngineeringController::class, 'export'])->name('list-sparepart.export');
            Route::resource('list-sparepart', ListSparepartEngController::class);

            // Stock Out
            Route::controller(StockOutEngineeringController::class)->prefix('out')->name('eng.out.')->group(function () {
                Route::get('/manual', 'manual')->name('manual');
                Route::get('/scan', 'scan')->name('scan');
                Route::post('/store', 'store')->name('store');
            });

            // Stock In
            Route::controller(StockInEngineeringController::class)->prefix('in')->name('eng.in')->group(function () {
                Route::get('/', 'index');
                Route::get('/index', 'index')->name('.index');
                Route::get('/manual', 'manual')->name('.manual');
                Route::get('/scan', 'scan')->name('.scan');
                Route::post('/store', 'store')->name('.store');
                Route::post('/scan/store', 'storeScan')->name('.scan.store');
            });

            // Stock Return (Menggunakan StockReturnEngineeringController)
            Route::controller(StockReturnEngineeringController::class)->prefix('return')->name('eng.return.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/scan', 'scan')->name('scan');
                Route::post('/store', 'store')->name('store');
            });

            // Approval Engineering
            Route::controller(ApprovalEngController::class)->prefix('approval')->name('eng.approval')->group(function () {
                Route::get('/', 'index');
                Route::get('/review/{id}', 'review')->name('.review');
                Route::post('/approve/{id}', 'approve')->name('.approve');
                Route::post('/reject/{id}', 'reject')->name('.reject');
            });

            // Purchase Request & Verification Flow
            Route::prefix('purchase-request')->name('purchase.request.')->group(function () {
                
                // RUTE STATIS (Harus di atas)
                Route::controller(PurchaseRequestEngController::class)->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/', 'store')->name('store');
                    Route::get('/list', 'listRequests')->name('list');
                });

                Route::controller(PurchaseRequestHistoryEngController::class)->group(function () {
                    Route::get('/history', 'index')->name('history');
                });

                // RUTE DINAMIS DENGAN PARAMETER {id}
                Route::controller(PurchaseRequestEngController::class)->group(function () {
                    Route::get('/{id}/check', 'checkedView')->name('checked.view');
                    Route::post('/{id}/check-confirm', 'checkRequest')->name('check');
                    Route::post('/{id}/reject', 'rejectRequest')->name('reject');
                });

                Route::controller(PurchaseRequestHistoryEngController::class)->group(function () {
                    Route::get('/{id}/preview', 'preview')->name('preview');
                    Route::get('/{id}/edit', 'edit')->name('edit');
                    Route::put('/{id}/update', 'update')->name('update');
                    Route::delete('/{id}/delete', 'destroy')->name('delete');
                });
            });

            // Barcode Parsing Single Route
            Route::controller(BarcodeParsingController::class)->group(function () {
                Route::get('/barcode-parsing', 'index')->name('barcode.parsing.index');
                Route::post('/barcode-scan', 'scan')->name('barcode.parsing.scan');
            });
        });

        // Modul Engineering Overview (/eng-overview/...)
        Route::prefix('eng-overview')->group(function () {
            // 🔥 Direct URL Access ke Dashboard Overview jika diakses melalui /eng-overview
            Route::get('/', [EngineeringOverviewController::class, 'index'])->name('eng.overview.direct');

            Route::controller(BarcodeParsingController::class)->group(function () {
                Route::get('/barcode-parsing', 'index')->name('barcode.parsing');
                Route::get('/barcode-parsing-in', 'indexIn')->name('barcode.parsing.in');
                Route::post('/barcode-parsing', 'store')->name('barcode.parsing.store');
                Route::post('/barcode-parsing/store', 'store')->name('barcode.store');
                Route::post('/barcode-parsing/store-batch-in', 'storeBatchIn')->name('barcode.store.batch.in');
                Route::post('/barcode-parsing/store-inbound', 'storeInbound')->name('barcode.store.inbound');
                Route::get('/barcode-parsing/get-last-counter', 'getLastCounter')->name('barcode.get.last.counter');
                Route::get('/barcode-parsing/get-configs', 'getConfigs');
            });

            Route::controller(DbBarcodeController::class)->prefix('db-barcode')->name('barcode.db')->group(function () {
                Route::get('/', 'index');
                Route::delete('/{id}', 'destroy')->name('.delete');
            });
        });

        // Modul Stock Transaction & Disposal (/stock-eng/transaction/...)
        Route::prefix('stock-eng/transaction')->name('stock_eng.transaction.')->group(function () {
            
            // Transaksi IN & OUT
            Route::controller(TransactionController::class)->group(function () {
                Route::get('/in', 'indexIn')->name('in');
                Route::get('/out', 'indexOut')->name('out');
            });

            // Transaksi RETURN Log & Executions
            Route::controller(StockReturnEngineeringController::class)->group(function () {
                Route::get('/return', 'index')->name('return');
                Route::get('/return/scan', 'scan')->name('return.scan');
                Route::post('/return/store', 'store')->name('return.store');
            });

            Route::get('/return/manual', function () {
                $stocks = \App\Models\StockEng::all();
                $raks = \App\Models\Rak::all();
                return view('stock_eng.transaction.return_manual', compact('stocks', 'raks'));
            })->name('return.manual');

            Route::get('/disposal', [DisposalEngineeringController::class, 'index'])->name('disposal');

            Route::controller(DisposalEngineeringController::class)->prefix('disposal')->name('disposal.')->group(function () {
                Route::get('/scan', 'scanView')->name('scan');
                Route::post('/scan/process', 'processScan')->name('scan.process');
            });
        });
    });

    // --- GRUP COSTING SECTION ---
    Route::middleware('role:admin,costing')->prefix('costing')->name('costing.pr.')->group(function () {
        Route::controller(CostingApprovalController::class)->group(function () {
            Route::get('/incoming-pr', 'index')->name('index');
            Route::get('/approve-pr/{id}', 'approveForm')->name('approve.form');
            Route::put('/approve-pr/{id}', 'approve')->name('approve');
            Route::get('/history-pr', 'history')->name('history');
            Route::get('/preview-approval/{id}', 'previewApproval')->name('preview_approval');
        });
    });

    // --- GRUP PRODUCTION ---
    Route::middleware('role:admin,production')->group(function () {
        require base_path('routes/Production/transaction.php');
    });

    // --- SHARED / GLOBAL (Auth) ---
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

    // History & Preview Approval Global
    Route::controller(HistoryApprovalController::class)->group(function () {
        Route::get('/approval/history', 'index')->name('approval.history');
        Route::get('/engineering/approval-history/preview/{id}', 'preview')->name('approval.history.preview');
        Route::delete('/approval/history/{id}', 'destroy')->name('approval.history.destroy');
    });
});
