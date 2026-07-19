@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght=0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="font-nunito mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">

    {{-- COUNTER BANNER WARNING ALERT --}}
    @php
    $outOfStock = 0;
    $lowStock = 0;

    foreach($lines as $item) {
        if ($item->stocks) {
            if ($item->stocks->qty <= 0 && $item->stocks->no_nozzle != null) {
                $outOfStock++;
            } elseif ($item->stocks->qty <= $item->stocks->min_stock && $item->stocks->no_nozzle != null) {
                $lowStock++;
            }
        }
    }

    if ($outOfStock > 0) {
        $theme = [
            'bg' => 'bg-red-50', 'border' => 'border-red-200', 'dot' => 'bg-red-600', 'text' => 'text-red-800',
            'status' => 'ALERT', 'msg' => $outOfStock . ' lines require active nozzle transaction'
        ];
    } elseif ($lowStock > 0) {
        $theme = [
            'bg' => 'bg-[#FFFBEB]', 'border' => 'border-amber-200', 'dot' => 'bg-[#F59E0B]', 'text' => 'text-[#92400E]',
            'status' => 'WARNING', 'msg' => $lowStock . ' lines running low on nozzle stock'
        ];
    } else {
        $theme = [
            'bg' => 'bg-emerald-50', 'border' => 'border-emerald-200', 'dot' => 'bg-emerald-500', 'text' => 'text-emerald-800',
            'status' => 'SAFE', 'msg' => 'All monitored production lines stable'
        ];
    }
    @endphp

    <div class="mb-6 flex items-center gap-3 rounded-2xl border {{ $theme['border'] }} {{ $theme['bg'] }} px-5 py-3 shadow-sm transition-all">
        <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $theme['dot'] }} animate-pulse"></span>
        <p class="text-sm font-semibold {{ $theme['text'] }}">
            <span class="uppercase font-extrabold mr-1">{{ $theme['status'] }}:</span> 
            {{ $theme['msg'] }}
        </p>
    </div>

    {{-- HEADER ACTION --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-800 dark:text-white tracking-tight uppercase">Production Floor Inventory</h2>
            <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Live Machine Line & Nozzle Infrastructure Tracking</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2">
            <button onclick="openActionModal('line')" class="flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-indigo-700 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                ADD LINE
            </button>

            <button onclick="openActionModal('nozzle')" class="flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:bg-blue-700 transition-all active:scale-95 tracking-wide">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M21 12H3m9-9v18" /></svg>
                ADD NOZZLE
            </button>
        </div>
    </div>

    {{-- DATA BARANG DAN SEARCHING --}}
    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm dark:border-slate-700 dark:bg-boxdark overflow-hidden">
        <div class="p-5 border-b border-slate-100 dark:border-slate-700">
            <div class="relative mb-6 w-full max-w-md">
                <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="Search machine, nozzle name, sap code..." class="w-full rounded-xl border border-slate-200 bg-slate-50/50 dark:bg-slate-800 dark:text-white py-2.5 pl-10 pr-4 text-sm outline-none focus:border-indigo-500 font-medium">
            </div>

            {{-- MENU TABS PER LINE --}}
            <div class="flex items-center gap-2 overflow-x-auto scrollbar-hide border-b border-slate-100 dark:border-slate-700 pb-1" id="lineTabs">
                <button onclick="filterLine('all')" class="tab-btn active px-4 py-2 rounded-t-lg text-xs font-bold transition-all bg-indigo-600 text-white shadow-sm whitespace-nowrap uppercase">
                    All Factory Lines
                </button>
                
                @foreach($lines as $item)
                    @php $displayLineName = $item->no_line ?? $item->name_machine ?? 'LINE-'.$item->line_id; @endphp
                    <button onclick="filterLine('{{ $displayLineName }}')" class="tab-btn px-4 py-2 rounded-t-lg text-xs font-bold text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 whitespace-nowrap uppercase">
                        {{ $displayLineName }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- TABLE MONITORING VIEW --}}
        <div class="max-w-full overflow-x-auto scrollbar-hide">
            <table class="w-full text-left border-collapse" id="prodTable">
                <thead>
                    <tr class="text-[10px] font-extrabold text-slate-800 dark:text-slate-200 uppercase tracking-widest bg-slate-50/50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-700">
                        <th class="px-4 py-4 text-center w-12">NO</th>
                        <th class="px-4 py-4 text-center">Line Name</th>
                        <th class="px-4 py-4 text-center">Status</th>
                        <th class="px-4 py-4 text-center">No Nozzle</th>
                        <th class="px-4 py-4 text-center">Part No</th>
                        <th class="px-4 py-4 text-center">Sap Code</th>
                        <th class="px-4 py-4 text-center">Category</th>
                        <th class="px-4 py-4 text-center w-16">Qty</th>
                        <th class="px-4 py-4 text-center w-24">Min Stock</th>
                        <th class="px-4 py-4 text-center w-28">Update At</th>
                        <th class="px-6 py-4 text-center w-28">Action</th>
                    </tr>
                </thead>
                <tbody class="text-xs font-semibold text-slate-900 dark:text-white divide-y divide-slate-50 dark:divide-slate-700">
                    @php $insertedCount = 0; @endphp
                    
                    @foreach($lines as $index => $item)
                        @php 
                            $insertedCount++;
                            $displayLineName = $item->no_line ?? $item->name_machine ?? 'LINE-'.$item->line_id;
                            $stock = $item->stocks;
                        @endphp
                        <tr class="row-prod hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-all" data-line="{{ $displayLineName }}">
                            <td class="px-4 py-4 text-center text-slate-500 font-bold">{{ $insertedCount }}</td>
                            <td class="px-4 py-4 text-center font-extrabold text-sm text-slate-800 dark:text-white uppercase tracking-tight">{{ $displayLineName }}</td>
                            
                            <td class="px-4 py-4 text-center">
                                <div class="flex items-center justify-center">
                                    @if(!$stock || !$stock->no_nozzle || $stock->no_nozzle === 'N/A' || $stock->no_nozzle === '-')
                                        <div class="h-2.5 w-2.5 rounded-full bg-slate-300 shadow-[0_0_8px_rgba(203,213,225,0.8)]" title="Belum Dikonfigurasi"></div>
                                    @elseif($stock->qty <= 0)
                                        <div class="h-2.5 w-2.5 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-pulse" title="Stok Habis (0)"></div>
                                    @elseif($stock->qty <= $stock->min_stock)
                                        <div class="h-2.5 w-2.5 rounded-full bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.8)]" title="Stok Menipis"></div>
                                    @else
                                        <div class="h-2.5 w-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)]" title="Stok Aman"></div>
                                    @endif
                                </div>
                            </td>
                            
                            <td class="px-4 py-4 text-center font-bold text-slate-800 dark:text-white">{{ ($stock && $stock->no_nozzle) ? $stock->no_nozzle : '-' }}</td>
                            <td class="px-4 py-4 text-center font-bold font-mono tracking-wide text-slate-600 dark:text-slate-400">{{ ($stock && $stock->part_no) ? $stock->part_no : '-' }}</td>
                            <td class="px-4 py-4 text-center font-bold font-mono tracking-wide text-indigo-600 dark:text-indigo-400">{{ ($stock && $stock->sap_code) ? $stock->sap_code : '-' }}</td>
                            <td class="px-4 py-4 text-center font-bold text-slate-600 dark:text-slate-400 uppercase">{{ ($stock && $stock->category) ? $stock->category : '-' }}</td>
                            <td class="px-4 py-4 text-center font-extrabold text-slate-900 dark:text-white">{{ $stock->qty ?? 0 }}</td>
                            <td class="px-4 py-4 text-center font-extrabold text-slate-500 dark:text-slate-400">{{ $stock->min_stock ?? 0 }}</td>
                            
                            <td class="px-4 py-4 whitespace-nowrap font-bold text-[11px] text-slate-600 dark:text-slate-300 leading-normal text-center">
                                @if($stock && $stock->updated_at && $stock->no_nozzle)
                                    {{ \Carbon\Carbon::parse($stock->updated_at)->format('d/m/y') }}
                                    <br><span class="text-[9px] text-slate-400 font-medium">{{ \Carbon\Carbon::parse($stock->updated_at)->format('H:i') }} WIB</span>
                                @else
                                    -
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($stock && $stock->no_nozzle && $stock->no_nozzle !== 'N/A' && $stock->no_nozzle !== '-')
                                        <button onclick="openModal('edit', {{ json_encode($stock) }}, '{{ $displayLineName }}')" class="flex h-8 w-8 items-center justify-center rounded-lg bg-yellow-400 text-white transition-all hover:bg-yellow-500 active:scale-90 shadow-sm"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                        <form action="{{ route('stock.prod.destroy', $stock->id) }}" method="POST" class="inline form-delete">
                                            @csrf @method('DELETE')
                                            <button type="button" class="flex h-8 w-8 items-center justify-center rounded-lg bg-red-500 text-white btn-delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                                        </form>
                                    @else
                                        <span class="text-slate-400 italic text-[11px]">Ready to Add</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL INPUT DATA COMBINED --}}
