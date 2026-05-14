@extends('admin') {{-- Sesuaikan dengan master layout Anda --}}

@section('content')
<div class="min-h-screen bg-slate-50 p-4">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Nozzle Stock In (Masuk)</h1>
            <p class="text-slate-500">Scan barcode atau pilih nozzle untuk menambah stok</p>
        </div>
        <a href="{{ route('stock.eng.index') }}" class="rounded-lg bg-slate-200 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-300 transition-all">
             Kembali ke Inventory
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- KIRI: Scanner & Input -->
        <div class="space-y-6">
            <!-- Card Scanner -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-slate-800">Barcode Scanner</h2>
                    <span id="status-scanner" class="rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-600">Off</span>
                </div>
                
                <!-- Area Kamera -->
                <div id="reader" class="overflow-hidden rounded-xl bg-slate-100" style="width: 100%;"></div>
                
                <div class="mt-4 flex gap-2">
                    <button id="start-scan" class="flex-1 rounded-lg bg-indigo-600 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-all">
                        Mulai Scan
                    </button>
                    <button id="stop-scan" class="flex-1 rounded-lg bg-slate-200 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-300 transition-all">
                        Matikan Kamera
                    </button>
                </div>
            </div>

            <!-- Card Form Input -->
            <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200">
                <h2 class="mb-4 text-lg font-semibold text-slate-800">Data Transaksi</h2>
                <form action="#" method="POST" id="form-stock-in">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700">SAP CODE / Nozzle</label>
                            <input type="text" id="sap_code" name="sap_code" readonly
                                class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 p-3 text-sm focus:border-indigo-500 focus:ring-indigo-500" 
                                placeholder="Scan Barcode untuk mengisi otomatis...">
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Nama Nozzle</label>
                                <input type="text" id="no_nozzle" readonly class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 p-3 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700">Stock Saat Ini</label>
                                <input type="text" id="current_qty" readonly class="mt-1 block w-full rounded-xl border-slate-200 bg-slate-50 p-3 text-sm font-bold text-indigo-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-700 text-red-600 font-bold">Qty Masuk (Update)</label>
                            <input type="number" name="qty_in" required min="1"
                                class="mt-1 block w-full rounded-xl border-slate-200 p-3 text-sm focus:border-emerald-500 focus:ring-emerald-500" 
                                placeholder="Masukkan jumlah stok masuk">
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-emerald-600 py-4 text-sm font-bold text-white shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition-all">
                            SIMPAN STOK MASUK
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- KANAN: History Input Hari Ini -->
        <div class="rounded-2xl bg-white p-6 shadow-sm border border-slate-200 overflow-hidden">
            <h2 class="mb-4 text-lg font-semibold text-slate-800">History Masuk Hari Ini</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50">
                            <th class="p-3 font-semibold text-slate-700">Waktu</th>
                            <th class="p-3 font-semibold text-slate-700">Item</th>
                            <th class="p-3 font-semibold text-slate-700 text-center text-emerald-600 font-bold underline">Qty In</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        {{-- Data statis contoh --}}
                        <tr>
                            <td class="p-3 text-slate-500">14:20</td>
                            <td class="p-3">
                                <div class="font-medium text-slate-800 italic">NZL-001</div>
                                <div class="text-xs text-slate-400 font-bold">SCODE12345</div>
                            </td>
                            <td class="p-3 text-center text-emerald-600 font-bold">+50</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- SCRIPTS -->
<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    const html5QrCode = new Html5Qrcode("reader");
    const sapInput = document.getElementById('sap_code');
    const nozzleInput = document.getElementById('no_nozzle');
    const qtyInput = document.getElementById('current_qty');
    const statusLabel = document.getElementById('status-scanner');

    const config = { fps: 10, qrbox: { width: 250, height: 250 } };

    // Fungsi Sukses Scan
    const onScanSuccess = (decodedText, decodedResult) => {
        // Bunyi Bip (Optional)
        // audio.play();
        
        sapInput.value = decodedText;
        statusLabel.innerText = "Berhasil Scan!";
        statusLabel.className = "rounded-full bg-emerald-100 px-3 py-1 text-xs font-medium text-emerald-600";
        
        // Disini nanti kita panggil AJAX ke Controller untuk cari data Nozzle berdasarkan sap_code
        fetchDataNozzle(decodedText);
        
        // Hentikan scanner setelah berhasil (agar tidak scan terus menerus)
        stopScanner();
    };

    const startScanner = () => {
        html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess);
        statusLabel.innerText = "Scanning...";
        statusLabel.className = "rounded-full bg-blue-100 px-3 py-1 text-xs font-medium text-blue-600";
    };

    const stopScanner = () => {
        html5QrCode.stop().catch(err => console.log(err));
        statusLabel.innerText = "Off";
        statusLabel.className = "rounded-full bg-red-100 px-3 py-1 text-xs font-medium text-red-600";
    };

    document.getElementById('start-scan').addEventListener('click', startScanner);
    document.getElementById('stop-scan').addEventListener('click', stopScanner);

    // Simulasi Fetch Data (Nanti diganti AJAX Laravel)
    function fetchDataNozzle(code) {
        // Contoh respon simulasi
        nozzleInput.value = "Nozzle Type A"; 
        qtyInput.value = "150";
    }

    // Submit Form SweetAlert
    document.getElementById('form-stock-in').addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Konfirmasi Stok',
            text: "Yakin ingin menambahkan stok ini?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Ya, Simpan!',
            didOpen: () => {
                document.querySelector('.swal2-container').style.zIndex = '10000';
            }
        }).then((result) => {
            if (result.isConfirmed) {
                this.submit();
            }
        });
    });
</script>
@endsection