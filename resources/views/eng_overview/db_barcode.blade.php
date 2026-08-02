@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    .swal2-popup { border-radius: 1rem !important; font-family: 'Nunito', sans-serif !important; }
    .dark .swal2-popup { background-color: #0f172a !important; border: 1px solid #1e293b !important; }
    .dark .swal2-title, .dark .swal2-html-container { color: #f8fafc !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">

    {{-- Banner Top Alert Status Counter --}}
    <div class="mb-4 flex items-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 dark:bg-emerald-950/30 dark:border-emerald-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
        <span class="h-2 w-2 shrink-0 rounded-full bg-emerald-500 animate-pulse"></span>
        <p class="text-[12px] md:text-[14px] font-bold text-emerald-800 dark:text-emerald-400 font-nunito leading-tight">
            <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">SYSTEM RECORD:</span> 
            Total {{ $barcodes->total() }} registered barcode configurations logged in database.
        </p>
    </div>

    {{-- Header Section --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
        <div>
            <h2 class="text-xl md:text-2xl font-black text-slate-900 dark:text-white tracking-tight">Database Barcode Master</h2>
            <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">PT SIIX EMS KARAWANG</p>
        </div>
        <div>
            <a href="{{ route('barcode.parsing') }}" class="flex items-center justify-center gap-1.5 rounded-lg border border-gray-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs md:text-[13px] font-black text-slate-950 dark:text-white shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-all active:scale-95 cursor-pointer font-nunito">
                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Back To Customizer
            </a>
        </div>
    </div>

    {{-- PEMBUNGKUS UTAMA TABEL --}}
    <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">

        {{-- AREA SCROLL HORIZONTAL --}}
        <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
            <table class="w-full table-fixed text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1250px]" id="barcode-table">
                <thead>
                    <tr class="text-[12px] font-black uppercase tracking-wider bg-blue-600 dark:bg-blue-950/80 text-white dark:text-blue-200 font-nunito table-header-row">
                        <th class="px-2 py-3.5 w-[50px] text-center">NO</th>
                        <th class="px-2 py-3.5 w-[90px] border-l border-blue-500 dark:bg-blue-900/50 text-center">VISUAL</th>
                        <th class="px-3 py-3.5 w-[250px] border-l border-blue-500 dark:bg-blue-900/50 text-center">BARCODE ID</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:bg-blue-900/50 text-center">BARCODE TYPE</th>
                        <th class="px-3 py-3.5 w-[160px] border-l border-blue-500 dark:bg-blue-900/50 text-center">DIMENSION / CONFIG</th>
                        <th class="px-3 py-3.5 w-[180px] border-l border-blue-500 dark:bg-blue-900/50 text-center">CURRENT LIFECYCLE</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:bg-blue-900/50 text-center">CREATED AT</th>
                        <th class="px-3 py-3.5 w-[130px] border-l border-blue-500 dark:bg-blue-900/50 text-center">UPDATED AT</th>
                        <th class="px-3 py-3.5 w-[140px] border-l border-blue-500 dark:bg-blue-900/50 text-center">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold font-nunito bg-transparent table-body-data">
                    @forelse($barcodes as $index => $barcode)
                    <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-850/40 transition-colors duration-150 bg-transparent">
                        <td class="px-2 py-3.5 text-center">
                            {{ $barcodes->firstItem() + $index }}
                        </td>
                        
                        <td class="px-2 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
                            <div class="flex items-center justify-center">
                                <div id="render_thumb_{{ $barcode->id }}" 
                                     onclick="openBarcodeModal({{ $barcode->id }}, '{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}', '{{ $barcode->barcode_size }}')"
                                     title="Click to view large"
                                     class="w-[44px] h-[44px] bg-white border border-gray-200 p-1 rounded-xl shadow-sm flex items-center justify-center cursor-pointer hover:scale-105 hover:border-blue-500 dark:hover:border-blue-400 transition-all overflow-hidden bg-center bg-no-repeat dark:border-slate-700 dark:bg-slate-800">
                                </div>
                            </div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-extrabold text-center">
                            <span class="inline-flex px-2.5 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/40 text-indigo-600 dark:text-indigo-400 font-mono tracking-tight text-[12px] border border-indigo-100 dark:border-indigo-900/40 whitespace-normal break-all leading-tight">
                                {{ $barcode->barcode_id }}
                            </span>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center">
                                <span class="type-cell inline-flex items-center rounded-lg border px-2.5 py-0.5 text-[10px] font-black uppercase tracking-wider shadow-sm bg-blue-50 text-blue-950 border-blue-300 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900/50 status-badge">
                                    {{ $barcode->barcode_type }}
                                </span>
                            </div>
                        </td>
                        
                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center truncate" title="{{ $barcode->barcode_size }}">
                            {{ $barcode->barcode_size }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center font-bold text-slate-900 dark:text-slate-100 whitespace-normal break-words">
                            {{ $barcode->current_lifecycle ?? '-' }}
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold">{{ \Carbon\Carbon::parse($barcode->created_at)->format('d/m/Y') }}</div>
                            <div class="text-[10px] mt-0.5 tracking-wide text-slate-400 dark:text-slate-500">{{ \Carbon\Carbon::parse($barcode->created_at)->format('H:i') }} WIB</div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-semibold whitespace-nowrap text-center">
                            <div class="font-bold">{{ \Carbon\Carbon::parse($barcode->updated_at)->format('d/m/Y') }}</div>
                            <div class="text-[10px] mt-0.5 tracking-wide text-slate-400 dark:text-slate-500">{{ \Carbon\Carbon::parse($barcode->updated_at)->format('H:i') }} WIB</div>
                        </td>

                        <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
                            <div class="flex justify-center items-center gap-1.5">
                                {{-- PREVIEW BUTTON --}}
                                <button type="button" 
                                        onclick="openBarcodeModal({{ $barcode->id }}, '{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}', '{{ $barcode->barcode_size }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-blue-500 hover:bg-blue-600 text-white transition-all shadow-md active:scale-95 cursor-pointer"
                                        title="Preview"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178c.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>

                                {{-- DOWNLOAD BUTTON --}}
                                <button type="button" 
                                        onclick="downloadBarcodeDirectly('{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white transition-all shadow-md active:scale-95 cursor-pointer"
                                        title="Download PNG"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                                    </svg>
                                </button>

                                {{-- PRINT BUTTON --}}
                                <button type="button" 
                                        onclick="printBarcodeDirectly('{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}', '{{ $barcode->barcode_size }}')"
                                        class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-purple-500 hover:bg-purple-600 text-white transition-all shadow-md active:scale-95 cursor-pointer"
                                        title="Print Barcode"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096a42.415 42.415 0 00-10.56 0m10.56 0L17.66 18m0 0a2.25 2.25 0 01-2.244 2.077H8.584A2.25 2.25 0 016.34 18m11.32 0h.008v.008h-.008V18zm-.008-6.141L16.14 4.31A2.25 2.25 0 0013.92 2.25H10.08a2.25 2.25 0 00-2.22 2.06L6.34 11.859m11.32 0a2.25 2.25 0 002.25-2.25V8.584a2.25 2.25 0 00-2.25-2.25m0 5.515a2.25 2.25 0 01-2.25 2.25H6.34"></path>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-10 text-center italic font-medium text-[13px] font-nunito dark:bg-slate-900 table-empty-text">
                            No Barcode Registered in Database
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- FOOTER PAGINATION --}}
        <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
            <p class="text-[11px] font-black tracking-wide uppercase text-black dark:text-slate-400">
                Showing {{ $barcodes->firstItem() ?? 0 }} to {{ $barcodes->lastItem() ?? 0 }} of {{ $barcodes->total() ?? 0 }} Entries
            </p>
            <div class="flex items-center justify-center gap-1.5 text-xs custom-pagination text-black dark:text-white">
                @if(method_exists($barcodes, 'links'))
                    {{ $barcodes->links() }}
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL PREVIEW BARCODE --}}
<div id="barcodeModal" class="fixed inset-0 z-99999 hidden flex items-center justify-center bg-slate-950/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-in-out" onclick="closeBarcodeModal(event)">
    <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-6 md:p-8 shadow-2xl border border-gray-200 dark:border-slate-800 max-w-md w-full transform scale-90 transition-transform duration-300 ease-in-out mx-4" onclick="event.stopPropagation()">
        
        <button onclick="closeBarcodeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-rose-500 font-bold text-2xl transition-colors">&times;</button>

        <div class="text-center mb-5">
            <h3 id="modal_header_type" class="text-indigo-600 dark:text-indigo-400 font-black text-[13px] tracking-widest uppercase mb-1"></h3>
            <p id="modal_header_size" class="text-slate-400 dark:text-slate-500 text-[10px] font-bold uppercase tracking-wider"></p>
        </div>

        <div class="bg-slate-50 dark:bg-slate-950 p-6 rounded-xl border border-gray-100 dark:border-slate-800/80 flex items-center justify-center min-h-[240px] mb-5 shadow-inner">
            <div id="modal_render_area" class="flex flex-col items-center justify-center bg-white p-4 rounded-xl shadow-sm border border-gray-100"></div>
        </div>

        <div class="text-center space-y-4 font-nunito">
            <p id="modal_content_text" class="text-xs font-mono font-bold text-slate-700 dark:text-slate-300 break-all bg-slate-50 dark:bg-slate-950 p-3 rounded-xl border border-gray-100 dark:border-slate-800"></p>
            
            <button id="modal_download_btn" type="button" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-3.5 rounded-xl transition-all uppercase text-xs tracking-widest shadow-md active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"></path>
                </svg>
                Download PNG (Actual Size)
            </button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bwip-js@3.0.4/dist/bwip-js-min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        @foreach($barcodes as $b)
            renderThumbnail({{ $b->id }}, '{{ $b->barcode_type }}', '{{ $b->final_content }}');
        @endforeach
    });

    function renderThumbnail(id, type, content) {
        const targetDiv = document.getElementById(`render_thumb_${id}`);
        if (!targetDiv) return;

        if (type === 'QR CODE') {
            new QRCode(targetDiv, { text: content, width: 34, height: 34, colorDark : "#000000", colorLight : "#ffffff", correctLevel : QRCode.CorrectLevel.H });
            setTimeout(() => {
                const img = targetDiv.querySelector('img');
                if(img) { img.style.width = "34px"; img.style.height = "34px"; }
            }, 50);
        } else if (type === 'DATA MATRIX') {
            const canvas = document.createElement('canvas'); targetDiv.appendChild(canvas);
            try { bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: content, scale: 1.5, include0: true }); canvas.style.maxWidth = "34px"; canvas.style.maxHeight = "34px";
            } catch (e) { targetDiv.innerText = "Err"; }
        } else if (type === '2D CODE') {
            const canvas = document.createElement('canvas'); targetDiv.appendChild(canvas);
            try { bwipjs.toCanvas(canvas, { bcid: 'pdf417', text: content, scale: 1, height: 8, columns: 3 }); canvas.style.maxWidth = "40px";
            } catch (e) { targetDiv.innerText = "Err"; }
        } else if (type === '3D CODE') {
            const canvas = document.createElement('canvas'); canvas.width = 34; canvas.height = 34; const ctx = canvas.getContext('2d'); ctx.fillStyle = "#4f46e5"; ctx.fillRect(0, 0, 34, 34); ctx.fillStyle = "#10b981"; ctx.fillRect(6, 6, 22, 22); targetDiv.appendChild(canvas);
        }
    }

    function downloadBarcodeDirectly(type, content) {
        const canvas = document.createElement('canvas');
        const safeName = content.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        const filename = `barcode_${type.replace(/\s+/g, '_').toLowerCase()}_${safeName}.png`;

        if (type === 'QR CODE') {
            const div = document.createElement('div');
            new QRCode(div, { text: content, width: 250, height: 250, correctLevel: QRCode.CorrectLevel.H });
            setTimeout(() => {
                const img = div.querySelector('img');
                if (img) {
                    const link = document.createElement('a'); link.href = img.src; link.download = filename;
                    link.click();
                }
            }, 100);
        } else {
            let bcid = type === 'DATA MATRIX' ? 'datamatrix' : 'pdf417';
            let opts = type === 'DATA MATRIX' ? { bcid: bcid, text: content, scale: 5, include0: true } : { bcid: bcid, text: content, scale: 2, height: 10, columns: 3 };
            try {
                bwipjs.toCanvas(canvas, opts);
                const link = document.createElement('a'); link.href = canvas.toDataURL("image/png"); link.download = filename;
                link.click();
            } catch(e) { alert('Download failed: ' + e.message); }
        }
    }

    function printBarcodeDirectly(type, content, sizeString) {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Print Master Barcode</title>
                <style>
                    body { display: flex; flex-direction: column; align-items: center; justify-content: center; height: 80vh; margin: 0; font-family: 'Nunito', sans-serif; color: #000; }
                    .wrapper { text-align: center; border: 2px dashed #cbd5e1; padding: 30px; border-radius: 15px; background: #fff; }
                    .type { font-weight: 900; text-transform: uppercase; font-size: 14px; letter-spacing: 1px; margin-bottom: 5px; color: #4f46e5; }
                    .size { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; margin-bottom: 15px; }
                    .render-zone { display: inline-block; margin-bottom: 15px; padding: 10px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; }
                    .render-zone canvas, .render-zone img { max-width: 200px; height: auto; }
                    .raw-content { font-family: monospace; font-size: 12px; font-weight: 700; word-break: break-all; max-width: 280px; margin: 0 auto; background: #f8fafc; padding: 8px; border-radius: 6px; border: 1px solid #f1f5f9; }
                </style>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"><\/script>
                <script src="https://cdn.jsdelivr.net/npm/bwip-js@3.0.4/dist/bwip-js-min.js"><\/script>
            </head>
            <body>
                <div class="wrapper">
                    <div class="type">${type} VISUALIZATION</div>
                    <div class="size">CONFIG: ${sizeString}</div>
                    <div id="print_area" class="render-zone"></div>
                    <div class="raw-content">${content}</div>
                </div>
                <script>
                    const zone = document.getElementById('print_area');
                    if ('${type}' === 'QR CODE') {
                        new QRCode(zone, { text: '${content}', width: 180, height: 180, correctLevel: QRCode.CorrectLevel.H });
                    } else if ('${type}' === 'DATA MATRIX') {
                        const canvas = document.createElement('canvas'); zone.appendChild(canvas);
                        bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: '${content}', scale: 5, include0: true });
                    } else if ('${type}' === '2D CODE') {
                        const canvas = document.createElement('canvas'); zone.appendChild(canvas);
                        bwipjs.toCanvas(canvas, { bcid: 'pdf417', text: '${content}', scale: 2, height: 10, columns: 3 });
                    }
                    setTimeout(() => { window.print(); window.close(); }, 400);
                <\/script>
            </body>
            </html>
        `);
        printWindow.document.close();
    }

    let currentDownloadData = null;

    function openBarcodeModal(id, type, content, sizeString) {
        const modal = document.getElementById('barcodeModal');
        const renderArea = document.getElementById('modal_render_area');
        
        modal.classList.remove('hidden');
        renderArea.innerHTML = '';
        
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modal.firstElementChild.classList.remove('scale-90');
        }, 10);

        const matches = sizeString.match(/\d+/);
        const sizeMm = matches ? parseInt(matches[0]) : 10;

        document.getElementById('modal_header_type').innerText = `${type} VISUALIZATION`;
        document.getElementById('modal_header_size').innerText = `CONFIG: ${sizeString}`;
        document.getElementById('modal_content_text').innerText = content;

        let renderPixelSize = 100 + ((sizeMm - 1) * 10.5); 
        currentDownloadData = { url: '', filename: '' };

        if (type === 'QR CODE') {
            new QRCode(renderArea, {
                text: content, width: renderPixelSize, height: renderPixelSize,
                colorDark : "#000000", colorLight : "#ffffff", correctLevel : QRCode.CorrectLevel.H
            });
            setTimeout(() => {
                const img = renderArea.querySelector('img');
                if (img) prepareDownload(img.src, type, content);
            }, 100);
        } else if (type === 'DATA MATRIX') {
            const canvas = document.createElement('canvas'); renderArea.appendChild(canvas);
            try {
                let targetScale = sizeMm <= 3 ? 2 : (sizeMm <= 8 ? 4 : 6);
                bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: content, scale: targetScale, include0: true });
                prepareDownload(canvas.toDataURL("image/png"), type, content);
            } catch (e) { renderArea.innerText = "Error rendering: " + e.message; }
        } else if (type === '2D CODE') {
            const canvas = document.createElement('canvas'); renderArea.appendChild(canvas);
            try {
                bwipjs.toCanvas(canvas, { bcid: 'pdf417', text: content, scale: sizeMm <= 4 ? 1 : 2, height: sizeMm <= 4 ? 8 : 12, columns: 3 });
                canvas.style.width = `${renderPixelSize}px`;
                prepareDownload(canvas.toDataURL("image/png"), type, content);
            } catch (e) { renderArea.innerText = "Error rendering: " + e.message; }
        } else if (type === '3D CODE') {
            const canvas = document.createElement('canvas'); canvas.width = renderPixelSize; canvas.height = renderPixelSize; const ctx = canvas.getContext('2d'); ctx.fillStyle = "#4f46e5"; ctx.fillRect(0, 0, renderPixelSize, renderPixelSize); ctx.fillStyle = "#10b981"; ctx.fillRect(renderPixelSize*0.15, renderPixelSize*0.15, renderPixelSize*0.7, renderPixelSize*0.7);
            renderArea.appendChild(canvas);
            prepareDownload(canvas.toDataURL("image/png"), type, content);
        }
    }

    function closeBarcodeModal(event) {
        if (event && event.target !== document.getElementById('barcodeModal')) return;
        const modal = document.getElementById('barcodeModal');
        modal.classList.remove('opacity-100');
        modal.firstElementChild.classList.add('scale-90');
        setTimeout(() => {
            modal.classList.add('hidden');
            currentDownloadData = null;
        }, 300);
    }

    function prepareDownload(dataUrl, type, content) {
        const safeName = content.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        currentDownloadData.url = dataUrl;
        currentDownloadData.filename = `db_barcode_${type.replace(/\s+/g, '_').toLowerCase()}_${safeName}.png`;
    }

    document.getElementById('modal_download_btn').addEventListener('click', function() {
        if (!currentDownloadData || !currentDownloadData.url) {
            alert("Gambar barcode belum siap diunduh.");
            return;
        }
        const link = document.createElement('a');
        link.href = currentDownloadData.url;
        link.download = currentDownloadData.filename;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    });
</script>

<style>
    .font-nunito, .swal2-popup, #barcode-table { font-family: 'Nunito', sans-serif !important; }
    .table-body-data tr td, .table-body-data tr td div, .table-empty-text { color: #000000 !important; }
    .dark .table-body-data tr td, .dark .table-body-data tr td div, .dark .table-empty-text { color: #cbd5e1 !important; }
    .status-badge { color: inherit !important; }
    .table-header-row th { color: #ffffff !important; }
    .scrollbar-thin::-webkit-scrollbar { height: 7px; }
    .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
    .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
    #barcode-table td, #barcode-table th { vertical-align: middle !important; }
    .custom-pagination nav svg { width: 14px; height: 14px; display: inline; }
    .custom-pagination nav div:first-child { display: none; }
</style>
@endsection