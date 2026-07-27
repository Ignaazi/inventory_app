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

            // 3. Route Custom Engineering untuk Stock Out Activities (Bawaan lu yang sudah ada)
            Route::middleware('web')
                ->group(__DIR__.'/../routes/engineering/stock_out.php');

            // 4. 🛠️ NEW: Route Custom Engineering untuk Material Received Dropdown Module
            Route::middleware('web')
                ->group(__DIR__.'/../routes/engineering/eng_list_material_received.php');

            // 5. Route Master Data Line Production (Disamakan jadi huruf kecil 'production')
            Route::middleware('web')
                ->group(__DIR__.'/../routes/production/list_line_production.php');

            // 6. Route Core Stock Management Production 
            Route::middleware('web')
                ->group(__DIR__.'/../routes/production/stock_prod.php');

            // 7. 💸 UPDATE MODULAR COSTING & COATING RECEIVING: Load Modular Costing Routes Per File
            Route::middleware(['web', 'auth'])
                ->prefix('costing')
                ->group(function () {
                    require __DIR__.'/../routes/costing/overviewCosting.php';
                    require __DIR__.'/../routes/costing/incomingPrCosting.php';
                    require __DIR__.'/../routes/costing/materialReceivedCosting.php'; // Di sini mencakup route form serah terima coating
                });

            // 8. 🌐 UPDATE UTAMA API MOBILE: Mendaftarkan file routes/api.php agar dibaca sistem
            Route::middleware('api')
                ->prefix('api') // Menambahkan prefix 'api/' secara otomatis di depan rute
                ->group(__DIR__.'/../routes/api.php');
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Mendaftarkan alias 'role' agar bisa dipakai di route dengan middleware('role:...')
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();