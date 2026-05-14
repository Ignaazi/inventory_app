@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <!-- Header Section -->
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-xl font-bold text-slate-950 dark:text-white">
        Stock In Activities
      </h2>
      <p class="text-xs font-medium text-slate-600">Track your recent sparepart incoming activities</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('eng.in.scan') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-2 text-xs font-bold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
      >
        <i class="fas fa-qrcode text-primary"></i> Scan IN
      </a>
      <a href="{{ route('eng.in.manual') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-primary px-3 py-2 text-xs font-bold text-white shadow-md hover:bg-opacity-90"
      >
        <i class="fas fa-keyboard"></i> Manual IN
      </a>
    </div>
  </div>

  <!-- Table Card Section -->
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-base font-bold text-slate-950 dark:text-white">
          Recent History
        </h3>
      </div>

      <div class="flex items-center gap-3">
        <button
          class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-slate-950 shadow-sm hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
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
      <table class="min-w-full text-left border-collapse">
        <thead>
          <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50">
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
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($history as $key => $log)
          <tr class="hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
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
              <!-- Qty IN Badge: Gold (Smaller) -->
              <span class="inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-100">
                +{{ $log->qty_added }}
              </span>
            </td>
            <td class="py-3 px-3 text-center">
              <!-- Status Badge (Smaller) -->
              <span class="inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight
                @if($log->status == 'Success') bg-emerald-50 text-emerald-700 
                @elseif($log->status == 'Pending') bg-orange-50 text-orange-700
                @else bg-rose-50 text-rose-700 @endif">
                {{ $log->status == 'Success' ? 'Success' : ($log->status == 'Pending' ? 'Pending' : strtoupper($log->status)) }}
              </span>
            </td>
            <td class="py-3 px-3 text-center">
              <!-- Remark Badge: Manual (Blue) vs Scan (Purple) (Smaller) -->
              @php
                $remarkLower = strtolower($log->remark);
                $isManual = str_contains($remarkLower, 'manual');
                $isScan = str_contains($remarkLower, 'scan');
              @endphp

              <span class="inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold italic tracking-tight
                @if($isManual) bg-blue-50 text-blue-700 border border-blue-100
                @elseif($isScan) bg-purple-50 text-purple-700 border border-purple-100
                @else bg-slate-50 text-slate-600 border border-slate-100 @endif">
                {{ $log->remark ?? '-' }}
              </span>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
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
@endsection