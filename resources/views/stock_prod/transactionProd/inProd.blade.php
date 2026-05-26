@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-950 dark:text-white">
        Production Stock In Activities
      </h2>
      <p class="text-xs font-medium text-slate-600 dark:text-gray-400">Track and manage received nozzles on the production floor</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ Route::has('prod.transaction.in.scan') ? route('prod.transaction.in.scan') : '#' }}"
        class="inline-flex items-center gap-2 rounded-lg border border-transparent bg-gradient-to-r from-emerald-600 to-teal-500 px-3 py-2 text-xs font-bold text-white shadow-sm hover:opacity-90 transition-all active:scale-95"
      >
        <i class="fas fa-qrcode"></i> Scan IN (Prod)
      </a>
      <a href="{{ Route::has('prod.transaction.in.manual') ? route('prod.transaction.in.manual') : '#' }}"
        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-indigo-600 to-blue-500 px-3 py-2 text-xs font-bold text-white shadow-md hover:opacity-90 transition-all active:scale-95"
      >
        <i class="fas fa-keyboard"></i> Manual IN (Prod)
      </a>
    </div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-slate-900 sm:px-6">
    
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-bold text-slate-950 dark:text-white">
          Production Inflow History
        </h3>
      </div>

       <div class="flex flex-wrap items-center gap-3">
        <div class="inline-flex p-1 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <button type="button" onclick="filterTable('all', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterTable('success', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Success
          </button>
          <button type="button" onclick="filterTable('pending', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Pending
          </button>
          <button type="button" onclick="filterTable('manual in', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Manual In
          </button>
          <button type="button" onclick="filterTable('scan in', this)" class="filter-btn px-3 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Scan In
          </button>
        </div>

        <button
          class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700/50"
        >
          <svg class="stroke-current" width="14" height="14" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.29004 5.90393H17.7067" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M17.7075 14.0961H2.29085" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331 12.0826 3.33331Z" stroke-width="1.5" />
            <path d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z" stroke-width="1.5" />
          </svg>
          Filter
        </button>
      </div>
    </div>

    <div class="w-full overflow-x-auto scrollbar-hide">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50 dark:bg-slate-800/40">
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-12">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">INPRODUCTION ID</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">DATE</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">NIK</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">LINE ID</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">NO NOZZLE</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">TRANSACTION OUT ID</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">REQUEST NO</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">BARCODE ID</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">STOCK PROD ID</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">QTY IN</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">STATUS</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">REMARK</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">COMMENT</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">CREATED AT</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">UPDATED AT</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          
          @if(isset($history) && count($history) > 0)
            
            @foreach($history as $log)
            <tr class="table-row-item hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
              
              <td class="py-3 px-3 text-xs font-bold text-slate-600 dark:text-slate-400 text-center font-mono">
                {{ ($history->currentPage() - 1) * $history->perPage() + $loop->iteration }}
              </td>

              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white text-center font-mono">
                {{ $log->inproduction_id }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white whitespace-nowrap">
                {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') : '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono">
                {{ $log->nik ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-indigo-600 dark:text-indigo-400 font-mono">
                {{ $log->line_id ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white">
                {{ $log->no_nozzle ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono">
                {{ $log->transaction_out_id ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-amber-600 dark:text-amber-400 font-mono">
                {{ $log->request_no ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono">
                {{ $log->barcode_id ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono">
                {{ $log->stock_prod_id ?? '-' }}
              </td>
              
              <td class="py-3 px-3 text-center">
                <span class="inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                  +{{ $log->qty_in ?? 0 }}
                </span>
              </td>
              
              <td class="py-3 px-3 text-center">
                <span class="status-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight
                  @if(strtolower($log->status ?? '') == 'success') bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400
                  @elseif(strtolower($log->status ?? '') == 'pending') bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400
                  @else bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 @endif">
                  {{ $log->status ?? 'success' }}
                </span>
              </td>
              
              <td class="py-3 px-3 text-center">
                @php
                  $remarkText = $log->remark ?? '';
                  $remarkLower = strtolower($remarkText);
                  $isManual = str_contains($remarkLower, 'manual');
                  $isScan = str_contains($remarkLower, 'scan');
                @endphp

                <span class="remark-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight
                  @if($isManual) bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                  @elseif($isScan) bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                  @else bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600 @endif">
                  {{ $remarkText ? $log->remark : '-' }} 
                </span>
              </td>
              
              <td class="py-3 px-3 text-xs font-medium text-slate-700 dark:text-gray-300 max-w-[150px] truncate" title="{{ $log->comment }}">
                {{ $log->comment ?? '-' }}
              </td>

              <td class="py-3 px-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                {{ $log->created_at ? $log->created_at->toDateTimeString() : '-' }}
              </td>

              <td class="py-3 px-3 text-xs font-medium text-slate-500 dark:text-slate-400 whitespace-nowrap">
                {{ $log->updated_at ? $log->updated_at->toDateTimeString() : '-' }}
              </td>
            </tr>
            @endforeach

          @else
            {{-- 🔥 FIXED: Karena total kolom bertambah menjadi 16, colspan diubah ke 16 agar simetris --}}
            <tr>
              <td colspan="16" class="py-12 text-center text-slate-400 italic bg-white dark:bg-slate-900 text-xs font-bold">
                No recent production incoming history records found.
              </td>
            </tr>
          @endif

        </tbody>
      </table>
    </div>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-2 border-t border-gray-100 pt-4 dark:border-gray-800">
      <p class="text-xs font-bold text-slate-950 dark:text-white">
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

<style>
  .scrollbar-hide::-webkit-scrollbar { display: none; }
  nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
  nav[role="navigation"] div:first-child { display: none; }
  .pagination .page-item.active .page-link {
    background-color: #3C50E0 !important;
    border-color: #3C50E0 !important;
    color: white !important;
    font-weight: bold;
    font-size: 12px;
  }
  .pagination .page-link {
    color: inherit !important;
    font-weight: 700;
    font-size: 12px;
    padding: 4px 8px;
  }
  .dark .pagination .page-item:not(.active) .page-link {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
  }
</style>

<script>
  function filterTable(criteria, element) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => {
      btn.classList.remove('bg-white', 'text-slate-950', 'shadow-sm', 'dark:bg-gray-700', 'dark:text-white');
      btn.classList.add('text-slate-600', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
    });

    if (element) {
      element.classList.remove('text-slate-600', 'dark:text-gray-400', 'hover:text-slate-950', 'dark:hover:text-white');
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