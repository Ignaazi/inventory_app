@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .manual-in-prod-view, .manual-in-prod-view * {
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

<div class="manual-in-prod-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Create Manual IN (Production Floor)
      </h2>
      <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 mt-0.5">Pilih Transaction Out ID untuk memuat seluruh aspek data secara otomatis dari Engineering</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="{{ route('prod.transaction.in') }}"
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

    <form action="{{ route('prod.transaction.in.store_manual') }}" method="POST" id="form-manual-in-prod">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5">
            
            {{-- 1. DROPDOWN UTAMA: PILIH TRANSACTION OUT ID --}}
            <div class="flex flex-col gap-2 md:col-span-2 bg-gray-50/70 p-4 rounded-xl border border-dashed border-blue-300 dark:bg-slate-800/40 dark:border-slate-700">
                <label class="text-[10px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-wider">PILIH TRANSACTION OUT ID ENGINEERING</label>
                <div class="relative mt-1">
                    <select id="transaction_out_id" name="transaction_out_id" required 
                            class="w-full bg-white dark:bg-slate-800 border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-950 dark:text-white focus:outline-none focus:border-blue-500 appearance-none shadow-sm font-mono">
                        <option value="" class="font-sans">-- Silakan Pilih ID Transaksi Pengeluaran --</option>
                        @foreach($engineeringOuts as $eng)
                            <option value="{{ $eng->transaction_out_id }}" {{ old('transaction_out_id') == $eng->transaction_out_id ? 'selected' : '' }}>
                                {{ $eng->transaction_out_id }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
                <small id="load-status" class="text-[11px] font-bold text-slate-500 mt-1.5 font-mono">Status: Menunggu pilihan transaksi...</small>
            </div>

            {{-- 2. BARCODE ID (Auto-fill & Readonly) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">BARCODE ID</label>
                <input type="text" id="barcode_id" name="barcode_id" required readonly placeholder="Otomatis memuat..." value="{{ old('barcode_id') }}"
                       class="w-full bg-gray-100 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed font-mono shadow-inner">
            </div>

            {{-- 3. NO NOZZLE (Auto-fill & Readonly) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">NO NOZZLE</label>
                <input type="text" id="no_nozzle" name="no_nozzle" required readonly placeholder="Otomatis memuat..." value="{{ old('no_nozzle') }}"
                       class="w-full bg-gray-100 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-500 dark:text-slate-400 cursor-not-allowed shadow-inner">
            </div>

            {{-- 4. REQUEST NO (Auto-fill & Readonly) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">REQUEST NO</label>
                <input type="text" id="request_no" name="request_no" readonly placeholder="Otomatis memuat..." value="{{ old('request_no') }}"
                       class="w-full bg-gray-100 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-amber-600 dark:text-amber-400 cursor-not-allowed font-mono shadow-inner">
            </div>

            {{-- 5. QTY IN (Terkunci Otomatis dari Qty Out Engineering - Readonly) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">QTY IN (LOCKED FROM ENG)</label>
                <input type="number" id="qty_in" name="qty_in" required readonly placeholder="Otomatis memuat..." value="{{ old('qty_in') }}"
                       class="w-full bg-gray-100 dark:bg-slate-800/60 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-emerald-600 dark:text-emerald-400 cursor-not-allowed text-center shadow-inner">
            </div>

            {{-- 6. TARGET LINE PRODUCTION (Pilihan Dropdown Mandiri Sesuai Tema) --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">TARGET LINE PRODUCTION</label>
                <div class="relative">
                    <select id="line_id" name="line_id" required 
                            class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-bold text-slate-950 dark:text-white focus:outline-none focus:border-blue-500 appearance-none shadow-sm">
                        <option value="">-- Pilih Line Tujuan --</option>
                        @foreach($lines as $line)
                            <option value="{{ $line->line_id }}" {{ old('line_id') == $line->line_id ? 'selected' : '' }}>
                                Line {{ $line->no_line }} - {{ $line->name_machine }}
                            </option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- 7. COMMENT / CATATAN PENERIMAAN --}}
            <div class="flex flex-col gap-2">
                <label class="text-[10px] font-black text-slate-950 dark:text-white uppercase tracking-wider">COMMENT / NOTES</label>
                <input type="text" name="comment" placeholder="Tambahkan catatan penerimaan..." value="{{ old('comment') }}"
                       class="w-full bg-white dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-950 dark:text-white focus:outline-none focus:border-blue-500 shadow-sm">
            </div>

        </div>

        {{-- SUBMIT BUTTON WITH GRADIENT THEME MATCHING OUT MANUAL --}}
        <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
            <button type="submit" class="photo-grad-btn w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-6 py-2.5 text-xs font-black text-white tracking-wider uppercase">
                <i class="fas fa-save mr-1"></i> Submit In
            </button>
        </div>
    </form>
  </div>
</div>

{{-- SCRIPT SINKRONISASI AJAX DETAIL ASPEK DATA SECARA LIVE --}}
<script>
    document.getElementById('transaction_out_id').addEventListener('change', function() {
        let trxId = this.value;
        let statusText = document.getElementById('load-status');

        if (!trxId) {
            // Kosongkan form jika dropdown di-reset ke default
            document.getElementById('barcode_id').value = '';
            document.getElementById('no_nozzle').value = '';
            document.getElementById('request_no').value = '';
            document.getElementById('qty_in').value = '';
            statusText.innerText = 'Status: Menunggu pilihan transaksi...';
            statusText.className = 'text-[11px] font-bold text-slate-500 mt-1.5 font-mono';
            return;
        }

        statusText.innerText = '🔄 Sedang menyinkronkan seluruh aspek data engineering...';
        statusText.className = 'text-[11px] font-bold text-orange-500 mt-1.5 font-mono';

        fetch("/prod/transaction/get-eng-detail/" + encodeURIComponent(trxId))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server mengembalikan respon error (Status: ' + response.status + ')');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Set isi input secara instan tanpa celah manipulasi manual
                    document.getElementById('barcode_id').value = data.barcode_id;
                    document.getElementById('no_nozzle').value = data.no_nozzle;
                    document.getElementById('request_no').value = data.request_sparepart_id;
                    document.getElementById('qty_in').value = data.qty_out; // Qty otomatis terkunci dari eng_out

                    statusText.innerText = '✅ Semua data aspek engineering berhasil tersinkronisasi otomatis!';
                    statusText.className = 'text-[11px] font-bold text-emerald-600 dark:text-emerald-400 mt-1.5 font-mono';
                } else {
                    statusText.innerText = '❌ Gagal: ' + data.message;
                    statusText.className = 'text-[11px] font-bold text-rose-600 dark:text-rose-400 mt-1.5 font-mono';
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                statusText.innerText = '❌ Error Jaringan/Server: Pastikan Route API sudah benar!';
                statusText.className = 'text-[11px] font-bold text-rose-600 dark:text-rose-400 mt-1.5 font-mono';
            });
    });
</script>
@endsection