<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Engineering\StockInEngineeringController;
use App\Http\Controllers\Engineering\StockOutEngineeringController;
use App\Http\Controllers\Engineering\StockReturnEngineeringController;
use App\Http\Controllers\Engineering\DisposalEngineeringController;
use App\Http\Controllers\Production\InProdController;
use App\Http\Controllers\Production\OutProdController;
use App\Http\Controllers\Production\ReturnProdController;
use App\Http\Controllers\Api\TransactionHistoryController;

Route::group(['middleware' => ['api', 'json']], function () {
    Route::post('/login', [AuthController::class, 'loginMobile']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/users', [AuthController::class, 'getAllUsers']);

        // Alias untuk client mobile yang sudah memakai endpoint API sebelumnya.
        Route::post('/engineering/stock-out', [StockOutEngineeringController::class, 'store'])
            ->middleware('role:admin,engineering');
        Route::post('/engineering/transactions/disposal', [DisposalEngineeringController::class, 'processScan'])
            ->middleware('role:admin,engineering');
        Route::get('/engineering/transactions/history', [TransactionHistoryController::class, 'engineering'])
            ->middleware('role:admin,engineering');

        Route::middleware('role:admin,engineering')
            ->prefix('engineering/transactions')
            ->group(function () {
                Route::post('/in', [StockInEngineeringController::class, 'store']);
                Route::post('/out', [StockOutEngineeringController::class, 'store']);
                Route::post('/return', [StockReturnEngineeringController::class, 'store']);
            });

        Route::middleware('role:admin,production')
            ->prefix('production/transactions')
            ->group(function () {
                Route::post('/in', [InProdController::class, 'store']);
                Route::post('/out', [OutProdController::class, 'storeScanOut']);
                Route::post('/return', [ReturnProdController::class, 'storeScanReturn']);
            });

        Route::get('/production/transactions/history', [TransactionHistoryController::class, 'production'])
            ->middleware('role:admin,production');
    });
});
