<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Production\StockProdController;

// Route Utama Tampilan Stok Produksi
Route::get('/prod/stock', [StockProdController::class, 'index'])->name('stock.prod.index');

// Route Actions (Request, Receive, Export)
Route::post('/prod/stock/request', [StockProdController::class, 'storeRequest'])->name('stock.prod.request.store');
Route::post('/prod/stock/receive', [StockProdController::class, 'receiveItem'])->name('stock.prod.receive');
Route::get('/prod/stock/export', [StockProdController::class, 'exportCSV'])->name('stock.prod.export');

// Route untuk View Request History (Biar Tombol di View Nggak Eror)
Route::get('/prod/stock/request-history', [StockProdController::class, 'requestHistory'])->name('stock.prod.request.history');

// UPDATE BARU: Route CRUD Actions (Edit & Delete)
Route::put('/prod/stock/{id}', [StockProdController::class, 'update'])->name('stock.prod.update');
Route::delete('/prod/stock/{id}', [StockProdController::class, 'destroy'])->name('stock.prod.destroy');