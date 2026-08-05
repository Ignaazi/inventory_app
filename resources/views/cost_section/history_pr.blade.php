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

<div class="font-nunito w-full p-1 md:p-2 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Header Section - Diperbesar & Paling Pojok Kiri --}}
    <div class="mb-3 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 font-nunito px-1 pt-1">
        <div>
            <h2 class="text-2xl md:text-3xl font-black text-slate-900 dark:text-white tracking-tight leading-tight">Purchase Request History Log</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS KARAWANG — COSTING AUDIT SECTION</p>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-3 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF --}}
        <div class="mb-3 flex flex-col gap-3 px-3 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <!-- Entries Controller -->
            <div class="flex flex-wrap items-center gap-3 text-xs md:text-[13px] font-black text-slate-950 dark:text-slate-300 order-2 sm:order-1">
                <div class="flex items-center gap-1.5">
                    <span>Show</span>
                    <form action="{{ route('costing.pr.history') }}" method="GET" id="entriesForm">
                        <select name="per_page" onchange="this.form.submit()" class="rounded-md border border-gray-300 dark:border-slate-700 bg-transparent px-2 py-1 outline-none text-slate-950 dark:text-white font-black cursor-pointer font-nunito text-xs focus:ring-2 focus:ring-orange-500">
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
            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                {{-- LIVE SEARCH INPUT DENGAN AUTO-SUBMIT --}}
                <div class="relative col-span-8 sm:w-64 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400 pointer-events-none">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ route('costing.pr.history') }}" method="GET" id="searchForm" class="w-full">
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <input type="text" 
                               name="search" 
                               value="{{ $search ?? '' }}" 
                               id="tableSearch" 
                               placeholder="Search PR, NIK, Sparepart, Email..." 
                               class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-8 text-xs md:text-[13px] outline-none focus:border-orange-500 text-slate-950 dark:text-white font-bold font-nunito transition-all"
                               autocomplete="off">
                        
                        @if(!empty($search))
                            <a href="{{ route('costing.pr.history', ['per_page' => request('per_page')]) }}" class="absolute inset-y-0 right-2.5 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xs font-black">
                                ✕
                            </a>
                        @endif
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('costing-pr-history.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
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
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[2350px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-orange-600 dark:bg-orange-950/80 text-white dark:text-orange-200 font-nunito table-header-row">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-orange-400 bg-transparent text-orange-600 focus:ring-orange-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-orange-500 bg-orange-700/30">NO</th>
                        <th class="px-3 py-3.5 w-[220px] border-l border-orange-500 bg-orange-700/30">PR NUMBER</th>
                        <th class="px-3 py-3.5 w-[100px] border-l border-orange-500 bg-orange-700/30">NIK</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-orange-500 bg-orange-700/30">Requester</th>
                        
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[140px]">Sparepart ID</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[160px]">Part Number</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[140px]">SAP Code</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[130px]">Category</th>
                        
                        <th class="px-2 py-3.5 w-[90px] border-l border-orange-500 bg-orange-700/30">QTY PR</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-orange-500 bg-orange-700/30">Priority</th>
                        <th class="px-3 py-3.5 w-[180px] border-l border-orange-500 bg-orange-700/30">Destination Delivery</th>
                        
                        {{-- NOTIFICATION EMAIL --}}
                        <th class="px-3 py-3.5 w-[220px] border-l border-orange-500 bg-orange-700/30 text-center">Notification Email</th>
                        
                        <th class="px-3 py-3.5 w-[110px] border-l border-orange-500 bg-orange-700/30">Status</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 bg-orange-700/30 text-center w-[160px]">Remark / Notes</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 bg-orange-700/30 text-center">Created At</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 bg-orange-700/30 text-center">Updated At</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-700/30 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($historyPr as $index => $pr)
                    @php
                        $statusText = 'unknown';
                        $badgeClass = 'bg-slate-100 text-slate-950 border-slate-300';
                        $rawStatus = strtolower($pr->status ?? 'unknown');
                        
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
                    @endphp
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            {{ $historyPr->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-left text-indigo-600 dark:text-indigo-400">
                            {{ $pr->no_pr }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($pr->user)->nik ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-left">
                            {{ optional($pr->user)->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-nowrap text-slate-900 dark:text-white">
                            {{ optional($pr->sparepart)->sparepart_id ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-left border-l border-gray-100 dark:border-slate-800 font-mono tracking-wide whitespace-nowrap text-slate-800 dark:text-slate-200 uppercase">
                            {{ optional($pr->sparepart)->part_number ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 tracking-wide font-mono text-indigo-600 dark:text-indigo-400">
                            {{ optional($pr->sparepart)->sap_code ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 tracking-wide uppercase text-[11px]">
                            {{ optional($pr->sparepart)->category ?? '-' }}
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-orange-600 font-black">
                            {{ $pr->qty_pr }} Pcs
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase whitespace-nowrap">
                            <span class="{{ $pr->priority == 'urgent' ? 'text-rose-600 font-black' : 'text-slate-600' }}">
                                {{ $pr->priority }}
                            </span>
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-normal break-words text-left leading-normal">
                            {{ $pr->destination }}
                        </td>

                        {{-- NOTIFICATION EMAIL DATA --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-center font-mono text-[11.5px] text-blue-600 dark:text-blue-400">
                            {{ $pr->notification_email ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 status-td">
                            <div class="flex justify-center items-center">
                                <span class="inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm status-badge {{ $badgeClass }}">
                                    {{ $statusText }}
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left font-semibold tracking-wide whitespace-normal break-words leading-normal">
                            {{ $pr->remark ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold">{{ $pr->created_at ? $pr->created_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $pr->created_at ? $pr->created_at->format('H:i') : '-' }} WIB</div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $pr->updated_at ? $pr->updated_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $pr->updated_at ? $pr->updated_at->format('H:i') : '-' }} WIB</div>
                        </td>
                        
                        {{-- ACTION BUTTON --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('costing.pr.preview_approval', $pr->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-500 hover:bg-orange-600 text-white transition-all shadow-md active:scale-95 cursor-pointer" title="Preview Approval PR Document">
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
                        <td colspan="18" class="py-10 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text">
                            No historical purchase requests found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF DENGAN GRADIENT ORANGE --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-3.5 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase font-nunito text-center sm:text-left text-black dark:text-slate-300">
                Showing {{ $historyPr->firstItem() ?? 0 }} to {{ $historyPr->lastItem() ?? 0 }} of {{ $historyPr->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination">
                {{ $historyPr->appends(['search' => $search ?? '', 'per_page' => request('per_page')])->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // 1. Select All Checkboxes
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // 2. Export Table to CSV Function
    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#historyTable tr");
        for (let i = 0; i < rows.length; i++) {
            let row = [], cols = rows[i].querySelectorAll("td, th");
            for (let j = 1; j < cols.length - 1; j++) { 
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

    // 3. Auto-Submit Debounce untuk Kolom Pencarian (500ms delay saat mengetik)
    let searchTimeout = null;
    const searchInput = document.getElementById('tableSearch');
    const searchForm = document.getElementById('searchForm');

    if (searchInput && searchForm) {
        // Posisikan kursor di akhir kata saat reload jika sedang mencari
        if (searchInput.value.length > 0) {
            searchInput.focus();
            searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
        }

        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                searchForm.submit();
            }, 500); // Otomatis submit setelah 0.5 detik selesai mengetik
        });
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

    .dark .table-body-data tr td:not(.status-td), 
    .dark .table-body-data tr td:not(.status-td) div {
        color: #f1f5f9 !important;
    }

    .status-badge {
        color: inherit !important; 
    }

    .table-header-row th {
        color: #ffffff !important;
    }
    
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #ea580c; border-radius: 3px; }
    
    #historyTable td, #historyTable th {
        vertical-align: middle !important;
    }

    /* ========================================================= */
    /* FIX PAGINATION: SIMPLE, SINGLE BOX, EQUAL SIZE (34x34px)  */
    /* ========================================================= */
    .custom-pagination nav {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        box-shadow: none !important;
    }
    
    /* Sembunyikan elemen pembungkus bawaan Laravel */
    .custom-pagination nav div:first-child,
    .custom-pagination nav p { 
        display: none !important; 
    }

    /* RESET SEMUA PEMBUNGKUS LUAR (Hilangkan Kotak Ganda/Double Border) */
    .custom-pagination nav span.relative.z-0,
    .custom-pagination nav span[aria-disabled="true"],
    .custom-pagination nav span[aria-current="page"] {
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        box-shadow: none !important;
        display: inline-flex !important;
    }

    /* UKURAN UTAMA TOMBOL (PRESISI 34px x 34px SAMA RATA) */
    .custom-pagination nav a, 
    .custom-pagination nav span[aria-current="page"] > span,
    .custom-pagination nav span[aria-disabled="true"] > span {
        width: 34px !important;
        height: 34px !important;
        min-width: 34px !important;
        min-height: 34px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 8px !important;
        font-size: 13px !important;
        font-weight: 800 !important;
        padding: 0 !important;
        margin: 0 !important;
        box-sizing: border-box !important;
        transition: all 0.2s ease-in-out !important;
    }

    /* 1. TOMBOL BIASA & PANAH INAKTIF */
    .custom-pagination nav a {
        background-color: #fff7ed !important;
        color: #c2410c !important;
        border: 1px solid #ffedd5 !important;
    }

    /* Hover effect */
    .custom-pagination nav a:hover {
        background-color: #ea580c !important;
        color: #ffffff !important;
        border-color: #ea580c !important;
        transform: translateY(-1px);
    }

    /* 2. TOMBOL AKTIF (HALAMAN SAAT INI - ORANGE SOLID) */
    .custom-pagination nav span[aria-current="page"] > span {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
        color: #ffffff !important;
        border: 1px solid #ea580c !important;
        box-shadow: 0 2px 5px rgba(234, 88, 12, 0.3) !important;
    }

    /* 3. TOMBOL PANAH MATI / DISABLED (Contoh: Pas di Halaman 1, panah `<` mati) */
    .custom-pagination nav span[aria-disabled="true"] > span {
        background-color: #f8fafc !important;
        color: #cbd5e1 !important;
        border: 1px solid #e2e8f0 !important;
        cursor: not-allowed !important;
        opacity: 0.7;
    }

    /* Rapikan Ikon Panah SVG */
    .custom-pagination nav svg { 
        width: 14px !important; 
        height: 14px !important; 
        display: block !important; 
        margin: auto !important;
    }
</style>
@endsection