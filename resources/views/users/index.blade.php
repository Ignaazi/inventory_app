@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="font-nunito mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-6 flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-3 shadow-sm transition-all dark:bg-emerald-950/20 dark:border-emerald-900/50">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-sm font-semibold text-emerald-800 dark:text-emerald-400">
            <span class="uppercase font-extrabold mr-1">MASTER DATA:</span> 
            Total {{ method_exists($users, 'total') ? $users->total() : $users->count() }} user accounts registered in central database.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">User Management</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">System Access Control & Authorization Credentials</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button onclick="toggleUserModal()" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                ADD NEW USER
            </button>
        </div>
    </div>

    {{-- Container Master Table --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        {{-- Search Input Section --}}
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <form action="{{ url()->current() }}" method="GET">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search data..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500 font-medium">
                </form>
            </div>
        </div>

        {{-- Table Main Body --}}
        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="userTable">
                <thead>
                    <tr class="text-[10px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-4 text-center w-16">NO</th>
                        <th class="px-4 py-4 text-center w-24">Photo</th>
                        <th class="px-6 py-4 text-center min-w-[220px]">Full Name</th>
                        <th class="px-4 py-4 text-center w-40">NIK KARYAWAN</th>
                        <th class="px-4 py-4 text-center w-44">System Role</th>
                        <th class="px-6 py-4 text-center w-44">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-semibold text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all">
                        {{-- No Center --}}
                        <td class="px-4 py-5 text-center text-slate-500 dark:text-slate-400 font-bold">
                            {{ method_exists($users, 'firstItem') ? ($users->firstItem() + $index) : ($index + 1) }}
                        </td>
                        
                        {{-- Photo Center --}}
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center items-center">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </td>

                        {{-- Name Center --}}
                        <td class="px-6 py-5 text-center font-bold text-slate-800 dark:text-white" title="{{ $user->name }}">
                            {{ $user->name }}
                        </td>

                        {{-- NIM Center --}}
                        <td class="px-4 py-5 text-center font-mono font-bold text-indigo-600 dark:text-indigo-400 tracking-wide text-sm">
                            {{ $user->nim }}
                        </td>

                        {{-- Role Center --}}
                        <td class="px-4 py-5 text-center">
                            <div class="flex justify-center items-center">
                                @if($user->role === 'admin')
                                    <span class="bg-red-100 text-red-700 dark:bg-red-950/40 dark:text-red-400 px-3 py-1 rounded-md font-extrabold text-[10px] uppercase tracking-wider">
                                        ADMIN
                                    </span>
                                @elseif($user->role === 'engineering')
                                    <span class="bg-emerald-100 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 px-3 py-1 rounded-md font-extrabold text-[10px] uppercase tracking-wider">
                                        ENGINEERING
                                    </span>
                                @elseif($user->role === 'production')
                                    <span class="bg-blue-100 text-blue-700 dark:bg-blue-950/40 dark:text-blue-400 px-3 py-1 rounded-md font-extrabold text-[10px] uppercase tracking-wider">
                                        PRODUCTION
                                    </span>
                                @elseif($user->role === 'costing')
                                    <span class="bg-amber-100 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 px-3 py-1 rounded-md font-extrabold text-[10px] uppercase tracking-wider">
                                        COSTING
                                    </span>
                                @else
                                    <span class="bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 px-3 py-1 rounded-md font-extrabold text-[10px] uppercase tracking-wider">
                                        {{ $user->role }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Action Center --}}
                        <td class="px-6 py-5 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                {{-- ACTION 1: PREVIEW (BLUE) --}}
                                <button onclick="previewUser('{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}', '{{ $user->name }}', '{{ strtoupper($user->role) }}', '{{ $user->nim }}')" 
                                    type="button"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-500 text-white transition-all hover:bg-blue-600 active:scale-90 shadow-sm" 
                                    title="Preview Account">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
            
                                {{-- ACTION 2: EDIT (YELLOW) --}}
                                <button onclick="openEditModal({{ $user }})" 
                                    type="button"
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm" 
                                    title="Edit User">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                {{-- ACTION 3: DELETE / ACTIVE BADGE REPLACEMENT --}}
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline form-delete">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white btn-delete" title="Delete User">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                {{-- UPDATE: Button Kotak Indikator Logo Active Ijo Terang --}}
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 h-8 border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900 rounded-lg shadow-sm text-emerald-700 dark:text-emerald-400 font-extrabold text-[10px] tracking-wider uppercase select-none">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Active
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="py-12 text-center text-slate-400 italic font-medium">No account entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Block Footer --}}
        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-6 py-4">
            <p class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                @if(method_exists($users, 'firstItem'))
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} Entries
                @else
                    Showing 1 to {{ $users->count() }} of {{ $users->count() }} Entries
                @endif
            </p>
            <div class="flex items-center gap-2">
                @if(method_exists($users, 'links'))
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH USER --}}
<div id="modalAddUser" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Add New User</h3>
            <button onclick="toggleUserModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('users.store') }}" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Full Name</label>
                    <input type="text" name="name" placeholder="Ex: John Doe" required class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold">
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">NIM / Username</label>
                    <input type="text" name="nim" placeholder="Ex: 123456" required class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-mono font-bold">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Password</label>
                    <input type="password" name="password" placeholder="Min 6 chars" required class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-medium">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">System Role</label>
                    <select name="role" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold">
                        <option value="admin">Admin</option>
                        <option value="engineering">Engineering</option>
                        <option value="costing">Costing</option>
                        <option value="production">Production</option>
                    </select>
                </div>
                <div class="col-span-2 mt-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Profile Photo</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200 hover:file:bg-slate-200">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="toggleUserModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide">Save Account</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT USER --}}
