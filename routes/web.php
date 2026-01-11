<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
// use App\Http\Controllers\ProductionLineController; // Siapkan untuk nanti
// use App\Http\Controllers\ScannerController;        // Siapkan untuk nanti
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
    
    // Dashboard Utama
    Route::get('/admin', function () {
        return view('admin');
    })->name('dashboard');

    // --- Module: Management User ---
    Route::prefix('users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- Module: Production Line (Persiapan) ---
    // Route::get('/production-line', [ProductionLineController::class, 'index'])->name('line.index');
    
    // --- Module: Scanner Check (Persiapan) ---
    // Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner.index');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});