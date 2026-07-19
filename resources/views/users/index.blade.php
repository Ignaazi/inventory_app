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

    {{-- PEMBUNGKUS UTAMA: Datatable 3 Style --}}
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        
        {{-- HEADER KONTROL (SHOW ENTRIES, LIVE SEARCH & EXPORT CSV) --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 text-sm font-medium text-slate-500 dark:text-slate-400">
                <span>Show</span>
                <select class="rounded-lg border border-gray-200 bg-transparent px-3 py-1.5 text-sm outline-none dark:border-gray-700 dark:bg-boxdark text-slate-700 dark:text-white font-bold">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
                <span>entries</span>
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative w-full sm:w-64">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="tableSearch" placeholder="Search..." class="w-full rounded-lg border border-gray-200 bg-transparent py-1.5 pl-9 pr-4 text-sm outline-none focus:border-primary dark:border-gray-700 dark:bg-boxdark dark:text-white font-medium">
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('users-data.csv')" class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-bold text-slate-700 shadow-sm hover:bg-slate-50 dark:border-gray-700 dark:bg-boxdark dark:text-white dark:hover:bg-slate-800 transition-all active:scale-95 cursor-pointer">
                    <span>Export CSV</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- AREA TABEL UTAMA DENGAN JARA KELOM & BARIS DEKAT (COMPACT) --}}
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto text-center border-collapse border-b border-gray-200 dark:border-gray-800" id="userTable">
                <thead>
                    <tr class="border-t border-b border-gray-200 dark:border-gray-800 text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-400 bg-slate-50/50 dark:bg-slate-800/20">
                        {{-- Checkbox Header --}}
                        <th class="px-3 py-3 w-12 text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </th>
                        <th class="px-3 py-3 w-14 border-l border-gray-200 dark:border-gray-800">NO</th>
                        <th class="px-3 py-3 w-20 border-l border-gray-200 dark:border-gray-800">Photo</th>
                        <th class="px-5 py-3 min-w-[200px] border-l border-gray-200 dark:border-gray-800 text-left">Full Name</th>
                        <th class="px-3 py-3 w-36 border-l border-gray-200 dark:border-gray-800">NIK KARYAWAN</th>
                        <th class="px-3 py-3 w-40 border-l border-gray-200 dark:border-gray-800">System Role</th>
                        <th class="px-5 py-3 w-40 border-l border-gray-200 dark:border-gray-800">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-800 text-xs font-bold text-black dark:text-black">
                    @forelse($users as $index => $user)
                    <tr class="table-row-item hover:bg-slate-50/60 dark:hover:bg-white/[0.02] transition-colors duration-150">
                        {{-- Kotak Klik Checkbox per Baris --}}
                        <td class="px-3 py-2.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        {{-- No --}}
                        <td class="px-3 py-2.5 text-slate-400 font-medium border-l border-gray-200 dark:border-gray-800">
                            {{ method_exists($users, 'firstItem') ? ($users->firstItem() + $index) : ($index + 1) }}
                        </td>
                        
                        {{-- Photo --}}
                        <td class="px-3 py-2 border-l border-gray-200 dark:border-gray-800">
                            <div class="flex justify-center items-center">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </td>

                        {{-- Name --}}
                        <td class="px-5 py-2.5 font-extrabold text-left border-l border-gray-200 dark:border-gray-800 search-name" title="{{ $user->name }}">
                            {{ $user->name }}
                        </td>

                        {{-- NIK --}}
                        <td class="px-3 py-2.5 font-mono font-bold tracking-wide text-sm border-l border-gray-200 dark:border-gray-800 search-nim">
                            {{ $user->nim }}
                        </td>

                        {{-- Role (Status Box Ukuran Diperbesar) --}}
                        <td class="px-3 py-2.5 border-l border-gray-200 dark:border-gray-800 search-role">
                            <div class="flex justify-center items-center">
                                @if($user->role === 'admin')
                                    <span class="bg-red-50 text-red-600 border border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-900/40 px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider">
                                        ADMIN
                                    </span>
                                @elseif($user->role === 'engineering')
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/40 px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider">
                                        ENGINEERING
                                    </span>
                                @elseif($user->role === 'production')
                                    <span class="bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/40 px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider">
                                        PRODUCTION
                                    </span>
                                @elseif($user->role === 'costing')
                                    <span class="bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/40 px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider">
                                        COSTING
                                    </span>
                                @else
                                    <span class="bg-slate-50 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 px-3 py-1.5 rounded-md text-xs font-black uppercase tracking-wider">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                @endif
                            </div>
                        </td>
                        
                        {{-- Action --}}
                        <td class="px-5 py-2.5 border-l border-gray-200 dark:border-gray-800">
                            <div class="flex items-center justify-center gap-1.5">
                                <button onclick="previewUser('{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}', '{{ $user->name }}', '{{ strtoupper($user->role) }}', '{{ $user->nim }}')" 
                                    type="button"
                                    class="flex h-7 w-7 items-center justify-center rounded bg-blue-500 text-white transition-all hover:bg-blue-600 active:scale-90 shadow-sm" 
                                    title="Preview Account">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </button>
            
                                <button onclick="openEditModal({{ $user }})" 
                                    type="button"
                                    class="flex h-7 w-7 items-center justify-center rounded bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm" 
                                    title="Edit User">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline form-delete">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete" title="Delete User">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                                @else
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 h-7 border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900 rounded shadow-sm text-emerald-700 dark:text-emerald-400 font-extrabold text-[10px] tracking-wide uppercase select-none">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Active
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="py-12 text-center text-slate-400 italic font-medium">No account entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION --}}
        <div class="flex items-center justify-between border-t border-gray-200 dark:border-gray-800 bg-white dark:bg-transparent px-5 py-4">
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 tracking-wide uppercase">
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
    // FUNGSI LIVE SEARCH TANPA RELOAD
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.table-row-item');
        
        rows.forEach(function(row) {
            let name = row.querySelector('.search-name').textContent.toLowerCase();
            let nim = row.querySelector('.search-nim').textContent.toLowerCase();
            let role = row.querySelector('.search-role').textContent.toLowerCase();
            
            if (name.includes(value) || nim.includes(value) || role.includes(value)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // FUNGSI CHECKBOX SELECT ALL
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // FUNGSI EXPORT DATA KE CSV
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#userTable tr");
        
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            
            // Mulai dari indeks 1 untuk skip kolom checkbox paling kiri
            for (let j = 1; j < cols.length; j++) {
                // Bersihkan teks dari whitespace berlebih
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s+)/gm, " ");
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }

        // Download link
        let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    // Preview Account SwAl Intercept Style
    function previewUser(imageUrl, name, role, nim) {
        Swal.fire({
            title: name,
            html: `<div class="text-center mt-3 space-y-1 text-sm font-nunito font-semibold text-black">
                    <p class="text-slate-600">NIK/Username: <span class="font-mono font-bold text-black">${nim}</span></p>
                    <p class="text-slate-600">Access Group: <span class="font-bold text-indigo-600">${role}</span></p>
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
                popup: 'font-nunito text-black',
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
                customClass: { popup: 'font-nunito text-black' }
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
            customClass: { popup: 'font-nunito text-black' }
        });
    @endif

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: "{{ $errors->first() }}",
            customClass: { popup: 'font-nunito text-black' }
        });
    @endif
</script>

<style>
    .font-nunito { font-family: 'Nunito', sans-serif !important; }
    .swal2-container { z-index: 10000 !important; }
    
    /* Paksa semua teks di tabel menjadi warna hitam pekat Nunito */
    #userTable th, #userTable td {
        color: #000000 !important;
        vertical-align: middle !important;
    }
    #userTable th {
        text-align: center !important;
    }a
    #userTable td:nth-child(4) {
        text-align: left !important; /* Nama tetap kiri agar rapi */
    }
    #userTable td:not(:nth-child(4)) {
        text-align: center !important; /* Sisanya center */
    }
</style>
@endsection