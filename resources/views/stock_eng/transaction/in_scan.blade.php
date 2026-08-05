@extends('admin')

@section('content')
{{-- Load Google Font Nunito, FontAwesome & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* UTAMA FONT NUNITO */
    .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container { 
        font-family: 'Nunito', sans-serif !important; 
    }
    
    /* BINGKAI GRID BIRU TUA SECARA PERSISI (EFEK 3D CLEAN) */
    .panel-box-3d {
        background-color: #ffffff;
        border: 1.5px solid #1e3a8a; /* Blue-900 Border */
        border-radius: 16px;
        box-shadow: 0 4px 12px -2px rgba(30, 58, 138, 0.12), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.25s ease-in-out;
    }
    .dark .panel-box-3d {
        background-color: #0f172a;
        border-color: #1e40af; /* Dark Blue Border */
        box-shadow: 0 4px 14px -2px rgba(0, 0, 0, 0.5);
    }

    /* KOTAK VIEWPORT KAMERA */
    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        aspect-ratio: 4/3;
        width: 100%;
        border: 1.5px solid #1e3a8a;
    }

    /* Laser Line Emerald untuk Mode Stock IN */
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
    #reader video { object-fit: cover !important; border-radius: 10px; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">
    
    <!-- HEADER TERMINAL SCAN -->
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-emerald-500"></i> Terminal Stock In Otomatis
            </h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                Scan barcode/QR untuk menambah stok rak gudang dan mencatat transaksi penerimaan barang.
            </p>
        </div>
        <div>
            <a href="{{ route('eng.in') }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-white dark:bg-slate-900 px-4 py-2.5 text-xs font-black text-slate-700 dark:text-slate-200 border-[1.5px] border-blue-900/60 dark:border-blue-800 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all shadow-sm no-underline active:scale-95">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
            </a>
        </div>
    </div>

    <!-- GRID LAYOUT (1 GRID KAMERA & 2 GRID LOG REALTIME) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 font-nunito">
        
        <!-- GRID 1: LENSA KAMERA SCANNER (RESPONSIVE: COL-SPAN 7 DI DESKTOP) -->
        <div class="lg:col-span-7 panel-box-3d flex flex-col overflow-hidden">
            <div class="border-b-[1.5px] border-blue-900/40 dark:border-blue-800/60 px-5 py-3.5 flex justify-between items-center bg-slate-50/80 dark:bg-slate-900/80">
                <h3 class="font-black text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-camera text-emerald-500"></i> Lensa Kamera Live
                </h3>
                <span id="badge_method" class="px-3 py-1 text-[10px] font-black bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700 rounded-md flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY
                </span>
            </div>
            
            <div class="p-4 md:p-5 flex-1 flex flex-col justify-between gap-4">
                {{-- VIEWPORT KAMERA --}}
                <div class="scanner-viewport shadow-inner">
                    <div id="reader"></div>
                    <div id="scan-laser-line" class="laser-line"></div>
                    
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-950 z-0 text-center p-4">
                        <div class="w-16 h-16 rounded-full bg-slate-900 border border-blue-900/50 flex items-center justify-center mb-3 shadow-inner">
                            <i class="fa-solid fa-camera-retro text-2xl text-slate-500"></i>
                        </div>
                        <span class="text-slate-300 text-xs font-black uppercase tracking-wider">Lensa Offline</span>
                        <p class="text-[11px] text-slate-500 font-bold mt-1">Klik tombol di bawah untuk mengaktifkan kamera scanner</p>
                    </div>
                </div>

                {{-- TOMBOL CONTROL KAMERA (GRADIENT EMERALD) --}}
                <div class="w-full pt-1">
                    <button type="button" id="start-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 px-6 py-3.5 text-xs font-black text-white shadow-lg hover:opacity-90 active:scale-95 transition-all uppercase tracking-wider cursor-pointer border border-emerald-400/30">
                        <i class="fa-solid fa-video text-sm"></i> Aktifkan Lensa Kamera
                    </button>
                    <button type="button" id="stop-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 px-6 py-3.5 text-xs font-black text-white shadow-lg hover:opacity-90 active:scale-95 transition-all uppercase tracking-wider hidden cursor-pointer border border-rose-400/30">
                        <i class="fa-solid fa-power-off text-sm"></i> Matikan Lensa Kamera
                    </button>
                </div>
            </div>
        </div>

        <!-- GRID 2: LOG TERMINAL REALTIME & INFORMASI SYSTEM (RESPONSIVE: COL-SPAN 5 DI DESKTOP) -->
        <div class="lg:col-span-5 flex flex-col gap-5">
            
            <!-- PANEL LOG SYSTEM -->
            <div id="status-container" class="panel-box-3d flex flex-col overflow-hidden transition-colors duration-300">
                <div class="border-b-[1.5px] border-blue-900/40 dark:border-blue-800/60 px-5 py-3.5 bg-slate-50/80 dark:bg-slate-900/80 flex justify-between items-center">
                    <h3 class="font-black text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-terminal text-blue-600 dark:text-blue-400"></i> Log Terminal Realtime
                    </h3>
                    <span class="text-[10px] font-mono font-bold text-slate-400">SYNC ONLINE</span>
                </div>
                
                <div class="p-5 flex-1 flex flex-col justify-between gap-4">
                    <div class="flex items-start gap-3.5 bg-slate-50 dark:bg-slate-900/60 p-3.5 rounded-xl border border-slate-200 dark:border-slate-800">
                        <div id="status-icon-box" class="shrink-0 w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-500 shadow-sm">
                            <i class="fa-solid fa-microchip text-lg" id="main-status-icon"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wide" id="status-title">Mesin Standby</h4>
                            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" id="status-desc">
                                Terminal siap mendeteksi masukan data via scan barcode/QR dari kamera live.
                            </p>
                        </div>
                    </div>

                    {{-- MONITOR DISPLAY HASIL SCAN TERAKHIR --}}
                    <div class="rounded-xl border border-blue-900/50 bg-slate-950 p-4 font-mono text-slate-300 flex flex-col justify-between min-h-[140px] shadow-inner">
                        <div class="flex justify-between items-center border-b border-slate-800 pb-2">
                            <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest">Last Scanned Payload</span>
                            <span id="scan-timestamp" class="text-[10px] text-emerald-400 font-bold">--:--:--</span>
                        </div>
                        <div class="py-3">
                            <div id="last-scanned-code" class="text-xs font-bold text-emerald-400 break-all bg-slate-900 p-3 rounded-lg border border-slate-800 text-center tracking-wider">
                                WAITING FOR SCAN...
                            </div>
                        </div>
                        <div class="text-[10px] text-slate-500 flex justify-between items-center pt-1 border-t border-slate-800/60">
                            <span>Status Process:</span>
                            <span id="last-scanned-type" class="text-slate-400 font-bold uppercase">IDLE</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL PERINGATAN / INFORMASI OPERASIONAL -->
            <div class="panel-box-3d p-5 flex flex-col gap-3">
                <div class="flex items-center gap-2 text-slate-900 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2.5">
                    <i class="fa-solid fa-triangle-exclamation text-amber-500 text-sm"></i>
                    <h3 class="font-black text-xs uppercase tracking-wider">Peringatan & Panduan Scan</h3>
                </div>
                <ul class="text-[11px] font-bold text-slate-600 dark:text-slate-400 space-y-2 list-disc list-inside">
                    <li>Pastikan stiker barcode ID dalam kondisi bersih dan pencahayaan ruangan mencukupi.</li>
                    <li>Satu kali scan sukses otomatis menambahkan <strong>+1 QTY Stock IN</strong> ke dalam rak.</li>
                    <li>Sistem memiliki jeda verifikasi otomatis <strong>2 detik</strong> untuk mencegah duplikasi masukan data.</li>
                </ul>
            </div>

        </div>

    </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let html5QrCode = new Html5Qrcode("reader");
    let isCameraRunning = false;
    let isProcessing = false;
    
    const startCamBtn = document.getElementById('start-cam');
    const stopCamBtn = document.getElementById('stop-cam');
    const laserLine = document.getElementById('scan-laser-line');
    const camPlaceholder = document.getElementById('camera-placeholder');

    // KONFIGURASI SCANNER MULTI-FORMAT
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

    // FUNGSI INTI AJAX SUBMIT SCAN IN
    async function processAjaxStockIn(rawCode, mode = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
        updateMonitorDisplay(cleanCode, 'PROCESSING');
        changeUIStatus('PROCESSING', 'MEMBACA DATA...', `Mengirim payload: ${cleanCode}`);

        // ALERT LOADING PROSES
        Swal.fire({
            title: 'Memproses Stok IN...',
            html: `<div class="text-xs font-mono font-bold text-emerald-600 dark:text-emerald-400 break-all mt-2 bg-slate-100 dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700">${cleanCode}</div>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '340px',
            customClass: { popup: 'font-nunito rounded-2xl' },
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('barcode_scan', cleanCode);
        formData.append('process_type', mode);
        formData.append('comment', `Automated Stock IN via camera scan.`);

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
                changeUIStatus('VALID', 'STOK IN BERHASIL', result.message || 'Stok berhasil ditambahkan!');
                updateMonitorDisplay(cleanCode, 'SUCCESS');
                
                // ALERT BERHASIL - JEDA JELAS (2000 ms / 2 DETIK)
                Swal.fire({
                    icon: 'success',
                    title: 'Stok Bertambah (+1)',
                    text: result.message || 'Barang berhasil dicatat ke sistem!',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: '360px',
                    customClass: { popup: 'font-nunito rounded-2xl' }
                });
            } else {
                changeUIStatus('INVALID', 'TRANSAKSI DITOLAK', result.message || 'Format barcode tidak terdaftar.');
                updateMonitorDisplay(cleanCode, 'FAILED');

                // ALERT GAGAL - JEDA JELAS (2000 ms / 2 DETIK)
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Scan In',
                    text: result.message || 'Kode barcode/QR tidak valid atau belum terdaftar.',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    width: '360px',
                    customClass: { popup: 'font-nunito rounded-2xl' }
                });
            }
        } catch (error) {
            changeUIStatus('INVALID', 'KONEKSI TERPUTUS', 'Gagal terhubung ke database server.');
            updateMonitorDisplay(cleanCode, 'ERROR');
            
            // ALERT ERROR KONEKSI - JEDA JELAS (2000 ms / 2 DETIK)
            Swal.fire({ 
                icon: 'error', 
                title: 'Network Error', 
                text: 'Periksa koneksi jaringan atau server Anda.',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
                width: '360px',
                customClass: { popup: 'font-nunito rounded-2xl' }
            });
        } finally {
            // JEDA SAMA DENGAN ALERT (2000 ms / 2 DETIK) SEBELUM SCAN SELANJUTNYA
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
            typeEl.className = "text-amber-400 font-bold uppercase animate-pulse";
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

        badge.className = "px-3 py-1 text-[10px] font-black rounded-md flex items-center gap-1.5 uppercase tracking-wider ";
        iconBox.className = "shrink-0 w-10 h-10 rounded-xl flex items-center justify-center ";

        if (status === 'VALID') {
            badge.innerHTML = `<i class="fa-solid fa-check"></i> OK`;
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border', 'border-emerald-300', 'dark:bg-emerald-950', 'dark:text-emerald-400', 'dark:border-emerald-800');
            statusContainer.className = "panel-box-3d flex flex-col overflow-hidden !border-emerald-500/80";
            iconBox.classList.add('bg-emerald-100', 'text-emerald-600', 'dark:bg-emerald-950', 'dark:text-emerald-400');
            mainIcon.className = "fa-solid fa-square-check text-lg";
        } else if (status === 'PROCESSING') {
            badge.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> SYNC`;
            badge.classList.add('bg-amber-100', 'text-amber-700', 'border', 'border-amber-300', 'dark:bg-amber-950', 'dark:text-amber-400', 'dark:border-amber-800');
            statusContainer.className = "panel-box-3d flex flex-col overflow-hidden !border-amber-500/80";
            iconBox.classList.add('bg-amber-100', 'text-amber-600', 'dark:bg-amber-950', 'dark:text-amber-400');
            mainIcon.className = "fa-solid fa-rotate fa-spin text-lg";
        } else {
            badge.innerHTML = `<i class="fa-solid fa-xmark"></i> DITOLAK`;
            badge.classList.add('bg-rose-100', 'text-rose-700', 'border', 'border-rose-300', 'dark:bg-rose-950', 'dark:text-rose-400', 'dark:border-rose-800');
            statusContainer.className = "panel-box-3d flex flex-col overflow-hidden !border-rose-500/80";
            iconBox.classList.add('bg-rose-100', 'text-rose-600', 'dark:bg-rose-950', 'dark:text-rose-400');
            mainIcon.className = "fa-solid fa-triangle-exclamation text-lg";
        }
    }

    function resetSystemState() {
        isProcessing = false;
        
        if(!isCameraRunning) {
            document.getElementById('status-container').className = "panel-box-3d flex flex-col overflow-hidden";
            document.getElementById('status-icon-box').className = "shrink-0 w-10 h-10 rounded-xl bg-slate-200 dark:bg-slate-800 flex items-center justify-center text-slate-500";
            document.getElementById('main-status-icon').className = "fa-solid fa-microchip text-lg";
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY`;
            document.getElementById('badge_method').className = "px-3 py-1 text-[10px] font-black bg-slate-200/80 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-300 dark:border-slate-700 rounded-md flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-video text-[10px] animate-pulse"></i> LIVE SCANNING`;
            document.getElementById('badge_method').className = "px-3 py-1 text-[10px] font-black bg-emerald-600 text-white rounded-md flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'block';
        }
    }

    // ACTIONS EVENT KAMERA LIVE
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
                    confirmButtonColor: '#f59e0b',
                    customClass: { popup: 'font-nunito rounded-2xl' }
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
</script>
@endsection