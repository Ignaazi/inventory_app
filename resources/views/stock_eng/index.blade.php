@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    
    <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                Nozzle Warehouse Mapping
            </h2>
            <p class="text-sm text-slate-500 font-medium">Monitoring stock distribution per rack unit</p>
        </div>

        <button 
            onclick="openModal('add')"
            class="inline-flex items-center justify-center gap-2.5 rounded-xl bg-indigo-600 py-3 px-6 text-center font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Register New Nozzle
        </button>
    </div>

    {{-- SYSTEM MAPPING: PER RAK GROUPING --}}
    @php
        // Kita kelompokkan data berdasarkan id_rak agar tampil per box rak
        $groupedStocks = $stocks->groupBy('id_rak');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
        @forelse($groupedStocks as $rakId => $items)
        <div class="flex flex-col rounded-3xl border-2 border-slate-200 bg-slate-50/50 dark:border-slate-800 dark:bg-slate-900/50 overflow-hidden transition-all hover:border-indigo-400">
            
            {{-- Rack Header --}}
            <div class="bg-white dark:bg-slate-800 p-5 border-b border-slate-200 dark:border-slate-700 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-indigo-600 rounded-lg text-white shadow-md shadow-indigo-200 dark:shadow-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ $rakId }}</h3>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $items->count() }} Nozzle Types</p>
                    </div>
                </div>
                <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-600 dark:bg-emerald-900/30">Active</span>
            </div>

            {{-- Nozzle List Inside Rack --}}
            <div class="p-4 space-y-3 overflow-y-auto max-h-[400px] scrollbar-hide">
                @foreach($items as $item)
                <div class="group relative flex items-center justify-between p-4 rounded-2xl bg-white border border-slate-100 shadow-sm dark:bg-slate-800 dark:border-slate-700 hover:shadow-md transition-all">
                    <div class="flex-1">
                        <h4 class="font-bold text-slate-700 dark:text-slate-200 text-sm mb-1 line-clamp-1">{{ $item->item_name }}</h4>
                        <div class="flex items-center gap-3 text-[10px] font-medium text-slate-400 uppercase">
                            <span>Qty: <b class="text-slate-800 dark:text-white">{{ $item->qty }}</b></span>
                            <span>Min: {{ $item->min_stock }}</span>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        {{-- Mini Status Indicator --}}
                        <div class="h-2 w-2 rounded-full {{ strpos($item->status_class, 'rose') !== false ? 'bg-rose-500 animate-ping' : (strpos($item->status_class, 'amber') !== false ? 'bg-amber-500' : 'bg-emerald-500') }}"></div>
                        
                        {{-- Action Buttons --}}
                        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openModal('edit', {{ json_encode($item) }})" class="p-1 text-slate-400 hover:text-indigo-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                            </button>
                            <form action="{{ route('stock.eng.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Delete this nozzle?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1 text-slate-400 hover:text-rose-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Rack Footer --}}
            <div class="mt-auto p-4 bg-slate-100/50 dark:bg-slate-800/30">
                <button onclick="openModal('add', {id_rak: '{{ $rakId }}'})" class="w-full py-2 rounded-xl border border-dashed border-slate-300 dark:border-slate-600 text-[10px] font-bold text-slate-500 hover:bg-white dark:hover:bg-slate-800 transition-all uppercase tracking-widest">
                    + Add Nozzle to {{ $rakId }}
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-slate-400">
            <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="font-medium">Warehouse is empty. Start by registering a nozzle.</p>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL AREA --}}
<div id="modalRak" class="fixed inset-0 z-9999 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl dark:bg-slate-900 border border-white/20">
        <div class="flex items-center justify-between mb-8">
            <h3 id="modalTitle" class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Add New Rak</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form id="rakForm" action="{{ route('stock.eng.store') }}" method="POST" class="space-y-5">
            @csrf
            <div id="methodField"></div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Target Rak ID</label>
                <input type="text" name="id_rak" id="id_rak" placeholder="e.g. RAK-01" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white font-bold uppercase">
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Nozzle Description</label>
                <input type="text" name="item_name" id="item_name" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Initial Quantity</label>
                    <input type="number" name="qty" id="qty" value="0" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Min. Alert Stock</label>
                    <input type="number" name="min_stock" id="min_stock" value="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModal()" class="flex-1 rounded-xl border border-slate-200 py-3 font-bold text-slate-600 hover:bg-slate-50 transition-all dark:border-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-3 font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200">Save Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal(mode, data = null) {
        const modal = document.getElementById('modalRak');
        const form = document.getElementById('rakForm');
        const title = document.getElementById('modalTitle');
        const methodField = document.getElementById('methodField');

        modal.classList.remove('hidden');
        modal.classList.add('flex');

        if (mode === 'edit') {
            title.innerText = 'Edit Nozzle Record';
            form.action = `/stock-engineering/${data.id}`;
            methodField.innerHTML = '@method("PUT")';
            
            document.getElementById('id_rak').value = data.id_rak;
            document.getElementById('item_name').value = data.item_name;
            document.getElementById('qty').value = data.qty;
            document.getElementById('min_stock').value = data.min_stock;
        } else {
            title.innerText = 'Add New Nozzle';
            form.action = "{{ route('stock.eng.store') }}";
            methodField.innerHTML = '';
            form.reset();
            // Jika diklik dari tombol "+ Add more to Rak X", isi otomatis field id_rak
            if(data && data.id_rak) {
                document.getElementById('id_rak').value = data.id_rak;
            }
        }
    }

    function closeModal() {
        const modal = document.getElementById('modalRak');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>
@endsection