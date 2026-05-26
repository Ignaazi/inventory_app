<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     * * Contoh penggunaan di route: middleware('role:engineering,costing')
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (!$request->user()) {
            return redirect('login');
        }

        // 2. Cek apakah role user ada di dalam daftar $roles yang diizinkan
        if (in_array($request->user()->role, $roles)) {
            return $next($request);
        }

        // 3. Jika tidak punya akses, arahkan kembali dengan pesan error
        return redirect('/dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
    }
}