@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-950 dark:text-white">
        Stock In Activities
      </h2>
      <p class="text-xs font-medium text-slate-600 dark:text-gray-400">Track your recent sparepart incoming activities</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('eng.in.scan') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-transparent bg-gradient-to-r from-purple-600 to-red-500 px-3 py-2 text-xs font-bold text-white shadow-sm"
      >
        <i class="fas fa-qrcode text-primary"></i> Scan IN
      </a>
      <a href="{{ route('eng.in.manual') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-gradient-to-r from-blue-600 to-amber-500 px-3 py-2 text-xs font-bold text-white shadow-md"
      >
        <i class="fas fa-keyboard"></i> Manual IN
      </a>
    </div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-slate-900 sm:px-6">
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-bold text-slate-950 dark:text-white">
          Recent History
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

    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50 dark:bg-slate-800/40">
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">DATE</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">NIK</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">No Nozzle</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">Part No</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">SAP Code</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">Category</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Qty IN</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Status</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Remark</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">Comment</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($history as $key => $log)
          <tr class="table-row-item hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white">
              {{ $history->firstItem() + $key }}
            </td>
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white">
              {{ $log->created_at->format('d/m/Y') }}
            </td>
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white">
              {{ $log->nik }}
            </td>
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white">
              {{ $log->stockEng->no_nozzle ?? '-' }}
            </td>
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono">
              {{ $log->stockEng->part_no ?? '-' }}
            </td>
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono">
              {{ $log->stockEng->sap_code ?? '-' }}
            </td>
            <td class="py-3 px-3 text-xs font-bold text-slate-700 dark:text-gray-300">
              {{ $log->stockEng->category ?? '-' }}
            </td>
            <td class="py-3 px-3 text-center">
              <span class="inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20">
                +{{ $log->qty_added }}
              </span>
            </td>
            <td class="py-3 px-3 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight
                @if(strtolower($log->status) == 'success') bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400
                @elseif(strtolower($log->status) == 'pending') bg-orange-50 text-orange-700 dark:bg-orange-500/10 dark:text-orange-400
                @else bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 @endif">
                {{ $log->status }}
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
            <td class="py-3 px-3 text-xs font-medium text-slate-700 dark:text-gray-300 max-w-[200px] truncate" title="{{ $log->comment }}">
              {{ $log->comment ?? '-' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-2 border-t border-gray-100 pt-4 dark:border-gray-800">
      <p class="text-xs font-bold text-slate-950 dark:text-white">
        Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $history->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  nav[role="navigation"] svg { width: 16px; height: 16px; display: inline; }
  nav[role="navigation"] div:first-child { display: none; }
  .pagination .page-item.active .page-link {
    background-color: #3C50E0 !important;
    border-color: #3C50E0 !important;
    color: white !important;
    font-weight: bold;
    font-size: 12px;
  }
  /* ADJUSTED FOR PAGINATION IN DARK MODE */
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

      const statusText = row.querySelector('.status-cell').textContent.trim().toLowerCase();
      const remarkText = row.querySelector('.remark-cell').textContent.trim().toLowerCase();

      if (criteria === 'success' || criteria === 'pending') {
        if (statusText === criteria) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      } else if (criteria === 'manual in') {
        if (remarkText.includes('manual')) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      } else if (criteria === 'scan in') {
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