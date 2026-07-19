@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .stock-in-view, .stock-in-view * {
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

<div class="stock-in-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
        Stock In Activities
      </h2>
      <p class="text-xs font-semibold text-slate-500 dark:text-gray-400 mt-0.5">Track your recent sparepart incoming activities</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="{{ route('eng.in.scan') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#E51E43] to-[#F86E1B] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Scan IN</span>
      </a>
      
      <a href="{{ route('eng.in.manual') }}"
        class="photo-grad-btn w-full sm:w-36 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#2563EB] via-[#4F7FE7] to-[#EAB308] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>Manual IN</span>
      </a>
    </div>
  </div>

  {{-- FLASH MESSAGES NOTIFICATION LOGS --}}
  @if(session('success'))
    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-600 dark:bg-emerald-950/20 dark:border-emerald-900 dark:text-emerald-400 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
      <i class="fas fa-check-circle mr-1"></i> {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-600 dark:bg-red-950/20 dark:border-red-900 dark:text-red-400 rounded-xl text-xs font-bold uppercase tracking-wider shadow-sm">
      <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
    </div>
  @endif

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-4 pt-4 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-6">
    
    <div class="flex flex-col gap-4 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-extrabold text-slate-950 dark:text-white tracking-tight uppercase">
          Recent History
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
      </div>
    </div>

    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">NO</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">NIK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">No Nozzle</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">Part No</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">SAP Code</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">RAK</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">Qty IN</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">Status</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">Remark</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white">Comment</th>
            <th class="py-2.5 px-3 text-[10px] font-black text-slate-950 uppercase tracking-wider dark:text-white text-center">Created At</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($history as $key => $log)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            <td class="py-2 px-3 text-[11px] font-extrabold text-slate-950 dark:text-white">
              {{ $history->firstItem() + $key }}
            </td>
            <td class="py-2 px-3 text-[11px] font-bold text-slate-900 dark:text-white whitespace-nowrap">
              {{ $log->nik ?? '-' }}
            </td>
            <!-- Mengambil nama dari relasi sparepart -->
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->stockEng->sparepart->name ?? '-' }}
            </td>
            <!-- Mengambil part number dari relasi sparepart -->
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->stockEng->sparepart->part_number ?? '-' }}
            </td>
            <!-- Mengambil sap code dari relasi sparepart -->
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->stockEng->sparepart->sap_code ?? '-' }}
            </td>
            <!-- Mengambil nama rak dari relasi raks -->
            <td class="py-2 px-3 text-[11px] font-bold text-slate-950 dark:text-white text-center font-mono tracking-tight whitespace-nowrap">
              {{ $log->stockEng->rak->nama_rak ?? '-' }}
            </td>
            <td class="py-2 px-3 text-center">
              <span class="inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold bg-emerald-50 text-emerald-600 border border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20">
                <!-- Mendukung fleksibilitas penamaan kolom qty_in atau qty_added -->
                +{{ $log->qty_in ?? ($log->qty_added ?? 0) }}
              </span>
            </td>
            <td class="py-2 px-3 text-center">
              <span class="status-cell inline-flex items-center justify-center rounded-full px-2 py-0.5 text-[10px] font-extrabold tracking-tight uppercase
                @if(strtolower($log->status) == 'success') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                @elseif(strtolower($log->status) == 'pending') bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-400
                @else bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 @endif">
                {{ $log->status }}
              </span>
            </td>
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
            <td class="py-2 px-3 text-[11px] font-semibold text-slate-600 dark:text-gray-300 max-w-[150px] truncate" title="{{ $log->comment }}">
              {{ $log->comment ?? '-' }}
            </td>
            <td class="py-1.5 px-3 text-center whitespace-nowrap">
              <div class="text-[11px] font-bold text-slate-900 dark:text-white tracking-tight">
                {{ $log->created_at ? $log->created_at->format('d/m/y') : '-' }}
              </div>
              <div class="text-[9px] font-bold text-slate-500 dark:text-slate-400 leading-none mt-0.5">
                {{ $log->created_at ? $log->created_at->format('H:i') : '' }}
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center py-6 text-xs font-bold text-slate-400 uppercase tracking-wider">
              No recent stock in logs found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

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