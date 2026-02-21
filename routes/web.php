<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StockEngController;
use App\Http\Controllers\ProfileController; // 1. Tambahkan import ini
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
    
    // Dashboard Utama (Admin)
    Route::get('/admin', function () {
        return view('admin'); 
    })->name('dashboard'); // Tetap pakai name 'dashboard' sesuai request lo

    // --- Module: Profile (Fitur Edit Foto & Nama) ---
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::prefix('admin/users')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('users.index');
        Route::post('/store', [UserController::class, 'store'])->name('users.store');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // --- Module: Stock Engineering (RAK System) ---
    Route::prefix('stock-engineering')->group(function () {
        Route::get('/', [StockEngController::class, 'index'])->name('stock.eng.index');
        Route::post('/store', [StockEngController::class, 'store'])->name('stock.eng.store');
    });

    // --- Module: Production ---
    Route::get('/production-dashboard', function () {
        return view('dashboard'); 
    })->name('production.dashboard');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});