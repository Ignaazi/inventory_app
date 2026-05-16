@extends('admin')

@section('content')
<div class="-m-10 bg-slate-50 dark:bg-boxdark-2 min-h-screen">
    <div class="p-10">
        <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black uppercase text-slate-800 dark:text-white">Database Barcode</h1>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1">Engineering / Registered Barcode Logs</p>
            </div>
            <a href="{{ route('barcode.parsing') }}" class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white font-black px-5 py-3 rounded-xl transition-all uppercase text-xs tracking-widest">
                ← Back To Customizer
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-4 bg-emerald-100 border border-emerald-200 text-emerald-700 rounded-xl font-bold text-xs uppercase tracking-wide">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white dark:bg-boxdark rounded-2xl border border-slate-200 dark:border-strokedark shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 dark:border-strokedark flex items-center justify-between">
                <h2 class="text-sm font-black uppercase text-slate-800 dark:text-white">All Registered Composite Data</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-meta-4 border-b border-slate-100 dark:border-strokedark">
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-16">ID</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-28 text-center">Visual</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-40">Barcode Type</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-32">Dimension</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500">Final Composite Content</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-40">Saved At</th>
                            <th class="p-4 text-[10px] font-black uppercase text-slate-500 w-24 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-strokedark">
                        @forelse($barcodes as $barcode)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-meta-4/20 transition-all">
                            <td class="p-4 text-xs font-black text-slate-400">#{{ $barcode->id }}</td>
                            
                            <td class="p-4 text-center">
                                <div class="flex items-center justify-center">
                                    <div id="render_thumb_{{ $barcode->id }}" 
                                         onclick="openBarcodeModal({{ $barcode->id }}, '{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}', {{ $barcode->barcode_size ?? 10 }})"
                                         title="Click to view large"
                                         class="w-[60px] h-[60px] bg-white border border-slate-200 p-1 rounded-lg shadow-sm flex items-center justify-center cursor-pointer hover:scale-105 hover:border-indigo-500 transition-all overflow-hidden bg-center bg-no-repeat">
                                    </div>
                                </div>
                            </td>

                            <td class="p-4">
                                <span class="bg-indigo-50 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-[10px] font-black px-2.5 py-1.5 rounded-lg border border-indigo-100 dark:border-indigo-800 uppercase tracking-wide">
                                    {{ $barcode->barcode_type }}
                                </span>
                            </td>
                            
                            <td class="p-4 text-xs font-black text-slate-600 dark:text-slate-300">
                                {{ $barcode->barcode_size }}mm × {{ $barcode->barcode_size }}mm
                            </td>

                            <td class="p-4">
                                <div class="bg-slate-50 dark:bg-slate-800/60 font-mono font-black text-xs text-indigo-600 dark:text-indigo-400 px-3 py-2 rounded-xl border border-slate-100 dark:border-strokedark break-all inline-block max-w-xs shadow-sm">
                                    {{ $barcode->final_content }}
                                </div>
                            </td>

                            <td class="p-4 text-xs text-slate-400 font-bold">
                                {{ \Carbon\Carbon::parse($barcode->created_at)->format('Y-m-d H:i') }}
                            </td>

                            <td class="p-4 text-center">
                                <form action="{{ route('barcode.db.delete', $barcode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data barcode ini dari database?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-black px-3 py-2 rounded-lg transition-all text-[10px] uppercase tracking-wider border border-rose-100">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-12 text-center text-xs font-black uppercase text-slate-400">
                                No Barcode Registered in Database
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="barcodeModal" class="fixed inset-0 z-99999 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-in-out" onclick="closeBarcodeModal(event)">
    <div class="relative bg-white dark:bg-boxdark rounded-2xl p-8 shadow-2xl border border-slate-200 dark:border-strokedark max-w-lg w-full transform scale-90 transition-transform duration-300 ease-in-out" onclick="event.stopPropagation()">
        
        <button onclick="closeBarcodeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-rose-500 font-bold text-2xl transition-colors">&times;</button>

        <div class="text-center mb-6">
            <h3 id="modal_header_type" class="text-indigo-600 font-black text-xs uppercase tracking-widest mb-1"></h3>
            <p id="modal_header_size" class="text-slate-500 text-[10px] font-bold uppercase"></p>
        </div>

        <div class="bg-slate-50 dark:bg-meta-4 p-6 rounded-2xl border-2 border-dashed border-slate-200 dark:border-strokedark flex items-center justify-center min-h-[250px] mb-6">
            <div id="modal_render_area" class="flex flex-col items-center justify-center"></div>
        </div>

        <div class="text-center space-y-5">
            <p id="modal_content_text" class="text-xs font-mono font-black text-slate-700 dark:text-white break-all bg-slate-100 dark:bg-slate-800/60 p-3 rounded-lg border border-slate-100 dark:border-strokedark"></p>
            
            <button id="modal_download_btn" type="button" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-black px-6 py-3.5 rounded-xl transition-all uppercase text-xs tracking-widest shadow-lg shadow-emerald-200 dark:shadow-none">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
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
    // 1. ENGINE RENDER THUMBNAIL (DI TABEL)
    document.addEventListener("DOMContentLoaded", function() {
        @foreach($barcodes as $b)
            renderThumbnail({{ $b->id }}, '{{ $b->barcode_type }}', '{{ $b->final_content }}');
        @endforeach
    });

    function renderThumbnail(id, type, content) {
        const targetDiv = document.getElementById(`render_thumb_${id}`);
        if (!targetDiv) return;

        if (type === 'QR CODE') {
            new QRCode(targetDiv, { text: content, width: 50, height: 50, colorDark : "#000000", colorLight : "#ffffff", correctLevel : QRCode.CorrectLevel.H });
            setTimeout(() => {
                const img = targetDiv.querySelector('img');
                if(img) { img.style.width = "50px"; img.style.height = "50px"; }
            }, 50);
        } else if (type === 'DATA MATRIX') {
            const canvas = document.createElement('canvas'); targetDiv.appendChild(canvas);
            try { bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: content, scale: 2, include0: true }); canvas.style.maxWidth = "50px"; canvas.style.maxHeight = "50px";
            } catch (e) { targetDiv.innerText = "Err"; }
        } else if (type === '2D CODE') {
            const canvas = document.createElement('canvas'); targetDiv.appendChild(canvas);
            try { bwipjs.toCanvas(canvas, { bcid: 'pdf417', text: content, scale: 1, height: 10, columns: 3 }); canvas.style.maxWidth = "55px";
            } catch (e) { targetDiv.innerText = "Err"; }
        } else if (type === '3D CODE') {
            const canvas = document.createElement('canvas'); canvas.width = 50; canvas.height = 50; const ctx = canvas.getContext('2d'); ctx.fillStyle = "#4f46e5"; ctx.fillRect(0, 0, 50, 50); ctx.fillStyle = "#10b981"; ctx.fillRect(8, 8, 34, 34); targetDiv.appendChild(canvas);
        }
    }

    // ================== LOGIC MODAL & DOWNLOAD ==================
    let currentDownloadData = null; // Temporary storage for download logic

    function openBarcodeModal(id, type, content, sizeMm) {
        const modal = document.getElementById('barcodeModal');
        const renderArea = document.getElementById('modal_render_area');
        
        // Reset Modal State
        modal.classList.remove('hidden');
        renderArea.innerHTML = '';
        
        // Animate In
        setTimeout(() => {
            modal.classList.add('opacity-100');
            modal.firstElementChild.classList.remove('scale-90');
        }, 10);

        // Fill Metadata
        document.getElementById('modal_header_type').innerText = `${type} VISUALIZATION`;
        document.getElementById('modal_header_size').innerText = `TARGET PHYSICAL SIZE: ${sizeMm}mm x ${sizeMm}mm`;
        document.getElementById('modal_content_text').innerText = content;

        // Render High-Res Version in Modal (Using physical scale logic from customizer)
        let renderPixelSize = 100 + ((sizeMm - 1) * 10.5); 
        currentDownloadData = { url: '', filename: '' }; // Reset

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
            currentDownloadData = null; // Clear data
        }, 300);
    }

    // Prepare filename and URL but don't trigger download yet
    function prepareDownload(dataUrl, type, content) {
        const safeName = content.replace(/[^a-z0-9]/gi, '_').toLowerCase();
        currentDownloadData.url = dataUrl;
        currentDownloadData.filename = `db_barcode_${type.replace(/\s+/g, '_').toLowerCase()}_${safeName}.png`;
    }

    // Set up click handler for the download button inside modal
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
@endsection