@extends('admin')

@section('content')
@php($isProductionIn = ($scan_mode ?? 'engineering') === 'production')

<!-- Import Font Profesional ala Dashboard Stock Out -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .tail-admin-ui { font-family: 'Inter', sans-serif; }
    
    .panel-box {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .dark .panel-box {
        background-color: #1e293b;
        border-color: #334155;
    }

    /* VIEWPORT KAMERA TINGGI & LEGA UNTUK PEMBACAAN MAKSIMAL */
    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 580px;
        height: 580px;
        border: 1px solid #334155;
    }

    /* Responsif Layar Smartphone / HP */
    @media (max-width: 640px) {
        .scanner-viewport {
            min-height: 520px;
            height: 520px;
        }
    }

    /* Laser Line Emerald khusus Terminal Stock IN */
    .laser-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: rgba(16, 185, 129, 0.9);
        box-shadow: 0 0 15px 3px rgba(16, 185, 129, 0.7);
        left: 0;
        top: 0;
        z-index: 10;
        display: none;
        animation: scanAnim 2s ease-in-out infinite;
    }
    @keyframes scanAnim {
        0% { top: 5%; opacity: 0; }
        10% { opacity: 1; }
        90% { opacity: 1; }
        100% { top: 95%; opacity: 0; }
    }

    #reader { width: 100%; height: 100%; }
    #reader video { object-fit: cover !important; border-radius: 8px; width: 100% !important; height: 100% !important; }
