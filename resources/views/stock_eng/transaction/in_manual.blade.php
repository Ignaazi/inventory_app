@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Create Manual IN</h2>
                <p class="text-xs text-gray-500 mt-1">Input manual stock receiving or continue from Costing PR verification.</p>
            </div>
            
            <a href="{{ route('eng.in') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-4 py-2.5 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95 uppercase tracking-wider no-underline">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        @if(session('success'))
            <div class="mb-5 p-4 text-sm text-green-800 rounded-lg bg-green-50 font-bold border border-green-200">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-5 p-4 text-sm text-red-800 rounded-lg bg-red-50 font-bold border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('eng.in.store') }}" method="POST">
            @csrf
            <input type="hidden" name="source" value="manual">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
                
                <!-- DROPDOWN PR COSTING -->
                <div class="flex flex-col gap-2 bg-slate-50 dark:bg-slate-900/40 p-4 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                    <label class="text-xs font-black text-slate-900 dark:text-gray-200 uppercase tracking-wider flex items-center gap-1.5">
                        <i class="fas fa-file-invoice text-indigo-500"></i> Continue From Costing Material Received? <span class="text-[10px] font-normal text-gray-400">(1x Pakai)</span>
                    </label>
                    <div class="relative mt-1">
                        <select name="eng_material_receiving_id" id="select-costing-pr" class="w-full bg-white dark:bg-slate-950 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium appearance-none text-black dark:text-white">
                            <option value="">-- Murni Manual / Pilih PR Dokumen Jika Ada --</option>
                            @foreach($costingReceivings as $pr)
                                <option value="{{ $pr->id }}" data-qty="{{ $pr->qty_received }}" data-note="{{ $pr->costing_notes }}">
                                    [{{ $pr->receiving_code }}] {{ $pr->pr_code }} (Qty: {{ $pr->qty_received }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- SELECT RAK -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">1. Pilih Lokasi RAK</label>
                    <div class="relative">
                        <select id="select-rak" required class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium appearance-none text-black dark:text-white">
                            <option value="">-- Pilih RAK --</option>
                            @foreach($listRak as $rak)
                                <option value="{{ $rak }}">RAK: {{ $rak }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- SELECT NOZZLE / SPAREPART -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">2. Pilih Nozzle / Sparepart</label>
                    <div class="relative">
                        <select name="stock_eng_id" id="select-nozzle" required disabled class="w-full bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium appearance-none cursor-not-allowed text-black dark:text-white">
                            <option value="">-- Pilih RAK Dahulu --</option>
                        </select>
                    </div>
                </div>

                <!-- READ ONLY AUTOFILL DATA -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-gray-400">SAP Code</label>
                    <input type="text" id="auto_sap_code" readonly placeholder="Auto-fill" class="w-full bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-500 cursor-not-allowed">
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-gray-400">Part Number</label>
                    <input type="text" id="auto_part_number" readonly placeholder="Auto-fill" class="w-full bg-gray-100 dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-gray-500 cursor-not-allowed">
                </div>

                <!-- INPUT QUANTITY -->
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Amount Of Incoming Stock</label>
                    <input type="number" name="qty_in" id="qty_in" required min="1" placeholder="Enter The Quantity" class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-black dark:text-white">
                </div>

                <!-- REMARK -->
                <div class="flex flex-col gap-2 md:col-span-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Remark</label>
                    <textarea name="remark" id="remark" rows="3" placeholder="Receipt Info (optional)" class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium resize-none text-black dark:text-white"></textarea>
                </div>

            </div>

            <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-white px-6 py-2.5 rounded-lg text-sm font-bold shadow-md hover:opacity-90 transition-all active:scale-95 uppercase tracking-wider cursor-pointer">
                    Submit Data
                </button>
            </div>
        </form>
    </div>
</div>

<!-- DATA JEMBATAN KE JAVASCRIPT -->
<div id="raw-stock-data" style="display: none;">
    @foreach($stocks as $item)
        <div class="stock-item" 
             data-id="{{ $item->id }}" 
             data-rak="{{ $item->rak->nama_rak ?? '' }}" 
             data-nozzle="{{ $item->sparepart->category ?? 'SPAREPART' }} - {{ $item->sparepart->sparepart_id ?? 'N/A' }}" 
             data-sap="{{ $item->sparepart->sap_code ?? '-' }}" 
             data-pn="{{ $item->sparepart->part_number ?? '-' }}"
             data-qty="{{ $item->qty ?? 0 }}">
        </div>
    @endforeach
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectRak = document.getElementById('select-rak');
        const selectNozzle = document.getElementById('select-nozzle');
        const txtSap = document.getElementById('auto_sap_code');
        const txtPn = document.getElementById('auto_part_number');
        const qtyInField = document.getElementById('qty_in');
        const remarkField = document.getElementById('remark');
        const costingSelect = document.getElementById('select-costing-pr');

        // Load data stock dari DOM jembatan
        const stockData = Array.from(document.querySelectorAll('.stock-item')).map(el => ({
            id: el.getAttribute('data-id'),
            rak: el.getAttribute('data-rak'),
            nozzle: el.getAttribute('data-nozzle'),
            sap: el.getAttribute('data-sap'),
            pn: el.getAttribute('data-pn'),
            qty: el.getAttribute('data-qty')
        }));

        // 1. Filter Nozzle berdasarkan pilihan RAK
        selectRak.addEventListener('change', function() {
            const rakTerpilih = this.value;
            selectNozzle.innerHTML = '<option value="">-- Pilih Nozzle --</option>';
            txtSap.value = ''; txtPn.value = '';

            if (rakTerpilih === '') {
                selectNozzle.disabled = true;
                selectNozzle.classList.add('bg-gray-100', 'cursor-not-allowed');
                return;
            }

            const filtered = stockData.filter(item => item.rak === rakTerpilih);
            filtered.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = `${item.nozzle} (Stok Saat Ini: ${item.qty})`;
                selectNozzle.appendChild(opt);
            });

            selectNozzle.disabled = false;
            selectNozzle.classList.remove('bg-gray-100', 'cursor-not-allowed');
        });

        // 2. Autofill SAP & Part Number saat Nozzle dipilih
        selectNozzle.addEventListener('change', function() {
            const item = stockData.find(i => i.id === this.value);
            if (item) {
                txtSap.value = item.sap;
                txtPn.value = item.pn;
            } else {
                txtSap.value = ''; txtPn.value = '';
            }
        });

        // 3. Autofill dari PR Costing saat dokumen dipilih
        costingSelect.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            if (opt && opt.value !== "") {
                qtyInField.value = opt.getAttribute('data-qty') || '';
                const note = opt.getAttribute('data-note');
                remarkField.value = note ? "Continued from Costing PR note: " + note : "Continued from Costing PR transaction.";
            } else {
                qtyInField.value = ""; 
                remarkField.value = "";
            }
        });
    });
</script>
@endsection