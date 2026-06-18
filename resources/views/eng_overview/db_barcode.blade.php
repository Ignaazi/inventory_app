@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .barcode-db-view, .barcode-db-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  .action-btn-icon {
    transition: all 0.2s ease-in-out;
  }
  .action-btn-icon:hover {
    transform: scale(1.08);
    filter: brightness(1.05);
  }
</style>

<div class="barcode-db-view mx-auto max-w-screen-2xl p-5 md:p-8 2xl:p-12">
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white tracking-tight">
        Database Barcode
      </h2>
      <p class="text-sm font-semibold text-slate-500 mt-1">Track and manage your registered barcode configurations</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('barcode.parsing') }}"
        class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-extrabold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-white transition-colors"
      >
        ← Back To Customizer
      </a>
    </div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm sm:px-7">
    
    <div class="flex flex-col gap-4 mb-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-extrabold text-slate-950 dark:text-white tracking-tight">
          All Registered Composite Data
        </h3>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <div class="inline-flex p-1.5 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
          <button type="button" onclick="filterBarcodeTable('all', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-gray-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterBarcodeTable('qr code', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            QR Code
          </button>
          <button type="button" onclick="filterBarcodeTable('data matrix', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Data Matrix
          </button>
          <button type="button" onclick="filterBarcodeTable('2d code', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            2D Code
          </button>
        </div>
      </div>
    </div>

    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="barcode-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">NO</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center w-24">VISUAL</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">BARCODE ID</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">BARCODE TYPE</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">DIMENSION / CONFIG</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">FINAL COMPOSITE CONTENT</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">CREATE AT</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center w-48">ACTION</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($barcodes as $key => $barcode)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            <td class="py-4 px-4 text-[13px] font-extrabold text-slate-950 dark:text-white">
              {{ $loop->iteration }}
            </td>
            
            <td class="py-4 px-4 text-center">
              <div class="flex items-center justify-center">
                <div id="render_thumb_{{ $barcode->id }}" 
                     onclick="openBarcodeModal({{ $barcode->id }}, '{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}', '{{ $barcode->barcode_size }}')"
                     title="Click to view large"
                     class="w-[48px] h-[48px] bg-white border border-gray-200 p-1 rounded-lg shadow-sm flex items-center justify-center cursor-pointer hover:scale-105 hover:border-indigo-500 transition-all overflow-hidden bg-center bg-no-repeat dark:border-gray-700">
                </div>
              </div>
            </td>

            <td class="py-4 px-4 text-[13px] font-black text-indigo-600 dark:text-indigo-400 font-mono tracking-tight">
              {{ $barcode->barcode_id }}
            </td>

            <td class="py-4 px-4 whitespace-nowrap">
              <span class="type-cell inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold tracking-tight bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-950/40 dark:text-blue-400 dark:border-blue-900">
                {{ $barcode->barcode_type }}
              </span>
            </td>
            
            <td class="py-4 px-4 text-[13px] font-bold text-slate-900 dark:text-white max-w-[150px] truncate" title="{{ $barcode->barcode_size }}">
              {{ $barcode->barcode_size }}
            </td>

            <td class="py-4 px-4 text-[13px] font-bold text-slate-950 dark:text-white font-mono break-all max-w-xs">
              {{ $barcode->final_content }}
            </td>

            <td class="py-4 px-4 text-[13px] whitespace-nowrap">
              <div class="font-bold text-slate-700 dark:text-slate-200">
                {{ \Carbon\Carbon::parse($barcode->created_at)->format('d/m/y') }}
              </div>
              <div class="text-[11px] font-medium text-slate-400 mt-0.5">
                {{ \Carbon\Carbon::parse($barcode->created_at)->format('H:i') }} WIB
              </div>
            </td>

            <td class="py-4 px-4">
              <div class="flex items-center justify-center gap-2">
                <button type="button" 
                        onclick="openBarcodeModal({{ $barcode->id }}, '{{ $barcode->barcode_type }}', '{{ $barcode->final_content }}', '{{ $barcode->barcode_size }}')"
                        class="action-btn-icon flex items-center justify-center w-8 h-8 rounded-lg bg-blue-500 text-white shadow-sm"
                        title="Preview"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178a1.005 1.005 0 010 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                  </svg>
                </button>

                <a href="{{ route('barcode.parsing', ['edit_id' => $barcode->id]) }}" 
                   class="action-btn-icon flex items-center justify-center w-8 h-8 rounded-lg bg-[#f1c40f] text-white shadow-sm"
                   title="Edit"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125"></path>
                  </svg>
                </a>

                <form action="{{ route('barcode.db.delete', $barcode->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data barcode ini dari database?');" class="inline m-0">
                  @csrf
                  @method('DELETE')
                  <button type="submit" 
                          class="action-btn-icon flex items-center justify-center w-8 h-8 rounded-lg bg-[#e74c3c] text-white shadow-sm"
                          title="Delete"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"></path>
                    </svg>
                  </button>
                </form>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="p-12 text-center text-xs font-bold uppercase text-slate-400 tracking-wider">
              No Barcode Registered in Database
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(method_exists($barcodes, 'links'))
    <div class="mt-5 px-2">
        {{ $barcodes->links() }}
    </div>
    @endif
  </div>
