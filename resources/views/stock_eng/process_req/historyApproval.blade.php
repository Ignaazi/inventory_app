@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 font-sans antialiased">
  
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-950 dark:text-white uppercase tracking-tight">
        Approval History
      </h2>
      <p class="text-xs font-medium text-slate-600 dark:text-gray-400">Review previously processed sparepart and nozzle requests</p>
    </div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    
    <div class="flex flex-col gap-4 mb-4 lg:flex-row lg:items-center lg:justify-between">
      
      <form action="{{ route('approval.history') }}" method="GET" class="relative w-full lg:w-72">
        <span class="absolute inset-y-0 left-3 flex items-center text-slate-400">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </span>
        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search History..." class="w-full pl-9 pr-4 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl font-semibold text-xs outline-none transition-all focus:border-indigo-500 text-slate-950 dark:text-white placeholder-slate-400">
      </form>

      <div class="flex flex-wrap items-center gap-3 self-start lg:self-auto">
        <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <button type="button" onclick="filterTable('all', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 bg-white text-slate-950 shadow-sm dark:bg-gray-700 dark:text-white">
            All
          </button>
          <button type="button" onclick="filterTable('checked by staff', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Checked
          </button>
          <button type="button" onclick="filterTable('approved', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Approved
          </button>
          <button type="button" onclick="filterTable('rejected', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Rejected
          </button>
        </div>

        <button class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-white">
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
          <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50">
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">REQ No.</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">No Nozzle</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white">SAP Code</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Qty</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Machine/Line</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Requestor</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Sign Prod</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Approver</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Sign Eng</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Status</th>
            <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center">Processed Date</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-slate-950 dark:text-white">
          @forelse($history as $log)
          <tr class="table-row-item hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            
            <td class="py-3 px-3 text-xs font-bold text-slate-400 dark:text-slate-500 tracking-widest uppercase">
              {{ $log->request_no }}
            </td>
            
            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white uppercase whitespace-nowrap">
              {{ $log->sparepart_name }}
            </td>

            <td class="py-3 px-3 text-xs font-bold text-slate-950 dark:text-white font-mono uppercase whitespace-nowrap">
              {{ $log->sap_code }}
            </td>
            
            <td class="py-3 px-3 text-xs font-bold text-center text-slate-950 dark:text-white">
              {{ sprintf("%02d", $log->qty_req) }} PCS
            </td>
            
            <td class="py-3 px-3 text-xs font-bold text-center text-slate-950 dark:text-white uppercase whitespace-nowrap">
              {{ $log->line_machine }}
            </td>
            
            <td class="py-3 px-3 text-xs font-bold text-center text-slate-950 dark:text-white uppercase tracking-wider">
              {{ $log->requestor }}
            </td>

            <td class="py-2 px-3 text-center">
              @if(!empty($log->production_signature))
                <div class="inline-block bg-white p-0.5 rounded border border-gray-200 dark:border-gray-700">
                  <img src="{{ $log->production_signature }}" alt="Sign Prod" class="h-7 w-auto max-w-[80px] object-contain mix-blend-multiply">
                </div>
              @elseif($log->productionRequest && !empty($log->productionRequest->production_signature))
                <div class="inline-block bg-white p-0.5 rounded border border-gray-200 dark:border-gray-700">
                  <img src="{{ $log->productionRequest->production_signature }}" alt="Sign Prod" class="h-7 w-auto max-w-[80px] object-contain mix-blend-multiply">
                </div>
              @else
                <span class="text-[10px] text-slate-400 font-bold uppercase">—</span>
              @endif
            </td>

            <td class="py-3 px-3 text-xs font-bold text-center text-slate-950 dark:text-white uppercase tracking-wider">
              {{ $log->approved_by ?? '—' }}
            </td>

            {{-- 🎯 UPDATED: Sign Eng menampilkan Staff & SPV secara berdampingan --}}
            <td class="py-2 px-3 text-center">
              <div class="flex items-center justify-center gap-1">
                @if(!empty($log->staff_signature))
                  <div class="inline-block bg-white p-0.5 rounded border border-blue-400" title="Signed by Staff">
                    <img src="{{ $log->staff_signature }}" alt="Sign Staff" class="h-6 w-auto max-w-[40px] object-contain mix-blend-multiply">
                  </div>
                @endif
                @if(!empty($log->spv_signature))
                  <div class="inline-block bg-white p-0.5 rounded border border-emerald-400" title="Signed by SPV">
                    <img src="{{ $log->spv_signature }}" alt="Sign SPV" class="h-6 w-auto max-w-[40px] object-contain mix-blend-multiply">
                  </div>
                @endif
                @if(empty($log->staff_signature) && empty($log->spv_signature))
                  <span class="text-[10px] text-slate-400 font-bold uppercase">—</span>
                @endif
              </div>
            </td>

            <td class="py-3 px-3 text-center">
              @php 
                $currentStatus = strtolower($log->status);
              @endphp

              @if($currentStatus == 'approved' || $currentStatus == 'success')
                <span class="status-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/40">
                  Approved
                </span>
              @elseif($currentStatus == 'checked by staff' || $currentStatus == 'checked')
                <span class="status-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/40">
                  Checked by Staff
                </span>
              @else
                <span class="status-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight bg-rose-50 text-rose-700 border border-rose-100 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/40">
                  Rejected
                </span>
              @endif
            </td>

            <td class="py-3 px-3 text-center text-xs font-bold text-slate-950 dark:text-white whitespace-nowrap">
              {{ $log->processed_at ? $log->processed_at->format('d M Y H:i') : ($log->created_at ? $log->created_at->format('d M Y H:i') : '—') }}
            </td>

          </tr>
          @empty
          <tr>
            <td colspan="11" class="p-12 text-center text-xs font-bold uppercase text-slate-400 dark:text-slate-500 tracking-widest">
              No History Records Found.
            </td>
          </tr>
          @endforelse
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
  nav[role="navigation"] svg {
    width: 16px;
    height: 16px;
    display: inline;
  }
  nav[role="navigation"] div:first-child {
    display: none;
  }
  .pagination .page-item.active .page-link {
    background-color: #3C50E0 !important;
    border-color: #3C50E0 !important;
    color: white !important;
    font-weight: bold;
    font-size: 12px;
  }
  .pagination .page-link {
    color: #0f172a !important; 
    font-weight: 700;
    font-size: 12px;
    padding: 4px 8px;
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

      if (criteria === 'approved' && (statusText === 'approved' || statusText === 'success')) {
         row.style.display = '';
      } else if (criteria === 'checked by staff' && (statusText === 'checked by staff' || statusText === 'checked')) {
         row.style.display = '';
      } else if (criteria === 'rejected' && statusText === 'rejected') {
         row.style.display = '';
      } else {
         row.style.display = 'none';
      }
    });
  }
</script>
@endsection