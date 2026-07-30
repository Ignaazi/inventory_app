@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">APPROVAL HISTORY LOGS:</span> 
            Track and monitor the recent sparepart request authorization history records.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight uppercase">History Approvals</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Review recent engineering electronic authorization records</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="#" 
               class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3 text-[11px] font-bold text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-36 text-center cursor-pointer no-underline">
                Pending Approval
            </a>
        </div>
    </div>

    {{-- Table Container --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <!-- Filter Status Buttons -->
            <div class="flex items-center order-2 sm:order-1">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-inner">
                    <button type="button" onclick="filterTable('all', this)" class="filter-btn px-3 py-1 text-xs font-black rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white">
                        All
                    </button>
                    <button type="button" onclick="filterTable('Approved', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Approved
                    </button>
                    <button type="button" onclick="filterTable('Checked by Staff', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Checked
                    </button>
                    <button type="button" onclick="filterTable('Pending', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Pending
                    </button>
                    <button type="button" onclick="filterTable('Rejected', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Rejected
                    </button>
                </div>
            </div>

            <!-- Search Bar -->
            <div class="flex items-center w-full sm:w-auto order-1 sm:order-2 justify-end">
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="tableSearch" placeholder="Search request..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-black dark:text-white font-bold font-nunito">
                </div>
            </div>
        </div>

        {{-- Scrollable Table --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1150px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">NO</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Request No</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Requestor (NIK)</th>
                        <th class="px-4 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">Sparepart ID</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">SAP Code</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-blue-500 dark:border-blue-900/50">Line</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 dark:border-blue-900/50">Qty Req</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Checked By</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Approved By</th>
                        <th class="px-2 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">Status</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Processed At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
                    @forelse($history as $index => $log)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <!-- NO -->
                        <td class="px-2 py-3.5 text-slate-500 text-center">
                            {{ $history->firstItem() + $index }}
                        </td>
                        
                        <!-- REQUEST NO -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-[12px] whitespace-nowrap">
                            {{ $log->request_no }}
                        </td>
                        
                        <!-- REQUESTOR (NIK) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left">
                            <span class="block font-black text-black dark:text-white leading-tight">{{ $log->user->name ?? 'Unknown' }}</span>
                            <span class="block text-[10px] text-slate-400 font-mono mt-0.5">NIK: {{ $log->user->nik ?? '-' }}</span>
                        </td>
                        
                        <!-- SPAREPART ID -->
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide text-center text-blue-600 dark:text-blue-400">
                            {{ $log->sparepart_id ?? '-' }}
                        </td>
                        
                        <!-- SAP CODE -->
                        <td class="px-3 py-3.5 font-mono border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $log->sap_code ?? '-' }}
                        </td>
                        
                        <!-- LINE MACHINE -->
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 uppercase text-center">
                            {{ $log->line_machine }}
                        </td>
                        
                        <!-- QTY REQUESTED -->
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <span class="inline-flex items-center justify-center rounded-lg px-2 py-0.5 text-[11px] font-black bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-300">
                                {{ $log->qty_req }} Pcs
                            </span>
                        </td>

                        <!-- CHECKED BY (STAFF) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center truncate" title="{{ $log->approved_by ?? '-' }}">
                            {{ $log->approved_by ?? '-' }}
                        </td>

                        <!-- APPROVED BY (SPV) -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center truncate" title="{{ $log->spv_name ?? '-' }}">
                            {{ $log->spv_name ?? '-' }}
                        </td>
                        
                        <!-- STATUS -->
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            @php
                                $statusLower = strtolower($log->status);
                            @endphp
                            <div class="flex justify-center items-center">
                                <span class="status-cell inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border
                                    @if($statusLower == 'approved') bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-500/20 dark:text-emerald-400 dark:border-emerald-800
                                    @elseif($statusLower == 'checked by staff') bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-500/20 dark:text-blue-400 dark:border-blue-800
                                    @elseif($statusLower == 'pending') bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-500/20 dark:text-amber-400 dark:border-amber-800
                                    @else bg-rose-50 text-rose-700 border-rose-200 dark:bg-rose-500/20 dark:text-rose-400 dark:border-rose-800 @endif">
                                    {{ $log->status ?? 'PENDING' }}
                                </span>
                            </div>
                        </td>
                        
                        <!-- PROCESSED AT -->
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-600 dark:text-slate-400 text-center">
                            <div class="text-[12px] font-bold text-black dark:text-white leading-tight">
                                {{ $log->processed_at ? $log->processed_at->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 leading-none mt-0.5">
                                {{ $log->processed_at ? $log->processed_at->format('H:i') : '' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito">
                            No approval history records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase font-nunito text-center sm:text-left">
                Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito text-black dark:text-white w-full sm:w-auto">
                {{ $history->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    let searchTimer;
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        clearTimeout(searchTimer);
        let query = this.value;
        searchTimer = setTimeout(() => {
            let url = new URL(window.location.href);
            url.searchParams.set('search', query);
            url.searchParams.set('page', 1);
            window.location.href = url.toString();
        }, 600);
    });

    window.addEventListener('DOMContentLoaded', () => {
        let urlParams = new URLSearchParams(window.location.search);
        if(urlParams.has('search')){
            document.getElementById('tableSearch').value = urlParams.get('search');
            document.getElementById('tableSearch').focus();
        }
        
        if(urlParams.has('filter')){
            let filterVal = urlParams.get('filter');
            document.querySelectorAll('.filter-btn').forEach(btn => {
                if(btn.innerText.trim().toLowerCase() === filterVal.toLowerCase()){
                    setActiveButton(btn);
                }
            });
        }
    });

    function filterTable(criteria, element) {
        let url = new URL(window.location.href);
        if(criteria === 'all') {
            url.searchParams.delete('filter');
        } else {
            url.searchParams.set('filter', criteria);
        }
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }

    function setActiveButton(element) {
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
            btn.classList.add('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
        });
        element.classList.add('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
    }

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", timer: 3000, showConfirmButton: false });
    @endif
</script>
@endsection