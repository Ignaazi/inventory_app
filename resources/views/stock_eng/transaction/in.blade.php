@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 bg-slate-50/30 dark:bg-slate-900/50 min-h-screen">
    
    <!-- HEADER AREA -->
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-slate-800 dark:text-white">Transaction: Stock In</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 italic">Modul penambahan stok untuk Staff & SPV Engineering.</p>
    </div>

    <!-- MODE SWITCHER -->
    <div class="mb-8 inline-flex p-1 bg-slate-200 dark:bg-slate-800 rounded-2xl shadow-inner">
        <button id="btn-scan" class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all bg-white text-indigo-600 shadow-sm dark:bg-boxdark dark:text-white">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h2M4 8h16" stroke-width="2" stroke-linecap="round"/></svg>
            SCAN BARCODE
        </button>
        <button id="btn-manual" class="flex items-center gap-2 px-6 py-3 rounded-xl text-sm font-bold transition-all text-slate-600 dark:text-slate-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h7" stroke-width="2" stroke-linecap="round"/></svg>
            PILIH MANUAL
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- LEFT COLUMN: INPUT & SCANNER (8 COLS) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- 1. SCANNER AREA -->
            <div id="wrapper-scan" class="overflow-hidden rounded-3xl bg-black shadow-2xl border-4 border-white dark:border-slate-800 relative group">
                <div id="reader" style="width: 100%"></div>
                <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                    <button id="start-cam" class="bg-indigo-600 text-white px-6 py-2 rounded-full text-xs font-bold hover:bg-indigo-700 transition-all">START CAMERA</button>
                    <button id="stop-cam" class="bg-rose-500 text-white px-6 py-2 rounded-full text-xs font-bold hover:bg-rose-600 transition-all">STOP</button>
                </div>
            </div>

            <!-- 2. FORM DATA -->
            <form action="{{ route('stock.eng.in.update') }}" method="POST" id="form-stock-in" class="bg-white dark:bg-boxdark p-8 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700">
                @csrf
                <input type="hidden" name="stock_id" id="stock_id_final">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Dropdown Manual (Hidden by default) -->
                    <div id="wrapper-manual" class="hidden col-span-2">
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2 block">Pilih Item (Manual)</label>
                        <select id="select-manual" class="w-full bg-slate-50 dark:bg-slate-800 border-none rounded-2xl p-4 text-sm focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Cari Nozzle / Part No --</option>
                            @foreach($stocks as $item)
                                <option value="{{ $item->id }}" data-sap="{{ $item->sap_code }}" data-qty="{{ $item->qty }}" data-name="{{ $item->no_nozzle }}">
                                    {{ $item->no_nozzle }} | {{ $item->sap_code }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Read-only Info -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">SAP Code</label>
                        <input type="text" id="view_sap" readonly placeholder="---" class="w-full bg-transparent border-b border-slate-200 py-2 font-bold text-slate-800 dark:text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1 block">Current Stock</label>
                        <input type="text" id="view_qty" readonly placeholder="0" class="w-full bg-transparent border-b border-slate-200 py-2 font-bold text-indigo-600 focus:outline-none">
                    </div>
                </div>

                <div class="mb-8">
                    <label class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2 block text-center">Jumlah Stok Masuk (QTY IN)</label>
                    <input type="number" name="qty_in" required min="1" placeholder="0" class="w-full text-center text-6xl font-black bg-slate-50 dark:bg-slate-800 rounded-3xl p-6 focus:ring-4 focus:ring-emerald-500/20 transition-all">
                </div>

                <button type="submit" class="w-full bg-emerald-500 hover:bg-emerald-600 text-white py-6 rounded-3xl text-xl font-black shadow-xl shadow-emerald-500/20 transition-all hover:-translate-y-1">
                    SUBMIT STOCK ENTRY
                </button>
            </form>
        </div>

        <!-- RIGHT COLUMN: INFO & RECENT (4 COLS) -->
        <div class="lg:col-span-4 space-y-6">
            <div class="bg-indigo-600 p-8 rounded-3xl text-white shadow-xl shadow-indigo-500/20">
                <h4 class="font-bold text-lg mb-2">Instruksi Entry</h4>
                <p class="text-sm text-indigo-100 leading-relaxed">Gunakan scanner untuk meminimalkan kesalahan input data. Jika label rusak, gunakan mode manual untuk mencari berdasarkan Part Number.</p>
            </div>

            <div class="bg-white dark:bg-boxdark p-6 rounded-3xl shadow-sm border border-slate-100 dark:border-slate-700">
                <h4 class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Update Terakhir</h4>
                <div class="space-y-4">
                    @foreach($recent_logs as $log)
                    <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-800 rounded-2xl">
                        <div>
                            <div class="text-xs font-bold">{{ $log->no_nozzle }}</div>
                            <div class="text-[9px] text-slate-400 uppercase">{{ $log->updated_at->diffForHumans() }}</div>
                        </div>
                        <div class="text-emerald-500 font-bold">+{{ session('last_in_'.$log->id) ?? '?' }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const btnScan = document.getElementById('btn-scan');
    const btnManual = document.getElementById('btn-manual');
    const wrapperScan = document.getElementById('wrapper-scan');
    const wrapperManual = document.getElementById('wrapper-manual');

    // Toggle Mode
    btnScan.addEventListener('click', () => {
        btnScan.classList.add('bg-white', 'text-indigo-600', 'shadow-sm', 'dark:bg-boxdark', 'dark:text-white');
        btnManual.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm', 'dark:bg-boxdark', 'dark:text-white');
        wrapperScan.classList.remove('hidden');
        wrapperManual.classList.add('hidden');
    });

    btnManual.addEventListener('click', () => {
        btnManual.classList.add('bg-white', 'text-indigo-600', 'shadow-sm', 'dark:bg-boxdark', 'dark:text-white');
        btnScan.classList.remove('bg-white', 'text-indigo-600', 'shadow-sm', 'dark:bg-boxdark', 'dark:text-white');
        wrapperManual.classList.remove('hidden');
        wrapperScan.classList.add('hidden');
        stopScanner();
    });

    // Manual Selection
    document.getElementById('select-manual').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if(this.value) {
            document.getElementById('stock_id_final').value = this.value;
            document.getElementById('view_sap').value = opt.dataset.sap;
            document.getElementById('view_qty').value = opt.dataset.qty;
        }
    });

    // Scanner Logic
    const onScanSuccess = (decodedText) => {
        const stocks = @json($stocks);
        const item = stocks.find(s => s.sap_code == decodedText);

        if(item) {
            document.getElementById('stock_id_final').value = item.id;
            document.getElementById('view_sap').value = item.sap_code;
            document.getElementById('view_qty').value = item.qty;
            Swal.fire({ icon: 'success', title: 'Item Found', text: item.no_nozzle, timer: 1500, didOpen: () => { document.querySelector('.swal2-container').style.zIndex = '10000'; } });
            stopScanner();
        } else {
            Swal.fire({ icon: 'error', title: 'Not Found', text: 'SAP: ' + decodedText, didOpen: () => { document.querySelector('.swal2-container').style.zIndex = '10000'; } });
        }
    };

    const stopScanner = () => html5QrCode.stop().catch(() => {});
    document.getElementById('start-cam').addEventListener('click', () => html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScanSuccess));
    document.getElementById('stop-cam').addEventListener('click', stopScanner);

    // SweetAlert Success/Error handling (Global)
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'Succeed!', text: "{{ session('success') }}", didOpen: () => { document.querySelector('.swal2-container').style.zIndex = '10000'; } });
    @endif
</script>
@endsection