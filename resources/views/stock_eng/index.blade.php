@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">
    
    @php
        $outOfStock = $stocks->where('qty', '<=', 0)->count();
        $lowStock = $stocks->filter(function($item) {
            return $item->qty > 0 && $item->qty <= $item->min_stock;
        })->count();
    @endphp
    
    @if($outOfStock > 0 || $lowStock > 0)
    <div class="mb-6 flex items-center gap-3 rounded-xl border border-amber-100 bg-amber-50 dark:bg-amber-900/20 dark:border-amber-900/30 px-4 py-3 text-sm text-amber-800 dark:text-amber-200">
        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-amber-400 text-white font-bold">!</span>
        <p>
            <span class="font-bold text-rose-600 dark:text-rose-400">{{ $outOfStock }} Out of Stock</span> · 
            <span class="font-bold text-amber-600 dark:text-amber-400">{{ $lowStock }} Low Stock</span> — Immediate reorder recommended
        </p>
    </div>
    @endif
<!-- HEADER AREA: JUDUL & BUTTON SEJAJAR -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Nozzle Inventory</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400">Inventory Monitoring System</p>
    </div>

    <div class="flex flex-wrap items-center gap-2">
        <!-- BUTTON EXPORT CSV (Warna Hijau & Icon File CSV) -->
        <a href="{{ route('stock.eng.export') }}" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
            </svg>
            CSV EXPORT
        </a>
        
        <!-- BUTTON ADD RAK (Warna Orange & Icon Rak/Box) -->
        <button onclick="openRackModal()" class="flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            ADD RAK
        </button>
        
        <!-- BUTTON ADD NOZZLE (Warna Biru & Icon Nozzle/Machine Part) -->
        <button onclick="openModal('add')" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all active:scale-95">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
            </svg>
            ADD NOZZLE
        </button>
    </div>
</div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <!-- SEARCH BAR -->
            <div class="relative mb-6 w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search data..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 dark:border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500">
            </div>

            <!-- TABS RAK -->
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide border-b border-slate-100 dark:border-slate-700 pb-1" id="rackTabs">
                <button onclick="filterRak('all')" class="tab-btn active px-4 py-2 rounded-t-lg text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm">
                    All Storage
                </button>
                @foreach($raks as $rak)
                    <button onclick="filterRak('{{ $rak->nama_rak }}')" class="tab-btn px-4 py-2 rounded-t-lg text-xs font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 whitespace-nowrap">
                        {{ $rak->nama_rak }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- TABLE AREA -->
        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="nozzleTable">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-4 text-center">NO</th>
                        <th class="px-4 py-4">No Rak</th>
                        <th class="px-4 py-4">Status</th>
                        <th class="px-4 py-4">No Nozzle</th>
                        <th class="px-4 py-4">Part No</th>
                        <th class="px-4 py-4">Sap Code</th>
                        <th class="px-4 py-4">Category</th>
                        <th class="px-4 py-4 text-center">Qty</th>
                        <th class="px-4 py-4 text-center">Min Stock</th>
                        <th class="px-4 py-4">Create At</th>
                        <th class="px-4 py-4">Update At</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($stocks as $index => $item)
                    <tr class="row-nozzle hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all" data-rak="{{ $item->rak->nama_rak ?? '' }}">
                        <td class="px-4 py-4 text-center">{{ $stocks->firstItem() + $index }}</td>
                        <td class="px-4 py-4 font-bold">{{ $item->rak->nama_rak ?? '-' }}</td>
                        <td class="px-4 py-4">
                            @php
                                $statusColor = 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]'; 
                                if($item->qty <= 0) {
                                    $statusColor = 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]';
                                } elseif($item->qty <= $item->min_stock) {
                                    $statusColor = 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)]';
                                }
                            @endphp
                            <div class="flex items-center gap-2">
                                <div class="h-2.5 w-2.5 rounded-full {{ $statusColor }}"></div>
                            </div>
                        </td>
                        <td class="px-4 py-4 font-bold">{{ $item->no_nozzle ?? '-' }}</td>
                        <td class="px-4 py-4 font-bold">{{ $item->part_no ?? '-' }}</td>
                        <td class="px-4 py-4 font-bold">{{ $item->sap_code ?? '-' }}</td>
                        <td class="px-4 py-4 font-bold">{{ $item->category ?? '-' }}</td>
                        <td class="px-4 py-4 text-center font-bold">{{ $item->qty }}</td>
                        <td class="px-4 py-4 text-center font-bold">{{ $item->min_stock }}</td>
                        <td class="px-4 py-4 font-bold text-[10px]">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-4 font-bold text-[10px]">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                        
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Action Edit (Kuning Normal -> Hover Agak Gelap) -->
                                <button onclick="openModal('edit', {{ json_encode($item) }})" 
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm" 
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                        <path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                        
                                <!-- Action Delete (Merah Normal -> Hover Agak Gelap) -->
                                <form action="{{ route('stock.eng.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete item?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" 
                                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white transition-all hover:bg-red-700 active:scale-90 shadow-sm" 
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                            <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                       
                    </tr>
                    @empty
                    <tr><td colspan="12" class="py-12 text-center text-slate-400 italic">Data not found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-6 py-4">
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} Entries
            </p>
            <div class="flex items-center gap-2">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL NOZZLE --}}
