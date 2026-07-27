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

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300 text-black">

    @php
    // Patokan data dari database untuk sistem alert status banner
    $outOfStock = $stocks->where('qty', '<=', 0)->count();
    $lowStock = $stocks->filter(function($item) {
        return $item->qty > 0 && $item->qty <= $item->min_stock;
    })->count();

    // Menentukan Tema Warna Berdasarkan Status Terparah
    if ($outOfStock > 0) {
        $theme = [
            'bg' => 'bg-red-50 dark:bg-red-950/20', 
            'border' => 'border-red-200 dark:border-red-900/50', 
            'dot' => 'bg-red-600', 
            'text' => 'text-red-800 dark:text-red-300',
            'status' => 'LOST',
            'msg' => $outOfStock . ' item out of stock — immediate reorder recommended'
        ];
    } elseif ($lowStock > 0) {
        $theme = [
            'bg' => 'bg-[#FFFBEB] dark:bg-amber-950/10', 
            'border' => 'border-amber-200 dark:border-amber-900/30', 
            'dot' => 'bg-[#F59E0B]', 
            'text' => 'text-[#92400E] dark:text-amber-300',
            'status' => 'WARNING',
            'msg' => $lowStock . ' low stock — prepare for reorder'
        ];
    } else {
        $theme = [
            'bg' => 'bg-emerald-50 dark:bg-emerald-950/10', 
            'border' => 'border-emerald-200 dark:border-emerald-900/30', 
            'dot' => 'bg-emerald-500', 
            'text' => 'text-emerald-800 dark:text-emerald-300',
            'status' => 'SAFE',
            'msg' => 'All systems stable — stock levels are safe'
        ];
    }
    @endphp

    {{-- Banner Status Real-time --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border {{ $theme['border'] }} {{ $theme['bg'] }} px-3 py-2.5 md:px-4 md:py-3 shadow-sm transition-all">
        <span class="h-2 w-2 shrink-0 rounded-full {{ $theme['dot'] }} animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold {{ $theme['text'] }} font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">{{ $theme['status'] }}:</span> 
            {{ $theme['msg'] }}
        </p>
    </div>

    {{-- Header & Action Buttons --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight">Nozzle Inventory</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Inventory Monitoring System</p>
        </div>

        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            {{-- Button CSV Export - HIJAU GRADIENT --}}
            <a href="{{ route('stock.eng.export') }}" class="flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 tracking-wide text-center uppercase no-underline">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                Export CSV
            </a>
            
            {{-- Button Add Rak - ORANGE GRADIENT --}}
            <button onclick="openRackModal()" class="flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 tracking-wide uppercase">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                Add Rak
            </button>
            
            {{-- Button Add Nozzle - BIRU GRADIENT --}}
            <button onclick="openModal('add')" class="flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 tracking-wide uppercase">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                Add Nozzle
            </button>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL (Sesuai Master Data) --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        {{-- HEADER KONTROL & FILTER TAB --}}
        <div class="px-4 border-b border-slate-100 dark:border-slate-800 font-nunito">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                {{-- LIVE SEARCH INPUT --}}
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="applyFilterAndSearch()" placeholder="Search data..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] outline-none focus:border-blue-500 text-black dark:text-white font-bold font-nunito">
                </div>
            </div>

            {{-- TAB RAK NAVIGASI DENGAN ABU-ABU LEBIH JELAS/KONTRAS --}}
            <div class="flex items-center gap-1.5 overflow-x-auto scrollbar-thin pb-1" id="rackTabs">
                <button onclick="filterRak(this, 'all')" class="tab-btn active px-4 py-2 rounded-t-lg text-xs font-black transition-all bg-blue-600 text-white shadow-sm whitespace-nowrap uppercase tracking-wider">
                    All Storage
                </button>
                @foreach($raks as $rak)
                    <button onclick="filterRak(this, '{{ $rak->nama_rak }}')" class="tab-btn px-4 py-2 rounded-t-lg text-xs font-black text-slate-600 dark:text-slate-300 bg-slate-200/80 dark:bg-slate-800 hover:bg-slate-300 border-t border-x border-slate-300 dark:border-slate-700 whitespace-nowrap uppercase tracking-wider transition-all">
                        {{ $rak->nama_rak }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- AREA SCROLL HORIZONTAL & DESAIN MATRIKS TABEL SAMAKAN TEMA --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1200px]" id="nozzleTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito">
                        <th class="px-2 py-3.5 w-[60px] text-center">NO</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-blue-500 dark:border-blue-900/50">No Rak</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 dark:border-blue-900/50">Status</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 dark:border-blue-900/50 text-center w-[200px]">No Nozzle (ID)</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Part No</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">Sap Code</th>
                        <th class="px-2 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">Category</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500 dark:border-blue-900/50">Qty</th>
                        <th class="px-2 py-3.5 w-[95px] border-l border-blue-500 dark:border-blue-900/50">Min Stock</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Create At</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:border-blue-900/50">Update At</th>
                        <th class="px-4 py-3.5 w-[120px] border-l border-blue-500 dark:border-blue-900/50">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent">
                    @forelse($stocks as $index => $item)
                    <tr class="row-nozzle hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent" data-rak="{{ $item->rak->nama_rak ?? '' }}">
                        <td class="px-2 py-3.5 text-center text-slate-500">
                            {{ $stocks->firstItem() + $index }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap">
                            {{ $item->rak->nama_rak ?? '-' }}
                        </td>
                        
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            @php
                                $statusColor = 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]'; 
                                if($item->qty <= 0) {
                                    $statusColor = 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]';
                                } elseif($item->qty <= $item->min_stock) {
                                    $statusColor = 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)]';
                                }
                            @endphp
                            <div class="flex items-center justify-center">
                                <div class="h-2.5 w-2.5 rounded-full {{ $statusColor }}"></div>
                            </div>
                        </td>
                        
                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold tracking-wide whitespace-normal break-words leading-normal">
                            {{ $item->sparepart->sparepart_id ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono tracking-wide whitespace-nowrap">
                            {{ $item->sparepart->part_number ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-blue-600 dark:text-blue-400 font-extrabold whitespace-nowrap">
                            {{ $item->sparepart->sap_code ?? '-' }}
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex justify-center items-center">
                                <span class="bg-slate-100 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700 px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider">
                                    {{ $item->category ?? ($item->sparepart->category ?? '-') }}
                                </span>
                            </div>
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold text-[14px] whitespace-nowrap">{{ $item->qty }}</td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-slate-500 whitespace-nowrap">{{ $item->min_stock }}</td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-slate-600 dark:text-slate-400">
                            {{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-1.5 w-full">
                                <button type="button" 
                                    data-item="{{ json_encode($item) }}"
                                    onclick="openModal('edit', this)" 
                                    class="flex h-7 w-7 shrink-0 items-center justify-center rounded bg-yellow-400 text-white hover:bg-yellow-500 active:scale-90 shadow-sm transition-all" 
                                    title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                <form action="{{ route('stock.eng.destroy', $item->id) }}" method="POST" class="inline form-delete shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete hover:bg-red-600 active:scale-90 transition-all" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="py-10 text-center text-slate-400 italic font-medium text-[13px] font-nunito">No entries found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION RESPONSIF --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase text-center sm:text-left">
                Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs text-black dark:text-white w-full sm:w-auto">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL NOZZLE (EDIT & ADD MODE) --}}
<div id="modalNozzle" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800 text-black transform scale-100 transition-all">
        {{-- HEADER MODAL: UBAH JADI BIRU (bg-blue-600) DAN TEKS PUTIH --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-blue-600 text-white">
            <h3 id="modalTitle" class="text-lg font-extrabold tracking-tight text-white">Add Nozzle</h3>
            <button onclick="closeModal()" class="text-white/80 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="nozzleForm" method="POST" class="p-6 space-y-4">
            @csrf
            <div id="methodField"></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Pilih Rak</label>
                    <select name="rak_id" id="rak_id" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-semibold" required>
                        <option value="">-- Pilih Rak --</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}">{{ $rak->nama_rak }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Part No (Pilih Disini)</label>
                    <select name="sparepart_id" id="sparepart_id" onchange="autoFillByPart(this)" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-mono font-bold" required>
                        <option value="">-- Pilih Part Number --</option>
                        @foreach($ListSparepartEng as $sp)
                            <option value="{{ $sp->sparepart_id }}" 
                                    data-name="{{ $sp->sparepart_id ?? '' }}" 
                                    data-sap="{{ $sp->sap_code ?? '' }}"
                                    data-category="{{ $sp->category ?? '' }}">
                                {{ $sp->part_number ?? 'No Part Num' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">No Nozzle</label>
                    <input type="text" id="no_nozzle" class="w-full rounded-lg border border-gray-300 bg-slate-50 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none text-black dark:text-white font-bold" placeholder="Terisi Otomatis..." readonly>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Sap Code</label>
                    <input type="text" id="sap_code" class="w-full rounded-lg border border-gray-300 bg-slate-50 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none text-black dark:text-white font-mono font-bold" placeholder="Terisi Otomatis..." readonly>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Category</label>
                    <input type="text" name="category" id="category" placeholder="Terisi Otomatis..." class="w-full rounded-lg border border-gray-300 bg-slate-50 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none text-black dark:text-white font-bold" readonly>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Qty</label>
                    <input type="number" name="qty" id="qty" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Min Stock</label>
                    <input type="number" name="min_stock" id="min_stock" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                </div>
            </div>
            <div class="mt-8 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500 hover:text-slate-700 dark:hover:text-slate-300">Cancel</button>
                {{-- Button Submit Data - BIRU GRADIENT --}}
                <button type="submit" class="bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-lg hover:opacity-90 transition-all active:scale-95 tracking-wide">Save Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RACK MANAGE (ADD & DELETE SATU INTEGRASI) --}}
<div id="modalRack" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden text-black transform scale-100 transition-all">
        {{-- HEADER MODAL RACK: UBAH JADI BIRU (bg-blue-600) DAN TEKS PUTIH --}}
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-blue-600 text-white">
            <h3 class="text-lg font-bold tracking-tight text-white">Rack Management</h3>
            <button onclick="closeRackModal()" class="text-white/80 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        
        {{-- BAGIAN 1: FORM TAMBAH RAK --}}
        <form action="{{ route('rak.store') }}" method="POST" class="p-6 border-b border-slate-100 dark:border-slate-800">
            @csrf
            <div class="mb-2">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase tracking-wide">Nama Rak Baru</label>
                <div class="flex gap-2">
                    <input type="text" name="nama_rak" placeholder="Contoh: RAK-A1" class="flex-1 rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                    {{-- Button Add Rak Inside Form - ORANGE GRADIENT --}}
                    <button type="submit" class="bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 text-white px-5 py-2.5 rounded-lg text-xs font-bold shadow-md hover:opacity-90 transition-all tracking-wide whitespace-nowrap uppercase">
                        + Add
                    </button>
                </div>
            </div>
        </form>

        {{-- BAGIAN 2: LIST DAN HAPUS RAK --}}
        <div class="p-6">
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 block uppercase tracking-wide">Existing Racks (Delete List)</label>
            <div class="max-h-48 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 pr-1 scrollbar-thin">
                @forelse($raks as $rak)
                    <div class="flex items-center justify-between py-2.5">
                        <span class="text-sm font-bold text-black dark:text-white uppercase">{{ $rak->nama_rak }}</span>
                        <form action="{{ route('rak.destroy', $rak->id) }}" method="POST" class="form-delete-rak">
                            @csrf @method('DELETE')
                            <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete-rak hover:bg-red-600 transition-all active:scale-90 shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                    <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 italic text-center py-4">No rack available.</p>
                @endforelse
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 px-6 py-3 flex justify-end border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeRackModal()" class="px-4 py-2 text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200 rounded-lg">Close</button>
        </div>
    </div>
</div>

<script>
    // Global State Filter Penataan Rak
    let currentRackFilter = 'all';

    function filterRak(element, rakName) {
        currentRackFilter = rakName;
        
        let btns = document.querySelectorAll(".tab-btn");
        btns.forEach(btn => {
            // Reset ke tampilan abu-abu kontras default saat tidak aktif
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-slate-200/80', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'border-slate-300', 'dark:border-slate-700');
        });

        // Set tombol aktif menjadi Biru Terang
        element.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
        element.classList.remove('bg-slate-200/80', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300', 'border-slate-300', 'dark:border-slate-700');

        applyFilterAndSearch();
    }

    // Kombinasi Search Bar Filter & Rak Active Tabs
    function applyFilterAndSearch() {
        let searchInput = document.getElementById("searchInput").value.toUpperCase();
        
        document.querySelectorAll(".row-nozzle").forEach(row => {
            let rowRak = row.getAttribute('data-rak');
            let textMatches = row.innerText.toUpperCase().includes(searchInput);
            let rackMatches = (currentRackFilter === 'all' || rowRak === currentRackFilter);
            
            if (textMatches && rackMatches) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    }

    // Autofill Data Nozzle Berdasarkan Dropdown Part Number
    function autoFillByPart(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        const nozzleName = selectedOption.getAttribute('data-name') || '';
        const sapCode = selectedOption.getAttribute('data-sap') || '';
        const category = selectedOption.getAttribute('data-category') || '';
        
        document.getElementById('no_nozzle').value = nozzleName;
        document.getElementById('sap_code').value = sapCode;
        document.getElementById('category').value = category;
    }

    // Pengelolaan Pembukaan Modal Nozzle Edit & Add Mode
    function openModal(mode, element = null) {
        const modal = document.getElementById('modalNozzle');
        const form = document.getElementById('nozzleForm');
        const methodField = document.getElementById('methodField');
        
        modal.classList.remove('hidden');
        
        if (mode === 'edit' && element) {
            const data = JSON.parse(element.getAttribute('data-item'));
            
            document.getElementById('modalTitle').innerText = 'Edit Nozzle Data';
            form.action = "/stock-engineering/" + data.id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('rak_id').value = data.rak_id;
            document.getElementById('sparepart_id').value = data.sparepart_id;
            
            const selectEl = document.getElementById('sparepart_id');
            if (selectEl) autoFillByPart(selectEl);

            if(data.category) {
                document.getElementById('category').value = data.category;
            }

            // Set value input qty dan min_stock secara akurat dari data baris table
            document.getElementById('qty').value = data.qty;
            document.getElementById('min_stock').value = data.min_stock;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Nozzle';
            form.action = "{{ route('stock.eng.store') }}";
            form.reset();
            methodField.innerHTML = '';
            
            document.getElementById('no_nozzle').value = '';
            document.getElementById('sap_code').value = '';
            document.getElementById('category').value = '';
        }
    }

    // Konfirmasi Kontekstual Hapus Nozzle Baris Tabel
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete');
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this nozzle entry!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Yes, delete it!',
                customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Konfirmasi Hapus Rak Tunggal Inside Management List
    document.querySelectorAll('.btn-delete-rak').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete-rak');
            Swal.fire({
                title: 'Hapus Rak ini?',
                text: "Nozzle di dalam rak ini mungkin akan kehilangan relasi penempatan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Intersepsi Submit Form Nozzle Utama Dengan SweetAlert Confirmation
    document.getElementById('nozzleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let form = this;
        let method = document.getElementById('methodField').innerHTML;
        let isEdit = method.includes('PUT');

        Swal.fire({
            title: isEdit ? 'Yakin simpan perubahan?' : 'Yakin tambah data?',
            text: "Pastikan data volume stok penempatan sudah tepat",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: isEdit ? '#f59e0b' : '#2563eb',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses!',
            customClass: { popup: 'font-nunito bg-white dark:bg-slate-900 max-w-[90%] sm:max-w-md' }
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Flash Messaging Dialogues
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            timer: 3000,
            showConfirmButton: false,
            customClass: { popup: 'font-nunito' }
        });
    @endif
    
    // Modal Utility triggers
    function closeModal() { document.getElementById('modalNozzle').classList.add('hidden'); }
    function openRackModal() { document.getElementById('modalRack').classList.remove('hidden'); }
    function closeRackModal() { document.getElementById('modalRack').classList.add('hidden'); }
</script>

<style>
    /* Sinkronisasi font dan layout pembatas core */
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container { font-family: 'Nunito', sans-serif !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
    
    /* Memaksa isi data tabel berwarna hitam pekat dan sejajar vertikal di tengah */
    #nozzleTable td, #nozzleTable td span, #nozzleTable td font {
        color: #000000 !important;
        vertical-align: middle !important;
    }
    #nozzleTable th { vertical-align: middle !important; }
</style>
@endsection