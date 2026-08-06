@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    .font-nunito, .swal2-popup, .swal2-title, .swal2-html-container { font-family: 'Nunito', sans-serif !important; }
    
    .panel-box {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    }
    .dark .panel-box {
        background-color: #1e293b;
        border-color: #334155;
    }

    /* VIEWPORT KAMERA TINGGI & LEGA UNTUK PEMBACAAN MAKSIMAL */
    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617 !important;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 580px;
        height: 580px;
        border: 2px solid #334155;
    }

    /* Penyesuaian Responsif Layar Smartphone / HP */
    @media (max-width: 640px) {
        .scanner-viewport {
            min-height: 520px;
            height: 520px;
        }
    }

    #reader {
        width: 100% !important;
        height: 100% !important;
        background-color: #020617 !important;
        border: none !important;
    }
    
    #reader video {
        object-fit: cover !important;
        width: 100% !important;
        height: 100% !important;
        border-radius: 8px;
    }

    #reader__border_element, #reader__dashboard {
        display: none !important;
    }

    /* Laser Line Merah khusus Terminal Disposal */
    .laser-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: rgba(229, 30, 67, 0.9);
        box-shadow: 0 0 15px 2px rgba(229, 30, 67, 0.7);
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
</style>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 font-nunito text-slate-800 dark:text-slate-200">
    
    <!-- HEADER -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-dumpster-fire text-[#E51E43]"></i> Terminal Stock Disposal
            </h2>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Scan hanya menerima barcode hasil Production OUT. Stok tidak dikembalikan ke mana pun dan barcode akan dikunci sebagai Disposal.</p>
        </div>
        <a href="{{ route('stock_eng.transaction.disposal') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-100 dark:bg-slate-800 px-4 py-2 text-xs font-black text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all no-underline tracking-wider uppercase">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>

    <!-- FORM DENGAN ID ACTION KHUSUS -->
    <form id="disposal_main_form" action="{{ route('stock_eng.transaction.disposal.scan.process') }}" method="POST">
        @csrf
        
        <!-- GRID LAYOUT (8 COLUMNS KAMERA & 4 COLUMNS PANEL LOG / PAYLOAD) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- MODULE CAMERA SCANNER (COL-SPAN 8) -->
            <div class="lg:col-span-8 panel-box flex flex-col">
                <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 rounded-t-xl">
                    <h3 class="font-bold text-slate-800 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-camera text-[#E51E43]"></i> Hardware Lensa Live Detector
                    </h3>
                    <span id="badge_method" class="px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 rounded-lg flex items-center gap-1.5 uppercase tracking-wider">
                        <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY
                    </span>
                </div>
                
                <div class="p-6 flex-1 flex flex-col">
                    <div class="scanner-viewport shadow-inner">
                        <div id="reader"></div>
                        <div id="scan-laser-line" class="laser-line"></div>
                        
                        <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-0">
                            <i class="fa-solid fa-qrcode text-5xl text-slate-700 mb-3 animate-pulse"></i>
                            <span class="text-slate-500 text-xs font-mono font-bold uppercase">LENSA READY TO SCAN</span>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <button type="button" id="start-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-[#E51E43] hover:opacity-90 px-6 py-3 text-xs font-black text-white shadow-md transition-all uppercase tracking-wider cursor-pointer active:scale-95">
                            <i class="fa-solid fa-video"></i> Aktifkan Kamera
                        </button>
                        <button type="button" id="stop-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-lg bg-slate-600 hover:bg-slate-500 px-6 py-3 text-xs font-black text-white shadow-md transition-all uppercase tracking-wider hidden cursor-pointer active:scale-95">
                            <i class="fa-solid fa-power-off"></i> Matikan Kamera
                        </button>
                    </div>
                </div>
            </div>

            <!-- PANEL COL-SPAN 4 (Dua Panel Terpisah Atas-Bawah) -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                
                <!-- GRID ATAS: LOG TERMINAL REALTIME -->
                <div id="status-container" class="panel-box overflow-hidden transition-colors duration-300">
                    <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
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
                                    Arahkan barcode hasil Production OUT ke lensa kamera untuk mengunci status barang menjadi Disposal.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRID BAWAH: MONITOR PAYLOAD SCAN TERAKHIR -->
                <div class="panel-box overflow-hidden flex flex-col justify-between">
                    <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-receipt text-[#E51E43]"></i> Payload Scan Terakhir
                        </h3>
                        <span id="scan-timestamp" class="text-[10px] font-mono text-[#E51E43] font-bold">--:--:--</span>
                    </div>
                    <div class="p-5 flex flex-col gap-4">
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-900 p-4 text-slate-200 flex flex-col justify-between shadow-inner">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hasil Scan ID / Barcode</div>
                            <div class="py-2">
                                <div id="last-scanned-code" class="text-xs font-mono font-bold text-rose-400 break-all bg-slate-950 p-3.5 rounded-lg border border-slate-800 text-center tracking-wider">
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
                            <span class="text-rose-600 dark:text-rose-400 font-black uppercase tracking-wider">
                                DISPOSAL (PERMANENT LOCK)
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </form>
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
    const mainForm = document.getElementById('disposal_main_form');

    const scanConfig = { fps: 20, aspectRatio: 1.0 };

    // Kunci Form Biar Gak Ada Bypass Enter Sembarangan
    if(mainForm) {
        mainForm.addEventListener('submit', function(e) { e.preventDefault(); });
    }

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

    // Dynamic State Controller log header
    function changeUIStatus(status, labelText, descText) {
        const badge = document.getElementById('badge_method');
        const statusContainer = document.getElementById('status-container');
        const iconBox = document.getElementById('status-icon-box');
        const mainIcon = document.getElementById('main-status-icon');
        
        if(document.getElementById('status-title')) document.getElementById('status-title').innerText = labelText;
        if(document.getElementById('status-desc')) document.getElementById('status-desc').innerText = descText;

        if(!badge || !statusContainer || !iconBox || !mainIcon) return;

        badge.className = "px-2.5 py-1 text-[10px] font-black rounded-lg flex items-center gap-1.5 uppercase tracking-wider ";
        iconBox.className = "flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ";

        if (status === 'VALID') {
            badge.innerHTML = `<i class="fa-solid fa-check"></i> OK`;
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border', 'border-emerald-200');
            statusContainer.className = "panel-box overflow-hidden border-emerald-400 bg-emerald-50/30";
            iconBox.classList.add('bg-emerald-100', 'text-emerald-600');
            mainIcon.className = "fa-solid fa-square-check text-lg";
        } else if (status === 'PROCESSING') {
            badge.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> SYNC`;
            badge.classList.add('bg-rose-100', 'text-rose-700', 'border', 'border-rose-200');
            statusContainer.className = "panel-box overflow-hidden border-rose-400 bg-rose-50/30";
            iconBox.classList.add('bg-rose-100', 'text-rose-600');
            mainIcon.className = "fa-solid fa-rotate fa-spin text-lg";
        } else {
            badge.innerHTML = `<i class="fa-solid fa-xmark"></i> DITOLAK`;
            badge.classList.add('bg-rose-100', 'text-rose-700', 'border', 'border-rose-200');
            statusContainer.className = "panel-box overflow-hidden border-rose-400 bg-rose-50/30";
            iconBox.classList.add('bg-rose-100', 'text-rose-600');
            mainIcon.className = "fa-solid fa-triangle-exclamation text-lg";
        }
    }

    // 🌟 REVOLUSI CORE: MENGGUNAKAN Jalur AJAX FETCH (MURNI SAMA DENGAN LOGIKA ORIGINAL) 🌟
    function executeDisposalTransaction(rawCode, methodType = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
        updateMonitorDisplay(cleanCode, 'PROCESSING');
        changeUIStatus('PROCESSING', 'MEMPROSES DISPOSAL...', `Mengirim payload: ${cleanCode}`);

        // Tampilkan loading popup ramah di depan kamera
        Swal.fire({
            title: 'Memproses Disposal...',
            html: `<div class="text-xs font-mono text-rose-600 bg-rose-50 dark:bg-rose-950/30 p-2 rounded break-all font-bold mb-1">${cleanCode}</div>
                   <p class="text-[11px] text-slate-400"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Mengunci lifecycle barcode...</p>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '350px',
            didOpen: () => { Swal.showLoading(); }
        });

        // Kirim data via fetch API (Jalur Belakang Murni Original)
        fetch(mainForm.action, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                barcode_raw: cleanCode,
                process_type: methodType
            })
        })
        .then(response => response.json())
        .then(data => {
            if(manualInput) manualInput.value = "";
            isProcessing = false;

            if (data.success) {
                updateMonitorDisplay(cleanCode, 'SUCCESS');
                changeUIStatus('VALID', 'DISPOSAL SUKSES', data.message || 'Barcode berhasil dikunci sebagai Disposal.');
                
                Swal.fire({
                    icon: 'success',
                    title: 'Disposal Sukses!',
                    text: data.message,
                    timer: 2200,
                    showConfirmButton: false,
                    confirmButtonColor: '#E51E43'
                });
            } else {
                updateMonitorDisplay(cleanCode, 'FAILED');
                changeUIStatus('INVALID', 'TRANSAKSI DITOLAK', data.message || 'Format barcode ditolak.');

                Swal.fire({
                    icon: data.message && data.message.includes('sudah berstatus DISPOSAL') ? 'warning' : 'error',
                    title: data.message && data.message.includes('sudah berstatus DISPOSAL') ? 'Barcode Sudah Mati!' : 'Transaksi Ditolak!',
                    text: data.message,
                    confirmButtonColor: '#E51E43'
                });
            }
        })
        .catch(err => {
            isProcessing = false;
            updateMonitorDisplay(cleanCode, 'ERROR');
            changeUIStatus('INVALID', 'SYSTEM ERROR', 'Gagal terhubung ke server lokal.');

            Swal.fire({
                icon: 'error',
                title: 'System Error!',
                text: 'Gagal terhubung ke server. Periksa koneksi lokal pabrik.',
                confirmButtonColor: '#E51E43'
            });
        })
        .finally(() => {
            setTimeout(() => {
                resetSystemState();
            }, 1000);
        });
    }

    // STATE CONTROLLER INTERFACE
    function resetSystemState() {
        if(photoInput) photoInput.value = "";
        if(manualInput) manualInput.value = "";
        const lbl = document.getElementById('label-upload-status');
        if(lbl) lbl.innerText = "Upload Gambar QR";
        
        if(!isCameraRunning) {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-black bg-slate-100 text-slate-700 border border-slate-200 rounded-lg flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-expand text-[10px] animate-pulse"></i> SCANNER ACTIVE`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-black bg-[#E51E43] text-white rounded-lg flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'block';
        }
    }

    // BUTTON CONTROLLER
    startCamBtn.addEventListener('click', () => {
        if (!isCameraRunning) {
            html5QrCode.start(
                { facingMode: "environment" }, 
                scanConfig, 
                (decodedText) => { 
                    if (!isProcessing) {
                        executeDisposalTransaction(decodedText.trim(), 'scan');
                    }
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
                Swal.fire({ icon: 'error', title: 'Akses Ditolak!', text: 'Kamera gagal aktif. Pastikan menggunakan HTTPS.' });
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

    // LASER GUN SCAN & ENTER DETECTOR
    if(btnManual) {
        btnManual.addEventListener('click', () => {
            const val = manualInput ? manualInput.value.trim() : '';
            if(val) executeDisposalTransaction(val, 'manual');
        });
    }

    if(manualInput) {
        manualInput.addEventListener('keypress', (e) => {
            if(e.key === 'Enter') {
                e.preventDefault();
                if(btnManual) btnManual.click();
            }
        });
    }

    // UPLOAD IMAGE FILE SENSOR
    if(photoInput) {
        photoInput.addEventListener('change', function(e) {
            if (e.target.files.length === 0) return;
            const file = e.target.files[0];
            const lbl = document.getElementById('label-upload-status');
            if(lbl) lbl.innerText = "File Dimuat";

            html5QrCode.scanFile(file, true)
                .then(decodedText => { executeDisposalTransaction(decodedText.trim(), 'scan'); })
                .catch(err => { 
                    Swal.fire({ icon: 'error', title: 'Gagal Membaca!', text: 'QR Code tidak tajam atau tidak terdeteksi.' }); 
                    resetSystemState();
                });
        });
    }
</script>
@endsection