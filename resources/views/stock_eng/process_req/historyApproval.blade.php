@extends('admin')

@section('content')
<style>
  @import url('https://fonts.googleapis.com/css2?family=Nunito:wght=400;600;700;800;900&display=swap');

  .approval-history-view, .approval-history-view * {
    font-family: 'Nunito', ui-sans-serif, system-ui, sans-serif !important;
  }

  .table-row-item {
    transition: all 0.2s ease-in-out;
  }
</style>

<div class="approval-history-view mx-auto max-w-screen-2xl p-4 md:p-6 2xl:p-10 font-sans antialiased">
    
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-xl font-bold text-xs uppercase tracking-wide shadow-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="flex flex-col gap-2 mb-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-950 dark:text-white tracking-tight">
                Approval History
            </h2>
            <p class="text-sm font-semibold text-slate-500 dark:text-gray-400 mt-1">PT SIIX EMS KARAWANG</p>
        </div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] shadow-sm sm:px-6">
        
        <div class="flex flex-col gap-4 mb-4 lg:flex-row lg:items-center lg:justify-between">
            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-300 uppercase tracking-wider">Processed Requests History</h3>
            
            <div class="flex flex-wrap items-center gap-3 self-start lg:self-auto">
                <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
                    @foreach(['all' => 'All', 'checked' => 'Checked', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $key => $label)
                        <button type="button" onclick="filterTable('{{ $key }}', this)" class="filter-btn px-4 py-1 text-xs font-bold rounded-lg transition-all duration-200 {{ $key == 'all' ? 'bg-white text-slate-950 shadow-sm dark:bg-gray-700 dark:text-white' : 'text-slate-600 dark:text-gray-400 hover:text-slate-950 dark:hover:text-white' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="w-full overflow-x-auto block align-middle">
            <table class="min-w-full text-left border-collapse mx-auto" id="history-table">
                <thead>
                    <tr class="border-gray-100 border-y dark:border-gray-800 bg-gray-50/50">
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center whitespace-nowrap">NO</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">REQ No.</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Sparepart</th>
                        <th class="py-2.5 px-3 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center whitespace-nowrap">Qty</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Line</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Requestor</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center whitespace-nowrap">Status</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Created At</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white whitespace-nowrap">Last Update</th>
                        <th class="py-2.5 px-4 text-[10px] font-bold text-slate-950 uppercase dark:text-white text-center w-24 whitespace-nowrap">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-800 font-medium text-slate-950 dark:text-white">
                    @forelse($history as $index => $log)
                    <tr class="table-row-item hover:bg-gray-50/50 transition-colors duration-200 dark:hover:bg-white/[0.02]">
                        
                        <td class="py-3 px-3 text-xs font-bold text-center whitespace-nowrap">
                            {{ $history->firstItem() + $index }}
                        </td>
                        
                        <td class="py-3 px-4 text-xs font-bold text-slate-400 dark:text-slate-500 tracking-widest uppercase whitespace-nowrap">
                            {{ $log->request_no }}
                        </td>
                        
                        <td class="py-3 px-4 text-xs font-bold uppercase whitespace-nowrap">
                            {{ $log->sparepart_name }}
                        </td>
                        
                        <td class="py-3 px-3 text-xs font-bold text-center whitespace-nowrap">
                            {{ $log->qty_req }} <span class="text-[10px] text-slate-400">Pcs</span>
                        </td>
                        
                        <td class="py-3 px-4 text-xs font-bold uppercase whitespace-nowrap">
                            {{ $log->line_machine }}
                        </td>
                        
                        <td class="py-3 px-4 text-xs font-bold uppercase tracking-wider whitespace-nowrap">
                            {{ $log->requestor }}
                        </td>
                        
                        <td class="py-3 px-4 text-center whitespace-nowrap">
                            @php $status = strtolower($log->status); @endphp
                            <span class="status-cell inline-flex items-center justify-center rounded-full px-3 py-0.5 text-[10px] font-bold tracking-tight
                                {{ $status == 'approved' || $status == 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100 dark:bg-emerald-950/20 dark:text-emerald-400 dark:border-emerald-900/40' : 
                                  ($status == 'checked' ? 'bg-blue-50 text-blue-700 border border-blue-100 dark:bg-blue-950/20 dark:text-blue-400 dark:border-blue-900/40' : 
                                                          'bg-rose-50 text-rose-700 border border-rose-100 dark:bg-rose-950/20 dark:text-rose-400 dark:border-rose-900/40') }}">
                                {{ $log->status }}
                            </span>
                        </td>

                        <td class="py-3 px-4 text-xs whitespace-nowrap">
                            <div class="font-bold text-slate-800 dark:text-slate-200">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('d/m/y') }}
                            </div>
                            <div class="text-[10px] font-semibold text-slate-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($log->created_at)->format('H:i') }} WIB
                            </div>
                        </td>

                        <td class="py-3 px-4 text-xs whitespace-nowrap">
                            <div class="font-bold text-slate-800 dark:text-slate-200">
                                {{ \Carbon\Carbon::parse($log->updated_at)->format('d/m/y') }}
                            </div>
                            <div class="text-[10px] font-semibold text-slate-400 mt-0.5">
                                {{ \Carbon\Carbon::parse($log->updated_at)->format('H:i') }} WIB
                            </div>
                        </td>
                        
                        <td class="py-3 px-4 whitespace-nowrap">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('prod.request.preview', $log->id) }}" class="w-7 h-7 flex items-center justify-center bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                </a>
                                <form action="{{ route('approval.history.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Hapus record ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-7 h-7 flex items-center justify-center bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="p-12 text-center text-xs font-bold uppercase text-slate-400 dark:text-slate-500 tracking-widest">
                            Belum ada riwayat approval.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
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
      } else if (criteria === 'checked' && statusText === 'checked') {
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