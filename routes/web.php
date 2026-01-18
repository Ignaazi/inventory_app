<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockEngController; // Controller baru kita
// use App\Http\Controllers\ProductionLineController; 
// use App\Http\Controllers\ScannerController;        

use Illuminate\Support\Facades\Route;

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
    
    // Dashboard Utama (Sekarang mengarah ke View Dashboard Overview)
    Route::get('/admin', function () {
        return view('admin'); // Pastikan file admin.blade.php sudah berisi kodingan "Dashboard Overview"
    })->name('dashboard');

    // --- Module: Management User ---
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- Module: Stock Engineering (RAK System) ---
    Route::prefix('stock-engineering')->group(function () {
        Route::get('/', [StockEngController::class, 'index'])->name('stock.eng.index');
        Route::post('/store', [StockEngController::class, 'store'])->name('stock.eng.store');
        Route::put('/{id}', [StockEngController::class, 'update'])->name('stock.eng.update');
        Route::delete('/{id}', [StockEngController::class, 'destroy'])->name('stock.eng.destroy');
    });
// Pastikan di web.php ada 'name' nya seperti ini:
Route::get('/stock-engineering', [StockEngController::class, 'index'])->name('stock.eng.index');
Route::post('/stock-engineering', [StockEngController::class, 'store'])->name('stock.eng.store');
    // --- Module: Production Line (Persiapan) ---
    // Route::get('/production-line', [ProductionLineController::class, 'index'])->name('line.index');
    
    // --- Module: Scanner Check (Persiapan) ---
    // Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});