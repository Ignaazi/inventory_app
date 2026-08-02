@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-container { z-index: 99999 !important; }
    .swal2-popup { border-radius: 1rem !important; font-family: 'Nunito', sans-serif !important; }
    .dark .swal2-popup { background-color: #0f172a !important; border: 1px solid #1e293b !important; }
    .dark .swal2-title, .dark .swal2-html-container { color: #f8fafc !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300 text-black">

    @php
    $outOfStock = $stocks->where('qty', '<=', 0)->count();
    $lowStock = $stocks->filter(function($item) {
        return $item->qty > 0 && $item->qty <= $item->min_stock;
    })->count();

    if ($outOfStock > 0) {
        $theme = ['bg' => 'bg-red-50 dark:bg-red-950/20', 'border' => 'border-red-200 dark:border-red-900/50', 'dot' => 'bg-red-600', 'text' => 'text-red-800 dark:text-red-300', 'status' => 'LOST', 'msg' => $outOfStock . ' item out of stock'];
    } elseif ($lowStock > 0) {
        $theme = ['bg' => 'bg-[#FFFBEB] dark:bg-amber-950/10', 'border' => 'border-amber-200 dark:border-amber-900/30', 'dot' => 'bg-[#F59E0B]', 'text' => 'text-[#92400E] dark:text-amber-300', 'status' => 'WARNING', 'msg' => $lowStock . ' low stock'];
    } else {
        $theme = ['bg' => 'bg-emerald-50 dark:bg-emerald-950/10', 'border' => 'border-emerald-200 dark:border-emerald-900/30', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-800 dark:text-emerald-300', 'status' => 'SAFE', 'msg' => 'All systems stable'];
    }
    @endphp

    {{-- Banner Status Real-time --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border {{ $theme['border'] }} {{ $theme['bg'] }} px-3 py-2.5 shadow-sm">
        <span class="h-2 w-2 rounded-full {{ $theme['dot'] }} animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold {{ $theme['text'] }} leading-tight">
            <span class="uppercase font-black mr-1">{{ $theme['status'] }}:</span> {{ $theme['msg'] }}
        </p>
    </div>

    {{-- Header & Action Buttons --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight">Production Nozzle Inventory</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Production Floor Monitoring System</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <button onclick="openLineModal()" class="flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-orange-600 to-amber-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 tracking-wide uppercase">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                Add New Line
            </button>
            
            <button onclick="openModal('add')" class="flex flex-1 sm:flex-none items-center justify-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-indigo-500 px-3.5 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 tracking-wide uppercase">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" /></svg>
                Add Nozzle
            </button>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        
        <div class="px-4 border-b border-slate-100 dark:border-slate-800">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="relative w-full sm:w-60">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </span>
                    <input type="text" id="searchInput" onkeyup="applyFilterAndSearch()" placeholder="Search data..." class="w-full rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent py-2 pl-9 pr-3 text-xs md:text-[13px] text-black dark:text-white font-bold">
                </div>
            </div>

            {{-- DYNAMIC TABS: HANYA MUNCUL JIKA LINE SUDAH DIAKTIFKAN --}}
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1" id="lineTabs">
                <button onclick="filterLine(this, 'all')" class="tab-btn active px-4 py-2 rounded-t-lg text-xs font-black bg-blue-600 text-white shadow-sm whitespace-nowrap uppercase tracking-wider">
                    All Lines
                </button>
                @foreach($activeLines as $line)
                    <button onclick="filterLine(this, '{{ $line->no_line }}')" class="tab-btn px-4 py-2 rounded-t-lg text-xs font-black text-slate-600 dark:text-slate-300 bg-slate-200/80 dark:bg-slate-800 hover:bg-slate-300 border-t border-x border-slate-300 dark:border-slate-700 whitespace-nowrap uppercase tracking-wider transition-all">
                        Line {{ $line->no_line }}
                    </button>
                @endforeach
            </div>
        </div>

        <div class="w-full overflow-x-auto bg-transparent">
            <table class="w-full table-fixed text-center border-collapse min-w-[1200px]" id="nozzleTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 text-white">
                        <th class="px-2 py-3.5 w-[60px] text-center">NO</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500">Line Name</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500">Status</th>
                        <th class="px-4 py-3.5 border-l border-blue-500 w-[200px]">No Nozzle (ID)</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500">Part No</th>
                        <th class="px-3 py-3.5 w-[120px] border-l border-blue-500">Sap Code</th>
                        <th class="px-2 py-3.5 w-[120px] border-l border-blue-500">Category</th>
                        <th class="px-2 py-3.5 w-[80px] border-l border-blue-500">Qty</th>
                        <th class="px-2 py-3.5 w-[95px] border-l border-blue-500">Min Stock</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500">Create At</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500">Update At</th>
                        <th class="px-4 py-3.5 w-[120px] border-l border-blue-500">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold bg-transparent">
                    @forelse($stocks as $index => $item)
                    <tr class="row-nozzle hover:bg-slate-50/50 dark:hover:bg-slate-850/40 bg-transparent" data-line="{{ $item->line->no_line ?? '' }}">
                        <td class="px-2 py-3.5 text-center text-slate-500">
                            {{ $stocks->firstItem() + $index }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold whitespace-nowrap uppercase">
                            Line {{ $item->line->no_line ?? '-' }}
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            @php
                                $statusColor = 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]'; 
                                if($item->qty <= 0) $statusColor = 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.8)]';
                                elseif($item->qty <= $item->min_stock) $statusColor = 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)]';
                            @endphp
                            <div class="flex items-center justify-center"><div class="h-2.5 w-2.5 rounded-full {{ $statusColor }}"></div></div>
                        </td>
                        <td class="px-4 py-3.5 text-center border-l border-gray-100 dark:border-slate-800 font-extrabold break-words">
                            {{ $item->sparepart->sparepart_id ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono">
                            {{ $item->sparepart->part_number ?? '-' }}
                        </td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono text-blue-600 dark:text-blue-400 font-extrabold">
                            {{ $item->sparepart->sap_code ?? '-' }}
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <span class="bg-slate-100 dark:bg-slate-800 dark:text-slate-300 px-2 py-0.5 rounded text-[9px] font-black uppercase">
                                {{ $item->sparepart->category ?? '-' }}
                            </span>
                        </td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold text-[14px]">{{ $item->qty }}</td>
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-slate-500">{{ $item->min_stock }}</td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-slate-600 dark:text-slate-400">{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-slate-600 dark:text-slate-400">{{ $item->updated_at ? $item->updated_at->format('d/m/Y H:i') : '-' }}</td>
                        <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" data-item="{{ json_encode($item) }}" onclick="openModal('edit', this)" class="flex h-7 w-7 items-center justify-center rounded bg-yellow-400 text-white hover:bg-yellow-500 shadow-sm"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round"/></svg></button>
                                <form action="{{ route('stock.prod.destroy', $item->id) }}" method="POST" class="inline form-delete">
                                    @csrf @method('DELETE')
                                    <button type="button" class="flex h-7 w-7 items-center justify-center rounded bg-red-500 text-white btn-delete hover:bg-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round"/></svg></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="12" class="py-10 text-center text-slate-400 italic font-medium text-[13px]">No entries found. Klik "Add New Line" untuk mengaktifkan line ke halaman utama.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between bg-white dark:bg-slate-900 px-4 py-4">
            <p class="text-[11px] font-black text-black dark:text-slate-400 uppercase tracking-wide">Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} Entries</p>
            <div class="flex items-center justify-center text-xs text-black dark:text-white">{{ $stocks->links() }}</div>
        </div>
    </div>
</div>

{{-- MODAL NOZZLE ALLOCATION --}}
<div id="modalNozzle" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-xl shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden text-black">
        <div class="px-6 py-4 bg-blue-600 text-white flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-extrabold text-white">Add Nozzle Allocation</h3>
            <button onclick="closeModal()" class="text-white/80 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="nozzleForm" method="POST" onsubmit="return validateDuplicateStock(this)" class="p-6 space-y-4">
            @csrf
            <div id="methodField"></div>
            <input type="hidden" id="current_item_id" value="">
            <div class="grid grid-cols-2 gap-4">
                
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase">Pilih Line Production</label>
                    <select name="line_id" id="line_id" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm text-black dark:text-white font-bold uppercase" required>
                        <option value="">-- Pilih Line --</option>
                        @foreach($allLines as $line)
                            <option value="{{ $line->id }}">LINE {{ $line->no_line }} ({{ $line->name_machine }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-1 block uppercase">Part No</label>
                    <select name="sparepart_id" id="sparepart_id" onchange="autoFillByPart(this)" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm text-black dark:text-white font-mono font-bold" required>
                        <option value="">-- Pilih Part Number --</option>
                        @foreach($ListSparepartEng as $sp)
                            <option value="{{ $sp->id }}" data-name="{{ $sp->sparepart_id ?? '' }}" data-sap="{{ $sp->sap_code ?? '' }}" data-category="{{ $sp->category ?? '' }}">
                                {{ $sp->part_number ?? 'No Part Num' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">No Nozzle</label>
                    <input type="text" id="no_nozzle" class="w-full rounded-lg border border-gray-300 bg-slate-50 dark:bg-slate-800 p-2.5 text-sm text-black dark:text-white font-bold" readonly>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Sap Code</label>
                    <input type="text" id="sap_code" class="w-full rounded-lg border border-gray-300 bg-slate-50 dark:bg-slate-800 p-2.5 text-sm font-mono text-black dark:text-white font-bold" readonly>
                </div>

                <div class="col-span-2 md:col-span-1">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Category</label>
                    <input type="text" id="category" class="w-full rounded-lg border border-gray-300 bg-slate-50 dark:bg-slate-800 p-2.5 text-sm text-black dark:text-white font-bold" readonly>
                </div>

                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Qty</label>
                    <input type="number" name="qty" id="qty" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 p-2.5 text-sm text-black dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase">Min Stock</label>
                    <input type="number" name="min_stock" id="min_stock" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 p-2.5 text-sm text-black dark:text-white font-bold" required>
                </div>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-gradient-to-r from-blue-600 to-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md hover:opacity-90">Save Data</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL LINE MANAGE: (FIXED !! HANYA 1 DROPDOWN SELEKSI LINE YANG BELUM AKTIF) --}}
<div id="modalLine" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4">
    <div class="bg-white dark:bg-slate-900 rounded-2xl w-full max-w-md shadow-2xl border border-slate-200 dark:border-slate-800 overflow-hidden text-black transform scale-100 transition-all">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 flex justify-between items-center bg-blue-600 text-white">
            <h3 class="text-lg font-bold tracking-tight text-white">Activate Production Line</h3>
            <button onclick="closeLineModal()" class="text-white/80 hover:text-white transition-colors"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        
        <form action="{{ route('stock.prod.line.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 block uppercase tracking-wide">Pilih Nomor Line Yang Tersedia</label>
                {{-- DROPDOWN BERISI MASTER LINE YANG BELUM AKTIF --}}
                <select name="line_id" class="w-full rounded-lg border border-gray-300 dark:bg-slate-800 dark:border-slate-700 p-2.5 text-sm outline-none focus:border-blue-500 text-black dark:text-white font-bold" required>
                    <option value="">-- Pilih Line Terdaftar --</option>
                    @foreach($availableLines as $aLine)
                        <option value="{{ $aLine->id }}">LINE {{ $aLine->no_line }} ({{ $aLine->name_machine }})</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="w-full bg-gradient-to-r from-blue-600 to-indigo-500 text-white py-2.5 rounded-lg text-xs font-bold shadow-md hover:opacity-90 transition-all tracking-wide uppercase mt-2">
                + Add Line to View
            </button>
        </form>

        <div class="p-6 border-t border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900">
            <label class="text-xs font-bold text-slate-500 dark:text-slate-400 mb-2 block uppercase tracking-wide">Lines Currently Active</label>
            <div class="max-h-32 overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800 pr-1 scrollbar-thin">
                @foreach($activeLines as $actLine)
                    <div class="flex items-center justify-between py-2">
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase">● LINE {{ $actLine->no_line }} ({{ $actLine->name_machine }})</span>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-slate-50 dark:bg-slate-900 px-6 py-3 flex justify-end border-t border-slate-100 dark:border-slate-800">
            <button type="button" onclick="closeLineModal()" class="px-4 py-2 text-xs font-bold bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-200 rounded-lg">Close</button>
        </div>
    </div>
</div>

<script>
    const currentInventoryData = @json($stocks->items());
    let currentLineFilter = 'all';

    function filterLine(element, lineName) {
        currentLineFilter = lineName;
        document.querySelectorAll(".tab-btn").forEach(btn => {
            btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-slate-200/80', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
        });
        element.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
        element.classList.remove('bg-slate-200/80', 'dark:bg-slate-800', 'text-slate-600', 'dark:text-slate-300');
        applyFilterAndSearch();
    }

    function applyFilterAndSearch() {
        let searchInput = document.getElementById("searchInput").value.toUpperCase();
        document.querySelectorAll(".row-nozzle").forEach(row => {
            let rowLine = row.getAttribute('data-line');
            let textMatches = row.innerText.toUpperCase().includes(searchInput);
            let lineMatches = (currentLineFilter === 'all' || String(rowLine) === String(currentLineFilter));
            row.style.display = (textMatches && lineMatches) ? "" : "none";
        });
    }

    function autoFillByPart(selectElement) {
        const opt = selectElement.options[selectElement.selectedIndex];
        document.getElementById('no_nozzle').value = opt.getAttribute('data-name') || '';
        document.getElementById('sap_code').value = opt.getAttribute('data-sap') || '';
        document.getElementById('category').value = opt.getAttribute('data-category') || '';
    }

    function openModal(mode, element = null) {
        const modal = document.getElementById('modalNozzle');
        const form = document.getElementById('nozzleForm');
        modal.classList.remove('hidden');
        
        if (mode === 'edit' && element) {
            const data = JSON.parse(element.getAttribute('data-item'));
            document.getElementById('modalTitle').innerText = 'Edit Line Nozzle Data';
            form.action = "{{ route('stock.prod.update', ':id') }}".replace(':id', data.id);
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('current_item_id').value = data.id;
            document.getElementById('line_id').value = data.line_id;
            
            const spSelect = document.getElementById('sparepart_id');
            spSelect.value = data.sparepart_id;
            autoFillByPart(spSelect);

            document.getElementById('qty').value = data.qty;
            document.getElementById('min_stock').value = data.min_stock;
        } else {
            document.getElementById('modalTitle').innerText = 'Add New Nozzle Allocation';
            form.action = "{{ route('stock.prod.nozzle.store') }}"; 
            form.reset();
            document.getElementById('methodField').innerHTML = '';
            document.getElementById('current_item_id').value = '';
        }
    }

    function closeModal() { document.getElementById('modalNozzle').classList.add('hidden'); }
    function openLineModal() { document.getElementById('modalLine').classList.remove('hidden'); }
    function closeLineModal() { document.getElementById('modalLine').classList.add('hidden'); }

    function validateDuplicateStock(formElement) {
        const lineVal = document.getElementById('line_id').value;
        const spVal = document.getElementById('sparepart_id').value;
        const currentId = document.getElementById('current_item_id').value;

        const isDuplicate = currentInventoryData.some(item => {
            if (currentId && String(item.id) === String(currentId)) return false;
            return String(item.line_id) === String(lineVal) && String(item.sparepart_id) === String(spVal);
        });

        if (isDuplicate) {
            Swal.fire({ icon: 'error', title: 'Duplicate Entry!', text: 'Gagal! Nozzle ini sudah terdaftar di line tersebut.', confirmButtonColor: '#3b82f6' });
            return false;
        }
        return true;
    }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function() {
            let form = this.closest('.form-delete');
            Swal.fire({ title: 'Are you sure?', text: "Delete this entry?", icon: 'warning', showCancelButton: true, confirmButtonColor: '#ef4444', confirmButtonText: 'Yes, delete it!' }).then((result) => {
                if (result.isConfirmed) form.submit();
            });
        });
    });

    @if(session('success')) Swal.fire({ icon: 'success', title: 'Success!', text: "{{ session('success') }}", timer: 3000 }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'Error!', text: "{{ session('error') }}" }); @endif
    @if($errors->any()) Swal.fire({ icon: 'error', title: 'Error', text: "{{ $errors->first() }}" }); @endif
</script>
@endsection