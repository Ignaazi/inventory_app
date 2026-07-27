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
        border: 1px solid #1e293b !important;
    }
    .dark .swal2-title, .dark .swal2-html-container {
        color: #f8fafc !important;
    }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">STOCK IN ACTIVITIES:</span> 
            Track and monitor your recent sparepart incoming logs and history records.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight uppercase">Stock In Activities</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Track your recent sparepart incoming activities</p>
        </div>

        {{-- ACTIONS BUTTON - UKURAN LEBIH KECIL (h-8) --}}
        <div class="flex items-center gap-2 w-full sm:w-auto">
            {{-- Tombol Scan IN - OREN GRADIENT --}}
            <a href="{{ route('eng.in.scan') }}" 
               class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 px-3 text-[11px] font-bold text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-28 text-center cursor-pointer no-underline">
                Scan IN
            </a>
            
            {{-- Tombol Manual IN - BIRU GRADIENT --}}
            <a href="{{ route('eng.in.manual') }}" 
               class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3 text-[11px] font-bold text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-28 text-center cursor-pointer no-underline">
                Manual IN
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL & FILTER KATEGORI --}}
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            
            <!-- Custom Filters Buttons Area -->
            <div class="flex items-center order-2 sm:order-1">
                <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-slate-700 shadow-inner">
                    <button type="button" onclick="filterTable('all', this)" class="filter-btn px-3 py-1 text-xs font-black rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white">
                        All
                    </button>
                    <button type="button" onclick="filterTable('success', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Success
                    </button>
                    <button type="button" onclick="filterTable('pending', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Pending
                    </button>
                    <button type="button" onclick="filterTable('manual in', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Manual In
                    </button>
                    <button type="button" onclick="filterTable('scan in', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
                        Scan In
                    </button>
                </div>
            </div>

            <!-- Search Bar Grid -->
            <div class="flex items-center w-full sm:w-auto order-1 sm:order-2 justify-end">
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="tableSearch" placeholder="Search logs..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-black dark:text-white font-bold font-nunito">
                </div>
            </div>
        </div>

        {{-- AREA SCROLL HORIZONTAL --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1300px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">NO</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 dark:border-blue-900/50">NIK</th>
                        <th class="px-4 py-3.5 w-[180px] border-l border-blue-500 dark:border-blue-900/50">No Nozzle</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">Part No</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:border-blue-900/50">SAP Code</th>
                        <th class="px-2 py-3.5 w-[100px] border-l border-blue-500 dark:border-blue-900/50">RAK</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-blue-500 dark:border-blue-900/50">Qty IN</th>
                        <th class="px-2 py-3.5 w-[105px] border-l border-blue-500 dark:border-blue-900/50">Status</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">Remark</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:border-blue-900/50 text-left w-[180px]">Comment</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Created At</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
                    @forelse($history as $index => $log)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        
                        <td class="px-2 py-3.5 text-slate-500">
                            {{ $history->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $log->nik ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-normal break-words leading-normal">
                            {{ $log->stockEng->sparepart->name ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 font-mono border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $log->stockEng->sparepart->part_number ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 font-mono border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $log->stockEng->sparepart->sap_code ?? '-' }}
                        </td>

                        <td class="px-2 py-3.5 font-mono border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ $log->stockEng->rak->nama_rak ?? '-' }}
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <span class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-black bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                                +{{ $log->qty_in ?? ($log->qty_added ?? 0) }}
                            </span>
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <span class="status-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-black tracking-tight uppercase
                                    @if(strtolower($log->status) == 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                                    @elseif(strtolower($log->status) == 'pending') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                                    @else bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 @endif">
                                    {{ $log->status }}
                                </span>
                            </div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            @php
                                $remarkText = $log->remark ?? '';
                                $remarkLower = strtolower($remarkText);
                                $isManual = str_contains($remarkLower, 'manual');
                                $isScan = str_contains($remarkLower, 'scan');
                            @endphp
                            <div class="flex justify-center items-center">
                                <span class="remark-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[9px] font-black tracking-tight uppercase
                                    @if($isManual) bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                                    @elseif($isScan) bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                                    @else bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600 @endif">
                                    {{ $remarkText ? $log->remark : '-' }} 
                                </span>
                            </div>
                        </td>

                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left font-semibold text-slate-600 dark:text-slate-300 max-w-[180px] truncate" title="{{ $log->comment }}">
                            {{ $log->comment ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-600 dark:text-slate-400">
                            <div class="text-[12px] font-bold text-black dark:text-white leading-tight">
                                {{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 leading-none mt-0.5">
                                {{ $log->created_at ? $log->created_at->format('H:i') : '' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="11" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito">No entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
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
    // Live Search Client Side untuk menyaring baris berdasarkan input
    document.getElementById('tableSearch').addEventListener('keyup', function() {
        let value = this.value.toLowerCase();
        let rows = document.querySelectorAll('#historyTable tbody .table-row-item');
        
        rows.forEach(row => {
            let text = row.textContent.toLowerCase();
            if(text.includes(value)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });

    // Client Side Filter Status dan Remark Kegiatan
    function filterTable(criteria, element) {
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
            btn.classList.add('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
            btn.classList.remove('font-black');
            btn.classList.add('font-bold');
        });

        if (element) {
            element.classList.remove('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
            element.classList.add('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
            element.classList.remove('font-bold');
            element.classList.add('font-black');
        }

        const rows = document.querySelectorAll('#historyTable tbody .table-row-item');
        
        rows.forEach(row => {
            if (criteria === 'all') {
                row.style.display = '';
                return;
            }

            const statusCell = row.querySelector('.status-cell');
            const remarkCell = row.querySelector('.remark-cell');
            
            const statusText = statusCell ? statusCell.textContent.trim().toLowerCase() : '';
            const remarkText = remarkCell ? remarkCell.textContent.trim().toLowerCase() : '';

            if (criteria === 'success' || criteria === 'pending') {
                row.style.display = (statusText === criteria) ? '' : 'none';
            } else if (criteria === 'manual in') {
                row.style.display = (remarkText.includes('manual')) ? '' : 'none';
            } else if (criteria === 'scan in') {
                row.style.display = (remarkText.includes('scan')) ? '' : 'none';
            }
        });
    }

    // Alert Flash Session handler
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false, customClass: { popup: 'font-nunito' } });
    @endif
    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: "{{ session('error') }}", timer: 3000, showConfirmButton: false, customClass: { popup: 'font-nunito' } });
    @endif
</script>

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container { font-family: 'Nunito', sans-serif !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
    
    #historyTable td, #historyTable td span, #historyTable td font {
        color: #000000 !important;
        vertical-align: middle !important;
    }
    #historyTable th { vertical-align: middle !important; }

    /* Theme override pagination style */
    nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
    nav[role="navigation"] div:first-child { display: none; }
</style>
@endsection