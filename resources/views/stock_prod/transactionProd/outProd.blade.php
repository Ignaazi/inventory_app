@extends('admin')

@section('content')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,300;0,400;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

<style>
  .font-nunito, body, select, input, button, table, th, td { 
    font-family: 'Nunito', sans-serif !important; 
  }
  .scrollbar-thin::-webkit-scrollbar { height: 6px; width: 6px; }
  .scrollbar-thin::-webkit-scrollbar-track { background: transparent; }
  .scrollbar-thin::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
  .dark .scrollbar-thin::-webkit-scrollbar-thumb { background: #475569; }
  
  #history-table td { color: #000000 !important; vertical-align: middle !important; }
  .dark #history-table td { color: #cbd5e1 !important; }
  #history-table th { vertical-align: middle !important; }
</style>

<div class="font-nunito w-full p-3 md:p-6 bg-slate-50/30 dark:bg-slate-950 min-h-screen transition-all duration-300">
  
  {{-- Info Banner --}}
  <div class="mb-4 flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 dark:bg-rose-950/30 dark:border-rose-900/50 px-3 py-2.5 md:px-4 md:py-3 shadow-sm">
    <span class="h-2 w-2 shrink-0 rounded-full bg-rose-500 animate-pulse"></span>
    <p class="text-[12px] md:text-[14px] font-bold text-rose-800 dark:text-rose-400 font-nunito leading-tight">
      <span class="uppercase font-black mr-1 text-[13px] md:text-[15px]">PRODUCTION STOCK OUT ACTIVE:</span> 
      Track and monitor component discharge records from the production floor in real-time.
    </p>
  </div>

  {{-- HEADER SECTION & NAV BUTTONS --}}
  <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 font-nunito">
    <div>
      <h2 class="text-xl md:text-2xl font-black text-black dark:text-white tracking-tight uppercase">
        Production Stock Out Activities
      </h2>
      <p class="text-[11px] md:text-[13px] font-bold text-slate-500 dark:text-slate-400">
        Track and manage components discharged from the production floor
      </p>
    </div>

    <div class="flex items-center gap-2 w-full sm:w-auto">
      <a href="{{ route('prod.transaction.out.scan') }}"
        class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-rose-600 via-rose-500 to-amber-500 px-3 text-[11px] font-black text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-32 text-center no-underline">
        <i class="fa-solid fa-qrcode text-xs"></i> Scan OUT
      </a>
      
      <a href="{{ route('prod.transaction.out.manual') }}"
        class="inline-flex items-center justify-center gap-1.5 h-8 rounded-lg bg-gradient-to-r from-blue-600 via-blue-500 to-indigo-500 px-3 text-[11px] font-black text-white shadow-md hover:opacity-90 tracking-wider uppercase active:scale-95 transition-all font-nunito w-full sm:w-32 text-center no-underline">
        <i class="fa-solid fa-keyboard text-xs"></i> Manual OUT
      </a>
    </div>
  </div>

  {{-- CONTAINER DATA TABLE --}}
  <div class="w-full overflow-hidden rounded-xl border border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 pt-4 shadow-sm">
    
    {{-- TABLE FILTER CONTROL --}}
    <div class="mb-4 flex flex-col gap-3 px-4 sm:flex-row sm:items-center sm:justify-between font-nunito">
      <div>
        <h3 class="text-xs md:text-sm font-black text-black dark:text-white tracking-wider uppercase">
          Production Outflow History
        </h3>
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <div class="inline-flex p-1 bg-slate-100 dark:bg-slate-800 rounded-lg border border-gray-200 dark:border-slate-700 shadow-inner">
          <button type="button" onclick="filterTable('all', this)" class="filter-btn px-2.5 py-1 text-[11px] font-black rounded-md transition-all duration-200 bg-white text-black shadow-sm dark:bg-slate-700 dark:text-white uppercase">
            All
          </button>
          <button type="button" onclick="filterTable('success', this)" class="filter-btn px-2.5 py-1 text-[11px] font-black rounded-md transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-black dark:hover:text-white uppercase">
            Success
          </button>
          <button type="button" onclick="filterTable('pending', this)" class="filter-btn px-2.5 py-1 text-[11px] font-black rounded-md transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-black dark:hover:text-white uppercase">
            Pending
          </button>
          <button type="button" onclick="filterTable('manual', this)" class="filter-btn px-2.5 py-1 text-[11px] font-black rounded-md transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-black dark:hover:text-white uppercase">
            Manual Out
          </button>
          <button type="button" onclick="filterTable('scan', this)" class="filter-btn px-2.5 py-1 text-[11px] font-black rounded-md transition-all duration-200 text-slate-500 dark:text-slate-400 hover:text-black dark:hover:text-white uppercase">
            Scan Out
          </button>
        </div>
      </div>
    </div>

    {{-- MAIN TABLE LOG OUT --}}
    <div class="w-full overflow-x-auto scrollbar-thin bg-transparent">
      <table class="w-full text-center border-collapse border-b border-gray-200 dark:border-slate-800 min-w-[1200px]" id="history-table">
        <thead>
          <tr class="text-[12px] font-black uppercase tracking-wider bg-slate-900 dark:bg-slate-950 text-white dark:text-slate-200 font-nunito">
            <th class="px-2 py-3.5 w-[50px] text-center">NO</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">TRANSACTION ID</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">STOCK PROD ID</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">BARCODE ID</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">REQUEST ID</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-left">NIK PIC</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">CATEGORY</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">QTY OUT</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">PROCESS TYPE</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">STATUS</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">PHOTO PROOF</th>
            <th class="px-4 py-3.5 border-l border-slate-700/50 text-left">REMARK / KRONOLOGI</th>
            <th class="px-3 py-3.5 border-l border-slate-700/50 text-center">CREATED AT</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 dark:divide-slate-800 text-[13px] font-bold text-black dark:text-slate-200 font-nunito bg-transparent">
          @forelse($history as $key => $log)
          <tr class="table-row-item hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition-colors duration-150 bg-transparent">
            {{-- 1. NO --}}
            <td class="px-2 py-3.5 text-slate-500 font-bold text-center">
              {{ (method_exists($history, 'currentPage')) ? ($history->firstItem() + $key) : ($key + 1) }}
            </td>

            {{-- 2. TRANSACTION ID --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono font-black text-rose-600 dark:text-rose-400 text-center whitespace-nowrap select-all">
              {{ $log->tx_id }}
            </td>

            {{-- 3. STOCK PROD ID --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono font-bold text-blue-600 dark:text-blue-400 text-center whitespace-nowrap">
              {{ $log->stock_prods_id }}
            </td>

            {{-- 4. BARCODE ID --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono font-bold text-slate-700 dark:text-slate-300 text-center whitespace-nowrap">
              {{ $log->db_barcodes_id ?? '-' }}
            </td>

            {{-- 5. REQUEST ID --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono font-bold text-slate-700 dark:text-slate-300 text-center whitespace-nowrap">
              {{ $log->production_request_id ?? '-' }}
            </td>

            {{-- 6. NIK PIC --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 font-mono font-bold text-left whitespace-nowrap">
              {{ $log->nik_karyawan ?? '-' }}
            </td>

            {{-- 7. CATEGORY --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
              @if($log->out_category == 'broken')
                <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400">
                  Broken
                </span>
              @elseif($log->out_category == 'lost')
                <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400">
                  Lost
                </span>
              @else
                <span class="text-slate-400 font-bold">-</span>
              @endif
            </td>

            {{-- 8. QTY OUT --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center">
              <span class="inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[11px] font-black border border-rose-200 bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-900/50">
                -{{ $log->qty_transaction ?? 0 }}
              </span>
            </td>

            {{-- 9. PROCESS TYPE --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
              <span class="process-cell inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border
                @if($log->process_type == 'manual') border-blue-200 bg-blue-50 text-blue-700 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-900/50
                @else border-purple-200 bg-purple-50 text-purple-700 dark:bg-purple-500/10 dark:text-purple-400 dark:border-purple-900/50 @endif">
                {{ $log->process_type ?? 'scan' }}
              </span>
            </td>

            {{-- 10. STATUS --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
              <span class="status-cell inline-flex items-center justify-center rounded-lg px-2.5 py-0.5 text-[10px] font-black tracking-tight uppercase border
                @if(strtolower($log->status ?? '') == 'success') border-emerald-200 bg-emerald-50 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-900/50
                @elseif(strtolower($log->status ?? '') == 'pending') border-amber-200 bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-900/50
                @else border-rose-200 bg-rose-50 text-rose-700 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-900/50 @endif">
                {{ $log->status ?? 'success' }}
              </span>
            </td>

            {{-- 11. PHOTO PROOF --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
              @if($log->photo_path)
                <a href="{{ asset('storage/' . $log->photo_path) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] font-bold text-blue-600 dark:text-blue-400 hover:underline">
                  <i class="fa-solid fa-image"></i> View Photo
                </a>
              @else
                <span class="text-slate-400 font-mono text-xs">-</span>
              @endif
            </td>

            {{-- 12. REMARK --}}
            <td class="px-4 py-3.5 border-l border-gray-100 dark:border-slate-800 text-left font-semibold text-slate-700 dark:text-slate-300 max-w-[200px] truncate" title="{{ $log->remark }}">
              {{ $log->remark ?? '-' }}
            </td>

            {{-- 13. CREATED AT --}}
            <td class="px-3 py-3.5 border-l border-gray-100 dark:border-slate-800 text-center whitespace-nowrap">
              @php
                $createdAt = null;
                if ($log->created_at) {
                  $createdAt = $log->created_at instanceof \Carbon\Carbon ? $log->created_at : \Carbon\Carbon::parse($log->created_at);
                }
              @endphp
              <div class="text-[11px] font-bold text-black dark:text-white tracking-tight">
                {{ $createdAt ? $createdAt->format('d/m/y') : '-' }}
              </div>
              <div class="text-[9px] font-bold text-slate-400 leading-none mt-0.5">
                {{ $createdAt ? $createdAt->format('H:i') : '' }}
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="13" class="py-12 text-center text-slate-400 italic font-medium text-[13px] font-nunito">
              No recent production outgoing history records found.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- PAGINATION INTERFACE --}}
    <div class="flex flex-col sm:flex-row gap-3 items-center justify-between border-t border-gray-200 dark:border-slate-800 bg-white dark:bg-slate-900 px-4 py-4 font-nunito">
      <p class="text-[11px] font-black text-black dark:text-slate-400 tracking-wide uppercase font-nunito text-center sm:text-left">
        Showing {{ (isset($history) && method_exists($history, 'firstItem')) ? ($history->firstItem() ?? 0) : 0 }} 
        to {{ (isset($history) && method_exists($history, 'lastItem')) ? ($history->lastItem() ?? 0) : 0 }} 
        of {{ (isset($history) && method_exists($history, 'total')) ? ($history->total() ?? 0) : 0 }} Entries
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
    background-color: #ea580c !important;
    border-color: #ea580c !important;
    color: white !important;
    font-weight: 800;
    font-size: 11px;
    border-radius: 6px;
  }
  .pagination .page-link {
    color: inherit !important;
    font-weight: 700;
    font-size: 11px;
    padding: 6px 12px;
    border-radius: 6px;
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
      btn.classList.remove('bg-white', 'text-black', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
      btn.classList.add('text-slate-500', 'dark:text-slate-400', 'hover:text-black', 'dark:hover:text-white');
    });

    if (element) {
      element.classList.remove('text-slate-500', 'dark:text-slate-400', 'hover:text-black', 'dark:hover:text-white');
      element.classList.add('bg-white', 'text-black', 'shadow-sm', 'dark:bg-slate-700', 'dark:text-white');
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