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
            Total {{ $requests->total() }} production requests queue logged for engineering review.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Engineering Approval List</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS KARAWANG</p>
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
                        <input type="text" name="search" value="{{ request('search') }}" id="tableSearch" placeholder="Search Request..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('engineering-approvals.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
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
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1450px]" id="approvalTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito table-header-row">
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
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:bg-blue-900/50 text-center w-[130px]">Remark</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:bg-blue-900/50 text-center">Created At</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:bg-blue-900/50 text-center">Updated At</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:bg-blue-900/50 text-center">Action Decision</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($requests as $index => $req)
                    @php
                        $statusText = 'unknown';
                        $badgeClass = 'bg-slate-100 text-slate-950 border-slate-300';
                        
                        $rawStatus = strtolower($req->status);
                        
                        if(str_contains($rawStatus, 'draft')) {
                            $statusText = 'draft';
                            $badgeClass = 'bg-gray-100 text-gray-950 border-gray-300';
                        } elseif(str_contains($rawStatus, 'pending')) {
                            $statusText = 'pending';
                            $badgeClass = 'bg-amber-50 text-amber-950 border-amber-300';
                        } elseif(str_contains($rawStatus, 'staff') || str_contains($rawStatus, 'checked')) {
                            $statusText = 'checked';
                            $badgeClass = 'bg-blue-50 text-blue-950 border-blue-300';
                        } elseif(str_contains($rawStatus, 'success') || str_contains($rawStatus, 'approved')) {
                            $statusText = 'approved';
                            $badgeClass = 'bg-emerald-50 text-emerald-950 border-emerald-300';
                        } elseif(str_contains($rawStatus, 'reject')) {
                            $statusText = 'rejected';
                            $badgeClass = 'bg-rose-50 text-rose-950 border-rose-300';
                        } elseif(str_contains($rawStatus, 'finished')) {
                            $statusText = 'finished';
                            $badgeClass = 'bg-purple-50 text-purple-950 border-purple-300';
                        }
                    @endphp
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            {{ $requests->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap">
                            {{ $req->request_no }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($req->user)->nik ?? $req->nik ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($req->user)->name ?? $req->requestor ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-normal break-words leading-normal">
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

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $req->qty_req }}
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase whitespace-nowrap">
                            {{ optional($req->lineProduction)->no_line ?? ($req->line_machine ? explode(' - ', $req->line_machine)[0] : '-') }}
                        </td>
                        
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase whitespace-normal break-words leading-normal">
                            {{ optional($req->lineProduction)->name_machine ?? ($req->line_machine ? (explode(' - ', $req->line_machine)[1] ?? '-') : '-') }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm status-badge {{ $badgeClass }}">
                                    {{ $statusText === 'unknown' ? $req->status : $statusText }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center font-semibold tracking-wide whitespace-normal break-words leading-normal">
                            {{ $req->remark ?? '-' }}
                        </td>

                        {{-- CREATED AT --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold">{{ $req->created_at ? $req->created_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5">{{ $req->created_at ? $req->created_at->format('H:i') : '-' }} WIB</div>
                        </td>

                        {{-- UPDATED AT --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold">{{ $req->updated_at ? $req->updated_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5">{{ $req->updated_at ? $req->updated_at->format('H:i') : '-' }} WIB</div>
                        </td>
                        
                        {{-- DECISION ACTION --}}
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <div class="flex items-center justify-center gap-1.5 w-full">
                                @if(!in_array(strtolower($req->status), ['success', 'approved', 'rejected']))
                                    <form action="{{ route('eng.approval.reject', $req->id) }}" method="POST" class="decision-form inline-block m-0">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1.5 bg-gradient-to-r from-red-500 to-amber-500 text-white font-black rounded text-[10px] uppercase tracking-wider shadow-sm hover:opacity-90 active:scale-95 transition-all cursor-pointer">
                                            Reject
                                        </button>
                                    </form>

                                    <a href="{{ route('eng.approval.review', $req->id) }}" 
                                       class="px-2.5 py-1.5 bg-gradient-to-r from-emerald-500 to-blue-500 text-white font-black rounded text-[10px] uppercase tracking-wider shadow-sm hover:opacity-90 active:scale-95 transition-all text-center inline-block no-underline cursor-pointer">
                                        Approve
                                    </a>
                                @else
                                    <span class="text-[10px] font-black uppercase tracking-wider processed-text">Processed</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="py-10 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text">
                            No production requests waiting in review queue.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase font-nunito text-center sm:text-left text-black">
                Showing {{ $requests->firstItem() ?? 0 }} to {{ $requests->lastItem() ?? 0 }} of {{ $requests->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination text-black">
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Export CSV Client Side Logic
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#approvalTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length; j++) { // Skip checkbox
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

    // Intercept Reject Submit Form dengan SweetAlert2
    document.querySelectorAll('.decision-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            let currentForm = this;
            Swal.fire({
                title: 'Reject Request?',
                text: "Are you sure you want to reject this production request?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, Reject it!',
                customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
            }).then((result) => { if (result.isConfirmed) currentForm.submit(); });
        });
    });

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false, customClass: { popup: 'font-nunito' } });
    @endif
</script>

<style>
    /* Mengatur Font Secara Global */
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container, #approvalTable { 
        font-family: 'Nunito', sans-serif !important; 
    }

    /* Memaksa Teks di Dalam Body Table Menggunakan Warna Hitam Pekat */
    .table-body-data tr td, 
    .table-body-data tr td div,
    .table-empty-text,
    .processed-text {
        color: #000000 !important;
    }

    /* Pengecualian Warna Spesifik Teks untuk Status Badge & Tombol Akses */
    .status-badge {
        /* Warna teks dinamis mengikuti isi background badge masing-masing */
        color: inherit !important; 
    }

    /* Memastikan Text di Header Table Tetap Putih */
    .table-header-row th {
        color: #ffffff !important;
    }
    
    /* Scrollbar minimalis horizontal */
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    
    #approvalTable td, #approvalTable th {
        vertical-align: middle !important;
    }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection