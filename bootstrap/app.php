<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route; // Wajib diimport biar Route::middleware jalan aman, coy!

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        // Tambahan method 'then' untuk ngeload semua route custom terpisah lu
        then: function () {
            // 1. Route Custom Production (Bawaan lu yang sudah ada)
            Route::middleware('web')
                ->group(__DIR__.'/../routes/production/production.php');

            // 2. Route Custom Engineering untuk Add Rak (Bawaan lu yang sudah ada)
            Route::middleware('web')
                ->group(__DIR__.'/../routes/engineering/add_rak.php');

            // 3. 🚀 UPDATE BARU: Route Custom Engineering untuk Stock Out Activities
            Route::middleware('web')
                ->group(__DIR__.'/../routes/engineering/stock_out.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();