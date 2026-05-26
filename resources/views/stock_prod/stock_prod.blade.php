@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

    @php
    $outOfStock = $stocks->where('qty', '<=', 0)->count();
    $lowStock = $stocks->filter(function($item) {
        return $item->qty > 0 && $item->qty <= $item->min_stock;
    })->count();

    if ($outOfStock > 0) {
        $theme = [
            'bg' => 'bg-red-50', 
            'border' => 'border-red-200', 
            'dot' => 'bg-red-600', 
            'text' => 'text-red-800',
            'status' => 'CRITICAL',
            'msg' => $outOfStock . ' nozzle types depleted on production line — verify incoming items or manage line allocation immediately.'
        ];
    } elseif ($lowStock > 0) {
        $theme = [
            'bg' => 'bg-[#FFFBEB]', 
            'border' => 'border-amber-200', 
            'dot' => 'bg-[#F59E0B]', 
            'text' => 'text-[#92400E]',
            'status' => 'WARNING',
            'msg' => $lowStock . ' items running low on line — check engineering transaction status'
        ];
    } else {
        $theme = [
            'bg' => 'bg-emerald-50', 
            'border' => 'border-emerald-200', 
            'dot' => 'bg-emerald-500', 
            'text' => 'text-emerald-800',
            'status' => 'STABLE',
            'msg' => 'All registered production lines stable — component stocks are sufficient'
        ];
    }
    @endphp

    <div class="mb-6 flex items-center gap-3 rounded-2xl border {{ $theme['border'] }} {{ $theme['bg'] }} px-5 py-3 shadow-sm transition-all">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $theme['dot'] }} animate-pulse"></span>
        <p class="text-sm font-medium {{ $theme['text'] }}">
            <span class="uppercase font-bold mr-1">{{ $theme['status'] }}:</span> 
            {{ $theme['msg'] }}
        </p>
    </div>

    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Production Line Control Deck</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Manage Machine Lines and Incoming Engineering Nozzles</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <button onclick="openAddLineModal()" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                ADD LINE
            </button>

            <button onclick="openDeleteLineModal()" class="flex items-center gap-2 rounded-lg bg-rose-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-rose-700 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                DELETE LINE
            </button>

            <button onclick="openAddNozzleModal()" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                ADD NOZZLE (IN)
            </button>

            <a href="{{ route('stock.prod.export.csv') }}" class="flex items-center gap-2 rounded-lg bg-slate-700 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-slate-800 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                EXPORT CSV
            </a>
        </div>
    </div>

    <div class="mb-6">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Nozzle Stock Accumulation Summary
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @php
                // Kelompokkan data stocks berdasarkan nama/nomor nozzle untuk dihitung total Qty-nya
                $groupedStocks = $stocks->groupBy('no_nozzle');
            @endphp
            
            @forelse($groupedStocks as $nozzleType => $items)
                <div class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-boxdark hover:shadow-md transition-all">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500">TYPE</span>
                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-[10px] font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 font-mono">
                            {{ $items->count() }} Tx
                        </span>
                    </div>
                    <div class="text-lg font-black text-slate-800 dark:text-white mb-1">
                        {{ $nozzleType ?: 'Unknown' }}
                    </div>
                    <div class="flex items-baseline gap-1 text-xs text-slate-500">
                        <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 font-mono">{{ $items->sum('qty') }}</span>
                        <span class="text-[10px] uppercase font-semibold text-slate-400">Pcs Total</span>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-4 text-center text-xs text-slate-400 italic bg-white dark:bg-boxdark rounded-xl border border-dashed border-slate-200 dark:border-slate-700">
                    No accumulation data available.
                </div>
            @endforelse
        </div>
    </div>
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative mb-6 w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search line or component data..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 dark:border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500">
            </div>

            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide border-b border-slate-100 dark:border-slate-700 pb-1" id="lineTabs">
                <button onclick="filterLine('all')" class="tab-btn active px-4 py-2 rounded-t-lg text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm">
                    All Factory Lines
                </button>
                @foreach($lines as $line)
                    <button onclick="filterLine('{{ $line->no_line }}')" class="tab-btn px-4 py-2 rounded-t-lg text-xs font-bold text-slate-500 dark:text-slate-400 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 whitespace-nowrap">
                        {{ $line->no_line }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="prodStockTable">
                <thead>
                    <tr class="text-[10px] font-bold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-4 text-center">NO</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4">Line ID</th>
                        <th class="px-4 py-4">No Line</th>
                        <th class="px-4 py-4">No Nozzle</th>
                        <th class="px-4 py-4">Req Sparepart ID</th>
                        <th class="px-4 py-4">Part No</th>
                        <th class="px-4 py-4">Sap Code</th>
                        <th class="px-4 py-4">Barcode ID</th>
                        <th class="px-4 py-4">Transaction Out</th>
                        <th class="px-4 py-4 text-center">Current Qty</th>
                        <th class="px-4 py-4 text-center">Min Stock</th>
                        <th class="px-4 py-4">Created At</th>
                        <th class="px-4 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($stocks as $index => $item)
                    <tr class="row-stock-prod hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all" data-line="{{ $item->line->no_line ?? '' }}">
                        <td class="px-4 py-4 text-center font-medium">{{ $stocks->firstItem() + $index }}</td>
                        
                        <td class="px-4 py-4 text-center">
                            <div class="flex justify-center" title="{{ $item->status_label['text'] ?? 'Status' }}">
                                <div class="h-3 w-3 rounded-full {{ $item->status_label['color'] ?? 'bg-slate-300' }} shadow-md animate-pulse"></div>
                            </div>
                        </td>

                        <td class="px-4 py-4 text-slate-500 font-mono">{{ $item->line_id }}</td>
                        <td class="px-4 py-4 font-bold text-indigo-600 dark:text-indigo-400">{{ $item->line->no_line ?? '-' }}</td>
                        <td class="px-4 py-4 font-bold">{{ $item->no_nozzle ?? '-' }}</td>
                        <td class="px-4 py-4 font-medium text-slate-600 dark:text-slate-300">{{ $item->request_no ?? '-' }}</td>
                        
                        <td class="px-4 py-4 font-medium">{{ $item->part_no ?? '-' }}</td>
                        <td class="px-4 py-4 font-mono font-bold">{{ $item->sap_code ?? '-' }}</td>
                        
                        <td class="px-4 py-4 font-mono text-slate-500">{{ $item->barcode_id ?? '-' }}</td>
                        <td class="px-4 py-4 font-mono text-slate-500">{{ $item->transaction_out_id ?? '-' }}</td>
                        
                        <td class="px-4 py-4 text-center font-bold text-sm text-slate-800 dark:text-slate-100">{{ $item->qty }}</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-500">{{ $item->min_stock }}</td>
                        
                        <td class="px-4 py-4 text-[10px] font-medium text-slate-400 whitespace-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditModal({{ json_encode($item) }})" class="rounded bg-amber-500 px-2 py-1 text-[10px] font-bold text-white hover:bg-amber-600 transition-all">
                                    EDIT
                                </button>
                                
                                <form id="delete-form-{{ $item->id }}" action="{{ route('stock.prod.destroy', $item->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete({{ $item->id }})" class="rounded bg-rose-500 px-2 py-1 text-[10px] font-bold text-white hover:bg-rose-600 transition-all">
                                        DELETE
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="14" class="py-12 text-center text-slate-400 italic">No component stock found on production floor.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 px-6 py-4">
            <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                Showing {{ $stocks->firstItem() ?? 0 }} to {{ $stocks->lastItem() ?? 0 }} of {{ $stocks->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center gap-2">
                {{ $stocks->links() }}
            </div>
        </div>
    </div>
</div>

{{-- MODAL 1: ADD LINE --}}
<div id="modalAddLine" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Register Production Line</h3>
            <button onclick="closeAddLineModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('stock.prod.line.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Select Line (From Registered Master Data)</label>
                <select name="no_line" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                    <option value="">-- Select Line --</option>
                    @foreach($masterLines as $mLine)
                        <option value="{{ $mLine->no_line }}">{{ $mLine->no_line }} - {{ $mLine->name_machine ?? 'SMT Machine' }}</option>
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Hanya memuat lini yang belum aktif. Begitu terdaftar, lini ini akan hilang dari pilihan.</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeAddLineModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all">Register Line</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 2: DELETE LINE POP-UP KHUSUS --}}
<div id="modalDeleteLine" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-rose-600 dark:text-rose-400">Remove Registered Line</h3>
            <button onclick="closeDeleteLineModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Select Active Line to Remove</label>
                <select id="select_delete_line_id" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-rose-500 dark:text-white" required>
                    <option value="">-- Choose Active Line --</option>
                    @foreach($stocks as $stockItem)
                        @if($stockItem->line)
                            <option value="{{ $stockItem->id }}">{{ $stockItem->line->no_line }} [ID: {{ $stockItem->line_id }}]</option>
                        @endif
                    @endforeach
                </select>
                <p class="text-[10px] text-slate-400 mt-1">Menghapus lini dari control deck akan mengembalikan status ketersediaannya ke dalam modal registrasi awal.</p>
            </div>
            
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeDeleteLineModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="button" onclick="executeDeleteLine()" class="bg-rose-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-rose-700 transition-all">Delete Line</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 3: ADD NOZZLE (IN) --}}
<div id="modalAddNozzle" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-lg shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Add Nozzle from Engineering (IN)</h3>
            <button onclick="closeAddNozzleModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('stock.prod.nozzle.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Pilih Transaksi Keluar (Engineering)</label>
                <select name="stock_out_log_id" id="select_log" onchange="autoFillQty()" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-emerald-500 dark:text-white" required>
                    <option value="">-- Pilih No. Transaksi --</option>
                    @foreach($logs as $log)
                        <option value="{{ $log->id }}" data-qty="{{ $log->qty_out }}">
                            {{ $log->transaction_out_id }} | Nozzle: {{ $log->no_nozzle }} | Qty: {{ $log->qty_out }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Target Allocated Line</label>
                <select name="line_id" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                    <option value="">-- Select Line --</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->line_id }}">{{ $line->no_line }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Input Qty</label>
                    <input type="number" name="qty" id="input_qty" min="1" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Min Stock Threshold</label>
                    <input type="number" name="min_stock" min="1" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4">
                <button type="button" onclick="closeAddNozzleModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-emerald-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-emerald-700 transition-all">Process In & Sync</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL 4: EDIT STOCK PRODUCTION --}}
