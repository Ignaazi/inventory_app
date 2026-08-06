@extends('admin')

@section('content')
{{-- Load Google Fonts Nunito, FontAwesome & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://unpkg.com/html5-qrcode"></script>

<style>
    .font-nunito, .swal2-popup { font-family: 'Nunito', sans-serif !important; }
    
    .panel-box {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
    }
    .dark .panel-box {
        background-color: #0f172a;
        border-color: #1e293b;
    }

    /* VIEWPORT KAMERA TINGGI & LEGA UNTUK PEMBACAAN MAKSIMAL */
    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 580px;
        height: 580px;
        border: 1px solid #1e293b;
    }

    /* Responsif Layar Smartphone / HP */
    @media (max-width: 640px) {
        .scanner-viewport {
            min-height: 520px;
            height: 520px;
        }
    }

    .laser-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: rgba(245, 158, 11, 0.85);
        box-shadow: 0 0 15px 2px rgba(245, 158, 11, 0.7);
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
    #reader video { object-fit: cover !important; border-radius: 12px; width: 100% !important; height: 100% !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300 text-slate-800 dark:text-slate-200">
    
    {{-- HEADER TERMINAL SCAN RETURN --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight uppercase flex items-center gap-2">
                <i class="fa-solid fa-rotate-left text-amber-500"></i> Terminal Scan Stock Return
            </h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">
                Scan barcode untuk membatalkan/mengembalikan stok salah terima di lantai produksi.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ url('/prod/transaction/return') }}" 
               class="inline-flex items-center justify-center gap-2 h-10 rounded-xl bg-slate-200 dark:bg-slate-800 px-5 text-xs font-black text-slate-700 dark:text-slate-300 hover:bg-slate-300 dark:hover:bg-slate-700 transition-all font-nunito uppercase no-underline cursor-pointer">
                <i class="fa-solid fa-arrow-left"></i> Kembali Ke History
            </a>
        </div>
    </div>

    {{-- GRID MODULE SCANNER & DIAGNOSTIC --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 mb-6">
        
        {{-- MODULE KAMERA SCANNER (COL-SPAN 8) --}}
        <div class="lg:col-span-8 panel-box flex flex-col p-4 md:p-5">
            <div class="mb-3 flex justify-between items-center pb-3 border-b border-gray-100 dark:border-slate-800">
                <h3 class="font-black text-slate-800 dark:text-white text-xs md:text-sm uppercase tracking-wide flex items-center gap-2">
                    <i class="fa-solid fa-camera text-amber-500"></i> Live Camera Return Scanner
                </h3>
                <span id="badge_method" class="px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 rounded-lg flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY
                </span>
            </div>
            
            <div class="flex-1 flex flex-col">
                <div class="scanner-viewport shadow-inner">
                    <div id="reader"></div>
                    <div id="scan-laser-line" class="laser-line"></div>
                    
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-0">
                        <i class="fa-solid fa-camera text-4xl text-slate-700 mb-2"></i>
                        <span class="text-slate-500 text-xs font-mono font-bold uppercase">KAMERA NONAKTIF</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <button type="button" id="start-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 px-5 py-2.5 text-xs font-black text-white shadow-md shadow-orange-500/20 hover:brightness-110 active:scale-95 transition-all uppercase tracking-wider cursor-pointer">
                        <i class="fa-solid fa-video"></i> Aktifkan Kamera
                    </button>
                    <button type="button" id="stop-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-rose-600 px-5 py-2.5 text-xs font-black text-white shadow-md shadow-rose-600/20 hover:bg-rose-700 active:scale-95 transition-all uppercase tracking-wider hidden cursor-pointer">
                        <i class="fa-solid fa-power-off"></i> Matikan Kamera
                    </button>
                </div>
            </div>
        </div>

        {{-- DIAGNOSTIC LOG & PAYLOAD MONITOR (COL-SPAN 4) --}}
        <div class="lg:col-span-4 flex flex-col gap-5">
            
            {{-- LOG TERMINAL --}}
            <div id="status-container" class="panel-box p-4 md:p-5 transition-colors duration-300">
                <div class="flex justify-between items-center border-b border-gray-100 dark:border-slate-800 pb-3 mb-3">
                    <h3 class="font-black text-slate-800 dark:text-white text-xs md:text-sm uppercase tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-microchip text-slate-500"></i> Terminal Log Realtime
                    </h3>
                    <span class="text-[10px] font-bold text-slate-400">SYNC ONLINE</span>
                </div>
                <div class="flex items-start gap-3">
                    <div id="status-icon-box" class="flex-shrink-0 w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                        <i class="fa-solid fa-server text-lg" id="main-status-icon"></i>
                    </div>
                    <div>
                        <h4 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white" id="status-title">Terminal Ready</h4>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed" id="status-desc">
                            Arahkan barcode ke lensa kamera untuk membatalkan/mengembalikan ketersediaan stok komponen.
                        </p>
                    </div>
                </div>
            </div>

            {{-- MONITOR PAYLOAD SCAN TERAKHIR --}}
            <div class="panel-box p-4 md:p-5 flex flex-col justify-between">
                <div class="flex justify-between items-center border-b border-gray-100 dark:border-slate-800 pb-3 mb-3">
                    <h3 class="font-black text-slate-800 dark:text-white text-xs md:text-sm uppercase tracking-wide flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-amber-500"></i> Payload Scan Terakhir
                    </h3>
                    <span id="scan-timestamp" class="text-[10px] font-mono text-amber-600 dark:text-amber-400 font-bold">--:--:--</span>
                </div>
                <div class="flex flex-col gap-4">
                    <div class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-900 p-4 text-slate-200 flex flex-col justify-between shadow-inner">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hasil Scan ID / Barcode</div>
                        <div class="py-2">
                            <div id="last-scanned-code" class="text-xs font-mono font-bold text-amber-400 break-all bg-slate-950 p-3.5 rounded-lg border border-slate-800 text-center tracking-wider">
                                WAITING FOR SCAN...
                            </div>
                        </div>
                        <div class="text-[10px] text-slate-400 flex justify-between items-center pt-2 border-t border-slate-800 font-mono">
                            <span>Status Process:</span>
                            <span id="last-scanned-type" class="text-slate-300 font-bold uppercase">IDLE</span>
                        </div>
                    </div>

                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50 dark:bg-slate-900/40 text-xs font-bold text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Mode Transaksi</span>
                        <span class="text-amber-600 dark:text-amber-400 font-extrabold uppercase">
                            PRODUCTION RETURN (+1 STOK)
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

    // UPDATE DISPLAY MONITOR SCAN TERAKHIR
    function updateMonitorDisplay(code, status) {
        const now = new Date();
        const timeStr = now.toTimeString().split(' ')[0] + ' WIB';
        
        const codeEl = document.getElementById('last-scanned-code');
        const timeEl = document.getElementById('scan-timestamp');
        const typeEl = document.getElementById('last-scanned-type');

        if(codeEl) codeEl.innerText = code;
        if(timeEl) timeEl.innerText = timeStr;
        
        if(typeEl) {
            typeEl.innerText = status;
            if (status === 'SUCCESS') {
                typeEl.className = "text-emerald-400 font-bold uppercase";
            } else if (status === 'PROCESSING') {
                typeEl.className = "text-amber-400 font-bold uppercase";
            } else {
                typeEl.className = "text-rose-400 font-bold uppercase";
            }
        }
    }

    // AJAX SUBMIT RETURN (LOGIKA ORIGINAL 100% UTUH)
    async function processAjaxStockReturn(rawCode, mode = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
        updateMonitorDisplay(cleanCode, 'PROCESSING');
        changeUIStatus('PROCESSING', 'MEMBACA...', `Mengirim data return: ${cleanCode}`);

        Swal.fire({
            title: 'Mengurangi Stok Produksi...',
            html: `<div class="text-xs font-mono text-amber-600 font-bold break-all">${cleanCode}</div>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '320px',
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('barcode_scan', cleanCode);
        formData.append('process_type', mode);
        formData.append('remark', `Automated Stock RETURN via ${mode}`);

        try {
            const response = await fetch("{{ url('/prod/transaction/return/store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                changeUIStatus('VALID', 'STOK DIKEMBALIKAN', result.message);
                updateMonitorDisplay(cleanCode, 'SUCCESS');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Return Sukses!',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false,
                    width: '350px'
                }).then(() => {
                    window.location.reload();
                });
            } else {
                changeUIStatus('INVALID', 'RETURN DITOLAK', result.message);
                updateMonitorDisplay(cleanCode, 'FAILED');

                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Return',
                    text: result.message,
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            changeUIStatus('INVALID', 'KONEKSI LOSS', 'Koneksi ke database terputus.');
            updateMonitorDisplay(cleanCode, 'ERROR');

            Swal.fire({ icon: 'error', title: 'Network Error', text: 'Periksa jaringan/server Anda.' });
        } finally {
            setTimeout(() => {
                resetSystemState();
            }, 1000);
        }
    }

    function changeUIStatus(status, labelText, descText) {
        const badge = document.getElementById('badge_method');
        const statusContainer = document.getElementById('status-container');
        const iconBox = document.getElementById('status-icon-box');
        const mainIcon = document.getElementById('main-status-icon');
        
        document.getElementById('status-title').innerText = labelText;
        document.getElementById('status-desc').innerText = descText;

        if (status === 'VALID') {
            badge.innerHTML = `<i class="fa-solid fa-check"></i> RETURN OK`;
            badge.className = "px-2.5 py-1 text-[10px] font-black rounded-lg flex items-center gap-1.5 uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200";
            statusContainer.className = "panel-box p-4 md:p-5 border-emerald-400 bg-emerald-50/30";
            iconBox.className = "flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center bg-emerald-100 text-emerald-600";
            mainIcon.className = "fa-solid fa-square-check text-lg";
        } else if (status === 'PROCESSING') {
            badge.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> SYNC`;
            badge.className = "px-2.5 py-1 text-[10px] font-black rounded-lg flex items-center gap-1.5 uppercase tracking-wider bg-amber-100 text-amber-700 border border-amber-200";
            statusContainer.className = "panel-box p-4 md:p-5 border-amber-400 bg-amber-50/30";
            iconBox.className = "flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center bg-amber-100 text-amber-600";
            mainIcon.className = "fa-solid fa-rotate fa-spin text-lg";
        } else {
            badge.innerHTML = `<i class="fa-solid fa-xmark"></i> DITOLAK`;
            badge.className = "px-2.5 py-1 text-[10px] font-black rounded-lg flex items-center gap-1.5 uppercase tracking-wider bg-rose-100 text-rose-700 border border-rose-200";
            statusContainer.className = "panel-box p-4 md:p-5 border-rose-400 bg-rose-50/30";
            iconBox.className = "flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center bg-rose-100 text-rose-600";
            mainIcon.className = "fa-solid fa-triangle-exclamation text-lg";
        }
    }

    function resetSystemState() {
        isProcessing = false;
        if(photoInput) photoInput.value = "";
        if(manualInput) manualInput.value = "";
        const labelUpload = document.getElementById('label-upload-status');
        if(labelUpload) labelUpload.innerText = "Upload File Barcode";
        
        if(!isCameraRunning) {
            document.getElementById('status-container').className = "panel-box p-4 md:p-5";
            document.getElementById('status-icon-box').className = "flex-shrink-0 w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500";
            document.getElementById('main-status-icon').className = "fa-solid fa-microchip text-lg";
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 rounded-lg flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-video text-[10px] animate-pulse"></i> LIVE SCANNING`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-black bg-amber-500 text-white rounded-lg flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'block';
        }
    }

    // EVENT LISTENERS
    startCamBtn.addEventListener('click', () => {
        if (!isCameraRunning) {
            html5QrCode.start(
                { facingMode: "environment" }, 
                scanConfig, 
                (decodedText) => { 
                    if (!isProcessing) processAjaxStockReturn(decodedText.trim(), 'scan'); 
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

    if(btnManual) {
        btnManual.addEventListener('click', () => {
            const val = manualInput ? manualInput.value.trim() : '';
            if(val) processAjaxStockReturn(val, 'manual');
        });
    }

    if(manualInput) {
        manualInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter' && btnManual) {
                btnManual.click();
            }
        });
    }

    if(photoInput) {
        photoInput.addEventListener('change', function(e) {
            if (e.target.files.length === 0) return;
            const file = e.target.files[0];
            const lbl = document.getElementById('label-upload-status');
            if(lbl) lbl.innerText = "File terunggah";

            html5QrCode.scanFile(file, true)
                .then(decodedText => { processAjaxStockReturn(decodedText.trim(), 'scan'); })
                .catch(err => { 
                    Swal.fire({ icon: 'error', title: 'Gagal Dekripsi', text: 'Format Barcode/QR tidak terdeteksi.' }); 
                    resetSystemState();
                });
        });
    }
</script>
@endsection