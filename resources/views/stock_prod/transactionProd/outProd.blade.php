@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .stock-out-prod-view, .stock-out-prod-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  /* Custom Clean Hover & Shadow matching main theme */
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

<div class="stock-out-prod-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Production Stock Out Activities
      </h2>
      <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 mt-0.5">Track and manage components discharged from the production floor</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="{{ route('prod.transaction.out.scan') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#E51E43] to-[#F86E1B] px-3 text-xs font-black text-white tracking-wider uppercase no-underline"
      >
        <span><i class="fa-solid fa-qrcode mr-1.5"></i> Scan OUT</span>
      </a>
      
      <a href="{{ route('prod.transaction.out.manual') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-3 text-xs font-black text-white tracking-wider uppercase no-underline"
      >
        <span><i class="fa-solid fa-keyboard mr-1.5"></i> Manual OUT</span>
      </a>
    </div>
  </div>

  {{-- CONTAINER DATA TABLE --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-4 pt-4 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-6">
    {{-- TABLE FILTER CONTROL --}}
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
          Production Outflow History
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
          <button type="button" onclick="filterTable('manual', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Manual Out
          </button>
          <button type="button" onclick="filterTable('scan', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
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
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">TRANSACTION ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">STOCK PROD ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">BARCODE ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">REQUEST ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">NIK PIC</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">CATEGORY</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">QTY OUT</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">PROCESS TYPE</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">STATUS</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">PHOTO PROOF</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">REMARK / KRONOLOGI</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">CREATED AT</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($history as $key => $log)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            {{-- 1. NO --}}
            <td class="py-2 px-3 text-[11px] font-extrabold text-slate-950 dark:text-white text-center">
              {{ (method_exists($history, 'currentPage')) ? ($history->firstItem() + $key) : ($key + 1) }}
            </td>
            {{-- 2. TRANSACTION ID (tx_id) --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono text-center whitespace-nowrap">
              {{ $log->tx_id }}
            </td>
            {{-- 3. STOCK PROD ID (stock_prods_id) --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono text-center whitespace-nowrap bg-slate-50/30 dark:bg-slate-800/10">
              {{ $log->stock_prods_id }}
            </td>
            {{-- 4. BARCODE ID (db_barcodes_id) --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono text-center whitespace-nowrap">
              {{ $log->db_barcodes_id ?? '-' }}
            </td>
            {{-- 5. REQUEST ID (production_request_id) --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono text-center whitespace-nowrap">
              {{ $log->production_request_id ?? '-' }}
            </td>
            {{-- 6. NIK PIC (nik_karyawan) --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono whitespace-nowrap">
              {{ $log->nik_karyawan ?? '-' }}
            </td>
            {{-- 7. CATEGORY (out_category) --}}
            <td class="py-2 px-3 text-center whitespace-nowrap">
              @if($log->out_category == 'broken')
                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-400 dark:border-rose-900/50 uppercase">
                  Broken
                </span>
              @elseif($log->out_category == 'lost')
                <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-900/50 uppercase">
                  Lost
                </span>
              @else
                <span class="text-slate-400 dark:text-slate-600 font-bold">-</span>
              @endif
            </td>
            {{-- 8. QTY OUT (qty_transaction) --}}
            <td class="py-2 px-3 text-center">
              <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-rose-50 text-rose-600 border border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20">
                -{{ $log->qty_transaction ?? 0 }}
              </span>
            </td>
            {{-- 9. PROCESS TYPE (process_type) --}}
            <td class="py-2 px-3 text-center whitespace-nowrap">
              <span class="process-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold tracking-tight uppercase
                @if($log->process_type == 'manual') bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                @else bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20 @endif">
                {{ $log->process_type ?? 'scan' }}
              </span>
            </td>
            {{-- 10. STATUS (status) --}}
            <td class="py-2 px-3 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold tracking-tight uppercase
                @if(strtolower($log->status ?? '') == 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                @elseif(strtolower($log->status ?? '') == 'pending') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                @else bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 @endif">
                {{ $log->status ?? 'success' }}
              </span>
            </td>
            {{-- 11. PHOTO PROOF (photo_path) --}}
            <td class="py-2 px-3 text-center whitespace-nowrap">
              @if($log->photo_path)
                <a href="{{ asset('storage/' . $log->photo_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                  <i class="fa-solid fa-image"></i> View Photo
                </a>
              @else
                <span class="text-slate-400 dark:text-slate-600 font-mono">-</span>
              @endif
            </td>
            {{-- 12. REMARK (remark) --}}
            <td class="py-2 px-3 text-[11px] font-semibold text-slate-600 dark:text-gray-300 max-w-[180px] truncate" title="{{ $log->remark }}">
              {{ $log->remark ?? '-' }}
            </td>
            {{-- 13. CREATED AT (created_at) --}}
            <td class="py-1.5 px-3 text-center whitespace-nowrap">
              @php
                $createdAt = null;
                if ($log->created_at) {
                  $createdAt = $log->created_at instanceof \Carbon\Carbon ? $log->created_at : \Carbon\Carbon::parse($log->created_at);
                }
              @endphp
              <div class="text-[11px] font-bold text-slate-900 dark:text-white tracking-tight">
                {{ $createdAt ? $createdAt->format('d/m/y') : '-' }}
              </div>
              <div class="text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-none mt-0.5">
                {{ $createdAt ? $createdAt->format('H:i') : '' }}
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="13" class="text-center py-6 text-xs font-bold text-slate-400 uppercase tracking-wider">
              No recent production outgoing history records found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- PAGINATION INTERFACE --}}
    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-1 border-t border-gray-100 pt-4 dark:border-gray-800">
      <p class="text-xs font-extrabold text-slate-950 dark:text-white">
        Showing {{ (isset($history) && method_exists($history, 'firstItem')) ? ($history->firstItem() ?? 0) : 0 }} 
        to {{ (isset($history) && method_exists($history, 'lastItem')) ? ($history->lastItem() ?? 0) : 0 }} 
        of {{ (isset($history) && method_exists($history, 'total')) ? ($history->total() ?? 0) : 0 }} entries
      </p>
      <div class="flex items-center">
        @if(isset($history) && method_exists($history, 'links'))
          {{ $history->links() }}
        @endif
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
      const processCell = row.querySelector('.process-cell');

      if (!statusCell || !processCell) return;

      const statusText = statusCell.textContent.trim().toLowerCase();
      const processText = processCell.textContent.trim().toLowerCase();

      if (criteria === 'success' || criteria === 'pending') {
        row.style.display = (statusText === criteria) ? '' : 'none';
      } else if (criteria === 'manual') {
        row.style.display = (processText === 'manual') ? '' : 'none';
      } else if (criteria === 'scan') {
        row.style.display = (processText === 'scan') ? '' : 'none';
      }
    });
  }
</script>
@endsection