</style>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 tail-admin-ui text-slate-800 dark:text-slate-200">
    
    <!-- HEADER DASHBOARD TERMINAL STOCK IN -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white uppercase tracking-wide flex items-center gap-2">
                <i class="fa-solid fa-barcode text-emerald-600 dark:text-emerald-400"></i>
                {{ $isProductionIn ? 'Terminal Stock In Produksi' : 'Terminal Stock In Engineering' }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                {{ $isProductionIn
                    ? 'Scan barcode/QR setelah Production OUT untuk menambah stok pada line dan sparepart yang sudah terdaftar.'
                    : 'Scan barcode/QR untuk menambah stok rak gudang dan mencatat transaksi penerimaan barang.' }}
            </p>
        </div>
        <a href="{{ $isProductionIn ? route('prod.transaction.in') : route('eng.in') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all no-underline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- GRID LAYOUT (8 COLUMNS KAMERA & 4 COLUMNS PANEL LOG / PAYLOAD) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- MODULE UTAMA: SCANNER KAMERA (COL-SPAN 8) -->
        <div class="lg:col-span-8 panel-box flex flex-col">
            <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 rounded-t-xl">
                <h3 class="font-semibold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                    <i class="fa-solid fa-camera text-emerald-600 dark:text-emerald-400"></i> Hardware Lensa Live
                </h3>
                <span id="badge_method" class="px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 rounded flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY
                </span>
            </div>
            
            <div class="p-6 flex-1 flex flex-col">
                <div class="scanner-viewport shadow-inner">
                    <div id="reader"></div>
                    <div id="scan-laser-line" class="laser-line"></div>
                    
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-0">
                        <i class="fa-solid fa-camera-retro text-5xl text-slate-700 mb-3"></i>
                        <span class="text-slate-500 text-xs font-mono uppercase">LENSA OFFLINE</span>
                    </div>
                </div>

                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button type="button" id="start-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-md bg-emerald-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-all uppercase tracking-wide cursor-pointer">
                        <i class="fa-solid fa-video"></i> Aktifkan Lensa
                    </button>
                    <button type="button" id="stop-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-md bg-rose-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 transition-all uppercase tracking-wide hidden cursor-pointer">
                        <i class="fa-solid fa-power-off"></i> Matikan Lensa
                    </button>
                </div>
            </div>
        </div>

        <!-- PANEL COL-SPAN 4 (Dua Panel Terpisah Atas-Bawah) -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            
            <!-- GRID ATAS: LOG TERMINAL REALTIME -->
            <div id="status-container" class="panel-box overflow-hidden transition-colors duration-300">
                <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                        <i class="fa-solid fa-microchip text-slate-500"></i> Log Terminal Realtime
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400">SYNC ONLINE</span>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <div id="status-icon-box" class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                            <i class="fa-solid fa-server text-lg" id="main-status-icon"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white" id="status-title">Mesin Ready</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" id="status-desc">
                                {{ $isProductionIn
                                    ? 'Terminal siap menerima scan barcode setelah Production OUT dan menambah stok line terkait.'
                                    : 'Terminal siap mendeteksi masukan data via scan barcode/QR dari kamera live.' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- GRID BAWAH: MONITOR PAYLOAD SCAN TERAKHIR -->
            <div class="panel-box overflow-hidden flex flex-col justify-between">
                <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-emerald-500"></i> Payload Scan Terakhir
                    </h3>
                    <span id="scan-timestamp" class="text-[10px] font-mono text-emerald-600 dark:text-emerald-400 font-bold">--:--:--</span>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-900 p-4 text-slate-200 flex flex-col justify-between shadow-inner">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hasil Scan ID / Barcode</div>
                        <div class="py-2">
                            <div id="last-scanned-code" class="text-xs font-mono font-bold text-emerald-400 break-all bg-slate-950 p-3.5 rounded-lg border border-slate-800 text-center tracking-wider">
                                WAITING FOR SCAN...
                            </div>
                        </div>
                        <div class="text-[10px] text-slate-400 flex justify-between items-center pt-2 border-t border-slate-800 font-mono">
                            <span>Status Process:</span>
                            <span id="last-scanned-type" class="text-slate-300 font-bold uppercase">IDLE</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/40 text-xs font-medium text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Mode Transaksi</span>
                        <span class="text-slate-800 dark:text-slate-200 font-bold uppercase">
                            {{ $isProductionIn ? 'PRODUCTION IN (+1 LINE)' : 'STOCK IN (+1 RAK)' }}
                        </span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- ELEMEN TERSEMBUNYI UNTUK MENJAGA SCRIPT JS ORIGINAL TETAP JALAN TANPA ERROR -->
<div class="hidden">
    <input type="text" id="manual_barcode">
    <button type="button" id="btn_manual_submit"></button>
    <input type="file" id="upload-image-scan">
    <span id="label-upload-status"></span>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let html5QrCode = new Html5Qrcode("reader");
    let isCameraRunning = false;
    let isProcessing = false;
    
    const photoInput = document.getElementById('upload-image-scan');
    const startCamBtn = document.getElementById('start-cam');
    const stopCamBtn = document.getElementById('stop-cam');
    const laserLine = document.getElementById('scan-laser-line');
    const camPlaceholder = document.getElementById('camera-placeholder');
    const manualInput = document.getElementById('manual_barcode');
    const btnManual = document.getElementById('btn_manual_submit');

    // KONFIGURASI SCANNER ORIGINAL
    const scanConfig = {
        fps: 25, 
        qrbox: function(viewfinderWidth, viewfinderHeight) {
            return {
                width: Math.floor(viewfinderWidth * 0.85),
                height: Math.floor(viewfinderHeight * 0.65)
            };
        },
        formatsToSupport: [
            Html5QrcodeSupportedFormats.QR_CODE,
            Html5QrcodeSupportedFormats.CODE_128,
            Html5QrcodeSupportedFormats.CODE_39,
            Html5QrcodeSupportedFormats.EAN_13,
            Html5QrcodeSupportedFormats.EAN_8,
            Html5QrcodeSupportedFormats.UPC_A,
            Html5QrcodeSupportedFormats.UPC_E,
            Html5QrcodeSupportedFormats.DATA_MATRIX,
            Html5QrcodeSupportedFormats.ITF
        ],
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true 
        }
    };

    // FUNGSI INTI AJAX SUBMIT SCAN IN (MURNI DARI KODE ORIGINAL)
    async function processAjaxStockIn(rawCode, mode = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
        updateMonitorDisplay(cleanCode, 'PROCESSING');
        changeUIStatus('PROCESSING', 'MEMBACA DATA...', `Mengirim payload: ${cleanCode}`);

        // ALERT LOADING PROSES
        Swal.fire({
            title: 'Memproses Stok IN...',
            html: `<div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 break-all mt-2 bg-slate-100 dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 font-sans">${cleanCode}</div>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '340px',
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('barcode_scan', cleanCode);
        formData.append('process_type', mode);
        formData.append('comment', 'AUTOMATED IN');
        formData.append('remark', 'AUTOMATED IN');

        try {
            const response = await fetch("{{ $isProductionIn ? route('prod.transaction.in.store_scan') : route('eng.in.store') }}", {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json' 
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                changeUIStatus('VALID', 'STOK IN BERHASIL', result.message || 'Stok berhasil ditambahkan!');
                updateMonitorDisplay(cleanCode, 'SUCCESS');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Stok Bertambah (+1)',
                    text: result.message || 'Barang berhasil dicatat ke sistem!',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: '360px'
                });
            } else {
                changeUIStatus('INVALID', 'TRANSAKSI DITOLAK', result.message || 'Format barcode tidak terdaftar.');
                updateMonitorDisplay(cleanCode, 'FAILED');

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Scan In',
                    text: result.message || 'Kode barcode/QR tidak valid atau belum terdaftar.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: '360px'
                });
            }
        } catch (error) {
            changeUIStatus('INVALID', 'KONEKSI TERPUTUS', 'Gagal terhubung ke database server.');
            updateMonitorDisplay(cleanCode, 'ERROR');
            
            Swal.fire({ 
                icon: 'error', 
                title: 'Network Error', 
                text: 'Periksa koneksi jaringan atau server Anda.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                width: '360px'
            });
        } finally {
            setTimeout(() => {
                resetSystemState();
            }, 2000);
        }
    }

    // UPDATE DISPLAY MONITOR SCAN TERAKHIR
    function updateMonitorDisplay(code, status) {
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0] + ' WIB';
        
        document.getElementById('last-scanned-code').innerText = code;
        document.getElementById('scan-timestamp').innerText = timeStr;
        
        const typeEl = document.getElementById('last-scanned-type');
        typeEl.innerText = status;
        
        if (status === 'SUCCESS') {
            typeEl.className = "text-emerald-400 font-bold uppercase";
        } else if (status === 'PROCESSING') {
            typeEl.className = "text-amber-400 font-bold uppercase";
        } else {
            typeEl.className = "text-rose-400 font-bold uppercase";
        }
    }

    // INTERFACE DYNAMIC CONTROLLER
    function changeUIStatus(status, labelText, descText) {
        const badge = document.getElementById('badge_method');
        const statusContainer = document.getElementById('status-container');
        const iconBox = document.getElementById('status-icon-box');
        const mainIcon = document.getElementById('main-status-icon');
        
        document.getElementById('status-title').innerText = labelText;
        document.getElementById('status-desc').innerText = descText;

        badge.className = "px-2.5 py-1 text-[10px] font-bold rounded flex items-center gap-1.5 uppercase tracking-wider ";
        iconBox.className = "flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ";

        if (status === 'VALID') {
            badge.innerHTML = `<i class="fa-solid fa-check"></i> OK`;
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border', 'border-emerald-200');
            statusContainer.className = "panel-box overflow-hidden border-emerald-400 bg-emerald-50/30";
            iconBox.classList.add('bg-emerald-100', 'text-emerald-600');
            mainIcon.className = "fa-solid fa-square-check text-lg";
        } else if (status === 'PROCESSING') {
            badge.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> SYNC`;
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border', 'border-emerald-200');
            statusContainer.className = "panel-box overflow-hidden border-emerald-400 bg-emerald-50/30";
            iconBox.classList.add('bg-emerald-100', 'text-emerald-600');
            mainIcon.className = "fa-solid fa-rotate fa-spin text-lg";
        } else {
            badge.innerHTML = `<i class="fa-solid fa-xmark"></i> DITOLAK`;
            badge.classList.add('bg-rose-100', 'text-rose-700', 'border', 'border-rose-200');
            statusContainer.className = "panel-box overflow-hidden border-rose-400 bg-rose-50/30";
            iconBox.classList.add('bg-rose-100', 'text-rose-600');
            mainIcon.className = "fa-solid fa-triangle-exclamation text-lg";
        }
    }

    function resetSystemState() {
        isProcessing = false;
        if(photoInput) photoInput.value = "";
        if(manualInput) manualInput.value = "";
        const labelUpload = document.getElementById('label-upload-status');
        if(labelUpload) labelUpload.innerText = "Browse Image";
        
        if(!isCameraRunning) {
            document.getElementById('status-container').className = "panel-box overflow-hidden";
            document.getElementById('status-icon-box').className = "flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500";
            document.getElementById('main-status-icon').className = "fa-solid fa-server text-lg";
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 rounded flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-video text-[10px] animate-pulse"></i> LIVE SCANNING`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-bold bg-emerald-600 text-white rounded flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'block';
        }
    }

    // ACTIONS EVENT KAMERA LIVE ORIGINAL
    startCamBtn.addEventListener('click', () => {
        if (!isCameraRunning) {
            html5QrCode.start(
                { facingMode: "environment" }, 
                scanConfig, 
                (decodedText) => { 
                    if (!isProcessing) processAjaxStockIn(decodedText.trim(), 'scan'); 
                }
            )
            .then(() => { 
                isCameraRunning = true; 
                startCamBtn.classList.add('hidden');
                stopCamBtn.classList.remove('hidden');
                camPlaceholder.classList.add('hidden');
                resetSystemState();
            })
            .catch(err => {
                Swal.fire({ 
                    icon: 'warning', 
                    title: 'Izin Kamera Diperlukan', 
                    text: 'Pastikan web diakses via HTTPS / localhost dan berikan izin akses kamera.',
                    confirmButtonColor: '#f59e0b'
                });
            });
        }
    });

    stopCamBtn.addEventListener('click', () => {
        if (isCameraRunning) {
            html5QrCode.stop().then(() => { 
                isCameraRunning = false; 
                startCamBtn.classList.remove('hidden');
                stopCamBtn.classList.add('hidden');
                camPlaceholder.classList.remove('hidden');
                resetSystemState();
            });
        }
    });

    // KIRIM MANUAL BY KEYBOARD / USB GUN SCANNER
    if(btnManual) {
        btnManual.addEventListener('click', () => {
            const val = manualInput ? manualInput.value.trim() : '';
            if(val) processAjaxStockIn(val, 'manual');
        });
    }

    if(manualInput) {
        manualInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter' && btnManual) {
                btnManual.click();
            }
        });
    }

    // DEKRIPSI FILE GAMBAR
    if(photoInput) {
        photoInput.addEventListener('change', function(e) {
            if (e.target.files.length === 0) return;
            const file = e.target.files[0];
            const lbl = document.getElementById('label-upload-status');
            if(lbl) lbl.innerText = "File terunggah";

            html5QrCode.scanFile(file, true)
                .then(decodedText => { processAjaxStockIn(decodedText.trim(), 'scan'); })
                .catch(err => { 
                    Swal.fire({ icon: 'error', title: 'Gagal Dekripsi', text: 'Format Barcode/QR di dalam foto tidak jelas atau salah.' }); 
                    resetSystemState();
                });
        });
    }
</script>
@endsection