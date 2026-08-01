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

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">SYSTEM RECORD:</span> 
            Total {{ $receivings->total() }} engineering material received records logged for approval routing.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">List Eng Material Received</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS INDONESIA — ENGINEERING SECTION</p>
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
                    <form action="{{ route('eng.material.receiving.index') }}" method="GET" class="w-full">
                        @if(request('per_page'))
                            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
                        @endif
                        <input type="text" name="search" value="{{ request('search') }}" id="tableSearch" placeholder="Search MR, PR, NIK..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-slate-950 dark:text-white font-bold font-nunito">
                    </form>
                </div>

                {{-- TOMBOL EXPORT CSV --}}
                <button type="button" onclick="exportTableToCSV('eng-material-received.csv')" class="col-span-4 flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 sm:px-3.5 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
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
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[2300px]" id="approvalTable">
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
                        
                        <th class="px-2 py-3.5 w-[120px] border-l border-orange-500 bg-orange-700/30">QTY PR</th>
                        <th class="px-2 py-3.5 w-[120px] border-l border-orange-500 bg-orange-700/30">QTY RECEIVED</th>
                        <th class="px-2 py-3.5 w-[160px] border-l border-orange-500 bg-orange-700/30">QTY STATUS</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-755/30">Status Flow</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-700/30 text-center">Created At</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 bg-orange-700/30 text-center">Updated At</th>
                        <th class="px-3 py-3.5 w-[210px] border-l border-orange-500 bg-orange-700/30 text-center">Action Decision</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($receivings as $index => $item)
                    @php
                        $statusText = 'unknown';
                        $badgeClass = 'bg-slate-100 text-slate-950 border-slate-300';
                        $rawStatus = strtolower($item->status ?? 'unknown');
                        
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

                        // Qty Status Logic
                        $qtyStatusRaw = strtoupper($item->qty_status ?? 'OPEN');
                        $qtyStatusClass = 'bg-amber-50 text-amber-950 border-amber-300';
                        if(!str_contains(strtolower($qtyStatusRaw), 'open')) {
                            $qtyStatusClass = 'bg-emerald-50 text-emerald-950 border-emerald-300';
                        }
                    @endphp
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-orange-600 focus:ring-orange-500 cursor-pointer">
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            {{ $receivings->firstItem() + $index }}
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-left text-indigo-600 dark:text-indigo-400">
                            {{ $item->no_mr ?? 'MR-SYSTEM-GEN' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap text-left text-slate-700 dark:text-slate-300 font-mono">
                            {{ $item->purchaseRequest->no_pr ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap">
                            {{ optional($item->user)->nik ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-left">
                            {{ optional($item->user)->name ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-nowrap text-slate-900 dark:text-white">
                            {{ optional($item->purchaseRequest->sparepart)->sparepart_id ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-left border-l border-gray-100 dark:border-slate-800 font-mono tracking-wide whitespace-nowrap text-slate-800 dark:text-slate-200 uppercase">
                            {{ optional($item->purchaseRequest->sparepart)->part_number ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 tracking-wide font-mono text-indigo-600 dark:text-indigo-400">
                            {{ optional($item->purchaseRequest->sparepart)->sap_code ?? '-' }}
                        </td>

                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 tracking-wide uppercase text-[11px]">
                            {{ optional($item->purchaseRequest->sparepart)->category ?? '-' }}
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-slate-900 dark:text-slate-100 font-extrabold">
                            {{ number_format(optional($item->purchaseRequest)->qty_pr ?? 0) }} Pcs
                        </td>

                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 whitespace-nowrap text-orange-600 font-black">
                            {{ number_format($item->qty_received) }} Pcs
                        </td>

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
                            <div class="font-bold">{{ $item->created_at ? $item->created_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $item->created_at ? $item->created_at->format('H:i') : '-' }} WIB</div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold text-slate-800 dark:text-slate-200">{{ $item->updated_at ? $item->updated_at->format('d/m/Y') : '-' }}</div>
                            <div class="text-[10px] mt-0.5 text-slate-500">{{ $item->updated_at ? $item->updated_at->format('H:i') : '-' }} WIB</div>
                        </td>
                        
                        {{-- DYNAMIC ACTION BUTTONS BERDASARKAN STATUS TRANS-FLOW --}}
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center gap-2.5">
                                {{-- JIKA STATUS PENDING: MUNCUL REJECT & CHECKED (STAFF LAB) --}}
                                @if(strtolower($item->status) == 'pending')
                                    <form id="reject-form-{{ $item->id }}" action="{{ route('costing.material.delete', $item->id) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" onclick="confirmRejectMaterial('{{ $item->id }}', '{{ $item->purchaseRequest->no_pr ?? $item->no_mr }}')" 
                                            class="inline-flex items-center justify-center w-[85px] py-1.5 bg-gradient-to-r from-[#f74242] to-[#ff9e15] text-white font-black rounded-md text-[11px] tracking-wider uppercase shadow hover:brightness-105 transition-all active:scale-95 cursor-pointer">
                                        Reject
                                    </button>

                                    <a href="{{ route('eng.material.receiving.create', $item->id) }}" 
                                       class="inline-flex items-center justify-center w-[85px] py-1.5 bg-gradient-to-r from-[#00ba90] to-[#2b83f6] text-white font-black rounded-md text-[11px] tracking-wider uppercase shadow hover:brightness-105 transition-all active:scale-95 cursor-pointer">
                                        Checked
                                    </a>

                                {{-- FIX DI SINI: JIKA STATUS CHECKED: MUNCUL REJECT & APPROVE (SUPERVISOR DESK) --}}
                                @elseif(strtolower($item->status) == 'checked')
                                    <form id="reject-form-{{ $item->id }}" action="{{ route('costing.material.delete', $item->id) }}" method="POST" class="hidden">
                                        @csrf @method('DELETE')
                                    </form>
                                    <button type="button" onclick="confirmRejectMaterial('{{ $item->id }}', '{{ $item->purchaseRequest->no_pr ?? $item->no_mr }}')" 
                                            class="inline-flex items-center justify-center w-[85px] py-1.5 bg-gradient-to-r from-[#f74242] to-[#ff9e15] text-white font-black rounded-md text-[11px] tracking-wider uppercase shadow hover:brightness-105 transition-all active:scale-95 cursor-pointer">
                                        Reject
                                    </button>

                                    {{-- GARIS INI DIUBAH MENEMBAK ROUTE APPROVAL SUPERVISOR (.approve) --}}
                                    <a href="{{ route('eng.material.receiving.approve', $item->id) }}" 
                                       class="inline-flex items-center justify-center w-[85px] py-1.5 bg-gradient-to-r from-[#6366f1] to-[#a855f7] text-white font-black rounded-md text-[11px] tracking-wider uppercase shadow hover:brightness-105 transition-all active:scale-95 cursor-pointer">
                                        Approve
                                    </a>
                                @else
                                    {{-- JIKA STATUS SUDAH APPROVED / REJECTED --}}
                                    <span class="text-[10px] font-black uppercase tracking-wider text-slate-400 bg-slate-50 dark:bg-slate-800 px-4 py-1.5 rounded-md border border-gray-200 dark:border-slate-700 processed-text shadow-sm">Processed</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
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
                Showing {{ $receivings->firstItem() ?? 0 }} to {{ $receivings->lastItem() ?? 0 }} of {{ $receivings->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination text-black">
                {{ $receivings->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
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
        let rows = document.querySelectorAll("#approvalTable tr");
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

    function confirmRejectMaterial(id, prCode) {
        Swal.fire({
            title: 'Reject Data Received?',
            text: "Data berkas dengan referensi PR " + prCode + " akan ditolak!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f74242',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Reject!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('reject-form-' + id).submit();
            }
        });
    }
</script>

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container, #approvalTable { 
        font-family: 'Nunito', sans-serif !important; 
    }

    .table-body-data tr td, 
    .table-body-data tr td div,
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
    
    #approvalTable td, #approvalTable th {
        vertical-align: middle !important;
    }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection