@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* Custom Styling SweetAlert2 agar harmonis dengan tema aplikasi */
    .swal2-popup {
        border-radius: 1rem !important;
        font-family: 'Nunito', sans-serif !important;
    }
    .dark .swal2-popup {
        background-color: #0f172a !important; /* slate-900 */
        border: 1px solid #1e293b !important; /* slate-850 */
    }
    .dark .swal2-title, .dark .swal2-html-container {
        color: #f8fafc !important; /* slate-50 */
    }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">MASTER DATA:</span> 
            Total {{ method_exists($users, 'total') ? $users->total() : $users->count() }} user accounts registered.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight">User Management</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">System Access Control & Authorization Credentials</p>
        </div>

        {{-- TOMBOL ADD NEW USER GRADIENT --}}
        <div class="flex items-center w-full sm:w-auto">
            <a href="{{ route('users.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-opacity uppercase tracking-wider active:scale-95 transition-all font-nunito w-full sm:w-auto text-center">
                <svg class="w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add New User
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <!-- Entries Selector -->
            <div class="flex items-center gap-2 text-xs md:text-[13px] font-black text-black dark:text-slate-300 order-2 sm:order-1">
                <span>Show</span>
                <select class="rounded-md border border-gray-300 dark:border-slate-700 bg-transparent px-2 py-1 outline-none text-black dark:text-white font-black cursor-pointer font-nunito text-xs">
                    <option value="10" class="dark:bg-slate-900">10</option>
                    <option value="25" class="dark:bg-slate-900">25</option>
                    <option value="50" class="dark:bg-slate-900">50</option>
                </select>
                <span>entries</span>
            </div>

            <!-- Search & Export Grid -->
            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative col-span-8 sm:w-60 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="tableSearch" placeholder="Search..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-black dark:text-white font-bold font-nunito">
                </div>

                {{-- TOMBOL EXPORT CSV RESPONSIF --}}
                <button type="button" onclick="exportTableToCSV('users-data.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-black dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
                    <span class="hidden sm:inline">Export CSV</span>
                    <span class="sm:hidden">CSV</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- AREA SCROLL HORIZONTAL --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1150px]" id="userTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-blue-400 bg-transparent text-blue-600 focus:ring-blue-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-blue-500 dark:border-blue-900/50">NO</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 dark:border-blue-900/50">Photo</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:border-blue-900/50 text-left w-[260px]">Full Name</th>
                        <th class="px-2 py-3.5 w-[150px] border-l border-blue-500 dark:border-blue-900/50">NIK KARYAWAN</th>
                        <th class="px-2 py-3.5 w-[100px] border-l border-blue-500 dark:border-blue-900/50">Sign</th>
                        <th class="px-2 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">System Role</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:border-blue-900/50">Created At</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:border-blue-900/50">Updated At</th>
                        <th class="px-4 py-3.5 w-[170px] border-l border-blue-500 dark:border-blue-900/50">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
                    @forelse($users as $index => $user)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            {{ method_exists($users, 'firstItem') ? ($users->firstItem() + $index) : ($index + 1) }}
                        </td>
                        
                        <td class="px-2 py-2 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center overflow-hidden border border-gray-200 dark:border-slate-700 shadow-sm">
                                    <img src="{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}" class="w-full h-full object-cover">
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 text-left border-l border-gray-100 dark:border-slate-800 search-name font-extrabold tracking-wide whitespace-nowrap overflow-hidden text-ellipsis" title="{{ $user->name }}">
                            {{ $user->name }}
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 search-nik whitespace-nowrap">
                            {{ $user->nik }}
                        </td>

                        <td class="px-2 py-2 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <div class="w-16 h-8 bg-slate-50 dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded flex items-center justify-center overflow-hidden p-0.5 shadow-2xs">
                                    @if($user->signature_path)
                                        <img src="{{ asset('storage/' . $user->signature_path) }}" class="w-full h-full object-contain mix-blend-multiply dark:mix-blend-normal dark:brightness-200 cursor-zoom-in" onclick="Swal.fire({imageUrl: this.src, showConfirmButton: false})">
                                    @else
                                        <span class="text-[10px] text-slate-400 dark:text-slate-500 font-bold italic uppercase tracking-tighter select-none">No Sign</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 search-role">
                            <div class="flex justify-center items-center">
                                @if($user->role === 'admin')
                                    <span class="bg-red-50 text-red-600 border border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-900 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">ADMIN</span>
                                @elseif($user->role === 'engineering')
                                    <span class="bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">ENGINEERING</span>
                                @elseif($user->role === 'production')
                                    <span class="bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">PRODUCTION</span>
                                @elseif($user->role === 'costing')
                                    <span class="bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">COSTING</span>
                                @else
                                    <span class="bg-slate-50 text-slate-600 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">{{ strtoupper($user->role) }}</span>
                                @endif
                            </div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap">
                            {{ $user->created_at ? $user->created_at->format('d/m/Y H:i') : '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap">
                            {{ $user->updated_at ? $user->updated_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-1.5 w-full">
                                <button onclick="previewUser('{{ $user->profile_photo_path ? asset('storage/' . $user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}', '{{ $user->name }}', '{{ strtoupper($user->role) }}', '{{ $user->nik }}')" 
                                    type="button" class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-blue-500 text-white hover:bg-blue-600 active:scale-90 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </button>
            
                                <a href="{{ route('users.edit', $user->id) }}" 
                                   class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-yellow-400 text-white hover:bg-yellow-500 active:scale-90 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                        
                                @if(auth()->id() !== $user->id)
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" class="inline form-delete shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete hover:bg-red-600 active:scale-90 transition-all">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </button>
                                </form>
                                @else
                                <div class="inline-flex items-center gap-1 px-1.5 h-7 border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/50 dark:border-emerald-900 rounded shadow-sm text-emerald-700 dark:text-emerald-400 font-black text-[9px] tracking-tight uppercase select-none shrink-0">
                                    <span class="w-1 h-1 rounded-full bg-emerald-500 animate-pulse"></span>Active
                                </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito">No account entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase font-nunito text-center sm:text-left">
                @if(method_exists($users, 'firstItem'))
                    Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} Entries
                @else
                    Showing 1 to {{ $users->count() }} of {{ $users->count() }} Entries
                @endif
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito text-black dark:text-white w-full sm:w-auto">
                @if(method_exists($users, 'links'))
                    {{ $users->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // LIVE SEARCH
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('.table-row-item');
        
        rows.forEach(function(row) {
            let name = row.querySelector('.search-name').textContent.toLowerCase();
            let nik = row.querySelector('.search-nik').textContent.toLowerCase();
            let role = row.querySelector('.search-role').textContent.toLowerCase();
            
            if (name.includes(value) || nik.includes(value) || role.includes(value)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#userTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length; j++) {
                let data = cols[j].innerText.replace(/(\r\n|\n|\r)/gm, "").replace(/(\s\s+)/gm, " ");
                row.push('"' + data + '"');
            }
            csv.push(row.join(","));
        }
        let csvFile = new Blob([csv.join("\n")], {type: "text/csv"});
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    function previewUser(imageUrl, name, role, nik) {
        Swal.fire({
            title: name,
            html: `<div class="text-center mt-2 space-y-0.5 text-xs font-nunito font-bold text-slate-950 dark:text-slate-200">
                    <p>NIK/Username: <span class="font-bold text-black dark:text-white">${nik}</span></p>
                    <p>Access Group: <span class="font-black text-blue-600 dark:text-blue-400">${role}</span></p>
                   </div>`,
            imageUrl: imageUrl,
            imageWidth: 90,
            imageHeight: 90,
            imageAlt: name,
            showCloseButton: true,
            confirmButtonColor: '#2563eb',
            confirmButtonText: 'Close Preview',
            customClass: {
                popup: 'font-nunito bg-white dark:bg-slate-900 border dark:border-slate-800 max-w-[90%] sm:max-w-md',
                image: 'rounded-full border-2 border-blue-500 object-cover shadow-sm',
                title: 'text-black dark:text-white text-lg md:text-xl font-extrabold'
            }
        });
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const form = this.closest('form');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this user entry!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                customClass: { 
                    popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' 
                }
            }).then((result) => {
                if (result.isConfirmed) { form.submit(); }
            });
        });
    });
</script>
@endsection