<div id="modalAction" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 id="modalActionTitle" class="text-lg font-bold text-slate-800 dark:text-white uppercase tracking-tight">Add Infrastructure</h3>
            <button onclick="closeActionModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        
        <form action="{{ route('stock.prod.nozzleStore') }}" method="POST" id="actionForm" class="p-6">
            @csrf
            <input type="hidden" name="action_type" id="action_type" value="nozzle">
            
            <div class="grid grid-cols-2 gap-4">
                
                {{-- ELEMENT DROPDOWN ADD LINE --}}
                <div id="container_add_line" class="col-span-2 hidden">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Register Target Production Line</label>
                    <select name="register_line_id" id="register_line_id" class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold uppercase">
                        <option value="" disabled selected>-- SELECT FACTORY LINE SYSTEM --</option>
                        @foreach($lines as $item)
                            @if(!$item->stocks)
                                <option value="{{ $item->line_id }}">{{ $item->no_line ?? $item->name_machine }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>

                {{-- ELEMENT DROPDOWN ADD NOZZLE --}}
                <div id="container_add_nozzle" class="col-span-2 flex flex-col gap-4">
                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Target Registered Line</label>
                        <select name="line_id" id="action_line_id" class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold uppercase" required>
                            <option value="" disabled selected>-- CHOOSE ACTIVE LINE --</option>
                            @foreach($lines as $item)
                                <option value="{{ $item->line_id }}">{{ $item->no_line ?? $item->name_machine }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Select Nozzle Item (From Engineering)</label>
                        <select name="stock_eng_id" id="action_log_id" onchange="autoFillNozzleSpecs(this)" class="w-full rounded-lg border border-slate-200 bg-white dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-mono text-xs font-bold" required>
                            <option value="" disabled selected>-- SELECT NOZZLE FROM ENGINEERING --</option>
                            @isset($stockEngs)
                                @foreach($stockEngs as $eng)
                                    @php 
                                        $name     = $eng->sparepart->name ?? 'N/A';
                                        $partNo   = $eng->sparepart->part_number ?? ($eng->sparepart->part_no ?? ($eng->part_no ?? ($eng->part_number ?? 'N/A')));
                                        $sapCode  = $eng->sparepart->sap_code ?? ($eng->sap_code ?? 'N/A');
                                    @endphp
                                    <option value="{{ $eng->id }}" data-part="{{ $partNo }}" data-sap="{{ $sapCode }}">
                                        Nozzle: {{ $name }} | Stock Eng: {{ $eng->qty ?? 0 }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    {{-- REALTIME AUTOFILL PREVIEW --}}
                    <div class="grid grid-cols-2 gap-4 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700/80">
                        <div class="col-span-2 md:col-span-1">
                            <label class="text-xs font-bold text-slate-400 mb-1 block uppercase tracking-wide">Part Number (Auto)</label>
                            <input type="text" id="action_display_part_no" class="w-full rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-700 p-2.5 text-sm outline-none dark:text-white font-mono font-bold text-slate-500" placeholder="Waiting selection..." readonly>
                        </div>
                        <div class="col-span-2 md:col-span-1">
                            <label class="text-xs font-bold text-slate-400 mb-1 block uppercase tracking-wide">SAP Code (Auto)</label>
                            <input type="text" id="action_display_sap_code" class="w-full rounded-lg border border-slate-200 bg-slate-100 dark:bg-slate-700 p-2.5 text-sm outline-none dark:text-white font-mono font-bold text-slate-500" placeholder="Waiting selection..." readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Quantity Allocation</label>
                            <input type="number" name="qty" id="action_qty" min="0" value="0" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Minimum Stock Alert</label>
                            <input type="number" name="min_stock" id="action_min_stock" min="0" value="0" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                        </div>
                    </div>
                </div>

            </div>
            
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeActionModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide uppercase">Confirm Submission</button>
            </div>
        </form>
    </div>
</div>

{{-- MODAL EDIT --}}
<div id="modalProd" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm px-4 font-nunito">
    <div class="bg-white dark:bg-boxdark rounded-2xl w-full max-w-xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
        <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
            <h3 id="modalTitle" class="text-lg font-bold text-slate-800 dark:text-white">Edit Production Stock</h3>
            <button onclick="closeModal()" class="text-slate-400 hover:text-slate-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2" stroke-linecap="round"/></svg></button>
        </div>
        <form id="prodForm" method="POST" class="p-6">
            @csrf
            <div id="methodField"></div>
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Line Name</label>
                    <input type="text" id="no_line" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm outline-none dark:text-white font-bold bg-slate-100 dark:bg-slate-700 uppercase" readonly>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">No Nozzle</label>
                    <input type="text" id="no_nozzle" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm outline-none dark:text-white font-bold bg-slate-100 dark:bg-slate-700" readonly>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Part No</label>
                    <input type="text" id="part_no" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm outline-none dark:text-white font-mono font-bold bg-slate-100 dark:bg-slate-700" readonly>
                </div>
                <div class="col-span-2">
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Sap Code</label>
                    <input type="text" id="sap_code" class="w-full rounded-lg border border-slate-200 p-2.5 text-sm outline-none dark:text-white font-mono font-bold bg-slate-100 dark:bg-slate-700" readonly>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Qty</label>
                    <input type="number" name="qty" id="qty" min="0" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>
                <div>
                    <label class="text-xs font-bold text-slate-500 mb-1 block uppercase tracking-wide">Min Stock</label>
                    <input type="number" name="min_stock" id="min_stock" min="0" class="w-full rounded-lg border border-slate-200 dark:bg-slate-800 dark:border-slate-600 p-2.5 text-sm outline-none focus:border-indigo-500 dark:text-white font-bold" required>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-3">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-sm font-bold text-slate-500">Cancel</button>
                <button type="submit" class="bg-indigo-600 text-white px-6 py-2 rounded-lg text-sm font-bold shadow-lg hover:bg-indigo-700 transition-all tracking-wide">Save Data</button>
            </div>
        </form>
    </div>
</div>

<script>
    function autoFillNozzleSpecs(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        document.getElementById('action_display_part_no').value = selectedOption.getAttribute('data-part') || '-';
        document.getElementById('action_display_sap_code').value = selectedOption.getAttribute('data-sap') || '-';
    }

    function filterLine(lineName) {
        let btns = document.querySelectorAll(".tab-btn");
        btns.forEach(btn => {
            btn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm', 'active');
            btn.classList.add('bg-slate-100', 'dark:bg-slate-800', 'text-slate-500', 'dark:text-slate-400', 'border-slate-200');
        });
        
        event.currentTarget.classList.remove('bg-slate-100', 'text-slate-500');
        event.currentTarget.classList.add('bg-indigo-600', 'text-white', 'shadow-sm', 'active');
        
        document.querySelectorAll(".row-prod").forEach(row => {
            row.style.display = (lineName === 'all' || row.getAttribute('data-line') === lineName) ? "" : "none";
        });
    }

    function searchTable() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        document.querySelectorAll(".row-prod").forEach(row => {
            row.style.display = row.innerText.toUpperCase().includes(input) ? "" : "none";
        });
    }

    function openActionModal(type) {
        const modal = document.getElementById('modalAction');
        const title = document.getElementById('modalActionTitle');
        const typeField = document.getElementById('action_type');
        modal.classList.remove('hidden');
        typeField.value = type;

        if(type === 'line') {
            title.innerText = "Register New Production Line";
            document.getElementById('container_add_line').classList.remove('hidden');
            document.getElementById('container_add_nozzle').classList.add('hidden');
            document.getElementById('register_line_id').setAttribute('required', 'required');
        } else {
            title.innerText = "Allocate Nozzle Stock to Line";
            document.getElementById('container_add_line').classList.add('hidden');
            document.getElementById('container_add_nozzle').classList.remove('hidden');
            document.getElementById('register_line_id').removeAttribute('required');
            document.getElementById('action_line_id').value = "";
        }
    }

    function closeActionModal() {
        document.getElementById('modalAction').classList.add('hidden');
        document.getElementById('actionForm').reset();
    }

    document.getElementById('actionForm').addEventListener('reset', function() {
        document.getElementById('action_display_part_no').value = '';
        document.getElementById('action_display_sap_code').value = '';
    });

    function openModal(mode, data = null, lineName = '') {
        const modal = document.getElementById('modalProd');
        modal.classList.remove('hidden');
        if (mode === 'edit') {
            document.getElementById('prodForm').action = "/prod/stock/" + data.id; 
            document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
            document.getElementById('no_line').value = lineName;
            document.getElementById('no_nozzle').value = data.no_nozzle || '';
            document.getElementById('part_no').value = data.part_no || '';
            document.getElementById('sap_code').value = data.sap_code || '';
            document.getElementById('qty').value = data.qty;
            document.getElementById('min_stock').value = data.min_stock;
        }
    }

    function closeModal() { document.getElementById('modalProd').classList.add('hidden'); }

    document.querySelectorAll('.btn-delete').forEach(button => {
        button.addEventListener('click', function(e) {
            let form = this.closest('.form-delete');
            Swal.fire({
                title: 'Hapus Alokasi Nozzle?',
                text: "Data nozzle pada lini ini akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                customClass: { container: '!z-[10000]' }
            }).then((result) => { if (result.isConfirmed) form.submit(); });
        });
    });

    document.getElementById('actionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let form = this;
        Swal.fire({
            title: 'Validasi Data Sistem',
            text: "Apakah data submission Anda sudah benar?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Ya, Kirim!',
            customClass: { container: '!z-[10000]' }
        }).then((result) => { if (result.isConfirmed) form.submit(); });
    });

    @if(session('success')) Swal.fire({ icon: 'success', title: 'Berhasil!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false, customClass: { container: '!z-[10000]' } }); @endif
    @if($errors->any()) Swal.fire({ icon: 'error', title: 'Oops...', text: "{{ $errors->first() }}", customClass: { container: '!z-[10000]' } }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'Error', text: "{{ session('error') }}", customClass: { container: '!z-[10000]' } }); @endif
</script>

<style>
    .font-nunito { font-family: 'Nunito', sans-serif !important; }
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    #prodTable th, #prodTable td { vertical-align: middle !important; text-align: center !important; }
</style>
@endsection