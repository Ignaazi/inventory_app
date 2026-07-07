@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .stock-in-prod-view, .stock-in-prod-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  /* Custom Clean Hover & Shadow matching out.blade.php theme */
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

<div class="stock-in-prod-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Production Stock In Activities
      </h2>
      <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 mt-0.5">Track and manage received nozzles on the production floor</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="{{ Route::has('prod.transaction.in.scan') ? route('prod.transaction.in.scan') : '#' }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#E51E43] to-[#F86E1B] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Scan IN</span>
      </a>
      
      <a href="{{ route('prod.transaction.in.manual') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Manual IN</span>
      </a>
    </div>
  </div>

  {{-- CONTAINER DATA TABLE --}}
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-4 pt-4 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-6">
    {{-- TABLE FILTER CONTROL --}}
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
          Production Inflow History
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
          <button type="button" onclick="filterTable('manual in', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Manual In
          </button>
          <button type="button" onclick="filterTable('scan in', this)" class="filter-btn px-3 py-1 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Scan In
          </button>
        </div>

        <button
          class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-3 py-1.5 text-xs font-extrabold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700/50 transition-colors"
        >
          <svg class="stroke-current" width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.29004 5.90393H17.7067" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M17.7075 14.0961H2.29085" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z" stroke-width="1.8" />
            <path d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z" stroke-width="1.8" />
          </svg>
          Filter
        </button>
      </div>
    </div>

    {{-- MAIN TABLE LOG IN --}}
    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">INPRODUCTION ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">NIK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">LINE TARGET</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">NO NOZZLE</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">TRANSACTION OUT ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">REQUEST NO</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">BARCODE ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">STOCK PROD ID</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">QTY IN</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">STATUS</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">REMARK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">COMMENT</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">CREATED AT</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">UPDATED AT</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($history as $key => $log)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            {{-- 1. NO --}}
            <td class="py-2 px-3 text-[11px] font-extrabold text-slate-950 dark:text-white text-center">
              {{ (method_exists($history, 'firstItem')) ? ($history->firstItem() + $key) : ($key + 1) }}
            </td>
            {{-- 2. INPRODUCTION ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono text-center whitespace-nowrap">
              {{ $log->inproduction_id ?? $log->id }}
            </td>
            {{-- 3. NIK --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white font-mono whitespace-nowrap">
              {{ $log->nik ?? '-' }}
            </td>
            {{-- 4. LINE TARGET --}}
            <td class="py-2 px-3 text-center whitespace-nowrap">
              <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-slate-100 text-slate-800 border border-slate-200 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-700">
                @if(isset($log->no_line))
                  Line {{ $log->no_line }} ({{ $log->name_machine ?? 'N/A' }})
                @else
                  ID: {{ $log->line_id ?? '-' }}
                @endif
              </span>
            </td>
            {{-- 5. NO NOZZLE --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->no_nozzle ?? '-' }}
            </td>
            {{-- 6. TRANSACTION OUT ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white text-center font-mono whitespace-nowrap">
              {{ $log->transaction_out_id ?? '-' }}
            </td>
            {{-- 7. REQUEST NO --}}
            <td class="py-2 px-3 text-center whitespace-nowrap">
              <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 font-mono">
                {{ $log->request_no ?? '-' }}
              </span>
            </td>
            {{-- 8. BARCODE ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->barcode_id ?? '-' }}
            </td>
            {{-- 9. STOCK PROD ID --}}
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono whitespace-nowrap">
              {{ $log->stock_prod_id ?? '-' }}
            </td>
            {{-- 10. QTY IN --}}
            <td class="py-2 px-3 text-center">
              <span class="inline-flex items-center justify-center rounded-full px-2.5 py-0.5 text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                +{{ $log->qty_in ?? 0 }}
              </span>
            </td>
            {{-- 11. STATUS --}}
            <td class="py-2 px-3 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold tracking-tight uppercase
                @if(strtolower($log->status ?? '') == 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                @elseif(strtolower($log->status ?? '') == 'pending') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                @else bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 @endif">
                {{ $log->status ?? 'success' }}
              </span>
            </td>
            {{-- 12. REMARK --}}
            <td class="py-2 px-3 text-center whitespace-nowrap">
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
            {{-- 13. COMMENT --}}
            <td class="py-2 px-3 text-[11px] font-semibold text-slate-600 dark:text-gray-300 max-w-[130px] truncate" title="{{ $log->comment }}">
              {{ $log->comment ?? '-' }}
            </td>
            {{-- 14. CREATED AT --}}
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
            {{-- 15. UPDATED AT --}}
            <td class="py-1.5 px-3 text-center whitespace-nowrap">
              @php
                $updatedAt = null;
                if ($log->updated_at) {
                  $updatedAt = $log->updated_at instanceof \Carbon\Carbon ? $log->updated_at : \Carbon\Carbon::parse($log->updated_at);
                }
              @endphp
              <div class="text-[11px] font-bold text-slate-900 dark:text-white tracking-tight">
                {{ $updatedAt ? $updatedAt->format('d/m/y') : '-' }}
              </div>
              <div class="text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-none mt-0.5">
                {{ $updatedAt ? $updatedAt->format('H:i') : '' }}
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="15" class="text-center py-6 text-xs font-bold text-slate-400 uppercase tracking-wider">
              No recent production incoming history records found.
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
      const remarkCell = row.querySelector('.remark-cell');

      if (!statusCell || !remarkCell) return;

      const statusText = statusCell.textContent.trim().toLowerCase();
      const remarkText = remarkCell.textContent.trim().toLowerCase();

      if (criteria === 'success' || criteria === 'pending') {
        row.style.display = (statusText === criteria) ? '' : 'none';
      } else if (criteria === 'manual in') {
        row.style.display = (remarkText.includes('manual')) ? '' : 'none';
      } else if (criteria === 'scan in') {
        row.style.display = (remarkText.includes('scan')) ? '' : 'none';
      }
    });
  }
</script>
@endsection