@extends('admin')

@section('content')
<div class="mx-auto max-w-lg p-4 md:p-6">
    <div class="mb-6">
        <h2 class="text-lg font-bold text-slate-950 dark:text-white">Form Manual OUT - Production</h2>
        <p class="text-xs text-slate-600 dark:text-gray-400">Kurangi dan keluarkan nozzle berdasarkan referensi Inproduction ID</p>
    </div>

    {{-- Alert Error --}}
    @if(session('error'))
        <div class="mb-4 p-3 text-xs font-bold text-rose-800 rounded-lg bg-rose-50 border border-rose-200">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-slate-900 shadow-sm">
        <form action="{{ route('prod.transaction.out.manual.store') }}" method="POST">
            @csrf

            <div class="mb-4">
                <label class="block mb-1 text-xs font-bold text-slate-950 dark:text-white uppercase">Pilih Inproduction ID (Referensi IN)</label>
                <select name="inproduction_id" id="inproduction_id" class="w-full rounded-lg border border-gray-300 p-2 text-xs font-bold text-slate-950 dark:border-gray-700 dark:bg-slate-800 dark:text-white" required onchange="fetchInProdDetail(this.value)">
                    <option value="">-- Pilih Referensi IN --</option>
                    @foreach($availableIns as $item)
                        <option value="{{ $item->inproduction_id }}">
                            ID: {{ $item->inproduction_id }} | Nozzle: {{ $item->no_nozzle }} | Line: {{ $item->no_line }} (Stok Live: {{ $item->current_stock_qty }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-xs font-bold text-slate-950 dark:text-white uppercase">Line Origin</label>
                <input type="text" id="line_display" class="w-full rounded-lg border bg-gray-50/80 p-2 text-xs font-bold text-slate-700 dark:bg-slate-800/50 dark:text-gray-300" readonly placeholder="- Otomatis Terisi -">
            </div>

            <div class="mb-4">
                <label class="block mb-1 text-xs font-bold text-slate-950 dark:text-white uppercase">No Nozzle</label>
                <input type="text" id="no_nozzle_display" class="w-full rounded-lg border bg-gray-50/80 p-2 text-xs font-bold text-slate-700 dark:bg-slate-800/50 dark:text-gray-300" readonly placeholder="- Otomatis Terisi -">
            </div>

            <div id="detail_box" class="hidden mb-4 p-3 bg-slate-50 dark:bg-slate-800/30 border border-gray-100 dark:border-gray-800 rounded-lg">
                <div class="flex justify-between mb-1">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Request No:</span>
                    <span id="req_no_display" class="text-[11px] font-bold text-slate-950 dark:text-white font-mono">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-[11px] font-bold text-slate-500 uppercase">Barcode ID:</span>
                    <span id="barcode_display" class="text-[11px] font-bold text-slate-950 dark:text-white font-mono">-</span>
                </div>
            </div>

            <div class="mb-5">
                <label class="block mb-1 text-xs font-bold text-slate-950 dark:text-white uppercase">Qty Out</label>
                <input type="number" name="qty_out" id="qty_out" min="1" class="w-full rounded-lg border border-gray-300 p-2 text-xs font-bold text-slate-950 dark:border-gray-700 dark:bg-slate-800 dark:text-white" placeholder="Masukkan jumlah yang mau dikeluarkan" required>
                <span id="max-info" class="text-[10px] text-rose-600 font-bold mt-1 block"></span>
            </div>

            <div class="mb-5">
                <label class="block mb-1 text-xs font-bold text-slate-950 dark:text-white uppercase">Comment / Alasan Keluar</label>
                <textarea name="comment" rows="2" class="w-full rounded-lg border border-gray-300 p-2 text-xs font-medium text-slate-950 dark:border-gray-700 dark:bg-slate-800 dark:text-white" placeholder="Contoh: Nozzle aus / Maintenance berkala / Scrap / Tukar barang"></textarea>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex gap-3">
                <a href="{{ route('prod.transaction.out') }}" class="w-1/2 text-center rounded-lg bg-gray-100 py-2 text-xs font-bold text-slate-700 hover:bg-gray-200 transition-colors">Batal</a>
                <button type="submit" class="w-1/2 rounded-lg bg-slate-950 py-2 text-xs font-bold text-white hover:bg-slate-800 dark:bg-indigo-600 dark:hover:bg-indigo-700 transition-colors shadow-sm">Submit OUT</button>
            </div>
        </form>
    </div>
</div>

{{-- Script AJAX Autofill Detail --}}
<script>
function fetchInProdDetail(id) {
    const detailBox = document.getElementById('detail_box');
    const lineDisplay = document.getElementById('line_display');
    const nozzleDisplay = document.getElementById('no_nozzle_display');
    const reqDisplay = document.getElementById('req_no_display');
    const barcodeDisplay = document.getElementById('barcode_display');
    const qtyInput = document.getElementById('qty_out');
    const maxInfo = document.getElementById('max-info');

    if(!id) {
        detailBox.classList.add('hidden');
        lineDisplay.value = '';
        nozzleDisplay.value = '';
        qtyInput.max = '';
        maxInfo.textContent = '';
        return;
    }

    // Menembak URL API internal yang ada di controller
    fetch(`/prod/transaction/out/detail/${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                // Tampilkan Kotak Detail & Isikan datanya
                detailBox.classList.remove('hidden');
                lineDisplay.value = data.line_display;
                nozzleDisplay.value = data.no_nozzle;
                reqDisplay.textContent = data.request_no;
                barcodeDisplay.textContent = data.barcode_id;
                
                // Kunci batas maksimal input sesuai sisa live stock
                qtyInput.max = data.max_available;
                maxInfo.textContent = `*Batas maksimum pengeluaran item ini: ${data.max_available} unit`;
            } else {
                alert(data.message);
            }
        })
        .catch(err => {
            console.error('Error fetching detail:', err);
        });
}
</script>
@endsection