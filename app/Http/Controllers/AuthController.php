<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User; // Pastikan model User di-import

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nim' => ['required', 'string'], 
            'password' => ['required'],
        ]);

        $throttleKey = strtolower($request->input('nim')) . '|' . $request->ip();

        // 1. Cek apakah user sedang dalam masa lockout (salah 3x)
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            return back()->with([
                'error_type' => 'lockout',
                'seconds' => $seconds
            ])->withInput($request->except('password'));
        }

        // 2. Cek apakah NIK/NIM terdaftar di database
        $userExists = User::where('nim', $request->nim)->exists(); // Sesuaikan nama kolom NIK di DB-mu

        if (!$userExists) {
            // Jika akun belum terdaftar, tetap hitung sebagai attempt demi keamanan
            RateLimiter::hit($throttleKey, 50); 
            
            return back()->with([
                'error_type' => 'not_registered',
                'message' => 'The NIK you entered is not registered in our system.'
            ])->withInput($request->except('password'));
        }

        // 3. Jika akun ada, coba proses authentication (Login)
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            $role = Auth::user()->role;
            if ($role === 'admin') return redirect('/admin');
            if ($role === 'engineering') return redirect('/eng/overview');
            
            return redirect('/'); 
        }

        // 4. Jika akun ada tapi PASSWORD SALAH
        // Set decay time ke 50 detik, jadi begitu hit ke-3, otomatis lock selama 50 detik
        RateLimiter::hit($throttleKey, 50); 

        return back()->with([
            'error_type' => 'wrong_password',
            'message' => 'The password you entered is incorrect. Please try again.'
        ])->withInput($request->except('password'));
    }
}