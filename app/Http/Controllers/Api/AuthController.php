<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; 

class AuthController extends Controller
{
    public function loginMobile(Request $request)
    {
        $request->validate([
            'nik'      => 'required',
            'password' => 'required',
        ]);

        $user = DB::table('users')
                    ->where('nik', $request->nik)
                    ->first();

        if ($user && $user->is_active == 1) {
            if (Hash::check($request->password, $user->password)) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Selamat datang kembali, ' . $user->name . '!',
                    'user' => [
                        'id'   => $user->id,
                        'name' => $user->name,
                        'nik'  => $user->nik,
                        'role' => $user->role,
                    ]
                ], 200);
            }
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'NIK atau Password salah, Bro!'
        ], 401);
    }

    public function getAllUsers()
    {
        try {
            $users = DB::table('users')
                        ->select('id', 'name', 'nik', 'role')
                        ->orderBy('name', 'asc')
                        ->get();

            return response()->json($users, 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data dari DB: ' . $e->getMessage()
            ], 500);
        }
    }
}