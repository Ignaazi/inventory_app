<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class AuthController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'nik' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $throttleKey = strtolower($request->input('nik')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            
            return back()->with([
                'error_type' => 'lockout',
                'seconds' => $seconds
            ])->withInput($request->except('password'));
        }
        $userExists = User::where('nik', $request->nik)->exists();

        if (!$userExists) {
            RateLimiter::hit($throttleKey, 50); 
            
            return back()->with([
                'error_type' => 'not_registered',
                'message' => 'The NIK you entered is not registered in our system.'
            ])->withInput($request->except('password'));
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            RateLimiter::clear($throttleKey);

            $role = Auth::user()->role;
            if ($role === 'admin') return redirect('/admin');
            if ($role === 'engineering') return redirect('/eng/overview');
            
            return redirect('/'); 
        }
        RateLimiter::hit($throttleKey, 50); 

        return back()->with([
            'error_type' => 'wrong_password',
            'message' => 'The password you entered is incorrect. Please try again.'
        ])->withInput($request->except('password'));
    }
}