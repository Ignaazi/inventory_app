@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup {
        border-radius: 0.5rem !important;
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

    {{-- Alert Banner Top Counter (TEMA BIRU) --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-950/30 dark:border-blue-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-blue-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-blue-800 dark:text-blue-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">STOCK OUT ACTIVITIES:</span> 
            Total {{ (isset($history) && method_exists($history, 'total')) ? $history->total() : count($history) }} outgoing sparepart logs recorded.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase">Stock Out Activities</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Track and monitor your recent sparepart outgoing logs and dispatch history</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('eng.out.scan') }}" 
               class="inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-600 px-3.5 text-[11px] font-black text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-auto text-center no-underline">
                <i class="fa-solid fa-qrcode text-xs"></i> Scan OUT
            </a>
            
            <a href="{{ route('eng.out.scan') }}?mode=manual" 
               class="inline-flex items-center justify-center gap-1.5 h-9 rounded-lg bg-gradient-to-r from-indigo-600 via-sky-600 to-blue-600 px-3.5 text-[11px] font-black text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-auto text-center no-underline">
                <i class="fa-solid fa-keyboard text-xs"></i> Manual OUT
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF (Show Entries, Search, Export CSV) --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <!-- Entries Controller -->
            <div class="flex flex-wrap items-center gap-3 text-xs md:text-[13px] font-black text-slate-950 dark:text-slate-300 order-2 sm:order-1">
                <div class="flex items-center gap-1.5">
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
            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative col-span-8 sm:w-60 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ url()->current() }}" method="GET" class="w-full">
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" id="tableSearch" placeholder="Search TX ID, Sparepart, NIK..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('stock-out-activities.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
                    <span class="hidden sm:inline">Export CSV</span>
                    <span class="sm:hidden">CSV</span>
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- AREA SCROLL HORIZONTAL TABEL TEMATIC BLUE --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1700px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito table-header-row">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-blue-400 bg-transparent text-blue-600 focus:ring-blue-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-blue-500 bg-blue-700/30">NO</th>
                        <th class="px-3 py-3.5 w-[200px] border-l border-blue-500 bg-blue-700/30">TRANSACTION OUT ID</th>
                        <th class="px-3 py-3.5 w-[170px] border-l border-blue-500 bg-blue-700/30">OPERATOR</th>
                        <th class="px-3 py-3.5 w-[170px] border-l border-blue-500 bg-blue-700/30">REQ PRODUCTION NO</th>
                        <th class="px-3 py-3.5 w-[180px] border-l border-blue-500 bg-blue-700/30">BARCODE ID</th>
                        <th class="px-3 py-3.5 w-[150px] border-l border-blue-500 bg-blue-700/30">SPAREPART ID</th>
                        <th class="px-2 py-3.5 w-[110px] border-l border-blue-500 bg-blue-700/30">RAK ID</th>
                        <th class="px-2 py-3.5 w-[110px] border-l border-blue-500 bg-blue-700/30">QTY OUT</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 bg-blue-700/30">STATUS</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 bg-blue-700/30">PROCESS TYPE</th>
                        <th class="px-4 py-3.5 w-[220px] border-l border-blue-500 bg-blue-700/30 text-left">REMARK</th>
                        <th class="px-3 py-3.5 w-[150px] border-l border-blue-500 bg-blue-700/30 text-center">TIMESTAMP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($history as $index => $log)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        {{-- 1. NO --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            {{ (method_exists($history, 'firstItem')) ? ($history->firstItem() + $index) : ($index + 1) }}
                        </td>

                        {{-- 2. TRANSACTION OUT ID --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold font-mono text-blue-600 dark:text-blue-400 text-center whitespace-nowrap select-all">
                            {{ $log->tx_id ?? '-' }}
                        </td>

                        {{-- 3. OPERATOR --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center text-[12px] whitespace-nowrap">
                            <div class="font-extrabold text-slate-900 dark:text-white">
                                {{ $log->operator_name ?? 'Unknown' }}
                            </div>
                            <div class="text-[10px] text-slate-400 font-mono">
                                NIK: {{ $log->operator_nik ?? '-' }}
                            </div>
                        </td>

                        {{-- 4. REQ PRODUCTION NO --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide text-center whitespace-nowrap text-blue-600 dark:text-blue-400">
                            {{ $log->production_req_no ?? '-' }}
                        </td>

                        {{-- 5. BARCODE ID --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-amber-600 dark:text-amber-400 text-center whitespace-nowrap">
                            {{ $log->barcode_id ?? '-' }}
                        </td>

                        {{-- 6. SPAREPART ID --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-emerald-600 dark:text-emerald-400 text-center whitespace-nowrap">
                            {{ $log->sparepart_id ?? '-' }}
                        </td>

                        {{-- 7. RAK ID --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-purple-600 dark:text-purple-400 text-center whitespace-nowrap">
                            {{ $log->rak_id ?? '-' }}
                        </td>

                        {{-- 8. QTY OUT --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[11px] font-black border border-rose-200 bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-900/50">
                                -{{ number_format($log->qty_transaction ?? 0) }} Pcs
                            </span>
                        </td>

                        {{-- 9. STATUS --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border
                                @if(strtolower($log->status ?? '') == 'success') border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-900/50
                                @elseif(strtolower($log->status ?? '') == 'pending') border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-900/50
                                @else border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-900/50 @endif">
                                {{ $log->status ?? 'success' }}
                            </span>
                        </td>

                        {{-- 10. PROCESS TYPE --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            @php $isProcManual = strtolower($log->process_type ?? '') === 'manual'; @endphp
                            <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border
                                @if($isProcManual) border-purple-200 bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-900/50
                                @else border-blue-200 bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-900/50 @endif">
                                {{ $log->process_type ?? 'Scan' }} Out
                            </span>
                        </td>

                        {{-- 11. REMARK --}}
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left font-semibold text-slate-700 dark:text-slate-300 truncate max-w-[220px]" title="{{ $log->remark }}">
                            {{ $log->remark ?? '-' }}
                        </td>

                        {{-- 12. TIMESTAMP --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            @php
                                $createdAt = null;
                                if ($log->created_at) {
                                    $createdAt = $log->created_at instanceof \Carbon\Carbon ? $log->created_at : \Carbon\Carbon::parse($log->created_at);
                                }
                            @endphp
                            <div class="font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                {{ $createdAt ? $createdAt->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] mt-0.5 text-slate-500">
                                {{ $createdAt ? $createdAt->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="13" class="py-10 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text">
                            No stock out logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase font-nunito text-center sm:text-left text-black">
                Showing {{ (isset($history) && method_exists($history, 'firstItem')) ? ($history->firstItem() ?? 0) : 0 }} 
                to {{ (isset($history) && method_exists($history, 'lastItem')) ? ($history->lastItem() ?? 0) : 0 }} 
                of {{ (isset($history) && method_exists($history, 'total')) ? ($history->total() ?? 0) : 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination text-black">
                @if(isset($history) && method_exists($history, 'links'))
                    {{ $history->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

<script>
    // Select All Checkbox Handler
    document.getElementById('selectAllCheckbox')?.addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    // Export Table Data to CSV
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

    // Trigger SweetAlert Alerts
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", timer: 3000, showConfirmButton: false });
    @endif
</script>

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container, #historyTable { 
        font-family: 'Nunito', sans-serif !important; 
    }

    .table-body-data tr td, 
    .table-body-data tr td div,
    .table-empty-text {
        color: #000000 !important;
    }

    .dark .table-body-data tr td {
        color: #cbd5e1 !important;
    }

    .table-header-row th {
        color: #ffffff !important;
    }
    
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
    
    #historyTable td, #historyTable th {
        vertical-align: middle !important;
    }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection