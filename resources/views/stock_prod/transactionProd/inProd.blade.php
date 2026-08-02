@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-html-container { font-family: 'Nunito', sans-serif !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
    
    #historyTable td { color: #000000 !important; vertical-align: middle !important; }
    .dark #historyTable td { color: #cbd5e1 !important; }
    #historyTable th { vertical-align: middle !important; }
    nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
    nav[role="navigation"] div:first-child { display: none; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Alert Banner --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 dark:bg-blue-950/30 dark:border-blue-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-blue-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-blue-800 dark:text-blue-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">PRODUCTION STOCK IN:</span> 
            Monitor and log incoming component movements, barcode arrivals, and internal line supplies.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight uppercase">Production Stock In Activities</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Track and manage received nozzles on the production floor</p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ Route::has('prod.transaction.in.scan') ? route('prod.transaction.in.scan') : '#' }}" 
               class="inline-flex items-center justify-center gap-1.5 h-9 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 px-4 text-[11px] font-black text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-36 text-center cursor-pointer no-underline">
                <i class="fa-solid fa-qrcode mr-1"></i> Scan IN
            </a>
            
            <a href="{{ route('prod.transaction.in.manual') }}" 
               class="inline-flex items-center justify-center gap-1.5 h-9 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-4 text-[11px] font-black text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-36 text-center cursor-pointer no-underline">
                <i class="fa-solid fa-keyboard mr-1"></i> Manual IN
            </a>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <div>
                <h3 class="text-sm md:text-base font-black text-slate-950 dark:text-white tracking-tight uppercase">
                    Production Inflow History
                </h3>
            </div>
            
            {{-- Filter Pills System --}}
            <div class="flex items-center">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-inner overflow-x-auto max-w-full scrollbar-none">
                    <button type="button" onclick="filterTable('all', this)" class="filter-btn px-3 py-1 text-xs font-black rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white whitespace-nowrap">
                        All
                    </button>
                    <button type="button" onclick="filterTable('success', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white whitespace-nowrap">
                        Success
                    </button>
                    <button type="button" onclick="filterTable('pending', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white whitespace-nowrap">
                        Pending
                    </button>
                    <button type="button" onclick="filterTable('manual in', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white whitespace-nowrap">
                        Manual In
                    </button>
                    <button type="button" onclick="filterTable('scan in', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white whitespace-nowrap">
                        Scan In
                    </button>
                </div>
            </div>
        </div>

        {{-- Scrollable Table Container --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1860px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">NO</th>
                        <th class="px-3 py-3.5 w-[180px] border-l border-blue-500 dark:border-blue-900/50">Transaction ID</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 dark:border-blue-900/50">NIK PIC</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:border-blue-900/50">Line Target</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Sparepart / Nozzle</th>
                        <th class="px-3 py-3.5 w-[180px] border-l border-blue-500 dark:border-blue-900/50">Ref Out ID</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Request No</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Barcode ID</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Stock Prod ID</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-blue-500 dark:border-blue-900/50">Qty In</th>
                        <th class="px-2 py-3.5 w-[100px] border-l border-blue-500 dark:border-blue-900/50">Status</th>
                        <th class="px-2 py-3.5 w-[110px] border-l border-blue-500 dark:border-blue-900/50">Process Type</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:border-blue-900/50">Remark / Notes</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 dark:border-blue-900/50">Created At</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 dark:border-blue-900/50">Updated At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
                    @forelse($history as $key => $log)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <!-- 1. NO -->
                        <td class="px-2 py-3.5 text-slate-500 text-center">
                            {{ (method_exists($history, 'firstItem')) ? ($history->firstItem() + $key) : ($key + 1) }}
                        </td>
                        
                        <!-- 2. TRANSACTION ID (Menggunakan field baru tx_id) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-center whitespace-nowrap text-slate-900 dark:text-slate-100">
                            {{ $log->tx_id ?? $log->id }}
                        </td>
                        
                        <!-- 3. NIK PIC (Menggunakan field baru nik_karyawan) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-center text-[12px]">
                            {{ $log->nik_karyawan ?? '-' }}
                        </td>
                        
                        <!-- 4. LINE TARGET (Relasi baru: stockProd -> line -> name) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 shadow-sm">
                                {{ $log->stockProd->line->name ?? 'ID: '.($log->stockProd->line_id ?? '-') }}
                            </span>
                        </td>
                        
                        <!-- 5. SPAREPART / NOZZLE (Relasi baru: stockProd -> sparepart -> name) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-bold text-center tracking-tight truncate max-w-[130px]" title="{{ $log->stockProd->sparepart->name ?? '-' }}">
                            {{ $log->stockProd->sparepart->name ?? '-' }}
                        </td>
                        
                        <!-- 6. REF OUT ID (Menggunakan field baru ref_tx_id / reference transaction jika ada) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-center whitespace-nowrap">
                            {{ $log->ref_tx_id ?? $log->transaction_out_id ?? '-' }}
                        </td>
                        
                        <!-- 7. REQUEST NO -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 font-mono shadow-sm">
                                {{ $log->request_no ?? '-' }}
                            </span>
                        </td>
                        
                        <!-- 8. BARCODE ID -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-center tracking-tight text-blue-600 dark:text-blue-400">
                            {{ $log->barcode_id ?? '-' }}
                        </td>
                        
                        <!-- 9. STOCK PROD ID -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-center">
                            {{ $log->stock_prod_id }}
                        </td>
                        
                        <!-- 10. QTY IN (Menggunakan field baru qty_transaction) -->
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 shadow-sm">
                                +{{ $log->qty_transaction ?? 0 }}
                            </span>
                        </td>
                        
                        <!-- 11. STATUS -->
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <span class="status-cell inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border shadow-sm
                                @if(strtolower($log->status ?? '') == 'success') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-800
                                @elseif(strtolower($log->status ?? '') == 'pending') bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-800
                                @else bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/20 dark:text-rose-400 dark:border-rose-800 @endif">
                                {{ $log->status ?? 'success' }}
                            </span>
                        </td>
                        
                        <!-- 12. PROCESS TYPE (Menggunakan field baru process_type - Ditempel di class remark-cell agar JS filter tidak rusak) -->
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            @php
                                $procText = $log->process_type ?? '';
                                $procLower = strtolower($procText);
                                $isManual = str_contains($procLower, 'manual');
                                $isScan = str_contains($procLower, 'scan');
                            @endphp
                            <span class="remark-cell inline-flex items-center justify-center rounded-lg px-2 py-0.5 text-[9px] font-black tracking-tight uppercase border shadow-sm
                                @if($isManual) bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                                @elseif($isScan) bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                                @else bg-slate-50 text-slate-600 border border-slate-200 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600 @endif">
                                {{ $procText ? $log->process_type : '-' }} 
                            </span>
                        </td>
                        
                        <!-- 13. REMARK / NOTES (Menggunakan field baru remark) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left truncate max-w-[160px]" title="{{ $log->remark }}">
                            {{ $log->remark ?? '-' }}
                        </td>
                        
                        <!-- 14. CREATED AT -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            @php
                                $createdAt = $log->created_at ? ($log->created_at instanceof \Carbon\Carbon ? $log->created_at : \Carbon\Carbon::parse($log->created_at)) : null;
                            @endphp
                            <div class="text-[12px] font-bold text-black dark:text-white leading-tight">
                                {{ $createdAt ? $createdAt->format('d/m/y') : '-' }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 leading-none mt-0.5">
                                {{ $createdAt ? $createdAt->format('H:i') : '' }}
                            </div>
                        </td>
                        
                        <!-- 15. UPDATED AT -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            @php
                                $updatedAt = $log->updated_at ? ($log->updated_at instanceof \Carbon\Carbon ? $log->updated_at : \Carbon\Carbon::parse($log->updated_at)) : null;
                            @endphp
                            <div class="text-[12px] font-bold text-black dark:text-white leading-tight">
                                {{ $updatedAt ? $updatedAt->format('d/m/y') : '-' }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 leading-none mt-0.5">
                                {{ $updatedAt ? $updatedAt->format('H:i') : '' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="15" class="py-10 text-center text-slate-400 italic font-medium text-[13px]">
                            No recent production incoming history records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Interface --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase text-center sm:text-left">
                Showing {{ (isset($history) && method_exists($history, 'firstItem')) ? ($history->firstItem() ?? 0) : 0 }} 
                to {{ (isset($history) && method_exists($history, 'lastItem')) ? ($history->lastItem() ?? 0) : 0 }} 
                of {{ (isset($history) && method_exists($history, 'total')) ? ($history->total() ?? 0) : 0 }} entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs text-black dark:text-white w-full sm:w-auto">
                @if(isset($history) && method_exists($history, 'links'))
                    {{ $history->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Realtime Client-side Row Filter Script --}}
<script>
    function filterTable(criteria, element) {
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.className = "filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white whitespace-nowrap";
        });

        if (element) {
            element.className = "filter-btn px-3 py-1 text-xs font-black rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white whitespace-nowrap";
        }

        const rows = document.querySelectorAll('.table-row-item');
        
        rows.forEach(row => {
            if (criteria === 'all') {
                row.style.display = '';
                return;
            }

            const statusCell = row.querySelector('.status-cell');
            const remarkCell = row.querySelector('.remark-cell');

            if (!statusCell || !remarkCell) return;

            const statusText = statusCell.textContent.trim().toLowerCase();
            const remarkText = remarkCell.textContent.trim().toLowerCase();

            if (criteria === 'success' || criteria === 'pending') {
                row.style.display = (statusText === criteria) ? '' : 'none';
            } else if (criteria === 'manual in') {
                row.style.display = (remarkText.includes('manual')) ? '' : 'none';
            } else if (criteria === 'scan in') {
                row.style.display = (remarkText.includes('scan')) ? '' : 'none';
            }
        });
    }
</script>
@endsection