<div id="modalEditStock" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Edit Stock Production</h3>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="formEditStock" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Line Target</label>
                <select name="line_id" id="edit_line_id" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                    @foreach($lines as $line)
                        <option value="{{ $line->line_id }}">{{ $line->no_line }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">No Nozzle</label>
                <input type="text" name="no_nozzle" id="edit_no_nozzle" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Part No</label>
                    <input type="text" name="part_no" id="edit_part_no" class="w-full rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-700 p-2.5 text-sm outline-none dark:text-white" readonly>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">SAP Code</label>
                    <input type="text" name="sap_code" id="edit_sap_code" class="w-full rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-700 p-2.5 text-sm outline-none dark:text-white" readonly>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Current Qty</label>
                    <input type="number" name="qty" id="edit_qty" min="0" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Min Stock Threshold</label>
                    <input type="number" name="min_stock" id="edit_min_stock" min="0" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-amber-600 transition-all">Update Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filterLine(lineNo) {
        let btns = document.querySelectorAll(".tab-btn");
        btns.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-100', 'dark:border-slate-700');
        });

        const activeBtn = event.currentTarget;
        activeBtn.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
        activeBtn.classList.remove('bg-white', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-100', 'dark:border-slate-700');

        document.querySelectorAll(".row-stock-prod").forEach(row => {
            row.style.display = (lineNo === 'all' || row.getAttribute('data-line') === lineNo) ? "" : "none";
        });
    }

    function searchTable() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        document.querySelectorAll(".row-stock-prod").forEach(row => {
            row.style.display = row.innerText.toUpperCase().includes(input) ? "" : "none";
        });
    }

    // Modal Control Functions
    function openAddLineModal() { document.getElementById('modalAddLine').classList.remove('hidden'); }
    function closeAddLineModal() { document.getElementById('modalAddLine').classList.add('hidden'); }
    
    function openDeleteLineModal() { document.getElementById('modalDeleteLine').classList.remove('hidden'); }
    function closeDeleteLineModal() { document.getElementById('modalDeleteLine').classList.add('hidden'); }

    function openAddNozzleModal() { document.getElementById('modalAddNozzle').classList.remove('hidden'); }
    function closeAddNozzleModal() { document.getElementById('modalAddNozzle').classList.add('hidden'); }

    // Execute Delete Line dari Modal Atas via SweetAlert2
    function executeDeleteLine() {
        let selectedId = document.getElementById('select_delete_line_id').value;
        if (!selectedId) {
            Swal.fire({ icon: 'error', title: 'Gagal', text: 'Silakan pilih lini produksi aktif yang ingin dihapus!' });
            return;
        }

        Swal.fire({
            title: 'Hapus Lini Terpilih?',
            text: "Lini akan terhapus dari deck kontrol utama produksi!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus Lini!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                let form = document.getElementById(`delete-form-${selectedId}`);
                if (form) {
                    form.submit();
                } else {
                    Swal.fire({ icon: 'error', title: 'Sistem Error', text: 'Form pemicu eksekusi data gagal dimuat.' });
                }
            }
        });
    }

    // Modal Edit Controls
    function openEditModal(item) {
        const form = document.getElementById('formEditStock');
        form.action = `/prod/stock/${item.id}`;
        
        document.getElementById('edit_line_id').value = item.line_id;
        document.getElementById('edit_no_nozzle').value = item.no_nozzle;
        document.getElementById('edit_part_no').value = item.part_no ?? '';
        document.getElementById('edit_sap_code').value = item.sap_code ?? '';
        document.getElementById('edit_qty').value = item.qty;
        document.getElementById('edit_min_stock').value = item.min_stock;

        document.getElementById('modalEditStock').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('modalEditStock').classList.add('hidden'); }

    // SweetAlert2 Confirm Delete dari baris tabel langsung
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Lini / Nozzle Terkait?',
            text: "Data alokasi item pada area produksi ini akan dihapus permanen!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(`delete-form-${id}`).submit();
            }
        });
    }

    function autoFillQty() {
        const selectLog = document.getElementById('select_log');
        const inputQty = document.getElementById('input_qty');
        const selectedOption = selectLog.options[selectLog.selectedIndex];
        const qtyValue = selectedOption.getAttribute('data-qty');
        
        if (qtyValue) {
            inputQty.value = qtyValue;
        } else {
            inputQty.value = '';
        }
    }

    // Flash Alert Notification Handler
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif

    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Validation Error', text: "{{ $errors->first() }}" });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal', text: "{{ session('error') }}" });
    @endif
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .swal2-container { z-index: 10000 !important; }
</style>
@endsection