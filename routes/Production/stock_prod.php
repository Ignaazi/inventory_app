<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Production\StockProdController;

// Route Utama Tampilan Stok Produksi
Route::get('/prod/stock', [StockProdController::class, 'index'])->name('stock.prod.index');

// Route Actions (Request, Receive, Export)
Route::post('/prod/stock/request', [StockProdController::class, 'storeRequest'])->name('stock.prod.request.store');
Route::post('/prod/stock/receive', [StockProdController::class, 'receiveItem'])->name('stock.prod.receive');
// Route untuk Export CSV di Production
Route::get('/prod/stock/export-csv', [App\Http\Controllers\Production\StockProdController::class, 'exportCSV'])->name('stock.prod.export.csv');

// Route untuk View Request History (Biar Tombol di View Nggak Eror)
Route::get('/prod/stock/request-history', [StockProdController::class, 'requestHistory'])->name('stock.prod.request.history');

// UPDATE BARU: Route CRUD Actions (Edit & Delete)
Route::put('/prod/stock/{id}', [StockProdController::class, 'update'])->name('stock.prod.update');
Route::delete('/prod/stock/{id}', [StockProdController::class, 'destroy'])->name('stock.prod.destroy');

// Route untuk menambahkan lini baru (ADD LINE)
Route::post('/prod/stock/line-store', [StockProdController::class, 'lineStore'])->name('stock.prod.line.store');

// Route untuk menerima nozzle via token Engineering (ADD NOZZLE IN)
Route::post('/prod/stock/nozzle-store', [StockProdController::class, 'nozzleStore'])->name('stock.prod.nozzle.store');