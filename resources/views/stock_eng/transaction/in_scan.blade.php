@extends('admin')

@section('content')
<!-- Import Font Profesional ala Dashboard -->
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

    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        border: 1px solid #334155;
    }

    /* Laser Line Emerald untuk Mode Stock IN */
    .laser-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: rgba(16, 185, 129, 0.8);
        box-shadow: 0 0 15px 2px rgba(16, 185, 129, 0.6);
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
    #reader video { object-fit: cover !important; border-radius: 8px; }
</style>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 tail-admin-ui text-slate-800 dark:text-slate-200">
    
    <!-- HEADER DASHBOARD -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white uppercase tracking-wide">
                <i class="fa-solid fa-qrcode text-emerald-600 dark:text-emerald-400 mr-2"></i> Terminal Stock In Otomatis
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">1 Barcode scan langsung menambah 1 stock gudang & otomatis mengikat Material Received ID terkait.</p>
        </div>
        <a href="{{ route('eng.in') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all no-underline">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- GRID LAYOUT -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- MODULE UTAMA: SCANNER KAMERA -->
        <div class="lg:col-span-8 panel-box flex flex-col">
            <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 rounded-t-xl">
                <h3 class="font-semibold text-slate-800 dark:text-white text-sm">Hardware Lensa Live</h3>
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
                        <span class="text-slate-500 text-xs font-mono">LENSA OFFLINE</span>
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

        <!-- PANEL DIAGNOSTIC & FAILSAFE -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            
            <!-- PANEL SYSTEM LOG -->
            <div id="status-container" class="panel-box overflow-hidden transition-colors duration-300">
                <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm">Log Terminal Realtime</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <div id="status-icon-box" class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                            <i class="fa-solid fa-microchip text-lg" id="main-status-icon"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white" id="status-title">Mesin Ready</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" id="status-desc">
                                Jalankan lensa kamera atau gunakan input manual untuk mengeksekusi penerimaan/pemasukan barang.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- MANUAL KEYBOARD MODE & FAILSAFE -->
            <div class="panel-box flex flex-col">
                <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                        <i class="fa-solid fa-keyboard text-amber-500"></i> Mode Input Manual / Keyboard
                    </h3>
                </div>
                <div class="p-5 flex flex-col gap-4">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Ketik / Tembak Barcode ID IN</label>
                        <div class="flex gap-2">
                            <input type="text" id="manual_barcode" placeholder="Contoh: TXENGIN04082600001" class="flex-1 rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-xs text-slate-950 dark:text-white focus:outline-none focus:border-emerald-500">
                            <button type="button" id="btn_manual_submit" class="bg-emerald-600 hover:bg-emerald-700 px-4 py-2 rounded-lg text-white text-xs font-semibold transition-all cursor-pointer">
                                Kirim
                            </button>
                        </div>
                    </div>

                    <div class="border-t border-slate-200 dark:border-slate-700 pt-3">
                        <p class="text-[10px] text-slate-400 mb-2">Alternatif: Deteksi via upload berkas gambar QR / Barcode</p>
                        <label for="upload-image-scan" class="flex flex-col items-center justify-center w-full p-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100 cursor-pointer transition-all group">
                            <i class="fa-solid fa-cloud-arrow-up text-emerald-500 mb-1"></i>
                            <span class="text-xs font-medium text-emerald-600" id="label-upload-status">Browse Image</span>
                            <input type="file" id="upload-image-scan" accept="image/*" class="hidden" />
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>
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

    const scanConfig = {
        fps: 20, 
        qrbox: function(width, height) {
            const minEdge = Math.min(width, height);
            return { width: Math.floor(minEdge * 0.75), height: Math.floor(minEdge * 0.75) };
        },
        aspectRatio: 1.0
    };

    // FUNGSI INTI AJAX SUBMIT SCAN IN
    async function processAjaxStockIn(rawCode, mode = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
        changeUIStatus('PROCESSING', 'MEMBACA...', `Mengirim data: ${cleanCode}`);

        Swal.fire({
            title: 'Menambah Stok Rak...',
            html: `<div class="text-xs font-mono text-emerald-600 break-all">${cleanCode}</div>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '320px',
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('barcode_scan', cleanCode); // Dikirim ke StockInEngineeringController
        formData.append('process_type', mode);       // Mengindikasikan tipe scan/manual
        formData.append('comment', `Automated Stock IN via scan.`);

        try {
            const response = await fetch("{{ route('eng.in.store') }}", {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}', 
                    'Accept': 'application/json' 
                },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                changeUIStatus('VALID', 'BERHASIL DITAMBAH', result.message);
                
                Swal.fire({
                    icon: 'success',
                    title: 'Stok Bertambah (+1)',
                    text: result.message,
                    timer: 1500,
                    showConfirmButton: false,
                    width: '350px'
                });
            } else {
                changeUIStatus('INVALID', 'TRANSAKSI DITOLAK', result.message);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Scan In',
                    text: result.message,
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            changeUIStatus('INVALID', 'KONEKSI LOSS', 'Koneksi ke database terputus.');
            Swal.fire({ icon: 'error', title: 'Network Error', text: 'Periksa koneksi jaringan/server Anda.' });
        } finally {
            setTimeout(() => {
                resetSystemState();
            }, 1000);
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
        photoInput.value = "";
        manualInput.value = "";
        document.getElementById('label-upload-status').innerText = "Browse Image";
        
        if(!isCameraRunning) {
            document.getElementById('status-container').className = "panel-box overflow-hidden";
            document.getElementById('status-icon-box').className = "flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500";
            document.getElementById('main-status-icon').className = "fa-solid fa-microchip text-lg";
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 rounded flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-video text-[10px] animate-pulse"></i> LIVE SCANNING`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-bold bg-emerald-600 text-white rounded flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'block';
        }
    }

    // ACTIONS EVENT
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
                Swal.fire({ icon: 'warning', title: 'Izin Kamera Blokir', text: 'Gunakan protokol HTTPS atau jalankan via localhost.' });
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
    btnManual.addEventListener('click', () => {
        const val = manualInput.value.trim();
        if(val) processAjaxStockIn(val, 'manual');
    });

    manualInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            btnManual.click();
        }
    });

    // DEKRIPSI FILE GAMBAR BARCODE
    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length === 0) return;
        const file = e.target.files[0];
        document.getElementById('label-upload-status').innerText = "File terunggah";

        html5QrCode.scanFile(file, true)
            .then(decodedText => { processAjaxStockIn(decodedText.trim(), 'scan'); })
            .catch(err => { 
                Swal.fire({ icon: 'error', title: 'Gagal Dekripsi', text: 'Format Barcode/QR di dalam foto tidak jelas atau salah.' }); 
                resetSystemState();
            });
    });
</script>
@endsection