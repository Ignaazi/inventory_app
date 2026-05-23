@extends('admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

    @php
    // Patokan data stock di area lini produksi menggunakan accessor label model baru
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
            'msg' => $outOfStock . ' nozzle types depleted on production line — request stock to engineering immediately'
        ];
    } elseif ($lowStock > 0) {
        $theme = [
            'bg' => 'bg-[#FFFBEB]', 
            'border' => 'border-amber-200', 
            'dot' => 'bg-[#F59E0B]', 
            'text' => 'text-[#92400E]',
            'status' => 'WARNING',
            'msg' => $lowStock . ' items running low on line — prepare for supply request'
        ];
    } else {
        $theme = [
            'bg' => 'bg-emerald-50', 
            'border' => 'border-emerald-200', 
            'dot' => 'bg-emerald-500', 
            'text' => 'text-emerald-800',
            'status' => 'STABLE',
            'msg' => 'All production lines stable — component stocks are sufficient'
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

    <!-- Header & Tombol Aksi Kerja Area Produksi -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Production Line Stock Status</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400">Real-time Nozzle Stock on Machine Lines</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- CSV EXPORT -->
            <a href="{{ route('stock.prod.export') }}" class="flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                </svg>
                CSV EXPORT
            </a>

            <!-- VIEW REQUEST HISTORY -->
            <a href="{{ route('stock.prod.request.history') }}" class="flex items-center gap-2 rounded-lg bg-slate-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                REQUEST HISTORY
            </a>
            
            <!-- RECEIVE ITEM (IN) -->
            <button onclick="openReceiveModal()" class="flex items-center gap-2 rounded-lg bg-orange-500 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-orange-600 dark:bg-orange-600 dark:hover:bg-orange-700 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                </svg>
                RECEIVE ITEM (IN)
            </button>

            <!-- REQUEST STOCK TO ENGINEERING -->
            <button onclick="openRequestModal()" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-all active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                REQUEST STOCK
            </button>
        </div>
    </div>

    <!-- Kontainer Tabel Data -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative mb-6 w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search component data on production area..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 dark:border-slate-600 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500">
            </div>

            <!-- Tab Filter Lini Produksi -->
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
                        <th class="px-4 py-4">Updated At</th>
                        <th class="px-4 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @forelse($stocks as $index => $item)
                    <tr class="row-stock-prod hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all" data-line="{{ $item->line->no_line ?? '' }}">
                        <td class="px-4 py-4 text-center font-medium">{{ $stocks->firstItem() + $index }}</td>
                        
                        <!-- Status Bulat Berwarna -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex justify-center" title="{{ $item->status_label['text'] }}">
                                <div class="h-3 w-3 rounded-full {{ $item->status_label['color'] }} shadow-md animate-pulse"></div>
                            </div>
                        </td>

                        <!-- Line ID & No Line -->
                        <td class="px-4 py-4 text-slate-500 font-mono">{{ $item->line_id }}</td>
                        <td class="px-4 py-4 font-bold text-indigo-600 dark:text-indigo-400">{{ $item->line->no_line ?? '-' }}</td>
                        
                        <!-- No Nozzle -->
                        <td class="px-4 py-4 font-bold">{{ $item->no_nozzle ?? '-' }}</td>
                        
                        <!-- Req Sparepart ID -->
                        <td class="px-4 py-4 font-medium text-slate-600 dark:text-slate-300">{{ $item->request_no ?? '-' }}</td>
                        
                        <!-- Part No & SAP Code -->
                        <td class="px-4 py-4 font-medium">{{ $item->part_no ?? '-' }}</td>
                        <td class="px-4 py-4 font-mono font-bold">{{ $item->sap_code ?? '-' }}</td>
                        
                        <!-- Barcode ID & Transaction Out -->
                        <td class="px-4 py-4 font-mono text-slate-500">{{ $item->barcode_id ?? '-' }}</td>
                        <td class="px-4 py-4 font-mono text-slate-500">{{ $item->transaction_out_id ?? '-' }}</td>
                        
                        <!-- Qty & Min Stock -->
                        <td class="px-4 py-4 text-center font-bold text-sm text-slate-800 dark:text-slate-100">{{ $item->qty }}</td>
                        <td class="px-4 py-4 text-center font-semibold text-slate-500">{{ $item->min_stock }}</td>
                        
                        <!-- Timestamps -->
                        <td class="px-4 py-4 text-[10px] font-medium text-slate-400 whitespace-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-4 text-[10px] font-medium text-slate-400 whitespace-nowrap">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
                        
                        <!-- Action: Edit & Delete -->
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
                    <tr><td colspan="15" class="py-12 text-center text-slate-400 italic">No component stock found on production floor.</td></tr>
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

{{-- MODAL ACTION 1: REQUEST STOCK TO ENGINEERING --}}
<div id="modalRequestStock" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Request Stock to Engineering</h3>
            <button onclick="closeRequestModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('stock.prod.request.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Target Production Line</label>
                <select name="line_id" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                    <option value="">-- Select Line --</option>
                    @foreach($lines as $line)
                        <option value="{{ $line->id }}">{{ $line->no_line }} - {{ $line->name_machine }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Select Nozzle Item</label>
                <select name="no_nozzle" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
                    <option value="">-- Select Registered Nozzle --</option>
                    @foreach($allNozzles as $nz)
                        <option value="{{ $nz->no_nozzle }}">{{ $nz->no_nozzle }} ({{ $nz->sap_code }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Request Quantity</label>
                <input type="number" name="request_qty" min="1" placeholder="e.g. 5" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white" required>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeRequestModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ACTION 2: RECEIVE ITEM (IN) --}}
<div id="modalReceiveStock" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-sm shadow-2xl border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 class="text-lg font-bold text-slate-800 dark:text-white">Receive Stock Item (In)</h3>
            <button onclick="closeReceiveModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form action="{{ route('stock.prod.receive') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Input Token / Request ID Code</label>
                <input type="text" name="request_code" placeholder="e.g. REQ-2026001" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white uppercase" required>
                <p class="text-[10px] text-slate-400 mt-1">Sistem akan memverifikasi material pengiriman dari Engineering.</p>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeReceiveModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-orange-500 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-orange-600 transition-all">Confirm Receive</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL ACTION 3: EDIT STOCK PRODUCTION (NEW) --}}
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
                        <option value="{{ $line->id }}">{{ $line->no_line }} - {{ $line->name_machine }}</option>
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
            <div>
                <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Req Sparepart ID (Optional)</label>
                <input type="text" name="request_no" id="edit_request_no" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-amber-500 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-amber-600 transition-all">Update Stock</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Penyesuaian filter tab berdasarkan Line Produksi SMT
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

    // Modal Request & Receive Controls
    function openRequestModal() { document.getElementById('modalRequestStock').classList.remove('hidden'); }
    function closeRequestModal() { document.getElementById('modalRequestStock').classList.add('hidden'); }
    
    function openReceiveModal() { document.getElementById('modalReceiveStock').classList.remove('hidden'); }
    function closeReceiveModal() { document.getElementById('modalReceiveStock').classList.add('hidden'); }

    // Modal Edit Controls & Data Binder
    function openEditModal(item) {
        const form = document.getElementById('formEditStock');
        form.action = `/prod/stock/${item.id}`; // Pasangkan rute ID tujuan update
        
        document.getElementById('edit_line_id').value = item.line_id;
        document.getElementById('edit_no_nozzle').value = item.no_nozzle;
        document.getElementById('edit_part_no').value = item.part_no ?? '';
        document.getElementById('edit_sap_code').value = item.sap_code ?? '';
        document.getElementById('edit_qty').value = item.qty;
        document.getElementById('edit_min_stock').value = item.min_stock;
        document.getElementById('edit_request_no').value = item.request_no ?? '';

        document.getElementById('modalEditStock').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('modalEditStock').classList.add('hidden'); }

    // SweetAlert2 Confirm Delete Trigger
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Data Stock?',
            text: "Data stock nozzle ini akan dihapus permanen dari area produksi!",
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

    // Intersepsi Form Submit Request dengan SweetAlert2
    document.querySelector('#modalRequestStock form').addEventListener('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Kirim Request Stock?',
            text: "Permintaan material akan diteruskan ke departemen Engineering.",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Kirim!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    // Flash Alert Notifikasi Handler dari Controller
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif

    @if($errors->any())
        Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ $errors->first() }}" });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: "{{ session('error') }}" });
    @endif
</script>

<style>
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .swal2-container { z-index: 10000 !important; }
</style>
@endsection