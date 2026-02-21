<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function index()
    {
        // Pastikan nama file view-nya 'login.blade.php'
        return view('login');
    }

    // Proses Autentikasi
    public function login(Request $request)
    {
        // 1. Validasi: Ganti 'nim' (rule) menjadi 'string'
        $credentials = $request->validate([
            'nim' => ['required', 'string'], 
            'password' => ['required'],
        ]);

        // 2. Proses Login menggunakan NIM
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // 3. Redirect dinamis berdasarkan role (Opsional tapi bagus untuk sistem Anda)
            $user = Auth::user();
            if ($user->role === 'admin' || $user->role === 'engineering') {
                return redirect()->intended('admin');
            }
            
            return redirect()->intended('dashboard'); // default untuk role lain
        }

        // Jika gagal, kembali dengan pesan error
        return back()->withErrors([
            'nim' => 'NIM atau password yang Anda masukkan salah.',
        ])->onlyInput('nim');
    }

    // Proses Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}