</div>

<div id="barcodeModal" class="fixed inset-0 z-99999 hidden flex items-center justify-center bg-black/80 backdrop-blur-sm opacity-0 transition-opacity duration-300 ease-in-out" onclick="closeBarcodeModal(event)">
    <div class="relative bg-white dark:bg-slate-900 rounded-2xl p-8 shadow-2xl border border-gray-200 dark:border-gray-800 max-w-lg w-full transform scale-90 transition-transform duration-300 ease-in-out" onclick="event.stopPropagation()">
        
        <button onclick="closeBarcodeModal()" class="absolute top-4 right-4 text-slate-400 hover:text-rose-500 font-bold text-2xl transition-colors">&times;</button>

        <div class="text-center mb-6">
            <h3 id="modal_header_type" class="text-indigo-600 dark:text-indigo-400 font-extrabold text-sm uppercase tracking-widest mb-1"></h3>
            <p id="modal_header_size" class="text-slate-500 text-[10px] font-bold uppercase"></p>
        </div>

        <div class="bg-gray-50 dark:bg-slate-800 p-6 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700 flex items-center justify-center min-h-[250px] mb-6">
            <div id="modal_render_area" class="flex flex-col items-center justify-center"></div>
        </div>

        <div class="text-center space-y-5">
            <p id="modal_content_text" class="text-xs font-mono font-bold text-slate-700 dark:text-white break-all bg-gray-100 dark:bg-gray-800/60 p-3 rounded-lg border border-gray-200 dark:border-gray-700"></p>
            
            <button id="modal_download_btn" type="button" class="w-full inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-6 py-3.5 rounded-xl transition-all uppercase text-xs tracking-widest shadow-md">
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
    document.addEventListener("DOMContentLoaded", function() {
        @foreach($barcodes as $b)
            renderThumbnail({{ $b->id }}, '{{ $b->barcode_type }}', '{{ $b->final_content }}');
        @endforeach
    });

    function renderThumbnail(id, type, content) {
        const targetDiv = document.getElementById(`render_thumb_${id}`);
        if (!targetDiv) return;

        if (type === 'QR CODE') {
            new QRCode(targetDiv, { text: content, width: 38, height: 38, colorDark : "#000000", colorLight : "#ffffff", correctLevel : QRCode.CorrectLevel.H });
            setTimeout(() => {
                const img = targetDiv.querySelector('img');
                if(img) { img.style.width = "38px"; img.style.height = "38px"; }
            }, 50);
        } else if (type === 'DATA MATRIX') {
            const canvas = document.createElement('canvas'); targetDiv.appendChild(canvas);
            try { bwipjs.toCanvas(canvas, { bcid: 'datamatrix', text: content, scale: 1.5, include0: true }); canvas.style.maxWidth = "38px"; canvas.style.maxHeight = "38px";
            } catch (e) { targetDiv.innerText = "Err"; }
        } else if (type === '2D CODE') {
            const canvas = document.createElement('canvas'); targetDiv.appendChild(canvas);
            try { bwipjs.toCanvas(canvas, { bcid: 'pdf417', text: content, scale: 1, height: 8, columns: 3 }); canvas.style.maxWidth = "40px";
            } catch (e) { targetDiv.innerText = "Err"; }
        } else if (type === '3D CODE') {
            const canvas = document.createElement('canvas'); canvas.width = 38; canvas.height = 38; const ctx = canvas.getContext('2d'); ctx.fillStyle = "#4f46e5"; ctx.fillRect(0, 0, 38, 38); ctx.fillStyle = "#10b981"; ctx.fillRect(6, 6, 26, 26); targetDiv.appendChild(canvas);
        }
    }

    function filterBarcodeTable(criteria, element) {
        const buttons = document.querySelectorAll('.filter-btn');
        buttons.forEach(btn => {
            btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
            btn.classList.add('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
        });

        if (element) {
            element.classList.remove('text-slate-500', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
            element.classList.add('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
        }

        const rows = document.querySelectorAll('.table-row-item');
        rows.forEach(row => {
            if (criteria === 'all') {
                row.style.display = '';
                return;
            }
            const typeText = row.querySelector('.type-cell').textContent.trim().toLowerCase();
            if (typeText === criteria) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
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
@endsection