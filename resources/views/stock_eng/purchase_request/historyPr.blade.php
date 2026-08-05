@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup { border-radius: 1rem !important; font-family: 'Nunito', sans-serif !important; }
    .dark .swal2-popup { background-color: #0f172a !important; border: 1px solid #1e293b !important; }
    .dark .swal2-title, .dark .swal2-html-container { color: #f8fafc !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-orange-200 bg-orange-50 dark:bg-orange-950/30 dark:border-orange-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-orange-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-orange-800 dark:text-orange-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">SYSTEM RECORD:</span> 
            Total {{ $historyPr->total() }} purchase request records logged in history log.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Engineering Purchase Request History</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS KARAWANG</p>
        </div>
        
        <div class="flex items-center gap-3">
            <a href="{{ route('purchase.request.index') }}"
               class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-red-500 via-orange-500 to-yellow-500 px-3 py-2 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 uppercase tracking-wider font-nunito"
            >
                <svg class="w-3.5 h-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="3" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                New Request
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL RESPONSIF --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
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

            <div class="grid grid-cols-12 gap-2 w-full sm:w-auto order-1 sm:order-2">
                <div class="relative col-span-8 sm:w-60 sm:block">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <form action="{{ url()->current() }}" method="GET" class="w-full">
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" id="tableSearch" placeholder="Search Request..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-orange-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                <button type="button" onclick="exportTableToCSV('purchase-requests-history.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
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
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[2130px]" id="historyPrTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-orange-600 dark:bg-orange-950/80 text-white dark:text-orange-200 font-nunito table-header-row">
                        <th class="px-2 py-3.5 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-orange-400 bg-transparent text-orange-600 focus:ring-orange-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-2 py-3.5 w-[60px] border-l border-orange-500 dark:bg-orange-900/50">NO</th>
                        <th class="px-3 py-3.5 w-[260px] border-l border-orange-500 dark:bg-orange-900/50">PR NUMBER</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-orange-500 dark:bg-orange-900/50">NIK</th>
                        <th class="px-3 py-3.5 w-[150px] border-l border-orange-500 dark:bg-orange-900/50">Requester</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 dark:bg-orange-900/50">Sparepart ID</th>
                        <th class="px-3 py-3.5 w-[150px] border-l border-orange-500 dark:bg-orange-900/50">Part Number</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 dark:bg-orange-900/50">SAP Code</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 dark:bg-orange-900/50">Category</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-orange-500 dark:bg-orange-900/50">QTY PR</th>
                        <th class="px-2 py-3.5 w-[100px] border-l border-orange-500 dark:bg-orange-900/50">Priority</th>
                        <th class="px-4 py-3.5 w-[180px] border-l border-orange-500 dark:bg-orange-900/50">Destination</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 dark:bg-orange-900/50">Status</th>
                        <th class="px-4 py-3.5 w-[160px] border-l border-orange-500 dark:bg-orange-900/50 text-center">Remark</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 dark:bg-orange-900/50 text-center">Created At</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 dark:bg-orange-900/50 text-center">Updated At</th>
                        <th class="px-3 py-3.5 w-[90px] border-l border-orange-500 dark:bg-orange-900/50 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($historyPr as $index => $pr)
                    @php
                        $statusText = 'unknown';
                        $badgeClass = 'bg-slate-100 text-slate-950 border-slate-300';
                        $rawStatus = strtolower($pr->status ?? 'unknown');
                        
                        if(str_contains($rawStatus, 'pending') || str_contains($rawStatus, 'waiting')) {
                            $statusText = 'pending'; 
                            $badgeClass = 'bg-yellow-50 text-yellow-800 border-yellow-400';
                        } elseif(str_contains($rawStatus, 'checked')) {
                            $statusText = 'checked'; 
                            $badgeClass = 'bg-blue-50 text-blue-800 border-blue-400';
                        } elseif(str_contains($rawStatus, 'approved') || str_contains($rawStatus, 'success') || str_contains($rawStatus, 'done')) {
                            $statusText = 'approved'; 
                            $badgeClass = 'bg-emerald-50 text-emerald-800 border-emerald-400';
                        } elseif(str_contains($rawStatus, 'reject')) {
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
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-left">
                            {{ $pr->no_pr }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($pr->user)->nik ?? optional($pr->user)->nim ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-left">
                            {{ optional($pr->user)->name ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap">
                            {{ optional($pr->sparepart)->sparepart_id ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($pr->sparepart)->part_number ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($pr->sparepart)->sap_code ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap uppercase text-[11px]">
                            {{ optional($pr->sparepart)->category ?? '-' }}
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap font-black">
                            {{ $pr->qty_pr ?? 1 }} Pcs
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase whitespace-nowrap">
                            <span class="{{ strtolower($pr->priority) == 'urgent' ? 'font-black' : '' }}">
                                {{ $pr->priority }}
                            </span>
                        </td>
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left whitespace-normal break-words leading-tight">
                            {{ $pr->destination }}
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
                            <div class="font-bold">{{ $pr->updated_at ? $pr->updated_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $pr->updated_at ? $pr->updated_at->format('H:i') : '-' }} WIB</div>
                        </td>

                        {{-- ACTION BUTTON: DIRECT TO FULL DOKUMEN PREVIEW (previewPR.blade.php) --}}
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center">
                                <a href="{{ route('purchase.request.preview', $pr->id) }}" class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-orange-500 hover:bg-orange-600 text-white transition-all shadow-md active:scale-95 cursor-pointer" title="Preview PR Document">
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
                        <td colspan="17" class="py-10 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text text-slate-400">
                            No purchase request history records logged.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase text-black">
                Showing {{ $historyPr->firstItem() ?? 0 }} to {{ $historyPr->lastItem() ?? 0 }} of {{ $historyPr->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs custom-pagination text-black">
                {{ $historyPr->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('selectAllCheckbox').addEventListener('change', function() {
        let checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#historyPrTable tr");
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
    /* Paksa Font Nunito untuk komponen utama dokumen */
    .font-nunito, .swal2-popup, #historyPrTable, #historyPrTable td, #historyPrTable th { font-family: 'Nunito', sans-serif !important; }
    
    /* Paksa warna text hitam (#000000) ke seluruh baris data KECUALI kolom status (.status-td) */
    .table-body-data tr td:not(.status-td), .table-body-data tr td:not(.status-td) div, .table-empty-text { color: #000000 !important; }
    
    /* Menjaga pewarnaan bawaan kelas badge status Tailwind */
    .status-badge { color: inherit !important; }
    
    .table-header-row th { color: #ffffff !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 7px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #historyPrTable td, #historyPrTable th { vertical-align: middle !important; }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection