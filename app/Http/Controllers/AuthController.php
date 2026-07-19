<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function index()
    {
        // Sesuai dengan kode aslimu
        return view('login');
    }

    // Proses Autentikasi
    public function login(Request $request)
    {
        // 1. Validasi
        $credentials = $request->validate([
            'nim' => ['required', 'string'], 
            'password' => ['required'],
        ]);

        // 2. Proses Login
        if (Auth::attempt($credentials)) {
            // Regenerasi session untuk keamanan
            $request->session()->regenerate();
            
            // 3. Ambil role user (Pakai Auth::user() agar VS Code tidak garis merah)
            $role = Auth::user()->role;
            
            // 4. Redirect PAKSA (tanpa intended) berdasarkan role yang presisi
            if ($role === 'admin') {
                return redirect('/admin');
            } elseif ($role === 'engineering') {
                return redirect('/eng/overview');
            } elseif ($role === 'production') {
                return redirect('/prod/overview');
            }
            
            // Default jika role tidak dikenali
            return redirect('/'); 
        }

        // Jika gagal, kembalikan dengan session 'error' agar modal HTML-mu muncul
        return back()->with('error', 'Authentication Failed')
                     ->withInput($request->except('password'));
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