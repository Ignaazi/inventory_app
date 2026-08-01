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

    /**
     * 🛠️ METHOD BARU: Menampilkan halaman add_users terpisah
     */
    public function create()
    {
        return view('users.add_users');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'nik'       => 'required|string|unique:users,nik',
            'email'     => 'nullable|email|max:255|unique:users,email', // ◄ TAMBAHAN: Validasi email unik tapi boleh kosong
            'password'  => 'required|min:6',
            'role'      => 'required|in:admin,engineering,production,costing',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Proses upload Foto Profil
        $profilePath = null;
        if ($request->hasFile('image')) {
            $profilePath = $request->file('image')->store('profile-users', 'public');
        }

        // Proses upload Foto Tanda Tangan
        $signaturePath = null;
        if ($request->hasFile('signature')) {
            $signaturePath = $request->file('signature')->store('signatures', 'public');
        }

        User::create([
            'name'               => $request->name,
            'nik'                => $request->nik,
            'email'              => $request->email, // ◄ TAMBAHAN: Menyimpan input email ke database
            'password'           => Hash::make($request->password),
            'role'               => $request->role,
            'profile_photo_path' => $profilePath,
            'signature_path'     => $signaturePath,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    /**
     * 🛠️ METHOD EDIT: Menampilkan halaman edit terpisah
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'nik'       => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'email'     => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id)], // ◄ TAMBAHAN: Validasi update email
            'password'  => 'nullable|min:6',
            'role'      => 'required|in:admin,engineering,production,costing',
            'image'     => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'signature' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        $data = [
            'name'  => $request->name,
            'nik'   => $request->nik, 
            'email' => $request->email, // ◄ TAMBAHAN: Update data email
            'role'  => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('image')->store('profile-users', 'public');
        }
        
        if ($request->hasFile('signature')) {
            if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
                Storage::disk('public')->delete($user->signature_path);
            }
            $data['signature_path'] = $request->file('signature')->store('signatures', 'public');
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if (Auth::id() === $user->id) {
            return back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
        }
        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }
        if ($user->signature_path && Storage::disk('public')->exists($user->signature_path)) {
            Storage::disk('public')->delete($user->signature_path);
        }

        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }
}