@extends('admin')

@section('content')
{{-- Load Google Font Nunito, FontAwesome & SweetAlert2 --}}
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    /* UTAMA FONT NUNITO DI SELURUH KOMPONEN */
    *, body, html, .font-nunito, .swal2-popup, .swal2-title, .swal2-content, .swal2-html-container { 
        font-family: 'Nunito', sans-serif !important; 
    }
    
    /* BINGKAI GRID 3D DENGAN GARIS BIRU TUA PERSISI */
    .panel-box-3d {
        background-color: #ffffff;
        border: 2px solid #1e3a8a; /* Biru Tua / Blue-900 */
        border-radius: 16px;
        box-shadow: 0 6px 16px -2px rgba(30, 58, 138, 0.15), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        transition: all 0.25s ease-in-out;
    }
    .dark .panel-box-3d {
        background-color: #0f172a;
        border-color: #1e40af; /* Biru Tua Dark Mode */
        box-shadow: 0 6px 18px -2px rgba(0, 0, 0, 0.5);
    }

    /* KOTAK VIEWPORT KAMERA SCANNER */
    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #0f172a;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        aspect-ratio: 4/3;
        width: 100%;
        border: 1.5px solid #1e3a8a;
    }

    /* Laser Line Emerald untuk Scanner */
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
    
    <!-- HEADER TERMINAL SCAN IN -->
    <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white uppercase tracking-tight flex items-center gap-2">
                Terminal Stock In Otomatis
            </h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400 mt-0.5">
                Scan barcode/QR untuk menambah stok rak gudang dan mencatat transaksi penerimaan barang.
            </p>
        </div>
        <div>
            {{-- Tombol BACK Gradient Hijau Tanpa Icon --}}
            <a href="{{ route('eng.in') }}" 
               class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 px-6 py-2.5 text-xs font-black text-white shadow-md shadow-emerald-600/20 hover:brightness-110 active:scale-95 transition-all no-underline font-nunito tracking-wider uppercase">
                BACK
            </a>
        </div>
    </div>

    <!-- TAMPILAN 2 GRID MURNI (KOTAK KIRI KAMERA SCAN & KOTAK KANAN LOG TERMINAL) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 md:gap-6 font-nunito">
        
        <!-- GRID 1: LENSA KAMERA SCANNER (DESKTOP: COL-SPAN 7) -->
        <div class="lg:col-span-7 panel-box-3d flex flex-col overflow-hidden">
            <div class="border-b-2 border-blue-900/60 dark:border-blue-800 px-5 py-3.5 flex justify-between items-center bg-slate-50/80 dark:bg-slate-900/80">
                <h3 class="font-black text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                    <i class="fa-solid fa-camera text-emerald-600 dark:text-emerald-400"></i> Lensa Kamera Live
                </h3>
                <span id="badge_method" class="px-3 py-1 text-[10px] font-black bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-md flex items-center gap-1.5 uppercase tracking-wider font-nunito">
                    STANDBY
                </span>
            </div>
            
            <div class="p-4 md:p-5 flex-1 flex flex-col justify-between gap-4">
                {{-- VIEWPORT KAMERA --}}
                <div class="scanner-viewport shadow-inner">
                    <div id="reader"></div>
                    <div id="scan-laser-line" class="laser-line"></div>
                    
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-0 text-center p-4">
                        <div class="w-16 h-16 rounded-2xl bg-slate-800 border-2 border-blue-900/60 flex items-center justify-center mb-3 shadow-md">
                            <i class="fa-solid fa-qrcode text-2xl text-slate-400"></i>
                        </div>
                        <span class="text-slate-200 text-xs font-black uppercase tracking-wider font-nunito">Lensa Standby / Offline</span>
                        <p class="text-[11px] text-slate-400 font-bold mt-1 font-nunito">Klik tombol di bawah untuk mengaktifkan kamera scanner</p>
                    </div>
                </div>

                {{-- TOMBOL CONTROL KAMERA (GRADIENT EMERALD) --}}
                <div class="w-full pt-1">
                    <button type="button" id="start-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-600 px-6 py-3.5 text-xs font-black text-white shadow-md hover:brightness-110 active:scale-95 transition-all uppercase tracking-wider cursor-pointer border border-emerald-400/30 font-nunito">
                        Aktifkan Lensa Kamera
                    </button>
                    <button type="button" id="stop-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-xl bg-gradient-to-r from-rose-600 to-red-600 px-6 py-3.5 text-xs font-black text-white shadow-md hover:brightness-110 active:scale-95 transition-all uppercase tracking-wider hidden cursor-pointer border border-rose-400/30 font-nunito">
                        Matikan Lensa Kamera
                    </button>
                </div>
            </div>
        </div>

        <!-- GRID 2: LOG TERMINAL REALTIME (DESKTOP: COL-SPAN 5) -->
        <div class="lg:col-span-5 flex flex-col">
            
            <div id="status-container" class="panel-box-3d flex flex-col overflow-hidden h-full justify-between">
                <div>
                    <div class="border-b-2 border-blue-900/60 dark:border-blue-800 px-5 py-3.5 bg-slate-50/80 dark:bg-slate-900/80 flex justify-between items-center">
                        <h3 class="font-black text-slate-900 dark:text-white text-xs uppercase tracking-wider flex items-center gap-2">
                            <i class="fa-solid fa-terminal text-blue-800 dark:text-blue-400"></i> Log Terminal Realtime
                        </h3>
                        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 font-nunito">SYNC ONLINE</span>
                    </div>
                    
                    <div class="p-5 flex flex-col gap-4">
                        {{-- MESIN STATUS HEADER --}}
                        <div class="flex items-start gap-3.5 bg-slate-50 dark:bg-slate-900/80 p-4 rounded-xl border border-slate-200 dark:border-slate-800">
                            <div id="status-icon-box" class="shrink-0 w-11 h-11 rounded-xl bg-blue-900/10 dark:bg-blue-900/30 border border-blue-900/40 flex items-center justify-center text-blue-900 dark:text-blue-400 shadow-sm">
                                <i class="fa-solid fa-server text-lg" id="main-status-icon"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-slate-900 dark:text-white uppercase tracking-wide font-nunito" id="status-title">Mesin Standby</h4>
                                <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-1 leading-relaxed font-nunito" id="status-desc">
                                    Terminal siap mendeteksi masukan data via scan barcode/QR dari kamera live.
                                </p>
                            </div>
                        </div>

                        {{-- MONITOR DISPLAY HASIL SCAN TERAKHIR (WARNA SLATE ELEGANT ELEGAN & BORDER BIRU TUA) --}}
                        <div class="rounded-xl border-2 border-blue-900/60 bg-gradient-to-br from-slate-900 via-slate-850 to-blue-950 p-4 text-slate-200 flex flex-col justify-between min-h-[160px] shadow-lg relative overflow-hidden">
                            <div class="flex justify-between items-center border-b border-slate-700/60 pb-2.5">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest font-nunito">Last Scanned Payload</span>
                                <span id="scan-timestamp" class="text-[10px] text-emerald-400 font-extrabold font-nunito">--:--:--</span>
                            </div>
                            
                            <div class="py-4">
                                <div id="last-scanned-code" class="text-xs font-extrabold text-emerald-400 break-all bg-slate-950/70 p-3.5 rounded-lg border border-blue-900/50 text-center tracking-wider font-nunito shadow-inner">
                                    WAITING FOR SCAN...
                                </div>
                            </div>
                            
                            <div class="text-[10px] text-slate-400 flex justify-between items-center pt-2 border-t border-slate-700/60 font-nunito">
                                <span>Status Process:</span>
                                <span id="last-scanned-type" class="text-slate-300 font-black uppercase">IDLE</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATISTIK/INFO FOOTER DALAM LOG --}}
                <div class="p-5 pt-0">
                    <div class="p-3.5 rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-900/50 text-[11px] font-bold text-slate-500 dark:text-slate-400 flex items-center justify-between">
                        <span>Mode Transaksi</span>
                        <span class="text-slate-800 dark:text-slate-200 font-black uppercase">STOCK IN (+1 AUTO)</span>
                    </div>
                </div>

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

    // KONFIGURASI SCANNER
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

    // FUNGSI INTI AJAX SUBMIT SCAN IN (REMARK: AUTOMATED IN)
    async function processAjaxStockIn(rawCode, mode = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
        updateMonitorDisplay(cleanCode, 'PROCESSING');
        changeUIStatus('PROCESSING', 'MEMBACA DATA...', `Mengirim payload: ${cleanCode}`);

        // ALERT LOADING PROSES
        Swal.fire({
            title: 'Memproses Stok IN...',
            html: `<div class="text-xs font-bold text-emerald-600 dark:text-emerald-400 break-all mt-2 bg-slate-100 dark:bg-slate-800 p-2.5 rounded-lg border border-slate-200 dark:border-slate-700 font-nunito">${cleanCode}</div>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '340px',
            customClass: { popup: 'font-nunito rounded-2xl' },
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('barcode_scan', cleanCode);
        formData.append('process_type', mode);
        formData.append('comment', 'AUTOMATED IN');
        formData.append('remark', 'AUTOMATED IN');

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
            typeEl.className = "text-emerald-400 font-extrabold uppercase font-nunito";
        } else if (status === 'PROCESSING') {
            typeEl.className = "text-amber-400 font-extrabold uppercase font-nunito";
        } else {
            typeEl.className = "text-rose-400 font-extrabold uppercase font-nunito";
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

        badge.className = "px-3 py-1 text-[10px] font-black rounded-md flex items-center gap-1.5 uppercase tracking-wider font-nunito ";
        iconBox.className = "shrink-0 w-11 h-11 rounded-xl flex items-center justify-center ";

        if (status === 'VALID') {
            badge.innerText = "OK";
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border', 'border-emerald-300', 'dark:bg-emerald-950', 'dark:text-emerald-400');
            statusContainer.className = "panel-box-3d flex flex-col overflow-hidden h-full justify-between !border-emerald-500/80";
            iconBox.classList.add('bg-emerald-100', 'text-emerald-600', 'dark:bg-emerald-950', 'dark:text-emerald-400');
            mainIcon.className = "fa-solid fa-circle-check text-lg";
        } else if (status === 'PROCESSING') {
            badge.innerText = "SYNC";
            badge.classList.add('bg-amber-100', 'text-amber-700', 'border', 'border-amber-300', 'dark:bg-amber-950', 'dark:text-amber-400');
            statusContainer.className = "panel-box-3d flex flex-col overflow-hidden h-full justify-between !border-amber-500/80";
            iconBox.classList.add('bg-amber-100', 'text-amber-600', 'dark:bg-amber-950', 'dark:text-amber-400');
            mainIcon.className = "fa-solid fa-spinner text-lg";
        } else {
            badge.innerText = "DITOLAK";
            badge.classList.add('bg-rose-100', 'text-rose-700', 'border', 'border-rose-300', 'dark:bg-rose-950', 'dark:text-rose-400');
            statusContainer.className = "panel-box-3d flex flex-col overflow-hidden h-full justify-between !border-rose-500/80";
            iconBox.classList.add('bg-rose-100', 'text-rose-600', 'dark:bg-rose-950', 'dark:text-rose-400');
            mainIcon.className = "fa-solid fa-circle-xmark text-lg";
        }
    }

    function resetSystemState() {
        isProcessing = false;
        
        if(!isCameraRunning) {
            document.getElementById('status-container').className = "panel-box-3d flex flex-col overflow-hidden h-full justify-between";
            document.getElementById('status-icon-box').className = "shrink-0 w-11 h-11 rounded-xl bg-blue-900/10 dark:bg-blue-900/30 border border-blue-900/40 flex items-center justify-center text-blue-900 dark:text-blue-400";
            document.getElementById('main-status-icon').className = "fa-solid fa-server text-lg";
            document.getElementById('badge_method').innerText = "STANDBY";
            document.getElementById('badge_method').className = "px-3 py-1 text-[10px] font-black bg-slate-200/80 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-700 rounded-md flex items-center gap-1.5 uppercase tracking-wider font-nunito";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerText = "LIVE SCANNING";
            document.getElementById('badge_method').className = "px-3 py-1 text-[10px] font-black bg-emerald-600 text-white rounded-md flex items-center gap-1.5 uppercase tracking-wider font-nunito";
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