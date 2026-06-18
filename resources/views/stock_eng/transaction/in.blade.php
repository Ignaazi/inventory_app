@extends('admin')

@section('content')
<!-- Inject Font Nunito & Global View Styling -->
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .stock-in-view, .stock-in-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  /* Custom Clean Hover & Shadow matching image_d4fed2.png */
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

<div class="stock-in-view mx-auto max-w-screen-2xl p-5 md:p-8 2xl:p-12">
  <!-- HEADER SECTION -->
  <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white tracking-tight">
        Stock In Activities
      </h2>
      <p class="text-sm font-semibold text-slate-500 dark:text-gray-400 mt-1">Track your recent sparepart incoming activities</p>
    </div>

    <!-- COMPACT ACTION BUTTONS BLOCK (Sized exactly smaller & matched to image_d4fed2.png) -->
    <div class="flex items-center gap-3 w-full sm:w-auto">
      <!-- Scan IN: Compact Red to Orange Gradient -->
      <a href="{{ route('eng.in.scan') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#E51E43] to-[#F86E1B] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Scan IN</span>
      </a>
      
      <!-- Manual IN: Compact Blue to Gold/Yellow Gradient -->
      <a href="{{ route('eng.in.manual') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Manual IN</span>
      </a>
    </div>
  </div>

  <!-- TABLE CONTAINER -->
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-7">
    
    <!-- FILTER TOP BAR -->
    <div class="flex flex-col gap-4 mb-5 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-extrabold text-slate-950 dark:text-white tracking-tight">
          Recent History
        </h3>
      </div>

      <div class="flex flex-wrap items-center gap-3">
        <div class="inline-flex p-1.5 bg-gray-100 dark:bg-slate-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-inner">
          <button type="button" onclick="filterTable('all', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-slate-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterTable('success', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Success
          </button>
          <button type="button" onclick="filterTable('pending', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Pending
          </button>
          <button type="button" onclick="filterTable('manual in', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Manual In
          </button>
          <button type="button" onclick="filterTable('scan in', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Scan In
          </button>
        </div>

        <button
          class="inline-flex items-center gap-2 rounded-xl border border-gray-300 bg-white px-4 py-2 text-xs font-extrabold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-slate-800 dark:text-white dark:hover:bg-slate-700/50 transition-colors"
        >
          <svg class="stroke-current" width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M2.29004 5.90393H17.7067" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M17.7075 14.0961H2.29085" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            <path d="M12.0826 3.33331C13.5024 3.33331 14.6534 4.48431 14.6534 5.90414C14.6534 7.32398 13.5024 8.47498 12.0826 8.47498C10.6627 8.47498 9.51172 7.32398 9.51172 5.90415C9.51172 4.48432 10.6627 3.33331/12.0826 3.33331Z" stroke-width="1.8" />
            <path d="M7.91745 11.525C6.49762 11.525 5.34662 12.676 5.34662 14.0959C5.34661 15.5157 6.49762 16.6667 7.91745 16.6667C9.33728 16.6667 10.4883 15.5157 10.4883 14.0959C10.4883 12.676 9.33728 11.525 7.91745 11.525Z" stroke-width="1.8" />
          </svg>
          Filter
        </button>
      </div>
    </div>

    <!-- DATATABLE -->
    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">NO</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">DATE</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">NIK</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">No Nozzle</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">Part No</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">SAP Code</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">Category</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center">Qty IN</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center">Status</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center">Remark</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">Comment</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($history as $key => $log)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            <td class="py-4 px-4 text-[13px] font-extrabold text-slate-950 dark:text-white">
              {{ $history->firstItem() + $key }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-900 dark:text-white whitespace-nowrap">
              {{ $log->created_at->format('d/m/Y') }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-900 dark:text-white">
              {{ $log->nik }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-900 dark:text-white">
              {{ $log->stockEng->no_nozzle ?? '-' }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-950 dark:text-white font-mono tracking-tight">
              {{ $log->stockEng->part_no ?? '-' }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-950 dark:text-white font-mono tracking-tight">
              {{ $log->stockEng->sap_code ?? '-' }}
            </td>
            <td class="py-4 px-4 text-[13px] font-semibold text-slate-600 dark:text-gray-300">
              {{ $log->stockEng->category ?? '-' }}
            </td>
            <td class="py-4 px-4 text-center">
              <span class="inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                +{{ $log->qty_added }}
              </span>
            </td>
            <td class="py-4 px-4 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold tracking-tight
                @if(strtolower($log->status) == 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                @elseif(strtolower($log->status) == 'pending') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                @else bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 @endif">
                {{ $log->status }}
              </span>
            </td>
            <td class="py-4 px-4 text-center">
              @php
                $remarkText = $log->remark ?? '';
                $remarkLower = strtolower($remarkText);
                $isManual = str_contains($remarkLower, 'manual');
                $isScan = str_contains($remarkLower, 'scan');
              @endphp

              <span class="remark-cell inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold tracking-tight
                @if($isManual) bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20
                @elseif($isScan) bg-purple-50 text-purple-700 border border-purple-100 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-500/20
                @else bg-slate-50 text-slate-600 border border-slate-100 dark:bg-slate-700/50 dark:text-slate-300 dark:border-slate-600 @endif">
                {{ $remarkText ? $log->remark : '-' }} 
              </span>
            </td>
            <td class="py-4 px-4 text-[13px] font-semibold text-slate-600 dark:text-gray-300 max-w-[220px] truncate" title="{{ $log->comment }}">
              {{ $log->comment ?? '-' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- PAGINATION BAR -->
    <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-1 border-t border-gray-100 pt-5 dark:border-gray-800">
      <p class="text-xs font-extrabold text-slate-950 dark:text-white">
        Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $history->links() }}
      </div>
    </div>
  </div>
</div>

<!-- Extra Styles for Pagination Fixes -->
<style>
  nav[role="navigation"] svg { width: 18px; height: 18px; display: inline; }
  nav[role="navigation"] div:first-child { display: none; }
  .pagination .page-item.active .page-link {
    background-color: #2563EB !important;
    border-color: #2563EB !important;
    color: white !important;
    font-weight: 800;
    font-size: 13px;
  }
  .pagination .page-link {
    color: inherit !important;
    font-weight: 700;
    font-size: 13px;
    padding: 6px 12px;
  }
  .dark .pagination .page-item:not(.active) .page-link {
    background-color: #1e293b !important;
    border-color: #334155 !important;
    color: #cbd5e1 !important;
  }
</style>

<!-- Filter Script Engine -->
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