@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="font-nunito mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

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
    <div class="mb-6 flex items-center gap-3 rounded-2xl border {{ $theme['border'] }} {{ $theme['bg'] }} px-5 py-3 shadow-sm transition-all">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $theme['dot'] }} animate-pulse"></span>
        <p class="text-sm font-semibold {{ $theme['text'] }}">
            <span class="uppercase font-extrabold mr-1">{{ $theme['status'] }}:</span> 
            {{ $theme['msg'] }}
        </p>
    </div>

    {{-- Header & Action Buttons --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight">Nozzle Inventory</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Inventory Monitoring System</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('stock.eng.export') }}" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                CSV EXPORT
            </a>
            
            <button onclick="openRackModal()" class="flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                ADD RAK
            </button>

            <button onclick="openDeleteRackModal()" class="flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-red-700 dark:bg-red-500 dark:hover:bg-red-600 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                DELETE RAK
            </button>
            
            <button onclick="openModal('add')" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
                </svg>
                ADD NOZZLE
            </button>
        </div>
    </div>

    {{-- Main Container Card --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        {{-- Search & Tab Navigation Section --}}
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative mb-6 w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" id="searchInput" onkeyup="applyFilterAndSearch()" placeholder="Search data..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 dark:border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500 font-medium">
            </div>

            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide border-b border-slate-100 dark:border-slate-700 pb-1" id="rackTabs">
                <button onclick="filterRak(this, 'all')" class="tab-btn active px-4 py-2 rounded-t-lg text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm whitespace-nowrap">
                    All Storage
                </button>
                @foreach($raks as $rak)
                    <button onclick="filterRak(this, '{{ $rak->nama_rak }}')" class="tab-btn px-4 py-2 rounded-t-lg text-xs font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 whitespace-nowrap">
                        {{ $rak->nama_rak }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Table Content --}}
        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="nozzleTable">
                <thead>
                    <tr class="text-[10px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-4 text-center w-12">NO</th>
                        <th class="px-4 py-4 text-center">No Rak</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4 text-center">No Nozzle</th>
                        <th class="px-4 py-4 text-center">Part No</th>
                        <th class="px-4 py-4 text-center">Sap Code</th>
                        <th class="px-4 py-4 text-center">Category</th>
                        <th class="px-4 py-4 text-center w-16">Qty</th>
                        <th class="px-4 py-4 text-center w-24">Min Stock</th>
                        <th class="px-4 py-4 text-center w-28">Create At</th>
                        <th class="px-4 py-4 text-center w-28">Update At</th>
                        <th class="px-6 py-4 text-center w-28">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-semibold text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($stocks as $index => $item)
                    <tr class="row-nozzle hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all" data-rak="{{ $item->rak->nama_rak ?? '' }}">
                        <td class="px-4 py-4 text-center text-slate-500 font-bold">{{ $stocks->firstItem() + $index }}</td>
                        <td class="px-4 py-4 text-center font-bold text-slate-700 dark:text-slate-300">{{ $item->rak->nama_rak ?? '-' }}</td>
                        <td class="px-4 py-4 text-center">
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
                        <td class="px-4 py-4 text-center font-bold text-slate-800 dark:text-white">{{ $item->sparepart->name ?? '-' }}</td>
                        <td class="px-4 py-4 text-center font-bold font-mono tracking-wide text-slate-600 dark:text-slate-400">{{ $item->sparepart->part_number ?? '-' }}</td>
                        <td class="px-4 py-4 text-center font-bold font-mono tracking-wide text-indigo-600 dark:text-indigo-400">{{ $item->sparepart->sap_code ?? '-' }}</td>
                        
                        {{-- Menampilkan category dari database dengan fallback jika data lama kosong --}}
                        <td class="px-4 py-4 text-center">
                            <span class="bg-slate-100 dark:bg-slate-800 px-2.5 py-1 rounded-md font-extrabold text-[10px] uppercase">
                                {{ $item->category ?? ($item->sparepart->category ?? '-') }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-center font-extrabold text-slate-900 dark:text-white">{{ $item->qty }}</td>
                        <td class="px-4 py-4 text-center font-extrabold text-slate-500 dark:text-slate-400">{{ $item->min_stock }}</td>
                        
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                            {{ $item->created_at->format('d/m/y') }}
                            <br><span class="text-[9px] text-slate-400 font-medium">{{ $item->created_at->format('H:i') }} WIB</span>
                        </td>
                        <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                            {{ $item->updated_at->format('d/m/y') }}
                            <br><span class="text-[9px] text-slate-400 font-medium">{{ $item->updated_at->format('H:i') }} WIB</span>
                        </td>
                        
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" 
                                    data-item="{{ json_encode($item) }}"
                                    onclick="openModal('edit', this)" 
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm" 
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                <form action="{{ route('stock.eng.destroy', $item->id) }}" method="POST" class="inline form-delete">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white btn-delete" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="py-12 text-center text-slate-400 italic font-semibold">Data not found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination footer --}}
        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-6 py-4">
            <p class="text-[10px] font-extrabold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} Entries
            </p>
            <div class="flex items-center gap-2">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL NOZZLE (WITH AUTOFILL VIA PART NUMBER DROPDOWN) --}}
<div id="modalNozzle" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">Add Nozzle</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="nozzleForm" method="POST" class="p-6">
            @csrf
            <div id="methodField"></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Pilih Rak</label>
                    <select name="rak_id" id="rak_id" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-semibold" required>
                        <option value="">-- Pilih Rak --</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}">{{ $rak->nama_rak }}</option>
                        @endforeach
                    </select>
                </div>
                
                {{-- DROP-DOWN UTAMA: Pilihan Part Number --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Part No (Pilih Disini)</label>
                    <select name="sparepart_id" id="sparepart_id" onchange="autoFillByPart(this)" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-mono font-bold" required>
                        <option value="">-- Pilih Part Number --</option>
                        @foreach($ListSparepartEng as $sp)
                            <option value="{{ $sp->id }}" 
                                    data-name="{{ $sp->name ?? '' }}" 
                                    data-sap="{{ $sp->sap_code ?? '' }}"
                                    data-category="{{ $sp->category ?? '' }}">
                                {{ $sp->part_number ?? 'No Part Num' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- INPUT TEXT FIELD: Otomatis terisi & Readonly --}}
                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">No Nozzle</label>
                    <input type="text" id="no_nozzle" class="w-full rounded-lg border border-slate-200 bg-slate-50 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none dark:text-white font-bold" placeholder="Terisi Otomatis..." readonly>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Sap Code</label>
                    <input type="text" id="sap_code" class="w-full rounded-lg border border-slate-200 bg-slate-50 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none dark:text-white font-mono font-bold" placeholder="Terisi Otomatis..." readonly>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Category</label>
                    {{-- 🌟 FIX: Pastikan atribut name="category" terpasang agar ikut terkirim ke backend Laravel saat disave --}}
                    <input type="text" name="category" id="category" placeholder="Terisi Otomatis..." class="w-full rounded-lg border border-slate-200 bg-slate-50 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none dark:text-white font-bold" readonly>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Qty</label>
                    <input type="number" name="qty" id="qty" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Min Stock</label>
                    <input type="number" name="min_stock" id="min_stock" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide">Save Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RACK ADD --}}
<div id="modalRack" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-sm shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Add New Rack</h3>
            <button onclick="closeRackModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('rak.store') }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Nama Rak</label>
                <input type="text" name="nama_rak" placeholder="Contoh: RAK-A1" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRackModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide">Add Rack</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL DELETE RACK --}}
<div id="modalDeleteRack" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Delete Existing Rack</h3>
            <button onclick="closeDeleteRackModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <div class="p-6 max-h-60 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700">
            @forelse($raks as $rak)
                <div class="flex items-center justify-between py-3">
                    <span class="text-sm font-bold text-slate-800 dark:text-white">{{ $rak->nama_rak }}</span>
                    <form action="{{ route('rak.destroy', $rak->id) }}" method="POST" class="form-delete-rak">
                        @csrf @method('DELETE')
                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded-lg bg-red-500 text-white btn-delete-rak hover:bg-red-600 transition-all active:scale-90 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-400 italic text-center py-4">No rack available.</p>
            @endforelse
        </div>
        <div class="bg-slate-50 dark:bg-slate-800 px-6 py-3 flex justify-end">
            <button type="button" onclick="closeDeleteRackModal()" class="px-4 py-2 text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200 rounded-lg">Close</button>
        </div>
    </div>
</div>

<script>
    // State global melacak filter penyimpanan yang aktif
    let currentRackFilter = 'all';

    function filterRak(element, rakName) {
        currentRackFilter = rakName;
        
        let btns = document.querySelectorAll(".tab-btn");
        btns.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-100', 'dark:border-slate-700');
        });

        element.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
        element.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-100', 'dark:border-slate-700');

        applyFilterAndSearch();
    }

    // Fungsi gabungan sinkronisasi Search Bar dan Tab Filter Rak
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

    // LOGIKA AUTOFILL INSTAN: Mengisi data berdasarkan pilihan Part Number
    function autoFillByPart(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        
        const nozzleName = selectedOption.getAttribute('data-name') || '';
        const sapCode = selectedOption.getAttribute('data-sap') || '';
        const category = selectedOption.getAttribute('data-category') || '';
        
        document.getElementById('no_nozzle').value = nozzleName;
        document.getElementById('sap_code').value = sapCode;
        document.getElementById('category').value = category;
    }

    // Penanganan Modal Nozzle dengan proteksi JSON.parse
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
            
            // Trigger pengisian kolom teks otomatis saat mode edit dibuka
            const selectEl = document.getElementById('sparepart_id');
            if (selectEl) autoFillByPart(selectEl);

            if(data.category) {
                document.getElementById('category').value = data.category;
            }

            document.getElementById('qty').value = data.qty;
            document.getElementById('min_stock').value = data.min_stock;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Nozzle';
            form.action = "{{ route('stock.eng.store') }}";
            form.reset();
            methodField.innerHTML = '';
            
            // Bersihkan teks field otomatisasi
            document.getElementById('no_nozzle').value = '';
            document.getElementById('sap_code').value = '';
            document.getElementById('category').value = '';
        }
    }

    // Konfirmasi Hapus Nozzle
    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete');
            Swal.fire({
                title: 'Yakin mau hapus?',
                text: "Data yang dihapus tidak bisa dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { 
                    container: 'z-[10000]', // 🌟 Mengangkat popup di atas modal z-[9999]
                    popup: 'font-nunito' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // Konfirmasi Hapus Rak Tunggal
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
                customClass: { 
                    container: 'z-[10000]', // 🌟 Mengangkat popup di atas modal z-[9999]
                    popup: 'font-nunito' 
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    // FORM SUBMIT HANDLING: Menambahkan lapisan penanganan tumpukan z-index & cegah freeze
    document.getElementById('nozzleForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        let form = this;
        let method = document.getElementById('methodField').innerHTML;
        let isEdit = method.includes('PUT');

        Swal.fire({
            title: isEdit ? 'Yakin simpan perubahan?' : 'Yakin tambah data?',
            text: "Pastikan semua data sudah diisi dengan benar",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Proses!',
            cancelButtonText: 'Cek Lagi',
            customClass: { 
                container: 'z-[10000]', // 🌟 FIX: Memaksa SweetAlert berada paling depan di atas lapisan Tailwind modal modalNozzle (z-[9999])
                popup: 'font-nunito' 
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Sembunyikan modal Nozzle terlebih dahulu agar tumpukan abu-abu Tailwind hilang dari background
                closeModal();

                // Tampilkan loading screen yang bersih
                Swal.fire({
                    title: 'Sedang memproses...',
                    allowOutsideClick: false,
                    didOpen: () => { Swal.showLoading() },
                    customClass: { 
                        container: 'z-[10000]',
                        popup: 'font-nunito' 
                    }
                });

                // Jalankan instruksi submit form HTML biasa ke Controller backend
                form.submit();
            }
        });
    });

    // Notifikasi Response Server via Session (Dipanggil saat halaman selesai reload)
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

    @if($errors->any())
        Swal.fire({
            icon: 'error',
            title: 'Validasi Gagal',
            text: "{{ $errors->first() }}",
            customClass: { popup: 'font-nunito' }
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Terjadi Kesalahan',
            text: "{{ session('error') }}",
            customClass: { popup: 'font-nunito' }
        });
    @endif

    // Fungsi Utility Pembukaan & Penutupan Modal Utama
    function closeModal() { document.getElementById('modalNozzle').classList.add('hidden'); }
    function openRackModal() { document.getElementById('modalRack').classList.remove('hidden'); }
    function closeRackModal() { document.getElementById('modalRack').classList.add('hidden'); }
    function openDeleteRackModal() { document.getElementById('modalDeleteRack').classList.remove('hidden'); }
    function closeDeleteRackModal() { document.getElementById('modalDeleteRack').classList.add('hidden'); }
</script>
@endsection