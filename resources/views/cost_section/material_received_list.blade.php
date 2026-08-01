@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup {
        border-radius: 1rem !important;
        font-family: 'Nunito', sans-serif !important;
    }
    .dark .swal2-popup {
        background-color: #0f172a !important; 
        border: 1px solid #1e293b !important; 
    }
    .dark .swal2-title, .dark .swal2-html-container {
        color: #f8fafc !important; 
    }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter & Sessions --}}
    @if(session('success') || session('error'))
        <div class="mb-4 flex items-center gap-2 rounded-xl border {{ session('success') ? 'border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 text-emerald-800 dark:text-emerald-400' : 'border-rose-200 bg-rose-50 dark:bg-rose-950/30 dark:border-rose-900/50 text-rose-800 dark:text-rose-400' }} px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
            <span class="h-2 w-2 shrink-0 rounded-full {{ session('success') ? 'bg-emerald-500' : 'bg-rose-500' }} animate-pulse"></span>
            <p class="text-[12px] md:text-[14px] font-bold font-nunito leading-tight">
                <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">SYSTEM NOTIFICATION:</span> 
                {{ session('success') ?? session('error') }}
            </p>
        </div>
    @else
        <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
            <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
            <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
                <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">SYSTEM RECORD:</span> 
                Total {{ $materialReceived->total() }} material received records have been archived in database log tracker.
            </p>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Material Received History Tracker</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS KARAWANG — MATERIAL CONTROL & COSTING AUDIT SECTION</p>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF --}}
        <div class="mb-4 flex flex-col gap-4 px-4 lg:flex-row lg:items-center lg:justify-between font-nunito">
            
            <div class="flex flex-wrap items-center gap-4 order-2 lg:order-1">
                <!-- Entries Controller -->
                <div class="flex items-center gap-1.5 text-xs md:text-[13px] font-black text-slate-950 dark:text-slate-300">
                    <span>Show</span>
                    <form action="{{ url()->current() }}" method="GET" id="entriesForm">
                        <select name="per_page" onchange="this.form.submit()" class="rounded-md border border-gray-300 dark:border-slate-700 bg-transparent px-2 py-1 outline-none text-slate-950 dark:text-white font-black cursor-pointer font-nunito text-xs">
                            <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }} class="dark:bg-slate-900">10</option>
                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }} class="dark:bg-slate-900">25</option>
                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }} class="dark:bg-slate-900">50</option>
                        </select>
                        @if(request('search'))
                            <input type="hidden" name="search" value="{{ request('search') }}">
                        @endif
                    </form>
                    <span>entries</span>
                </div>
            </div>

            <!-- Search & Export Grid -->
            <div class="grid grid-cols-12 gap-2 w-full lg:w-auto order-1 lg:order-2">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative col-span-8 sm:w-64 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ route('costing.material.list') }}" method="GET" class="w-full">
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <input type="text" name="search" value="{{ $search ?? '' }}" id="tableSearch" placeholder="Search MR, PR, NIK, Sparepart..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('material-received-history.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
                    <span class="hidden sm:inline">Export CSV</span>
                    <span class="sm:hidden">CSV</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- AREA SCROLL HORIZONTAL (MIN-W 2200PX) --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[2200px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-orange-600 dark:bg-orange-950/80 text-white dark:text-orange-200 font-nunito table-header-row">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-orange-400 bg-transparent text-orange-600 focus:ring-orange-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-orange-500 bg-orange-700/30">NO</th>
                        <th class="px-3 py-3.5 w-[220px] border-l border-orange-500 bg-orange-700/30">MR NUMBER</th>
                        <th class="px-3 py-3.5 w-[220px] border-l border-orange-500 bg-orange-700/30">PR REFERENCE</th>
                        <th class="px-3 py-3.5 w-[100px] border-l border-orange-500 bg-orange-700/30">NIK</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-orange-500 bg-orange-700/30">Prepared By</th>
                        
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[140px]">Sparepart ID</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[200px]">Part Number</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[140px]">SAP Code</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[130px]">Category</th>
                        
                        {{-- KOLOM BARU DI SINI --}}
                        <th class="px-2 py-3.5 w-[120px] border-l border-orange-500 bg-orange-700/30">QTY PR</th>
                        <th class="px-2 py-3.5 w-[120px] border-l border-orange-500 bg-orange-700/30">QTY RECEIVED</th>
                        <th class="px-2 py-3.5 w-[160px] border-l border-orange-500 bg-orange-700/30">QTY STATUS</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-700/30">Status Flow</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-700/30 text-center">Created At</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-700/30 text-center">Updated At</th>
                        <th class="px-3 py-3.5 w-[100px] border-l border-orange-500 bg-orange-700/30 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($materialReceived as $index => $mr)
                    @php
                        // 1. Pengondisian Warna Status Flow
                        $statusText = 'unknown';
                        $badgeClass = 'bg-slate-100 text-slate-950 border-slate-300';
                        $rawStatus = strtolower($mr->status ?? 'unknown');
                        
                        if(str_contains($rawStatus, 'pending')) {
                            $statusText = 'pending';
                            $badgeClass = 'bg-amber-50 text-amber-950 border-amber-300';
                        } elseif(str_contains($rawStatus, 'checked')) {
                            $statusText = 'checked';
                            $badgeClass = 'bg-blue-50 text-blue-950 border-blue-300';
                        } elseif(str_contains($rawStatus, 'approved')) {
                            $statusText = 'approved';
                            $badgeClass = 'bg-emerald-50 text-emerald-950 border-emerald-300';
                        } elseif(str_contains($rawStatus, 'rejected')) {
                            $statusText = 'rejected';
                            $badgeClass = 'bg-rose-50 text-rose-950 border-rose-300';
                        }

                        // 2. PERBAIKAN & KALKULASI DINAMIS QTY STATUS (OPEN DENGAN SISA MINUSNYA)
                        $qtyStatusRaw = strtoupper($mr->qty_status ?? 'OPEN');
                        
                        if(str_contains(strtolower($qtyStatusRaw), 'open')) {
                            $qtyStatusClass = 'bg-amber-50 text-amber-950 border-amber-300'; // OPEN (Kuning)
                            
                            $qtyPr = optional($mr->purchaseRequest)->qty_pr ?? 0;
                            
                            if($qtyPr > 0) {
                                // Hitung total item terkumpul untuk PR ini sampai record ini dibuat
                                $totalAccumulated = \App\Models\Costing\MaterialReceived::where('purchase_request_id', $mr->purchase_request_id)
                                    ->whereIn('status', ['pending', 'checked', 'approved'])
                                    ->where('id', '<=', $mr->id) 
                                    ->sum('qty_received');
                                    
                                $shortage = max(0, $qtyPr - $totalAccumulated);
                                
                                if($shortage > 0) {
                                    $qtyStatusRaw = "OPEN (-" . number_format($shortage) . " Pcs)";
                                } else {
                                    $qtyStatusRaw = "CLOSE";
                                    $qtyStatusClass = 'bg-emerald-50 text-emerald-950 border-emerald-300';
                                }
                            } else {
                                $qtyStatusRaw = "OPEN";
                            }
                        } else {
                            $qtyStatusClass = 'bg-emerald-50 text-emerald-950 border-emerald-300'; // CLOSE (Hijau)
                            $qtyStatusRaw = "CLOSE";
                        }
                    @endphp
                    <tr class="material-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            {{ $materialReceived->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-left text-indigo-600 dark:text-indigo-400">
                            {{ $mr->no_mr ?? 'MR-SYSTEM-GEN' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-left text-slate-700 dark:text-slate-300 font-mono">
                            {{ $mr->purchaseRequest->no_pr ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($mr->user)->nik ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-left">
                            {{ optional($mr->user)->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-nowrap text-slate-900 dark:text-white">
                            {{ optional($mr->purchaseRequest->sparepart)->sparepart_id ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-left border-l border-gray-100 dark:border-slate-800 font-mono tracking-wide whitespace-nowrap text-slate-800 dark:text-slate-200 uppercase">
                            {{ optional($mr->purchaseRequest->sparepart)->part_number ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 tracking-wide font-mono text-indigo-600 dark:text-indigo-400">
                            {{ optional($mr->purchaseRequest->sparepart)->sap_code ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 tracking-wide uppercase text-[11px]">
                            {{ optional($mr->purchaseRequest->sparepart)->category ?? '-' }}
                        </td>

                        {{-- VALUE DATA BARU QTY PR --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-slate-900 dark:text-slate-100 font-extrabold bg-slate-50/50 dark:bg-slate-850/20">
                            {{ number_format(optional($mr->purchaseRequest)->qty_pr ?? 0) }} Pcs
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-orange-600 font-black">
                            {{ number_format($mr->qty_received) }} Pcs
                        </td>

                        {{-- DYNAMIC QTY STATUS BADGE --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 status-td">
                            <div class="flex justify-center items-center">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm status-badge {{ $qtyStatusClass }}">
                                    {{ $qtyStatusRaw }}
                                </span>
                            </div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 status-td">
                            <div class="flex justify-center items-center">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm status-badge {{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $mr->created_at ? $mr->created_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $mr->created_at ? $mr->created_at->format('H:i') : '-' }} WIB</div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $mr->updated_at ? $mr->updated_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $mr->updated_at ? $mr->updated_at->format('H:i') : '-' }} WIB</div>
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('costing.material.preview', $mr->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-500 hover:bg-orange-600 text-white transition-all shadow-md active:scale-95 cursor-pointer" title="View Preview Document">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        {{-- DISINI UDAH SAYA SINKRONKAN JADI COLSPAN 17 --}}
                        <td colspan="17" class="py-10 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text">
                            No archived material received documents found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase font-nunito text-center sm:text-left text-black">
                Showing {{ $materialReceived->firstItem() ?? 0 }} to {{ $materialReceived->lastItem() ?? 0 }} of {{ $materialReceived->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination text-black">
                {{ $materialReceived->appends(['search' => $search ?? ''])->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Fitur Export Otomatis mendeteksi kolom QTY PR yang baru
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#historyTable tr");
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
</script>

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container, #historyTable { 
        font-family: 'Nunito', sans-serif !important; 
    }

    .table-body-data tr td:not(.status-td), 
    .table-body-data tr td:not(.status-td) div,
    .table-empty-text {
        color: #000000 !important;
    }

    .status-badge {
        color: inherit !important; 
    }

    .table-header-row th {
        color: #ffffff !important;
    }
    
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    
    #historyTable td, #historyTable th {
        vertical-align: middle !important;
    }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection