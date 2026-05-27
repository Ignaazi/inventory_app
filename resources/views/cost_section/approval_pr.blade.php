@extends('admin')

@section('content')
<div x-data="{ 
    currentFilter: 'all',
    selectedPr: null, 
    
    filterTable(status) {
        this.currentFilter = status;
        const rows = document.querySelectorAll('.odoo-table-row');
        rows.forEach(row => {
            if (status === 'all') {
                row.style.display = '';
            } else if (status === 'urgent') {
                const priority = row.getAttribute('data-priority');
                row.style.display = (priority === 'urgent') ? '' : 'none';
            } else {
                const rowStatus = row.getAttribute('data-status');
                row.style.display = (rowStatus === status) ? '' : 'none';
            }
        });
    }
}" class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 font-sans text-slate-900 dark:text-slate-100">

    <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-950 dark:text-white uppercase tracking-tight">
                Purchase Requests Approval
            </h2>
            <p class="text-xs font-medium text-slate-600 dark:text-gray-400">Verify, audit, and validate engineering machine sparepart requirements</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('costing.pr.index') }}" method="GET" class="relative w-full sm:w-auto">
                <div class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="SEARCH REFERENCE..." class="bg-white dark:bg-slate-900 text-slate-900 dark:text-white pl-9 pr-12 py-2 border border-gray-200 dark:border-gray-800 rounded-xl text-xs font-medium focus:outline-none uppercase w-full sm:w-60 transition-all shadow-sm">
                @if($search)
                    <a href="{{ route('costing.pr.index') }}" class="absolute inset-y-0 right-3 flex items-center text-xs text-slate-400 hover:text-slate-950 uppercase font-bold">Clear</a>
                @endif
            </form>

            <div class="inline-flex items-center gap-2 p-1 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm w-full sm:w-auto justify-center sm:justify-start">
                <button @click="filterTable('all')" :class="currentFilter === 'all' ? 'bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white'" class="px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 uppercase flex-1 sm:flex-none text-center">All</button>
                <button @click="filterTable('waiting approval')" :class="currentFilter === 'waiting approval' ? 'bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white'" class="px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 uppercase flex-1 sm:flex-none text-center">To Approve</button>
                <button @click="filterTable('urgent')" :class="currentFilter === 'urgent' ? 'bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white' : 'text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white'" class="px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 uppercase flex-1 sm:flex-none text-center">Urgent</button>
            </div>
        </div>
    </div>

    <div class="space-y-3 mb-4">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 dark:bg-emerald-950/20 dark:border-emerald-900 dark:text-emerald-400 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                Success: {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="p-4 bg-red-50 border border-red-200 text-red-600 dark:bg-red-950/20 dark:border-red-900 dark:text-red-400 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
                Error: {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-slate-900 sm:px-6">
        <div class="w-full overflow-x-auto">
            <table class="min-w-full text-left border-collapse" id="approval-table">
                <thead>
                    <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50 dark:bg-slate-800/40">
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white w-[180px]">PR Reference</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Item Product / Description</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white w-[140px]">SAP Code</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-[70px]">QTY</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-[100px]">Priority</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">Requested By</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-[130px]">Stage Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse($pendingPr as $pr)
                    <tr @click="selectedPr = {
                            id: '{{ $pr->id }}',
                            code: '{{ $pr->pr_code }}',
                            type: '{{ $pr->type_product }}',
                            product: '{{ $pr->product }}',
                            qty: '{{ $pr->qty ?? 1 }}',
                            priority: '{{ $pr->priority }}',
                            name: '{{ $pr->name }}',
                            nik: '{{ $pr->nik ?? '-' }}',
                            destination: '{{ $pr->destination ?? '-' }}',
                            notes: `{{ $pr->notes ?? 'No special notes given.' }}`,
                            date: '{{ $pr->created_at ? $pr->created_at->format('Y-m-d H:i') : '' }}',
                            status: '{{ strtolower($pr->status) }}',
                            approve_route: '{{ route('costing.pr.approve', $pr->id) }}',
                            reject_route: '{{ route('costing.pr.reject', $pr->id) }}'
                        }"
                        class="odoo-table-row hover:bg-gray-50/50 dark:hover:bg-white/[0.02] cursor-pointer transition-colors duration-200"
                        data-status="{{ strtolower($pr->status) === 'approved' ? 'approved' : (strtolower($pr->status) === 'rejected' ? 'rejected' : 'waiting approval') }}" 
                        data-priority="{{ strtolower($pr->priority) }}">
                        
                        <td class="py-2 px-3 text-xs font-semibold text-purple-600 dark:text-purple-400 font-mono">
                            {{ $pr->pr_code }}
                        </td>
                        <td class="py-2 px-3 text-xs font-semibold text-black dark:text-white uppercase tracking-tight text-center">
                            {{ $pr->type_product }}
                        </td>
                        <td class="py-2 px-3 text-xs font-semibold text-indigo-600 dark:text-indigo-400 uppercase font-mono">
                            {{ $pr->product }}
                        </td>
                        <td class="py-2 px-3 text-xs font-bold text-slate-900 dark:text-white text-center font-mono">
                            {{ $pr->qty ?? 1 }}
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if(strtolower($pr->priority) == 'urgent')
                                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">Urgent</span>
                            @else
                                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600/50">Normal</span>
                            @endif
                        </td>
                        <td class="py-2 px-3">
                            <span class="text-xs font-semibold text-black dark:text-white uppercase block leading-none mb-0.5">{{ $pr->name }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">NIK: {{ $pr->nik ?? '-' }}</span>
                        </td>
                        <td class="py-2 px-3 text-center">
                            @if(strtolower($pr->status) === 'approved')
                                <span class="inline-flex items-center justify-center w-24 rounded-lg py-1 text-[10px] font-bold tracking-wider uppercase border border-emerald-500/30 bg-emerald-50/50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Approved</span>
                            @elseif(strtolower($pr->status) === 'rejected')
                                <span class="inline-flex items-center justify-center w-24 rounded-lg py-1 text-[10px] font-bold tracking-wider uppercase border border-rose-500/30 bg-rose-50/50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400">Rejected</span>
                            @else
                                <span class="inline-flex items-center justify-center w-24 rounded-lg py-1 text-[10px] font-bold tracking-wider uppercase border border-blue-500/30 bg-blue-50/50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">Waiting</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-xs font-bold text-slate-400 uppercase tracking-wider">
                            No purchase requests pending inside ledger.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-2 border-t border-gray-100 pt-4 dark:border-gray-800">
            <p class="text-xs font-bold text-slate-950 dark:text-white">
                Showing {{ $pendingPr->firstItem() ?? 0 }} to {{ $pendingPr->lastItem() ?? 0 }} of {{ $pendingPr->total() ?? 0 }} entries
            </p>
            <div class="flex items-center odoo-pagination-container">
                {{ $pendingPr->appends(['search' => $search])->links() }}
            </div>
        </div>
    </div>

    {{-- RIGHT SIDE SLIDER PANEL --}}
    <div x-show="selectedPr" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 flex justify-end items-stretch"
         style="display: none;">
         
        <div @click.away="selectedPr = null" 
             class="w-full max-w-4xl bg-slate-50 dark:bg-slate-950 shadow-2xl flex flex-col justify-between border-l border-gray-200 dark:border-gray-800 animate-slide-in">
            
            <div class="bg-white dark:bg-slate-900 border-b border-gray-200 dark:border-gray-800 px-4 py-3 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between shadow-sm">
                
                <div class="flex flex-wrap items-center gap-3 w-full lg:w-auto order-2 lg:order-1">
                    <button @click="selectedPr = null" class="bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-400 text-white px-5 py-2 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm hover:opacity-90 transition-opacity flex-grow sm:flex-grow-0 text-center">
                        Back
                    </button>

                    <div x-show="selectedPr && selectedPr.status !== 'approved' && selectedPr.status !== 'rejected'" class="flex items-center gap-3 w-full sm:w-auto">
                        <button type="submit" form="approvalMainForm" class="w-full sm:w-auto bg-gradient-to-r from-emerald-500 via-green-400 to-yellow-300 text-white px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm hover:opacity-90 transition-opacity">
                            Approve PR
                        </button>
                        
                        <form :action="selectedPr ? selectedPr.reject_route : '#'" method="POST" class="inline w-full sm:w-auto" onsubmit="return confirm('Tolak request item ini, Bro?')">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-rose-500 via-orange-500 to-amber-400 text-white px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-wider shadow-sm hover:opacity-90 transition-opacity">
                                Reject
                            </button>
                        </form>
                    </div>

                    <div x-show="selectedPr && (selectedPr.status === 'approved' || selectedPr.status === 'rejected')" class="text-xs font-bold text-white uppercase bg-purple-600 dark:bg-purple-700 px-4 py-2 rounded-lg text-center w-full sm:w-auto shadow-sm tracking-wider">
                        Processed Ledger
                    </div>
                </div>

                <div class="flex items-center bg-gray-100 dark:bg-slate-800 rounded-lg text-[11px] font-extrabold uppercase select-none w-full lg:w-auto order-1 lg:order-2 overflow-hidden shadow-inner p-0.5">
                    
                    <div :class="selectedPr && selectedPr.status === 'draft' 
                            ? 'bg-gradient-to-r from-orange-500 to-amber-400 text-white font-active' 
                            : 'bg-gray-200 text-slate-500 dark:bg-slate-700 dark:text-slate-400'"
                          class="odoo-step step-first">
                        <span>Draft</span>
                    </div>
                    
                    <div :class="selectedPr && selectedPr.status !== 'approved' && selectedPr.status !== 'rejected' && selectedPr.status !== 'draft'
                            ? 'bg-gradient-to-r from-blue-600 via-cyan-500 to-teal-400 text-white font-active' 
                            : 'bg-gray-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500'"
                          class="odoo-step">
                        <span>Waiting Approval</span>
                    </div>
                    
                    <div :class="selectedPr && selectedPr.status === 'approved' 
                            ? 'bg-gradient-to-r from-emerald-600 via-green-500 to-lime-400 text-white font-active' 
                            : (selectedPr && selectedPr.status === 'rejected' ? 'bg-gradient-to-r from-rose-600 to-red-500 text-white font-active' : 'bg-gray-100 text-slate-400 dark:bg-slate-800 dark:text-slate-500')"
                          class="odoo-step step-last">
                        <span>Done</span>
                    </div>
                </div>
            </div>

            <div class="flex-grow p-4 md:p-6 overflow-y-auto">
                <div class="bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 rounded-2xl p-6 shadow-sm text-slate-950 dark:text-white">
                    
                    <div class="mb-5 border-b border-gray-100 dark:border-gray-800 pb-3">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">Purchase Request Reference</label>
                        <h2 class="text-xl font-bold text-purple-600 dark:text-purple-400 tracking-tight font-mono" x-text="selectedPr ? selectedPr.code : ''"></h2>
                    </div>

                    {{-- MASTER MAIN ACTION FORM --}}
                    <form :action="selectedPr ? selectedPr.approve_route : '#'" method="POST" id="approvalMainForm" onsubmit="return confirm('Setujui Purchase Request ini dengan jumlah QTY yang ditentukan?')">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-3.5 text-xs font-sans">
                            
                            <div class="space-y-3.5">
                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Requester Name</label>
                                    <div class="col-span-2 text-slate-950 dark:text-white font-medium uppercase text-xs" x-text="selectedPr ? selectedPr.name : ''"></div>
                                </div>

                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">NIK / NIM</label>
                                    <div class="col-span-2 text-slate-800 dark:text-slate-300 font-mono font-medium text-xs" x-text="selectedPr ? selectedPr.nik : ''"></div>
                                </div>

                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Product (Category)</label>
                                    <div class="col-span-2 text-indigo-600 dark:text-indigo-400 font-semibold uppercase font-mono text-xs" x-text="selectedPr ? selectedPr.product : ''"></div>
                                </div>

                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Type Product</label>
                                    <div class="col-span-2 text-slate-950 dark:text-white font-medium uppercase text-xs" x-text="selectedPr ? selectedPr.type : ''"></div>
                                </div>
                            </div>

                            <div class="space-y-3.5">
                                {{-- 🌟 TAMBAHAN: INPUT QUANTITY (QTY) DI DALAM PANEL APPROVAL COSTING --}}
                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Quantity (QTY)</label>
                                    <div class="col-span-2 flex items-center gap-1.5">
                                        <template x-if="selectedPr && selectedPr.status !== 'approved' && selectedPr.status !== 'rejected'">
                                            <input type="number" name="qty" min="1" x-model="selectedPr.qty" required class="w-20 bg-gray-50 dark:bg-slate-800 text-slate-950 dark:text-white font-bold font-mono text-xs px-2 py-0.5 border border-gray-200 dark:border-slate-700 rounded focus:ring-1 focus:ring-purple-500 outline-none text-center">
                                        </template>
                                        <template x-if="selectedPr && (selectedPr.status === 'approved' || selectedPr.status === 'rejected')">
                                            <span class="text-slate-950 dark:text-white font-bold font-mono text-xs" x-text="selectedPr.qty + ' Pcs'"></span>
                                        </template>
                                        <span x-show="selectedPr && selectedPr.status !== 'approved' && selectedPr.status !== 'rejected'" class="text-[10px] text-slate-400 font-bold uppercase">Pcs</span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Priority</label>
                                    <div class="col-span-2">
                                        <template x-if="selectedPr && selectedPr.priority.toLowerCase() === 'urgent'">
                                            <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400">Urgent</span>
                                        </template>
                                        <template x-if="selectedPr && selectedPr.priority.toLowerCase() !== 'urgent'">
                                            <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200 dark:bg-slate-700/50 dark:text-slate-300">Normal</span>
                                        </template>
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Request By</label>
                                    <div class="col-span-2 text-slate-800 dark:text-slate-200 font-medium uppercase text-xs">ENGINEERING DEPARTMENT</div>
                                </div>

                                <div class="grid grid-cols-3 items-center border-b border-gray-50 dark:border-gray-800/60 pb-1.5">
                                    <label class="font-bold text-slate-400 text-[11px] uppercase">Request Date</label>
                                    <div class="col-span-2 text-slate-800 dark:text-slate-300 font-mono font-medium text-xs" x-text="selectedPr ? selectedPr.date : ''"></div>
                                </div>
                            </div>

                        </div>
                    </form>

                    <div class="mt-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                            <div>
                                <div class="border-b border-gray-200 dark:border-gray-800 flex gap-6 text-[11px] font-bold uppercase text-slate-950 dark:text-white">
                                    <span class="border-b-2 border-purple-600 pb-1 text-purple-600">Internal Notes / Reason</span>
                                </div>
                                <div class="mt-2 p-3 bg-gray-50 dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-gray-800 min-h-[64px]">
                                    <p class="text-slate-700 dark:text-slate-300 italic font-medium text-xs" x-text="selectedPr ? selectedPr.notes : ''"></p>
                                </div>
                            </div>
                            <div class="mt-5 md:mt-0">
                                <div class="border-b border-gray-200 dark:border-gray-800 flex gap-6 text-[11px] font-bold uppercase text-slate-400">
                                    <span>Shipping Destination</span>
                                </div>
                                <div class="mt-3 flex items-center gap-2 text-emerald-600 dark:text-emerald-400 font-semibold uppercase text-xs">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25s-7.5-4.108-7.5-11.25a7.5 7.5 0 1 1 15 0Z"/>
                                    </svg>
                                    <span x-text="selectedPr ? selectedPr.destination : ''"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 pt-3 flex justify-between items-center text-xs font-bold uppercase text-slate-400 border-t border-gray-100 dark:border-gray-800 select-none">
                        <span>Verification Mode Active</span>
                        <div class="flex gap-2 items-center text-sm font-bold">
                            <span>Status:</span>
                            <span :class="selectedPr && selectedPr.status === 'approved' ? 'text-emerald-600' : (selectedPr && selectedPr.status === 'rejected' ? 'text-rose-600' : 'text-blue-600')" x-text="selectedPr ? selectedPr.status : ''"></span>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white dark:bg-slate-900 border-t border-gray-200 dark:border-gray-800 p-4 text-center text-xs text-slate-500 font-bold uppercase font-mono tracking-wider select-none">
                Costing Department Verification Ledger
            </div>
        </div>
    </div>
</div>

<style>
    .odoo-pagination-container nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
    .odoo-pagination-container nav[role="navigation"] div:first-child { display: none; }
    .pagination .page-item.active .page-link {
        background-color: #7c3aed !important;
        border-color: #7c3aed !important;
        color: white !important;
        font-weight: bold;
        font-size: 12px;
    }
    .pagination .page-link {
        color: inherit !important;
        font-weight: 700;
        font-size: 12px;
        padding: 4px 8px;
    }
    @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    .animate-slide-in { animation: slideIn 0.2s ease-out forwards; }

    /* 🎯 EXACT ODOO CHEVRON PIPELINE */
    .odoo-step {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px 22px 6px 30px;
        min-height: 32px;
        clip-path: polygon(calc(100% - 12px) 0%, 100% 50%, calc(100% - 12px) 100%, 0% 100%, 12px 50%, 0% 0%);
        margin-right: -10px;
        transition: all 0.3s ease;
    }

    .odoo-step.step-first {
        padding-left: 18px;
        clip-path: polygon(calc(100% - 12px) 0%, 100% 50%, calc(100% - 12px) 100%, 0% 100%, 0% 50%, 0% 0%);
    }

    .odoo-step.step-last {
        padding-right: 18px;
        margin-right: 0;
        clip-path: polygon(100% 0%, 100% 50%, 100% 100%, 0% 100%, 12px 50%, 0% 0%);
    }

    .font-active span {
        color: #ffffff !important;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .odoo-step span {
        display: block;
        white-space: nowrap;
        text-align: center;
    }

    /* Menghilangkan spin arrows bawaan browser pada input number qty */
    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button { 
        -webkit-appearance: none; 
        margin: 0; 
    }
    input[type=number] { -moz-appearance: textfield; }
</style>
@endsection