@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .stock-out-view, .stock-out-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  .photo-grad-btn {
    transition: all 0.2s ease-in-out;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
  }
  .photo-grad-btn:hover {
    transform: translateY(-1px);
    filter: brightness(1.05);
    box-shadow: 0 6px 14px rgba(0, 0, 0, 0.18);
  }
  .photo-grad-btn:active {
    transform: translateY(0);
  }
</style>

<div class="stock-out-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Stock Out Activities
      </h2>
      <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 mt-0.5">Track your recent sparepart outgoing and dispatch activities</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="{{ route('eng.out.scan') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#E51E43] to-[#F86E1B] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Scan OUT</span>
      </a>
      
      <a href="{{ route('eng.out.manual') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Manual OUT</span>
      </a>
    </div>
  </div>

  {{-- CONTAINER DATA TABLE --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-4 pt-4 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-6">
    {{-- TABLE FILTER CONTROL --}}
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
          Recent History Out
        </h3>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <div class="inline-flex p-1 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
          <button type="button" onclick="filterTable('all', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterTable('success', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Success
          </button>
          <button type="button" onclick="filterTable('pending', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Pending
          </button>
          <button type="button" onclick="filterTable('manual out', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Manual Out
          </button>
          <button type="button" onclick="filterTable('scan out', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Scan Out
          </button>
        </div>
      </div>
    </div>

    {{-- MAIN TABLE LOG OUT --}}
    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">TRANSACTION OUT ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">NIK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">REQ SPAREPART ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">BARCODE ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">NO NOZZLE</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">NO RAK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">QTY OUT</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">STATUS</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">REMARK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">COMMENT</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">CREATED AT</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">UPDATED AT</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($history as $key => $log)
          @php
            $reqSparepartNo = '-';
            $noNozzleText = '-';
            $namaRakText = '-';

            if (!empty($log->barcode_id)) {
                // 1. Ambil info Barcode langsung menggunakan ID angka dari log
                $dbBarcodeRow = \DB::table('db_barcodes')->where('id', $log->barcode_id)->first();
                
                // Jika tidak ketemu pakai ID angka, coba cari pakai string barcode
                if (!$dbBarcodeRow) {
                    $dbBarcodeRow = \DB::table('db_barcodes')->where('barcode_id', $log->barcode_id)->first();
                }
                
                if ($dbBarcodeRow) {
                    // 2. Hubungkan ke barcode_parsings menggunakan id dari db_barcodes[cite: 1]
                    $parsingRow = \DB::table('barcode_parsings')->where('barcode_db_id', $dbBarcodeRow->id)->first();
                    
                    if ($parsingRow) {
                        // 3. Hubungkan ke production_requests menggunakan production_request_id asli dari tabel[cite: 1]
                        $prodRequestRow = \DB::table('production_requests')->where('id', $parsingRow->production_request_id)->first();
                        if ($prodRequestRow) {
                            $reqSparepartNo = $prodRequestRow->request_no ?? '-';
                            $noNozzleText = $prodRequestRow->no_nozzle ?? '-';
                        }
                    }

                    // Ambil nama rak jika ada relasi rak_id
                    if (!empty($dbBarcodeRow->rak_id)) {
                        $rakRow = \DB::table('raks')->where('id', $dbBarcodeRow->rak_id)->value('nama_rak');
                        if($rakRow) $namaRakText = $rakRow;
                    }
                }

                // STRATEGI FALLBACK: Jika db_barcodes bypass gagal, langsung loncat tembak ke barcode_parsings pakai ID log[cite: 1]
                if ($reqSparepartNo === '-') {
                    $parsingFallback = \DB::table('barcode_parsings')->where('barcode_db_id', $log->barcode_id)->first();
                    if ($parsingFallback) {
                        $prodRequestRow = \DB::table('production_requests')->where('id', $parsingFallback->production_request_id)->first();
                        if ($prodRequestRow) {
                            $reqSparepartNo = $prodRequestRow->request_no ?? '-';
                            $noNozzleText = $prodRequestRow->no_nozzle ?? '-';
                        }
                    }
                }
            }

            // Fallback internal log / master jika data nozzle & rak dari request di atas kosong
            if ($noNozzleText === '-') {
                $noNozzleText = $log->no_nozzle ?? ($log->stockEng->no_nozzle ?? '-');
            }
            if ($namaRakText === '-') {
                $namaRakText = $log->rak->nama_rak ?? ($log->stockEng->rak->nama_rak ?? '-');
            }
          @endphp
          
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            {{-- 1. NO --}}
            <td class="py-2 px-3 text-[11px] font-extrabold text-slate-955 dark:text-white">
              {{ $history->firstItem() + $key }}
            </td>
            {{-- 2. TRANSACTION OUT ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono text-center whitespace-nowrap">
              {{ $log->transaction_out_id }}
            </td>
            {{-- 3. NIK --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white whitespace-nowrap">
              {{ $log->nik }}
            </td>
            {{-- 4. REQ SPAREPART ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white text-center font-mono whitespace-nowrap text-blue-600 dark:text-blue-400">
              {{ $reqSparepartNo }}
            </td>
            {{-- 5. BARCODE ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->barcode_id ?? '-' }}
            </td>
            {{-- 6. NO NOZZLE --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap text-emerald-600 dark:text-emerald-400">
              {{ $noNozzleText }}
            </td>
            {{-- DATA NO RAK --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono whitespace-nowrap">
              {{ $namaRakText }}
            </td>
            {{-- 7. QTY OUT --}}
            <td class="py-2 px-3 text-center">
              <span class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                -{{ $log->qty_out }}
              </span>
            </td>
            {{-- 8. STATUS --}}
            <td class="py-2 px-3 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold tracking-tight uppercase
                @if(strtolower($log->status) == 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                @elseif(strtolower($log->status) == 'pending') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                @else bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 @endif">
                {{ $log->status }}
              </span>
            </td>
            {{-- 9. REMARK --}}
            <td class="py-2 px-3 text-center">
              @php
                $remarkText = $log->remark ?? '';
                $remarkLower = strtolower($remarkText);
                $isManual = str_contains($remarkLower, 'manual');
                $isScan = str_contains($remarkLower, 'scan');
              @endphp

              <span class="remark-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[9px] font-extrabold tracking-tight uppercase
                @if($isManual) bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                @elseif($isScan) bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                @else bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600 @endif">
                {{ $remarkText ? $log->remark : '-' }} 
              </span>
            </td>
            {{-- 10. COMMENT --}}
            <td class="py-2 px-3 text-[11px] font-semibold text-slate-600 dark:text-gray-300 max-w-[130px] truncate" title="{{ $log->comment }}">
              {{ $log->comment ?? '-' }}
            </td>
            {{-- 11. CREATED AT --}}
            <td class="py-1.5 px-3 text-center whitespace-nowrap">
              <div class="text-[11px] font-bold text-slate-900 dark:text-white tracking-tight">
                {{ $log->created_at ? $log->created_at->format('d/m/y') : '-' }}
              </div>
              <div class="text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-none mt-0.5">
                {{ $log->created_at ? $log->created_at->format('H:i') : '' }}
              </div>
            </td>
            {{-- 12. UPDATED AT --}}
            <td class="py-1.5 px-3 text-center whitespace-nowrap">
              <div class="text-[11px] font-bold text-slate-900 dark:text-white tracking-tight">
                {{ $log->updated_at ? $log->updated_at->format('d/m/y') : '-' }}
              </div>
              <div class="text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-none mt-0.5">
                {{ $log->updated_at ? $log->updated_at->format('H:i') : '' }}
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="13" class="text-center py-6 text-xs font-bold text-slate-400 uppercase tracking-wider">
              No recent stock out logs found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- PAGINATION INTERFACE --}}
    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-1 border-t border-gray-100 pt-4 dark:border-gray-800">
      <p class="text-xs font-extrabold text-slate-950 dark:text-white">
        Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $history->links() }}
      </div>
    </div>
  </div>
</div>

{{-- CSS OVERRIDES PAGINATION --}}
<style>
  nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
  nav[role="navigation"] div:first-child { display: none; }
  .pagination .page-item.active .page-link {
    background-color: #2563EB !important;
    border-color: #2563EB !important;
    color: white !important;
    font-weight: 800;
    font-size: 12px;
  }
  .pagination .page-link {
    color: inherit !important;
    font-weight: 700;
    font-size: 12px;
    padding: 5px 10px;
  }
  .dark .pagination .page-item:not(.active) .page-link {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
  }
</style>

{{-- JAVASCRIPT REALTIME ROW FILTER --}}
<script>
  function filterTable(criteria, element) {
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

      const statusCell = row.querySelector('.status-cell');
      const sampleRemarkCell = row.querySelector('.remark-cell');

      const statusText = statusCell ? statusCell.textContent.trim().toLowerCase() : '';
      const remarkText = sampleRemarkCell ? sampleRemarkCell.textContent.trim().toLowerCase() : '';

      if (criteria === 'success' || criteria === 'pending') {
        if (statusText === criteria) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      } else if (criteria === 'manual out') {
        if (remarkText.includes('manual')) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      } else if (criteria === 'scan out') {
        if (remarkText.includes('scan')) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    });
  }
</script>
@endsection