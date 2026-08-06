@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .font-nunito, body, select, input, button, table, th, td, .swal2-popup, .swal2-title, .swal2-html-container { 
        font-family: 'Nunito', sans-serif !important; 
    }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; width: 6px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
    
    #barcodeTable td { color: #000000 !important; vertical-align: middle !important; }
    .dark #barcodeTable td { color: #cbd5e1 !important; }
    #barcodeTable th { vertical-align: middle !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Info Banner --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 dark:bg-amber-950/30 dark:border-amber-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-amber-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-amber-800 dark:text-amber-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">BARCODE GENERATOR ACTIVE:</span> 
            Automated serialization registration system for inbound engineering items.
        </p>
    </div>

    {{-- Header Title --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight uppercase">Barcode Batch Inbound</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">Register new incoming items to engineering stock racks with sequential barcodes</p>
        </div>

        <div class="flex items-center gap-2 w-full sm:w-auto">
            <a href="{{ route('barcode.parsing.in') }}" class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-orange-600 via-orange-500 to-amber-500 px-3 text-[11px] font-bold text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-28 text-center cursor-pointer no-underline">
                Barcode IN
            </a>
            <a href="{{ route('barcode.parsing') }}" class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3 text-[11px] font-bold text-white shadow-md tracking-wider uppercase font-nunito w-full sm:w-28 text-center dark:ring-offset-slate-950">
                Barcode OUT
            </a>
        </div>
    </div>

    <!-- MAIN CONFIGURATOR CONTAINER -->
    <div class="w-full rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-sm mb-6">
        <form id="automatedBarcodeForm">
            <input type="hidden" id="current_mode" name="mode" value="IN">
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- SEKSI KIRI: DROP DOWN CONTROLS -->
                <div class="lg:col-span-7 grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-7">
                    
                    <!-- 1. Material Received Dropdown -->
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">
                            <svg class="w-[18px] h-[18px] text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            1. Material Received Doc
                        </label>
                        <select id="source_id" name="source_id" onchange="handleDocumentSelection()" class="w-full h-9 bg-slate-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs font-bold text-black dark:text-white outline-none focus:border-orange-500 transition-all">
                            <option value="" disabled selected>-- Select Material Received --</option>
                            @foreach($materialReceived as $mr)
                                @php
                                    $spCode = $mr->custom_sparepart_code ?? $mr->purchase_sparepart_id ?? '-';
                                    $spPartNum = $mr->part_number ?? '-';
                                    $spSapCode = $mr->sap_code ?? '-';
                                @endphp
                                <option value="{{ $mr->id }}" 
                                        data-qty="{{ $mr->qty_received }}"
                                        data-partnum="{{ $spPartNum }}"
                                        data-sap="{{ $spSapCode }}"
                                        data-sparepart-id="{{ $spCode }}">
                                    {{ $mr->no_mr ?? 'MR-Doc #'.$mr->id }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- 2. Target Stock & Rak Locations Dropdown -->
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">
                            <svg class="w-[18px] h-[18px] text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/></svg>
                            2. Destination Target Rak
                        </label>
                        <select id="stock_eng_id" name="stock_eng_id" onchange="refreshInboundCounter()" class="w-full h-9 bg-slate-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs font-bold text-black dark:text-white outline-none focus:border-orange-500 transition-all">
                            <option value="" disabled selected>-- Select Destination Rak --</option>
                            @foreach($stockEngineering as $stock)
                                @php
                                    $stockSpCode = $stock->part_name ?? $stock->sparepart_id ?? '-';
                                @endphp
                                <option value="{{ $stock->stock_id }}" 
                                        data-sparepart-id="{{ $stockSpCode }}"
                                        data-partname="{{ $stock->part_name }}" 
                                        data-partnum="{{ $stock->part_number }}" 
                                        data-sap="{{ $stock->sap_code }}"
                                        data-rakname="{{ trim(str_replace(['[', ']'], '', $stock->rak_name ?? '')) }}"
                                        hidden disabled>
                                    {{ trim(str_replace(['[', ']'], '', $stock->rak_name ?? '')) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="block text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">3. Barcode Type</label>
                        <select id="barcode_type" name="barcode_type" onchange="calculateBatchPreview()" class="w-full h-9 bg-slate-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs font-bold text-black dark:text-white outline-none focus:border-orange-500 transition-all">
                            <option value="QR CODE" selected>QR CODE</option>
                        </select>
                    </div>

                    {{-- Size Label tidak digunakan untuk Barcode IN. --}}
                    {{--
                    <!-- 3. Barcode Type Dropdown -->
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">
                            <svg class="w-[18px] h-[18px] text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 19.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5zM17 14v7m-3-3h6"/></svg>
                            3. Barcode Type
                        </label>
                        <select id="barcode_type" name="barcode_type" onchange="calculateBatchPreview()" class="w-full h-9 bg-slate-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs font-bold text-black dark:text-white outline-none focus:border-orange-500 transition-all">
                            <option value="DATA MATRIX">DATA MATRIX</option>
                            <option value="QR CODE" selected>QR CODE</option>
                        </select>
                    </div>

                    <!-- 4. Size Label Dropdown -->
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-[11px] font-black uppercase text-slate-500 dark:text-slate-400">
                            <svg class="w-[18px] h-[18px] text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3.75v16.5m16.5-16.5v16.5M3.75 12h16.5M6 9h3M6 15h3m9-6h-3m3 6h-3"/></svg>
                            4. Size Label
                        </label>
                        <select id="barcode_size" name="barcode_size" class="w-full h-9 bg-slate-50 dark:bg-slate-800 border border-gray-300 dark:border-slate-700 rounded-lg px-2.5 py-1 text-xs font-bold text-black dark:text-white outline-none focus:border-orange-500 transition-all">
                            <option value="10" selected>10mm x 10mm</option>
                            <option value="15">15mm x 15mm</option>
                            <option value="20">20mm x 20mm</option>
                        </select>
                    </div>
                    --}}
                </div>

                <!-- SEKSI KANAN: PREVIEW DOKUMEN & ACTION -->
                <div class="lg:col-span-5 bg-slate-50 dark:bg-slate-800/40 p-4 rounded-xl border border-gray-200 dark:border-slate-800 space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase text-slate-400">Sparepart ID</label>
                            <input type="text" id="display_sparepart_id" readonly class="w-full h-9 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-black text-center text-blue-600 dark:text-blue-400 outline-none shadow-sm" placeholder="-">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase text-slate-400">Part Number</label>
                            <input type="text" id="display_part_num" readonly class="w-full h-9 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-black text-center text-slate-700 dark:text-slate-300 outline-none shadow-sm" placeholder="-">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase text-slate-400">SAP Code</label>
                            <input type="text" id="display_sap" readonly class="w-full h-9 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-black text-center text-orange-600 dark:text-orange-400 outline-none shadow-sm" placeholder="-">
                        </div>
                        <div class="space-y-1">
                            <label class="block text-[10px] font-black uppercase text-slate-400">Qty Inbound</label>
                            <input type="text" id="display_qty" readonly class="w-full h-9 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg text-xs font-black text-center text-slate-700 dark:text-slate-300 outline-none shadow-sm" placeholder="-">
                        </div>
                    </div>

                    <div class="text-[11px] font-bold text-slate-500 dark:text-slate-400 border-t border-dashed border-gray-200 dark:border-slate-700 pt-3">
                        <span class="block text-[9px] uppercase font-black text-slate-400">Pattern Combination Rule:</span>
                        <code id="stats_pattern_rule" class="text-xs text-amber-600 dark:text-amber-400 font-mono font-black break-all">Standby...</code>
                    </div>

                    <div class="flex gap-2 justify-end pt-1">
                        <button type="button" onclick="resetForm()" class="h-9 px-3.5 rounded-lg bg-white dark:bg-slate-800 hover:bg-slate-100 text-slate-700 dark:text-white text-xs font-black uppercase tracking-wider transition-all border border-gray-300 dark:border-slate-600 shadow-sm flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                            Reset
                        </button>
                        <button type="button" onclick="executeBatchGeneration()" class="h-9 px-4 rounded-lg bg-gradient-to-r from-orange-600 to-amber-500 hover:opacity-90 text-white text-xs font-black uppercase tracking-wider transition-all shadow-md active:scale-95 flex items-center gap-1.5 border-none">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 19.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/></svg>
                            Generate In
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- PREVIEW GRID TABLE -->
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
            <div>
                <span id="preview_count_badge" class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[11px] font-black bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300 border border-gray-200 dark:border-slate-700 uppercase">
                    0 Items Pending Registration
                </span>
            </div>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1200px]" id="barcodeTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-orange-600 dark:bg-orange-950/80 text-white dark:text-orange-200 font-nunito">
                        <th class="px-2 py-3.5 w-[50px] text-center">NO</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-orange-500 dark:border-orange-900/50 text-center">Barcode Img</th>
                        <th class="px-4 py-3.5 border-l border-orange-500 dark:border-orange-900/50 text-left">Generated Barcode String (Inbound Format)</th>
                        <th class="px-3 py-3.5 w-[110px] border-l border-orange-500 dark:border-orange-900/50">Sparepart ID</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 dark:border-orange-900/50">Part Number</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 dark:border-orange-900/50">SAP Code</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-orange-500 dark:border-orange-900/50">Destination Rak</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-orange-500 dark:border-orange-900/50">Barcode Type</th>
                        <th class="px-3 py-3.5 w-[150px] border-l border-orange-500 dark:border-orange-900/50 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="batch_table_body" class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
                    <tr>
                        <td colspan="9" class="py-12 text-center text-slate-400 italic font-medium text-[13px] font-nunito">
                            Pilih dokumen Material Received dan target Rak lokasi di atas untuk memuat daftar preview serialisasi masuk.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p id="showing_entries_text" class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase font-nunito text-center sm:text-left">
                Showing 0 to 0 of 0 Entries
            </p>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bwip-js@3.0.4/dist/bwip-js-min.js"></script>

<script>
    function handleDocumentSelection() {
        const docSelect = document.getElementById('source_id');
        const selectedOpt = docSelect.options[docSelect.selectedIndex];

        if (!selectedOpt || !selectedOpt.value) return;

        const qty = selectedOpt.getAttribute('data-qty') || '0';
        const partNum = selectedOpt.getAttribute('data-partnum') || '-';
        const sapCode = selectedOpt.getAttribute('data-sap') || '-';
        const sparepartId = selectedOpt.getAttribute('data-sparepart-id') || '-';

        document.getElementById('display_qty').value = qty + " Pcs";
        document.getElementById('display_part_num').value = partNum;
        document.getElementById('display_sap').value = sapCode;
        document.getElementById('display_sparepart_id').value = sparepartId;

        const rakSelect = document.getElementById('stock_eng_id');
        Array.from(rakSelect.options).forEach((option, index) => {
            if (index === 0) return;
            const matchesSparepart = option.getAttribute('data-sparepart-id') === sparepartId;
            option.hidden = !matchesSparepart;
            option.disabled = !matchesSparepart;
        });
        rakSelect.value = '';

        refreshInboundCounter();
    }

    let inboundNextCounter = 1;

    async function refreshInboundCounter() {
        if (!document.getElementById('source_id').value || !document.getElementById('stock_eng_id').value) {
            clearTableGrid();
            return;
        }

        try {
            const response = await fetch("{{ route('barcode.get.last.counter') }}", {
                headers: { 'Accept': 'application/json' }
            });
            const result = await response.json();
            if (result.success) inboundNextCounter = result.next_counter;
        } catch (error) {
            // Preview tetap bisa ditampilkan; counter final selalu ditentukan backend.
        }

        calculateBatchPreview();
    }

    function calculateBatchPreview() {
        const tbody = document.getElementById('batch_table_body');
        const patternText = document.getElementById('stats_pattern_rule');
        const badgeCount = document.getElementById('preview_count_badge');
        const showingText = document.getElementById('showing_entries_text');
        
        const docSelect = document.getElementById('source_id');
        const rakSelect = document.getElementById('stock_eng_id');

        if (!docSelect.value || !rakSelect.value) return;

        tbody.innerHTML = ''; 

        const selectedDocOpt = docSelect.options[docSelect.selectedIndex];
        const selectedRakOpt = rakSelect.options[rakSelect.selectedIndex];

        const sparepartId = (selectedDocOpt.getAttribute('data-sparepart-id') && selectedDocOpt.getAttribute('data-sparepart-id') !== '-') 
                            ? selectedDocOpt.getAttribute('data-sparepart-id') 
                            : (selectedRakOpt.getAttribute('data-sparepart-id') || '-');
                            
        const partNum = (selectedDocOpt.getAttribute('data-partnum') && selectedDocOpt.getAttribute('data-partnum') !== '-') 
                        ? selectedDocOpt.getAttribute('data-partnum') 
                        : (selectedRakOpt.getAttribute('data-partnum') || '-');
                        
        const sapCode = (selectedDocOpt.getAttribute('data-sap') && selectedDocOpt.getAttribute('data-sap') !== '-') 
                        ? selectedDocOpt.getAttribute('data-sap') 
                        : (selectedRakOpt.getAttribute('data-sap') || '-');

        document.getElementById('display_sparepart_id').value = sparepartId;
        document.getElementById('display_part_num').value = partNum;
        document.getElementById('display_sap').value = sapCode;

        // --- STRUCTURAL STRING GENERATOR ---
        const prefix = "TXENGINRAK"; 

        // Ambil angka rak & pad 2 digit (misal rak "1" -> "01")
        let rawRak = selectedRakOpt.getAttribute('data-rakname') || '';
        let cleanRak = rawRak.replace(/[^0-9]/g, '');
        cleanRak = cleanRak !== '' ? cleanRak.padStart(2, '0') : '01';

        // Clean Sparepart ID
        let cleanSpId = sparepartId.replace(/[^a-zA-Z0-9]/g, '').toUpperCase();

        // Format tanggal DDMMYY (misal "050826")
        const d = new Date();
        const dd = String(d.getDate()).padStart(2, '0');
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const yy = String(d.getFullYear()).slice(-2);
        const dateStr = dd + mm + yy;

        const qty = parseInt(selectedDocOpt.getAttribute('data-qty')) || 0;
        const bType = 'QR CODE';

        patternText.innerText = `${prefix}${cleanRak}${cleanSpId}${dateStr}[4-Digit Counter]`;
        badgeCount.innerText = `${qty} Items Registered`;
        showingText.innerText = `Showing 1 to ${qty} of ${qty} Entries`;

        for(let i = 1; i <= qty; i++) {
            let counterStr = String(inboundNextCounter + i - 1).padStart(4, '0');
            
            // Format: TXENGINRAK + RAK + SPAREPART_ID + DDMMYY + COUNTER
            let fullBarcodeString = `${prefix}${cleanRak}${cleanSpId}${dateStr}${counterStr}`;
            
            const inlineCanvasId = `inline_canvas_in_${i}`;
            const tr = document.createElement('tr');
            tr.className = "hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-150 bg-transparent";
            
            tr.innerHTML = `
                <td class="px-2 py-3.5 text-slate-500">${i}</td>
                <td class="px-3 py-2 border-l border-gray-100 dark:border-slate-800 text-center">
                    <div class="flex justify-center items-center">
                        <div id="${inlineCanvasId}" class="bg-white p-1 rounded border border-gray-200 flex items-center justify-center w-11 h-11 overflow-hidden shadow-sm"></div>
                    </div>
                </td>
                <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left font-mono font-black tracking-wide text-orange-600 dark:text-orange-400 select-all barcode-item-string">${fullBarcodeString}</td>
                <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-black text-blue-600 dark:text-blue-400">${sparepartId}</td>
                <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-bold">${partNum}</td>
                <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-bold">${sapCode}</td>
                <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-bold text-black dark:text-white">${selectedRakOpt.getAttribute('data-rakname')}</td>
                <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800">
                    <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border border-orange-200 bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400">${bType}</span>
                </td>
                <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                    <div class="flex items-center justify-center gap-1">
                        <button type="button" onclick="triggerBarcodePopup('${fullBarcodeString}', '${bType}')" title="Preview" class="w-7 h-7 rounded-md bg-blue-600 hover:bg-blue-700 text-white flex items-center justify-center transition-all active:scale-90 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.421 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                        <button type="button" onclick="downloadSingleBarcode('${fullBarcodeString}', '${bType}')" title="Download" class="w-7 h-7 rounded-md bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center transition-all active:scale-90 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/></svg>
                        </button>
                        <button type="button" onclick="printSingleBarcode('${fullBarcodeString}', '${bType}')" title="Print" class="w-7 h-7 rounded-md bg-amber-600 hover:bg-amber-700 text-white flex items-center justify-center transition-all active:scale-90 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.844l9.56-4.885M18 12a3 3 0 11-6 0 3 3 0 016 0zm-6 0a3 3 0 11-6 0 3 3 0 016 0zM6.72 13.844a3 3 0 104.56 2.625 3 3 0 00-4.56-2.625zM17.28 7.531a3 3 0 11-4.56-2.625 3 3 0 014.56 2.625z"/></svg>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(tr);

            setTimeout(() => {
                const targetBox = document.getElementById(inlineCanvasId);
                if (bType === 'QR CODE') {
                    new QRCode(targetBox, { text: fullBarcodeString, width: 36, height: 36, correctLevel : QRCode.CorrectLevel.M });
                } else {
                    const canvas = document.createElement('canvas');
                    targetBox.appendChild(canvas);
                    try { bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: fullBarcodeString, scale: 2 }); } 
                    catch (e) { targetBox.innerHTML = '<span class="text-[8px] text-rose-500">Err</span>'; }
                }
            }, 40);
        }
    }

    function clearTableGrid() {
        const tbody = document.getElementById('batch_table_body');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="py-12 text-center text-slate-400 italic font-medium text-[13px] font-nunito">
                    Pilih dokumen Material Received dan target Rak lokasi di atas untuk memuat daftar preview serialisasi masuk.
                </td>
            </tr>
        `;
        document.getElementById('stats_pattern_rule').innerText = 'Standby...';
        document.getElementById('preview_count_badge').innerText = '0 Items Pending Registration';
        document.getElementById('showing_entries_text').innerText = 'Showing 0 to 0 of 0 Entries';
    }

    function resetForm() {
        document.getElementById('automatedBarcodeForm').reset();
        Array.from(document.getElementById('stock_eng_id').options).forEach((option, index) => {
            if (index === 0) return;
            option.hidden = true;
            option.disabled = true;
        });
        document.getElementById('display_qty').value = '-';
        document.getElementById('display_part_num').value = '-';
        document.getElementById('display_sap').value = '-';
        document.getElementById('display_sparepart_id').value = '-';

        clearTableGrid();
    }

    function triggerBarcodePopup(textString, type) {
        Swal.fire({
            title: '<span class="text-sm font-black uppercase text-slate-800">Graphical Label Preview</span>',
            html: `
                <div class="flex flex-col items-center justify-center p-2 font-nunito">
                    <div id="modal_canvas_render" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-center min-w-[140px] min-h-[140px]"></div>
                    <div class="mt-4 bg-slate-100 rounded-lg px-3 py-2 border border-gray-200 w-full text-center">
                        <span class="block text-[9px] font-black uppercase text-slate-400">Payload Content</span>
                        <code class="text-xs font-mono font-black text-slate-800 select-all tracking-wider">${textString}</code>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'Close Container',
            confirmButtonColor: '#ea580c',
            didOpen: () => {
                const target = document.getElementById('modal_canvas_render');
                if(type === 'QR CODE') {
                    new QRCode(target, { text: textString, width: 130, height: 130, correctLevel : QRCode.CorrectLevel.M });
                } else {
                    const canvas = document.createElement('canvas');
                    target.appendChild(canvas);
                    try { bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: textString, scale: 5 }); } 
                    catch (e) { target.innerHTML = '<span class="text-xs text-rose-500 font-bold">Matrix Error</span>'; }
                }
            }
        });
    }

    function downloadSingleBarcode(textString, type) {
        const dummyContainer = document.createElement('div');
        dummyContainer.style.position = 'absolute';
        dummyContainer.style.left = '-9999px';
        document.body.appendChild(dummyContainer);

        if (type === 'QR CODE') {
            new QRCode(dummyContainer, { text: textString, width: 250, height: 250, correctLevel: QRCode.CorrectLevel.H });
            setTimeout(() => {
                const img = dummyContainer.querySelector('img');
                if (img) triggerClientDownload(img.src, `${textString}.png`);
                document.body.removeChild(dummyContainer);
            }, 100);
        } else {
            const canvas = document.createElement('canvas');
            dummyContainer.appendChild(canvas);
            try {
                bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: textString, scale: 10 });
                triggerClientDownload(canvas.toDataURL('image/png'), `${textString}.png`);
            } catch (e) {
                Swal.fire({ icon: 'error', title: 'Export Gagal', text: 'Gagal merender Data Matrix ke Canvas.' });
            }
            document.body.removeChild(dummyContainer);
        }
    }

    function triggerClientDownload(dataUrl, filename) {
        const link = document.createElement('a');
        link.href = dataUrl;
        link.download = filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function printSingleBarcode(textString, type) {
        const printWindow = window.open('', '_blank', 'width=600,height=600');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print Label - ${textString}</title>
                <style>
                    body { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 100vh; margin: 0; font-family: 'Nunito', sans-serif; text-align: center; }
                    .barcode-box { padding: 10px; border: 1px solid #ccc; background: white; }
                    .label-text { margin-top: 10px; font-size: 14px; font-weight: bold; font-family: monospace; letter-spacing: 1px; }
                </style>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>
                <script src="https://cdn.jsdelivr.net/npm/bwip-js@3.0.4/dist/bwip-js-min.js"><\/script>
            </head>
            <body>
                <div class="barcode-box" id="print_target"></div>
                <div class="label-text">${textString}</div>
                <script>
                    const type = "${type}";
                    const text = "${textString}";
                    const box = document.getElementById('print_target');
                    if(type === 'QR CODE') {
                        new QRCode(box, { text: text, width: 180, height: 180 });
                    } else {
                        const cvs = document.createElement('canvas');
                        box.appendChild(cvs);
                        bwipjs.toCanvas(cvs, { bcid: 'datamatrix', text: text, scale: 6 });
                    }
                    setTimeout(() => { window.print(); window.close(); }, 500);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    async function executeBatchGeneration() {
        const mode = document.getElementById('current_mode').value;
        const barcodeType = document.getElementById('barcode_type').value;
        const stockEngId = document.getElementById('stock_eng_id').value;
        const sourceDocumentId = document.getElementById('source_id').value;

        if (!sourceDocumentId || !stockEngId) {
            return Swal.fire({ icon: 'warning', title: 'Data Belum Lengkap', text: 'Silakan pilih dokumen Material Received dan target Lokasi Rak terlebih dahulu!' });
        }

        // --- AMBIL SEMUA STRING BARCODE FORMAT BARU DARI TABEL PREVIEW ---
        const generatedBarcodes = [];
        document.querySelectorAll('#batch_table_body .barcode-item-string').forEach(el => {
            generatedBarcodes.push(el.innerText.trim());
        });

        if (generatedBarcodes.length === 0) {
            return Swal.fire({ icon: 'warning', title: 'Daftar Kosong', text: 'Tabel preview barcode masih kosong!' });
        }

        const confirmAction = await Swal.fire({
            title: 'Eksekusi Otomasi Batch IN?',
            text: 'Sistem akan mendaftarkan serial barcode masuk baru dan menambahkan nominal kuantitas ke Master Stok Rak pilihan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#ea580c',
            cancelButtonColor: '#dc2626',
            confirmButtonText: 'Ya, Masukkan Stok!',
            cancelButtonText: 'Batal'
        });

        if (!confirmAction.isConfirmed) return;

        Swal.fire({
            title: 'Sedang Mendaftarkan Barcode...',
            text: 'Mohon tunggu sebentar, sistem sedang melakukan enkripsi & mutasi data.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        try {
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            const response = await fetch("{{ url('eng-overview/barcode-parsing') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    mode: mode,
                    source_id: sourceDocumentId,
                    barcode_type: barcodeType,
                    stock_eng_id: stockEngId
                })
            });

            const result = await response.json();

            if (result.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Batch IN Berhasil!',
                    text: result.message,
                    confirmButtonColor: '#ea580c'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal Memproses', text: result.message || 'Terjadi kesalahan internal backend.' });
            }
        } catch (error) {
            Swal.fire({ icon: 'error', title: 'Koneksi Gagal', text: 'Gagal terhubung dengan server API. Periksa kembali rute/jaringan.' });
        }
    }
</script>
@endsection