<div id="modalEditUser" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit User Data</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data" class="p-6">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 flex flex-col items-center mb-2">
                    <img id="edit_preview" src="" class="h-16 w-16 rounded-full object-cover border-2 border-indigo-500 shadow-sm">
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Full Name</label>
                    <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold">
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">NIM / Username</label>
                    <input type="text" name="nim" id="edit_nim" required class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-mono font-bold">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank if unchanged" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-medium">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">System Role</label>
                    <select name="role" id="edit_role" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold">
                        <option value="admin">Admin</option>
                        <option value="engineering">Engineering</option>
                        <option value="costing">Costing</option>
                        <option value="production">Production</option>
                    </select>
                </div>
                <div class="col-span-2 mt-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Change Photo</label>
                    <input type="file" name="image" accept="image/*" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-slate-100 file:text-slate-700 dark:file:bg-slate-800 dark:file:text-slate-200 hover:file:bg-slate-200">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide">Update Account</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Preview Account SwAl Intercept Style
    function previewUser(imageUrl, name, role, nim) {
        Swal.fire({
            title: name,
            html: `<div class="text-center mt-3 space-y-1 text-sm font-nunito font-semibold">
                    <p class="text-slate-500">NIM/Username: <span class="font-mono font-bold text-slate-800">${nim}</span></p>
                    <p class="text-slate-500">Access Group: <span class="font-bold text-indigo-600">${role}</span></p>
                   </div>`,
            imageUrl: imageUrl,
            imageWidth: 150,
            imageHeight: 150,
            imageAlt: name,
            animation: true,
            showCloseButton: true,
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Close Preview',
            customClass: {
                popup: 'font-nunito',
                image: 'rounded-full border-2 border-indigo-500 object-cover shadow-sm'
            }
        });
    }

    function toggleUserModal() {
        const modal = document.getElementById('modalAddUser');
        modal.classList.toggle('hidden');
    }

    function openEditModal(user) {
        const modal = document.getElementById('modalEditUser');
        const form = document.getElementById('editForm');
        const preview = document.getElementById('edit_preview');
        
        form.action = `/admin/users/${user.id}`;
        
        document.getElementById('edit_name').value = user.name;
        document.getElementById('edit_nim').value = user.nim;
        document.getElementById('edit_role').value = user.role;
        
        if(user.profile_photo_path) {
            preview.src = `/storage/${user.profile_photo_path}`;
        } else {
            preview.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&background=random`;
        }
        
        modal.classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('modalEditUser').classList.add('hidden');
    }

    // SweetAlert2 Delete Confirmation Intercept
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete');
            Swal.fire({
                title: 'Yakin mau hapus?',
                text: "Akun user yang dihapus tidak akan bisa mengakses sistem kembali!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'font-nunito' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Flash Session Popups Intercept
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'font-nunito' }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ $errors->first() }}",
            customClass: { popup: 'font-nunito' }
        });
    @endif
</script>

<style>
    .font-nunito { font-family: 'Nunito', sans-serif !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .swal2-container { z-index: 10000 !important; }
    
    /* Perfect Center Table Adjustment */
    #userTable th, #userTable td {
        vertical-align: middle !important;
        text-align: center !important;
    }
    #userTable th:nth-child(3), #userTable td:nth-child(3) {
        text-align: center !important; /* Memaksa kolom nama ikut center sempurna */
    }
</style>
@endsection