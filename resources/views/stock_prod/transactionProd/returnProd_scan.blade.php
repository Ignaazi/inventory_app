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

    .scanner-viewport {
        position: relative;
        overflow: hidden;
        background-color: #020617;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 380px;
        border: 1px solid #1e293b;
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
    #reader video { object-fit: cover !important; border-radius: 12px; }
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
        
        {{-- MODULE KAMERA SCANNER --}}
        <div class="lg:col-span-8 panel-box flex flex-col p-4 md:p-5">
            <div class="mb-3 flex justify-between items-center pb-3 border-b border-gray-100 dark:border-slate-800">
                <h3 class="font-black text-slate-800 dark:text-white text-xs md:text-sm uppercase tracking-wide">
                    Live Camera Return Scanner
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
                        <span class="text-slate-500 text-xs font-mono font-bold">KAMERA NONAKTIF</span>
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

        {{-- DIAGNOSTIC LOG & INPUT MANUAL --}}
        <div class="lg:col-span-4 flex flex-col gap-5">
            
            {{-- LOG TERMINAL --}}
            <div id="status-container" class="panel-box p-4 md:p-5 transition-colors duration-300">
                <h3 class="font-black text-slate-800 dark:text-white text-xs md:text-sm uppercase tracking-wide border-b border-gray-100 dark:border-slate-800 pb-3 mb-3">
                    Terminal Log Realtime
                </h3>
                <div class="flex items-start gap-3">
                    <div id="status-icon-box" class="flex-shrink-0 w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500">
                        <i class="fa-solid fa-microchip text-lg" id="main-status-icon"></i>
                    </div>
                    <div>
                        <h4 class="text-xs md:text-sm font-extrabold text-slate-900 dark:text-white" id="status-title">Terminal Ready</h4>
                        <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed" id="status-desc">
                            Arahkan barcode ke lensa kamera atau gunakan USB Gun Scanner/input keyboard di bawah.
                        </p>
                    </div>
                </div>
            </div>

            {{-- INPUT MANUAL KEYBOARD / GUN SCANNER --}}
            <div class="panel-box p-4 md:p-5 flex flex-col gap-4">
                <h3 class="font-black text-slate-800 dark:text-white text-xs md:text-sm uppercase tracking-wide border-b border-gray-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <i class="fa-solid fa-barcode text-amber-500"></i> Manual / Gun Scanner
                </h3>
                
                <div>
                    <label class="block text-[10px] font-black text-slate-500 uppercase mb-1">Scan / Ketik Barcode ID</label>
                    <div class="flex gap-2">
                        <input type="text" id="manual_barcode" placeholder="Scan barcode ID..." class="flex-1 rounded-lg border border-gray-300 dark:border-slate-700 bg-transparent px-3 py-2 text-xs font-bold text-slate-900 dark:text-white focus:outline-none focus:border-amber-500">
                        <button type="button" id="btn_manual_submit" class="bg-amber-500 hover:bg-amber-600 px-4 py-2 rounded-lg text-white text-xs font-black transition-all cursor-pointer">
                            Process
                        </button>
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-slate-800 pt-3">
                    <p class="text-[10px] font-bold text-slate-400 mb-1.5">Alternatif: Upload Gambar Barcode/QR</p>
                    <label for="upload-image-scan" class="flex flex-col items-center justify-center w-full p-3 border-2 border-dashed border-gray-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/40 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-all">
                        <i class="fa-solid fa-cloud-arrow-up text-amber-500 text-lg mb-1"></i>
                        <span class="text-xs font-extrabold text-amber-600" id="label-upload-status">Upload File Barcode</span>
                        <input type="file" id="upload-image-scan" accept="image/*" class="hidden" />
                    </label>
                </div>
            </div>

        </div>
    </div>

    {{-- TABEL RIWAYAT SCAN RETURN --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
        <div class="px-5 mb-3 flex items-center justify-between">
            <h3 class="text-sm font-black text-slate-900 dark:text-white uppercase tracking-wider">
                Recent Return Transactions
            </h3>
        </div>

        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[2000px]" id="historyTable">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950 text-white font-nunito table-header-row">
                        <th class="px-3 py-4 w-[50px] text-center">
                            <input type="checkbox" id="selectAllCheckbox" class="w-4 h-4 rounded border-blue-400 bg-transparent text-blue-600 focus:ring-blue-500 cursor-pointer checked:bg-white checked:border-white">
                        </th>
                        <th class="px-4 py-4 w-[70px] border-l border-blue-500/50 bg-blue-700/50">NO</th>
                        <th class="px-5 py-4 w-[220px] border-l border-blue-500/50 bg-blue-700/50">TRANSACTION ID</th>
                        <th class="px-4 py-4 w-[140px] border-l border-blue-500/50 bg-blue-700/50">NIK</th>
                        <th class="px-5 py-4 w-[180px] border-l border-blue-500/50 bg-blue-700/50">OPERATOR NAME</th>
                        <th class="px-5 py-4 w-[190px] border-l border-blue-500/50 bg-blue-700/50">BARCODE ID</th>
                        <th class="px-4 py-4 w-[160px] border-l border-blue-500/50 bg-blue-700/50">SPAREPART ID</th>
                        <th class="px-4 py-4 w-[130px] border-l border-blue-500/50 bg-blue-700/50">RAK</th>
                        <th class="px-4 py-4 w-[120px] border-l border-blue-500/50 bg-blue-700/50">QTY RETURN</th>
                        <th class="px-4 py-4 w-[130px] border-l border-blue-500/50 bg-blue-700/50">STATUS</th>
                        <th class="px-4 py-4 w-[150px] border-l border-blue-500/50 bg-blue-700/50">PROCESS TYPE</th>
                        <th class="px-5 py-4 w-[220px] border-l border-blue-500/50 bg-blue-700/50 text-left">REMARK</th>
                        <th class="px-4 py-4 w-[160px] border-l border-blue-500/50 bg-blue-700/50 text-center">CREATED AT</th>
                        <th class="px-4 py-4 w-[160px] border-l border-blue-500/50 bg-blue-700/50 text-center">UPDATED AT</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($history as $index => $log)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-3 py-4 text-center">
                            <input type="checkbox" class="row-checkbox w-4 h-4 rounded border-gray-300 dark:border-slate-700 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        </td>

                        {{-- 1. NO --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center">
                            {{ (method_exists($history, 'firstItem')) ? ($history->firstItem() + $index) : ($index + 1) }}
                        </td>

                        {{-- 2. TRANSACTION ID --}}
                        <td class="px-5 py-4 border-l border-gray-100 dark:border-slate-800 font-extrabold font-mono text-indigo-600 dark:text-indigo-400 text-center whitespace-nowrap select-all">
                            {{ $log->tx_id ?? '-' }}
                        </td>

                        {{-- 3. NIK OPERATOR --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center font-mono font-bold text-slate-700 dark:text-slate-300 whitespace-nowrap">
                            {{ $log->nik ?? $log->nik_karyawan ?? '-' }}
                        </td>

                        {{-- 4. OPERATOR NAME --}}
                        <td class="px-5 py-4 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap font-extrabold text-slate-900 dark:text-white">
                            {{ $log->operator_name ?? 'System' }}
                        </td>

                        {{-- 5. BARCODE ID --}}
                        <td class="px-5 py-4 border-l border-gray-100 dark:border-slate-800 font-mono text-amber-600 dark:text-amber-400 text-center whitespace-nowrap">
                            {{ $log->barcode_code ?? '-' }}
                        </td>

                        {{-- 6. SPAREPART ID --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 font-mono text-emerald-600 dark:text-emerald-400 text-center font-extrabold whitespace-nowrap">
                            {{ $log->item_code ?? '-' }}
                        </td>

                        {{-- 7. RAK / LINE TARGET --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 font-mono text-purple-600 dark:text-purple-400 text-center font-extrabold whitespace-nowrap">
                            {{ $log->line_name ?? ('LINE ' . ($log->stock_prods_id ?? '-')) }}
                        </td>

                        {{-- 8. QTY RETURN --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center rounded-lg px-3 py-1 text-[11px] font-black border border-indigo-200 bg-indigo-50 text-indigo-600 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-900/50">
                                {{ number_format($log->qty_transaction ?? 1) }} Pcs
                            </span>
                        </td>

                        {{-- 9. STATUS --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <span class="inline-flex items-center justify-center rounded-lg px-3 py-1 text-[10px] font-black tracking-tight uppercase border
                                @if(strtolower($log->status ?? '') == 'success') border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-900/50
                                @elseif(strtolower($log->status ?? '') == 'pending') border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-900/50
                                @else border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-900/50 @endif">
                                {{ $log->status ?? 'SUCCESS' }}
                            </span>
                        </td>

                        {{-- 10. PROCESS TYPE --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            @php $isProcManual = strtolower($log->process_type ?? '') === 'manual'; @endphp
                            <span class="inline-flex items-center justify-center rounded-lg px-3 py-1 text-[10px] font-black tracking-tight uppercase border
                                @if($isProcManual) border-blue-200 bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-900/50
                                @else border-purple-200 bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-900/50 @endif">
                                {{ $log->process_type ?? 'Scan' }} Return
                            </span>
                        </td>

                        {{-- 11. REMARK --}}
                        <td class="px-5 py-4 border-l border-gray-100 dark:border-slate-800 text-left font-black text-slate-800 dark:text-slate-200 uppercase tracking-tight truncate max-w-[220px]" title="{{ $log->remark ?? 'AUTOMATED RETURN' }}">
                            {{ !empty($log->remark) ? strtoupper($log->remark) : 'AUTOMATED RETURN' }}
                        </td>

                        {{-- 12. CREATED AT --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            @php
                                $createdAt = $log->created_at ? (\Carbon\Carbon::parse($log->created_at)) : null;
                            @endphp
                            <div class="font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                {{ $createdAt ? $createdAt->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] mt-0.5 text-slate-500">
                                {{ $createdAt ? $createdAt->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>

                        {{-- 13. UPDATED AT --}}
                        <td class="px-4 py-4 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            @php
                                $updatedAt = $log->updated_at ? (\Carbon\Carbon::parse($log->updated_at)) : null;
                            @endphp
                            <div class="font-bold text-slate-800 dark:text-slate-200 leading-tight">
                                {{ $updatedAt ? $updatedAt->format('d/m/Y') : '-' }}
                            </div>
                            <div class="text-[10px] mt-0.5 text-slate-500">
                                {{ $updatedAt ? $updatedAt->format('H:i') . ' WIB' : '' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="14" class="py-12 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text">
                            No production return history logs found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-5 py-4 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase font-nunito text-center sm:text-left text-black dark:text-slate-400">
                Showing {{ (isset($history) && method_exists($history, 'firstItem')) ? ($history->firstItem() ?? 0) : 0 }} 
                to {{ (isset($history) && method_exists($history, 'lastItem')) ? ($history->lastItem() ?? 0) : 0 }} 
                of {{ (isset($history) && method_exists($history, 'total')) ? ($history->total() ?? 0) : 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs font-nunito w-full sm:w-auto custom-pagination text-black dark:text-white">
                @if(isset($history) && method_exists($history, 'links'))
                    {{ $history->appends(['search' => request('search'), 'per_page' => request('per_page')])->links() }}
                @endif
            </div>
        </div>
    </div>
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

    // AJAX SUBMIT RETURN
    async function processAjaxStockReturn(rawCode, mode = 'scan') {
        const cleanCode = rawCode.replace(/[\n\r\t]/g, "").trim();
        if (!cleanCode || isProcessing) return;
        
        isProcessing = true; 
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
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Return',
                    text: result.message,
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (error) {
            changeUIStatus('INVALID', 'KONEKSI LOSS', 'Koneksi ke database terputus.');
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
        photoInput.value = "";
        manualInput.value = "";
        document.getElementById('label-upload-status').innerText = "Upload File Barcode";
        
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

    btnManual.addEventListener('click', () => {
        const val = manualInput.value.trim();
        if(val) processAjaxStockReturn(val, 'manual');
    });

    manualInput.addEventListener('keypress', (e) => {
        if(e.key === 'Enter') {
            btnManual.click();
        }
    });

    photoInput.addEventListener('change', function(e) {
        if (e.target.files.length === 0) return;
        const file = e.target.files[0];
        document.getElementById('label-upload-status').innerText = "File terunggah";

        html5QrCode.scanFile(file, true)
            .then(decodedText => { processAjaxStockReturn(decodedText.trim(), 'scan'); })
            .catch(err => { 
                Swal.fire({ icon: 'error', title: 'Gagal Dekripsi', text: 'Format Barcode/QR tidak terdeteksi.' }); 
                resetSystemState();
            });
    });
</script>

<style>
    .table-body-data tr td, .table-body-data tr td div { color: #000000 !important; }
    .dark .table-body-data tr td { color: #cbd5e1 !important; }
    .table-header-row th { color: #ffffff !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 6px; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
</style>
@endsection