<div id="modalNozzle" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
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
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Pilih Rak</label>
                    <select name="rak_id" id="rak_id" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                        <option value="">-- Pilih Rak --</option>
                        @foreach($raks as $rak)
                            <option value="{{ $rak->id }}">{{ $rak->nama_rak }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">No Nozzle</label>
                    <input type="text" name="no_nozzle" id="no_nozzle" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Part No</label>
                    <input type="text" name="part_no" id="part_no" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Sap Code</label>
                    <input type="text" name="sap_code" id="sap_code" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Category</label>
                    <input type="text" name="category" id="category" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white">
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Qty</label>
                    <input type="number" name="qty" id="qty" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Min Stock</label>
                    <input type="number" name="min_stock" id="min_stock" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all">Save Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL RACK --}}
<div id="modalRack" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-sm shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Add New Rack</h3>
            <button onclick="closeRackModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('rak.store') }}" method="POST" class="p-6">
            @csrf
            <div class="mb-4">
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Nama Rak</label>
                <input type="text" name="nama_rak" placeholder="Contoh: RAK-A1" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeRackModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all">Add Rack</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterRak(rakName) {
        let btns = document.querySelectorAll(".tab-btn");
        btns.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-100', 'dark:border-slate-700');
        });

        const activeBtn = event.currentTarget;
        activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
        activeBtn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-100', 'dark:border-slate-700');

        document.querySelectorAll(".row-nozzle").forEach(row => {
            row.style.display = (rakName === 'all' || row.getAttribute('data-rak') === rakName) ? "" : "none";
        });
    }

    function searchTable() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        document.querySelectorAll(".row-nozzle").forEach(row => {
            row.style.display = row.innerText.toUpperCase().includes(input) ? "" : "none";
        });
    }

    function openModal(mode, data = null) {
        const modal = document.getElementById('modalNozzle');
        const form = document.getElementById('nozzleForm');
        const methodField = document.getElementById('methodField');
        
        modal.classList.remove('hidden');
        
        if (mode === 'edit') {
            document.getElementById('modalTitle').innerText = 'Edit Nozzle Data';
            form.action = "/eng/stock-engineering/" + data.id;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            
            document.getElementById('rak_id').value = data.rak_id;
            document.getElementById('no_nozzle').value = data.no_nozzle;
            document.getElementById('part_no').value = data.part_no || '';
            document.getElementById('sap_code').value = data.sap_code || '';
            document.getElementById('category').value = data.category || '';
            document.getElementById('qty').value = data.qty;
            document.getElementById('min_stock').value = data.min_stock;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Nozzle';
            form.action = "{{ route('stock.eng.store') }}";
            form.reset();
            methodField.innerHTML = '';
        }
    }

    function closeModal() { document.getElementById('modalNozzle').classList.add('hidden'); }
    function openRackModal() { document.getElementById('modalRack').classList.remove('hidden'); }
    function closeRackModal() { document.getElementById('modalRack').classList.add('hidden'); }
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .flex.items-center.gap-2 svg { width: 20px; height: 20px; }
</style>
@endsection