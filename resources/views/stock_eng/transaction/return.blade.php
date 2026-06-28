@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .stock-return-view, .stock-return-view * {
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

<div class="stock-return-view mx-auto max-w-screen-2xl p-5 md:p-8 2xl:p-12">
  <div class="flex flex-col gap-4 mb-8 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white tracking-tight">
        Stock Return Activities
      </h2>
      <p class="text-sm font-semibold text-slate-500 dark:text-gray-400 mt-1">Track your recent sparepart return activities from lines</p>
    </div>

    <div class="flex items-center gap-3 w-full sm:w-auto">
      <a href="/stock-eng/transaction/return/create"
        class="photo-grad-btn w-full sm:w-40 h-10 inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-[#10B981] to-[#059669] px-3 text-xs font-black text-white tracking-wider uppercase"
      >
        <span>New Return</span>
      </a>
    </div>
  </div>

  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-5 pb-5 pt-5 dark:border-gray-800 dark:bg-slate-900 shadow-sm sm:px-7">
    
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
          <button type="button" onclick="filterTable('good', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Good Condition
          </button>
          <button type="button" onclick="filterTable('repair', this)" class="filter-btn px-4 py-1.5 text-xs font-extrabold rounded-lg transition-all duration-200 text-slate-500 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white">
            Need Repair
          </button>
        </div>
      </div>
    </div>

    <div class="w-full overflow-x-auto">
      <table class="min-w-full text-left border-collapse" id="history-table">
        <thead>
          <tr class="border-gray-200 border-y dark:border-gray-800 bg-gray-50/70 dark:bg-slate-800/60">
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">NO</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">DATE</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">RETURNED BY</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">No Nozzle</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">Part No</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">SAP Code</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">Category</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center">Qty RETURN</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white text-center">Condition</th>
            <th class="py-3.5 px-4 text-[11px] font-extrabold text-slate-950 uppercase tracking-wider dark:text-white">Remarks</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @forelse($history ?? [] as $key => $log)
          <tr class="table-row-item hover:bg-gray-50/60 transition-colors duration-200 dark:hover:bg-white/[0.02]">
            <td class="py-4 px-4 text-[13px] font-extrabold text-slate-950 dark:text-white">
              {{ method_exists($history, 'firstItem') ? $history->firstItem() + $key : $key + 1 }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-900 dark:text-white whitespace-nowrap">
              {{ $log->created_at ? $log->created_at->format('d/m/Y') : '-' }}
            </td>
            <td class="py-4 px-4 text-[13px] font-bold text-slate-900 dark:text-white">
              {{ $log->returned_by ?? '-' }}
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
              <span class="inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold bg-blue-50 text-blue-600 border border-blue-100 dark:bg-blue-500/10 dark:text-blue-400 dark:border-blue-500/20">
                {{ $log->qty ?? 0 }}
              </span>
            </td>
            <td class="py-4 px-4 text-center">
              <span class="condition-cell inline-flex items-center justify-center rounded-full px-3.5 py-1 text-xs font-extrabold tracking-tight
                @if(strtolower($log->condition ?? '') == 'good') bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400
                @else bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-400 @endif">
                {{ $log->condition ?? 'Good' }}
              </span>
            </td>
            <td class="py-4 px-4 text-[13px] font-semibold text-slate-600 dark:text-gray-300 max-w-[220px] truncate" title="{{ $log->remarks }}">
              {{ $log->remarks ?? '-' }}
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="10" class="py-8 text-center text-sm font-semibold text-slate-400">No return transaction history found.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    @if(isset($history) && method_exists($history, 'links'))
    <div class="mt-5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-1 border-t border-gray-100 pt-5 dark:border-gray-800">
      <p class="text-xs font-extrabold text-slate-950 dark:text-white">
        Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $history->links() }}
      </div>
    </div>
    @endif
  </div>
</div>

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
      const conditionText = row.querySelector('.condition-cell').textContent.trim().toLowerCase();
      if (conditionText.includes(criteria)) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }
</script>
@endsection