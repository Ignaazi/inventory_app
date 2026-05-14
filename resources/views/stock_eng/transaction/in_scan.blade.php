@extends('admin')

@section('content')
<div class="mx-auto max-w-2xl p-4 md:p-10">
    <a href="{{ route('eng.in') }}" class="text-sm text-slate-400 mb-4 inline-block hover:text-indigo-600">← Kembali ke History</a>
    
    <!-- Bagian Scanner dari Master Lo -->
    <div id="wrapper-scan" class="overflow-hidden rounded-3xl bg-black shadow-2xl border-4 border-white dark:border-slate-800 relative mb-6">
        <div id="reader" style="width: 100%"></div>
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
            <button id="start-cam" class="bg-indigo-600 text-white px-6 py-2 rounded-full text-xs font-bold shadow-lg">START CAMERA</button>
            <button id="stop-cam" class="bg-rose-500 text-white px-6 py-2 rounded-full text-xs font-bold shadow-lg">STOP</button>
        </div>
    </div>

    <!-- Form Submit (Tetap pake route yang sama) -->
    <form action="{{ route('stock.eng.in.update') }}" method="POST" id="form-stock-in" class="bg-white dark:bg-boxdark p-8 rounded-3xl shadow-xl">
        @csrf
        <input type="hidden" name="stock_id" id="stock_id_final">
        <div class="mb-6 text-center">
             <h3 id="display_name" class="text-xl font-bold text-slate-800 dark:text-white">Menunggu Scan...</h3>
             <p id="view_sap_text" class="text-sm text-slate-400 italic">SAP Code akan muncul di sini</p>
        </div>
        <div class="mb-8">
            <label class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest mb-2 block text-center">Jumlah Stok Masuk</label>
            <input type="number" name="qty_in" required min="1" class="w-full text-center text-6xl font-black bg-slate-50 dark:bg-slate-800 rounded-3xl p-6">
        </div>
        <button type="submit" class="w-full bg-emerald-500 text-white py-6 rounded-3xl text-xl font-black shadow-xl">SUBMIT SCAN ENTRY</button>
    </form>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    const html5QrCode = new Html5Qrcode("reader");
    const onScanSuccess = (decodedText) => {
        const stocks = @json($stocks);
        const item = stocks.find(s => s.sap_code == decodedText);
        if(item) {
            document.getElementById('stock_id_final').value = item.id;
            document.getElementById('display_name').innerText = item.no_nozzle;
            document.getElementById('view_sap_text').innerText = "SAP: " + item.sap_code;
            Swal.fire({ icon: 'success', title: 'Item Found', text: item.no_nozzle });
            html5QrCode.stop();
        }
    };
    document.getElementById('start-cam').addEventListener('click', () => html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: 250 }, onScanSuccess));
    document.getElementById('stop-cam').addEventListener('click', () => html5QrCode.stop());
</script>
@endsection