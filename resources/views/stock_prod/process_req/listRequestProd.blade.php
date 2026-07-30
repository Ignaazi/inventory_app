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
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">SYSTEM RECORD:</span> 
            Total {{ $requests->total() }} sparepart requests logged in production database.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">List Sparepart Request</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS KARAWANG</p>
        </div>

        {{-- TOMBOL CREATE NEW --}}
        <div class="flex items-center w-full sm:w-auto">
            <a href="{{ route('prod.request.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-opacity uppercase tracking-wider active:scale-95 transition-all font-nunito w-full sm:w-auto text-center cursor-pointer no-underline">
                <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create New Request
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <!-- Entries Controller -->
            <div class="flex flex-wrap items-center gap-3 text-xs md:text-[13px] font-black text-slate-950 dark:text-slate-300 order-2 sm:order-1">
                <div class="flex items-center gap-1.5">
                    <span>Show</span>
                    <select class="rounded-md border border-gray-300 dark:border-slate-700 bg-transparent px-2 py-1 outline-none text-slate-950 dark:text-white font-black cursor-pointer font-nunito text-xs">
                        <option value="10" class="dark:bg-slate-900">10</option>
                        <option value="25" class="dark:bg-slate-900">25</option>
                        <option value="50" class="dark:bg-slate-900">50</option>
                    </select>
                    <span>entries</span>
                </div>
            </div>

            <!-- Search & Export Grid -->
            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative col-span-8 sm:w-60 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ url()->current() }}" method="GET" class="w-full">
                        <input type="text" name="search" value="{{ request('search') }}" id="tableSearch" placeholder="Search..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('sparepart-requests.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
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
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1450px]" id="requestTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-blue-400 bg-transparent text-blue-600 focus:ring-blue-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-blue-500 dark:bg-blue-900/50">NO</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:bg-blue-900/50">Request NO</th>
                        <th class="px-3 py-3.5 w-[100px] border-l border-blue-500 dark:bg-blue-900/50">NIK</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:bg-blue-900/50">Name</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:bg-blue-900/50 text-center w-[160px]">Sparepart ID</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 dark:bg-blue-900/50">Qty Req</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 dark:bg-blue-900/50">Line</th>
                        <th class="px-2 py-3.5 w-[130px] border-l border-blue-500 dark:bg-blue-900/50">Machine Name</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 dark:bg-blue-900/50">Status</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:bg-blue-900/50 text-left w-[180px]">Remark</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 dark:bg-blue-900/50">Created At</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 dark:bg-blue-900/50">Updated At</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 dark:bg-blue-900/50">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-slate-950 dark:text-slate-200 font-nunito bg-transparent">
                    @forelse($requests as $index => $req)
                    @php
                        $statusText = 'unknown';
                        $badgeClass = 'bg-slate-100 text-slate-950 border-slate-300 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700';
                        
                        if(str_contains(strtolower($req->status), 'draft')) {
                            $statusText = 'draft';
                            $badgeClass = 'bg-gray-100 text-gray-950 border-gray-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700';
                        } elseif(str_contains(strtolower($req->status), 'pending')) {
                            $statusText = 'pending';
                            $badgeClass = 'bg-amber-50 text-amber-950 border-amber-300 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/50';
                        } elseif(str_contains(strtolower($req->status), 'staff') || str_contains(strtolower($req->status), 'checked')) {
                            $statusText = 'checked';
                            $badgeClass = 'bg-blue-50 text-blue-950 border-blue-300 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50';
                        } elseif(str_contains(strtolower($req->status), 'approved')) {
                            $statusText = 'approved';
                            $badgeClass = 'bg-emerald-50 text-emerald-950 border-emerald-300 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-900/50';
                        } elseif(str_contains(strtolower($req->status), 'reject')) {
                            $statusText = 'reject';
                            $badgeClass = 'bg-rose-50 text-rose-950 border-rose-300 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50';
                        } elseif(str_contains(strtolower($req->status), 'finished')) {
                            $statusText = 'finished';
                            $badgeClass = 'bg-purple-50 text-purple-950 border-purple-300 dark:bg-purple-950/40 dark:text-purple-400 dark:border-purple-900/50';
                        }
                    @endphp
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-slate-950 dark:text-slate-400">
                            {{ $requests->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-slate-950 dark:text-slate-100">
                            {{ $req->request_no }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-slate-950 dark:text-slate-200">
                            {{ optional($req->user)->nik ?? $req->nik ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-slate-950 dark:text-slate-200">
                            {{ optional($req->user)->name ?? $req->requestor ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-normal break-words leading-normal text-blue-600 dark:text-blue-400">
                            @if($req->sparepart)
                                {{ $req->sparepart->sparepart_id ?? $req->sparepart->id }}
                            @else
                                @php
                                    $fallback = is_numeric($req->sparepart_id) 
                                        ? \DB::table('spareparts')->where('id', $req->sparepart_id)->first() 
                                        : null;
                                @endphp
                                {{ $fallback ? ($fallback->sparepart_id ?? $req->sparepart_id) : ($req->sparepart_id ?? '-') }}
                            @endif
                        </td>

                        {{-- TULISAN PCS TELAH DIHAPUS --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-slate-950 dark:text-slate-200">
                            {{ $req->qty_req }}
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase whitespace-nowrap text-slate-950 dark:text-slate-200">
                            {{ optional($req->lineProduction)->no_line ?? ($req->line_machine ? explode(' - ', $req->line_machine)[0] : '-') }}
                        </td>
                        
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase whitespace-normal break-words leading-normal text-slate-950 dark:text-slate-200">
                            {{ optional($req->lineProduction)->name_machine ?? ($req->line_machine ? (explode(' - ', $req->line_machine)[1] ?? '-') : '-') }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 status-container">
                            <div class="flex justify-center items-center">
                                <span class="status-badge inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm {{ $badgeClass }}">
                                    {{ $statusText === 'unknown' ? $req->status : $statusText }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left font-semibold tracking-wide whitespace-normal break-words leading-normal text-slate-950 dark:text-slate-200">
                            {{ $req->remark ?? '-' }}
                        </td>

                        {{-- CREATED AT --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-950 dark:text-slate-400">
                            {{ $req->created_at ? $req->created_at->format('d/m/Y H:i') : '-' }}
                        </td>

                        {{-- UPDATED AT --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-950 dark:text-slate-400">
                            {{ $req->updated_at ? $req->updated_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-1.5 w-full">
                                {{-- BUTTON PREVIEW --}}
                                <a href="{{ route('prod.request.preview', $req->id) }}" 
                                   class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-blue-500 text-white hover:bg-blue-600 active:scale-90 shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                </a>
            
                                {{-- BUTTON EDIT (HANYA MUNCUL JIKA STATUS DRAFT) --}}
                                @if(str_contains(strtolower($req->status), 'draft'))
                                <button onclick="openModal('edit', {{ json_encode($req) }})" 
                                   class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-yellow-400 text-white hover:bg-yellow-500 active:scale-90 shadow-sm transition-all cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="14" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito dark:bg-slate-900">No request entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black text-slate-950 dark:text-slate-400 tracking-wide uppercase font-nunito text-center sm:text-left">
                Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito text-slate-950 dark:text-white w-full sm:w-auto custom-pagination">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL EDIT DATA REQUEST --}}
<div id="modalRequest" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800 transition-all transform scale-100">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-slate-50 dark:bg-slate-850">
            <h3 class="text-lg font-extrabold text-slate-950 dark:text-white tracking-tight">Edit Request Data</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors cursor-pointer">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg>
            </button>
        </div>
        <form id="requestForm" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Sparepart ID</label>
                    <input type="text" name="sparepart_id" id="modal_sparepart_id" class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent p-2.5 text-sm outline-none focus:border-blue-500 text-slate-950 dark:text-white font-semibold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Quantity Request</label>
                    <input type="number" name="qty_req" id="modal_qty_req" class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent p-2.5 text-sm outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Remark / Reason</label>
                    <input type="text" name="remark" id="modal_remark" class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent p-2.5 text-sm outline-none focus:border-blue-500 text-slate-950 dark:text-white font-semibold">
                </div>
            </div>
            
            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors cursor-pointer">Cancel</button>
                <button type="submit" class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:opacity-90 transition-all active:scale-95 tracking-wide uppercase cursor-pointer">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(mode, data = null) {
        const modal = document.getElementById('modalRequest');
        const form = document.getElementById('requestForm');
        modal.classList.remove('hidden');
        
        if (mode === 'edit' && data) {
            form.action = "/prod/request/" + data.id; 
            document.getElementById('modal_sparepart_id').value = data.sparepart ? (data.sparepart.sparepart_id ?? data.sparepart_id) : (data.sparepart_id ?? '');
            document.getElementById('modal_qty_req').value = data.qty_req;
            document.getElementById('modal_remark').value = data.remark ?? '';
        }
    }

    function closeModal() { document.getElementById('modalRequest').classList.add('hidden'); }

    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Export CSV Client Side Logic
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#requestTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length - 1; j++) { // Skip checkbox & action
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

    // Intercept Modal Submit Form
    document.getElementById('requestForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Save Changes?',
            text: "Ensure the parameters are valid",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, Save!',
            customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false, customClass: { popup: 'font-nunito' } });
    @endif
</script>

<style>
    /* Paksa Font Nunito & Teks Hitam Pekat */
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container, #requestTable, #requestTable tbody tr td { 
        font-family: 'Nunito', sans-serif !important; 
    }
    
    /* Scrollbar minimalis horizontal */
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    
    #requestTable td, #requestTable th {
        vertical-align: middle !important;
    }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection