@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 md:p-8 dark:border-gray-800 dark:bg-white/[0.03]">
        
        <div class="flex items-center justify-between mb-8 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h2 class="text-lg font-bold text-slate-950 dark:text-white">Scan IN To Add Nozzle</h2>
            </div>
            
            <a href="{{ route('eng.in') }}" 
               class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 via-blue-700 to-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-md transition-all">
                <i class="fas fa-arrow-left text-xs"></i> Kembali
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            
            <div class="lg:col-span-5 flex flex-col gap-4">
                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Chosee Type Code</label>
                    <div class="relative">
                        <select id="barcode-type" onchange="updateScannerUI()"
                                class="w-full bg-gray-50 dark:bg-transparent border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-xs font-bold text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 appearance-none">
                            <option value="ALL">-- AUTO DETECT --</option>
                            <option value="QR_CODE">QR CODE</option>
                            <option value="BARCODE">1D BARCODE</option>
                            <option value="DATA_MATRIX">DATA MATRIX</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-500">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Scan Your Camera</label>
                    <div id="wrapper-scan" class="overflow-hidden rounded-2xl bg-slate-950 border border-gray-200 dark:border-gray-800 relative shadow-inner aspect-[4/3] flex items-center justify-center">
                        
                        <div id="reader" class="w-full h-full object-cover"></div>
                        
                        <div id="scanner-overlay" class="absolute inset-0 bg-black/40 flex items-center justify-center pointer-events-none z-10 hide-laser">
                            <div id="custom-laser-box" class="relative transition-all duration-300 ease-in-out">
                                <div class="absolute top-0 left-0 w-4 h-4 border-t-4 border-l-4 border-indigo-500 -mt-1 -ml-1 rounded-tl"></div>
                                <div class="absolute top-0 right-0 w-4 h-4 border-t-4 border-r-4 border-indigo-500 -mt-1 -mr-1 rounded-tr"></div>
                                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-4 border-l-4 border-indigo-500 -mb-1 -ml-1 rounded-bl"></div>
                                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-4 border-r-4 border-indigo-500 -mb-1 -mr-1 rounded-br"></div>
                                
                                <div class="absolute left-0 w-full h-[3px] bg-red-500 shadow-[0_0_10px_#ef4444] animate-scan-line"></div>
                            </div>
                        </div>
                        
                        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-3 z-30">
                            <button type="button" id="start-cam" class="bg-indigo-600/90 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg backdrop-blur-sm transition-all hover:bg-indigo-600 whitespace-nowrap">
                                <i class="fas fa-play mr-1"></i> START
                            </button>
                            <button type="button" id="stop-cam" class="bg-rose-600/90 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-lg backdrop-blur-sm transition-all hover:bg-rose-600 whitespace-nowrap">
                                <i class="fas fa-stop mr-1"></i> STOP
                            </button>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 mt-2">
                    <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Kamera Rusak? Upload Foto Barcode/QR</label>
                    <label for="upload-image-scan" class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 dark:border-gray-700 rounded-2xl bg-gray-50/50 dark:bg-transparent cursor-pointer hover:bg-gray-100/50 transition-all p-3 text-center">
                        <div class="flex flex-col items-center justify-center">
                            <i class="fas fa-images text-xl text-indigo-500 mb-1"></i>
                            <p class="text-xs font-bold text-slate-700 dark:text-gray-300">Pilih / Drop Foto Kode</p>
                            <p id="file-name-info" class="text-[10px] text-slate-400 mt-0.5">Mendukung format JPG, PNG, atau WEBP</p>
                        </div>
                        <input type="file" id="upload-image-scan" accept="image/*" class="hidden" />
                    </label>
                </div>
            </div>

            <div class="lg:col-span-7">
                <form action="{{ route('eng.in.store') }}" method="POST" id="form-stock-in" class="bg-gray-50/50 dark:bg-white/[0.02] border border-gray-100 dark:border-gray-800 p-6 rounded-2xl h-full flex flex-col justify-between">
                    @csrf
                    
                    <input type="hidden" name="stock_eng_id" id="stock_id_final">
                    
                    <input type="hidden" name="source" value="scan">
                    
                    <div class="space-y-6">
                        <div class="p-6 bg-white dark:bg-slate-900 border border-gray-200 dark:border-gray-800 rounded-xl text-center shadow-sm">
                             <span class="text-[10px] font-bold tracking-widest text-indigo-500 uppercase">Status Scanner</span>
                             <h3 id="display_name" class="text-xl font-extrabold text-slate-950 dark:text-white uppercase tracking-wide mt-1">Menunggu Scan...</h3>
                             <div class="mt-2 inline-block px-3 py-1 bg-gray-50 dark:bg-gray-800 rounded-md">
                                 <p id="view_sap_text" class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">READY TO SCAN</p>
                             </div>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Jumlah Stok Masuk</label>
                            <input type="number" name="qty_in" required min="1" placeholder="0"
                                   class="w-full text-center text-5xl font-black bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-xl p-4 text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            @error('qty_in')
                                <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-xs font-bold text-slate-700 dark:text-gray-300">Comment / Catatan</label>
                            <textarea name="comment" rows="3" placeholder="Catatan khusus hasil scan (optional)" 
                                      class="w-full bg-white dark:bg-slate-900 border border-gray-300 dark:border-gray-700 rounded-lg px-4 py-2.5 text-sm font-medium text-slate-950 dark:text-white focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 resize-none"></textarea>
                            @error('comment')
                                <span class="text-xs text-red-500 font-bold mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-8 pt-5 border-t border-gray-100 dark:border-gray-800 flex justify-end">
                        <button type="submit" class="w-full md:w-auto bg-gradient-to-r from-purple-600 to-red-500 text-white px-10 py-2.5 rounded-lg text-sm font-bold shadow-md transition-all uppercase tracking-wider">
                            Submit
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
    #reader__border_shifter, #reader div {
        border: none !important;
    }
    
    .hide-laser {
        display: none !important;
    }

    .target-qr { width: 200px; height: 200px; }
    .target-long { width: 85%; height: 70px; }
    .target-all { width: 75%; height: 150px; }

    @keyframes scanAnimation {
        0% { top: 0%; }
        50% { top: 100%; }
        100% { top: 0%; }
    }

    .animate-scan-line {
        animation: scanAnimation 2s infinite linear;
    }
</style>

<script src="https://unpkg.com/html5-qrcode"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    let html5QrCode = new Html5Qrcode("reader");
    let isCameraRunning = false;

    function updateScannerUI() {
        const type = document.getElementById('barcode-type').value;
        const targetBox = document.getElementById('custom-laser-box');

        if (!isCameraRunning) return;

        targetBox.className = "relative transition-all duration-300 ease-in-out";

        if (type === 'QR_CODE') {
            targetBox.classList.add('target-qr');
        } else if (type === 'BARCODE' || type === 'DATA_MATRIX') {
            targetBox.classList.add('target-long');
        } else {
            targetBox.classList.add('target-all');
        }
    }

    const processDecodedText = (decodedText) => {
        // Data stocks sekarang aman karena sudah dilempar dari method scan() controller baru
        const stocks = @json($stocks ?? []);
        const item = stocks.find(s => s.sap_code == decodedText);
        
        if(item) {
            document.getElementById('stock_id_final').value = item.id;
            document.getElementById('display_name').innerText = item.no_nozzle;
            document.getElementById('view_sap_text').innerText = "SAP: " + item.sap_code;
            
            Swal.fire({ 
                icon: 'success', 
                title: 'Nozzle Ditemukan', 
                text: item.no_nozzle + ' (' + item.sap_code + ')',
                timer: 2000,
                showConfirmButton: false
            });
            
            stopScanner();
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Barcode Tidak Terdaftar',
                text: 'SAP Code: ' + decodedText,
                confirmButtonColor: '#3C50E0'
            });
        }
    };

    const onScanSuccess = (decodedText) => {
        processDecodedText(decodedText);
    };

    function getScannerConfig() {
        const type = document.getElementById('barcode-type').value;
        let config = { fps: 20 };

        if (type === 'QR_CODE') {
            config.qrbox = { width: 200, height: 200 };
            config.formatsToSupport = [Html5QrcodeSupportedFormats.QR_CODE];
        } else if (type === 'DATA_MATRIX') {
            config.qrbox = { width: 280, height: 100 };
            config.formatsToSupport = [Html5QrcodeSupportedFormats.DATA_MATRIX];
        } else if (type === 'BARCODE') {
            config.qrbox = { width: 320, height: 80 };
            config.formatsToSupport = [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.EAN_13
            ];
        } else {
            config.qrbox = { width: 280, height: 160 };
        }

        return config;
    }

    document.getElementById('start-cam').addEventListener('click', () => {
        if (!isCameraRunning) {
            const currentConfig = getScannerConfig();

            html5QrCode.start(
                { facingMode: "environment" }, 
                currentConfig, 
                onScanSuccess
            )
            .then(() => {
                isCameraRunning = true;
                document.getElementById('barcode-type').disabled = true;
                document.getElementById('scanner-overlay').classList.remove('hide-laser');
                updateScannerUI();
            })
            .catch((err) => {
                console.error("Gagal start kamera: ", err);
                Swal.fire({ icon: 'error', title: 'Kamera Gagal', text: 'Pastikan izin kamera aktif!' });
            });
        }
    });

    document.getElementById('stop-cam').addEventListener('click', () => {
        stopScanner();
    });

    function stopScanner() {
        if (isCameraRunning) {
            html5QrCode.stop().then(() => {
                isCameraRunning = false;
                document.getElementById('barcode-type').disabled = false;
                document.getElementById('scanner-overlay').classList.add('hide-laser');
            }).catch((err) => {
                console.error("Gagal stop kamera: ", err);
            });
        }
    }

    document.getElementById('upload-image-scan').addEventListener('change', function(e) {
        if (e.target.files.length === 0) return;

        const file = e.target.files[0];
        document.getElementById('file-name-info').innerText = "Memproses: " + file.name;

        stopScanner();

        Swal.fire({
            title: 'Membaca Gambar...',
            text: 'Harap tunggu, sistem sedang men-decode file kode.',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        html5QrCode.scanFile(file, true)
            .then(decodedText => {
                Swal.close();
                processDecodedText(decodedText);
            })
            .catch(err => {
                Swal.close();
                console.error("Gagal membaca gambar: ", err);
                document.getElementById('file-name-info').innerText = "Gagal memproses gambar.";
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Membaca',
                    text: 'Kode tidak terdeteksi di gambar. Pastikan posisi gambar tegak, jernih, dan tidak blur!'
                });
            });
    });
</script>
@endsection