@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">
                Stock Engineering
            </h2>
            <p class="text-sm text-slate-500 font-medium">Manage and monitor nozzle rack availability</p>
        </div>

        <button 
            onclick="toggleModal()"
            class="inline-flex items-center justify-center gap-2.5 rounded-xl bg-indigo-600 py-3 px-6 text-center font-bold text-white hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-200 dark:shadow-none"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Rak
        </button>
    </div>

    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" class="mb-6 flex w-full rounded-xl border-l-6 border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20 p-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="rounded-full bg-emerald-500 p-1">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-bold text-emerald-800 dark:text-emerald-400">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900 overflow-hidden">
        <div class="max-w-full overflow-x-auto">
            <table class="w-full table-auto">
                <thead>
                    <tr class="bg-slate-50 text-left dark:bg-slate-800/50">
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">ID Rak</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Description</th>
                        <th class="py-4 px-6 text-center font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Quantity</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Min. Stock</th>
                        <th class="py-4 px-6 font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Status</th>
                        <th class="py-4 px-6 text-right font-bold text-xs uppercase tracking-wider text-slate-500 dark:text-slate-400">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($stocks as $item)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-5 px-6">
                            <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-bold text-slate-700 dark:bg-slate-800 dark:text-slate-300">
                                {{ $item->id_rak }}
                            </span>
                        </td>
                        <td class="py-5 px-6">
                            <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $item->item_name }}</p>
                        </td>
                        <td class="py-5 px-6 text-center">
                            <span class="text-lg font-black {{ $item->qty <= $item->min_stock ? 'text-rose-500' : 'text-slate-800 dark:text-white' }}">
                                {{ $item->qty }}
                            </span>
                        </td>
                        <td class="py-5 px-6 font-medium text-slate-500">
                            {{ $item->min_stock }}
                        </td>
                        <td class="py-5 px-6">
                            @php
                                $status = $item->qty <= 0 ? ['ZERO', 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400'] : 
                                         ($item->qty <= $item->min_stock ? ['LOW', 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'] : 
                                         ['SAFE', 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400']);
                            @endphp
                            <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-[10px] font-black uppercase tracking-wide {{ $status[1] }}">
                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>
                                {{ $status[0] }} STOCK
                            </span>
                        </td>
                        <td class="py-5 px-6 text-right">
                            <div class="flex justify-end gap-2">
                                <button class="p-2 text-slate-400 hover:text-indigo-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </button>
                                <button class="p-2 text-slate-400 hover:text-rose-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-slate-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="mt-4 text-slate-400 font-medium">No inventory data found</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAddRak" class="fixed inset-0 z-9999 hidden items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4 py-5">
    <div class="w-full max-w-lg rounded-2xl bg-white p-8 shadow-2xl dark:bg-slate-900 border border-white/20">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-xl font-black text-slate-800 dark:text-white uppercase tracking-tight">Add New Rak</h3>
            <button onclick="toggleModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        
        <form action="{{ route('stock.eng.store') }}" method="POST" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">ID Rak</label>
                <input type="text" name="id_rak" placeholder="e.g. RAK-01" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
            </div>

            <div>
                <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Item Description</label>
                <input type="text" name="item_name" required class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Quantity</label>
                    <input type="number" name="qty" value="0" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-wider text-slate-500">Min. Stock</label>
                    <input type="number" name="min_stock" value="5" class="w-full rounded-xl border border-slate-200 bg-slate-50 py-3 px-5 outline-none focus:border-indigo-500 transition-all dark:border-slate-800 dark:bg-slate-800 dark:text-white">
                </div>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="toggleModal()" class="flex-1 rounded-xl border border-slate-200 py-3 font-bold text-slate-600 hover:bg-slate-50 transition-all dark:border-slate-800 dark:text-slate-400">Cancel</button>
                <button type="submit" class="flex-1 rounded-xl bg-indigo-600 py-3 font-bold text-white hover:bg-indigo-700 shadow-lg shadow-indigo-200 dark:shadow-none transition-all">Save Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleModal() {
        const modal = document.getElementById('modalAddRak');
        modal.classList.toggle('hidden');
        modal.classList.toggle('flex');
    }
</script>
@endsection