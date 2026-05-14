@extends('admin')

@section('content')
<div class="mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10">
  <!-- Header Section -->
  <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
    <div>
      <h2 class="text-2xl font-bold text-gray-800 dark:text-white/90">
        Stock In Activities
      </h2>
      <p class="text-sm text-gray-500">Track your recent sparepart incoming activities</p>
    </div>

    <div class="flex items-center gap-3">
      <a href="{{ route('eng.in.scan') }}"
        class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
      >
        <i class="fas fa-qrcode"></i> Scan IN
      </a>
      <a href="{{ route('eng.in.manual') }}"
        class="inline-flex items-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-theme-sm font-medium text-white shadow-theme-xs hover:bg-opacity-90"
      >
        <i class="fas fa-keyboard"></i> Manual IN
      </a>
    </div>
  </div>

  <!-- Table Card Section -->
  <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6">
    <div class="flex flex-col gap-2 mb-4 sm:flex-row sm:items-center sm:justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          Recent History
        </h3>
      </div>

      <div class="flex items-center gap-3">
        <button
          class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
        >
          <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
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
          <tr class="border-gray-100 border-y dark:border-gray-800">
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">NO</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">DATE</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">NIK</th>
            <!-- Kolom yang sudah dipecah -->
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">No Nozzle</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">Part No</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">SAP Code</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">Category</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">Qty IN</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">Status</th>
            <th class="py-3 px-2 text-theme-xs font-medium text-gray-500 uppercase dark:text-gray-400">Remark</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($history as $key => $log)
          <tr class="hover:bg-gray-50 dark:hover:bg-white/[0.02]">
            <td class="py-3 px-2 text-theme-sm text-gray-500 dark:text-gray-400">
              {{ $history->firstItem() + $key }}
            </td>
            <td class="py-3 px-2 text-theme-sm text-gray-500 dark:text-gray-400">
              {{ $log->created_at->format('d/m/Y') }}
            </td>
            <td class="py-3 px-2 text-theme-sm font-medium text-gray-800 dark:text-white/90">
              {{ $log->nik }}
            </td>
            <!-- Data ditarik via relasi stockEng -->
            <td class="py-3 px-2 text-theme-sm font-bold text-gray-800 dark:text-white/90">
              {{ $log->stockEng->no_nozzle ?? '-' }}
            </td>
            <td class="py-3 px-2 text-theme-sm text-gray-500 dark:text-gray-400 font-mono">
              {{ $log->stockEng->part_no ?? '-' }}
            </td>
            <td class="py-3 px-2 text-theme-sm text-gray-500 dark:text-gray-400 font-mono">
              {{ $log->stockEng->sap_code ?? '-' }}
            </td>
            <td class="py-3 px-2 text-theme-sm text-gray-500 dark:text-gray-400">
              {{ $log->stockEng->category ?? '-' }}
            </td>
            <td class="py-3 px-2">
              <p class="text-success-600 font-black text-theme-sm dark:text-success-500">
                +{{ $log->qty_added }}
              </p>
            </td>
            <td class="py-3 px-2">
              <p class="inline-flex rounded-full px-2 py-0.5 text-[10px] font-bold 
                @if($log->status == 'Success') bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500 
                @elseif($log->status == 'Pending') bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400
                @else bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500 @endif">
                {{ strtoupper($log->status) }}
              </p>
            </td>
            <td class="py-3 px-2 text-theme-xs italic text-gray-400 dark:text-gray-500">
              {{ $log->remark ?? '-' }}
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Pagination Footer -->
    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between px-2 pb-2 border-t border-gray-100 pt-4 dark:border-gray-800">
      <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
        Showing {{ $history->firstItem() ?? 0 }} to {{ $history->lastItem() ?? 0 }} of {{ $history->total() ?? 0 }} entries
      </p>
      <div class="flex items-center">
        {{ $history->links() }}
      </div>
    </div>
  </div>
</div>

<style>
  /* Menyamakan style pagination Laravel dengan TailAdmin */
  nav[role="navigation"] svg {
    width: 18px;
    height: 18px;
    display: inline;
  }
  nav[role="navigation"] div:first-child {
    display: none; /* Sembunyikan text "Showing..." bawaan laravel karena sudah kita buat custom di atas */
  }
  .pagination .page-item.active .page-link {
    background-color: #3C50E0 !important;
    border-color: #3C50E0 !important;
    color: white !important;
  }
</style>
@endsection