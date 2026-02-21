<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; 
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();
        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nim'      => 'required|string|unique:users,nim', 
            'password' => 'required|min:6',
            'role'     => 'required|in:admin,engineering,production,costing',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $path = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('profile-users', 'public');
        }

        User::create([
            'name'               => $request->name,
            'nim'                => $request->nim,
            'password'           => Hash::make($request->password),
            'role'               => $request->role,
            'profile_photo_path' => $path, // Sesuaikan dengan nama kolom DB lo
        ]);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'nim'      => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
            'role'     => 'required|in:admin,engineering,production,costing',
            'image'    => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'name' => $request->name,
            'nim'  => $request->nim,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            // Hapus foto lama jika ada
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            
            // Simpan foto baru ke kolom profile_photo_path
            $data['profile_photo_path'] = $request->file('image')->store('profile-users', 'public');
        }

        $user->update($data);

        return back()->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}