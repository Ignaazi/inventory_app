@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .font-nunito { font-family: 'Nunito', sans-serif !important; }
    .swal2-container { z-index: 99999 !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen">
    <div class="max-w-3xl mx-auto">
        <div class="w-full overflow-hidden rounded-2xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-md">
            <div class="border-b border-gray-200 dark:border-slate-800 px-6 py-4 flex items-center justify-between">
                <div>
                    <h3 class="text-base md:text-lg font-black text-slate-950 dark:text-white uppercase tracking-tight">
                        Manual OUT LOST
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-bold">
                        Kurangi satu stok produksi untuk barang yang hilang tanpa barcode.
                    </p>
                </div>
                <a href="{{ route('prod.transaction.out') }}" class="text-xs font-black text-slate-400 hover:text-slate-600 dark:hover:text-white uppercase no-underline">
                    Cancel
                </a>
            </div>

            <form action="{{ route('prod.transaction.out.manual.store') }}" method="POST" class="p-6 space-y-5">
                @csrf
                <input type="hidden" name="stock_prods_id" id="stock_prods_id">

                <div>
                    <label for="line_id" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Production Line <span class="text-rose-500">*</span>
                    </label>
                    <select id="line_id" required class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none">
                        <option value="">-- Pilih Line --</option>
                        @foreach($activeStocks->unique('line_id') as $stock)
                            <option value="{{ $stock->line_id }}">LINE {{ $stock->line_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="sparepart_id" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        Sparepart ID <span class="text-rose-500">*</span>
                    </label>
                    <select id="sparepart_id" required disabled class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none disabled:opacity-60">
                        <option value="">-- Pilih Line Terlebih Dahulu --</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 dark:text-slate-400 mb-2 uppercase">SAP Code</label>
                        <input id="sap_code" type="text" readonly class="w-full rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-sm font-mono font-bold text-slate-700 dark:text-slate-200" value="-">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 dark:text-slate-400 mb-2 uppercase">Part Number</label>
                        <input id="part_number" type="text" readonly class="w-full rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-sm font-mono font-bold text-slate-700 dark:text-slate-200" value="-">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 dark:text-slate-400 mb-2 uppercase">Stock Aktual</label>
                        <input id="stock_qty" type="text" readonly class="w-full rounded-xl border border-gray-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 px-3 py-2.5 text-sm font-black text-blue-700 dark:text-blue-300" value="0 Pcs">
                    </div>
                </div>

                <div>
                    <label for="nik_karyawan" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">
                        NIK Karyawan yang Menghilangkan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nik_karyawan" id="nik_karyawan" value="{{ old('nik_karyawan') }}" required maxlength="50" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2.5 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none" placeholder="Masukkan NIK karyawan">
                </div>

                <div>
                    <label for="remark" class="block text-xs font-black text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Remark</label>
                    <textarea name="remark" id="remark" rows="3" class="w-full rounded-xl border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-sm font-bold text-slate-900 dark:text-slate-100 focus:border-blue-500 focus:outline-none" placeholder="Keterangan tambahan (opsional)">{{ old('remark') }}</textarea>
                    <p class="text-[11px] text-slate-400 font-bold mt-1">Sistem otomatis mencatat remark sebagai AUTOMATED LOST.</p>
                </div>

                <button type="submit" id="submitButton" disabled class="w-full inline-flex items-center justify-center h-11 rounded-xl bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 text-xs font-black text-white tracking-wider uppercase shadow-md hover:opacity-95 transition-all active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50">
                    Save Manual LOST (1 Pcs)
                </button>
            </form>
        </div>
    </div>
</div>

@php
    $manualStockOptions = $activeStocks->map(function ($stock) {
        return [
            'stock_id' => $stock->id,
            'line_id' => $stock->line_id,
            'sparepart_id' => $stock->sparepart_id,
            'item_code' => $stock->item_code,
            'sap_code' => $stock->sap_code,
            'part_number' => $stock->part_number,
            'qty' => $stock->qty,
        ];
    })->values();
@endphp

<script>
    const stockOptions = @json($manualStockOptions);

    const lineSelect = document.getElementById('line_id');
    const sparepartSelect = document.getElementById('sparepart_id');
    const stockIdInput = document.getElementById('stock_prods_id');
    const submitButton = document.getElementById('submitButton');

    function resetStockDetails() {
        stockIdInput.value = '';
        document.getElementById('sap_code').value = '-';
        document.getElementById('part_number').value = '-';
        document.getElementById('stock_qty').value = '0 Pcs';
        submitButton.disabled = true;
    }

    function loadSpareparts() {
        const lineId = lineSelect.value;
        sparepartSelect.innerHTML = '<option value="">-- Pilih Sparepart --</option>';
        resetStockDetails();

        if (!lineId) {
            sparepartSelect.disabled = true;
            return;
        }

        stockOptions
            .filter(stock => String(stock.line_id) === String(lineId))
            .forEach(stock => {
                const option = document.createElement('option');
                option.value = stock.sparepart_id;
                option.textContent = `${stock.item_code} (Stock: ${stock.qty} Pcs)`;
                option.dataset.stockId = stock.stock_id;
                sparepartSelect.appendChild(option);
            });

        sparepartSelect.disabled = false;
    }

    function loadStockDetails() {
        const selected = stockOptions.find(stock =>
            String(stock.line_id) === String(lineSelect.value) &&
            String(stock.sparepart_id) === String(sparepartSelect.value)
        );

        if (!selected) {
            resetStockDetails();
            return;
        }

        stockIdInput.value = selected.stock_id;
        document.getElementById('sap_code').value = selected.sap_code || '-';
        document.getElementById('part_number').value = selected.part_number || '-';
        document.getElementById('stock_qty').value = `${selected.qty} Pcs`;
        submitButton.disabled = Number(selected.qty) < 1;
    }

    lineSelect.addEventListener('change', loadSpareparts);
    sparepartSelect.addEventListener('change', loadStockDetails);

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'Gagal!', text: @json(session('error')) });
    @endif
</script>
@endsection
