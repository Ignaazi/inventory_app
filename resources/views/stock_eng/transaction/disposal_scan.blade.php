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

    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617 !important;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        height: 380px; 
        max-height: 380px;
        border: 2px solid #334155;
    }

    #reader {
        width: 100% !important;
        height: 380px !important;
        background-color: #020617 !important;
        border: none !important;
    }
    
    #reader video {
        object-fit: cover !important;
        width: 100% !important;
        height: 380px !important;
    }

    #reader__border_element, #reader__dashboard {
        display: none !important;
    }

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
            <h2 class="text-xl font-black text-slate-900 dark:text-white uppercase tracking-tight">
                <i class="fa-solid fa-dumpster-fire text-[#E51E43] mr-2"></i> Terminal Stock Disposal
            </h2>
            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1">Sistem pemusnahan kode unik secara real-time tanpa refresh halaman.</p>
        </div>
        <a href="{{ route('stock_eng.transaction.disposal') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-100 dark:bg-slate-800 px-4 py-2 text-xs font-black text-slate-700 dark:text-slate-300 border border-slate-300 dark:border-slate-600 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all no-underline tracking-wider uppercase">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Riwayat
        </a>
    </div>

    <!-- FORM DENGAN ID ACTION KHUSUS -->
    <form id="disposal_main_form" action="{{ route('stock_eng.transaction.disposal.scan.process') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            
            <!-- MODULE CAMERA SCANNER -->
            <div class="lg:col-span-8 panel-box flex flex-col">
                <div class="border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-between items-center bg-slate-50 dark:bg-slate-800/50 rounded-t-xl">
                    <h3 class="font-bold text-slate-800 dark:text-white text-xs uppercase tracking-wider">Hardware Lensa Live Detector</h3>
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
                            <span class="text-slate-500 text-xs font-mono font-bold">LENSA READY TO SCAN</span>
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

            <!-- PANEL INPUT MANUAL & LASER GUN -->
            <div class="lg:col-span-4 flex flex-col gap-6">
                <div class="panel-box flex flex-col">
                    <div class="border-b border-slate-200 dark:border-slate-700 px-5 py-4 bg-slate-50 dark:bg-slate-800/50 rounded-t-xl">
                        <h3 class="font-bold text-slate-800 dark:text-white text-xs flex items-center gap-2 uppercase tracking-wider">
                            <i class="fa-solid fa-keyboard text-amber-500"></i> Input Manual / Laser Gun
                        </h3>
                    </div>
                    <div class="p-5 flex flex-col gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Tembak / Ketik Barcode ID</label>
                            <div class="flex gap-2">
                                <input type="text" id="manual_barcode" placeholder="Ketik/tembak barcode..." class="w-full rounded-lg border border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-mono font-bold text-slate-950 dark:text-white focus:outline-none focus:border-[#E51E43]" autofocus>
                            </div>
                            <button type="button" id="btn_manual_submit" class="w-full mt-2 bg-[#E51E43] hover:opacity-90 py-2 rounded-lg text-white text-xs font-black tracking-wider uppercase transition-all cursor-pointer">
                                Kirim Barcode
                            </button>
                        </div>

                        <div class="border-t border-slate-200 dark:border-slate-700 pt-3">
                            <label for="upload-image-scan" class="flex flex-col items-center justify-center w-full p-4 border-2 border-dashed border-slate-300 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-900/40 hover:bg-slate-100 cursor-pointer transition-all group">
                                <i class="fa-solid fa-cloud-arrow-up text-rose-500 mb-1"></i>
                                <span class="text-xs font-black text-rose-600 uppercase tracking-wider" id="label-upload-status">Upload Gambar QR</span>
                                <input type="file" id="upload-image-scan" accept="image/*" class="hidden" />
                            </label>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
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
    mainForm.addEventListener('submit', function(e) { e.preventDefault(); });

    // 🌟 REVOLUSI CORE: MENGGUNAKAN Jalur AJAX FETCH (ANTI-GETER & ANTI-REFRESH) 🌟
    function executeDisposalTransaction(rawCode, methodType = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 

        // Tampilkan loading popup ramah di depan kamera
        Swal.fire({
            title: 'Memproses Pemusnahan...',
            html: `<div class="text-xs font-mono text-rose-600 bg-rose-50 dark:bg-rose-950/30 p-2 rounded break-all font-bold mb-1">${cleanCode}</div>
                   <p class="text-[11px] text-slate-400"><i class="fa-solid fa-circle-notch fa-spin mr-1"></i>Mengunci lifecycle data via background...</p>`,
            allowOutsideClick: false,
            showConfirmButton: false,
            width: '350px',
            didOpen: () => { Swal.showLoading(); }
        });

        // Kirim data via fetch API (Jalur Belakang)
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
            // Reset status input agar siap untuk item selanjutnya
            manualInput.value = "";
            isProcessing = false;

            if (data.success) {
                // Tampilkan alert sukses dan hilang otomatis dalam 2 detik (Bisa langsung gas scan lagi!)
                Swal.fire({
                    icon: 'success',
                    title: 'Disposal Sukses!',
                    text: data.message,
                    timer: 2200,
                    showConfirmButton: false,
                    confirmButtonColor: '#E51E43'
                });
            } else {
                Swal.fire({
                    icon: data.message.includes('sudah berstatus DISPOSAL') ? 'warning' : 'error',
                    title: data.message.includes('sudah berstatus DISPOSAL') ? 'Barcode Sudah Mati!' : 'Transaksi Ditolak!',
                    text: data.message,
                    confirmButtonColor: '#E51E43'
                });
            }
        })
        .catch(err => {
            isProcessing = false;
            Swal.fire({
                icon: 'error',
                title: 'System Error!',
                text: 'Gagal terhubung ke server. Periksa koneksi lokal pabrik.',
                confirmButtonColor: '#E51E43'
            });
        });
    }

    // STATE CONTROLLER INTERFACE
    function resetSystemState() {
        photoInput.value = "";
        manualInput.value = "";
        document.getElementById('label-upload-status').innerText = "Upload Gambar QR";
        
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
    btnManual.addEventListener('click', () => {
        const val = manualInput.value.trim();
        if(val) executeDisposalTransaction(val, 'manual');
    });

    manualInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            e.preventDefault();
            btnManual.click();
        }
    });

    // UPLOAD IMAGE FILE SENSOR
    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length === 0) return;
        const file = e.target.files[0];
        document.getElementById('label-upload-status').innerText = "File Dimuat";

        html5QrCode.scanFile(file, true)
            .then(decodedText => { executeDisposalTransaction(decodedText.trim(), 'scan'); })
            .catch(err => { 
                Swal.fire({ icon: 'error', title: 'Gagal Membaca!', text: 'QR Code tidak tajam atau tidak terdeteksi.' }); 
                resetSystemState();
            });
    });
</script>
@endsection