@extends('admin')

@section('content')
<!-- Import Font Profesional ala Dashboard -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /* Styling Dasar TailAdmin-like */
    .tail-admin-ui { font-family: 'Inter', sans-serif; }
    
    /* Box Dashboard Profesional */
    .panel-box {
        background-color: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    .dark .panel-box {
        background-color: #1e293b; /* slate-800 */
        border-color: #334155; /* slate-700 */
    }

    /* Area Kamera (Industrial Scanner Look) */
    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617; /* slate-950 */
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        border: 1px solid #334155;
    }

    /* Efek Garis Laser Scanner */
    .laser-line {
        position: absolute;
        width: 100%;
        height: 3px;
        background: rgba(239, 68, 68, 0.8);
        box-shadow: 0 0 15px 2px rgba(239, 68, 68, 0.6);
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

    /* Kustomisasi Library html5-qrcode agar pas kotak */
    #reader { width: 100%; height: 100%; }
    #reader video { object-fit: cover !important; border-radius: 8px; }
    #reader__dashboard_section_csr span { color: white !important; }
</style>

<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 tail-admin-ui text-slate-800 dark:text-slate-200">
    
    <!-- HEADER DASHBOARD -->
    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white uppercase tracking-wide">
                <i class="fa-solid fa-barcode text-indigo-600 dark:text-indigo-400 mr-2"></i> Terminal Stock Out
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Sistem deteksi QR/Barcode gudang engineering terintegrasi.</p>
        </div>
        <a href="{{ route('eng.out') }}" class="inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 dark:bg-slate-800 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar
        </a>
    </div>

    <!-- GRID LAYOUT 12 KOLOM (TAILADMIN STYLE) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- KOLOM KIRI (LEBAR 8/12): MAIN SCANNER MODULE -->
        <div class="lg:col-span-8 panel-box flex flex-col">
            <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 rounded-t-xl">
                <h3 class="font-semibold text-slate-800 dark:text-white text-sm">Hardware Interface</h3>
                <span id="badge_method" class="px-2.5 py-1 text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400 border border-amber-200 dark:border-amber-700/50 rounded flex items-center gap-1.5 uppercase tracking-wider">
                    <i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY
                </span>
            </div>
            
            <div class="p-6 flex-1 flex flex-col">
                <!-- VIEWPORT KAMERA -->
                <div class="scanner-viewport shadow-inner">
                    <div id="reader"></div>
                    <div id="scan-laser-line" class="laser-line"></div>
                    
                    <!-- PLACEHOLDER SEBELUM KAMERA NYALA -->
                    <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-900 z-0">
                        <i class="fa-solid fa-camera-retro text-5xl text-slate-700 mb-3"></i>
                        <span class="text-slate-500 text-xs font-mono">MODUL KAMERA OFFLINE</span>
                    </div>
                </div>

                <!-- CONTROLLER TOMBOL -->
                <div class="mt-5 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <button type="button" id="start-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-md bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-all uppercase tracking-wide">
                        <i class="fa-solid fa-video"></i> Aktifkan Lensa
                    </button>
                    <button type="button" id="stop-cam" class="w-full inline-flex justify-center items-center gap-2 rounded-md bg-rose-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-rose-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-rose-600 transition-all uppercase tracking-wide hidden">
                        <i class="fa-solid fa-power-off"></i> Matikan Lensa
                    </button>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN (LEBAR 4/12): STATUS LOG & UPLOAD -->
        <div class="lg:col-span-4 flex flex-col gap-6">
            
            <!-- PANEL 1: DIAGNOSTIC LOG -->
            <div id="status-container" class="panel-box overflow-hidden transition-colors duration-300">
                <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm">System Log</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-start gap-4">
                        <div id="status-icon-box" class="flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500">
                            <i class="fa-solid fa-microchip text-lg" id="main-status-icon"></i>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-slate-900 dark:text-white" id="status-title">Mesin Siap</h4>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" id="status-desc">
                                Klik tombol aktifkan lensa untuk memulai sesi pemindaian stok.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PANEL 2: FAILSAFE / SANDBOX (UPLOAD GAMBAR) -->
            <div class="panel-box h-full flex flex-col">
                <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-white text-sm flex items-center gap-2">
                        <i class="fa-solid fa-folder-open text-amber-500"></i> Failsafe Method
                    </h3>
                </div>
                <div class="p-5 flex-1 flex flex-col justify-center">
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-3 text-center">
                        Gunakan mode ini jika kamera perangkat terblokir oleh browser.
                    </p>
                    
                    <label for="upload-image-scan" class="flex flex-col items-center justify-center w-full p-6 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-all group">
                        <div class="w-10 h-10 rounded-full bg-indigo-100 dark:bg-indigo-900/50 flex items-center justify-center mb-2 group-hover:scale-110 transition-transform">
                            <i class="fa-solid fa-cloud-arrow-up text-indigo-600 dark:text-indigo-400"></i>
                        </div>
                        <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 text-center" id="label-upload-status">
                            Telusuri Gambar
                        </span>
                        <span class="text-[10px] text-slate-400 mt-1 text-center font-mono" id="sublabel-upload-status">
                            PNG, JPG, JPEG
                        </span>
                        <input type="file" id="upload-image-scan" accept="image/*" class="hidden" />
                    </label>
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

    const scanConfig = {
        fps: 15,
        qrbox: function(width, height) {
            const minEdge = Math.min(width, height);
            return { width: Math.floor(minEdge * 0.7), height: Math.floor(minEdge * 0.7) };
        },
        aspectRatio: 1.0 // Membuat kotak pembacaan di dalam lebih proporsional
    };

    async function processAjaxStockOut(rawCode) {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        isProcessing = true; 

        changeUIStatus('PROCESSING', 'MENYINKRONKAN...', `Memverifikasi data: ${cleanCode}`);

        // Custom SweetAlert untuk tampilan industri
        Swal.fire({
            title: 'Memproses Transaksi',
            html: '<span class="text-sm text-slate-500">Mencari item logistik di database...</span>',
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '380px',
            padding: '2rem',
            didOpen: () => { Swal.showLoading(); }
        });

        const formData = new FormData();
        formData.append('barcode_scan', cleanCode);
        formData.append('qty_out', '1');
        formData.append('comment', 'Transaksi Terminal POS Engine');

        if (photoInput.files.length > 0) {
            formData.append('test_photo', photoInput.files[0]);
        }

        try {
            const response = await fetch("{{ route('eng.out.store') }}", {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                body: formData
            });

            const result = await response.json();

            if (response.ok && result.success) {
                changeUIStatus('VALID', 'TRANSAKSI SUKSES', 'Sistem berhasil memotong stok.');
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: result.message,
                    timer: 2000,
                    showConfirmButton: false,
                    width: '380px'
                }).then(() => resetSystemState());

            } else {
                changeUIStatus('INVALID', 'TRANSAKSI GAGAL', result.message || 'Data ditolak oleh server.');
                Swal.fire({
                    icon: 'warning',
                    title: 'Ditolak Sistem',
                    width: '440px',
                    confirmButtonColor: '#4f46e5',
                    html: `<div class="text-left bg-rose-50 dark:bg-rose-950/30 p-3 rounded-lg text-rose-700 dark:text-rose-400 mb-3 text-xs font-mono border border-rose-200 dark:border-rose-800">
                            <strong>Feedback Server:</strong><br>${result.message}
                           </div>
                           <div class="text-left bg-slate-900 p-3 rounded-lg text-xs font-mono text-slate-300">
                            <strong>Konten Terbaca:</strong><br><span class="text-amber-400 break-all">"${cleanCode}"</span>
                           </div>`
                }).then(() => resetSystemState());
            }
        } catch (error) {
            changeUIStatus('INVALID', 'KONEKSI TERPUTUS', 'Gagal menghubungkan terminal ke server lokal.');
            Swal.fire({ 
                icon: 'error', 
                title: 'Network Error', 
                text: 'Koneksi database localhost bermasalah.',
                confirmButtonColor: '#ef4444' 
            }).then(() => resetSystemState());
        }
    }

    // MANAJEMEN UI DASHBOARD PANEL KANAN
    function changeUIStatus(status, labelText, descText) {
        const badge = document.getElementById('badge_method');
        const statusContainer = document.getElementById('status-container');
        const iconBox = document.getElementById('status-icon-box');
        const mainIcon = document.getElementById('main-status-icon');
        const statusTitle = document.getElementById('status-title');
        const statusDesc = document.getElementById('status-desc');

        statusTitle.innerText = labelText;
        statusDesc.innerText = descText;

        // Reset class badge
        badge.className = "px-2.5 py-1 text-[10px] font-bold rounded flex items-center gap-1.5 uppercase tracking-wider ";
        iconBox.className = "flex-shrink-0 w-10 h-10 rounded-full flex items-center justify-center ";

        if (status === 'VALID') {
            badge.innerHTML = `<i class="fa-solid fa-check"></i> ${labelText}`;
            badge.classList.add('bg-emerald-100', 'text-emerald-700', 'border', 'border-emerald-200');
            statusContainer.className = "panel-box overflow-hidden transition-colors duration-300 border-emerald-300 bg-emerald-50/50";
            iconBox.classList.add('bg-emerald-100', 'text-emerald-600');
            mainIcon.className = "fa-solid fa-check-double text-lg";
        } else if (status === 'PROCESSING') {
            badge.innerHTML = `<i class="fa-solid fa-circle-notch fa-spin"></i> PROSES`;
            badge.classList.add('bg-amber-100', 'text-amber-700', 'border', 'border-amber-200');
            statusContainer.className = "panel-box overflow-hidden transition-colors duration-300 border-amber-300 bg-amber-50/50";
            iconBox.classList.add('bg-amber-100', 'text-amber-600');
            mainIcon.className = "fa-solid fa-arrows-rotate fa-spin text-lg";
        } else {
            badge.innerHTML = `<i class="fa-solid fa-triangle-exclamation"></i> GAGAL`;
            badge.classList.add('bg-rose-100', 'text-rose-700', 'border', 'border-rose-200');
            statusContainer.className = "panel-box overflow-hidden transition-colors duration-300 border-rose-300 bg-rose-50/50";
            iconBox.classList.add('bg-rose-100', 'text-rose-600');
            mainIcon.className = "fa-solid fa-circle-xmark text-lg";
        }
    }

    function resetSystemState() {
        isProcessing = false;
        photoInput.value = "";
        document.getElementById('label-upload-status').innerText = "Telusuri Gambar";
        document.getElementById('sublabel-upload-status').innerText = "PNG, JPG, JPEG";
        
        const isCam = isCameraRunning;
        changeUIStatus(
            isCam ? 'PROCESSING' : 'STANDBY', 
            isCam ? 'AKSES KAMERA AKTIF' : 'Mesin Siap', 
            isCam ? 'Arahkan QR Code ke area pindaian lensa.' : 'Klik tombol aktifkan lensa untuk memulai sesi pemindaian stok.'
        );
        
        // Kembalikan kotak status ke default jika standby
        if(!isCam) {
            document.getElementById('status-container').className = "panel-box overflow-hidden transition-colors duration-300";
            document.getElementById('status-icon-box').className = "flex-shrink-0 w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-slate-500";
            document.getElementById('main-status-icon').className = "fa-solid fa-microchip text-lg";
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-circle text-[8px] animate-pulse"></i> STANDBY`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200 rounded flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'none';
        } else {
            document.getElementById('badge_method').innerHTML = `<i class="fa-solid fa-video text-[10px] animate-pulse"></i> LIVE STREAM`;
            document.getElementById('badge_method').className = "px-2.5 py-1 text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 rounded flex items-center gap-1.5 uppercase tracking-wider";
            laserLine.style.display = 'block';
        }
    }

    // TRIGGER KAMERA
    startCamBtn.addEventListener('click', () => {
        if (!isCameraRunning) {
            html5QrCode.start(
                { facingMode: "environment" }, 
                scanConfig, 
                (decodedText) => { if (!isProcessing) processAjaxStockOut(decodedText.trim()); }
            )
            .then(() => { 
                isCameraRunning = true; 
                startCamBtn.classList.add('hidden');
                stopCamBtn.classList.remove('hidden');
                camPlaceholder.classList.add('hidden'); // Hilangkan placeholder logo kamera
                resetSystemState();
            })
            .catch(err => {
                Swal.fire({
                    icon: 'info',
                    title: 'Izin Peramban Dibutuhkan',
                    text: 'Pastikan Anda menggunakan localhost atau URL https:// untuk membuka modul lensa hardware.',
                    confirmButtonColor: '#4f46e5'
                });
            });
        }
    });

    stopCamBtn.addEventListener('click', () => { stopScanner(); });
    
    function stopScanner() {
        if (isCameraRunning) {
            html5QrCode.stop().then(() => { 
                isCameraRunning = false; 
                startCamBtn.classList.remove('hidden');
                stopCamBtn.classList.add('hidden');
                camPlaceholder.classList.remove('hidden'); // Tampilkan logo kamera lagi
                resetSystemState();
            }).catch(err => console.error(err));
        }
    }

    // TRIGGER BROWSE IMAGE
    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length === 0) return;
        stopScanner();
        
        const file = e.target.files[0];
        document.getElementById('label-upload-status').innerText = "Berkas Masuk!";
        document.getElementById('sublabel-upload-status').innerText = file.name.substring(0, 20);

        html5QrCode.scanFile(file, true)
            .then(decodedText => { processAjaxStockOut(decodedText.trim()); })
            .catch(err => { 
                Swal.fire({ 
                    icon: 'error', 
                    title: 'Gagal Dekripsi', 
                    text: 'Bentuk QR/Barcode di dalam foto tidak dapat dipetakan oleh mesin.',
                    confirmButtonColor: '#ef4444' 
                }); 
                resetSystemState();
            });
    });
</script>
@endsection