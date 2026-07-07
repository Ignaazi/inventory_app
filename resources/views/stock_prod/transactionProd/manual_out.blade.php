@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .manual-out-prod-view, .manual-out-prod-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  /* Custom Clean Hover & Shadow matching main theme */
  .photo-grad-btn {
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
  }
  .photo-grad-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.05);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
  }
  .photo-grad-btn:active {
    transform: translateY(0);
  }
</style>

<div class="manual-out-prod-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Create Manual OUT (Production Floor)
      </h2>
      <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 mt-0.5">Kurangi dan keluarkan nozzle berdasarkan referensi Inproduction ID</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="{{ route('prod.transaction.out') }}"
        class="photo-grad-btn w-full sm:w-44 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <i class="fas fa-arrow-left mr-2"></i> Kembali ke History
      </a>
    </div>
  </div>

  {{-- CONTAINER FORM --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-6 pt-5 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-6">
    
    <div class="mb-6 border-b border-gray-100 pb-4 dark:border-gray-800">
      <h3 class="text-base font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Transaction Detail Form
      </h3>
    </div>

    {{-- ALERT NOTIFIKASI ERROR --}}
    @if(session('error'))
        <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-600 dark:bg-red-950/20 dark:border-red-900 dark:text-red-400 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
            <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('prod.transaction.out.manual.store') }}" method="POST" id="form-manual-out-prod">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            
            {{-- 1. DROPDOWN UTAMA: PILIH INPRODUCTION ID REFERENCE --}}
            <div class="flex flex-col gap-2 md:col-span-2 bg-gray-50/70 p-4 rounded-xl border border-dashed border-blue-300 dark:bg-slate-800/40 dark:border-slate-700">
                <label class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider">PILIH INPRODUCTION ID (REFERENSI IN)</label>
                <div class="relative mt-1">
                    <select id="inproduction_id" name="inproduction_id" required onchange="fetchInProdDetail(this.value)"
                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-950 dark:text-white focus:outline-none focus:border-blue-500 appearance-none shadow-sm font-mono">
                        <option value="" class="font-sans">-- Pilih Referensi IN --</option>
                        @foreach($availableIns as $item)
                            <option value="{{ $item->inproduction_id }}">
                                ID: {{ $item->inproduction_id }} | Nozzle: {{ $item->no_nozzle }} | Line: {{ $item->no_line }} (Stok Live: {{ $item->current_stock_qty }})
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
                <small id="load-status" class="text-[11px] font-bold text-slate-500 mt-1.5 font-mono">Status: Menunggu pilihan referensi...</small>
            </div>

            {{-- 2. LINE ORIGIN (Auto-fill & Readonly) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">LINE ORIGIN</label>
                <input type="text" id="line_display" name="line_display" required readonly placeholder="- Otomatis Terisi -"
                       class="w-full bg-gray-100 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed shadow-inner">
            </div>

            {{-- 3. NO NOZZLE (Auto-fill & Readonly) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">NO NOZZLE</label>
                <input type="text" id="no_nozzle_display" name="no_nozzle_display" required readonly placeholder="- Otomatis Terisi -"
                       class="w-full bg-gray-100 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed font-mono shadow-inner">
            </div>

            {{-- DYNAMIC DETAILS BOX (Auto-fill & Smooth layout injection) --}}
            <div id="detail_box" class="hidden md:col-span-2 p-4 bg-slate-50 dark:bg-slate-800/30 border border-gray-100 dark:border-gray-800 rounded-xl space-y-2">
                <div class="flex justify-between items-center pb-2 border-b border-gray-200/50 dark:border-gray-800">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">REQUEST NO:</span>
                    <span id="req_no_display" class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 font-mono">-</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">BARCODE ID:</span>
                    <span id="barcode_display" class="text-[11px] font-bold text-slate-950 dark:text-white font-mono tracking-tight">-</span>
                </div>
            </div>

            {{-- 4. QUANTITY OUT WITH MAXIMUM NOTIFICATION --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">QTY OUT</label>
                <input type="number" name="qty_out" id="qty_out" required min="1" placeholder="Masukkan jumlah yang mau dikeluarkan"
                       class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-950 dark:text-white focus:outline-none focus:border-blue-500 shadow-sm text-center">
                <span id="max-info" class="text-[10px] text-rose-600 font-extrabold tracking-tight mt-0.5 block"></span>
            </div>

            {{-- 5. COMMENT / REASON --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">COMMENT / ALASAN KELUAR</label>
                <input type="text" name="comment" placeholder="Contoh: Nozzle aus / Maintenance berkala / Scrap / Tukar barang"
                       class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-950 dark:text-white focus:outline-none focus:border-blue-500 shadow-sm">
            </div>

        </div>

        {{-- SUBMIT BUTTON WITH GRADIENT THEME MATCHING MAIN DESIGN --}}
        <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end gap-3">
            <a href="{{ route('prod.transaction.out') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-300 bg-white px-5 py-2.5 text-xs font-extrabold text-slate-700 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700/50 transition-colors uppercase tracking-wider">
                Batal
            </a>
            <button type="submit" class="photo-grad-btn inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-6 py-2.5 text-xs font-black text-white tracking-wider uppercase">
                <i class="fas fa-save mr-1"></i> Submit Out
            </button>
        </div>
    </form>
  </div>
</div>

{{-- SCRIPT AJAX AUTOFILL DETAIL --}}
<script>
function fetchInProdDetail(id) {
    const detailBox = document.getElementById('detail_box');
    const lineDisplay = document.getElementById('line_display');
    const nozzleDisplay = document.getElementById('no_nozzle_display');
    const reqDisplay = document.getElementById('req_no_display');
    const barcodeDisplay = document.getElementById('barcode_display');
    const qtyInput = document.getElementById('qty_out');
    const maxInfo = document.getElementById('max-info');
    const statusText = document.getElementById('load-status');

    if(!id) {
        detailBox.classList.add('hidden');
        lineDisplay.value = '';
        nozzleDisplay.value = '';
        qtyInput.max = '';
        maxInfo.textContent = '';
        statusText.innerText = 'Status: Menunggu pilihan referensi...';
        statusText.className = 'text-[11px] font-bold text-slate-500 mt-1.5 font-mono';
        return;
    }

    statusText.innerText = '🔄 Memuat seluruh rincian aspek data referensi IN...';
    statusText.className = 'text-[11px] font-bold text-orange-500 mt-1.5 font-mono';

    fetch(`/prod/transaction/out/detail/${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                detailBox.classList.remove('hidden');
                lineDisplay.value = data.line_display;
                nozzleDisplay.value = data.no_nozzle;
                reqDisplay.textContent = data.request_no;
                barcodeDisplay.textContent = data.barcode_id;
                
                qtyInput.max = data.max_available;
                maxInfo.textContent = `*Batas maksimum pengeluaran item ini: ${data.max_available} unit`;
                
                statusText.innerText = '✅ Seluruh data referensi masuk berhasil disinkronkan!';
                statusText.className = 'text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1.5 font-mono';
            } else {
                statusText.innerText = '❌ Gagal: ' + data.message;
                statusText.className = 'text-[11px] font-bold text-rose-600 dark:text-rose-400 mt-1.5 font-mono';
                alert(data.message);
            }
        })
        .catch(err => {
            console.error('Error fetching detail:', err);
            statusText.innerText = '❌ Error Jaringan/Server: Pastikan Route API sudah benar!';
            statusText.className = 'text-[11px] font-bold text-rose-600 dark:text-rose-400 mt-1.5 font-mono';
        });
}
</script>
@endsection