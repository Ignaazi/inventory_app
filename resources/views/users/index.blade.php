@extends('admin')

@section('content')
{{-- Pembungkus utama disamakan dengan Stock Eng agar padding konsisten --}}
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    
    {{-- Header Halaman --}}
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                User Management
            </h2>
            <p class="text-sm text-slate-500 font-medium">Manage system access roles and credentials using NIM</p>
        </div>

        <button 
            onclick="toggleUserModal()"
            class="inline-flex items-center justify-center gap-2.5 rounded-xl bg-indigo-600 py-3 px-6 text-center font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 dark:shadow-none"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
            </svg>
            Add New User
        </button>
    </div>

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 flex w-full rounded-xl border-l-6 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="rounded-full bg-emerald-500 p-1">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    {{-- Error Validation --}}
    @if ($errors->any())
    <div class="mb-6 rounded-xl border-l-6 border-red-500 bg-red-50 p-4 dark:bg-red-900/20">
        <ul class="list-disc ml-5">
            @foreach ($errors->all() as $error)
                <li class="text-sm font-bold text-red-800 dark:text-red-400">{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- Tabel User --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 text-left dark:bg-slate-800/50">
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Full Name</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">NIM</th>
                        <th class="py-4 px-6 text-center font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Role</th>
                        <th class="py-4 px-6 text-right font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($users as $user)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-5 px-6 font-semibold text-slate-800 dark:text-slate-200">
                            <div class="flex items-center gap-3">
                                {{-- Menggunakan profile_photo_path sesuai DB --}}
                                <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" 
                                     class="h-10 w-10 rounded-full object-cover border border-slate-200 dark:border-slate-700 shadow-sm"
                                     alt="Profile">
                                <span>{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="py-5 px-6 font-mono text-slate-500 text-sm">
                            {{ $user->nim }}
                        </td>
                        <td class="py-5 px-6 text-center">
                            <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-[10px] font-black uppercase tracking-wide text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="py-5 px-6 text-right">
                            <div class="flex justify-end gap-2">
                                <button 
                                    onclick="openEditModal({{ $user }})"
                                    class="p-2 text-slate-400 hover:text-indigo-600 transition-colors"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>

                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Hapus user ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-400 hover:text-rose-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @else
                                <span class="text-[10px] font-bold text-indigo-500 uppercase italic px-2">You</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-20 text-center text-slate-400 font-medium">No users found in database.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal Tambah User --}}
<div id="modalAddUser" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl dark:bg-slate-900 border border-white/10">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Create New Account</h3>
            <button onclick="toggleUserModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Profile Image</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-slate-800 dark:file:text-slate-300">
            </div>
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Full Name</label>
                <input type="text" name="name" placeholder="Ex: John Doe" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">NIM / Username</label>
                <input type="text" name="nim" placeholder="Ex: 123456" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white font-mono">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Password</label>
                    <input type="password" name="password" placeholder="Min 6 chars" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">System Role</label>
                    <select name="role" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                        <option value="admin">Admin</option>
                        <option value="engineering">Engineering</option>
                        <option value="costing">Costing</option>
                        <option value="production">Production</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleUserModal()" class="flex-1 rounded-xl border border-slate-200 py-3 font-bold text-slate-600 dark:border-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-3 font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 dark:shadow-none">Save User</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit User --}}
<div id="modalEditUser" class="fixed inset-0 z-[99999] hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl dark:bg-slate-900 border border-white/10">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Edit Account</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form id="editForm" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')
            <div class="flex flex-col items-center mb-4">
                <img id="edit_preview" src="" class="h-20 w-20 rounded-full object-cover border-2 border-indigo-500 mb-2">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500">Change Photo</label>
                <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100 dark:file:bg-slate-800 dark:file:text-slate-300 mt-2">
            </div>
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Full Name</label>
                <input type="text" name="name" id="edit_name" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white">
            </div>
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">NIM / Username</label>
                <input type="text" name="nim" id="edit_nim" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white font-mono">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Password (Opt)</label>
                    <input type="password" name="password" placeholder="New password" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">System Role</label>
                    <select name="role" id="edit_role" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                        <option value="admin">Admin</option>
                        <option value="engineering">Engineering</option>
                        <option value="costing">Costing</option>
                        <option value="production">Production</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeEditModal()" class="flex-1 rounded-xl border border-slate-200 py-3 font-bold text-slate-600 dark:border-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-3 font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 dark:shadow-none">Update User</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleUserModal() {
        const modal = document.getElementById('modalAddUser');
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }

    function openEditModal(user) {
        const modal = document.getElementById('modalEditUser');
        const form = document.getElementById('editForm');
        const preview = document.getElementById('edit_preview');
        
        form.action = `/admin/users/${user.id}`;
        
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_nim').value = user.nim;
        document.getElementById('edit_role').value = user.role;
        
        // Preview Foto saat Edit menggunakan profile_photo_path
        if(user.profile_photo_path) {
            preview.src = `/storage/${user.profile_photo_path}`;
        } else {
            preview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`;
        }
        
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closeEditModal() {
        const modal = document.getElementById('modalEditUser');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
    }
</script>
@endsection