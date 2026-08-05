<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController; 
// 1. TAMBAHKAN IMPORT CONTROLLER BAWAAN KAMU DI SINI:
use App\Http\Controllers\Engineering\StockOutEngineeringController;

Route::group(['middleware' => ['api']], function () {
    Route::post('/login', [AuthController::class, 'loginMobile']);
    Route::get('/users', [AuthController::class, 'getAllUsers']);

    // 2. TAMBAHKAN ROUTE TRANSACTION OUT INI:
    Route::post('/engineering/stock-out', [StockOutEngineeringController::class, 'store']);
});